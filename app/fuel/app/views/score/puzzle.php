<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>


<!-- 問題一覧 -->
<div class="row">
  <div class="col-md-12">
    <table class="table table-hover">
      <thead>
	<tr>
	  <th>番号</th><th>カテゴリ</th><th>タイトル</th><th>ポイント</th><th>レビュー</th>
	</tr>
      </thead>
      <tbody>
	<?php
	foreach ($puzzles as $puzzle)
	{
	    $puzzle_id = $puzzle['puzzle_id'];
	    // 回答済みは色を変える
	    if ($puzzle['answered'])
	    {
		echo "<tr class='success'>";
	    }
	    else
	    {
		echo "<tr>";
	    }
	    // ID
	    echo "<td>".$puzzle['puzzle_id']."</td>";
	    // カテゴリ
	    echo "<td>".$puzzle['category']."</td>";
	    // タイトル
	    echo "<td><a href='#puzzle".$puzzle_id."' data-toggle='modal'>".$puzzle['title']."</a></td>";
	    // ポイント
	    echo "<td>".$puzzle['point']."</td>";
	    // レビュー平均スコア
	    echo "<td><a href='/review/list/".$puzzle_id."'><div class='review' data-number='".\Config::get('ctfscore.review.max_data_number')."' data-score='".$puzzle['avg_score']."'></div></a></td>";
	    echo "</tr>\n";
	}
	?>
      </tbody>
    </table>
  </div>
</div>



<?php
// 問題本文
foreach ($puzzles as $puzzle) {
    $puzzle_id = $puzzle['puzzle_id'];

    echo "<div id='puzzle" . $puzzle_id . "' class='modal fade'>";
    echo "<div class='modal-dialog'>";
    echo "<div class='modal-content'>";

    echo "<div class='modal-header'>";
    echo "<h4 class='modal-title'>";
    // カテゴリ, ポイント
    echo "<div>".$puzzle['category']." ".$puzzle['point']."</div>";
    // タイトル
    echo "<div>".$puzzle['title']."</div>";
    echo "</h4>";
    echo "</div>";

    echo "<div class='modal-body'>";
    // 本文, 添付ファイル
    echo "<p>".$puzzle['content']."</p>";
    //echo "<p>".n2br($puzzle['content'])."</p>";
    foreach ($puzzle['attachments'] as $filename => $val)
    {
	// ダウンロードページへのリンク
	echo "<p><a href='/download/puzzle?id=".$puzzle_id."&file=".$val."'>".$val."</a></p>";
    }
    echo "</div>";

    echo "<div class='modal-footer'>";
    echo "<button class='btn btn-default' data-dismiss='modal'>閉じる</button>";
    echo "</div>";

    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "\n\n";
}
?>

