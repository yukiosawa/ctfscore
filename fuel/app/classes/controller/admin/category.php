<?php

class Controller_Admin_Category extends Controller_Template
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
        $data['categories'] = Model_Category::get_categories();
        $data['is_editable'] = true;
        $this->template->title = 'カテゴリ一覧';
        $this->template->content = View::forge('admin/category/list', $data);
        $this->template->footer = '';
    }


    public function action_create()
    {
        if (Input::method() == 'POST')
        {
            // 入力パラメータチェック
            Controller_Auth::checkCSRF();
            $val = Model_Category::validate('create');
            $category = Input::post('category');

            if ($val->run())
            {
                $category = $val->validated('category');
                $id = Model_Category::create_category($category);
                if ($id)
                {
                    // 成功画面へ転送
                    $data['categories'] = Model_Category::get_categories($id);
                    $data['msg'] = '作成しました。';
                    $this->template->title = 'Created';
                    $this->template->content = View::forge('admin/category/updated', $data);
                    $this->template->footer = View::forge('admin/category/footer');
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

            $data['category'] = $category;
        }

        $this->template->title = 'カテゴリ作成';
        $this->template->content = View::forge('admin/category/create', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/category/footer');
    }

    
    public function action_edit($category_id)
    {
        if ($category_id == null)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $category_tmp = Model_Category::get_categories($category_id)[0];
            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Category::validate('edit');

                if ($val->run())
                {
                    $category_id = $val->validated('category_id');
                    $category = $val->validated('category');
                    $result = Model_Category::update_category($category_id, $category);
                    if ($result)
                    {
                        // 成功画面へ転送
                        $data['categories'] = Model_Category::get_categories($category_id);
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/category/updated', $data);
                        $this->template->footer = View::forge('admin/category/footer');
                        return;
                    }
                    else
                    {
                        $error_msg = '更新に失敗しました。';
                        $error_msg .= $result['errmsg'];
                        $puzzle_tmp = $puzzle;
                    }
                }
                else
                {
                    $error_msg = $val->show_errors();
                }
            }
        }

        $data['is_new'] = $is_new;
        $data['category'] = $category_tmp;
        $data['categories'] = Model_Category::get_categories();
        $this->template->title = '問題編集';
        $this->template->content = View::forge('admin/category/edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/category/footer');
    }


    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Category::validate('delete');

        if ($val->run())
        {
            $category_id = $val->validated('category_id');
            $categories_tmp = Model_Category::get_categories($category_id);

            if (Model_Category::delete_category($category_id) < 1)
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

        $data['categories'] = $categories_tmp;
        $data['errmsg'] = $error_msg;
        $data['msg'] = $msg;
        $this->template->title = 'Delete';
        $this->template->content = View::forge('admin/category/delete', $data);
        $this->template->footer = View::forge('admin/category/footer');
    }
}

