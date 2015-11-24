<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::js('jquery.tablesorter.min.js'); ?>

<script>
    $(function(){
        var token = '';

        var active_id = '0';

        $('#puzzle-table').tablesorter();

        $('#ctfscore-puzzle-modal').on('hidden.bs.modal', function (e) {
            var self = $(this);
            $('.ctfscore-hint-container').hide()
            $('.ctfscore-hint-request').show();
            $('.ctfscore-hint-form').hide();
            $('.modal-header', self).text('');
            $('.modal-body', self).text('');
        });

        $('#ctfscore-puzzle-modal').on('show.bs.modal', function (e) {
            var self = $(this);
            active_id = $(e.relatedTarget).data('id');
            $.get('/score/puzzle_view/' + active_id, function (response) {
                $('.modal-header', self).text(response['title']);
                $('.modal-body', self).html(response['body']);

                if (response['is_hinted'] === false) {
                    $('.ctfscore-hint-container').show();
                }
            }, 'json');
        });


        $('.ctfscore-hint-request').click(function (e) {
            e.preventDefault();
            var self = $(this);
            $.get('/hint/token', function (response) {
                token = response['token'];
                $('.ctfscore-hint-form').show();
                self.hide();
            });
        });

        $('.ctfscore-hint').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var action = '/hint/create/' + active_id;
            $.post(action, self.serialize() + '&<?php echo \Config::get('security.csrf_token_key');?>=' + token, function (response) {
                response['status'] ? self.text(response['message']) : alert(response['message']);
            }, 'json');
        });

<?php if ($is_admin): ?>
        $('.ctfscore-hint-view').click(function (e) {
            e.preventDefault();
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
                var body = '<table class="table"><thead><tr><th>ユーザ</th><th>コメント</th></tr></thead><tbody>';
                $(response).each(function (index, values) {
                    body += '<tr><td>'
                         +  $('<div/>').text(values['username']).html()
                         +  '</td><td>'
                         +  $('<div/>').text(values['comment']).html()
                         +  '</td></tr>';
                });
                body += '</tbody></table>'
                $('.modal-body', self).html(body);
            }, 'json');
        });
<?php endif; ?>
   });
</script>

<?php if (!($my_name == '' || $my_name == 'guest')): ?>
<div class="row">
  <div>
    <div id="send-answer">
      <form action="/score/submit" method="post">
        <div class="clearfix">
          <div id="answertext-container" class="pull-left">
            <input id="answertext" name="answer" type="text" placeholder="flag を入力せよ"></input>
          </div>
          <div id="answersubmit-container" class="pull-left">
            <input type="image" src="<?php echo Asset::get_file('btn_submit_on.png', 'img'); ?>" alt="">
          </div>
         </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

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
        echo "<td><a href='#ctfscore-puzzle-modal' data-toggle='modal' data-id='" . $puzzle['puzzle_id'] . "'>" . $puzzle['title'] . "</a></td>";
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

<div class="modal fade" id="ctfscore-puzzle-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <div class="text-left ctfscore-hint-container">
                    <a href="#" class="ctfscore-hint-request">この問題のヒントがほしいですか？</a>
                    <div class="ctfscore-hint-form" style="display:none">
                        <form class="form-inline ctfscore-hint">
                            <div class="input-group">
                                <input name="comment" class="form-control" placeholder="コメント">
                                <span class="input-group-btn">
                                    <input type="submit" class="btn btn-primary" value="ヒントリクエスト">
                                </span>
                            </div>
                        </form>
                    </div>
                    <hr>
                </div>
                <button class='btn btn-default' data-dismiss='modal'>閉じる</button>
            </div>
        </div>
    </div>
</div>
