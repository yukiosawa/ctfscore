<ul class="nav nav-tabs">
  <li class="active"><a href="#images" data-toggle="tab">画像</a></li>
  <li><a href="#audio" data-toggle="tab">音</a></li>
  <li><a href="#texts" data-toggle="tab">テキスト</a></li>
  <li><a href="#levels" data-toggle="tab">レベル</a></li>
  <li><a href="#history" data-toggle="tab">サブミット</a></li>
  <li><a href="#review" data-toggle="tab">レビュー</a></li>
  <li><a href="#chart" data-toggle="tab">グラフ</a></li>
  <li><a href="#names" data-toggle="tab">名称</a></li>
  <li><a href="#time" data-toggle="tab">時刻</a></li>
  <li><a href="#switches" data-toggle="tab">ON/OFF</a></li>
  <li><a href="#system" data-toggle="tab">システム</a></li>
</ul>

<div class="tab-content">
  <!-- 画像 -->
  <div class="tab-pane active" id="images">
    <!-- ランダム画像 -->
    <?php foreach ($random_images as $random_image): ?>
      <h4><?php echo $random_image['description']; ?></h4>
      <!-- 最大5個まで表示 -->
      <?php if (count($random_image['assets']) > 5): ?>
        <span>・・・(省略)・・・　<a class="btn btn-sm btn-primary" href="<?php echo Uri::base(false).'admin/config/imageslist/'.$random_image['name'] ?>">すべて表示</a></span>
      <?php endif; ?>
      <?php echo render('admin/config/_images_list', array('images' => array_slice($random_image['assets'], 0, 5), 'name' => $random_image['name'])); ?>
    <?php endforeach; ?>

    <!-- 個々に指定する画像  -->
    <?php foreach ($images as $image): ?>
      <h4><?php echo $image['description']; ?></h4>
      <?php echo render('admin/config/_image_view', array('image' => $image, 'name' => $image['name'])); ?>
    <?php endforeach; ?>
  </div>

  <!-- 音 -->
  <div class="tab-pane" id="audio">
    <!-- ランダム音 -->
    <?php foreach ($random_sounds as $random_sound): ?>
      <h4><?php echo $random_sound['description']; ?></h4>
      <!-- 最大5個まで表示 -->
      <?php if (count($random_sound['assets']) > 5): ?>
        <span>・・・(省略)・・・　<a class="btn btn-sm btn-primary" href="<?php echo Uri::base(false).'admin/config/soundslist/'.$random_sound['name'] ?>">すべて表示</a></span>
      <?php endif; ?>
      <?php echo render('admin/config/_sounds_list', array('sounds' => array_slice($random_sound['assets'], 0, 5), 'name' => $random_sound['name'])); ?>
    <?php endforeach; ?>

    <!-- 個々に指定する音 -->
    <?php foreach ($sounds as $sound): ?>
      <h4><?php echo $sound['description']; ?></h4>
      <?php echo render('admin/config/_sound_view', array('sound' => $sound, 'name' => $sound['name'])); ?>
    <?php endforeach; ?>
  </div>

  <!-- テキスト -->
  <div class="tab-pane" id="texts">
    <h4>正解メッセージ(ランダムに表示)</h4>
    <?php
    echo render('admin/config/_texts_list', array('type' => 'success', 'texts' => $success_random_texts));
    ?>

    <h4>不正解メッセージ(ランダムに表示)</h4>
    <?php
    echo render('admin/config/_texts_list', array('type' => 'failure', 'texts' => $failure_random_texts));
    ?>
  </div>

  <!-- レベル -->
  <div class="tab-pane" id="levels">
    <?php $total_category_id = Model_Config::get_value('total_category_id'); ?>
    <h4>全体のレベル</h4>
    <?php echo render('admin/config/_levels_list', array('levels' => $total_levels, 'total_category_id' => $total_category_id)); ?>

    <h4>カテゴリごとのレベル</h4>
    <?php echo render('admin/config/_levels_list', array('levels' => $category_levels, 'total_category_id' => $total_category_id)); ?>
  </div>


  <!-- サブミット回数 -->
  <div class="tab-pane" id="history">
    <h4>フラグのサブミット</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_history));
    ?>
  </div>

  <!-- レビュー -->
  <div class="tab-pane" id="review">
    <h4>レビュー</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_review));
    ?>
  </div>

  <!-- グラフ描画 -->
  <div class="tab-pane" id="chart">
    <h4>グラフ描画</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_chart));
    echo render('admin/config/_chart_colors_list', array('config_chart_colors' => $config_chart_colors));
    ?>
  </div>

  <!-- CTF名称 -->
  <div class="tab-pane" id="names">
    <h4>CTFの名称</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_names));
    ?>
  </div>

  <!-- 時刻 -->
  <div class="tab-pane" id="time">
    <h4>時刻設定</h4>
    <?php
    echo render('admin/config/_time_list', array('start_time' => $start_time, 'end_time' => $end_time));
    ?>
  </div>

  <!-- 機能のON/OFF -->
  <div class="tab-pane" id="switches">
    <h4>機能のON/OFF　[0:無効, 1(0以外):有効]</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_switches));
    ?>
  </div>

  <!-- システム関連 -->
  <div class="tab-pane" id="system">
    <h4>システム関連</h4>
    <?php
    echo render('admin/config/_list', array('config' => $config_system, 'readonly' => true));
    ?>
  </div>

</div>
