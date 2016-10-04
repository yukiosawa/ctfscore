<?php echo Asset::js('ctfscore-overlay.js'); ?>

<?php $img_url = Model_Config::get_asset_images('register_img')[0]['url']; ?>
<?php $btn_url = Model_Config::get_asset_images('register_btn_img')[0]['url']; ?>
<?php if ($img_url): ?>
  <script>
   $(function(){
       showOverlay3(
           "<?php echo $img_url; ?>",
           "<?php echo $btn_url ? $btn_url : ''; ?>"
       );
   });
  </script>
<?php endif; ?>

<p class="h4">ユーザ登録しました。</p>
<?php $sound_url = Model_Config::get_asset_sounds('register_sound')[0]['url']; ?>
<?php if ($sound_on && $sound_url): ?>
    <audio src="<?php echo $sound_url; ?>" autoplay></audio>
<?php endif; ?>

<div id='overlay3'></div>

