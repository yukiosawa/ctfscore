<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php if (isset($description)): ?>
  <h4><?php echo $description; ?></h4>
<?php endif; ?>

<form style="display: inline;" action="<?php echo Uri::base(false).'admin/config/fileupload' ?>" method="post" enctype="multipart/form-data">
  
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <span class="form-group">
    <input type="hidden" name="name" value="<?php echo $name; ?>" />
    <!-- <label for="file_upload">ファイル</label> -->
    <input style="display: inline;" type='file' name='file_upload[]' accept="<?php echo $mimetype; ?>" multiple />
  </span>
  <span>
    <button style="display: inline;" class="btn btn-sm btn-primary" type="submit">アップロード</button>
  </span>

</form>
