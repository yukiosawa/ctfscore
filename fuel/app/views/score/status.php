<?php echo Asset::js('flipclock.min.js'); ?>
<?php echo Asset::js('ctfscore-flipclock.js'); ?>

<div class="row">
  <table class="table">
    <thead>
       <tr>
           <th class="text-center">開始時刻</th>
           <th class="text-center">終了時刻</th>
       </tr>
    </thead>
    <tbody>
        <tr>
           <td class="text-center lead"><?php echo $start_time; ?></td>
           <td class="text-center lead"><?php echo $end_time; ?></td>
       </tr>
    </tbody>
  </table>
</div>

<?php $countdown = Config::get('ctfscore.countdown'); ?>
<?php if ($countdown && $end_time): ?>
  <div id="countdown" class="row"></div>
  <script>
    $(function () {
        // 秒 -> ミリ秒
        startCountdown(<?php echo strtotime($end_time) * 1000; ?>);
    });
  </script>
<?php endif; ?>
<br>
<div class="row">
    <p class="lead text-center"><?php echo $status; ?></p>
</div>
