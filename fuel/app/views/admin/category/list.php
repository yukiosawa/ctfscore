<table class="table table-hover">
  <thead>
    <tr>
      <th class="col-md-1">ID</th><th class="col-md-2">カテゴリ</th><th class="col-md-2"></th>
    </tr>
  </thead>

  <tbody>
     <?php foreach ($categories as $category): ?>
       <tr>
         <td><?php echo $category['id']; ?></td>
         <td><?php echo $category['category']; ?></td>
         <td>
           <?php if (isset($is_editable) && $is_editable == true): ?>
             <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/category/edit/'.$category['id']; ?>">編集</a>
             <?php echo render('admin/category/_delete', array('action' => Uri::base(false).'admin/category/delete', 'category_id' => $category['id'])); ?>
           <?php endif; ?>
         </td>
       </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (isset($is_editable) && $is_editable == true): ?>
  <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/category/create'; ?>">新規追加する</a>
<?php endif; ?>
