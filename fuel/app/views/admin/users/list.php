<a href="<?php echo Uri::base(false).'admin/users/create/'; ?>" class="btn btn-primary">新規ユーザ作成</a>

<table class="table table-hover tablesorter">
  <thead>
    <tr>
      <th class="col-md-1">ID</th><th class="col-md-2">ユーザ</th><th class="col-md-2">グループ</th><th class="col-md-7"></th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
      <td><?php echo $user['id'] ?></td>
      <td><?php echo $user['username']; ?></td>
      <td><?php echo $user['group'] == Model_Config::get_value('admin_group_id') ? '管理者' : '一般'; ?></td>
      <td>
        <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/users/edit/'.$user['id']; ?>">編集</a>
        <?php echo render('users/_delete', array('action' => Uri::base(false).'admin/users/delete', 'username' => $user['username'])); ?>
        <?php echo render('users/_pwreset', array('action' => Uri::base(false).'admin/users/pwreset', 'username' => $user['username'])); ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

