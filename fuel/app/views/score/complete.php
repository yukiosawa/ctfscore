<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<?php
    if ($sound_on) {
        if ($complete_sound_url != '') {
            printf('<audio src="%s" autoplay loop></audio>', $complete_sound_url);
        }
    }
?>

<?php if ($complete_img_url != ''): ?>
  <div id="overlay" style="display: block; background-position: center center; background-image: url(<?php echo $complete_img_url; ?>);"></div>
<?php endif; ?>
