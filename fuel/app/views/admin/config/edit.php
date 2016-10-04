<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
if (!empty($config)) {
    $action = Uri::current();
    echo render('admin/config/_form', array('action' => $action, 'config' => $config));
}
?>

