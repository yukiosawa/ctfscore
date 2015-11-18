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
        $admin_group_id = Config::get('ctfscore.admin.admin_group_id');
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
                    $this->redirectIfAuth();
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
        $this->template->title = 'ログアウト';
        $this->template->content = View::forge('auth/logout');
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_create()
    {
        // ユーザー作成
        $data['file'] = Config::get('ctfscore.static_page.agreement_file');
        $this->template->title = 'ユーザー作成';
        $this->template->content = View::forge('auth/create', $data);
        $this->template->content->set_safe('errmsg', '');
        $this->template->footer = View::forge('auth/footer');
    }


    public function action_created()
    {
        // ユーザー作成実行
        // POST以外は受け付けない
        $this->checkAllowedMethod('POST');
        // 入力パラメータチェック
        $this->checkCSRF();
        $val = Model_Score::validate('create');
        $error_msg = '';
        if ($val->run())
        {
            $username = $val->validated('username');
            $password = $val->validated('password');
            /* SimpleAuthにemailが必要 */
            $dummyemail = rand() . '@dummy.com';
            try
            {
                // 登録
                if (Auth::create_user($username, $password, $dummyemail))
                {
                    // 登録したユーザでログインしておく
                    Auth::login($username, $password);
                    $data['sound_on'] = Cookie::get('sound_on', '1');
                    $this->template->title = 'ユーザー登録完了';
                    $this->template->content = View::forge('auth/created', $data);
                    $this->template->footer = View::forge('auth/footer');
                    return;
                }
                else
                {
                    $error_msg = 'ユーザー作成に失敗しました。';
                }
            }
            catch (SimpleUserUpdateException $e)
            {
                $error_msg = $e->getMessage();
            }
        }
        else
        {
            $error_msg = $val->show_errors();
        }

        $data['file'] = Config::get('ctfscore.static_page.agreement_file');
        $this->template->title = 'ユーザー作成';
        $this->template->content = View::forge('auth/create', $data);
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


    public function action_updated()
    {
        // ユーザー更新実行
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        // POST以外は受け付けない
        $this->checkAllowedMethod('POST');
        // 入力パラメータチェック
        $this->checkCSRF();
        $val = Model_Score::validate('update');
        $error_msg = '';
        if ($val->run())
        {
            $username = Auth::get_screen_name();
            $values = array(
                'password' => $val->validated('password'),
                'old_password' => $val->validated('old_password'),
            );
            if (!empty($values))
            {
                try {
                    if (Auth::update_user($values, $username))
                    {
                        $this->template->title = 'ユーザー更新完了';
                        $this->template->content = View::forge('auth/updated');
                        $this->template->footer = View::forge('auth/footer');
                        return;
                    }
                    else
                    {
                        $error_msg = '更新に失敗しました。';
                    }
                }
                catch (Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
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

    public function action_removed()
    {
        // ユーザー削除
        // 認証済みユーザのみ許可
        $this->redirectIfNotAuth();
        // POST以外は受け付けない
        $this->checkAllowedMethod('POST');
        // 入力パラメータチェック
        $this->checkCSRF();
        try {
            Auth::delete_user(Auth::get_screen_name());
            Auth::logout();
        }
        catch (Exception $e)
        {
            $error_msg = $e->getMessage();
            $this->template->title = 'ユーザー削除';
            $this->template->content = View::forge('auth/remove');
            $this->template->content->set('errmsg', $error_msg);
            $this->template->footer = View::forge('auth/footer');
            return;
        }
        $this->template->title = 'ユーザー削除完了';
        $this->template->content = View::forge('auth/removed');
        $this->template->footer = View::forge('auth/footer');
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
