<?php

class Controller_Admin_News extends Controller_News
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


    public function action_edit($id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($id)) $id = null;
	$error_msg = '';
	$data = '';
	$news = array();

        if (!$news_array = Model_News::get_news($id))
        {
            $error_msg = '編集できません。';
        }
        else
        {
	    $news = $news_array[0];
	    if (Input::method() == 'POST')
	    {
		// 入力パラメータチェック
		Controller_Auth::checkCSRF();
		$val = Model_News::validate('edit');

		if ($val->run())
		{
		    $comment = $val->validated('comment');
		    list($driver, $userid) = Auth::get_user_id();
		    $result = Model_News::update_news($id, $comment, $userid);
		    if($result)
		    {
			// 成功画面へ転送
			$data['news'] = Model_News::get_news($id)[0];
			$data['msg'] = '更新しました。';
			$this->template->title = 'Updated';
			$this->template->content = View::forge('news/created', $data);
			$this->template->footer = View::forge('news/footer');
			return;
		    }
		    else
		    {
			$error_msg = '更新に失敗しました。';
		    }
		}
		else
		{
		    $error_msg = $val->show_errors();
		}
	    }
	}

	$data['news'] = $news;
	$this->template->title = 'お知らせ投稿';
	$this->template->content = View::forge('news/edit', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('news/footer');
    }


    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_News::validate('delete');

	$error_msg = '';
	$msg = '';
	$news = array();
	if ($val->run())
	{
	    $id = $val->validated('id');
	    $news_array = Model_News::get_news($id);
	    if ($news_array)
	    {
		$news = $news_array[0];
	    }

	    if (Model_News::delete_news($id) < 1)
	    {
		$error_msg = '削除に失敗しました。';
	    }
	    else
	    {
		$msg = '削除しました。';
	    }
	}
	else
	{
	    $error_msg = $val->show_errors();
	}

	$data['news'] = $news;
	$data['errmsg'] = $error_msg;
	$data['msg'] = $msg;
	$this->template->title = 'Delete';
	$this->template->content = View::forge('news/delete', $data);
	$this->template->footer = View::forge('news/footer');
    }

    
    public function action_create()
    {
	$error_msg = '';
	$news['comment'] = '';

	list($driver, $userid) = Auth::get_user_id();
	if (Input::method() == 'POST')
	{
            // CSRFチェック
            Controller_Auth::checkCSRF();

	    $comment = Input::post('comment');

            // 入力パラメータチェック
	    $val = Model_News::validate('create');
	    if ($val->run())
	    {
		$id = Model_News::create_news($comment, $userid);
		if ($id)
		{
		    $data['news'] = Model_News::get_news($id)[0];
		    $data['msg'] = '作成しました。';
		    $this->template->title = 'Create';
		    $this->template->content = View::forge('news/created', $data);
		    $this->template->footer = View::forge('news/footer');
		    return;
		}
		else
		{
		    $error_msg = '作成に失敗しました。';
		}
	    }
	    else
	    {
		$error_msg = $val->show_errors();
	    }

	    // エラー画面表示用に保持
	    $news['comment'] = $comment;
	}

	$data['news'] = $news;
	$this->template->title = 'お知らせ投稿';
	$this->template->content = View::forge('news/create', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('news/footer');
    }
}



