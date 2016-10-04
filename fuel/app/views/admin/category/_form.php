<form class="form-horizontal" action="<?php echo $action; ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <div class="col-md-2">
      <label for="category_id">ID</label>
      <input type="text" class="form-control" id="category_id" name="category_id" value="<?php echo $category['id']; ?>" placeholder="N/A" readonly></input>
    </div>
    <div class="col-md-6">
      <label for="category">カテゴリ</label>
      <input type="text" class="form-control" id="category" name="category" value="<?php echo $category['category']; ?>" placeholder="カテゴリの名称"></input>
    </div>
  </div>
  
  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>
