<?php
$segs = Uri::segments();
$type = end($segs);
if ($type == 'success') {
    $item_display = '正解時に表示するメッセージ';
}
else if ($type == 'failure') {
    $item_display = '不正解時に表示するメッセージ';
}
?>

<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo Uri::current(); ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <?php echo render('admin/config/_texts_form.php', array('items' => $texts, 'item_name' => 'texts', 'item_display' => $item_display, 'item_val_name' => 'text')); ?>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>
