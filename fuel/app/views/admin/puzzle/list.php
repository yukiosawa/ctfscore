<table class="table table-hover">
  <thead>
    <tr>
      <th class="col-md-1">ID</th><th class="col-md-2">カテゴリ</th><th class="col-md-2">タイトル</th><th class="col-md-1">ポイント</th><th class="col-md-1">ボーナス</th><th class="col-md-3">問題文</th><th class="col-md-2"></th>
    </tr>
  </thead>

  <tbody>
     <?php foreach ($puzzles as $puzzle): ?>
       <tr>
         <td><?php echo $puzzle['puzzle_id']; ?></td>
         <td><?php echo $puzzle['category']; ?></td>
         <td><?php echo $puzzle['title']; ?></td>
         <td><?php echo $puzzle['point']; ?></td>
         <td><?php echo $puzzle['bonus_point']; ?></td>
         <td><?php echo $puzzle['content']; ?></td>
         <td>
           <?php if ($is_editable == true): ?>
             <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/puzzle/edit/'.$puzzle['puzzle_id']; ?>">編集</a>
             <?php echo render('admin/puzzle/_delete_form', array('action' => Uri::base(false).'admin/puzzle/delete', 'puzzle_id' => $puzzle['puzzle_id'])); ?>
           <?php endif; ?>
         </td>
       </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if ($is_editable == true): ?>
  <a class="btn btn-primary" href="<?php echo Uri::base(false).'admin/puzzle/edit/new'; ?>">新規追加する</a>
<?php endif; ?>
