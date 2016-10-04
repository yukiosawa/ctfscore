<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
//$action = $_SERVER['REQUEST_URI'];
$action = Uri::current();
echo render('users/_form', array('action' => $action));
?>



