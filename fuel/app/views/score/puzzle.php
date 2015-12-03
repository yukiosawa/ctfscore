<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<?php echo Asset::js('jquery.floatThead._.js'); ?>
<?php echo Asset::js('jquery.floatThead.js'); ?>

<script>
    $(function(){
        $('#puzzle-table').tablesorter().floatThead();

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
            $('.ctfscore-hint').data('id', $(e.relatedTarget).data('id'));
            $.get('/score/puzzle_view/' + $(e.relatedTarget).data('id'), function (response) {
                $('.modal-header', self).text(response['title']);
                $('.modal-body', self).html(response['body']);

                if (response['is_hinted'] === false) {
                    $('.ctfscore-hint-container').show();
                }
            }, 'json');
        });

        $('#ctfscore-puzzle-solvers-modal').on('hidden.bs.modal', function (e) {
            var self = $(this);
            $('.modal-header', self).text('');
            $('.modal-body', self).text('');
        });

        $('#ctfscore-puzzle-solvers-modal').on('show.bs.modal', function (e) {
            var self = $(this);
            $.get('/score/puzzle_solvers/' + $(e.relatedTarget).data('id'), function (response) {
                $('.modal-header', self).text('回答者 - ' + response['title']);
                $('.modal-body', self).html(response['body']);
            }, 'json');
        });

        $('.ctfscore-hint-request').click(function (e) {
            e.preventDefault();
            var self = $(this);
            $.get('/hint/token', function (response) {
                $('.ctfscore-hint-form').show();
                $('input[name="<?php echo \Config::get('security.csrf_token_key');?>"]', '.ctfscore-hint-form').val(response['token']);
                self.hide();
            });
        });

        $('.ctfscore-hint').submit(function (e) {
            e.preventDefault();
            var self = $(this);
            var action = '/hint/create/' + self.data('id');
            $.post(action, self.serialize(), function (response) {
                response['status'] ? self.text(response['message']) : alert(response['message']);
            }, 'json');
        });

<?php if ($is_admin): ?>
        $('#ctfscore-hint-modal').on('hidden.bs.modal', function (e) {
            var self = $(this);
            $('.modal-header', self).text('');
            $('.modal-body', self).text('');
        });

        $('#ctfscore-hint-modal').on('show.bs.modal', function (e) {
            var self = $(this);
            $.get('/hint/view/' + $(e.relatedTarget).data('id'), function (response) {
                $('.modal-header', self).text('ヒントリクエスト - ' + response['title']);
                var body = '<table class="table"><thead><tr><th>ユーザ</th><th>コメント</th></tr></thead><tbody>';
                $(response['body']).each(function (index, values) {
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

        $('<img>').attr('src', '<?php echo Asset::get_file('btn_submit_on.png', 'img')?>');
        $('#answersubmit').bind('touchstart mouseover', function () {
            $(this).fadeOut(0, function () {
                $(this).attr('src', '<?php echo Asset::get_file('btn_submit_on.png', 'img')?>').fadeIn(300);
            });
        }).bind('touchend mouseout', function () {
            $(this).fadeOut(300, function () {
                $(this).attr('src', '<?php echo Asset::get_file('btn_submit_off.png', 'img')?>').fadeIn(0);
            });
        });
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
            <input type="image" id="answersubmit" src="<?php echo Asset::get_file('btn_submit_off.png', 'img'); ?>">
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
      <th class="fix-min">ID</th>
      <th class="fix-lg">カテゴリ</th>
      <th>タイトル</th>
      <th class="fix-md">ポイント</th>
      <th class="fix-md">回答者数</th>
      <th class="fix-lg">レビュー</th>
      <?php 
          if ($is_admin) {
              echo '<th class="fix-md">ヒント</th>';
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
        echo "<td data-target='#ctfscore-puzzle-modal' data-toggle='modal' data-id='" . $puzzle['puzzle_id'] . "' class='anchor'>" . $puzzle['title'] . "</td>";
        // ポイント
        echo '<td class="suffix-point">' . $puzzle['point'] . "</td>";
        // 回答者数
        echo '<td data-target="#ctfscore-puzzle-solvers-modal" data-toggle="modal" data-id="' . $puzzle['puzzle_id'] . '" class="anchor suffix-human">' . $puzzle['gained'] . '</td>';
        // レビュー平均スコア
        echo "<td><a href='/review/list/".$puzzle_id."'><div class='review' data-number='".\Config::get('ctfscore.review.max_data_number')."' data-score='".$puzzle['avg_score']."'><span style='display:none'>".$puzzle['avg_score']."</span></div></a></td>";

        if ($is_admin) {
            echo '<td data-target="#ctfscore-hint-modal"  data-toggle="modal" data-id="' . $puzzle_id . '" class="anchor suffix-number">'. $puzzle['hints'] .'</td>';
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
                        <form class="form-inline ctfscore-hint" data-id="0">
                            <div class="input-group">
                                <input name="comment" class="form-control" placeholder="コメント">
                                <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="">
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

<div class="modal fade" id="ctfscore-puzzle-solvers-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
