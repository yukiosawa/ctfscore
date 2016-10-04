<table class="table table-hover">
  <thead>
    <tr>
      <th class="col-md-2">名称</th><th class="col-md-8">ページ本文</th><th class="col-md-1">状態</th><th class="col-md-1"></th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($pages as $page): ?>
      <tr>
        <td><a href="<?php echo Uri::base(false).$page['path']; ?>" target="_blank"><?php echo $page['display_name']; ?></a></td>
        <td><?php echo $page['content']; ?></td>
        <td><?php echo $page['is_active'] == 1 ? '有効' : '無効'; ?></td>
        <td>
          <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/staticpage/edit/'.$page['name']; ?>">編集</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>

</table>

