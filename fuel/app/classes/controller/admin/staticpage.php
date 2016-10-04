<?php

class Controller_Admin_Staticpage extends Controller_Template
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
        $data['pages'] = Model_Staticpage::get();
        $this->template->title = '静的ページ一覧';
        $this->template->content = View::forge('admin/staticpage/list', $data);
        $this->template->footer = '';
    }


    public function action_edit($name = null)
    {
        $error_msg = '';
        $data = '';

        if ($name == null)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $page = Model_Staticpage::get($name)[0];
            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Staticpage::validate('edit');

                if ($val->run())
                {
                    $name = $val->validated('name');
                    $display_name = $val->validated('display_name');
                    $content = $val->validated('content');
                    $is_active = $val->validated('is_active') == 1 ? 1 : 0;
                    $result = Model_Staticpage::update($name, $display_name, $content, $is_active);
                    if ($result)
                    {
                        // 成功画面へ転送
                        $data['page'] = Model_Staticpage::get($name)[0];
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/staticpage/updated', $data);
                        $this->template->footer = View::forge('admin/staticpage/footer');
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

        $data['page'] = $page;
        $this->template->title = '静的ページ編集';
        $this->template->content = View::forge('admin/staticpage/edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/staticpage/footer');
    }
}

