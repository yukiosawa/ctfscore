<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('admin/category/list', array('categories' => $categories)); ?>
<?php endif; ?>
