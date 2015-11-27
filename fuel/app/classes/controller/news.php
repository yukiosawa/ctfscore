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

        if (count($news) > 0) {
            Model_News::update_already($userid, $news[0]['id']);
        }

        $data['news'] = $news;
        $this->template->title = 'News';
        $this->template->content = View::forge('news/list', $data);
        $this->template->footer = '';
    }


}
