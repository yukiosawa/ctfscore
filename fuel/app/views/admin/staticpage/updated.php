<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('admin/staticpage/list', array('pages' => array($page))); ?>
<?php endif; ?>


