<?php

class Controller_Score extends Controller_Template
{

    public function before()
    {
        parent::before();
    }


    // CTF実施状況に応じてステータスページへリダイレクトする
    // 第1引数true: 開始前の場合リダイレクト
    // 第2引数true: 終了後の場合リダイレクト
    public static function checkCTFStatus($before = true, $ended = true)
    {
        $status = Model_Score::get_ctf_time_status();
        // 開始前のリダイレクト ($before=true指定)
        if ($before && $status['before'])
        {
            Response::redirect('score/status');
        }
        // 終了後のリダイレクト ($end=true指定)
        if ($ended && $status['ended'])
        {
            Response::redirect('score/status');
        }
    }


    // CTFの実施状況
    public function action_status()
    {
        $status = Model_Score::get_ctf_time_status();
        if ($status['before'])
        {
            $data['status'] = '開始前です';
        }
        else if ($status['running'])
        {
            $data['status'] = '実施中です';
        }
        else if ($status['ended'])
        {
            $data['status'] = '終了しました';
        }
        else if ($status['no_use'])
        {
            $data['status'] = '実施中です';
        }
        else
        {
            $data['status'] = '不明';
        }
        $data['start_time'] = $status['start_time'];
        $data['end_time'] = $status['end_time'];
        $this->template->title = "実施状況";
        $this->template->content = View::forge('score/status', $data);
        $this->template->footer = View::forge('score/footer');
    }


    // スコアボード
    public function action_view()
    {
        // 認証済みユーザのみ許可
        // Controller_Auth::redirectIfNotAuth();

        // 自分のユーザ名
        $data['my_name'] = Auth::get_screen_name();

        // カテゴリ一覧
        $ignore = array('練習');
        $data['categories'] = array_filter(
            Model_Puzzle::get_categories(),
            function ($var) use ($ignore) { return in_array($var, $ignore) === false; }
        );

        // 全ユーザの回答状況一覧
        $data['scoreboard'] = Model_Score::get_scoreboard();

        $this->template->title = "スコアボード";
        $this->template->content = View::forge('score/view', $data);
        $this->template->footer = '';
    }


