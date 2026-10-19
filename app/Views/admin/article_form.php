<?php
$is_edit = $article !== null;
$action  = $is_edit ? base_url('admin/article/update/' . $article['id']) : base_url('admin/article/store');
$errors  = session('errors') ?? [];
$attachments = $is_edit && $article['attachments'] ? json_decode($article['attachments'], true) : [];
?>
<?= $this->extend('admin/layout') ?>

<?= $this->section('actions') ?>
  <a class="btn btn--ghost" href="<?= base_url('admin/article') ?>">목록으로</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ckeditor5@42.0.2/build/ckeditor.min.css">

<form class="form" method="post" action="<?= $action ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="field">
    <label for="title">제목 <span class="req" aria-hidden="true">*</span></label>
    <input class="inp" type="text" id="title" name="title" maxlength="200" required
           <?= isset($errors['title']) ? 'aria-invalid="true" aria-describedby="title-err"' : '' ?>
           value="<?= esc(old('title', $is_edit ? $article['title'] : '')) ?>">
    <?php if (isset($errors['title'])): ?>
      <p class="err" id="title-err" role="alert"><?= esc($errors['title']) ?></p>
    <?php endif ?>
  </div>

  <div class="field">
    <label for="content">내용 <span class="req" aria-hidden="true">*</span></label>
    <p class="help" style="margin-bottom: 12px;">✏️ 텍스트와 이미지를 자유롭게 배치할 수 있습니다. 에디터의 이미지 아이콘을 클릭해 이미지를 삽입하세요.</p>
    <textarea class="txt" id="content" name="content" required
              <?= isset($errors['content']) ? 'aria-invalid="true" aria-describedby="content-err"' : '' ?>
    ><?= esc(old('content', $is_edit ? $article['content'] : '')) ?></textarea>
    <?php if (isset($errors['content'])): ?>
      <p class="err" id="content-err" role="alert"><?= esc($errors['content']) ?></p>
    <?php endif ?>
  </div>

  <div class="field">
    <label for="attachments">📎 파일 첨부 (다운로드용)</label>
    <input class="inp" type="file" id="attachments" name="attachments[]" multiple
           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.hwp,.zip,.txt">
    <p class="help">PDF, Word, Excel, PowerPoint, HWP, ZIP, TXT 등. 최대 10MB 각각. 여러 개를 동시에 선택할 수 있습니다.</p>

    <?php if (! empty($attachments)): ?>
      <div style="margin-top: 16px;">
        <p style="margin-bottom: 8px; font-size: 12px; color: #666; font-weight: 500;">현재 등록된 파일 (<?= count($attachments) ?>개):</p>
        <ul style="list-style: none; padding: 0;">
          <?php foreach ($attachments as $att): ?>
            <li style="padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px;">
              📄 <?= esc($att['name']) ?>
              <span style="color: #999; font-size: 12px;">(<?= number_format($att['size'] / 1024, 1) ?> KB)</span>
            </li>
          <?php endforeach ?>
        </ul>
        <p style="margin-top: 12px; font-size: 12px; color: #ff9800;">💡 새 파일을 업로드하면 기존 파일은 유지되고, 새 파일이 추가됩니다.</p>
      </div>
    <?php endif ?>
  </div>

  <div class="field">
    <label class="check">
      <input type="checkbox" name="is_notice" value="1"
             <?= old('is_notice', $is_edit ? $article['is_notice'] : 0) ? 'checked' : '' ?>>
      목록 맨 위에 고정 (공지)
    </label>
    <p class="help">중요한 공지사항을 항상 첫 줄에 보이게 합니다.</p>
  </div>

  <div class="form__act">
    <button type="submit" class="btn btn--primary"><?= $is_edit ? '수정 저장' : '등록' ?></button>
    <a class="btn btn--ghost" href="<?= base_url('admin/article') ?>">취소</a>

    <?php if ($is_edit): ?>
      <span class="right"></span>
    <?php endif ?>
  </div>
</form>

<?php if ($is_edit): ?>
  <form class="form form--bare" method="post" style="margin-top:16px"
        action="<?= base_url('admin/article/delete/' . $article['id']) ?>"
        data-confirm="이 글을 삭제할까요? 되돌릴 수 없습니다.">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn--danger">이 글 삭제</button>
  </form>
<?php endif ?>

<script src="https://cdn.jsdelivr.net/npm/ckeditor5@42.0.2/build/ckeditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ckeditor5@42.0.2/build/translations/ko.min.js"></script>
<script>
ClassicEditor
  .create(document.querySelector('#content'), {
    language: 'ko',
    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'imageInsert', 'blockQuote', 'bulletedList', 'numberedList', 'undo', 'redo'],
    image: {
      toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
    }
  })
  .catch(err => {
    console.error('CKEditor 로드 실패:', err);
  });
</script>

<?= $this->endSection() ?>
