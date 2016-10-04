<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
      <label for="name">ランク</label>
      <input type="text" class="form-control" id="rank" name="rank" rows="2" value="<?php echo $config_chart_color['rank']; ?>"></input>
  </div>

  <div class="form-group">
    <label for="color">色設定値</label>
    <input type="color" class="form-control" id="color" name="color" rows="2" value="<?php echo isset($config_chart_color) ? $config_chart_color['color'] : ''; ?>"></input>
  </div>

  <?php if (isset($config_chart_color)): ?>
    <input type="hidden" name="id" value=<?php echo $config_chart_color['id']; ?> />
    <div>
      <button class="btn btn-primary" type="submit">更新する</button>
    </div>
  <?php else: ?>
    <input type="hidden" name="id" value="10000" />
    <div>
      <button class="btn btn-primary" type="submit">追加する</button>
    </div>
  <?php endif; ?>

</form>

