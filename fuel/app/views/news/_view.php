<div class="row">
  <table class="table">
    <thead>
      <tr>
	<th>お知らせ</th><th>投稿者</th><th>更新日時</th>
      </tr>
    </thead>

    <tbody>
      <tr>
	<td><?php echo nl2br($news['comment']); ?></td>
	<td><?php echo $news['username']; ?></td>
	<td><?php echo $news['updated_at']; ?></td>
      </tr>
    </tbody>

  </table>
</div>

