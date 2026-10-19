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
            // 이미지 파일 삭제
            if ($article['images']) {
                $images = json_decode($article['images'], true) ?? [];
                foreach ($images as $img) {
                    $this->deleteFile($img['path']);
                }
            }
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
     * 이미지와 첨부파일 업로드 처리
     */
    private function handleFiles(array &$data, ?int $article_id = null): bool
    {
        $data['images'] = null;
        $data['attachments'] = null;

        // 이미지 업로드 처리
        $images = $this->request->getFiles('images');
        if ($images) {
            $uploadedImages = [];
            foreach ($images as $file) {
                if (! $file->isValid()) {
                    continue;
                }

                $ext = strtolower($file->getClientExtension());
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    return redirect()->back()->withInput()
                        ->with('error', 'JPG, PNG, GIF, WebP 이미지만 업로드 가능합니다.')->send();
                }

                if ($file->getSize() > 5242880) {
                    return redirect()->back()->withInput()
                        ->with('error', '이미지 파일은 5MB 이하여야 합니다.')->send();
                }

                $newName = $file->getRandomName();
                $uploadDir = FCPATH . 'uploads/articles/images/';
                if (! is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $file->move($uploadDir, $newName);
                $uploadedImages[] = [
                    'path' => 'uploads/articles/images/' . $newName,
                    'name' => $file->getClientName(),
                ];
            }

            if (! empty($uploadedImages)) {
                $data['images'] = json_encode($uploadedImages, JSON_UNESCAPED_UNICODE);
            }
        }

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

        // 기존 파일 삭제 (수정 시, 새로 업로드 안 한 경우 유지)
        if ($article_id) {
            $oldArticle = (new ArticleModel())->find($article_id);

            // 새 이미지를 업로드하지 않으면 기존 이미지 유지
            if ($data['images'] === null && $oldArticle['images']) {
                $data['images'] = $oldArticle['images'];
            }

            // 새 첨부파일을 업로드하지 않으면 기존 첨부파일 유지
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
