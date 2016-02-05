<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-profile.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>

<?php
    if (Cookie::get('sound_on', '1')) {
        printf('<audio src="%s" autoplay loop></audio>', Config::get('ctfscore.sound.complete_sound'));
    }
?>

<style>
nav.navbar { display: none; }
</style>

<div style="text-align: center;">
    <div class="fire big">
        <?php echo $score; ?>位 
        <span id="username"><?php echo $username; ?></span>殿
	    <span><?php foreach ($profile['levels'] as $level) { echo " " . $level; } ?></span>
    </div>
    <br>
    <div class="fire">貴殿は場阿忍愚CTFにおいて頭書の成績を収められた<br>よってその栄誉をたたえ、ここに表彰す</div>

    <div class="row">
      <!-- チャート描画エリア -->
      <div class="col-md-5">
        <canvas id="myChart" width="400" height="400"></canvas>
      </div>
      <!-- チャート凡例 -->
      <div class="col-md-2">
      </div>
      <!-- 元データ表示エリア -->
      <div class="col-md-5">
        <div id="chart-data"><span></span></div>
      </div>
    </div>
</div>

<script>
 $(function(){
     var usernames = [];
     usernames[0] = $('#username').text();
     print_profile_chart(usernames);
 });
</script>
