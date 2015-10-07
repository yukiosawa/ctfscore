  <?php
  // 前処理
  $logined = false;
  $my_name = '';
  $is_admin = false;
  $ctf_time = false;
  // ログイン状態の情報
  if (Auth::check())
  {
      $logined = true;
      $my_name = Auth::get_screen_name();
      $is_admin = Controller_Auth::is_admin();
  }
  else
  {
      $logined = false;
      $my_name = '';
      $is_admin = false;
  }
  // CTF時間の設定状況
  $status = Model_Score::get_ctf_time_status();
  if ($status['no_use'])
  {
      $ctf_time = false;
  }
  else
  {
      $ctf_time = true;
  }
  // 背景画像
  $bg_image = Config::get('ctfscore.background.image');
  // 背景画像切替設定
  $bg_switch = Config::get('ctfscore.background.bg_switch.is_active');
  // ロゴ画像
  $logo_image = Config::get('ctfscore.logo_image');
  ?>

  <?php if ($bg_image): ?>
    <style type='text/css'>
     body {
       Background-image: url(<?php echo $bg_image; ?>);
       background-size: 100%, auto;
     }
    </style>
  <?php endif; ?>

  <?php if ($bg_switch): ?>
    <?php
    $bg_wait = Config::get('ctfscore.background.bg_switch.time_before_start');
    $bg_image_dir = Config::get('ctfscore.background.bg_switch.image_dir');
    $bg_interval = Config::get('ctfscore.background.bg_switch.interval');
    $image_paths = array();
    try {
	// dir直下のファイルすべて
	$files = File::read_dir(DOCROOT.$bg_image_dir, 1, array(
            '!^\.', // 隠しファイルは除く
            '!.*' => 'dir', // ディレクトリは除く
	));
	foreach ($files as $file) {
	    $image_paths[] = $bg_image_dir.'/'.$file;
	}
    }
    catch (InvalidPathException $e)
    {
	// 無視する
    }
    ?>
    <script>
     $(function(){
	 setTimeout('startBgswitch()', <?php echo $bg_wait; ?>);
     });

     function startBgswitch(){
	     $('body').bgswitcher({
		 images: <?php echo json_encode($image_paths, JSON_UNESCAPED_SLASHES); ?>,
		 interval: <?php echo $bg_interval; ?>,
		 shuffle: true,
	     });
     }
    </script>
  <?php endif; ?>
