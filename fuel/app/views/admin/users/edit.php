<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = Uri::current();
$admin = $user['group'] == Model_Config::get_value('admin_group_id') ? true : false;
echo render('users/_form', array('action' => $action, 'username' => $user['username'], 'admin' => $admin));
?>
