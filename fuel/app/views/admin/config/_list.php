<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-3">名前</th><th class="col-md-2">設定値</th><th class="col-md-1"></th><th class="col-md-6">説明</th>
    </tr>
  </thead>

  <tbody>
     <?php foreach ($config as $item): ?>
       <tr>
         <td><?php echo $item['name']; ?></td>
         <td><?php echo $item['value']; ?></td>
         <td>
           <?php if (!isset($readonly) || $readonly != true): ?>
             <a href="<?php echo Uri::base(false).'admin/config/edit/'.$item['id']; ?>" class="btn btn-sm btn-primary">編集</a>
           <?php endif; ?>
         </td>
         <td><?php echo $item['description']; ?></td>
       </tr>
    <?php endforeach; ?>
  </tbody>
</table>

