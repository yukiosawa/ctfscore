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
           <td class="text-center lead"><?php echo empty($start_time) ? 'N/A' : $start_time; ?></td>
           <td class="text-center lead"><?php echo empty($end_time) ? 'N/A' : $end_time; ?></td>
       </tr>
    </tbody>
  </table>
</div>

<?php if ($status !== '終了しました'): ?>
    <?php $countdown = Model_Config::get_value('is_active_countdown'); ?>
    <?php if ($countdown && !empty($end_time) && !empty($start_time)): ?>
      <div id="countdown" class="row"></div>
      <script>
        $(function () {
            // 秒 -> ミリ秒
            startCountdown(<?php echo strtotime($status == '開始前です' ? $start_time : $end_time) * 1000; ?>);
        });
      </script>
    <?php endif; ?>
    <br>
<?php endif; ?>
<div class="row">
    <p class="lead text-center"><?php echo $status; ?></p>
</div>
