<?php

class Controller_Hint extends Controller_Rest
{
    /**
     * format 
     * 
     * @var string
     */
    protected $format = 'json';

    /**
     * before
     * 
     * @return void
     */
    public function before()
    {
        parent::before();
        // CTF開始前は許可しない
        Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();
    }

    /**
     * action_create
     * 
     * @param int $puzzle_id 
     * @return void
     */
    public function action_create($puzzle_id)
    {
        Controller_Auth::checkAllowedMethod('POST');
        Controller_Auth::checkCSRF();

        // 問題存在チェック
        if (!Model_Puzzle::get_puzzles($puzzle_id)) {
            $this->response(array('status' => false, 'message' => '指定IDは存在しません'));
            return;
        }

        list($driver, $userid) = Auth::get_user_id();

        // ヒントリクエスト重複チェック
        if (Model_Hint::get_hints($puzzle_id, $userid)) {
            $this->response(array('status' => false, 'message' => '既にリクエストしています'));
            return;
        }

        // ヒントリクエスト作成
        $val = Model_Hint::validate('create');
        if ($val->run()) {
            Model_Hint::create_hint(array(
                'puzzle_id' => $puzzle_id,
                'uid'       => $userid,
                'comment'   => $val->validated('comment'),
            ));

            $this->response(array('status' => true, 'message' => 'ヒントリクエスト完了しました'));
        } else {
            $this->response(array('status' => false, 'message' => $val->show_errors()));
        }
    }

    /**
     * action_view
     * 
     * @param int $puzzle_id 
     * @return void
     */
    public function action_view($puzzle_id)
    {
        if (!Controller_Auth::is_admin()) {
            Response::redirect('auth/invalid');
        }

        $puzzle = Model_Puzzle::get_puzzles($puzzle_id);

        // 問題存在チェック
        if (empty($puzzle) === true) {
            $this->response(array('message' => '指定IDは存在しません'));
            return;
        }

        $puzzle = $puzzle[0];
        $this->response(array('title' => $puzzle['title'], 'body' => Model_Hint::get_hints($puzzle_id)));
    }

    /**
     * action_token
     * 
     * @return void
     */
    public function action_token()
    {
        $this->response(array('token' => \Security::fetch_token()));
    }
}
