<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo Uri::current(); ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <?php $total_category_id = Model_Config::get_value('total_category_id'); ?>
  <h4>全体のレベル</h4>
  <?php echo render('admin/config/_levels_form', array('id' => 'total_levels', 'levels' => $total_levels, 'categories' => $categories, 'total_category_id' => $total_category_id)); ?>
  
  <h4>カテゴリごとのレベル</h4>
  <?php echo render('admin/config/_levels_form', array('id' => 'category_levels', 'levels' => $category_levels, 'categories' => $categories)); ?>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>
