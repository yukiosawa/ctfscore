<?php

class Controller_Admin_Puzzle extends Controller_Template
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
        $data['puzzles'] = Model_Puzzle::get_puzzles();
        $data['is_editable'] = true;
        $this->template->title = '問題 一覧';
        $this->template->content = View::forge('admin/puzzle/list', $data);
        $this->template->footer = '';
    }


    public function action_edit($puzzle_id)
    {
        $is_new = true;
        if ($puzzle_id == null)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $puzzle_tmp = Model_Puzzle::get_puzzles($puzzle_id)[0];
            $is_new = empty($puzzle_tmp) ? true : false;
            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Puzzle::validate('edit');

                if ($val->run())
                {
                    $puzzle['puzzle_id'] = $val->validated('puzzle_id');
                    $puzzle['category_id'] = $val->validated('category_id');
                    $puzzle['title'] = $val->validated('title');
                    $puzzle['point'] = $val->validated('point');
                    $puzzle['bonus_point'] = $val->validated('bonus_point');
                    $puzzle['content'] = $val->validated('content');
                    $flags = $val->validated('flag');
                    $attaches = $val->validated('attach');
                    $success_images = $val->validated('success_image');
                    $success_texts = $val->validated('success_text');

                    $result = Model_Puzzle::update_puzzle($puzzle, $flags, $attaches, $success_images, $success_texts);

                    if ($result['bool'] == true)
                    {
                        // 成功画面へ転送
                        $data['puzzles'] = Model_Puzzle::get_puzzles($puzzle['puzzle_id']);
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/puzzle/updated', $data);
                        $this->template->footer = View::forge('admin/puzzle/footer');
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
        if ($is_new == false)
        {
            $data['flags'] = Model_Puzzle::get_flags($puzzle_id);
            $data['attaches'] = Model_Puzzle::get_attachments($puzzle_id);
            $data['success_images'] = Model_Puzzle::get_success_images($puzzle_id);
            $data['success_texts'] = Model_Puzzle::get_success_text($puzzle_id);
        }
        $data['puzzle'] = $puzzle_tmp;
        $data['categories'] = Model_Category::get_categories();
        $this->template->title = '問題編集';
        $this->template->content = View::forge('admin/puzzle/edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/puzzle/footer');
    }


    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Puzzle::validate('delete');

        if ($val->run())
        {
            $puzzle_id = $val->validated('puzzle_id');
            $puzzle_tmp = Model_Puzzle::get_puzzles($puzzle_id);

            if (Model_Puzzle::delete_puzzle($puzzle_id) < 1)
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

        $data['puzzles'] = $puzzle_tmp;
        $data['errmsg'] = $error_msg;
        $data['msg'] = $msg;
        $this->template->title = 'Delete';
        $this->template->content = View::forge('admin/puzzle/delete', $data);
        $this->template->footer = View::forge('admin/puzzle/footer');
    }
}

