<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg; ?></div>
<?php endif; ?>

<form class="form-horizontal" action="<?php echo Uri::current(); ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <?php $total_category_id = Model_Config::get_value('total_category_id'); ?>
  <h4>全体のレベル</h4>
  <?php echo render('admin/config/_levels_form', array('id' => 'total_levels', 'levels' => $total_levels, 'categories' => $categories, 'total_category_id' => $total_category_id)); ?>
  
  <h4>カテゴリごとのレベル</h4>
  <?php echo render('admin/config/_levels_form', array('id' => 'category_levels', 'levels' => $category_levels, 'categories' => $categories)); ?>

  <div>
    レベル設定に関する説明
    <ul>
      <li>「レベル」は1以上の整数値とし、値が小さいものから順にレベルアップしていきます。</li>
      <li>初期状態で全ユーザにレベル設定する場合は、「正解した問題数」を0としてください。</li>
      <li>「レベル」と「正解した問題数」の大小関係は一致させてください。<br>(例)「レベル」=1 「正解した問題数」=0、「レベル」=2 「正解した問題数」=2、「レベル」=3 「正解した問題数」=4、、、</li>
    </ul>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">更新する</button>
  </div>

</form>
