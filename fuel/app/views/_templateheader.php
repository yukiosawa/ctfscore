  <?php
  // 前処理
  $logined = false;
  $my_name = '';
  $is_admin = false;
  $ctf_time = false;
  $sound_on = Cookie::get('sound_on', '1') === '1';
  $is_sp = preg_match('/android|iphone/i', $_SERVER['HTTP_USER_AGENT']) === 1;
  // ログイン状態の情報
  if (Auth::check())
  {
      list($driver, $userid) = Auth::get_user_id();
      $logined = true;
      $my_name = Auth::get_screen_name();
      $is_admin = Controller_Auth::is_admin();
      $already_news = Model_News::get_already_count($userid);
  }
  else
  {
      $userid = 0;
      $logined = false;
      $my_name = '';
      $is_admin = false;
      $already_news = 0;
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
  // CTF終了時刻
  $ctf_end_time = Model_Score::get_ctf_end_time();
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
      .container-main {
          padding: 0 15px 15px 15px;
          background: rgba(255, 255, 255, 0.8);
          box-shadow: 0px 0px 9px 4px rgba(0, 0, 0, 0.2);
      }
      <?php if (!$is_sp): ?>
      .container-blur {
          position: relative;
      }
      .container-blur::before {
          content: "";
          position: absolute;
          background-clip: content-box;
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
          -webkit-filter: blur(6px);
          -moz-filter: blur(6px);
          filter: blur(6px);
          background-image: url(<?php echo $bg_image; ?>);
          background-size: cover;
          background-position: center center;
          background-attachment: fixed;
          background-repeat: no-repeat;
      }
      .container-main {
          position: relative;
          background: rgba(255, 255, 255, 0.4);
          -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99ffffff, endColorstr=#99ffffff)";
      }
      @media all and (-ms-high-contrast:none) {
          .container-main {
              background: rgba(255, 255, 255, 0.7);
          }
      }
      <?php endif; ?>
    </style>
  <?php endif; ?>

