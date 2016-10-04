<?php

class Model_Puzzle extends Model
{
    public static function is_right_answer($puzzle_id, $flag)
    {
        $result = DB::select()->from('flags')
                              ->where('puzzle_id', $puzzle_id)
                              ->where('flag', $flag)
                              ->execute()->as_array();
        if (count($result) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    
    public static function get_puzzle_id($flag)
    {
        $result = DB::select('puzzle_id')->from('flags')
            ->where('flag', $flag)
            ->execute()->as_array();
        if (count($result) > 0)
        {
            return $result[0]['puzzle_id'];
        }
    }


    public static function get_answered_puzzles($uid = NULL)
    {
        // 指定されたユーザの回答済み一覧を返す
        if (is_null($uid))
        {
            return array();
        }
        $result = DB::select()->from('gained')
            ->where('uid', '=', $uid)
            ->join('puzzles')
            ->on('gained.puzzle_id', '=', 'puzzles.puzzle_id')
            ->join('categories')
            ->on('puzzles.category_id', '=', 'categories.id')
            ->order_by('gained.puzzle_id', 'asc')
            ->execute()->as_array();
        return $result;
    }


    public static function get_puzzles($puzzle_id = NULL)
    {
        // 指定された条件でpuzzleテーブルの内容を返す
        $query = DB::select()->from('puzzles')
            ->join('categories')
            ->on('puzzles.category_id', '=', 'categories.id');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzles.puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzles.puzzle_id', 'asc');
        $result = $query->execute()->as_array();

        return $result;
    }


    public static function get_puzzles_from_category($category)
    {
        $result = DB::select()->from('puzzles')
            ->join('categories')
            ->on('puzzles.category_id', '=', 'categories.id')
            ->where('categories.category', $category)
            ->order_by('puzzles.puzzle_id', 'asc')
            ->execute()->as_array();

        return $result;
    }


    public static function get_puzzles_addinfo($userid = NULL)
    {
        // 全問題を取得
        $puzzles = Model_Puzzle::get_puzzles();

        // 追加情報をセット
        if (!$userid)
        {
            // 指定されない場合はログイン中のユーザIDとする
            list($driver, $userid) = Auth::get_user_id();
        }

        // 追加情報付与
        $answered_all = Model_Puzzle::is_answered_puzzle_all($userid);
        $score_all = Model_Review::average_score_all();
        $gained_count = Model_Puzzle::get_puzzle_gained_count();
        $hints_count = Model_Hint::get_hints_count();

        for ($i = 0; $i < count($puzzles); $i++)
        {
            // 添付ファイルのファイル名
            //$puzzles[$i] += array('attachments' =>
            //    Model_Puzzle::get_attachment_names($puzzles[$i]['puzzle_id']));

            // 回答済かどうかを付加する
            $puzzles[$i] += array('answered' => in_array($puzzles[$i]['puzzle_id'], $answered_all));

            // レビュー平均スコアを付加
            $score = (isset($score_all[$puzzles[$i]['puzzle_id']]) === true) ?
                $score_all[$puzzles[$i]['puzzle_id']] : 0;
            $puzzles[$i] += array('avg_score' => $score);

            // 回答者数を付与
            $gained = (isset($gained_count[$puzzles[$i]['puzzle_id']]) === true) ?
                $gained_count[$puzzles[$i]['puzzle_id']] : 0;
            $puzzles[$i] += array('gained' => $gained);

            // ヒントリクエスト数を付与
            $hints = (isset($hints_count[$puzzles[$i]['puzzle_id']]) === true) ?
                $hints_count[$puzzles[$i]['puzzle_id']] : 0;
            $puzzles[$i] += array('hints' => $hints);
        }

        return $puzzles;
    }


    // ポイント一覧を取得
    public static function get_points()
    {
        $query = DB::select('point')->from('puzzles')
            ->group_by('point');
        $query->order_by('point', 'asc');
        $result = $query->execute()->as_array('point');
        return array_keys($result);
    }

    
    /* // カテゴリ一覧を取得
       public static function get_categories($id)
       {
       $query = DB::select()->from('categories')
       ->where('id', '!=', Model_Config::get_value('total_category_id'))
       ->order_by('id', 'asc');
       if ($id)
       {
       $query->where('id', $id);
       }
       $result = $query->execute()->as_array();
       return $result;
       } */


    // カテゴリ(+ポイント)一覧を取得
    public static function get_categories_with_point()
    {
        $query = DB::select(DB::expr('categories.category,SUM(puzzles.point) as point'))->from('puzzles')
            ->join('categories')
            ->on('puzzles.category_id', '=', 'categories.id')
            ->group_by('puzzles.category_id');
        $result = $query->execute()->as_array();
        return $result;
    }


    // カテゴリごとの獲得スコアを取得
    public static function get_category_point($uid = NULL)
    {
        $query = DB::select(DB::expr('categories.category as category, SUM(puzzles.point) + SUM(CASE WHEN gained.has_bonus = 1 THEN puzzles.bonus_point ELSE 0 END) as point'))->from('gained')
         ->join('puzzles')->on('gained.puzzle_id', '=', 'puzzles.puzzle_id')
         ->join('categories')->on('puzzles.category_id', '=', 'categories.id')
         ->where('gained.uid', $uid)->group_by('categories.category');
//        $query = DB::select(DB::expr('categories.category as category, SUM(point) + SUM(bonus_point) as point'))->from('gained')
//         ->join('categories')->on('gained.category_id', '=', 'categories.id')
//         ->where('gained.uid', $uid)->group_by('categories.category');
        $result = $query->execute()->as_array('category');
        return $result;
    }


    public static function is_answered_puzzle($uid = NULL, $puzzle_id = NULL)
    {
        // 指定されたpuzzleが回答済みかどうかを返す
        $query = DB::select()->from('gained');
        $query->where('uid', $uid);
        $query->where('puzzle_id', $puzzle_id);
        $result = $query->execute();

        if (count($result) != 1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * is_answered_puzzle_all
     * 
     * @static
     * @return array(puzzle_id,...)
     */
    public static function is_answered_puzzle_all($uid = NULL)
    {
        $result = DB::select('puzzle_id')->from('gained')
            ->where('uid', $uid)
            ->execute()->as_array();
        return array_map(function ($var) { return $var['puzzle_id']; }, $result);
    }

    /**
     * get_puzzle_gained
     * 
     * @param int $puzzle_id 
     * @static
     * @return array(array(username,gained_at),...)
     */
    public static function get_puzzle_gained($puzzle_id)
    {
        $result = DB::select(DB::expr('users.username,gained.gained_at'))->from('gained')
            ->join('users')
            ->on('gained.uid', '=', 'users.id')
            ->where('puzzle_id', $puzzle_id)
            ->order_by('gained_at', 'asc')
            ->execute()->as_array();
        return $result;
    }

    /**
     * get_puzzle_gained_count
     * 
     * @static
     * @return array(array(puzzle_id => COUNT(puzzle_id)),...)
     */
    public static function get_puzzle_gained_count()
    {
        $result = DB::select(DB::expr('puzzle_id, COUNT(puzzle_id)'))->from('gained')
            ->group_by('puzzle_id')
            ->execute()->as_array();
        $return = array();
        foreach ($result as $values) {
            $return[$values['puzzle_id']] = $values['COUNT(puzzle_id)'];
        }
        return $return;
    }

    public static function set_puzzle_gained($uid = NULL, $puzzle_id = NULL)
    {
        // 獲得ポイントを更新する
        $now = Model_Score::get_current_time();
        // 現在獲得済み総ポイント
        $totalpoint = DB::select('totalpoint')
            ->from('users')->where('id', $uid)
            ->execute()->as_array()[0]['totalpoint'];
        // 更新後の総ポイント
        $puzzle = Model_Puzzle::get_puzzles($puzzle_id)[0];
        $bonus_point = 0;
        $has_bonus = 0;
        if (Model_Score::is_first_winner($puzzle_id))
        {
            // 最初の正解者はボーナスポイント加点
            $bonus_point = $puzzle['bonus_point'];
            $has_bonus = 1;
        }
        $newpoint = $totalpoint + $puzzle['point'] + $bonus_point;
        try {
            DB::start_transaction();
            DB::insert('gained')->set(array(
                'uid' => $uid,
                'puzzle_id' => $puzzle_id,
                'has_bonus' => $has_bonus,
                'totalpoint' => $newpoint,
                'gained_at' => $now
            ))->execute();
            DB::update('users')->set(array(
                'totalpoint' => $newpoint,
                'pointupdated_at' =>$now
            ))->where('id', $uid)->execute();
            DB::commit_transaction();
        } catch (Exception $e) {
            /* ロールバック */
            DB::rollback_transaction();
            throw $e;
        }
    }


    // 現在の回答済問題から獲得総スコアを再計算する
    public static function refresh_gained_points()
    {
        // ユーザごとに再計算
        $users = DB::select()->from('users')->execute()->as_array();
        foreach ($users as $user)
        {
            $uid = $user['id'];
            $username = $user['username'];
            $totalpoint = 0;
            $gained_all = DB::select()->from('gained')->where('uid', $uid)->order_by('gained_at', 'asc')->execute()->as_array();
            foreach ($gained_all as $gained)
            {
                $puzzle = Model_Puzzle::get_puzzles($gained['puzzle_id'])[0];
                $totalpoint += $puzzle['point'];
                if ($gained['has_bonus'] == 1)
                {
                    $totalpoint += $puzzle['bonus_point'];
                }
                try
                {
                    DB::start_transaction();
                    DB::update('gained')->set(array(
                        'totalpoint' => $totalpoint
                    ))->where('uid', $uid)->where('puzzle_id', $gained['puzzle_id'])->execute();
                    DB::update('users')->set(array(
                        'totalpoint' => $totalpoint
                    ))->where('id', $uid)->execute();
                    DB::commit_transaction();
                }
                catch (Exception $e)
                {
                    /* ロールバック */
                    DB::rollback_transaction();
                    throw $e;
                }
            }
            
            // 管理者によるボーナスがあれば加算
            $admin_bonus = DB::select(DB::expr('SUM(bonus_point) as point'))
                ->from('admin_bonus_point')
                ->where('uid', $uid)->execute()->as_array();
            if ($admin_bonus[0]['point'] > 0)
            {
                $totalpoint += $admin_bonus[0]['point'];
                DB::start_transaction();
                DB::update('users')->set(array(
                    'totalpoint' => $totalpoint
                ))->where('id', $uid)->execute();
                DB::commit_transaction();
            }
        }
    }


    // 正解時に表示するメッセージ
    public static function get_success_message($puzzle_id = null)
    {
        // 問題個別指定の画像があるかチェック
        $images = Model_Puzzle::get_success_images($puzzle_id);
        if (count($images) > 0)
        {
            // 複数の場合はランダムとする
            $image = $images[array_rand($images)];
            $filename = $image['filename'];
            $url = $image['url'];
            $image = array(
                'name' => 'success_img_'.$puzzle_id,
                'filename' => $filename,
                'url' => $url
            );
            $message['image'] = $image;
        }
        else
        {
            // 指定がない場合は共通設定ののランダム画像
            $images = Model_Config::get_asset_random_images('success_random_image');
            $assets = $images[0]['assets'];
            $message['image'] = $assets[array_rand($assets)];
        }

        // 音声
        $sounds = Model_Config::get_asset_random_sounds('success_random_sound');
        $assets = $sounds[0]['assets'];
        $message['sound'] = $assets[array_rand($assets)];

        // 問題個別のテキストがあるかチェック
        $texts = Model_Puzzle::get_success_text($puzzle_id);
        if (count($texts) > 0)
        {
            // 複数の場合はランダムとする
            $text = $texts[array_rand($texts)];
            $message['text'] = $text['text'];
        }
        else
        {
            // 指定なしの場合は共通設定のランダムテキスト
            $texts = Model_Puzzle::get_random_text('success');
            if (count($texts) > 0)
            {
                $message['text'] = $texts[array_rand($texts)]['text'];
            }
        }

        return $message;
    }


    public static function get_success_text($puzzle_id = NULL)
    {
        $query = DB::select()->from('success_text');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzle_id', 'asc');
        $result = $query->execute()->as_array();

        return $result;
    }


    public static function get_random_text($type)
    {
        if ($type == 'success')
        {
            return DB::select()->from('success_random_text')->execute()->as_array();
        }
        else if ($type == 'failure')
        {
            return DB::select()->from('failure_random_text')->execute()->as_array();
        }
    }


    // 不正解時に表示するメッセージ
    public static function get_failure_message()
    {
        // 画像
        $images = Model_Config::get_asset_random_images('failure_random_image');
        $assets = $images[0]['assets'];
        $message['image'] = $assets[array_rand($assets)];

        // 音声
        $sounds = Model_Config::get_asset_random_sounds('failure_random_sound');
        $assets = $sounds[0]['assets'];
        $message['sound'] = $assets[array_rand($assets)];

        // テキスト
        $texts = Model_Puzzle::get_random_text('failure');
        if (count($texts) > 0)
        {
            $message['text'] = $texts[array_rand($texts)]['text'];
        }

        return $message;
    }


    public static function get_attachment_dir($puzzle_id = NULL)
    {
        if ($puzzle_id)
        {
            return DOCROOT.Model_Config::get_value('attachment_dir').'/'.$puzzle_id;
        }
        else
        {
            return DOCROOT.Model_Config::get_value('attachment_dir');
        }
    }


    public static function get_attachment_names($puzzle_id = NULL)
    {
        $files = array();
        $raws = DB::select('filename')->from('attachment')
            ->where('puzzle_id', $puzzle_id)
            ->execute()->as_array();
        foreach ($raws as $raw)
        {
            $files[] = $raw['filename'];
        }
        return $files;
    }


    public static function get_flags($puzzle_id = null)
    {
        $query = DB::select()->from('flags');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzle_id', 'asc');
        $result = $query->execute()->as_array();

        return $result;
    }


    public static function get_attachments($puzzle_id = null)
    {
        $query = DB::select()->from('attachment');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzle_id', 'asc');
        $result = $query->execute()->as_array();

        $attaches = array_map(function ($var) {
            $filename = $var['filename'];
            $url = Uri::base(false).'download/puzzle?id='.$var['puzzle_id'].'&file='.urlencode($filename);
            $var['url'] = $url;
            return $var;
        }, $result);

        return $attaches;
    }


    public static function get_success_images($puzzle_id = NULL)
    {
        $query = DB::select()->from('success_image');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzle_id', 'asc');
        $result = $query->execute()->as_array();

        $images = array_map(function ($var) {
            $sub_dir = Model_Config::get_value('success_image_dir').'/'.$var['puzzle_id'];
            $filename = $var['filename'];
            $url = Asset::get_file($filename, 'img', $sub_dir);
            $url = $url == false ? '' : $url;
            $var['url'] = $url;
            return $var;
        }, $result);

        return $images;
    }

    
    public static function get_success_image_dir($puzzle_id = NULL)
    {
        $dir1 = Config::get('asset.img_dir');
        $dir2 = Model_Config::get_value('success_image_dir');

        if ($puzzle_id)
        {
            return DOCROOT.Config::get('asset.paths')[0].$dir1.$dir2.'/'.$puzzle_id;
        }
        else
        {
            return DOCROOT.Config::get('asset.paths')[0].$dir1.$dir2;
        }
    }

    
    // 管理者：問題登録更新
    public static function update_puzzle($puzzle = NULL, $flags = NULL, $attaches = NULL, $success_images = NULL, $success_texts = NULL)
    {
        $bool = true;
        $errmsg = '';
        $puzzle_id = $puzzle['puzzle_id'];

        $result = Model_Puzzle::update_puzzle_main($puzzle);
        if ($result['bool'] == false)
        {
            $bool = false;
            $error_msg .= $result['errmsg'];
        }

        $texts_update = array(
            'flags' => $flags,
            'success_text' => $success_texts
        );
        foreach ($texts_update as $type => $texts)
        {
            $result = Model_Puzzle::update_texts($type, $puzzle_id, $texts);
            if ($result['bool'] == false)
            {
                $bool = false;
                $error_msg .= $result['errmsg'];
            }
        }

        // 登録済のファイル更新
        $files_update = array(
            'attachment' => $attaches,
            'success_image' => $success_images
        );
        foreach ($files_update as $type => $files)
        {
            $result = Model_Puzzle::update_files($type, $puzzle_id, $files);
            if ($result['bool'] == false)
            {
                $bool = false;
                $error_msg .= $result['errmsg'];
            }
        }

        // ファイル新規アップロード
        Upload::process();
        if (Upload::is_valid())
        {
            foreach ($files_update as $type => $files)
            {
                $result = Model_Puzzle::upload_files($type, $puzzle_id);
                if ($result['bool'] == false)
                {
                    $bool = false;
                    $error_msg .= $result['errmsg'];
                }
            }
        }

        foreach (Upload::get_errors() as $error_file)
        {
            foreach ($error_file['errors'] as $error)
            {
                // ファイルアップロードは必須ではない
                if ($error['error'] != Upload::UPLOAD_ERR_NO_FILE)
                {
                    $bool = false;
                    $error_msg .= '<div>'.$error['message'].'</div>';
                }
            }
        }

        return array('bool' => $bool, 'errmsg' => $error_msg);
    }


    public static function update_puzzle_main($puzzle)
    {
        $puzzle_id = $puzzle['puzzle_id'];
        $is_point_update = false;
        try
        {
            DB::start_transaction();

            // 既に登録があれば更新、なければ新規
            $q1 = '';
            $current = DB::select()->from('puzzles')->where('puzzle_id', $puzzle_id)->execute();
//            $cnt = count(DB::select()->from('puzzles')->where('puzzle_id', $puzzle_id)->execute());
            if (count($current) > 0)
            {
                $q1 = DB::update('puzzles')->where('puzzle_id', $puzzle_id);
                if ($current[0]['point'] != $puzzle['point'] || $current[0]['bonus_point'] != $puzzle['bonus_point'])
                {
                    $is_point_update = true;
                }
            }
            else
            {
                $q1 = DB::insert('puzzles');
            }
            $q1->set(array(
                'puzzle_id' => $puzzle_id,
                'point' => $puzzle['point'],
                'bonus_point' => $puzzle['bonus_point'],
                'category_id' => $puzzle['category_id'],
                'title' => $puzzle['title'],
                'content' => $puzzle['content'],
            ))->execute();

            if ($is_point_update == true)
            {
                // 全ユーザの獲得済ポイントを更新
                Model_Puzzle::refresh_gained_points();
            }

            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => '問題登録に失敗しました。');
        }

        return array('bool' => true);
    }


    public static function update_texts($type, $puzzle_id, $texts_update)
    {
        if ($type == 'flags')
        {
            $table_name = 'flags';
            $col_name = 'flag';
        }
        else if ($type == 'success_text')
        {
            $table_name = 'success_text';
            $col_name = 'text';
        }
        else
        {
            return array('bool' => false);
        }

        // POSTされていないデータは削除対象とする
        $texts_before = DB::select()->from($table_name)->where('puzzle_id', $puzzle_id)->execute()->as_array();
        $texts_delete = array_filter($texts_before, function ($var) use ($texts_update) {return !array_key_exists($var['id'], $texts_update); });

        try
        {
            DB::start_transaction();
            foreach ($texts_update as $id => $val)
            {
                if ($val == '')
                {
                    continue;
                }
                
                // 既に登録があれば更新、なければ新規
                if (count(DB::select()->from($table_name)->where('id', $id)->execute()) < 1)
                {
                    DB::insert($table_name)->set(array(
                        'puzzle_id' => $puzzle_id,
                        $col_name => $val
                    ))->execute();
                }
                else
                {
                    DB::update($table_name)->where('id', $id)->set(array(
                        'puzzle_id' => $puzzle_id,
                        $col_name => $val
                    ))->execute();
                }
            }

            foreach ($texts_delete as $del)
            {
                DB::delete($table_name)->where('id', $del['id'])->execute();
            }
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'テキストデータの更新に失敗しました。: '.$type);
        }

        return array('bool' => true);
    }


    public static function update_files($type, $puzzle_id, $files_update)
    {
        if ($type == 'attachment')
        {
            $files_before = Model_Puzzle::get_attachments($puzzle_id);
            $save_dir = Model_Puzzle::get_attachment_dir($puzzle_id);
            $table_name = 'attachment';
        }
        else if ($type == 'success_image')
        {
            $files_before = Model_Puzzle::get_success_images($puzzle_id);
            $save_dir = Model_Puzzle::get_success_image_dir($puzzle_id);
            $table_name = 'success_image';
        }
        else
        {
            return array('bool' => false);
        }

        // POSTされていないデータは削除対象とする
        $files_delete = array_filter($files_before, function ($var) use ($files_update) {return !array_key_exists($var['id'], $files_update); });

        try
        {
            DB::start_transaction();
            foreach ($files_delete as $del)
            {
                $filepath = $save_dir.'/'.$del['filename'];
                if (File::exists($filepath))
                {
                    File::delete($filepath);
                }
                DB::delete($table_name)->where('id', $del['id'])->execute();
            }
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'ファイルの更新に失敗しました。: '.$type);
        }
        return array('bool' => true);
    }


    // ファイルのアップロード
    public static function upload_files($type, $puzzle_id)
    {
        if ($type == 'attachment')
        {
            $field_new = 'attach_upload';
            $save_dir = Model_Puzzle::get_attachment_dir($puzzle_id);
            $table_name = 'attachment';
            $mimetypes = '';
        }
        else if ($type == 'success_image')
        {
            $field_new = 'success_image_upload';
            $save_dir = Model_Puzzle::get_success_image_dir($puzzle_id);
            $table_name = 'success_image';
            $mimetypes = array('image');
        }
        else
        {
            return array('bool' => false);
        }

        // アップロードされたファイルは全て新規追加
        $new_files = array();
        $files = Upload::get_files();
        for ($i=0; $i<count($files); $i++)
        {
            $tmp = explode(':', $files[$i]['field']);
            if ($tmp[0] == $field_new)
            {
                // ファイルをひとつずつ保存していく
                if ($mimetypes == '' || in_array(explode('/', $files[$i]['mimetype'])[0], $mimetypes))
                {
                    Upload::save($save_dir, $i);
                    $saved_file = Upload::get_files($i);
                    $new_files[$tmp[1]] = $saved_file['saved_as'];
                }
            }
        }

        try
        {
            DB::start_transaction();
            foreach ($new_files as $key => $filename)
            {
                $result = DB::insert($table_name)->set(array(
                    'puzzle_id' => $puzzle_id,
                    'filename' => $filename
                ))->execute();
            }

            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'ファイルのアップロードに失敗しました。: '.$type);
        }
        return array('bool' => true);
    }


