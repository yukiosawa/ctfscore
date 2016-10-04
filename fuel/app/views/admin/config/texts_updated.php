<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <table class="table">
    <thead>
      <tr>
	<th>メッセージ</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($texts as $text): ?>
        <tr><td><?php echo $text['text']; ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>


