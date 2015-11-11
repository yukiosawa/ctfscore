<?php
if (!empty($errmsg)) {
  echo "<div class='alert alert-danger'>$errmsg</div>";
}
?>

<ul class="nav nav-tabs">
  <li role="presentation"><a href="/auth/login">ログイン</a></li>
  <li role="presentation" class="active"><a href="#">新規ユーザー作成</a></li>
</ul>
<br>

<p class='h4'>
  新規ユーザ登録します。
</p>
<p>
  パスワードを忘れるとログインできませんので注意してください。(リセットはできません)
</p>

<form class="form-horizontal" action="/auth/created" method="POST">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
  <div class="form-group">
    <label class="col-md-2 control-label" for="username">ユーザ名</label>
    <div class="col-md-4">
      <input class="form-control" id="username" type="text" name="username" value="" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label" for="password">パスワード</label>
    <div class="col-md-4">
      <input class="form-control" id="password" type="password" name="password" value="" />
    </div>
  </div>

  <!-- 遵守事項 -->
  <p><?php File::read($file, false); ?></p>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-4">
      <!-- <button type="submit" class="btn btn-primary">ユーザ作成</button> -->
      <button type="submit" class="btn btn-primary">上記に同意してユーザ作成する</button>
    </div>
  </div>
</form>

