<?php
/* (1) DBに登録するテキストをこのファイルに記述する */
/* (2) 以下を実行する。
       php oil r ctfscore:insert_levels path_to_this_file */


$total_dummy = Config::get('ctfscore.level.dummy_name_total');
$levels = array(
    // 全体のレベル。基準は解いた問題数。
    array('category' => $total_dummy,
	  'level' => 1,
	  'name' => 'レベル1',
	  'criteria' => 2,
    ),
    array('category' => $total_dummy,
	  'level' => 2,
	  'name' => 'レベル2',
	  'criteria' => 4,
    ),
    array('category' => $total_dummy,
	  'level' => 3,
	  'name' => 'レベル3',
	  'criteria' => 6,
    ),
    // カテゴリごとのレベル。基準は各カテゴリ内で解いた問題数。
    array('category' => 'カテゴリ1',
	  'level' => 1,
	  'name' => 'レベル1-1',
	  'criteria' => 2,
    ),
    array('category' => 'カテゴリ2',
	  'level' => 1,
	  'name' => 'レベル2-1',
	  'criteria' => 2,
    ),
);


