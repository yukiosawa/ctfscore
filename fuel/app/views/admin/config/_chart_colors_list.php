<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th class="col-md-1">ランク</th><th class="col-md-2">色設定値</th><th class="col-md-2">プレビュー</th><th class="col-md-7"></th>
    </tr>
  </thead>

  <tbody>
     <?php foreach ($config_chart_colors as $item): ?>
       <tr>
         <td><?php echo $item['rank']; ?></td>
         <td><?php echo $item['color']; ?></td>
         <td><canvas width="100" height="5" id="chart<?php echo $item['id']; ?>"></canvas></td>
         <script>
          var canvas = document.getElementById("chart<?php echo $item['id']; ?>");
          var context = canvas.getContext('2d');
          context.beginPath();
          context.strokeStyle = "<?php echo $item['color']; ?>";
          context.lineWidth = 4;
          context.moveTo(0, 0);
          context.lineTo(100, 0);
          context.stroke();
         </script>
         <td>
           <a href="<?php echo Uri::base(false).'admin/config/editcolor/'.$item['id']; ?>" class="btn btn-sm btn-primary">編集</a>
           <?php echo render('admin/config/_delete_form', array('action' => Uri::base(false).'admin/config/deletecolor', 'id' => $item['id'])); ?>
         </td>
       </tr>
    <?php endforeach; ?>
    <tr><td /><td /><td /><td><a class="btn btn-sm btn-primary" href="<?php echo Uri::base(false).'admin/config/editcolor/new'; ?>">さらに追加する</a></td></tr>
  </tbody>
</table>



