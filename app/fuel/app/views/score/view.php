<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<script>
    $(function(){
        $('#ranking-table').tablesorter();
    });
</script>

<?php if (!($my_name == '' || $my_name == 'guest')): ?>
<div class="row">
  <div class="col-md-9 col-md-offset-3">
    <div id="send-answer">
      <form class="form-inline" action="/score/submit" method="post">
        <?php /*
        <!-- CSRF対策 -->
        <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
        */ ?>
        <div class="form-group col-md-6">
          <label class="sr-only" for="answertext">Answer</label>
          <input class="form-control" id="answertext" name="answer" type="text" placeholder="flag を入力"></input>
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary" type="submit">回答する</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<p>
<div id="ranking">
  <div class="row">
    <div class="col-md-12">
      <table id="ranking-table" class="table table-condensed table-hover tablesorter">
      <thead>
        <tr>
          <th style="width: 52px;">ランク</th><th style="width: 98px;">ユーザ</th><th>合計</th>
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
            echo "<td><a href=/score/profile/" . $score['username'] . ">" . $score['username'] . "</a></td>";
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

