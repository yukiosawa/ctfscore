<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>


<form class="form-horizontal" action="<?php echo Uri::current(); ?>" method="post" enctype="multipart/form-data">
  
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <input type="hidden" name="name" value="<?php echo $page['name']; ?>" />

  <div class="form-group">
    <div class="col-md-4">
      <label for="display_name">名称</label>
      <input class="form-control" type="text" id="display_name" name="display_name" value="<?php echo $page['display_name']; ?>" />
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-12">
      <label for="page">ページ本文</label>
      <textarea class="form-control" id="content" name="content"><?php echo $page['content']; ?></textarea>
    </div>
  </div>
  <!-- Wysiwygエディタで編集 -->
  <?php echo Asset::js('trumbowyg/dist/trumbowyg.min.js'); ?>
  <?php echo Asset::js('trumbowyg/dist/langs/ja.min.js'); ?>
  <?php echo Asset::css('trumbowyg.min.css'); ?>
  <script>$("#content").trumbowyg({
      lang: 'ja'
   });</script>

  <div>
    <label for="is_active">ページを有効化</label>
    <input type="checkbox" id="is_active" name="is_active" value="1" <?php if($page['is_active'] == 1) echo 'checked'; ?>>
  </div>

  <p>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>


