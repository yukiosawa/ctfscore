<?php echo Asset::js('jquery.tablesorter.min.js'); ?>
<?php echo Asset::js('jquery.floatThead.min.js'); ?>

<script>
    $(function(){
        $('#review-table').tablesorter().floatThead({zIndex: 100});
    });
</script>

<div class="row">
  <div class="col-md-12">
    <div class="clearfix">

      <?php if(isset($select_users) === true && isset($select_puzzles) === true && isset($select_results)): ?>
        <form action="" method="get" class="form-inline pull-right">
          <div class="form-group">
            <select name="puzzle_id" class="form-control">
              <option value="">全ての問題</option>
              <?php foreach ($select_puzzles as $value): ?>
                <option value="<?php echo $value['puzzle_id']; ?>" <?php echo ($value['puzzle_id'] === $search_puzzle_id) ? 'selected="selected"' : ''; ?>>
                  <?php echo $value['puzzle_id'].': '.mb_substr($value['title'], 0, 20); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <select name="result_event" class="form-control">
              <option value="">全ての結果</option>
              <?php foreach ($select_results as $value): ?>
                <option value="<?php echo $value['event']; ?>" <?php echo ($value['event'] === $search_result_event) ? 'selected="selected"' : ''; ?>>
                  <?php echo $value['event'].': '.$value['description']; ?>
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
            <th class="col-md-2">日時</th>
            <th class="col-md-3">問題</th>
            <th class="col-md-3">サブミット</th>
            <th class="col-md-3">結果</th>
            <th class="col-md-1">ユーザ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($history as $item): ?>
        <tr>
          <td><?php echo $item['submitted_at']; ?></td>
          <td><?php echo $item['puzzle_id'].': '.mb_substr($item['puzzle_title'], 0, 20); ?></td>
          <td><?php echo $item['answer']; ?></td>
          <td><?php echo $item['result_event'].': '.$item['result_description']; ?></td>
          <td><?php echo $item['username']; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
