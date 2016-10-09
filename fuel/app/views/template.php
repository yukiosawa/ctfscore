<?php function geturl ($path) {return Uri::base(false).$path;} ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="shortcut icon" href="<?php echo geturl('favicon.ico'); ?>" type="image/x-icon">
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('ctfscore.css'); ?>
    <?php echo Asset::css('flipclock.css'); ?>
    <?php echo Asset::js('jquery-2.2.4.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
  </head>

  <?php require(APPPATH.'views/_templateheader.php'); ?>

  <body>
    <div class="container">
      <div class="container-blur">
        <div class="container-main">
          <?php if ($logo_image): ?>
            <img src="<?php echo $logo_image; ?>" class="img-responsive" />
          <?php endif; ?>
          <nav class="navbar navbar-inverse">
            <ul class="nav navbar-nav">
              <li><a href="<?php echo geturl('score/view'); ?>">スコア</a></li>
              <li><a href="<?php echo geturl('score/puzzle'); ?>">問題</a></li>
              <li><a href="<?php echo geturl('review/list'); ?>">レビュー</a></li>
              <li class="dropdown update">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  チャート<span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <?php if ($ctf_time): ?>
                    <li><a href="<?php echo geturl('score/chart'); ?>">順位</a></li>
                  <?php endif; ?>
                  <li><a href="<?php echo geturl('score/solvedStatus'); ?>">正解者分布</a></li>
                </ul>
              </li>
              <?php if ($ctf_time): ?>
                <li><a href="<?php echo geturl('score/status'); ?>">カウントダウン</a></li>
              <?php endif; ?>
              <li><a href="<?php echo geturl('news/list'); ?>">お知らせ
                <?php if ($already_news): ?><span class="badge"><?php echo $already_news; ?></span><?php endif; ?>
              </a></li>
              <?php if ($status['ended']): ?><li><a href="<?php echo geturl('score/diploma/'.$my_name); ?>">賞状</a></li><?php endif; ?>

              <li class="dropdown update">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  その他<span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- ルール、About、その他説明 -->
                  <?php foreach ($pages as $page): ?>
                    <?php if ($page['is_active'] == 1): ?>
                      <li><a href="<?php echo geturl($page['path']); ?>"><?php echo $page['display_name']; ?></a></li>
                    <?php endif; ?>
                  <?php endforeach; ?>
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
                    <li><a href="<?php echo geturl('admin/mgmt/'); ?>" target="_blank">管理コンソール</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo geturl('admin/news/list'); ?>">お知らせ</a></li>
                    <li><a href="<?php echo geturl('admin/review/list'); ?>">レビュー</a></li>
                    <li><a href="<?php echo geturl('admin/bonus/list'); ?>">ボーナス点</a></li>
                    <li><a href="<?php echo geturl('admin/history/list'); ?>">サブミット履歴</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo geturl('admin/users/list'); ?>">ユーザ管理</a></li>
                    <li><a href="<?php echo geturl('admin/category/list'); ?>">問題カテゴリ</a></li>
                    <li><a href="<?php echo geturl('admin/puzzle/list'); ?>">問題登録・編集</a></li>
                    <li><a href="<?php echo geturl('admin/staticpage/list'); ?>">静的ページ編集</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo geturl('admin/config/list'); ?>">システム設定</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo geturl('admin/test/submit_complete'); ?>">全完画面の確認</a></li>
                    <li><a href="<?php echo geturl('admin/test/diploma'); ?>">賞状画面の確認</a></li>
                  </ul>
                </li>
              <?php endif; ?>
              <?php if ($logined): ?>
                <li class="dropdown update">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $my_name ?><span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo geturl('score/profile/'.$my_name); ?>">プロフィール</a></li>
                    <li><a href="<?php echo geturl('auth/update'); ?>">パスワード変更</a></li>
                    <li><a href="<?php echo geturl('auth/remove'); ?>">ユーザ削除</a></li>
                    <li><a href="<?php echo geturl('auth/logout'); ?>">ログアウト</a></li>
                  </ul>
                </li>
              <?php else: ?>
                <li><a href="<?php echo geturl('auth/login'); ?>">ログイン</a></li>
              <?php endif; ?>
              <li>
                <?php if ($sound_on): ?>
                    <a href="<?php echo geturl('auth/sound?on=0'); ?>" title="音声をOffにする"><?php echo Asset::img('ico_speaker.svg'); ?></a>
                <?php else: ?>
                    <a href="<?php echo geturl('auth/sound?on=1'); ?>" title="音声をOnにする"><?php echo Asset::img('ico_mute.svg'); ?></a>
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
