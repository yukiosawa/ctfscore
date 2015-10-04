<?php
    if (!empty($errmsg)) {
	echo "<div class='alert alert-danger'>$errmsg</div>";
    }
?>

<!-- メッセージ -->
<?php if ($result == 'success'): ?>
  <p class='alert alert-success h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'failure'): ?>
  <p class='alert alert-danger h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'duplicate'): ?>
  <p class='alert alert-info h4'><?php echo nl2br($text); ?></p>
<?php endif; ?>

<!-- レベルアップメッセージ -->
<?php if (!empty($levels)): ?>
  <p class='alert alert-success h4'>
    <?php
    foreach ($levels as $level) {
	echo '<b>'.$level.'</b>にレベルアップしました！　';
    }
    ?>
  </p>
<?php endif; ?>

<div class="row">
  <div class="col-md-5">
    <?php
    // 初回回答者のボーナス画像
    if (!empty($first_bonus_img)) {
	echo "<p><image src='".$first_bonus_img."' class='img-responsive' /></p>\n";
    }
    // 問題ごとのカスタム画像
    foreach ($image_names as $image_name) {
	echo "<p><image src='/download/image?id=".$puzzle_id.
	     "&type=".$result."&file=".$image_name.
	     "' class='img-responsive' /></p>\n";
    }
    ?>
  </div>
</div>


