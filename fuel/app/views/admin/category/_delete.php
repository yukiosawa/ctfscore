<form style="display: inline;" action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <span class="form-group">
    <input type="hidden" class="form-control" name="category_id" value="<?php echo $category_id ?>">
  </span>

  <span>
    <button class="btn btn-primary" type="submit" onclick="return confirm('削除するカテゴリに紐づく問題も削除されます。また、それら問題のポイントは全ユーザの獲得済スコアからも削除されます。削除しますか？')">削除</button>
  </span>

</form>

