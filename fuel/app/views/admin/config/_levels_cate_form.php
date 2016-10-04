<script>
 $(function(){
     $("#add_category_level").click(function(){


         var text = "<input type='text' class='form-control' id='<?php echo $item_name; ?>new" + rand + "' name='<?php echo $item_name; ?>[new" + rand +"]'></input>";
         var del = "<a class='btn btn-primary del_level'>削除</a>";
         var s1 = "<span class='col-md-11'>" + text + "</span>";
         var s2 = "<span class='col-md-1'>" + del + "</span>";
         $("#<?php echo $item_name; ?>").append("<span>" + s1 + s2 + "</span>");
     });
     $(document).on("click", ".del_category_level", function(){
         $(this).parent().parent().remove();
     });
 });
</script>

<div class="form-group">
  <div class="col-md-11">
    <table class="table table-hover" id="category_levels">
      <thead>
        <tr>
          <th class="col-md-2">カテゴリ</th><th class="col-md-2">レベル</th><th class="col-md-2">レベル名称</th><th class="col-md-2">正解した問題数</th><th class="col-md-1"></th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($levels as $level): ?>
            <tr>
              <input type="text" class="form-control" name="levels[category][]" value="<?php echo $level['category']; ?>"></input>
              <td>
                <input type="text" class="form-control" name="levels[level][]" value="<?php echo $level['level']; ?>"></input>
              </td>
              <td>
                <input type="text" class="form-control" name="levels[name][]" value="<?php echo $level['name']; ?>"></input>
              </td>
              <td>
                <input type="text" class="form-control" name="levels[criteria][]" value="<?php echo $level['criteria']; ?>"></input>
              </td>
              <td>
                <a class="btn btn-primary del_level">削除</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </span>
  </div>
  <div class="col-md-12">
    <a class="btn btn-primary" id="add_level">追加</a>
  </div>
</div>

