<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = Uri::current();
echo render('admin/config/_time_form', array('action' => $action, 'start_time' => $start_time, 'end_time' => $end_time));
?>

