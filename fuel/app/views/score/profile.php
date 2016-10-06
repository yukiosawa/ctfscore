<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-profile.js'); ?>
<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::css('animate.css'); ?>
<?php echo Asset::js('jquery.lettering.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>
<?php echo Asset::js('jquery.tablesorter.min.js'); ?>

<script>
    $(function(){
        print_progress_chart(<?php echo json_encode($usernames); ?>);
        print_profile_detail(<?php echo json_encode($usernames); ?>);
    });
</script>


<div id="errmsg"></div>

<div class="row">
  <!-- チャート描画エリア -->
  <div class="col-md-6">
    <canvas id="myChart" width="400" height="400"></canvas>
  </div>
  <!-- 元データ表示エリア -->
  <div class="col-md-6">
    <div id="chart-data"><span></span></div>
  </div>
</div>

<!-- 詳細表示 -->
<div id="profile-detail"></div>

