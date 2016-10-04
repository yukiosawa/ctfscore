<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = Uri::current();
echo render('admin/config/_chart_color_form', array('action' => $action, 'config_chart_color' => $config_chart_color));
?>

