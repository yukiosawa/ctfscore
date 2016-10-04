<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo View::forge('admin/puzzle/list')->set_safe(array('puzzles' => $puzzles))->render(); ?>
<?php endif; ?>
