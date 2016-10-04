<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="username">ユーザ名</label>
    <?php if (empty($username)): ?>
      <input type="text" class="form-control" id="username" name="username"></input>
    <?php else: ?>
      <input type="text" readonly class="form-control" id="username" name="username" value=<?php echo $username; ?>></input>
    <?php endif; ?>
  </div>

  <div class="checkbox">
    <label>
      <?php if ($admin == true): ?>
        <input type="checkbox" name="admin" value="1" checked>管理者権限</input>
      <?php else: ?>
        <input type="checkbox" name="admin" value="1">管理者権限</input>
      <?php endif; ?>
    </label>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">登録する</button>
  </div>

</form>

