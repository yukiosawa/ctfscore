<h4>十傑</h4>
<table class="table">
    <thead>
        <tr>
            <th style="width: 3em;">#</th>
            <th>名</th>
            <th>時刻</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach (array_slice($gained, 0, 10) as $key => $values): ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td><a href="/score/profile/<?php echo $values['username']; ?>" target="_blank"><?php echo $values['username']; ?></a></td>
            <td><?php echo $values['gained_at']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>   
</table>
<?php if (count($gained) > 10): ?>
    <h4>人傑</h4>
    <hr>
    <?php foreach (array_slice($gained, 10) as $values): ?>
        <span><a href="/score/profile/<?php echo $values['username']; ?>" target="_blank"><?php echo $values['username']; ?></a></span>
    <?php endforeach; ?>
<?php endif; ?>