    public static function update_random_texts($type, $texts_update)
    {
        if ($type == 'success')
        {
            $table_name = 'success_random_text';
            $col_name = 'text';
        }
        else if ($type == 'failure')
        {
            $table_name = 'failure_random_text';
            $col_name = 'text';
        }
        else
        {
            return array('bool' => false);
        }

        // POSTされていないデータは削除対象とする
        $texts_before = DB::select()->from($table_name)->execute()->as_array();
        $texts_delete = array_filter($texts_before, function ($var) use ($texts_update) {return !array_key_exists($var['id'], $texts_update); });

        try
        {
            DB::start_transaction();
            foreach ($texts_update as $id => $val)
            {
                if ($val == '')
                {
                    continue;
                }
                
                // 既に登録があれば更新、なければ新規
                if (count(DB::select()->from($table_name)->where('id', $id)->execute()) < 1)
                {
                    DB::insert($table_name)->set(array(
                        $col_name => $val
                    ))->execute();
                }
                else
                {
                    DB::update($table_name)->where('id', $id)->set(array(
                        $col_name => $val
                    ))->execute();
                }
            }

            foreach ($texts_delete as $del)
            {
                DB::delete($table_name)->where('id', $del['id'])->execute();
            }
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'テキストデータの更新に失敗しました。: '.$type);
        }