    public function action_submit()
    {
        // CTF開始前と終了後は許可しない
        $this->checkCTFStatus(true, true);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();
        // 管理者は許可しない
        if (Controller_Auth::is_admin()) {
            Response::redirect('auth/invalid');
        }
        // POST以外は受け付けない
        Controller_Auth::checkAllowedMethod('POST');
        // 入力パラメータチェック
        //Controller_Auth::checkCSRF();
        $val = Model_Score::validate('score_submit');

        $data = array();
        $answer = '';
        $result = '';
        $puzzle_id = '';
        $error_msg = '';
        $image_names = array();
        $text = '';
        $levels = '';
        $is_first_winner = false;
        $first_bonus_img = '';

        $image_urls = array();


        // ユーザID
        list($driver, $userid) = Auth::get_user_id();
        $username = Auth::get_screen_name();

        $answer = Input::post('answer');

        // 回数制限のチェック
        if (Model_Score::is_over_attempt_limit($userid))
        {
            $result = 'error';
            $interval_seconds = Config::get('ctfscore.history.attempt_interval_seconds');
            $error_msg = '連続回数制限。'.$interval_seconds.'秒後にリトライしてください。';

            // 管理画面へ通知
            $mgmt_msg = $username.':'.$error_msg;
            Model_Score::emitToMgmtConsole('notice', $mgmt_msg);
        }
        elseif ($val->run())
        {
            // POSTされた回答が正解かチェック
            $puzzle_id = Model_Puzzle::get_puzzle_id($answer);
            if (!isset($puzzle_id))
            {
                // 不正解
                $result = 'failure';

                // 管理画面へ通知
                //		$mgmt_msg = '不正解です。';
                //		Model_Score::emitToMgmtConsole('failure', $mgmt_msg);

                // 表示するメッセージ(画像、テキスト)
                $msg = Model_Puzzle::get_failure_messages();
                // 取得できない場合はデフォルト値をセット
                $text = (!empty($msg['text'])) ? $msg['text'] : '不正解';
                if (!empty($msg['image_name']))
                {
                    $image_names[] = $msg['image_name'];
                }

                // 管理画面への通知メッセージ
                $mgmt_msg = $text;
            }
            else
            {
                // 回答済かどうかチェック
                if (Model_Puzzle::is_answered_puzzle($userid, $puzzle_id))
                {
                    // 既に正解済み
                    $result = 'duplicate';
                    $text = '既に回答済み';
                    $mgmt_msg = $username.':'.$text;
                }
                else
                {
                    // 正解
                    $result = 'success';

                    // 初回回答者チェック
                    $is_first_winner = Model_Score::is_first_winner($puzzle_id);

                    // 獲得ポイントを更新
                    Model_Puzzle::set_puzzle_gained($userid, $puzzle_id);

                    // 獲得レベルを更新
                    $levels = Model_Score::set_level_gained($userid);

                    // 表示するメッセージ(画像、テキスト)
                    $msg = Model_Puzzle::get_success_messages($puzzle_id);
                    // 取得できない場合はデフォルト値をセット
                    $text = (!empty($msg['text'])) ? $msg['text'] : '正解';
                    if (!empty($msg['image_name']))
                    {
                        $image_names[] = $msg['image_name'];
                    }

                    // 初回回答者は特別画像
                    if ($is_first_winner)
                    {
                        if (Config::get('ctfscore.puzzles.images.is_active_on_bonus'))
                        {
                            $first_bonus_img = Config::get('ctfscore.puzzles.images.first_bonus_img');
                        }
                    }

                    // 管理画面への通知メッセージ
                    if ($levels)
                    {
                        $result = 'levelup';
                        // レベルアップ
                        $level_string = '';
                        foreach ($levels as $level)
                        {
                            $level_string = $level_string.' '.$level.' ';
                        }
                        $mgmt_msg = $username.' は'.$level_string.'にレベルアップしました！';
                    }
                    else
                    {
                        // レベルそのまま
                        $mgmt_msg = $username.' は puzzle#'.$puzzle_id.' を解きました！';
                    }
                }
            }

            // 管理画面へ通知(正解、不正解)
            $image_urls = array();
            foreach ($image_names as $image_name)
            {
                $image_urls[] = '/download/image?id='.$puzzle_id.'&type='.$result.'&file='.$image_name;
            }
            $data = array('msg' => $mgmt_msg,
                'img_urls' => $image_urls,
                'is_first_winner' => $is_first_winner,
                'first_bonus_img' => $first_bonus_img,
            );
            //Model_Score::emitToMgmtConsole($result, $data);
        }
        else
        {
            $result = 'error';
            $error_msg = $val->show_errors();
        }

        // 試行履歴を記録する
        Model_Score::set_attempt_history($userid, $answer, $result);

        $data['result'] = $result;
        $data['puzzle_id'] = $puzzle_id;
        $data['image_names'] = $image_names;

        //	$data['image_urls'] = $image_urls;

        $data['text'] = $text;
        $data['levels'] = $levels;
        $data['is_first_winner'] = $is_first_winner;
        $data['first_bonus_img'] = $first_bonus_img;
        $data['sound_on'] = Cookie::get('sound_on', '1');

        $this->template->title = '回答結果';
        $this->template->content = View::forge('score/submit', $data);
        $this->template->content->set_safe('image_urls', $image_urls);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('score/footer');
    }


    public function action_puzzle()
    {
        // CTF開始前は許可しない
        $this->checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $data = array();
        $data['is_admin'] = Controller_Auth::is_admin();
        // 問題一覧
        // puzzleの内容はhtmlで書くのでエスケープせずにviewへ渡す
        //$data['puzzles'] = Model_Puzzle::get_puzzles_addinfo();
        $puzzles = Model_Puzzle::get_puzzles_addinfo();
        $this->template->title = '問題一覧';
        $this->template->content = View::forge('score/puzzle', $data);
        $this->template->content->set_safe('puzzles', $puzzles);
        $this->template->footer = '';
    }


    public function action_chart()
    {
        $this->template->title = 'スコアグラフ';
        $status = Model_Score::get_ctf_time_status();
        if ($status['no_use'])
        {
            // CTF時間設定なしの場合はグラフ描画しない
            $this->template->content = 'N/A';
            $this->template->footer = '';
        }
        else
        {
            $this->template->content = View::forge('score/chart');
            $this->template->footer = '';
        }
    }


    public function action_rule()
    {
        $data['file'] = Config::get('ctfscore.static_page.rule_file');
        $this->template->title = 'ルール';
        $this->template->content = View::forge('score/static_page', $data);
        $this->template->footer = '';
    }


    public function action_about()
    {
        $data['file'] = Config::get('ctfscore.static_page.about_file');
        $this->template->title = 'About';
        $this->template->content = View::forge('score/static_page', $data);
        $this->template->footer = '';
    }


    public function action_level()
    {
        $data['file'] = Config::get('ctfscore.static_page.level_file');
        $this->template->title = 'Level';
        $this->template->content = View::forge('score/static_page', $data);
        $this->template->footer = '';
    }


    public function action_profile($username)
    {
        // CTF開始前は許可しない
        $this->checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $data['profile'] = Model_Score::get_profile($username);
        $this->template->title = 'ユーザプロファイル';
        $this->template->content = View::forge('score/profile', $data);
        $this->template->footer = '';
    }
}
