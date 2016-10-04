<a href="#names" class="btn btn-primary">名称</a>
<a href="#images" class="btn btn-primary">画像</a>
<a href="#audio" class="btn btn-primary">音</a>
<a href="#texts" class="btn btn-primary">テキスト</a>
<a href="#levels" class="btn btn-primary">レベル</a>
<a href="#history" class="btn btn-primary">サブミット</a>
<a href="#review" class="btn btn-primary">レビュー</a>
<a href="#chart" class="btn btn-primary">グラフ</a>
<a href="#time" class="btn btn-primary">時刻</a>
<a href="#switches" class="btn btn-primary">ON/OFF</a>
<a href="#system" class="btn btn-primary">システム</a>

<div id="names" class="page-header"><h4>CTF名称　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_names));
?>
</p>

<div id="images" class="page-header"><h4>画像設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<!-- 個々に指定する画像  -->
<p>
  <?php foreach ($images as $image): ?>
    <h4><?php echo $image['description']; ?></h4>
    <?php echo render('admin/config/_image_view', array('image' => $image, 'name' => $image['name'])); ?>
  <?php endforeach; ?>
</p>

<!-- ランダム画像 -->
<?php foreach ($random_images as $random_image): ?>
  <h4><?php echo $random_image['description']; ?></h4>
  <!-- 最大5個まで表示 -->
  <?php if (count($random_image['assets']) > 5): ?>
    <span>・・・(省略)・・・　<a class="btn btn-sm btn-primary" href="<?php echo Uri::base(false).'admin/config/imageslist/'.$random_image['name'] ?>">すべて表示</a></span>
  <?php endif; ?>
  <?php echo render('admin/config/_images_list', array('images' => array_slice($random_image['assets'], 0, 5), 'name' => $random_image['name'])); ?>
<?php endforeach; ?>


<div id="audio" class="page-header"><h4>音設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
  <!-- 個々に指定する音 -->
  <?php foreach ($sounds as $sound): ?>
    <h4><?php echo $sound['description']; ?></h4>
    <?php echo render('admin/config/_sound_view', array('sound' => $sound, 'name' => $sound['name'])); ?>
  <?php endforeach; ?>

  <!-- ランダム音 -->
  <?php foreach ($random_sounds as $random_sound): ?>
    <h4><?php echo $random_sound['description']; ?></h4>
    <!-- 最大5個まで表示 -->
    <?php if (count($random_sound['assets']) > 5): ?>
      <span>・・・(省略)・・・　<a class="btn btn-sm btn-primary" href="<?php echo Uri::base(false).'admin/config/soundslist/'.$random_sound['name'] ?>">すべて表示</a></span>
    <?php endif; ?>
    <?php echo render('admin/config/_sounds_list', array('sounds' => array_slice($random_sound['assets'], 0, 5), 'name' => $random_sound['name'])); ?>
  <?php endforeach; ?>
</p>


<div id="texts" class="page-header"><h4>テキスト設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
  <h4>正解メッセージ(ランダムに表示)</h4>
  <?php
  echo render('admin/config/_texts_list', array('type' => 'success', 'texts' => $success_random_texts));
  ?>

  <h4>不正解メッセージ(ランダムに表示)</h4>
  <?php
  echo render('admin/config/_texts_list', array('type' => 'failure', 'texts' => $failure_random_texts));
  ?>
</p>


<div id="levels" class="page-header"><h4>レベル設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
  <?php $total_category_id = Model_Config::get_value('total_category_id'); ?>
  <h4>全体のレベル</h4>
  <?php echo render('admin/config/_levels_list', array('levels' => $total_levels, 'total_category_id' => $total_category_id)); ?>
  
  <h4>カテゴリごとのレベル</h4>
  <?php echo render('admin/config/_levels_list', array('levels' => $category_levels)); ?>
</p>


<div id="history" class="page-header"><h4>サブミット回数設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_history));
?>
</p>

<div id="review" class="page-header"><h4>レビュー設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_review));
?>
</p>

<div id="chart" class="page-header"><h4>グラフ描画設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_chart));
echo render('admin/config/_chart_colors_list', array('config_chart_colors' => $config_chart_colors));
?>
</p>


<div id="time" class="page-header"><h4>時刻設定　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_time_list', array('start_time' => $start_time, 'end_time' => $end_time));
?>
</p>

<div id="switches" class="page-header"><h4>機能のON/OFF　[0:無効, 1(0以外):有効]　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_switches));
?>
</p>




<div id="system" class="page-header"><h4>システム関連　　　<small><a href="#">ページ上部へ戻る</a></small></h4></div>
<p>
<?php
echo render('admin/config/_list', array('config' => $config_system, 'readonly' => true));
?>
</p>

<div class="page-header"><a href="#">ページ上部へ戻る</a></div>