        return array('bool' => true);
    }


    public static function delete_puzzle($puzzle_id)
    {
        try
        {
            DB::start_transaction();
            // flag
            DB::delete('flags')->where('puzzle_id', $puzzle_id)->execute();
            // 正解時に表示するテキストメッセージ
            DB::delete('success_text')->where('puzzle_id', $puzzle_id)->execute();

            // 添付ファイル
            $files = DB::select()->from('attachment')->where('puzzle_id', $puzzle_id)->execute()->as_array();
            $save_dir = Model_Puzzle::get_attachment_dir($puzzle_id);
            foreach ($files as $file)
            {
                if (File::exists($save_dir.'/'.$file['filename']))
                {
                    File::delete($save_dir.'/'.$file['filename']);
                }
            }
            DB::delete('attachment')->where('puzzle_id', $puzzle_id)->execute();

            // 正解時に表示する画像ファイル
            $files = DB::select()->from('success_image')->where('puzzle_id', $puzzle_id)->execute()->as_array();
            $save_dir = Model_Puzzle::get_success_image_dir($puzzle_id);
            foreach ($files as $file)
            {
                if (File::exists($save_dir.'/'.$file['filename']))
                {
                    File::delete($save_dir.'/'.$file['filename']);
                }
            }
            DB::delete('success_image')->where('puzzle_id', $puzzle_id)->execute();

            // 問題を削除
            $result = DB::delete('puzzles')->where('puzzle_id', $puzzle_id)->execute();

            // 全ユーザの獲得済ポイントを更新
            Model_Puzzle::refresh_gained_points();
            
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            throw $e;
        }

        return $result;
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if ($factory == 'edit')
	{
            $val->add('puzzle_id', 'ID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
	    $val->add('category_id', 'カテゴリID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
            $val->add('title', 'タイトル')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
            $val->add('point', 'ポイント')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 0);
            $val->add('bonus_point', 'ボーナス')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 0);
	    $val->add('content', '問題文')
		->add_rule('max_length', 1000);
            $flag = Input::post('flag');
            foreach ($flag as $key => $value)
            {
                $val->add('flag['.$key.']', 'フラグ')
                    ->add_rule('required')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 255);
            }
            $attach = Input::post('attach');
            foreach ($attach as $key => $value)
            {
                $val->add('attach['.$key.']', '添付ファイル')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 255);
            }
            $success_image = Input::post('success_image');
            foreach ($success_image as $key => $value)
            {
                $val->add('success_image['.$key.']', '正解時に表示する画像')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 255);
            }
            $success_text = Input::post('success_text');
            foreach ($success_text as $key => $value)
            {
                $val->add('success_text['.$key.']', '正解時に表示するテキスト')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 255);
            }
	}
        else if ($factory == 'delete')
        {
            $val->add('puzzle_id', 'ID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
        }

	return $val;
    }

}

