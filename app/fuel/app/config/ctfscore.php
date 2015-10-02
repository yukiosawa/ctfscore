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
        ),
    ),
    'sound' => array(
	// trueの場合、問題正解時に音を鳴らす
	'is_active_on_success' => true,
	// 問題正解時に鳴らす音源ファイル[DOCROOTからの相対パス]
	'success_file' => '/audio/Doorbell-cheap-dingdong.ogg',
	// trueの場合、問題不正解時で音を鳴らす
	'is_active_on_failure' => true,
	// 問題不正解時にならす音源ファイル[DOCROOTからの相対パス]
	'failure_file' => '/audio/Buzzer.ogg',
	// trueの場合、レベルアップ時に音を鳴らす
	'is_active_on_levelup' => true,
	// レベルアップ時に鳴らす音源ファイル[DOCROOTからの相対パス]
	'levelup_file' => '/audio/Mobile_phone_ring.ogg',
	// trueの場合、その他通知時に音を鳴らす
	'is_active_on_notice' => true,
	// その他通知時に鳴らす音源ファイル[DOCROOTからの相対パス]
	'notice_file' => '/audio/D-Day_cricket.ogg',
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
        'plot_max_steps' => 10000,
    ),
    'history' => array(
	// 試行回数を制限する間隔(秒)
	'attempt_interval_seconds' => 60,
	// 試行回数の制限値(回)
	'attempt_limit_times' => 5,
    ),
    'review' => array(
	// 最大評価点
	'max_data_number' => 10,
	// 未回答の問題へのレビュー投稿を許可
	'allow_unanswered_puzzle' => false,
    ),
    'admin' => array(
	// 管理者ユーザのグループID
	'admin_group_id' => 100,
    ),
    'rule' => array(
	// 競技ルールを記載するファイル
	'rule_file' => DOCROOT.'../ctfadmin/rule/rule.html',
    ),
    'level' => array(
        // trueの場合、全体のレベルを有効にする
	'is_active_total_level' => true,
        // trueの場合、カテゴリごとのレベルを有効にする
	'is_active_category_level' => true,
        // 全体のダミーカテゴリ名（各カテゴリ名と重複しないこと)
	'dummy_name_total' => '__total__',
    ),
);

/* End of file ctfscore.php */
