<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="start_time">開始時刻</label>
    <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?php echo $start_time; ?>" placeholder="YYYY-MM-DD HH:MM:SS"></input>
  </div>

  <div class="form-group">
    <label for="end_time">終了時刻</label>
    <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?php echo $end_time; ?>" placeholder="YYYY-MM-DD HH:MM:SS"></input>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>

