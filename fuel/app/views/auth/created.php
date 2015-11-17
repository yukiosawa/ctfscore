<?php echo Asset::js('ctfscore-overlay.js'); ?>

<?php if ($img_url = Config::get('ctfscore.register_image')): ?>
  <script>
      $(function(){
          showOverlay3("<?php echo $img_url; ?>");
      });
  </script>
<?php endif; ?>

<p class="h4">ユーザ登録しました。</p>
<?php if ($sound_on && Config::get('ctfscore.register_sound')): ?>
    <audio src="<?php echo Config::get('ctfscore.register_sound') ?>" autoplay></audio>
<?php endif; ?>

<div id='overlay3'></div>

