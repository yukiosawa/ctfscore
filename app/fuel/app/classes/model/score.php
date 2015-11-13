<?php

class Model_Score extends Model
{

    // その問題の最初の正解者かどうか
    public static function is_first_winner($puzzle_id = null)
    {
        $result = DB::select()->from('gained')
            ->where('puzzle_id', $puzzle_id)
            ->execute()->as_array();
        if (count($result) > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // スコアボード全体を返す
    public static function get_scoreboard()
    {
        // 全員のスコア一覧 (管理者ユーザは表示しない)
        $admin_group_id = Config::get('ctfscore.admin.admin_group_id');
        $scores = DB::select('id', 'username', 'totalpoint', 'pointupdated_at')
            ->from('users')->where('group', '!=', $admin_group_id)
            ->order_by('totalpoint', 'desc')
            ->order_by('pointupdated_at', 'asc')
            ->execute()->as_array();

        // カテゴリごとのスコアを付加
        $categories = Model_Puzzle::get_categories();
        for ($i = 0; $i < count($scores); $i++)
        {
            $uid = $scores[$i]['id'];
            $category_point = Model_Puzzle::get_category_point($uid);
            foreach ($categories as $category)
            {
                $point = 0;
                if (array_key_exists($category, $category_point))
                {
                    $point = $category_point[$category]['point'];
                }
                $scores[$i] += array($category => $point);
            }
        }

        return $scores;
    }


    // 管理画面へイベントを通知
    public static function emitToMgmtConsole($event = NULL, $msg = NULL)
    {
        if (!Config::get('ctfscore.admin.management_console'))
        {
            return;
        }
        require(DOCROOT.'../nodejs/socket.io-php-emitter/vendor/autoload.php');
        require(DOCROOT.'../nodejs/socket.io-php-emitter/src/Emitter.php');
        // Below initialization will create a  phpredis client, or a TinyRedisClient depending on what is installed
        $emitter = new SocketIO\Emitter(array('port' => '6379', 'host' => '127.0.0.1'));

        // broadcast can be replaced by any of the other flags
        /* $emitter->broadcast->emit('php', 'TEST from PHP'); */
        $emitter->emit($event, $msg);
    }


    // グラフ描画用データを返す
    public static function get_ranking_chart()
    {
        // 横軸の項目
        // 開始と終了時刻
        $times = DB::select()->from('times')->execute()->as_array();
        if (count($times) < 1)
        {
            return;
        }
        $start_time = $times[0]['start_time'];
        $end_time = $times[0]['end_time'];
        // プロット間隔(秒)
        $interval_seconds = Config::get('ctfscore.chart.plot_interval_seconds');
        // 最大プロット数
        $max_steps = Config::get('ctfscore.chart.plot_max_steps');

        $labels = array();
        $now = Model_Score::get_current_time();
        $label = $start_time;
        // 開始時刻からプロット間隔で時刻を取得して横軸とする
        for ($i = 0; $i < $max_steps; $i++)
        {
            $labels[] = $label;
            $added_time = Model_Score::get_mod_time($label, 'add', $interval_seconds);
            $label = $added_time;

            //現在時刻 or 終了時刻まで
            $end = '';
            if (strtotime($now) < strtotime($end_time))
            {
                $end = $now;
            }
            else
            {
                $end = $end_time;
            }
            if (strtotime($label) > strtotime($end))
            {
                $labels[] = $end;
                break;
            }
        }
        $result['labels'] = $labels;

        // 上位のユーザだけ対象とする。また0点は対象外とする。
        // 管理者も対象外
        $max_number = Config::get('ctfscore.chart.max_number_of_users');
        $admin_group_id = Config::get('ctfscore.admin.admin_group_id');
        $users = DB::select('id', 'username')
            ->from('users')
            ->where('totalpoint', '>', 0)
            ->where('group', '!=', $admin_group_id)
            ->order_by('totalpoint', 'desc')
            ->limit($max_number)
            ->order_by('pointupdated_at', 'asc')
            ->execute()->as_array('id');
        if (count($users) < 1)
        {
            return;
        }

        // ユーザ名一覧とグラフの色
        $userlist = array();
        $colors = Config::get('ctfscore.chart.colors');
        $cnt = 0;
        foreach ($users as $user)
        {
            // 上位ユーザから順に色を割り当て
            if (count($colors) < $cnt + 1)
            {
                break;
            }
            $userlist += array($user['username'] => $colors[$cnt]);
            $cnt++;
        }

        // 各ユーザの獲得済み総スコア履歴
        $gained = DB::select('username', 'gained_at', 'gained.totalpoint')
            ->from('gained')
            ->where('uid', 'IN', array_keys($users))
            ->join('users', 'LEFT')
            ->on('gained.uid', '=', 'users.id')
            ->execute()->as_array();

        $result['userlist'] = $userlist;
        $result['pointlist'] = $gained;
        return $result;
    }


    // 個人プロファイル(カテゴリごとの獲得点数)を返す
    public static function get_profile_answered_category($username = NULL)
    {
        $userid = '';
        if (!$username)
        {
            // 指定されない場合はログイン中のユーザIDとする
            list($driver, $userid) = Auth::get_user_id();
            $username = Auth::get_screen_name();
        }
        else
        {
            $userid = Model_Score::get_uid($username);
        }
        if (!$userid) return;

        $puzzles = Model_Puzzle::get_puzzles_addinfo($userid);

        // カテゴリごとに獲得スコア／総スコアを算出
        $categories = array();
        foreach ($puzzles as $puzzle)
        {
            $category = $puzzle['category'];
            $point = $puzzle['point'];
            if (!array_key_exists($category, $categories))
            {
                $categories[$category]['totalpoint'] = 0;
                $categories[$category]['point'] = 0;
            }
            $categories[$category]['totalpoint'] += $point;
            if ($puzzle['answered'])
            {
                $categories[$category]['point'] += $point;
            }
        }
        $result['username'] = $username;
        $result['categories'] = $categories;

        return $result;
    }


    // 個人プロファイルを返す
    public static function get_profile($username = NULL)
    {
        $userid = '';
        if (!$username)
        {
            // 指定されない場合はログイン中のユーザIDとする
            list($driver, $userid) = Auth::get_user_id();
            $username = Auth::get_screen_name();
        }
        else
        {
            $userid = Model_Score::get_uid($username);
        }
        if (!$userid) return;

        $answered = Model_Puzzle::get_answered_puzzles($userid);
        $result['username'] = $username;
        $result['answered_puzzles'] = $answered;
        $result['reviews'] = Model_Review::get_reviews(null, null, $userid);
        $result['levels'] = Model_Score::get_current_levels_name($userid);
        return $result;
    }


    // usernameをuseridに変換する
    public static function get_uid($username = NULL)
    {
        if (!$username) return;

        $result = DB::select('id')->from('users')
            ->where('username', $username)
            ->execute()->as_array();
        if (count($result) > 0)
        {
            return $result[0]['id'];
        }
        else
        {
            return;
        }
    }


    // 現在のCTF実施状況を返す(開始前、実施中、終了)
    public static function get_ctf_time_status()
    {
        $status = array(
            'start_time' => '',
            'end_time' => '',
            'before' => false,
            'ended' => false,
            'running' => false,
            'no_use' => false,
        );

        $times = DB::select()->from('times')->execute()->as_array();
        // CTF時間設定なしの場合は常時実施中とする
        if (count($times) < 1)
        {
            $status['no_use'] = true;
            $status['start_time'] = 'N/A';
            $status['end_time'] = 'N/A';
            return $status;
        }

        // 開始時刻
        $status['start_time'] = $times[0]['start_time'];
        $start_unix_time = strtotime($status['start_time']);
        // 終了時刻
        $status['end_time'] = $times[0]['end_time'];
        $end_unix_time = strtotime($status['end_time']);
        // 現在時刻
        $now_unix = strtotime(Model_Score::get_current_time());

        if ($now_unix < $start_unix_time)
        {
            $status['before'] = true;
        }
        else if ($now_unix < $end_unix_time)
        {
            $status['running'] = true;
        }
        else
        {
            $status['ended'] = true;
        }
        return $status;
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
        $val = Validation::forge($factory);

        if (($factory == 'login') || ($factory == 'create'))
        {
            $val->add('username', 'ユーザー名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                ->add_rule('valid_string',
                    array(
                        'alpha',
                        'numeric',
                        'punctuation',
                        'dashes',
                        'quotes',
                        'brackets',
                        'braces',
                        'utf8'
                    ));
            $val->add('password', 'パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20);
        }
        else if ($factory == 'update')
        {
            $val->add('password', '新パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20);
            $val->add('old_password', '旧パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20);
        }
        else if ($factory == 'score_submit')
        {
            $val->add('answer', 'flag')
                ->add_rule('required')
                ->add_rule('max_length', 255);
        }

        return $val;
    }


    // 回答試行数制限を超過しているかどうかを返す
    public static function is_over_attempt_limit($uid = NULL)
    {
        $interval_seconds = Config::get('ctfscore.history.attempt_interval_seconds');
        $limit_times = Config::get('ctfscore.history.attempt_limit_times');
        $now = Model_Score::get_current_time();
        $subed_time = Model_Score::get_mod_time($now, 'sub', $interval_seconds);

        $query = DB::select()->from('history');
        $query->where('uid', '=', $uid);
        $query->where('posted_at', '>', $subed_time);
        // 正解時のポストは除外しておく
        $query->where('result', '!=', 'success');
        $query->where('result', '!=', 'levelup');
        $result = $query->execute();

        if (count($result) >= $limit_times)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    // 試行履歴を記録する
    public static function set_attempt_history($uid = NULL, $answer = NULL, $type = NULL)
    {
        // DB更新
        $now = Model_Score::get_current_time();
        try
        {
            DB::start_transaction();
            DB::insert('history')->set(array(
                'uid' => $uid,
                'posted_at' => $now,
                'posted_answer' => $answer,
                'result' => $type
            ))->execute();
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            /* ロールバック */
            DB::rollback_transaction();
            throw $e;
        }
    }


    // 現在時刻を返す
    public static function get_current_time()
    {
        // DBの時刻を基準とする
        // MySQL DATETIME型 "YYYY-MM-DD hh:mm:ss"
        $query = DB::select(DB::expr("NOW()"));
        $result = $query->execute()->as_array();
        list($key, $val) = each($result[0]);
        return $val;
    }


    // 時刻を加減算して返す
    public static function get_mod_time($time = NULL, $type = NULL, $interval_seconds = 0)
    {
        if ($time == NULL)
        {
            return NULL;
        }

        // DBの時刻を基準とする
        // MySQL DATETIME型 "YYYY-MM-DD hh:mm:ss"
        if ($type == 'add')
        {
            // インターバル秒数を加算
            $query = DB::select(DB::expr(
                "'".$time."' + INTERVAL ".$interval_seconds." SECOND"));
        }
        elseif ($type == 'sub')
        {
            // インターバル秒数を減算
            $query = DB::select(DB::expr(
                "'".$time."' - INTERVAL ".$interval_seconds." SECOND"));
        }
        else
        {
            return NULL;
        }

        $result = $query->execute()->as_array();
        list($key, $val) = each($result[0]);
        return $val;
    }


    // 回答済問題数に応じてレベルを更新する
    public static function set_level_gained($uid = NULL)
    {
        $updated_levels = array();
        // 現在のレベル
        $current_levels = Model_Score::get_current_levels($uid);

        // 回答済の問題数から得られるレベル
        $expected_levels = Model_Score::get_expected_levels($uid);

        if (count($expected_levels) < 1)
        {
            return null;
        }

        // カテゴリごとにレベルチェック
        foreach ($expected_levels as $category => $expected)
        {
            if (!array_key_exists($category, $current_levels))
            {
                $current_levels[$category] = 0;
            }
            if ($current_levels[$category] < $expected)
            {
                // レベルアップ
                if ($level = Model_Score::get_levels($category, $expected))
                {
                    $name = $level[0]['name'];
                    Model_Score::update_gained_levels_table($uid, $category, $expected);
                    $updated_levels[$category] = $name;
                }
            }
        }

        return $updated_levels;
    }


    // 現在のレベルを取得する
    public static function get_current_levels($uid = NULL)
    {
        $levels = array();
        $category_levels = array();
        $result = DB::select()->from('gained_levels')
            ->where('uid', $uid)
            ->where('is_current', true)
            ->order_by('category', 'asc')
            ->execute()->as_array();
        $total_dummy = Config::get('ctfscore.level.dummy_name_total');
        $conf_total = Config::get('ctfscore.level.is_active_total_level');
        $conf_cate = Config::get('ctfscore.level.is_active_category_level');
        foreach ($result as $row)
        {
            $category = $row['category'];
            if ($category == $total_dummy && $conf_total)
            {
                $levels[$category] = $row['level'];
            }
            else if ($category != $total_dummy && $conf_cate)
            {
                $category_levels[$category] = $row['level'];
            }
        }
        $levels += $category_levels;
        return $levels;
    }


    // 現在のレベルを名称で取得する
    public static function get_current_levels_name($uid)
    {
        $levels = Model_Score::get_current_levels($uid);
        $names = array();
        $category_names = array();
        $name = '';
        $total_dummy = Config::get('ctfscore.level.dummy_name_total');
        foreach ($levels as $category => $level)
        {
            if($level = Model_Score::get_levels($category, $level))
            {
                $name = $level[0]['name'];
            }
            else
            {
                continue;
            }
            if ($category == $total_dummy)
            {
                // 全体のレベルが配列の先頭にくるようにする
                $names[$category] = $name;
            }
            else
            {
                $category_names[$category] = $name;
            }
        }
        $names += $category_names;
        return $names;
    }


    // 獲得済問題数からどのレベルに相当するかを算定する
    public static function get_expected_levels($uid = NULL)
    {
        $levels = array();
        // 獲得済の問題数
        $solved_num = array();
        $puzzles = Model_Puzzle::get_answered_puzzles($uid);
        foreach ($puzzles as $puzzle)
        {
            $category = $puzzle['category'];
            if (array_key_exists($category, $solved_num))
            {
                $solved_num[$category] += 1;
            }
            else
            {
                $solved_num[$category] = 1;
            }
        }
        // カテゴリ名でソートしておく
        ksort($solved_num);

        // 全体レベル
        $total_dummy = Config::get('ctfscore.level.dummy_name_total');
        if (Config::get('ctfscore.level.is_active_total_level'))
        {
            $total_level = DB::select(DB::expr('MAX(level)'))
                ->from('levels')
                ->where('category', $total_dummy)
                ->where('criteria', '<=', count($puzzles))
                ->execute()->as_array();
            $levels[$total_dummy] = $total_level[0]['MAX(level)'];
        }

        // カテゴリごと
        if (Config::get('ctfscore.level.is_active_category_level'))
        {
            foreach ($solved_num as $category => $num)
            {
                $category_level = DB::select(DB::expr('MAX(level)'))
                    ->from('levels')
                    ->where('category', '!=', $total_dummy)
                    ->where('criteria', '<=', $num)
                    ->execute()->as_array();
                $levels[$category] = $category_level[0]['MAX(level)'];
            }
        }

        return $levels;
    }


    // 獲得済レベルのテーブル更新
    public static function update_gained_levels_table($uid = NULL, $category = NULL, $level = NULL)
    {
        if ($uid == NULL || $category == NULL || $level == NULL)
        {
            return;
        }
        $total_dummy = Config::get('ctfscore.level.dummy_name_total');
        $conf_total = Config::get('ctfscore.level.is_active_total_level');
        $conf_cate = Config::get('ctfscore.level.is_active_category_level');
        if (($category == $total_dummy && !$conf_total)
            || ($category != $total_dummy && !$conf_cate))
        {
            return;
        }

        $now = Model_Score::get_current_time();
        try {
            DB::start_transaction();
            DB::update('gained_levels')->set(array(
                'is_current' => false,
            ))->where('uid', $uid)->where('category', $category)
            ->where('is_current', true)->execute();
            DB::insert('gained_levels')->set(array(
                'uid' => $uid,
                'category' => $category,
                'level' => $level,
                'gained_at' => $now,
                'is_current' => true
            ))->execute();
            DB::commit_transaction();
        } catch (Exception $e) {
            //ロールバック
            DB::rollback_transaction();
            throw $e;
        }
    }


    // レベルのテーブルを返す
    public static function get_levels($category = NULL, $level = NULL)
    {
        $query = DB::select()->from('levels');
        if (!is_null($category))
        {
            $query->where('category', $category);
        }
        if (!is_null($level))
        {
            $query->where('level', $level);
        }
        $result = $query->execute()->as_array();
        return $result;
    }

}
