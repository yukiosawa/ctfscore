<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo Uri::current(); ?>" method="post" enctype="multipart/form-data">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <?php $readonly = $is_new ? '' : 'readonly'; ?>

  <div class="form-group">
    <div class="col-md-1">
      <label for="puzzle_id">ID<span style="color:red;"> *</span></label>
      <input type="text" <?php echo $readonly; ?> class="form-control" id="puzzle_id" name="puzzle_id" value="<?php echo isset($puzzle['puzzle_id']) ? $puzzle['puzzle_id'] : ''; ?>" placeholder="通番"></input>
    </div>

    <div class="col-md-3">
      <label for="category">カテゴリ<span style="color:red;"> *</span></label>
      <select class="form-control" id="category_id" name="category_id">
        <?php foreach ($categories as $category): ?>
          <?php $selected = isset($puzzle['category_id']) && $puzzle['category_id'] == $category['id'] ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $category['id']; ?>" <?php echo $selected; ?>>
            <?php echo $category['category']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-8">
      <label for="title">タイトル<span style="color:red;"> *</span></label>
      <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($puzzle['title']) ? $puzzle['title'] : ''; ?>" placeholder="問題タイトル"></input>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-2">
      <label for="point">ポイント<span style="color:red;"> *</span></label>
      <input type="text" class="form-control" id="point" name="point" value="<?php echo isset($puzzle['point']) ? $puzzle['point'] : ''; ?>" placeholder="正解点"></input>
    </div>
    <div class="col-md-2">
      <label for="bonus_point">ボーナスポイント<span style="color:red;"> *</span></label>
      <input type="text" class="form-control" id="bonus_point" name="bonus_point" value="<?php echo isset($puzzle['bonus_point']) ? $puzzle['bonus_point'] : ''; ?>" placeholder="初正解ボーナス点"></input>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-12">
      <label for="content">問題文</label>
      <textarea class="form-control" id="content" name="content" placeholder="問題の本文。画像も可、ただし公開エリアにおかれた画像へのリンクとなるので秘密情報を含まないよう注意すること。"><?php echo isset($puzzle['content']) ? $puzzle['content'] : ''; ?></textarea>
    </div>
  </div>
  <!-- Wysiwygエディタで編集 -->
  <?php echo Asset::js('trumbowyg/dist/trumbowyg.min.js'); ?>
  <?php echo Asset::js('trumbowyg/dist/langs/ja.min.js'); ?>
  <?php echo Asset::css('trumbowyg.min.css'); ?>
  <script>$("#content").trumbowyg({
      lang: 'ja'
   });</script>

  <span style="color:red;"> *</span>フラグひとつは必須
  <?php echo render('admin/puzzle/_items_form.php', array('items' => isset($flags) ? $flags : '', 'item_name' => 'flag', 'item_display' => 'フラグ', 'item_val_name' => 'flag')); ?>


  <?php echo render('admin/puzzle/_files_form.php', array('items' => isset($attaches) ? $attaches : array(), 'item_name' => 'attach', 'item_display' => '添付ファイル', 'item_val_name' => 'filename', 'multiple' => 'multiple')); ?>

  <?php echo render('admin/puzzle/_files_form.php', array('items' => isset($success_images) ? $success_images : array(), 'item_name' => 'success_image', 'item_display' => '正解時に表示する画像ファイル　[指定した場合、全体の共通設定より優先されます。複数だとランダム表示。]', 'item_val_name' => 'filename', 'multiple' => 'multiple', 'mimetype' => 'image/*')); ?>

  <?php echo render('admin/puzzle/_items_form.php', array('items' => isset($success_texts) ? $success_texts : '', 'item_name' => 'success_text', 'item_display' => '正解時に表示するテキストメッセージ　[指定した場合、全体の共通設定より優先されます。複数だとランダム表示。]', 'item_val_name' => 'text')); ?>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>


