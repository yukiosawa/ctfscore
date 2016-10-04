<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php elseif(Asset::find_file('thanksforfeedback.png', 'img')): ?>
  <div class="text-center"><?php echo Asset::img('thanksforfeedback.png', array('style' => 'width: 400px;')); ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('review/_view', array('review' => $review)); ?>
<?php endif; ?>
