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
          <th style="width: 55px;">ランク</th><th style="width: 135px;">ユーザ</th><th>合計</th>
          <?php
              $alias = array(
                  '超文書転送術' => '超文書<br>転送術'
              );
              foreach ($categories as $values) {
                  $category = $values['category'];
                  echo '<th>'.((isset($alias[$category]) === true) ? $alias[$category] : $category).'</th>';
              }
          ?>
        </tr>
      </thead>
      <tbody>
        <?php
            $rank = 1;
            $point_sum = (int)array_sum(array_map(function ($var) { return $var['point']; }, $categories));
            $score_decorate = function ($a, $b) {
                if ($a >= $b) {
                    return sprintf('<span class="text-success">%s</span>', $a);
                }

                if ($a === 0) {
                    return sprintf('<span class="text-danger">%s</span>', $a);
                }

                return sprintf('<span class="text-warning">%s</span>', $a);
            };

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
                echo "<td>" . $score_decorate((int)$score['totalpoint'], $point_sum) . "</td>";
                foreach ($categories as $values) {
                    echo "<td>" . $score_decorate($score[$values['category']], $values['point']) . "</td>";
                }
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

