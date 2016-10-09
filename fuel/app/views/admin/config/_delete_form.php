<form style="display: inline;" action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <span class="form-group">
    <!-- for deleting a chart color -->
    <?php if (isset($id)): ?>
      <input type="hidden" class="form-control" name="id" value="<?php echo $id ?>">
    <?php endif; ?>
    <!-- for deleting an asset file -->
    <?php if (isset($filename)): ?>
      <input type="hidden" name="filename" value="<?php echo $filename ?>">
      <input type="hidden" name="name" value="<?php echo $name ?>">
    <?php endif; ?>
  </span>

  <span>
    <button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('削除しますか？')">削除</button>
  </span>

</form>

