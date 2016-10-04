<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <table class="table">
    <thead>
      <tr>
        <th class="col-md-2">カテゴリ</th><th class="col-md-2">レベル</th><th class="col-md-2">レベル名称</th><th class="col-md-2">正解した問題数</th>
      </tr>
    </thead>

    <tbody>
      <?php $total_category_id = Model_Config::get_value('total_category_id'); ?>
      <?php $levels = array_merge($total_levels, $category_levels); ?>
      <?php foreach ($levels as $level): ?>
        <tr>
          <?php if ($level['category_id'] == $total_category_id): ?>
            <td>N/A</td>
          <?php else: ?>
            <td><?php echo $level['category']; ?></td>
          <?php endif; ?>
          <td><?php echo $level['level']; ?></td>
          <td><?php echo $level['name']; ?></td>
          <td><?php echo $level['criteria']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>


