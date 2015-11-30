<?php

class Controller_News extends Controller_Template
{

    public function before()
    {
        parent::before();

        // CTF開始前は許可しない
        Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();
    }


    public function action_list()
    {
        list($driver, $userid) = Auth::get_user_id();
        $news = Model_News::get_news();
        $already_id = Model_News::get_already_id($userid);

        if (count($news) > 0 && $news[0]['id'] > $already_id) {
            Model_News::update_already($userid, $news[0]['id']);
        }

        $data = array();
        $data['news'] = $news;
        $data['already_id'] = $already_id;
        $this->template->title = 'News';
        $this->template->content = View::forge('news/list', $data);
        $this->template->footer = '';
    }


}
