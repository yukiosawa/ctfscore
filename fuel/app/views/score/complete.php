<?php
    if ($sound_on) {
        printf('<audio src="%s" autoplay></audio>', $complete_sound);
    }
?>
<div id="overlay" style="display: block; background-position: center center; background-image: url(<?php echo $complete_img; ?>);"></div>
