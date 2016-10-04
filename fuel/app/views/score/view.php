<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<?php echo Asset::js('jquery.floatThead.min.js'); ?>

<script>
    $(function(){
        $('#ranking-table').tablesorter({
            headers: {
                0: {
                    sorter: false
                }
            }
        }).floatThead({zIndex: 100});
    });
</script>

<div id="ranking">
  <div class="row">
    <div class="col-md-12">
      <form  action="<?php echo Uri::base(false) . 'score/profile'; ?>" method="post">
        <table id="ranking-table" class="table table-condensed table-hover tablesorter">
        <thead>
          <tr>
            <th class="fix-min"><input type="submit" value="比較"></input></th>
            <th class="fix-min">ランク</th><th class="fix-lg">ユーザ</th><th>合計</th>
            <?php
            /* $alias = array(
               '超文書転送術' => '超文書<br>転送術'
               );
               foreach ($categories as $values) {
               $category = $values['category'];
               echo '<th>'.((isset($alias[$category]) === true) ? $alias[$category] : $category).'</th>';
               } */
              foreach ($categories as $values) {
                  $category = $values['category'];
                  echo '<th>'.$category.'</th>';
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

                  echo '<td><input type="checkbox" name="usernames[]" value="' . $score['username'] . '"></td>';
                  echo '<td>' . $rank . '</td>';
                  $levelname = empty($score['name']) ? '' : '['.$score['name'].']';
                  echo '<td><a href="'.Uri::base(false).'score/profile/' . $score['username'] . '">' . $score['username'] . '</a><div>' . $levelname . '</div></td>';
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
      </form>
    </div>
  </div>
</div>
