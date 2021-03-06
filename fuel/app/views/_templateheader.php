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

// 静的ページ
$pages = Model_Staticpage::get();

// ロゴ画像
$logo_image = Model_Config::get_asset_images('logo_img')[0]['url'];

// 背景画像
$bg_images = Model_Config::get_asset_random_images('background_image')[0]['assets'];
$bg_image = empty($bg_images[array_rand($bg_images)]['url']) ? null : $bg_images[array_rand($bg_images)]['url'];

// 賞状機能背景
if ((Request::main()->controller === 'Controller_Score' && Request::main()->action === 'diploma') || (Request::main()->controller === 'Controller_Admin_Test' && Request::main()->action === 'diploma')) {
    $bg_image = Model_Config::get_asset_images('diploma_img')[0]['url'];
}
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

