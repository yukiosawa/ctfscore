<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-profile.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>

<style>
    .container-main { 
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<div id="overlay" style="display: block; background-position: center center; background-image: url(<?php echo Asset::get_file('diploma.jpg', 'img')?>);">
    <div class="container">
        <div class="container-main">
            <div><?php echo Asset::img(Config::get('ctfscore.logo_image'), array('class' => 'img-responsive')); ?></div>
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
    </div>
</div>

<script>
 $(function(){
     var usernames = [];
     usernames[0] = $('#username').text();
     print_profile_chart(usernames);
 });
</script>
