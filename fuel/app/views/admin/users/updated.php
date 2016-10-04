<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <table class="table">
    <thead>
      <tr>
        <?php if (empty($user['password'])): ?>
        <th class="col-md-1">ID</th><th class="col-md-2">ユーザ名</th><th class="col-md-2">グループ</th>
        <?php else: ?>
        <th class="col-md-1">ID</th><th class="col-md-2">ユーザ名</th><th class="col-md-2">パスワード</th><th class="col-md-2">グループ</th>
        <?php endif; ?>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td><?php echo $user['id'] ?></td>
        <td><?php echo $user['username']; ?></td>
        <?php if (!empty($user['password'])): ?>
          <td><?php echo $user['password']; ?></td>
        <?php endif; ?>
        <td><?php echo $user['group'] == Model_Config::get_value('admin_group_id') ? '管理者' : '一般' ?></td>
        </tr>
    </tbody>
  </table>
<?php endif; ?>
