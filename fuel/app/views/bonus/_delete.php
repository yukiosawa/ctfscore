<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <input type="hidden" class="form-control" name="id" value="<?php echo $id ?>">
  </div>

  <div>
    <button class="btn btn-primary" type="submit" onclick="return confirm('削除しますか？')">削除</button>
  </div>

</form>

