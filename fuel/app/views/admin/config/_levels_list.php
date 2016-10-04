<div class="row">
  <div class="col-md-8">
    <table class="table table-hover table-condensed">
      <thead>
        <tr>
          <th class="col-md-2">カテゴリ</th><th class="col-md-2">レベル</th><th class="col-md-2">レベル名称</th><th class="col-md-2">正解した問題数</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($levels as $level): ?>
          <tr>
            <td>
              <?php if ($level['category_id'] == $total_category_id): ?>
                N/A
              <?php else: ?>
                <?php echo $level['category']; ?></td>
              <?php endif; ?>
            <td><?php echo $level['level']; ?></td>
            <td><?php echo $level['name']; ?></td>
            <td><?php echo $level['criteria']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>
  <div class="col-md-1">
    <a href="<?php echo Uri::base(false).'admin/config/editlevels'; ?>" class="btn btn-sm btn-primary">編集</a>
  </div>
</div>
