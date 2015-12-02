<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>
<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<?php echo Asset::js('jquery.floatThead._.js'); ?>
<?php echo Asset::js('jquery.floatThead.js'); ?>

<script>
    $(function(){
        $('#review-table').tablesorter({
            headers: {
                // コメント列(3,7列目)とボタン列(6列目)はソート対象外とする
                2: {
                    sorter: false
                },
                5: {
                    sorter: false
                },
                6: {
                    sorter: false
                }
            }
        }).floatThead();
    });
</script>

<?php
    $is_admin_url = Controller_Auth::is_admin_url();
    $path = '/review/create/';

    if ($is_admin_url) {
        $path = '/admin' . $path;
    }

    if (isset($puzzle_id)) {
        $path = $path . $puzzle_id;
    }
?>

<div class="row">
  <div class="col-md-12">
    <div class="clearfix">
      <div class="pull-left"><a href="<?php echo $path; ?>" class="btn btn-primary">レビューする</a></div>

      <?php if(isset($select_categories) === true && isset($select_users) === true): ?>
        <form action="" method="get" class="form-inline pull-right">
          <div class="form-group">
            <select name="category" class="form-control">
              <option value="">全カテゴリ</option>
              <?php foreach ($select_categories as $value): ?>
                <option value="<?php echo $value; ?>" <?php echo ($value === $search_category) ? 'selected="selected"' : ''; ?>>
                  <?php echo $value; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <input type="search" name="user" list="select-users" placeholder="ユーザ" value="<?php echo $search_user; ?>" class="form-control">
            <datalist id="select-users">
              <?php foreach ($select_users as $value): ?>
                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
              <?php endforeach; ?>
            </datalist>
          </div>

          <input type="submit" value="検索" class="btn btn-primary">
        </form>
      <?php endif; ?>
    </div>
    <br>
    
    <table id="review-table" class="table table-hover tablesorter">
      <thead>
        <tr>
          <?php if($is_admin_url): ?>
            <th class="col-md-2">問題タイトル</th>
            <th class="col-md-2">評価</th>
            <th class="col-md-3">公開コメント</th>
            <th class="col-md-1">評価者</th>
            <th class="fix-md">更新日時</th>
            <th class="fix-min"></th>
            <th class="col-md-3">管理者へのメッセージ</th>
          <?php else: ?>
            <th class="col-md-2">問題タイトル</th>
            <th class="col-md-2">評価</th>
            <th class="col-md-5">公開コメント</th>
            <th class="col-md-1">評価者</th>
            <th class="fix-md">更新日時</th>
            <th class="fix-min"></th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php ob_start(); foreach ($reviews as $item): ?>
        <tr>
          <td><?php echo $item['puzzle_id'].': '.$item['puzzle_title']; ?></td>
          <td><div class="review" data-number="<?php echo \Config::get('ctfscore.review.max_data_number');?>" data-score="<?php echo $item['score']; ?>"><span style="display:none"><?php echo $item['score']; ?></span></div></td>
          <td><?php echo nl2br($item['comment']); ?></td>
          <td><?php echo $item['username']; ?></td>
          <td><?php echo $item['updated_at']; ?></td>
          <td>
            <?php if($my_name == $item['username'] || $is_admin_url): ?>
              <?php
              $edit_path = '/review/edit/' . $item['id'];
              $del_path = '/review/delete';
              if ($is_admin_url){
                  $edit_path = '/admin' . $edit_path;
                  $del_path = '/admin' . $del_path;
              }
              ?>
              <a href="<?php echo $edit_path; ?>" class="btn btn-primary">編集</a>
              <?php echo render('review/_delete', array('action' => $del_path, 'review_id' => $item['id'])); ?>
            <?php endif; ?>
          </td>
          <?php if($is_admin_url): ?>
            <td><?php echo nl2br($item['secret_comment']); ?></td>
          <?php endif; ?>
        </tr>
        <?php endforeach; $content = ob_get_contents(); ob_end_clean(); echo implode('', array_map('trim', explode("\n", $content))); ?>
      </tbody>
    </table>
  </div>
</div>
