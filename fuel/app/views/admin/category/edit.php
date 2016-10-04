<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<?php
$action = $_SERVER['REQUEST_URI'];
echo render('admin/category/_form', array('action' => $action, 'category' => $category));
?>

