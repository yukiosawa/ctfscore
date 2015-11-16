<?php echo Asset::css('animate.css'); ?>
<?php echo Asset::js('jquery.lettering-0.6.min.js'); ?>
<?php echo Asset::js('jquery.textillate.js'); ?>
<?php echo Asset::js('ctfscore-overlay.js'); ?>

<script>
    function overlay(){
        var img_urls = <?php echo json_encode($image_urls, JSON_UNESCAPED_SLASHES); ?>;
        if (img_urls) {
            var text = "<?php echo $text; ?>";
            var msg = $("#levelup-msg").text() ? $("#levelup-msg").text() : text;
            var data = {
                msg: msg,
                img_urls: img_urls,
                is_first_winner: "<?php echo $is_first_winner; ?>",
                first_bonus_img: "<?php echo $first_bonus_img; ?>"
            };
            showOverlay(data);
        }
    }
</script>

<?php
    if (!empty($errmsg)) {
        echo "<div class='alert alert-danger'>$errmsg</div>";
    }

    if ($sound_on) {
        $files = File::read_dir(DOCROOT . '/audio/' . $result, 1, array(
            '!^\.', // 隠しファイルは除く
            '!.*' => 'dir', // ディレクトリは除く
        ));

        if (count($files) > 0) {
            printf('<audio src="%saudio/%s/%s" autoplay></audio>', Uri::base(false), $result, $files[array_rand($files)]);
        }
    }
?>

<!-- メッセージ -->
<?php if ($result == 'success' || $result == 'levelup'): ?>
  <script>
      $(function(){
          overlay();
          /* レビューを書いてもらう */
          setTimeout('goReview()', 13000);
      });

      function goReview(){
          location.href = '/review/create/<?php echo $puzzle_id; ?>';
      }
  </script>

  <button onclick='goReview();' class='btn btn-primary'>レビューを書く</button>
  <p class='alert alert-success h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'failure'): ?>
  <p class='alert alert-danger h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'duplicate'): ?>
  <p class='alert alert-info h4'><?php echo nl2br($text); ?></p>
<?php endif; ?>

<!-- レベルアップメッセージ -->
<?php if ($result == 'levelup' && !empty($levels)): ?>
  <p class='alert alert-success h4' id='levelup-msg'>
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
        foreach ($image_urls as $image_url) {
            echo "<p><image src='".$image_url."' class='img-responsive' /></p>\n";
        }
    ?>
  </div>
</div>

<div id='overlay'></div>
<div id='overlay2'></div>
