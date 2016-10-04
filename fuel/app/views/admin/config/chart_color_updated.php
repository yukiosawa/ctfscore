<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <table class="table table-hover table-condensed">
    <thead>
      <tr>
        <th class="col-md-1">ランク</th><th class="col-md-2">色設定値</th><th class="col-md-7"></th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td><?php echo $config_chart_color['rank']; ?></td>
        <td><?php echo $config_chart_color['color']; ?></td>
        <td>
          <canvas width="100" height="5" id="chart<?php echo $config_chart_color['id']; ?>"></canvas>
        </td>
        <script>
         var canvas = document.getElementById("chart<?php echo $config_chart_color['id']; ?>");
         var context = canvas.getContext('2d');
         context.beginPath();
         context.strokeStyle = "<?php echo $config_chart_color['color']; ?>";
         context.lineWidth = 4;
         context.moveTo(0, 0);
         context.lineTo(100, 0);
         context.stroke();
        </script>
    </tbody>
  </table>
<?php endif; ?>


