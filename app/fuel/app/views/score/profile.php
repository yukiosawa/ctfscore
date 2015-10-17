<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-profile.js'); ?>
<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::css('animate.css'); ?>
<?php echo Asset::js('jquery.lettering-0.6.min.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>

<div id="errmsg"></div>

<div class="row">
  <div class="col-md-7">
    <div class="h3">
      <span id="my_username"><?php echo $profile['username'] ?></span>
    </div>
    <?php if (!empty($profile['levels'])): ?>
      <div id='level_name' class="h3">
	またの名を
	<!-- <span class="h3"> -->
	<span>
	  <?php
	  foreach ($profile['levels'] as $level) {
	      echo "　".$level;
	  }
	  ?>
	</span>
      </div>
    <?php endif; ?>
  </div>
  <div class="col-md-5">
    <div class="col-md-6">
      <input id="username" type='text' class="form-control col-md-8" placeholder="比較したいユーザ"></input>
    </div>
    <div class="col-md-6">
    <button onclick="update_chart();" class="btn btn-primary">比較</button>
    <button onclick="$('#username').val(''); location.reload();" class="btn btn-primary">リセット</button>
    </div>
  </div>
</div>

<p></p>
<div class="row">
  <!-- チャート描画エリア -->
  <div class="col-md-5">
    <canvas id="myChart" width="400" height="400"></canvas>
  </div>
  <!-- チャート凡例 -->
  <div class="col-md-2">
    <div id="legend"></div>
  </div>
  <!-- 元データ表示エリア -->
  <div class="col-md-5">
    <div id="chart-data"><span></span></div>
  </div>
</div>


<p></p>
<div class="row">
  <div class="col-md-6">
    <p class="h4">正解した問題</p>
    <table class="table table-hover">
      <thead>
	<tr>
	  <th>カテゴリ</th><th>ポイント</th><th>タイトル</th>
	</tr>
      </thead>
      <tbody>
	<?php
	foreach ($profile['answered_puzzles'] as $puzzle)
	{
	    echo "<tr>";
	    // カテゴリ
	    echo "<td>".$puzzle['category']."</td>";
	    // ポイント
	    echo "<td>".$puzzle['point']."</td>";
	    // タイトル
	    echo "<td>".$puzzle['puzzle_id'].":".$puzzle['title']."</td>";
	    echo "</tr>\n";
	}
	?>
      </tbody>
    </table>
  </div>

  <div class="col-md-6">
    <p class="h4">投稿したレビュー</p>
    <table class="table table-hover">
      <thead>
	<tr>
	  <th>問題タイトル</th><th>評価</th><th>公開コメント</th>
	</tr>
      </thead>
      <tbody>
	<?php
	foreach ($profile['reviews'] as $review)
	{
	    echo "<tr>";
	    echo "<td>".$review['puzzle_id'].":".$review['puzzle_title']."</td>";
	    $num = \Config::get('ctfscore.review.max_data_number');
	    $score = $review['score'];
	    echo "<td><div class='review' data-number=".$num." data-score=".$score."><div></td>";
	    echo "<td>".$review['comment']."</td>";
	    echo "</tr>";
	}
	?>
      </tbody>
    </table>
  </div>

</div>


<script>
 $(function(){
     var usernames = [];
     usernames[0] = $('#my_username').text();
     print_profile_chart(usernames);
 });
</script>


