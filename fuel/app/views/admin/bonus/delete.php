<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php if (isset($bonus)): ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('admin/bonus/_view', array('bonus' => $bonus)); ?>
<?php endif; ?>



