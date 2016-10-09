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
        $this->template->footer = '';
    }


    // スコアボード
    public function action_view()
    {
        // 認証済みユーザのみ許可
        // Controller_Auth::redirectIfNotAuth();

        // 自分のユーザ名
        $data['my_name'] = Auth::get_screen_name();

        // カテゴリ一覧
        /* $ignore = array('練習');
           $data['categories'] = array_filter(
           Model_Puzzle::get_categories_with_point(),
           function ($var) use ($ignore) { return in_array($var['category'], $ignore) === false; }
           ); */
        $data['categories'] = Model_Puzzle::get_categories_with_point();

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
        $text = '';
        $image_url = '';
        $sound_url = '';
        $levels = '';
        $is_first_winner = false;
        $first_bonus_img_url = '';
        $is_complete = false;
        $complete_img_url = '';
        $complete_sound_url = '';

        // ユーザID
        list($driver, $userid) = Auth::get_user_id();
        $username = Auth::get_screen_name();

        $puzzle_id = Input::post('puzzle_id');
        $answer = Input::post('answer');

        // 回数制限のチェック
        if (Model_Score::is_over_attempt_limit($userid))
        {
            $result = Config::get('ctfscore.answer_result.over_limit');
            $interval_seconds = Model_Config::get_value('submit_interval_seconds');
            $error_msg = $result['description'].': '.$interval_seconds.'秒後にリトライしてください。';
            if (Model_Config::get_value('is_active_sound') != 0)
            {
                $sound_url = Model_Config::get_asset_sounds('notice_sound')[0]['url'];
            }

            // 管理画面へ通知
            $mgmt_msg = $username.':'.$error_msg;
            $mgmt_data = array(
               'msg' => $mgmt_msg,
               'image_url' => '',
               'sound_url' => $sound_url,
//               'is_first_winner' => false,
               'first_bonus_img_url' => '',
            );
            Model_Score::emitToMgmtConsole($result['event'], $mgmt_data);
        }
        elseif ($val->run())
        {
            // POSTされた回答が正解かチェック
//            $puzzle_id = Model_Puzzle::get_puzzle_id($answer);
//            if (!isset($puzzle_id))
            if (Model_Puzzle::is_right_answer($puzzle_id, $answer) != true)
            {
                // 不正解
                $result = Config::get('ctfscore.answer_result.failure');

                // 表示するメッセージ(画像、テキスト)
                $msg = Model_Puzzle::get_failure_message();
                // 取得できない場合はデフォルト値をセット
                $text = (!empty($msg['text'])) ? $msg['text'] : $result['description'];
                if (Model_Config::get_value('is_active_image') != 0)
                {
                    $image_url = $msg['image']['url'];
                }
                if (Model_Config::get_value('is_active_sound') != 0)
                {
                    $sound_url = $msg['sound']['url'];
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
                    $result = Config::get('ctfscore.answer_result.duplicate');
                    $text = $result['description'];
                    $mgmt_msg = $username.': '.$text.' #'.$puzzle_id;
                    if (Model_Config::get_value('is_active_sound') != 0)
                    {
                        $sound_url = Model_Config::get_asset_sounds('notice_sound')[0]['url'];
                    }
                }
                else
                {
                    // 正解
                    $result = Config::get('ctfscore.answer_result.success');

                    // 初回回答者チェック
                    $is_first_winner = Model_Score::is_first_winner($puzzle_id);

                    // 獲得ポイントを更新
                    Model_Puzzle::set_puzzle_gained($userid, $puzzle_id);

                    // 獲得レベルを更新
                    $levels = Model_Score::set_level_gained($userid);

                    // 表示するメッセージ(画像、テキスト)
                    $msg = Model_Puzzle::get_success_message($puzzle_id);
                    // 取得できない場合はデフォルト値をセット
                    $text = (!empty($msg['text'])) ? $msg['text'] : $result['description'];
                    $is_complete = Model_Score::is_complete($userid);
                    if (Model_Config::get_value('is_active_image') != 0)
                    {
                        $image_url = $msg['image']['url'];
                        // 初回回答者
                        if ($is_first_winner)
                        {
                            $first_bonus_img_url = Model_Config::get_asset_images('first_bonus_img')[0]['url'];
                        }
                        // 全問正解
                        if ($is_complete)
                        {
                            $complete_img_url = Model_Config::get_asset_images('complete_img')[0]['url'];
                        }
                    }
                    if (Model_Config::get_value('is_active_sound') != 0)
                    {
                        // 初回回答者
                        if ($is_first_winner)
                        {
                            $sound_url = Model_Config::get_asset_sounds('first_bonus_sound')[0]['url'];
                        }
                        else
                        {
                            $sound_url = $msg['sound']['url'];
                        }
                        // 全問正解
                        if ($is_complete)
                        {
                            $complete_sound_url = Model_Config::get_asset_sounds('complete_sound')[0]['url'];
                        }
                    }

                    // 管理画面への通知メッセージ
                    /* $puzzle = Model_Puzzle::get_puzzles($puzzle_id);
                       if (count($puzzle) > 0) {
                       $title = $puzzle[0]['title'];
                       }
                       else
                       {
                       $title = '----';
                       } */

                    $gained = Model_History::get_gained_history($userid, true)[0];
                    $title = $gained['puzzle_title'];

                    $mgmt_msg = $username.' は #'.$puzzle_id.':'.$title.' を解きました！ [ +'.$gained['point'].' +('.$gained['bonus_point'].') =>'.$gained['totalpoint'].']';
                    
                    // レベルアップ
                    if ($levels)
                    {
//                        $result = Config::get('ctfscore.answer_result.levelup');
                        $level_string = '';
                        foreach ($levels as $level)
                        {
                            $level_string = $level_string.' '.$level.' ';
                        }
                        $mgmt_msg .= $level_string.'にレベルアップしました！';
                        $text .= $level_string.'にレベルアップしました！';


                        // TODO: レベルアップの音


                        
                    }
                }
            }

            // 管理画面へ通知(正解、不正解)
            $mgmt_data = array(
                'msg' => $mgmt_msg,
                'image_url' => $image_url,
                'sound_url' => $sound_url,
//                'is_first_winner' => $is_first_winner,
                'first_bonus_img_url' => $first_bonus_img_url,
            );
            Model_Score::emitToMgmtConsole($result['event'], $mgmt_data);
        }
        else
        {
            $result = Config::get('ctfscore.answer_result.validation_error');
            $error_msg = $val->show_errors();
        }

        // 試行履歴を記録する
        Model_Score::set_attempt_history($userid, $puzzle_id, $answer, $result);

        $data['result'] = $result;
        $data['puzzle_id'] = $puzzle_id;
        $data['text'] = $text;
        $data['image_url'] = $image_url;
        $data['sound_url'] = $sound_url;
        $data['is_first_winner'] = $is_first_winner;
        $data['first_bonus_img_url'] = $first_bonus_img_url;
        $data['is_complete'] = $is_complete;
        $data['complete_img_url'] = $complete_img_url;
        $data['complete_sound_url'] = $complete_sound_url;
        $data['sound_on'] = Cookie::get('sound_on', '1');

        $this->template->title = '回答結果';
        $this->template->content = View::forge(($is_complete && $complete_img_url) ? 'score/complete' : 'score/submit', $data);
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
        $data['my_name'] = Auth::get_screen_name();
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

    public function action_puzzle_view($id)
    {
        // CTF開始前は許可しない
        $this->checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $puzzle = Model_Puzzle::get_puzzles($id);

        if (empty($puzzle) === true) {
            return new Response(json_encode(array('message' => 'この問題は存在しません。')));
        }

        $puzzle = $puzzle[0];
        list($driver, $userid) = Auth::get_user_id();
        $attachment = Model_Puzzle::get_attachment_names($id);
        $is_hinted = Model_Hint::is_hinted($id, $userid);
        $data = array('puzzle_id' => $id, 'content' => $puzzle['content'], 'attachment' => $attachment);
        $body = View::forge('score/puzzle_view')->set_safe($data)->__toString();

        return new Response(json_encode(array(
            'title'     => $puzzle['category'] . ' - ' . $puzzle['title'],
            'body'      => $body,
            'is_hinted' => $is_hinted
        )));
    }

    /**
     * action_puzzle_solvers
     * 
     * @return void
     */
    public function action_puzzle_solvers($id)
    {
        // CTF開始前は許可しない
        $this->checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $puzzle = Model_Puzzle::get_puzzles($id);

        if (empty($puzzle) === true) {
            return new Response('この問題は存在しません。');
        }

        $puzzle = $puzzle[0];
        $data = array();
        $data['gained'] = Model_Puzzle::get_puzzle_gained($id);
        return new Response(json_encode(array(
            'title' => $puzzle['title'],
            'body'  => View::forge('score/puzzle_solvers', $data)->__toString()
        )));
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


    public function action_solvedStatus()
    {
        $this->template->title = '正解者分布';
        $this->template->content = View::forge('score/solved_status');
        $this->template->footer = '';
    }


    public function action_rule()
    {
        $page = Model_Staticpage::get('rule')[0];
        if ($page['is_active'] != 1)
        {
            Response::redirect(Uri::base(false).'auth/404');
        }
        $this->template->title = $page['display_name'];
        $this->template->content = View::forge('score/static_page');
        $this->template->content->set_safe('page', $page);
        $this->template->footer = '';
    }


    public function action_about()
    {
        $page = Model_Staticpage::get('about')[0];
        if ($page['is_active'] != 1)
        {
            Response::redirect(Uri::base(false).'auth/404');
        }
        $this->template->title = $page['display_name'];
        $this->template->content = View::forge('score/static_page');
        $this->template->content->set_safe('page', $page);
        $this->template->footer = '';
    }


    public function action_misc()
    {
        $page = Model_Staticpage::get('misc')[0];
        if ($page['is_active'] != 1)
        {
            Response::redirect(Uri::base(false).'auth/404');
        }
        $this->template->title = $page['display_name'];
        $this->template->content = View::forge('score/static_page');
        $this->template->content->set_safe('page', $page);
        $this->template->footer = '';
    }


    public function action_profile($username = null)
    {
        // CTF開始前は許可しない
        $this->checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

        $usernames = array();
        if (Input::method() == 'POST')
        {
            $val = Model_Chart::validate('profile');
            if ($val->run()) {
                $usernames = $val->validated('usernames');
            }
        }
        else
        {
            $usernames[0] = $username;
        }
        // ユーザ名の存在チェック: 存在しない場合はログインユーザとする
        $usernames = array_filter($usernames, function ($var) {return Model_Score::get_uid($var); });
        $data['usernames'] = empty($usernames) ? array(Auth::get_screen_name()) : $usernames;
        
        $this->template->title = 'ユーザプロファイル';
        $this->template->content = View::forge('score/profile', $data);
        $this->template->footer = '';
    }


    public function action_diploma($username = null)
    {
        // CTF終了後判定
        $status = Model_Score::get_ctf_time_status();
        if ($status['ended'] === false) {
            Response::redirect('score/status');
        }

        $data['username'] = ($username === null) ? Auth::get_screen_name() : $username;
        $profile = Model_Score::get_profile_detail(array($data['username']))[0];
        if ($profile === null) {
            Response::redirect('score/view');
        }

        $complete_sound_url = '';
        if (Model_Config::get_value('is_active_sound') != 0)
        {
            $complete_sound_url = Model_Config::get_asset_sounds('complete_sound')[0]['url'];
        }

        $data['profile'] = $profile;
        $data['score'] = Model_Score::get_score_ranking($data['username']);
        $data['ctf_name'] = Model_Config::get_value('ctf_name');
        $data['complete_sound_url'] = $complete_sound_url;
        $data['sound_on'] = Cookie::get('sound_on', '1');
        $this->template->title = '賞状';
        $this->template->content = View::forge('score/diploma', $data);
        $this->template->footer = '';
    }
}
