<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php if (isset($news)): ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('news/_view', array('news' => $news)); ?>
<?php endif; ?>



