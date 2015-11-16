<?php echo Asset::js('flipclock.min.js'); ?>
<?php echo Asset::js('ctfscore-flipclock.js'); ?>

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

<div class="row">
  <div class="col-md-12">
      <p>
      <?php echo $status; ?>
      </p>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
      <table class="table">
	<thead>
	</thead>
	<tbody>
	    <tr><td>開始時刻</td><td><?php echo $start_time; ?></td></tr>
	    <tr><td>終了時刻</td><td><?php echo $end_time; ?></td></tr>
	</tbody>
      </table>
  </div>
</div>

