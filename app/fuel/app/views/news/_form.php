<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="comment">お知らせコメント</label>
    <textarea class="form-control" id="comment" name="comment" rows="5"><?php if(isset($news)) echo $news['comment']; ?></textarea>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">投稿する</button>
  </div>

</form>

