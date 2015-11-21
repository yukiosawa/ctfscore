<?php

class Controller_Admin_Bonus extends Controller_Template
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


    public function action_list()
    {
        $data['bonus'] = Model_Bonus::get_bonus();
        $this->template->title = '特別ボーナス';
        $this->template->content = View::forge('bonus/list', $data);
        $this->template->footer = '';
    }


    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Bonus::validate('delete');

	$error_msg = '';
	$msg = '';
	$bonus = array();
	if ($val->run())
	{
	    $id = $val->validated('id');
	    $bonus_array = Model_Bonus::get_bonus($id);
	    if ($bonus_array)
	    {
		$bonus = $bonus_array[0];
	    }

	    if (Model_Bonus::delete_bonus($id) < 1)
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

	$data['bonus'] = $bonus;
	$data['errmsg'] = $error_msg;
	$data['msg'] = $msg;
	$this->template->title = 'Delete';
	$this->template->content = View::forge('bonus/delete', $data);
	$this->template->footer = View::forge('bonus/footer');
    }

    
    public function action_create()
    {
	$error_msg = '';
        $bonus = null;

	if (Input::method() == 'POST')
	{
            // CSRFチェック
            Controller_Auth::checkCSRF();

            $username = Input::post('username');
            $bonus_point = Input::post('bonus_point');
	    $comment = Input::post('comment');

            // 入力パラメータチェック
	    $val = Model_Bonus::validate('create');
	    if ($val->run())
	    {
                if ($uid = Model_Score::get_uid($username))
                {
                    $id = Model_Bonus::create_bonus($uid, $bonus_point, $comment);
                    $data['bonus'] = Model_Bonus::get_bonus($id)[0];
                    $data['msg'] = 'ポイント付与しました。';
                    $this->template->title = 'Create';
                    $this->template->content = View::forge('bonus/created', $data);
                    $this->template->footer = View::forge('bonus/footer');
                    return;
                }
                else
                {
                    $error_msg = '指定されたユーザは存在しません。';
                }
            }
	    else
	    {
		$error_msg = $val->show_errors();
	    }

	    // エラー画面表示用に保持
            $bonus['username'] = $username;
            $bonus['bonus_point'] = $bonus_point;
	    $bonus['comment'] = $comment;
	}

	$data['bonus'] = $bonus;
	$this->template->title = '特別ボーナス付与';
	$this->template->content = View::forge('bonus/create', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('bonus/footer');
    }
}



