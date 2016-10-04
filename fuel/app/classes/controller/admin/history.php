<?php

class Controller_Admin_History extends Controller_Template
{
    public function before()
    {
        parent::before();

        // 管理者グループのみ許可
        if (!Controller_Auth::is_admin())
        {
            Response::redirect('auth/invalid');
        }
    }


    public function action_list()
    {
        $data = array();

        $search_user = (string)Input::get('user');
        $search_puzzle_id = (string)Input::get('puzzle_id');
        $search_result_event = (string)Input::get('result_event');
        $data['search_user'] = $search_user;
        $data['search_puzzle_id'] = $search_puzzle_id;
        $data['search_result_event'] = $search_result_event;
        $data['select_users'] = Model_History::get_users();
        $data['select_puzzles'] = Model_Puzzle::get_puzzles();
        $data['select_results'] = Config::get('ctfscore.answer_result');
        $data['history'] = Model_History::get_history_for_search($search_user, $search_puzzle_id, $search_result_event);

        $this->template->title = 'サブミット履歴';
        $this->template->content = View::forge('admin/history/list', $data);
        $this->template->footer = '';
    }
}
