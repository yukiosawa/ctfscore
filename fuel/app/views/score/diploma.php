<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-profile.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>

<?php
if ($sound_on) {
    if ($diploma_sound_url != '') {
        printf('<audio src="%s" autoplay loop></audio>', $diploma_sound_url);
    }
}
?>

<style>
 nav.navbar { display: none; }
</style>

<div style="text-align: center;">
  <div class="fire big">
    <?php echo $score; ?>位 
    <span id="username"><?php echo $username; ?></span>
    <span><?php foreach ($profile['levels'] as $level) { echo " " . $level; } ?></span>
  </div>
  <br>
  <div class="fire">貴殿は<?php if ($ctf_name) echo $ctf_name.'において'; ?>頭書の成績を収められた<br>よってその栄誉をたたえ、ここに表彰す</div>

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
</div>

<script>
 $(function(){
     var usernames = [];
     usernames[0] = $('#username').text();
     print_progress_chart(usernames);
 });
</script>
