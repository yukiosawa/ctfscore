<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
<?php endif; ?>

<h4><?php echo $description; ?></h4>

<?php
foreach ($filenames as $filename) {
    echo '<div>'.$filename.'</div>';
}
?>
