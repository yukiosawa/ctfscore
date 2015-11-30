<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon-anime.ico" type="image/x-icon">
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('ctfscore.css'); ?>
    <?php echo Asset::css('flipclock.css'); ?>
    <?php echo Asset::js('jquery-2.1.1.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
  </head>

  <?php require('_templateheader.php'); ?>

  <body>
    <div class="container">
      <div class="container-blur">
        <div class="container-main">
          <?php echo Asset::img($logo_image, array('class' => 'img-responsive')); ?>
          <nav class="navbar navbar-inverse">
            <ul class="nav navbar-nav">
              <li><a href="/score/view">スコア</a></li>
              <li><a href="/score/puzzle">問題</a></li>
              <li><a href="/review/list">レビュー</a></li>
              <?php if ($ctf_time): ?>
                <li><a href="/score/chart">グラフ</a></li>
              <?php endif; ?>
              <li><a href="/score/status">実施状況</a></li>
              <li><a href="/news/list">お知らせ
                <?php if ($already_news): ?><span class="badge"><?php echo $already_news; ?></span><?php endif; ?>
              </a></li>

              <li class="dropdown update">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  制札<span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="/score/rule">規則・禁止事項</a></li>
                  <li><a href="/score/level">レベルの説明</a></li>
                  <li><a href="/score/about">場阿忍愚CTFについて</a></li>
                </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <?php if ($is_admin): ?>
                <li class="dropdown update">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    管理者<span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="/admin/review/list">レビュー管理</a></li>
                    <li><a href="/admin/news/list">お知らせ管理</a></li>
                    <li><a href="/admin/bonus/list">ボーナス点管理</a></li>
                    <li><a href="/admin/mgmt/" target="_blank">管理コンソール</a></li>
                  </ul>
                </li>
              <?php endif; ?>
              <?php if ($logined): ?>
                <li class="dropdown update">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $my_name ?><span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="/score/profile/<?php echo $my_name ?>">能力分析</a></li>
                    <li><a href="/auth/update">秘文変更</a></li>
                    <li><a href="/auth/remove">ユーザ削除</a></li>
                    <li><a href="/auth/logout">ログアウト</a></li>
                  </ul>
                </li>
              <?php else: ?>
                <li><a href="/auth/login">ログイン</a></li>
              <?php endif; ?>
              <li>
                <?php if ($sound_on): ?>
                    <a href="/auth/sound?on=0" title="音声をOffにする"><?php echo Asset::img('ico_speaker.svg'); ?></a>
                <?php else: ?>
                    <a href="/auth/sound?on=1" title="音声をOnにする"><?php echo Asset::img('ico_mute.svg'); ?></a>
                <?php endif; ?>
              </li>
            </ul>
          </nav>
          <?php echo $content; ?>
          <?php echo $footer; ?>
        </div>
      </div>
    </div>
  </body>
</html>
