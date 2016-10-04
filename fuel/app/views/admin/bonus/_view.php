  <table class="table">
    <thead>
      <tr>
	<th>ユーザ名</th><th>ボーナス点</th><th>コメント</th><th>更新者</th><th>更新日時</th>
      </tr>
    </thead>

    <tbody>
      <tr>
	<td><?php echo $bonus['username']; ?></td>
	<td><?php echo $bonus['bonus_point']; ?></td>
	<td><?php echo nl2br($bonus['comment']); ?></td>
	<td><?php echo $bonus['updated_by']; ?></td>
	<td><?php echo $bonus['updated_at']; ?></td>
      </tr>
    </tbody>

  </table>

