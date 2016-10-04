<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-3">ファイル名</th><th class="col-md-4">再生</th><th class="col-md-5"></th>
    </tr>
  </thead>

  <tbody>

     <?php foreach ($sounds as $sound): ?>
       <tr>
         <td><?php echo $sound['filename']; ?></td>
         <td><?php echo Html::audio($sound['url'], 'controls'); ?></td>
         <td>
           <!-- 削除ボタン  -->
           <?php echo render('admin/config/_delete_form', array('action' => Uri::base(false).'admin/config/deletefile/', 'filename' => $sound['filename'], 'name' => $name)); ?>
         </td>
       </tr>
    <?php endforeach; ?>
    <tr>
      <td /><td /><td><?php echo render('admin/config/_file_upload_form', array('name' => $name, 'mimetype' => 'audio/*')); ?></td>
    </tr>
  </tbody>
</table>

