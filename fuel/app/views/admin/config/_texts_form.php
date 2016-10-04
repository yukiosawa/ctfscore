<script>
 $(function(){
     var input_texts = $("#<?php echo $item_name; ?> [type=text]");
     $("#add_<?php echo $item_name; ?>").click(function(){
         var rand = Math.floor(Math.random() * 10000);
         var text = "<input type='text' class='form-control' id='<?php echo $item_name; ?>new" + rand + "' name='<?php echo $item_name; ?>[new" + rand +"]'></input>";
         var del = "<a class='btn btn-primary del_<?php echo $item_name; ?>'>削除</a>";
         var s1 = "<span class='col-md-11'>" + text + "</span>";
         var s2 = "<span class='col-md-1'>" + del + "</span>";
         $("#<?php echo $item_name; ?>").append("<span>" + s1 + s2 + "</span>");
     });
     $(document).on("click", ".del_<?php echo $item_name; ?>", function(){
         $(this).parent().parent().remove();
     });
 });
</script>

<div class="form-group">
  <div class="col-md-12">
    <label><?php echo $item_display; ?></label>
  </div>
  <div class="col-md-11">
    <span id="<?php echo $item_name; ?>">
      <?php if (empty($items)): ?>
        <span>
          <span class="col-md-11">
            <input type="text" class="form-control" id="<?php echo $item_name; ?>new" name="<?php echo $item_name; ?>[new]"></input>
          </span>
          <span class="col-md-1">
            <a class="btn btn-primary del_<?php echo $item_name; ?>">削除</a>
          </span>
        </span>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <span>
            <span class="col-md-11">
              <input type="text" class="form-control" id="<?php echo $item_name.$item['id']; ?>" name="<?php echo $item_name . '[' . $item['id']; ?>]" value="<?php echo $item[$item_val_name]; ?>"></input>
            </span>
            <span class="col-md-1">
              <a class="btn btn-primary del_<?php echo $item_name; ?>">削除</a>
            </span>
          </span>
        <?php endforeach; ?>
      <?php endif; ?>
    </span>
  </div>
  <div class="col-md-1">
    <a class="btn btn-primary" id="add_<?php echo $item_name; ?>">追加</a>
  </div>
</div>

