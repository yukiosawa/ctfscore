<?php
/* (1) DBに登録する問題をこのファイルに記述する */
/* (2) 以下を実行する。
       php oil r ctfscore:insert_puzzles path_to_this_file */


$puzzles = array(
    // puzzle #1
    array(
	// 問題番号[必須]
        'puzzle_id' => 1,
	// ポイント[必須]
	'point' => 10,
	// ボーナスポイント[必須]
	'bonus_point' => 1,
	// カテゴリ[必須]
	'category' => 'カテゴリ1',
	// 問題タイトル[必須]
	'title' => '問題1',
	// 問題本文[任意]
	//'content' => '本文1',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p1.html', true),
	// flag[必須] (複数可)
	'flag' => array(
	    'flag1',
	),
	// 添付ファイル名[任意] (複数可)
	'attachment' => array(
	),
	// 正解時に表示する画像ファイル名[任意]
	'success_image' => '',
	// 正解時に表示するテキストメッセージ[任意]
	'success_text' => '',
    ),
    // puzzle #2
    array(
        'puzzle_id' => 2,
	'point' => 10,
	'bonus_point' => 1,
	'category' => 'カテゴリ1',
	'title' => '問題2',
	//'content' => '本文2',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p2.html', true),
	'flag' => array(
	    'flag2',
	),
	'attachment' => array(
	),
	'success_image' => '',
	'success_text' => '',
    ),
    // puzzle #3
    array(
        'puzzle_id' => 3,
	'point' => 10,
	'bonus_point' => 1,
	'category' => 'カテゴリ2',
	'title' => '問題3',
	//'content' => '本文3',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p3.html', true),
	'flag' => array(
	    'flag3',
	),
	'attachment' => array(
	),
	'success_image' => '',
	'success_text' => '',
    ),
    // puzzle #4
    array(
        'puzzle_id' => 4,
	'point' => 10,
	'bonus_point' => 1,
	'category' => 'カテゴリ2',
	'title' => '問題4',
	//'content' => '本文4',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p4.html', true),
	'flag' => array(
	    'flag4',
	),
	'attachment' => array(
	),
	'success_image' => '',
	'success_text' => '',
    ),
    // puzzle #5
    array(
        'puzzle_id' => 5,
	'point' => 10,
	'bonus_point' => 1,
	'category' => 'カテゴリ3',
	'title' => '問題5',
	//'content' => '本文5',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p5.html', true),
	'flag' => array(
	    'flag5',
	),
	'attachment' => array(
	),
	'success_image' => '',
	'success_text' => '',
    ),
    // puzzle #6
    array(
        'puzzle_id' => 6,
	'point' => 10,
	'bonus_point' => 1,
	'category' => 'カテゴリ3',
	'title' => '問題6',
	//'content' => '本文6',
	'content' => File::read(DOCROOT.'/ctfadmin/batch/p6.html', true),
	'flag' => array(
	    'flag6',
	),
	'attachment' => array(
	),
	'success_image' => '',
	'success_text' => '',
    ),
);

