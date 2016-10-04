<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <table class="table">
    <thead>
      <tr>
	<th>名前</th><th>設定値</th><th>説明</th>
      </tr>
    </thead>

    <tbody>
      <tr>
	<td><?php echo $config['name']; ?></td>
	<td><?php echo $config['value']; ?></td>
	<td><?php echo $config['description']; ?></td>
      </tr>
    </tbody>
  </table>
<?php endif; ?>


