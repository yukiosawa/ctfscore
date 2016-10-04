<?php

return array(
    'puzzles' => array(
        // 問題ファイルを格納する場所
        'path_to_puzzles' => DOCROOT.'../ctfadmin/puzzles/',
        // 添付ファイルを格納するサブディレクトリ名
        'attachment_dir' => 'attachments',
        // 問題解答時に表示する画像
        'images' => array(
            // trueの場合、正解時に画像表示する
            'is_active_on_success' => true,
            // 正解時に表示する画像ファイルを格納するサブディレクトリ名
            'success_image_dir' => 'images_on_success',
            'success_random_image_dir' => 'images_random_on_success',
            // trueの場合、不正解時に画像表示する
            'is_active_on_failure' => true,
            // 不正解時に表示する画像ファイルを格納するディレクトリ
            'failure_random_image_dir' => 'images_random_on_failure',
            // trueの場合、初回回答時にボーナス画像表示する
            'is_active_on_bonus' => 'true',
            // 初回回答者のボーナス画像(DOCROOTからの相対パス)
            'first_bonus_img' => '',
        ),
    ),
    'sound' => array(
        // trueの場合、問題正解時に音を鳴らす
        'is_active_on_success' => true,
        // 正解音を置くディレクトリ[DOCROOTからの相対パス]
        'success_dir' => '/audio/success',
        // 初回正解音を置くディレクトリ[DOCROOTからの相対パス]
        'first_winner_dir' => '/audio/first_winner',
        // trueの場合、問題不正解時で音を鳴らす
        'is_active_on_failure' => true,
        // 不正解音を置くディレクトリ[DOCROOTからの相対パス]
        'failure_dir' => '/audio/failure',
        // trueの場合、レベルアップ時に音を鳴らす
        'is_active_on_levelup' => true,
        // レベルアップ音を置くディレクトリ[DOCROOTからの相対パス]
        'levelup_dir' => '/audio/levelup',
        // trueの場合、その他通知時に音を鳴らす
        'is_active_on_notice' => true,
        // その他通知音を置くディレクトリ[DOCROOTからの相対パス]
        'notice_dir' => '/audio/notice',
    ),
    'chart' => array(
        // グラフ描画の対象とする最大人数(下にあるcolorsの数以下とすること)
        'max_number_of_users' => 10,
        // グラフ描画の色
        'colors' => array(
            'black',
            'maroon',
            'green',
            'navy',
            'gray',
            'red',
            'purple',
            'olive',
            'teal',
            'yellow',
            'coral',
            'springgreen',
            'orangered',
            'lawngreen',
            'pink',
            'skyblue',
            'brown',
            'khaki',
            'silver',
            'lime',
        ),
        // グラフをプロットする間隔(秒)
        // 1 hour = 3600 sec
        // 1 day  = 86400 sec
        //'plot_interval_seconds' => 3600 * 3,
        'plot_interval_seconds' => 86400,
        // グラフをプロットする最大数
        'plot_max_steps' => 100,
    ),
    'history' => array(
        // 試行回数を制限する間隔(秒)
        'attempt_interval_seconds' => 60,
        // 試行回数の制限値(回)
        'attempt_limit_times' => 5,
    ),
    'review' => array(
        // 最大評価点
        'max_data_number' => 5,
        // 未回答の問題へのレビュー投稿を許可
        'allow_unanswered_puzzle' => false,
    ),
    'admin' => array(
        // 管理者ユーザのグループID
        'admin_group_id' => 100,
        // 管理コンソールを有効にする
        'management_console' => true,
    ),
    'level' => array(
        // trueの場合、全体のレベルを有効にする
        'is_active_total_level' => true,
        // trueの場合、カテゴリごとのレベルを有効にする
        'is_active_category_level' => true,
        // 全体のダミーカテゴリ名（各カテゴリ名と重複しないこと)
        'dummy_name_total' => '__total__',
    ),
    'static_page' => array(
        // 競技ルールを記載するファイル
        'rule_file' => DOCROOT.'../ctfadmin/html/rule.html',
        // サイト説明を記載するファイル
        'about_file' => DOCROOT.'../ctfadmin/html/about.html',
        // 利用者の遵守事項を記載するファイル
        'agreement_file' => DOCROOT.'../ctfadmin/html/agreement.html',
        // レベル説明を記載するファイル
        'level_file' => DOCROOT.'../ctfadmin/html/level.html',
    ),
    // 背景画像を置くディレクトリ[DOCROOTからの相対パス]
    'background_image_dir' => '/assets/img/background',
    // ロゴ画像[DOCROOT/assets/img/配下]
    'logo_image' => '',
    // カウントダウンタイマー
    'countdown' => true,
    // 登録時音声[DOCROOTからの相対パス]
    'register_sound' => '',
    // 登録時画像[DOCROOTからの相対パス]
    'register_image' => '',
);

/* End of file ctfscore.php */
