<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="username">ユーザ名</label>
    <input type="text" class="form-control" id="username" name="username" rows="2" value="<?php if(isset($bonus)) echo $bonus['username']; ?>"></input>
  </div>

  <div class="form-group">
    <label for="bonus_point">ボーナス点</label>
    <input type="text" class="form-control" id="bonus_point" name="bonus_point" rows="1" value="<?php if(isset($bonus)) echo $bonus['bonus_point']; ?>"></input>
  </div>

  <div class="form-group">
    <label for="comment">コメント</label>
    <textarea class="form-control" id="comment" name="comment" rows="9"><?php if(isset($bonus)) echo $bonus['comment']; ?></textarea>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">ボーナス点を付与</button>
  </div>

</form>

