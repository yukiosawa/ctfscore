<?php

class Model_Puzzle extends Model
{
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
            ->order_by('gained.puzzle_id', 'asc')
            ->execute()->as_array();
        return $result;
    }


    public static function get_puzzles($puzzle_id = NULL)
    {
        // 指定された条件でpuzzleテーブルの内容を返す
        $query = DB::select()->from('puzzles');
        if (!is_null($puzzle_id))
        {
            $query->where('puzzle_id', $puzzle_id);
        }
        $query->order_by('puzzle_id', 'asc');
        $result = $query->execute()->as_array();

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
        $gained_count = Model_Puzzle::get_puzzlue_gained_count();
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


    // カテゴリ一覧を取得
    public static function get_categories()
    {
        $query = DB::select('category')->from('puzzles')
            ->group_by('category');
        $query->order_by('category', 'asc');
        $result = $query->execute()->as_array('category');
        return array_keys($result);
    }


    // カテゴリごとの獲得スコアを取得
    public static function get_category_point($uid = NULL)
    {
        $query = DB::select(
            'category', DB::expr('SUM(point) + SUM(bonus_point) as point')
        )->from('gained')->where('uid', $uid)->group_by('category');
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
     * get_puzzlue_gained_count
     * 
     * @static
     * @return array(array(puzzle_id => COUNT(puzzle_id)),...)
     */
    public static function get_puzzlue_gained_count()
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
        if (Model_Score::is_first_winner($puzzle_id))
        {
            // 最初の正解者はボーナスポイント加点
            $bonus_point = $puzzle['bonus_point'];
        }
        $newpoint = $totalpoint + $puzzle['point'] + $bonus_point;
        try {
            DB::start_transaction();
            DB::insert('gained')->set(array(
                'uid' => $uid,
                'puzzle_id' => $puzzle_id,
                'point' => $puzzle['point'],
                'bonus_point' => $bonus_point,
                'category' => $puzzle['category'],
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


    // 問題解答時の画像表示
    public static function is_image_active($event = null)
    {
        if ($event == 'success' || $event == 'levelup')
        {
            return Config::get('ctfscore.puzzles.images.is_active_on_success');
        }
        else if ($event == 'failure')
        {
            return Config::get('ctfscore.puzzles.images.is_active_on_failure');
        }
        else
        {
            return false;
        }
    }


    // 正解時に表示するメッセージ
    public static function get_success_messages($puzzle_id = null)
    {
        $messages = array();
        $messages['image_dir'] = '';
        $messages['image_name'] = '';
        $messages['text'] = '';
        if ($puzzle_id == null)
        {
            return $messages;
        }

        // 画像
        $messages['image_dir'] = Model_Puzzle::get_success_image_dir($puzzle_id);
        $messages['image_name'] = Model_Puzzle::get_success_image_name($puzzle_id);

        // テキスト
        $messages['text'] = Model_Puzzle::get_success_text($puzzle_id);

        return $messages;
    }


    public static function get_success_image_dir($puzzle_id = NULL)
    {
        if (!Model_Puzzle::is_image_active('success')) return;

        // 正解時の画像
        $base_path = Config::get('ctfscore.puzzles.path_to_puzzles');
        $res = DB::select()->from('success_image')->where('puzzle_id', $puzzle_id)->execute();
        if (count($res) > 0)
        {
            // 問題ごとの個別指定あり
            $image_dir = Config::get('ctfscore.puzzles.images.success_image_dir');
        }
        else
        {
            // 指定がない場合はランダム画像
            $image_dir = Config::get('ctfscore.puzzles.images.success_random_image_dir');
        }

        return $base_path.'/'.$image_dir;
    }


    public static function get_success_image_name($puzzle_id = NULL)
    {
        if (!Model_Puzzle::is_image_active('success')) return;

        $image_name = '';
        $res = DB::select()->from('success_image')
            ->where('puzzle_id', $puzzle_id)
            ->execute()->as_array();
        // 返すのは1ファイルとしてみる
        if (count($res) > 0)
        {
            $image_name = $res[0]['filename'];
        }
        else
        {
            // 指定がない場合はランダム画像
            $base_path = Config::get('ctfscore.puzzles.path_to_puzzles');
            $image_dir = Config::get('ctfscore.puzzles.images.success_random_image_dir');
            $dir = $base_path.'/'.$image_dir;
            try
            {
                // dir直下のファイルすべて
                $files = File::read_dir($dir, 1, array(
                    '!^\.', // 隠しファイルは除く
                    '!.*' => 'dir', // ディレクトリは除く
                ));
                if (count($files) > 0)
                {
                    $rand = rand() % count($files);
                    $image_name = $files[$rand];
                }
            }
            catch (InvalidPathException $e)
            {
                // 無視する
            }
        }
        return $image_name;
    }


    public static function get_success_text($puzzle_id = NULL)
    {
        $text = '';
        $res = DB::select()->from('success_text')
            ->where('puzzle_id', $puzzle_id)
            ->execute()->as_array();
        if (count($res) > 0)
        {
            // 返すのは１テキストとしてみる
            $text = $res[0]['text'];
        }
        else
        {
            // 指定なしの場合はランダムに
            $res = DB::select()->from('success_random_text')
                ->execute()->as_array();
            if (count($res) > 0)
            {
                $rand = rand() % count($res);
                $text = $res[$rand]['text'];
            }
        }
        return $text;
    }


    // 不正解時に表示するメッセージ
    public static function get_failure_messages()
    {
        $messages = array();
        $messages['image_dir'] = '';
        $messages['image_name'] = '';
        $messages['text'] = '';

        // 画像
        $messages['image_dir'] = Model_Puzzle::get_failure_image_dir();
        $messages['image_name'] = Model_Puzzle::get_failure_image_name();

        // テキスト
        $messages['text'] = Model_Puzzle::get_failure_text();

        return $messages;
    }


    public static function get_failure_image_dir()
    {
        if (!Model_Puzzle::is_image_active('failure')) return;

        // ランダム画像
        $base_path = Config::get('ctfscore.puzzles.path_to_puzzles');
        $image_dir = Config::get('ctfscore.puzzles.images.failure_random_image_dir');
        return $base_path.'/'.$image_dir;
    }


    public static function get_failure_image_name()
    {
        if (!Model_Puzzle::is_image_active('failure')) return;

        $image_name = '';
        // ランダム画像
        $base_path = Config::get('ctfscore.puzzles.path_to_puzzles');
        $image_dir = Config::get('ctfscore.puzzles.images.failure_random_image_dir');
        $dir = $base_path.'/'.$image_dir;
        try
        {
            // dir直下のファイルすべて
            $files = File::read_dir($dir, 1, array(
                '!^\.', // 隠しファイルは除く
                '!.*' => 'dir', // ディレクトリは除く
            ));
            if (count($files) > 0)
            {
                $rand = rand() % count($files);
                $image_name = $files[$rand];
            }
        }
        catch (InvalidPathException $e)
        {
            // 無視する
        }

        return $image_name;
    }


    public static function get_failure_text()
    {
        $text = '';
        // ランダム
        $res = DB::select()->from('failure_random_text')
            ->execute()->as_array();
        if (count($res) > 0)
        {
            $rand = rand() % count($res);
            $text = $res[$rand]['text'];
        }

        return $text;
    }


    public static function get_attachment_dir($puzzle_id = NULL)
    {
        $base_path = Config::get('ctfscore.puzzles.path_to_puzzles');
        $dir = Config::get('ctfscore.puzzles.attachment_dir');
        return $base_path.'/'.$dir;
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
}
