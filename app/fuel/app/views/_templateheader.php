  <?php
  // 前処理
  $logined = false;
  $my_name = '';
  $is_admin = false;
  $ctf_time = false;
  $sound_on = Cookie::get('sound_on', '1') === '1';
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
  $bg_image_dir = Config::get('ctfscore.background_image_dir');
  $image_paths = array();
  $bg_image = '';
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
  if (count($image_paths) > 0) {
      $rand = rand() % count($image_paths);
      $bg_image = $image_paths[$rand];
  }

  // ロゴ画像
  $logo_image = Config::get('ctfscore.logo_image');
  ?>

  <?php if ($bg_image): ?>
    <style type='text/css'>
     body {
       background-image: url(<?php echo $bg_image; ?>);
       background-size: cover;
       background-position: center center;
       background-attachment: fixed;
       background-repeat: no-repeat;
     }
    </style>
  <?php endif; ?>

