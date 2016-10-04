<?php

class Controller_Admin_Users extends Controller_Template
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
        $data['users'] = Model_Users::get_users();
        $this->template->title = 'ユーザ一覧';
        $this->template->content = View::forge('admin/users/list', $data);
        $this->template->footer = '';
    }


    public function action_create()
    {
	$error_msg = '';
        $user = '';

	if (Input::method() == 'POST')
	{
            // CSRFチェック
            Controller_Auth::checkCSRF();

            // 入力パラメータチェック
	    $val = Model_Users::validate('create');
	    if ($val->run())
	    {
                $username = $val->validated('username');
                $admin = $val->validated('admin') == '1' ? true : false;

                $result = Model_Users::create_user($username, null, $admin);
                $uid = $result['created_id'];
                if ($uid == false)
                {
                    $error_msg = 'ユーザ作成に失敗しました。'.$result['errmsg'];
                }
                else
                {
                    // ランダムなパスワードを割り当て
                    $result = Model_Users::reset_password($username);
                    if ($result['new_password'] == false)
                    {
                        $error_msg = 'ユーザ作成に失敗しました。'.$result['errmsg'];
                    }
                    else
                    {
                        $user = Model_Users::get_users($uid)[0];
                        $user['password'] = $result['new_password'];
                        $msg = '新規ユーザ作成しました。';
                    }
                }

                $data['user'] = $user;
                $data['msg'] = $msg;
                $data['errmsg'] = $error_msg;
                $this->template->title = 'Create';
                $this->template->content = View::forge('admin/users/updated', $data);
                $this->template->footer = View::forge('admin/users/footer');
                return;
            }
	    else
	    {
		$error_msg = $val->show_errors();
	    }
	}

	$this->template->title = '新規ユーザ作成';
	$this->template->content = View::forge('admin/users/create', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('admin/users/footer');
    }


    public function action_edit($uid)
    {
	$error_msg = '';

        $check = Model_Users::get_users($uid);
        if (count($check) != 1)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $user = $check[0];
	    if (Input::method() == 'POST')
	    {
                // CSRFチェック
                Controller_Auth::checkCSRF();
                // 入力パラメータチェック
	        $val = Model_Users::validate('edit');
	        if ($val->run())
	        {
                    $username = $val->validated('username');
                    $admin = $val->validated('admin') == '1' ? true : false;
                    // 一応チェック
                    if ($user['username'] != $username)
                    {
                        $error_msg = 'ユーザ更新に失敗しました。';
                    }
                    else
                    {

                        $result = Model_Users::update_user($username, $admin);
                        if ($result['bool'] == false)
                        {
                            $error_msg = 'ユーザ更新に失敗しました。'.$result['errmsg'];
                        }
                        else
                        {
                            $user = Model_Users::get_users($uid)[0];
                            $msg = 'ユーザ更新しました。';
                        }
                    }

                    $data['user'] = $user;
                    $data['msg'] = $msg;
                    $data['errmsg'] = $error_msg;
                    $this->template->title = 'Update';
                    $this->template->content = View::forge('admin/users/updated', $data);
                    $this->template->footer = View::forge('admin/users/footer');
                    return;
                }
	        else
	        {
		    $error_msg = $val->show_errors();
	        }
            }
        }

        $data['user'] = $user;
	$this->template->title = 'ユーザ情報編集';
	$this->template->content = View::forge('admin/users/edit', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('admin/users/footer');
    }

    
    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Users::validate('delete');

        $user = '';
	$error_msg = '';
	$msg = '';

	if ($val->run())
	{
	    $username = $val->validated('username');
            $user = Model_Users::get_users(null, $username)[0];

            $result = Model_Users::delete_user($username);
	    if ($result['bool'] == true)
	    {
		$msg = '削除しました。';
	    }
	    else
	    {
		$error_msg = '削除に失敗しました。'.$result['errmsg'];
	    }
	}
	else
	{
	    $error_msg = $val->show_errors();
	}

	$data['user'] = $user;
	$data['errmsg'] = $error_msg;
	$data['msg'] = $msg;
	$this->template->title = 'ユーザ削除';
	$this->template->content = View::forge('admin/users/updated', $data);
	$this->template->footer = View::forge('admin/users/footer');
    }


    public function post_pwreset()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Users::validate('pwreset');

        $user = '';
	$error_msg = '';
	$msg = '';

	if ($val->run())
	{
	    $username = $val->validated('username');
            $result = Model_Users::reset_password($username);
	    if ($result['new_password'] != '')
	    {
                $user = Model_Users::get_users(null, $username)[0];
                $user['password'] = $result['new_password'];
		$msg = 'パスワードをリセットしました。';
	    }
	    else
	    {
		$error_msg = 'パスワードリセットに失敗しました。'.$result['errmsg'];
	    }
	}
	else
	{
	    $error_msg = $val->show_errors();
	}

        $data['user'] = $user;
	$data['errmsg'] = $error_msg;
	$data['msg'] = $msg;
	$this->template->title = 'パスワードリセット';
	$this->template->content = View::forge('admin/users/updated', $data);
	$this->template->footer = View::forge('admin/users/footer');
    }

}

