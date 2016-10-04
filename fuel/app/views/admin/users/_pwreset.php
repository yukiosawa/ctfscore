<form style="display: inline;" action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <span class="form-group">
    <input type="hidden" class="form-control" name="username" value="<?php echo $username ?>">
  </span>

  <span>
    <button class="btn btn-primary" type="submit" onclick="return confirm('パスワードを初期化しますか？')">PWリセット</button>
  </span>

</form>

