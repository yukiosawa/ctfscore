<?php echo Asset::css('animate.css'); ?>
<?php echo Asset::js('jquery.lettering.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>
<?php echo Asset::js('ctfscore-overlay.js'); ?>

<script>
 function overlay(){
     var text = "<?php echo $text; ?>";
     var data = {
         msg: text,
         image_url: "<?php echo $image_url; ?>",
         first_bonus_img_url: "<?php echo $first_bonus_img_url; ?>"
     };
     showOverlay(data);
 }
</script>

<?php
    if (!empty($errmsg)) {
        echo "<div class='alert alert-danger'>$errmsg</div>";
    }

    if ($sound_on) {
        if ($sound_url != '') {
            printf('<audio src="%s" autoplay></audio>', $sound_url);
        }
    }
?>

<!-- メッセージ -->
<?php if ($result == Config::get('ctfscore.answer_result.success')): ?>
  <script>
   $(function(){
       overlay();
       /* レビューを書いてもらう */
       <?php if (Model_Config::get_value('is_active_force_review') != 0): ?>
       var sec = <?php echo Model_Config::get_value('review_force_wait_seconds'); ?> * 1000;
       setTimeout('goReview()', sec);
       <?php endif; ?>
   });

   function goReview(){
       location.href = '/review/create/<?php echo $puzzle_id; ?>';
   }
  </script>
  <button onclick='goReview();' class='btn btn-primary'>レビューを書く</button>
  <p class='alert alert-success h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == Config::get('ctfscore.answer_result.failure')): ?>
  <p class='alert alert-danger h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == Config::get('ctfscore.answer_result.duplicate')): ?>
  <p class='alert alert-info h4'><?php echo nl2br($text); ?></p>
<?php endif; ?>

<div class="row">
  <div class="col-md-5">
    <?php
    // 初回回答者のボーナス画像
    if ($is_first_winner && ($first_bonus_img_url != false)) {
        echo "<p><img src='".$first_bonus_img_url."' class='img-responsive' /></p>\n";
    }
    // 問題ごとのカスタム画像
    if (!empty($image_url)) {
        echo "<p><img src='".$image_url."' class='img-responsive' /></p>\n";
    }
    ?>
  </div>
</div>

<div id='overlay'></div>
<div id='overlay2'></div>
