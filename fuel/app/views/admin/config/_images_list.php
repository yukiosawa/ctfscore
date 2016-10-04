<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-3">ファイル名</th><th class="col-md-2">プレビュー</th><th class="col-md-7"></th>
    </tr>
  </thead>

  <tbody>
     <?php foreach ($images as $image): ?>
       <tr>
         <td><?php echo $image['filename']; ?></td>
         <td><a href="<?php echo $image['url']; ?>" target="_blank"><img src="<?php echo $image['url']; ?>" style="width:auto; height:auto; max-width:50px; max-height:50px; vertical-align: middle;" /></a></td>
         <td>
           <!-- 削除ボタン  -->
           <?php echo render('admin/config/_delete_form', array('action' => Uri::base(false).'admin/config/deletefile/', 'filename' => $image['filename'], 'name' => $name)); ?>
         </td>
       </tr>
    <?php endforeach; ?>
    <tr>
      <td /><td /><td><?php echo render('admin/config/_file_upload_form', array('name' => $name, 'mimetype' => 'image/*')); ?></td>
    </tr>
  </tbody>
</table>

