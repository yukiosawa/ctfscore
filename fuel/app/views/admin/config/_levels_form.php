<script>
 $(function(){
     $("#add_<?php echo $id; ?>").click(function(){
         <?php if ($total_category_id): ?>
             var cate = "<select class='form-control' name='levels[category_id][]'>";
             cate += "<option value='<?php echo $total_category_id; ?>'>N/A</option>";
             cate += "</select>";
         <?php else: ?>
             var cate = "<select class='form-control' name='levels[category_id][]'>";
             <?php foreach ($categories as $category): ?>
                 cate += "<option value='<?php echo $category['id']; ?>'>";
                 cate += "<?php echo $category['category']; ?>";
                 cate += "</option>";
             <?php endforeach; ?>
             cate += "</select>";
         <?php endif; ?>

         var level = "<input type='text' class='form-control' name='levels[level][]' value='<?php echo $level['level']; ?>'></input>";
         var name = "<input type='text' class='form-control' name='levels[name][]' value='<?php echo $level['name']; ?>'></input>";
         var criteria = "<input type='text' class='form-control' name='levels[criteria][]' value='<?php echo $level['criteria']; ?>'></input>";

         var del = "<a class='btn btn-primary del_<?php echo $id; ?>'>削除</a>";
         
         $("#<?php echo $id; ?> > tbody").append("<tr><td>" + cate + "</td><td>" + level + "</td><td>" + name + "</td><td>" + criteria + "</td><td>" + del + "</td></tr>");
     });
     
     $(document).on("click", ".del_<?php echo $id; ?>", function(){
         $(this).parent().parent().remove();
     });
 });
</script>

<div class="form-group">
  <div class="col-md-11">
    <table class="table table-hover" id="<?php echo $id; ?>">
      <thead>
        <tr>
          <th class="col-md-2">カテゴリ</th><th class="col-md-2">レベル</th><th class="col-md-2">レベル名称</th><th class="col-md-2">正解した問題数</th><th class="col-md-1"></th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($levels as $level): ?>
          <tr>
            <td>
              <?php if ($total_category_id): ?>
                <select class='form-control' name='levels[category_id][]'>
                  <option value='<?php echo $total_category_id; ?>'>N/A</option>
                </select>
              <?php else: ?>
                <select class='form-control' name='levels[category_id][]'>
                  <?php foreach ($categories as $category): ?>
                    <option value='<?php echo $category['id']; ?>'>
                      <?php echo $category['category']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              <?php endif; ?>
            </td>
            <td>
              <input type='text' class='form-control' name='levels[level][]' value='<?php echo $level['level']; ?>'></input>
            </td>
            <td>
              <input type='text' class='form-control' name='levels[name][]' value='<?php echo $level['name']; ?>'></input>
            </td>
            <td>
              <input type='text' class='form-control' name='levels[criteria][]' value='<?php echo $level['criteria']; ?>'></input>
            </td>
            <td>
              <a class='btn btn-primary del_<?php echo $id; ?>'>削除</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-1">
    <a class="btn btn-primary" id="add_<?php echo $id; ?>">追加</a>
  </div>
</div>

