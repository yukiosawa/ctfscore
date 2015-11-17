<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<script>
    $(function(){
        $('#ranking-table').tablesorter();
    });
</script>

<p>
<div id="ranking">
  <div class="row">
    <div class="col-md-12">
      <table id="ranking-table" class="table table-condensed table-hover tablesorter">
      <thead>
        <tr>
          <th style="width: 55px;">ランク</th><th style="width: 120px;">ユーザ</th><th>合計</th>
          <?php
              $alias = array(
                  '超文書転送術' => '超文書<br>転送術'
              );
              foreach ($categories as $category) {
                  echo '<th>'.((isset($alias[$category]) === true) ? $alias[$category] : $category).'</th>';
              }
          ?>
          <!--<th>更新時刻</th>-->
        </tr>
      </thead>
      <tbody>
        <?php
        $rank = 1;
        foreach ($scoreboard as $score) {
            /* 自分の行を強調表示 */
            if ($my_name == $score['username']) {
                echo "<tr class='success'>";
            } else {
                echo "<tr>";
            }
            echo "<td>" . $rank . "</td>";
            $levelname = empty($score['name']) ? 'PC使えるふり' : $score['name'];
            echo "<td><a href=/score/profile/" . $score['username'] . ">" . $score['username'] . "</a><div>[" . $levelname . "]</div></td>";
            echo "<td>" . $score['totalpoint'] . "</td>";
            foreach ($categories as $category) {
                echo "<td>".$score[$category]."</td>";
            }
            //echo "<td>" . $score['pointupdated_at'] . "</td>";
            echo "</tr>\n";
            $rank++;
        }
        ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</p>

