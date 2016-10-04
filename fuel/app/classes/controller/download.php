<?php

class Controller_Download extends Controller
{

    public function before()
    {
        parent::before();

        // CTF開始前は許可しない
        Controller_Score::checkCTFStatus(true, false);
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();
    }


    // 問題の添付ファイルをダウンロードする
    public function get_puzzle()
    {
	// idとfile名をgetで受け付ける
	$puzzle_id = basename(Input::get('id'));
	$file_name = basename(Input::get('file'));

	// idにひもづく問題
	$dir = Model_Puzzle::get_attachment_dir($puzzle_id);
	$path = $dir.'/'.$file_name;

	if (file_exists($path))
	{
	    // ダウンロード
	    File::download($path);
	}

	// 見つかりませんページへ
	Response::redirect('auth/404');
    }
}    
