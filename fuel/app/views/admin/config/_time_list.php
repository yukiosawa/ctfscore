<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-2">開始時刻</th><th class="col-md-2">終了時刻</th><th class="col-md-2"></th></th><th class="col-md-6">説明</th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td><?php echo $start_time; ?></td>
      <td><?php echo $end_time; ?></td>
      <td>
        <a href="<?php echo Uri::base(false).'admin/config/settime/'; ?>" class="btn btn-sm btn-primary">編集</a>
        <?php echo render('admin/config/_delete_form', array('action' => Uri::base(false).'admin/config/deletetime/')); ?>
      </td>
      <td>CTFの開始終了時刻です。 [YYYY-MM-DD HH:MM:SS]</td>
    </tr>
  </tbody>
</table>

