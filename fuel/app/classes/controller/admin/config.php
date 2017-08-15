<?php

class Controller_Admin_Config extends Controller_Template
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
        $data['config_names'] = Model_Config::get_config(null, 'names');

        $data['config_system'] = Model_Config::get_config(null,'system');
        $data['config_switches'] = Model_Config::get_config(null,'switches');

        $data['config_chart']  = Model_Config::get_config(null,'chart');
        $data['config_history']  = Model_Config::get_config(null,'history');
        $data['config_review']  = Model_Config::get_config(null,'review');
        $data['config_chart_colors'] = Model_Config::get_config_chart_colors();

        $data['images'] = Model_Config::get_asset_images();
        $data['random_images'] = Model_Config::get_asset_random_images();
        $data['sounds'] = Model_Config::get_asset_sounds();
        $data['random_sounds'] = Model_Config::get_asset_random_sounds();

        $status = Model_Score::get_ctf_time_status();
        $data['start_time'] = $status['start_time'];
        $data['end_time'] = $status['end_time'];

        $data['success_random_texts'] = Model_Puzzle::get_random_text('success');
        $data['failure_random_texts'] = Model_Puzzle::get_random_text('failure');

        $data['total_levels'] = Model_Score::get_total_levels();
        $data['category_levels'] = Model_Score::get_category_levels();

        $this->template->title = 'Config一覧';
        $this->template->content = View::forge('admin/config/list', $data);
        $this->template->footer = '';
    }


    // ランダム画像を全て一覧表示
    public function action_imageslist($name = null)
    {
        $random_images = Model_Config::get_asset_random_images($name);
        $random_image = array_filter($random_images, function ($var) use ($name) {return $var['name'] == $name;})[0];

        $data['description'] = $random_image['description'];
        $data['images'] = $random_image['assets'];
        $data['name'] = $random_image['name'];

        $this->template->title = '画像一覧';
        $this->template->content = View::forge('admin/config/images_list', $data);
        $this->template->footer = View::forge('admin/config/footer');
    }
    

    // ランダム音声を全て一覧表示
    public function action_soundslist($name = null)
    {
        $random_sounds = Model_Config::get_asset_random_sounds($name);
        $random_sound = array_filter($random_sounds, function ($var) use ($name) {return $var['name'] == $name;})[0];

        $data['description'] = $random_sound['description'];
        $data['sounds'] = $random_sound['assets'];
        $data['name'] = $random_sound['name'];

        $this->template->title = '音源一覧';
        $this->template->content = View::forge('admin/config/sounds_list', $data);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function action_edit($config_id = null)
    {
        $error_msg = '';
        $data = '';

        if ($config_id == null)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $config = Model_Config::get_config($config_id)[0];
            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Config::validate('edit');

                if ($val->run())
                {
                    $config_id = $val->validated('id');
                    $value = $val->validated('value');
                    $result = Model_Config::update_config($config_id, null, $value);
                    if ($result)
                    {
                        // 成功画面へ転送
                        $data['config'] = Model_Config::get_config($config_id)[0];
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/config/updated', $data);
                        $this->template->footer = View::forge('admin/config/footer');
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

        $data['config'] = $config;
        $this->template->title = 'Config編集';
        $this->template->content = View::forge('admin/config/edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function action_editcolor($color_id = null)
    {
        $error_msg = '';
        $data = '';

        if ($color_id == null)
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $config_chart_color = Model_Config::get_config_chart_colors($color_id)[0];

            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Config::validate('editcolor');

                if ($val->run())
                {
                    $color_id = $val->validated('id');
                    $rank = $val->validated('rank');
                    $color = $val->validated('color');
                    if($config_chart_color)
                    {
                        $cnt = Model_Config::update_config_chart_color($color_id, $rank, $color);
                    }
                    else
                    {
                        list($color_id, $cnt) = Model_Config::insert_config_chart_color($rank, $color);
                    }

                    if ($cnt > 0)
                    {
                        // 成功画面へ転送
                        $data['config_chart_color'] = Model_Config::get_config_chart_colors($color_id)[0];
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/config/chart_color_updated', $data);
                        $this->template->footer = View::forge('admin/config/footer');
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

        $data['config_chart_color'] = $config_chart_color;
        $this->template->title = 'Config編集';
        $this->template->content = View::forge('admin/config/chart_color_edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function post_deletecolor()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Config::validate('deletecolor');

        $error_msg = '';
        $msg = '';

        if ($val->run())
        {
            $color_id = $val->validated('id');
            $config_chart_color = Model_Config::get_config_chart_colors($color_id)[0];

            if (Model_Config::delete_config_chart_color($color_id) < 1)
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

        $data['config_chart_color'] = $config_chart_color;
        $data['errmsg'] = $error_msg;
        $data['msg'] = $msg;
        $this->template->title = 'Delete';
        $this->template->content = View::forge('admin/config/chart_color_updated', $data);
        $this->template->footer = View::forge('admin/config/footer');
    }
    

    public function post_fileupload()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Config::validate('fileupload');

        $error_msg = '';
        $data = '';

        if ($val->run())
        {
            $name = $val->validated('name');
            $assets = Model_Config::get_assets($name);
            if (count($assets) != 1)
            {
                $error_msg = '追加できません。';
            }
            else
            {
                $asset = $assets[0];
                $type = $asset['type'];
                $sub_dir = $asset['sub_dir'];
                $data['description'] = $asset['description'];

                $config = array(
                    'path' => Model_Config::get_asset_dir($type).$sub_dir
                );
                // MIMEタイプで許容ファイル指定
                if ($asset['type'] == 'img')
                {
                    $config += array('type_whitelist' => array('image'));
                    $data['mimetype'] = 'image/*';
                }
                else if ($asset['type'] == 'audio')
                {
                    $config += array('type_whitelist' => array('audio', 'video', 'application'));
                    $data['mimetype'] = 'audio/*';
                }

                Upload::process($config);

                if (Upload::is_valid())
                {
                    if ($asset['is_random'] == 0)
                    {
                        // ファイル名で管理しているもの:古いファイルを削除
                        Model_Config::delete_asset_file($type, $sub_dir, $asset['filename']);
                        
                        // 最初のファイルのみ保存、DBの設定を更新
                        Upload::save(0);
                        $filename = Upload::get_files(0)['saved_as'];
                        Model_Config::update_asset($asset['name'], $filename);
                    }
                    else
                    {
                        // 複数ファイル全て保存
                        Upload::save();
                    }
                }

                $error_files = Upload::get_errors();
                if (count($error_files) == 0)
                {
                    // アップロード成功
                    $data['filenames'] = array_map(function ($var) { return $var['saved_as']; }, Upload::get_files());
                    $data['msg'] = 'アップロードしました。';
                    $this->template->title = 'Uploaded';
                    $this->template->content = View::forge('admin/config/file_uploaded', $data);
                    $this->template->footer = View::forge('admin/config/footer');
                    return;
                }
                
                foreach ($error_files as $file)
                {
                    foreach ($file['errors'] as $error)
                    {
                        $error_msg .= '<div>'.$error['message'].'</div>';
                    }
                }
            }
        }
        else
        {
            $error_msg = $val->show_errors();
        }

        $data['name'] = $name;
        $this->template->title = 'Upload';
        $this->template->content = View::forge('admin/config/_file_upload_form', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function post_deletefile()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Config::validate('deletefile');

        $error_msg = '';
        $msg = '';

        if ($val->run())
        {
            $filename = basename($val->validated('filename'));
            $name = $val->validated('name');

            $asset = Model_Config::get_assets($name)[0];
            $type = $asset['type'];
            $sub_dir = $asset['sub_dir'];

            if (Model_Config::delete_asset_file($type, $sub_dir, $filename) == true)
            {
                $msg = '削除しました。';
            }
            else
            {
                $error_msg = '削除に失敗しました。';
            }

            if ($asset['is_random'] == 0)
            {
                // ファイル名で管理しているもの:古いファイル名を削除
                Model_Config::update_asset($asset['name'], '');
            }

            $data['description'] = $asset['description'];
            $data['filename'] = $filename;
        }
        else
        {
            $error_msg = $val->show_errors();
        }

        $data['errmsg'] = $error_msg;
        $data['msg'] = $msg;
        $this->template->title = 'Delete';
        $this->template->content = View::forge('admin/config/file_delete', $data);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function action_settime()
    {
        $error_msg = '';
        $data = '';

        if (Input::method() == 'POST')
        {
            // 入力パラメータチェック
            Controller_Auth::checkCSRF();
            $val = Model_Config::validate('settime');

            if ($val->run())
            {
                $start_time = $val->validated('start_time');
                $end_time = $val->validated('end_time');
                $result = Model_Config::set_time($start_time, $end_time);
                if ($result)
                {
                    // 成功画面へ転送
                    $data['status'] = Model_Score::get_ctf_time_status();
                    $this->template->title = "Updated";
                    $this->template->content = View::forge('score/status', $data);
                    $this->template->footer = View::forge('admin/config/footer');
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

        $status = Model_Score::get_ctf_time_status();
        $data['start_time'] = $status['start_time'];
        $data['end_time'] = $status['end_time'];
        
        $this->template->title = '時刻設定';
        $this->template->content = View::forge('admin/config/time_edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }

    
    public function post_deletetime()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();

        Model_Config::delete_time();

        $data['status'] = Model_Score::get_ctf_time_status();
        $this->template->title = 'Delete';
        $this->template->content = View::forge('score/status', $data);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function action_edittexts($type = null)
    {
        $error_msg = '';
        $data = '';

        if ($type != 'success' && $type != 'failure')
        {
            $error_msg = '編集できません。';
        }
        else
        {
            $texts = Model_Puzzle::get_random_text($type);
            if (Input::method() == 'POST')
            {
                // 入力パラメータチェック
                Controller_Auth::checkCSRF();
                $val = Model_Config::validate('edittexts');

                if ($val->run())
                {
                    $texts = $val->validated('texts');
                    $result = Model_Puzzle::update_random_texts($type, $texts);
                    
                    if ($result['bool'] == true)
                    {
                        // 成功画面へ転送
                        $data['texts'] = Model_Puzzle::get_random_text($type);
                        $data['msg'] = '更新しました。';
                        $this->template->title = 'Updated';
                        $this->template->content = View::forge('admin/config/texts_updated', $data);
                        $this->template->footer = View::forge('admin/config/footer');
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

        $data['texts'] = $texts;
        $this->template->title = 'テキストメッセージ編集';
        $this->template->content = View::forge('admin/config/texts_edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }


    public function action_editlevels()
    {
        $error_msg = '';
        $data = '';

        $total_levels = Model_Score::get_total_levels();
        $category_levels = Model_Score::get_category_levels();
        if (Input::method() == 'POST')
        {
            // 入力パラメータチェック
            Controller_Auth::checkCSRF();
            $val = Model_Config::validate('editlevels');

            if ($val->run())
            {
                $levels = $val->validated('levels');
                $result = Model_Score::update_levels($levels['category_id'], $levels['level'], $levels['name'], $levels['criteria']);
                Model_Score::refresh_gained_levels();

                if ($result['bool'] == true)
                {
                    // 成功画面へ転送
                    $data['total_levels'] = Model_Score::get_total_levels();
                    $data['category_levels'] = Model_Score::get_category_levels();
                    $data['msg'] = '更新しました。';
                    $this->template->title = 'Updated';
                    $this->template->content = View::forge('admin/config/levels_updated', $data);
                    $this->template->footer = View::forge('admin/config/footer');
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

        $data['total_levels'] = $total_levels;
        $data['category_levels'] = $category_levels;
        $data['categories'] = Model_Category::get_categories();
        $this->template->title = 'レベル編集';
        $this->template->content = View::forge('admin/config/levels_edit', $data);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('admin/config/footer');
    }
}

