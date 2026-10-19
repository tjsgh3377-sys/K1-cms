<?php

namespace App\Controllers\Admin;

use App\Models\ArticleModel;

class Article extends AdminController
{
    protected string $menu_key = 'article';

    public function index()
    {
        $model   = new ArticleModel();
        $keyword = trim((string) $this->request->getGet('q'));

        return $this->render('admin/article_list', [
            'title'        => '게시판',
            'subtitle'     => '홈페이지 홍보센터에 노출되는 공지사항과 소식입니다.',
            'article_list' => $model->getList($this->per_page, $this->currentPage(), $keyword),
            'pager'        => $model->pager,
            'total_count'  => $model->pager->getTotal(),
            'keyword'      => $keyword,
        ]);
    }

    public function create()
    {
        return $this->render('admin/article_form', [
            'title'    => '글쓰기',
            'subtitle' => '등록하면 홈페이지 게시판에 바로 올라갑니다.',
            'article'  => null,
        ]);
    }

    public function edit($id)
    {
        $article = (new ArticleModel())->find($id);

        if ($article === null) {
            return redirect()->to('/admin/article')->with('error', '없는 글입니다.');
        }

        return $this->render('admin/article_form', [
            'title'    => '글 수정',
            'subtitle' => esc($article['title']),
            'article'  => $article,
        ]);
    }

    public function store()
    {
        $model = new ArticleModel();
        $data = $this->postData();

        if (! $this->handleFiles($data)) {
            return redirect()->back()->withInput();
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/article')->with('message', '글을 등록했습니다.');
    }

    public function update($id)
    {
        $model = new ArticleModel();

        if ($model->find($id) === null) {
            return redirect()->to('/admin/article')->with('error', '없는 글입니다.');
        }

        $data = $this->postData();

        if (! $this->handleFiles($data, $id)) {
            return redirect()->back()->withInput();
        }

        if (! $model->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/article')->with('message', '글을 수정했습니다.');
    }

    public function delete($id)
    {
        $model = new ArticleModel();
        $article = $model->find($id);

        if ($article) {
            // 첨부파일 삭제
            if ($article['attachments']) {
                $attachments = json_decode($article['attachments'], true) ?? [];
                foreach ($attachments as $att) {
                    $this->deleteFile($att['path']);
                }
            }
        }

        $model->delete($id);

        return redirect()->to('/admin/article')->with('message', '글을 삭제했습니다.');
    }

    private function postData(): array
    {
        return [
            'title'     => trim((string) $this->request->getPost('title')),
            'content'   => (string) $this->request->getPost('content'),
            'is_notice' => $this->request->getPost('is_notice') ? 1 : 0,
        ];
    }

    /**
     * 첨부파일 업로드 처리 (이미지는 CKEditor에서 관리)
     */
    private function handleFiles(array &$data, ?int $article_id = null): bool
    {
        $data['attachments'] = null;

        // 첨부파일 업로드 처리
        $attachments = $this->request->getFiles('attachments');
        if ($attachments) {
            $uploadedAttachments = [];
            foreach ($attachments as $file) {
                if (! $file->isValid()) {
                    continue;
                }

                $ext = strtolower($file->getClientExtension());
                $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'hwp', 'zip', 'txt'];
                if (! in_array($ext, $allowed)) {
                    return redirect()->back()->withInput()
                        ->with('error', '지원하지 않는 파일 형식입니다. (PDF, Word, Excel, PowerPoint, HWP, ZIP, TXT 등)')->send();
                }

                if ($file->getSize() > 10485760) { // 10MB
                    return redirect()->back()->withInput()
                        ->with('error', '첨부파일은 10MB 이하여야 합니다.')->send();
                }

                $newName = $file->getRandomName();
                $uploadDir = FCPATH . 'uploads/articles/attachments/';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file->move($uploadDir, $newName);
                $uploadedAttachments[] = [
                    'path' => 'uploads/articles/attachments/' . $newName,
                    'name' => $file->getClientName(),
                    'size' => $file->getSize(),
                ];
            }

            if (! empty($uploadedAttachments)) {
                $data['attachments'] = json_encode($uploadedAttachments, JSON_UNESCAPED_UNICODE);
            }
        }

        // 기존 첨부파일 유지 (수정 시, 새로 업로드 안 한 경우)
        if ($article_id) {
            $oldArticle = (new ArticleModel())->find($article_id);
            if ($data['attachments'] === null && $oldArticle['attachments']) {
                $data['attachments'] = $oldArticle['attachments'];
            }
        }

        return true;
    }

    /**
     * 파일 삭제
     */
    private function deleteFile(string $filePath): void
    {
        $path = FCPATH . $filePath;
        if (file_exists($path) && is_file($path)) {
            unlink($path);
        }
    }
}
