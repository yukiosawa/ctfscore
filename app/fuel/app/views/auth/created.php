<p class="h4">ユーザ登録しました。</p>
<?php if ($sound_on && Config::get('ctfscore.register_sound')): ?>
    <audio src="<?php echo Config::get('ctfscore.register_sound') ?>" autoplay></audio>
<?php endif; ?>
