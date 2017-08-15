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
           <td class="text-center lead"><?php echo empty($status['start_time']) ? 'N/A' : $status['start_time']; ?></td>
           <td class="text-center lead"><?php echo empty($status['end_time']) ? 'N/A' : $status['end_time']; ?></td>
       </tr>
    </tbody>
  </table>
</div>

<?php if ($status['ended'] != true): ?>
    <?php $countdown = Model_Config::get_value('is_active_countdown'); ?>
    <?php if ($countdown && !empty($status['end_time']) && !empty($status['start_time'])): ?>
      <div id="countdown" class="row"></div>
      <script>
        $(function () {
            // 秒 -> ミリ秒
            startCountdown(<?php echo strtotime($status['before'] ? $status['start_time'] : $status['end_time']) * 1000; ?>);
        });
      </script>
    <?php endif; ?>
    <br>
<?php else: ?>
    <p class="lead text-center"><?php echo '終了しました'; ?></p>
<?php endif; ?>
