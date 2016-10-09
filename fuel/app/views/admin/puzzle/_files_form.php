<script>
 $(function(){
     $(document).on("click", ".del_<?php echo $item_name; ?>", function(){
         $(this).parent().parent().remove();
     });
 });
</script>

<div class="form-group">
  <div class="col-md-12">
    <label><?php echo $item_display; ?></label>
  </div>
  <div class="col-md-10">
    <span id="<?php echo $item_name; ?>">
      <?php foreach ($items as $item): ?>
        <span>
          <span class="col-md-11">
            <a href="<?php echo $item['url']; ?>" target="_blank">
              <input style="cursor:pointer;" type="text" class="form-control" id="<?php echo $item_name.$item['id']; ?>" name="<?php echo $item_name . '[' . $item['id']; ?>]" value="<?php echo $item[$item_val_name]; ?>" readonly></input>
            </a>
          </span>
          <span class="col-md-1">
            <a class="btn btn-primary del_<?php echo $item_name; ?>">削除</a>
          </span>
        </span>
      <?php endforeach; ?>
      <span>
        <span class="col-md-11">
          <input type="file" name="<?php echo $item_name; ?>_upload[]" accept="<?php echo isset($mymetype) ? $mimetype : ''; ?>" <?php echo isset($multiple) ? $multiple : ''; ?> />
        </span>
      </span>
    </span>
  </div>
</div>
