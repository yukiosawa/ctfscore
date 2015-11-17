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
	$data['news'] = Model_News::get_news();
	$this->template->title = 'News';
	$this->template->content = View::forge('news/list', $data);
	$this->template->footer = '';
    }


}
