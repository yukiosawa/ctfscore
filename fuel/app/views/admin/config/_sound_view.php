<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-3">ファイル名</th><th class="col-md-4">再生</th><th class="col-md-7"></th>
    </tr>
  </thead>

  <tbody>
       <tr>
         <td><?php echo $sound['filename']; ?></td>
         <td>
           <?php if (!empty($sound['filename'])): ?>
             <?php echo Html::audio($sound['url'], 'controls'); ?>
           <?php endif; ?>
         </td>
         <td>
           <!-- アップロードボタン  -->
           <?php echo render('admin/config/_file_upload_form', array('name' => $name, 'mimetype' => 'audio/*')); ?>
           <!-- 削除ボタン  -->
           <?php if (!empty($sound['filename'])): ?>
             <?php echo render('admin/config/_delete_form', array('action' => Uri::base(false).'admin/config/deletefile/', 'filename' => $sound['filename'], 'name' => $name)); ?>
           <?php endif; ?>
         </td>
       </tr>

  </tbody>
</table>
