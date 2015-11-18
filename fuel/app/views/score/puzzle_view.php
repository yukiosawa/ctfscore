<?php
    // 本文, 添付ファイル
    echo $content;
    foreach ($attachment as $filename => $val) {
        // ダウンロードページへのリンク
        printf(
            '<p><a href="/download/puzzle?id=%s&file=%s">%s</a></p>',
            $puzzle_id,
            urlencode($val),
            $val
        );
    }
