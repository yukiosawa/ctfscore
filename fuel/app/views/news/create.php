<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = $_SERVER['REQUEST_URI'];
echo render('news/_form', array('action' => $action, 'news' => $news));
?>



