<div class="row">
  <div class="col-md-8">
    <table class="table table-hover table-condensed">
      <thead>
        <tr>
          <th>メッセージ</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($texts as $text): ?>
          <tr>
            <td><?php echo $text['text']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>
  <div class="col-md-1">
    <a href="<?php echo Uri::base(false).'admin/config/edittexts/'.$type; ?>" class="btn btn-sm btn-primary">編集</a>
  </div>
</div>
