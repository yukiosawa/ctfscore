<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::js('jquery.tablesorter.min.js'); ?>

<script>
    $(function(){
        var token = '';

        $('#puzzle-table').tablesorter();

        $('.ctfscore-puzzle-modal').on('hidden.bs.modal', function () {
            var self = $(this);
            $('.ctfscore-hint-container-' + self.data('id')).hide();
            $('.ctfscore-hint-request', self).show();
        });

        $('.ctfscore-hint-request').click(function (e) {
            e.preventDefault();
            var self = $(this);
            $.get('/hint/token', function (response) {
                token = response['token'];
                $('.ctfscore-hint-container-' + self.data('id')).show();
                self.hide();
            });
        });

        $('.ctfscore-hint').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var action = '/hint/create/' + self.data('id');
            $.post(action, self.serialize() + '&<?php echo \Config::get('security.csrf_token_key');?>=' + token, function (response) {
                response['status'] ? self.text(response['message']) : alert(response['message']);
            }, 'json');
        });

        $('.ctfscore-hint-view').click(function () {
            var self = $(this);
            $('#ctfscore-hint-modal').modal({}, {
                'id': self.data('id'),
                'title': self.data('title')
            });
        });

        $('#ctfscore-hint-modal').on('show.bs.modal', function (e) {
            var self = $(this);
            $.get('/hint/view/' + e.relatedTarget.id, function (response) {
                $('.modal-header', self).text('ヒントリクエスト - ' + e.relatedTarget.title);
                var body = '<table class="table"><tr><th>ユーザ</th><th>コメント</th></tr>';
                $(response).each(function (index, values) {
                    body += '<tr><td>' + values['username'] + '</td><td>' + values['comment'] + '</td></tr>';
                });
                body += '</table>'
                $('.modal-body', self).html(body);
            }, 'json');
        });
   });
</script>

<!-- 問題一覧 -->
<div class="row">
  <div class="col-md-12">
    <table id="puzzle-table" class="table table-hover tablesorter">
      <thead>
    <tr>
      <th>ID</th><th>カテゴリ</th><th>タイトル</th><th>ポイント</th><th>回答者数</th><th>レビュー</th>
      <?php 
          if ($is_admin) {
              echo '<th>ヒント</th>';
          }
      ?>
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
        // 回答者数
        echo "<td>".$puzzle['gained']."</td>";
        // レビュー平均スコア
        echo "<td><a href='/review/list/".$puzzle_id."'><div class='review' data-number='".\Config::get('ctfscore.review.max_data_number')."' data-score='".$puzzle['avg_score']."'><span style='display:none'>".$puzzle['avg_score']."</span></div></a></td>";

        if ($is_admin) {
            echo '<td><a href="#" class="ctfscore-hint-view" data-id="' . $puzzle_id . '" data-title="' . $puzzle['title'] . '">'. $puzzle['hints'] .'</a></td>';
        }

        echo "</tr>\n";
    }
    ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($is_admin): ?>
<div class="modal fade" id="ctfscore-hint-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button class='btn btn-default' data-dismiss='modal'>閉じる</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// 問題本文
foreach ($puzzles as $puzzle) {
    $puzzle_id = $puzzle['puzzle_id'];

    echo "<div id='puzzle" . $puzzle_id . "' class='ctfscore-puzzle-modal modal fade' data-id='" . $puzzle_id . "'>";
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

    // ヒント
    if (!$puzzle['hinted'] && !$puzzle['answered']) {
        echo '<hr><a href="#" class="ctfscore-hint-request" data-id="' . $puzzle_id . '">この問題のヒントがほしいですか？</a>';
        echo '<div class="ctfscore-hint-container-' . $puzzle_id . '" style="display:none">';
        echo '<form class="form-inline ctfscore-hint" data-id="' . $puzzle_id . '">';
        echo '<div class="input-group"><input name="comment" class="form-control" placeholder="コメント">';
        echo '<span class="input-group-btn"><input type="submit" class="btn btn-primary" value="ヒントリクエスト"></span></div>';
        echo '</form></div>';
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

