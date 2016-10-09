<?php

class Controller_Auth extends Controller_Template
{

    public function before()
    {
        parent::before();
    }


    public function action_404()
    {
        // ページが見つからない
        $this->template->title = 'ページが見つかりません。';
        $this->template->content = View::forge('auth/404');
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_invalid()
    {
        // 不正な画面遷移
        $this->template->title = '不正な操作です。';
        $this->template->content = View::forge('auth/invalid');
        $this->template->footer = View::forge('auth/footer');
    }


    public static function redirectIfAuth($path = '/')
    {
        // 認証済みユーザの場合は指定のURLへリダイレクト
        if (Auth::check())
        {
            Response::redirect($path);
        }
    }


    public static function redirectIfNotAuth($path = 'auth/login')
    {
        // 未認証ユーザの場合は指定のURLへリダイレクト
        if (!Auth::check())
        {
            Response::redirect($path);
        }
    }


    public static function checkCSRF()
    {
        // CSRFチェック(トークンがPOSTされているかどうか)
        if (!Security::check_token())
        {
            Response::redirect('auth/invalid');
        }
    }


    public static function checkAllowedMethod($method = '')
    {
        // 指定されたmethod以外は不正と扱う
        if (Input::method() != $method)
        {
            Response::redirect('auth/invalid');
        }
    }


    public static function is_admin()
    {
        // 管理者ユーザかどうか判定
        $admin_group_id = Model_Config::get_value('admin_group_id');
        return Auth::member($admin_group_id);
    }


    public static function is_admin_url($url = null)
    {
        // 管理ページのURLかどうか判定
        if (!isset($url))
        {
            $url = $_SERVER['REQUEST_URI'];
        }
        if (strpos($url, '/admin/') === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }


    public function action_login()
    {
        // 未認証済みユーザのみ許可
        $this->redirectIfAuth();
        // ログイン処理
        $error_msg = '';
        if (Input::method() == 'POST')
        {
            // 入力パラメータチェック
            $this->checkCSRF();
            $val = Model_Score::validate('login');
            if ($val->run())
            {
                $username = $val->validated('username');
                $password = $val->validated('password');
                // ログイン認証
                if (Auth::login($username, $password))
                {
                    // ログイン成功
                    $this->redirectIfAuth('/score/puzzle');
                }
                $error_msg = 'ログインに失敗しました。';
            }
            else
            {
                $error_msg = $val->show_errors();
            }
        }
        $this->template->title = 'ログイン';
        $this->template->content = View::forge('auth/login');
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_logout()
    {
        // ログアウト処理
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        Auth::logout();
        Session::destroy();
        $this->template->title = 'ログアウト';
        $this->template->content = View::forge('auth/logout');
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_create()
    {
        // ユーザー作成
        $page = Model_Staticpage::get('rule')[0];
        $this->template->title = 'ユーザー作成';
        $this->template->content = View::forge('auth/create');
        $this->template->content->set_safe('page', $page);
        $this->template->content->set_safe('errmsg', '');
        $this->template->footer = View::forge('auth/footer');
    }


    public function post_created()
    {
        // ユーザー作成実行
        // 入力パラメータチェック
        $this->checkCSRF();
        $val = Model_Score::validate('create');
        $error_msg = '';
        if ($val->run())
        {
            $username = $val->validated('username');
            $password = $val->validated('password');
            $admin = false;

            // 初登録ユーザは管理者とする
            $admin = count(Model_Users::get_users()) == 0 ? true : false;
            $result = Model_Users::create_user($username, $password, $admin);
            if ($result['created_id'] == false)
            {
                    $error_msg = 'ユーザー作成に失敗しました。'.$result['errmsg'];
            }
            else
            {
                // 登録したユーザでログインしておく
                Auth::login($username, $password);
                $data['sound_on'] = Cookie::get('sound_on', '1');
                $this->template->title = 'ユーザー登録完了';
                $this->template->content = View::forge('auth/created', $data);
                $this->template->footer = View::forge('auth/footer');
                return;
            }
        }
        else
        {
            $error_msg = $val->show_errors();
        }

        $this->template->title = 'ユーザー作成';
        $this->template->content = View::forge('auth/create');
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('auth/footer');
    }

    public function action_update()
    {
        // ユーザー更新
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        $this->template->title = 'ユーザー更新';
        $this->template->content = View::forge('auth/update');
        $this->template->content->set_safe('errmsg', '');
        $this->template->footer = View::forge('auth/footer');
    }


    public function post_updated()
    {
        // ユーザー更新実行
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        // 入力パラメータチェック
        $this->checkCSRF();
        $val = Model_Score::validate('update');
        $error_msg = '';
        if ($val->run())
        {
            $username = Auth::get_screen_name();
            $old_password = $val->validated('old_password');
            $new_password = $val->validated('password');

            $result = Model_Users::change_password($old_password, $new_password);
            if ($result['bool'] == false)
            {
                $error_msg = '更新に失敗しました。'.$result['errmsg'];
            }
            else
            {
                $this->template->title = 'ユーザー更新完了';
                $this->template->content = View::forge('auth/updated');
                $this->template->footer = View::forge('auth/footer');
                return;
            }
        }
        else
        {
            $error_msg = $val->show_errors();
        }
        $this->template->title = 'ユーザー更新';
        $this->template->content = View::forge('auth/update');
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_remove()
    {
        // ユーザー削除
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        $username = Auth::get_screen_name();
        $this->template->title = 'ユーザー削除';
        $this->template->content = View::forge('auth/remove');
        $this->template->content->set('errmsg', '');
        $this->template->content->set('username', $username);
        $this->template->footer = View::forge('auth/footer');
    }

    public function post_removed()
    {
        // ユーザー削除
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        // 入力パラメータチェック
        $this->checkCSRF();

        $result = Model_Users::delete_user(Auth::get_screen_name());
        if ($result == true)
        {
            Auth::logout();
            $this->template->title = 'ユーザー削除完了';
            $this->template->content = View::forge('auth/removed');
            $this->template->footer = View::forge('auth/footer');
        }
        else
        {
            $error_msg = '削除に失敗しました。'.$result['errmsg'];
            $this->template->title = 'ユーザー削除';
            $this->template->content = View::forge('auth/remove');
            $this->template->content->set('errmsg', $error_msg);
            $this->template->footer = View::forge('auth/footer');
        }
    }


    /**
     * action_sound
     * 
     * @return void
     */
    public function action_sound()
    {
        Cookie::set('sound_on', (Input::get('on') === '1') ? '1' : '0', 86400 * 30);
        Response::redirect('/');
    }
}

