<?php $is_admin_url = Controller_Auth::is_admin_url(); ?>

<div class="row">
  <div class="col-md-12">
    <?php if ($is_admin_url): ?>
      <a href="/admin/news/create/" class="btn btn-primary">投稿する</a>
    <?php endif; ?>

    <table id="review-table" class="table table-hover tablesorter">
      <thead>
        <tr>
          <th class="fix-min">No</th>
          <th class="col-md-6">お知らせ</th>
          <th class="fix-md">投稿者</th>
          <th class="fix-md">更新日時</th>
          <?php if ($is_admin_url): ?>
            <th class="fix-min"></th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($news as $item): ?>
          <tr<?php if ($item['id'] > $already_id): ?> class="warning"<?php endif; ?>>
            <td><?php echo $item['id'] ?></td>
            <td><?php echo nl2br($item['comment']); ?></td>
            <td><?php echo $item['username']; ?></td>
            <td><?php echo $item['updated_at']; ?></td>
    	    <?php if($is_admin_url): ?>
              <td>
    	        <?php
    	        $edit_path = '/admin/news/edit/' . $item['id'];
    	        $del_path = '/admin/news/delete';
    	        ?>
    	        <a href="<?php echo $edit_path; ?>" class="btn btn-primary">編集</a>
    	        <?php echo render('news/_delete', array('action' => $del_path, 'id' => $item['id'])); ?>
    
              </td>
    	    <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
