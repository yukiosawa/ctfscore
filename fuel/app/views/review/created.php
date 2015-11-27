<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class="text-center"><?php echo Asset::img('thanksforfeedback.png', array('style' => 'width: 400px;')); ?></div>
  <?php echo render('review/_view', array('review' => $review)); ?>
<?php endif; ?>
