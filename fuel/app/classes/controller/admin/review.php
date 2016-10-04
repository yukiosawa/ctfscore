<?php

class Controller_Admin_Review extends Controller_Review
{
    /* public $template = 'admin/template'; */

    public function before()
    {
        parent::before();

        // 管理者グループのみ許可
        if (!Controller_Auth::is_admin())
        {
            Response::redirect('auth/invalid');
        }
    }


    public function action_list($puzzle_id = null)
    {
        // 入力された文字列が数字のみで構成されるかチェック。
        if (!ctype_digit($puzzle_id)) $puzzle_id = null;

        $data = array();

        if ($puzzle_id !== null) {
            // 管理者へのメッセージも取得(第4引数 admin = true)
            $data['reviews'] = Model_Review::get_reviews(null, $puzzle_id, null, true);
            $data['puzzle_id'] = $puzzle_id;
        } else {
            $search_category = (string)Input::get('category');
            $search_user = (string)Input::get('user');
            $data['search_category'] = $search_category;
            $data['search_user'] = $search_user;
            $data['select_categories'] = array_map(function ($var) { return $var['category']; }, Model_Category::get_categories());
            $data['select_users'] = Model_Review::get_users();
            $data['reviews'] = Model_Review::get_reviews_for_search($search_category, $search_user);
        }

        $data['my_name'] = Auth::get_screen_name();
        $this->template->title = 'Reviews';
        $this->template->content = View::forge('review/list', $data);
        $this->template->footer = '';
    }
}



