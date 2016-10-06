<?php

return array(
    // 回答結果の種類
    'answer_result' => array(
        'success' => array(
            'event' => 'success',
            'description' => '正解'
        ),
        'failure' => array(
            'event' => 'failure',
            'description' => '不正解'
        ),
        'duplicate' => array(
            'event' => 'duplicate',
            'description' => '既に回答済み',
        ),
        /* 'levelup' => array(
           'event' => 'levelup',
           'description' => 'レベルアップ',
           ), */
        'over_limit' => array(
            'event' => 'over_limit',
            'description' => '回数制限オーバー',
        ),
        'validation_error' => array(
            'event' => 'validation_error',
            'description' => 'バリデーションエラー',
        ),
    ),
    
    /**
     * 各種設定の初期値を定義。
     * これらの設定は初回起動時にDBに格納されて管理画面から変更可能。
     **/
    'default_values' => array(
        // CTFの名称
        array(
           'type' => 'names',
           'name' => 'ctf_name',
           'value' => '',
           'description' => 'CTFの名称。賞状に表示します。',
        ),
        // 機能のON/OFFに関するもの
        array(
           'type' => 'switches',
           'name' => 'is_active_image',
           'value' => 1,
           'description' => '問題回答時に画像表示する　[0:無効, 1(0以外):有効]',
        ),
        array(
           'type' => 'switches',
           'name' => 'is_active_sound',
           'value' => 1,
           'description' => '問題回答時に音を鳴らす　[0:無効, 1(0以外):有効]',
        ),
        array(
           'type' => 'switches',
           'name' => 'is_active_countdown',
           'value' => 1,
           'description' => 'カウントダウンタイマーを表示する　[0:無効, 1(0以外):有効]',
        ),
        array(
           'type' => 'switches',
           'name' => 'is_active_level',
           'value' => 1,
           'description' => '問題正解数に応じたレベル表示　[0:無効, 1(0以外):有効]',
        ),
        array(
           'type' => 'switches',
           'name' => 'is_active_management_console',
           'value' => 1,
           'description' => '管理コンソールへの通知　[0:無効, 1(0以外):有効]',
        ),
        array(
            'type' => 'switches',
            'name' => 'is_active_management_diag_msg',
            'value' => 1,
            'description' => '管理コンソール間の診断メッセージ通知　[0:無効, 1(0以外):有効]',
        ),
        array(
           'type' => 'switches',
           'name' => 'is_active_force_review',
           'value' => 1,
           'description' => '問題正解時にレビュー画面へ遷移させる　[0:無効, 1(0以外):有効]',
        ),

        // グラフ描画に関する設定
        array(
           'type' => 'chart',
           'name' => 'plot_interval_seconds',
           'value' => '900',
           'description' => 'ランキングをプロットする間隔[秒]　=> 900=15min, 3600=1h, 86400=24h',
        ),
        array(
           'type' => 'chart',
           'name' => 'plot_max_steps',
           'value' => '100',
           'description' => 'ランキングをプロットする最大数(時間軸)',
        ),
        array(
           'type' => 'chart',
           'name' => 'bubble_size_multiple_by',
           'value' => '2',
           'description' => 'バブルチャート（正解者数)の大きさ倍率',
        ),
        array(
           'type' => 'chart',
           'name' => 'bubble_color',
           'value' => '#FF6384',
           'description' => 'バブルチャート（正解者数)の色',
        ),

        // サブミット履歴に関する設定
        array(
              'type' => 'history',
              'name' => 'submit_interval_seconds',
              'value' => 60,
              'description' => '試行回数を制限する間隔[秒]',
        ),
        array(
              'type' => 'history',
              'name' => 'submit_limit_times',
              'value' => 10,
              'description' => '試行回数の制限値[回]',
        ),

        // レビューに関する設定
        array(
              'type' => 'review',
              'name' => 'max_review_score',
              'value' => 5,
              'description' => '最大評価点',
        ),
        array(
              'type' => 'review',
              'name' => 'allow_unanswered_review',
              'value' => 0,
              'description' => '未回答の問題へのレビュー投稿を許可 [0:不許可, 1(0以外):許可]',
        ),
        array(
              'type' => 'review',
              'name' => 'review_force_wait_seconds',
              'value' => 13,
              'description' => '問題正解時にレビュー画面へ遷移させるまでの時間[秒]',
        ),

        // システム関連
        array(
            'type' => 'system',
            'name' => 'attachment_dir',
            'value' => '../ctfadmin/attachments',
            'description' => '問題の添付ファイルを格納するディレクトリ [DOCROOTからの相対パス]',
        ),
        array(
            'type' => 'system',
            'name' => 'success_image_dir',
            'value' => 'success',
            'description' => '問題正解時に表示する画像を格納するディレクトリ [assets/img/からの相対パス]',
        ),
        array(
            'type' => 'system',
            'name' => 'total_category_id',
            'value' => 1,
            'description' => 'カテゴリ全体を示すカテゴリID',
        ),
        array(
            'type' => 'system',
            'name' => 'admin_group_id',
            'value' => 100,
            'description' => '管理者ユーザのグループID',
        ),
    ),

    // assets
    'default_assets' => array(
        // 画像
        array(
            'name' => 'first_bonus_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '初正解のボーナス画像',
        ),
        array(
            'name' => 'complete_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '全完時の画像',
        ),
        array(
            'name' => 'diploma_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '賞状の背景画像',
        ),
        array(
            'name' => 'logo_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => 'ロゴ画像',
        ),
        array(
            'name' => 'register_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '登録時画像',
        ),
        array(
            'name' => 'register_btn_img',
            'type' => 'img',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '登録時ボタン画像',
        ),
        array(
            'name' => 'success_random_image',
            'type' => 'img',
            'is_random' => 1,
            'sub_dir' => 'usr/success_random',
            'filename' => '',
            'description' => '正解時に表示する画像(ランダムに表示)',
        ),
        array(
            'name' => 'failure_random_image',
            'type' => 'img',
            'is_random' => 1,
            'sub_dir' => 'usr/failure_random',
            'filename' => '',
            'description' => '不正解時に表示する画像ファイル(ランダムに表示)',
        ),
        array(
            'name' => 'background_image',
            'type' => 'img',
            'is_random' => 1,
            'sub_dir' => 'usr/background_random',
            'filename' => '',
            'description' => '背景画像',
        ),
        // 音
        array(
            'name' => 'success_random_sound',
            'type' => 'audio',
            'is_random' => 1,
            'sub_dir' => 'usr/success_random',
            'filename' => '',
            'description' => '正解音(ランダムに再生)',
        ),
        array(
            'name' => 'failure_random_sound',
            'type' => 'audio',
            'is_random' => 1,
            'sub_dir' => 'usr/failure_random',
            'filename' => '',
            'description' => '不正解音(ランダムに再生)',
        ),
        array(
            'name' => 'levelup_sound',
            'type' => 'audio',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => 'レベルアップ音',
        ),
        array(
            'name' => 'notice_sound',
            'type' => 'audio',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => 'その他通知音',
        ),
        array(
            'name' => 'first_bonus_sound',
            'type' => 'audio',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '初正解のボーナス音',
        ),
        array(
            'name' => 'complete_sound',
            'type' => 'audio',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => '全完時の音',
        ),
        array(
            'name' => 'register_sound',
            'type' => 'audio',
            'is_random' => 0,
            'sub_dir' => 'usr',
            'filename' => '',
            'description' => 'ユーザ登録時の音',
        ),
    ),

    // グラフ色
    'default_chart_colors' => array(
        array('rank' => 1, 'color' => '#FF0000'), // red
        array('rank' => 2, 'color' => '#008000'), // green
        array('rank' => 3, 'color' => '#000080'), // navy
        array('rank' => 4, 'color' => '#808080'), // gray
        array('rank' => 5, 'color' => '#800000'), // maroon
        array('rank' => 6, 'color' => '#800080'), // purple
        array('rank' => 7, 'color' => '#808000'), // olive
        array('rank' => 8, 'color' => '#008080'), // teal
        array('rank' => 9, 'color' => '#FFFF00'), // yellow
        array('rank' => 10, 'color' => '#FF7F50'), // coral
        array('rank' => 11, 'color' => '#00FF7F'), // springgreen
        array('rank' => 12, 'color' => '#FF4500'), // orangered
        array('rank' => 13, 'color' => '#7CFC00'), // lawngreen
        array('rank' => 14, 'color' => '#FFC0CB'), // pink
        array('rank' => 15, 'color' => '#87CEEB'), // skyblue
        array('rank' => 16, 'color' => '#A52A2A'), // brown
        array('rank' => 17, 'color' => '#F0E68C'), // khaki
        array('rank' => 18, 'color' => '#C0C0C0'), // silver
        array('rank' => 19, 'color' => '#00FF00'), // lime
        array('rank' => 20, 'color' => '#000000'), // black
    ),

    // 静的ページ
    'default_static_pages' => array(
        array(
            'name' => 'about',
            'display_name' => 'このサイトについて',
            'path' => 'score/about',
            'content' => '<h4>このサイトについて</h4>サイトに関する説明を記載します。',
            'display_order' => 1,
            'is_active' => 1,
        ),
        array(
            'name' => 'rule',
            'display_name' => 'ルール',
            'path' => 'score/rule',
            'content' => '<h4>競技ルール</h4>競技ルールはここに記載します。',
            'display_order' => 2,
            'is_active' => 1,
        ),
        array(
            'name' => 'misc',
            'display_name' => 'その他説明',
            'path' => 'score/misc',
            'content' => '<h4>レベルの説明など他に説明したいことがあれば</h4><p>正解した問題数に応じてレベル獲得します。</p>',
            'display_order' => 3,
            'is_active' => 1,
        ),
    ),
);

