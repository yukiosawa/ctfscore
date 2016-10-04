<?php

class Controller_Chart extends Controller_Rest
{

    // 時系列ランキンググラフのデータをダウンロードする
    public function get_ranking()
    {
	$chart_data = Model_Score::get_ranking_chart();
	return $this->response($chart_data);
    }


    // ユーザプロファイル：カテゴリごとの進捗率
    public function post_progress()
    {
	// CTF開始前は許可しない
	Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $val = Model_Chart::validate('profile');
        if ($val->run()) {
            $usernames = $val->validated('usernames');
	    $chart_data = Model_Score::get_profile_progress($usernames);
	    return $this->response($chart_data);
        }
    }


    // ユーザプロファイル：詳細データ
    public function post_profile()
    {
	// CTF開始前は許可しない
	Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $val = Model_Chart::validate('profile');
        if ($val->run()) {
            $usernames = $val->validated('usernames');
            $profile_data = Model_Score::get_profile_detail($usernames);
            return $this->response($profile_data);
        }
    }


    // カテゴリ・点数別の回答済み数
    public function get_solvedStatus()
    {
	$chart_data = Model_Score::get_solved_status_chart();
	return $this->response($chart_data);
    }
}
