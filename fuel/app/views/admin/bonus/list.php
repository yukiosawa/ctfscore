<a href="<?php echo Uri::base(false).'admin/bonus/create/'; ?>" class="btn btn-primary">ボーナス点付与</a>

<table id="review-table" class="table table-hover tablesorter">
  <thead>
    <tr>
      <th class="col-md-1">No</th><th class="col-md-2">ユーザ</th><th class="col-md-2">ポイント</th><th class="col-md-6">説明</th><th class="col-md-2">更新者</th><th class="col-md-2">更新日時</th><th class="col-md-1"></th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($bonus as $item): ?>
    <tr>
      <td><?php echo $item['id'] ?></td>
      <td><?php echo $item['username']; ?></td>
      <td><?php echo $item['bonus_point'] ?></td>
      <td><?php echo nl2br($item['comment']); ?></td>
      <td><?php echo $item['updated_by'] ?></td>
      <td><?php echo $item['updated_at']; ?></td>
      <td>
	  <?php
	  $del_path = Uri::base(false).'/admin/bonus/delete';
	  ?>
	  <?php echo render('admin/bonus/_delete', array('action' => $del_path, 'id' => $item['id'])); ?>

      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>


