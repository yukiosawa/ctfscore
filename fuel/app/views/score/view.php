<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<?php echo Asset::js('jquery.floatThead._.js'); ?>
<?php echo Asset::js('jquery.floatThead.js'); ?>

<script>
    $(function(){
        $('#ranking-table').tablesorter().floatThead();
    });
</script>

<div id="ranking">
  <div class="row">
    <div class="col-md-12">
      <table id="ranking-table" class="table table-condensed table-hover tablesorter">
        <thead>
          <tr>
            <th class="fix-min">ランク</th><th class="fix-lg">ユーザ</th><th>合計</th>
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
              $point_sum = (int)array_sum(array_map(function ($var) { return $var['point']; }, $categories));
              $score_decorate = function ($a, $b) {
                  $class = '';

                  if ($a >= $b) {
                      $class = 'perfect';
                  }

                  if ($a === 0) {
                      $class = 'tryharder';
                  }

                  return ($class !== '') ? sprintf('<span class="%s">%s</span>', $class, $a) : sprintf('<span>%s</span>', $a);
              };

              $rank = 1;
              foreach ($scoreboard as $score) {
                  /* 自分の行を強調表示 */
                  if ($my_name == $score['username']) {
                      echo '<tr class="success">';
                  } else {
                      echo '<tr>';
                  }

                  echo '<td>' . $rank . '</td>';
                  $levelname = empty($score['name']) ? 'PC使えるふり' : $score['name'];
                  echo '<td><a href="/score/profile/' . $score['username'] . '">' . $score['username'] . '</a><div>[' . $levelname . ']</div></td>';
                  echo '<td><span class="totalpoint' . (($score['totalpoint'] >= $point_sum) ? ' perfect' : '') . '">' . $score['totalpoint'] . '</span></td>';
                  foreach ($categories as $values) {
                      echo '<td>' . $score_decorate($score[$values['category']], $values['point']) . '</td>';
                  }
                  echo '</tr>';
                  $rank++;
              }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
