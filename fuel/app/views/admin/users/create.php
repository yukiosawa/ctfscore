<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = Uri::current();
echo render('admin/users/_form', array('action' => $action));
?>



