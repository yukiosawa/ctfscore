<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="name">名前</label>
    <?php echo $config['name']; ?>
  </div>

  <div class="form-group">
    <label for="description">説明</label>
    <?php echo $config['description']; ?>
  </div>

  <div class="form-group">
    <label for="value">設定値</label>
    <input type="text" class="form-control" id="value" name="value" rows="2" value="<?php echo $config['value']; ?>"></input>
  </div>

  <input type="hidden" name="id" value=<?php echo $config['id']; ?> />

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>

