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

    public static function is_complete($userid)
    {
        $puzzle_count = count(DB::select('puzzle_id')->from('puzzles')->group_by('puzzle_id')->execute());

        $gained_count = count(DB::select('puzzle_id')->from('gained')->where('uid', $userid)->group_by('puzzle_id')->execute());

        return $gained_count >= $puzzle_count;
        /* $puzzle = DB::select(DB::expr('SUM(point)'))->from('puzzles')
           ->execute()->as_array();
           $puzzle_point = $puzzle[0]['SUM(point)'];

           $gained = DB::select(DB::expr('SUM(point)'))->from('gained')
           ->where('uid', $userid)
           ->execute()->as_array();
           $gained_point = $gained[0]['SUM(point)'];

           return $gained_point >= $puzzle_point; */
    }

    // スコアボード全体を返す
    public static function get_scoreboard()
    {
        // 全員のスコア一覧 (管理者ユーザは表示しない)
        $admin_group_id = Model_Config::get_value('admin_group_id');
        $total_category_id = Model_Config::get_value('total_category_id');
        $scores = DB::select('users.id', 'users.username', 'users.totalpoint', 'levels.name')
            ->from('users')
            ->join('gained_levels', 'LEFT')
            ->on('users.id', '=', 'gained_levels.uid')
            ->on('gained_levels.category_id', '=', $total_category_id)
            ->on('gained_levels.is_current', '=', "'1'")
            ->join('levels', 'LEFT')
            ->on('gained_levels.category_id', '=', 'levels.category_id')
            ->on('gained_levels.level', '=', 'levels.level')
            ->join('categories', 'LEFT')
            ->on('gained_levels.category_id', '=', 'categories.id')
            ->where('users.group', '!=', $admin_group_id)
            ->order_by('users.totalpoint', 'desc')
            ->order_by('users.pointupdated_at', 'asc')
            ->execute()->as_array();

        // カテゴリごとのスコアを付加
        $categories = array_map(function ($var) { return $var['category']; }, Model_Category::get_categories());
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
        if (Model_Config::get_value('is_active_management_console') == 0)
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
        $interval_seconds = Model_Config::get_value('plot_interval_seconds');
        // 最大プロット数
        $max_steps = Model_Config::get_value('plot_max_steps');

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
        $colors = Model_Config::get_config_chart_colors();
        $max_number = count($colors);
        $admin_group_id = Model_Config::get_value('admin_group_id');
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
        $cnt = 0;
        foreach ($users as $user)
        {
            // 上位ユーザから順に色を割り当て
            if (count($colors) < $cnt + 1)
            {
                break;
            }
            $userlist += array($user['username'] => $colors[$cnt]['color']);
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


    /**
     * get_ranking_user
     * 
     * @static
     * @return void
     */
    public static function get_score_ranking($username)
    {
        $user = DB::select(DB::expr('(select count(*) + 1 from users as t where t.totalpoint > users.totalpoint or (t.totalpoint = users.totalpoint and t.pointupdated_at < users.pointupdated_at)) as rank'))
            ->from('users')
            ->where('username', $username)
            ->execute()->as_array();
        return $user[0]['rank'];
    }


    // 個人プロファイル(カテゴリごとの獲得点数)を返す
    public static function get_profile_progress($usernames = NULL)
    {
        $result = array();
        if (!$usernames)
        {
            // 指定されない場合はログイン中のユーザとする
            $usernames[0] = Auth::get_screen_name();
        }

        $colors = Model_Config::get_config_chart_colors();

        foreach ($usernames as $username)
        {
            $userid = Model_Score::get_uid($username);
            if (!$userid)
            {
                continue;
            }
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

            $result[] = array('username' => $username, 'categories' => $categories, 'color' => array_shift($colors)['color']);
        }

        return $result;
    }


    // 個人プロファイルを返す
    public static function get_profile_detail($usernames = NULL)
    {
        $result = array();
        if (!$usernames)
        {
            // 指定されない場合はログイン中のユーザとする
            $username = Auth::get_screen_name();
        }

        foreach ($usernames as $username)
        {
            $userid = Model_Score::get_uid($username);
            if (!$userid)
            {
                continue;
            }
            $answered = Model_Puzzle::get_answered_puzzles($userid);
            $result[] = array(
                'username' => $username,
                'answered_puzzles' => $answered,
                'reviews' => Model_Review::get_reviews(null, null, $userid),
                'levels' => Model_Score::get_current_levels_name($userid)
            );
        }

        return $result;
    }


    public function get_solved_status_chart()
    {
        // x=カテゴリ名, y=問題得点, r=回答者数
        $data = array();
        // 回答済の問題
        $gained = DB::select()->from('gained')
                              ->join('puzzles')
                              ->on('gained.puzzle_id', '=', 'puzzles.puzzle_id')
                              ->execute()->as_array();
        // 問題設定されているカテゴリを列挙
        $categories = DB::select('categories.*')
            ->from('categories')
            ->join('puzzles')
            ->on('puzzles.category_id', '=', 'categories.id')
            ->order_by('categories.id', 'asc')
            ->execute()->as_array();

        // 問題設定されている得点を列挙
        $points = Model_Puzzle::get_points();

        $colors = Model_Config::get_config_chart_colors();
        foreach ($categories as $category)
        {
            foreach ($points as $point)
            {
                $solved = array_filter($gained, function ($var) use ($category, $point) {return $var['category_id'] == $category['id'] && $var['point'] == $point;});
                $data[] = array(
                    'category_id' => $category['id'],
                    'category' => $category['category'],
                    'point' => $point,
                    'solved' => count($solved),
                );
            }
        }
        return array(
            'data' => $data,
            'categories' => array_map(function ($var) {return $var['category'];}, $categories),
            'points' => $points,
            'multiple_by' => Model_Config::get_value('bubble_size_multiple_by'),
            'color' => Model_Config::get_value('bubble_color')
        );
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

        if ($factory == 'login')
        {
            $val->add('username', 'ユーザー名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                // 英数字
                ->add_rule('match_pattern', '/^[a-zA-Z0-9]+$/');
            $val->add('password', 'パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20)
                // 半角文字全て(英数字、記号) !(0x21) - ~(0x7e)
                ->add_rule('match_pattern', '/^[!-~]+$/');
        }
        else if ($factory == 'create')
        {
            $val->add('username', 'ユーザ名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                // 英数字
                ->add_rule('match_pattern', '/^[a-zA-Z0-9]+$/');
            $val->add('password', 'パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20)
                // 半角文字全て(英数字、記号) !(0x21) - ~(0x7e)
                ->add_rule('match_pattern', '/^[!-~]+$/');
            $val->add('password-confirm', 'パスワード確認')
                ->add_rule('match_field', 'password');
        }
        else if ($factory == 'update')
        {
            $val->add('password', '新パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20)
                // 半角文字全て(英数字、記号) !(0x21) - ~(0x7e)
                ->add_rule('match_pattern', '/^[!-~]+$/');
            $val->add('old_password', '旧パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4)
                ->add_rule('max_length', 20)
                // 半角文字全て(英数字、記号) !(0x21) - ~(0x7e)
                ->add_rule('match_pattern', '/^[!-~]+$/');
        }
        else if ($factory == 'score_submit')
        {
            $val->add('puzzle_id', '問題番号')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
            $val->add('answer', 'flag')
                ->add_rule('required')
                ->add_rule('max_length', 255);
        }

        return $val;
    }


    // 回答試行数制限を超過しているかどうかを返す
    public static function is_over_attempt_limit($uid = NULL)
    {
        $interval_seconds = Model_Config::get_value('submit_interval_seconds');
        $limit_times = Model_Config::get_value('submit_limit_times');
        $now = Model_Score::get_current_time();
        $subed_time = Model_Score::get_mod_time($now, 'sub', $interval_seconds);
        $success_event = Config::get('ctfscore.answer_result.success.event');

        // 正解時のポストは除外しておく
        $result = DB::select()->from('history')
                              ->where('uid', '=', $uid)
                              ->where('submitted_at', '>', $subed_time)
                              ->where('result_event', '!=', $success_event)
                              ->execute();

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
    public static function set_attempt_history($uid = NULL, $puzzle_id = NULL, $answer = NULL, $result = NULL)
    {
        // DB更新
        $now = Model_Score::get_current_time();
        try
        {
            DB::start_transaction();
            DB::insert('history')->set(array(
                'uid' => $uid,
                'submitted_at' => $now,
                'puzzle_id' => $puzzle_id,
                'answer' => $answer,
                'result_event' => $result['event'],
                'result_description' => $result['description']
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


    // CTF終了時刻 unixtime
    public static function get_ctf_end_time()
    {
        $times = DB::select()->from('times')->execute()->as_array();
        if (count($times) < 1)
        {
            return null;
        }
        return strtotime($times[0]['end_time']);
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
        foreach ($expected_levels as $category_id => $expected)
        {
            if (!array_key_exists($category_id, $current_levels))
            {
                $current_levels[$category_id] = 0;
            }
            if ($current_levels[$category_id] < $expected)
            {
                // レベルアップ
                if ($level = Model_Score::get_levels($category_id, $expected))
                {
                    $name = $level[0]['name'];
                    Model_Score::update_gained_levels_table($uid, $category_id, $expected);
                    $updated_levels[$category_id] = $name;
                }
            }
        }

        return $updated_levels;
    }


    // 現在のレベルを取得する
    public static function get_current_levels($uid = NULL, $byname = false)
    {
        $levels = array();
        if (Model_Config::get_value('is_active_level') == 0)
        {
            return $levels;
        }
        $category_levels = array();
        $result = DB::select('gained_levels.*', 'levels.name')->from('gained_levels')
            ->join('levels')
            ->on('gained_levels.level', '=', 'levels.level')
            ->where('gained_levels.uid', $uid)
            ->where('gained_levels.is_current', true)
            ->order_by('gained_levels.category_id', 'asc')
            ->execute()->as_array();
        $total_category_id = Model_Config::get_value('total_category_id');
        foreach ($result as $row)
        {
            $category_id = $row['category_id'];
            if ($category_id == $total_category_id)
            {
                if ($byname)
                {
                    $levels[$category_id] = $row['name'];
                }
                else
                {
                    $levels[$category_id] = $row['level'];
                }
            }
            else
            {
                if ($byname)
                {
                    $category_levels[$category_id] = $row['name'];
                }
                else
                {
                    $category_levels[$category_id] = $row['level'];
                }
            }
        }
        $levels += $category_levels;
        return $levels;
    }


    // 現在のレベルを名称で取得する
    public static function get_current_levels_name($uid)
    {
        Model_Score::get_current_levels($uid, true);
    }


    // 獲得済問題数からどのレベルに相当するかを算定する
    public static function get_expected_levels($uid = NULL)
    {
        $levels = array();
        if (Model_Config::get_value('is_active_level') == 0)
        {
            return $levels;
        }
        
        // 獲得済の問題数
        $solved_num = array();
        $puzzles = Model_Puzzle::get_answered_puzzles($uid);
        foreach ($puzzles as $puzzle)
        {
            $category_id = $puzzle['category_id'];
            if (array_key_exists($category_id, $solved_num))
            {
                $solved_num[$category_id] += 1;
            }
            else
            {
                $solved_num[$category_id] = 1;
            }
        }
        // カテゴリ名でソートしておく
        ksort($solved_num);

        // 全体レベル
        $total_category_id = Model_Config::get_value('total_category_id');
        $total_level = DB::select(DB::expr('MAX(level)'))
                ->from('levels')
                ->where('category_id', $total_category_id)
                ->where('criteria', '<=', count($puzzles))
                ->execute()->as_array();
        if ($total_level[0]['MAX(level)'])
        {
            $levels[$total_category_id] = $total_level[0]['MAX(level)'];
        }

        // カテゴリごと
        foreach ($solved_num as $category_id => $num)
        {
            $category_level = DB::select(DB::expr('MAX(level)'))
                    ->from('levels')
                    ->where('category_id', $category_id)
                    ->where('criteria', '<=', $num)
                    ->execute()->as_array();
            if ($category_level[0]['MAX(level)'])
            {
                $levels[$category_id] = $category_level[0]['MAX(level)'];
            }
        }
        
        return $levels;
    }


    // 獲得済レベルのテーブル更新
    public static function update_gained_levels_table($uid = NULL, $category_id = NULL, $level = NULL)
    {
        if ($uid == NULL || $category_id == NULL || $level == NULL)
        {
            return;
        }
        if (Model_Config::get_value('is_active_level') == 0)
        {
            return;
        }

        $now = Model_Score::get_current_time();
        try {
            DB::start_transaction();
            DB::update('gained_levels')->set(array(
                'is_current' => false,
            ))->where('uid', $uid)->where('category_id', $category_id)
            ->where('is_current', true)->execute();
            DB::insert('gained_levels')->set(array(
                'uid' => $uid,
                'category_id' => $category_id,
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
    public static function get_levels($category_id = NULL, $level = NULL)
    {
        $query = DB::select()->from('levels');
        if (!is_null($category_id))
        {
            $query->where('category_id', $category_id);
        }
        if (!is_null($level))
        {
            $query->where('level', $level);
        }
        $result = $query->execute()->as_array();
        return $result;
    }


    public static function get_total_levels()
    {
        return Model_Score::get_levels(Model_Config::get_value('total_category_id'));
    }


    public static function get_category_levels($category_id)
    {
        if ($category_id)
        {
            return Model_Score::get_levels($category_id);
        }
        else
        {
            $total_category_id = Model_Config::get_value('total_category_id');
            return DB::select()->from('levels')
                ->where('category_id', '!=', $total_category_id)
                ->execute()->as_array();
        }
    }


    // 現在のレベルテーブルで獲得済レベルを再設定する
    public static function refresh_gained_levels()
    {
        // 全ての獲得済レベルをクリアする
        DB::update('gained_levels')->value('is_current', false)
            ->where('is_current', true)
            ->execute();
        // ユーザごとにレベルを再計算
        $users = DB::select()->from('users')->execute()->as_array();
        foreach ($users as $user)
        {
            $uid = $user['id'];
            $username = $user['username'];
            $updated_levels = Model_Score::set_level_gained($uid);
        }
    }
    

    public static function update_levels($category_ids, $levels, $names, $criteria)
    {
        // POSTされていないデータは削除対象とする
        $levels_before = DB::select()->from('levels')->execute()->as_array();
        $levels_update = array();

        $table_name = 'levels';
        try
        {
            DB::start_transaction();
            for ($i=0; $i<count($category_ids); $i++)
            {
                $levels_update[$category_ids[$i].$levels[$i]] = array('name' => $names[$i], 'criteria' => $criteria[$i]);
                
                if ($category_ids[$i] == '' || $levels[$i] == '' || $names[$i] == '' || $criteria[$i] == '')
                {
                    continue;
                }

                // 既に登録があれば更新、なければ新規
                if (count(DB::select()->from($table_name)->where('category_id', $category_ids[$i])->where('level', $levels[$i])->execute()) < 1)
                {
                    DB::insert($table_name)->set(array(
                        'category_id' => $category_ids[$i],
                        'level' => $levels[$i],
                        'name' => $names[$i],
                        'criteria' => $criteria[$i]
                    ))->execute();
                }
                else
                {
                    DB::update($table_name)->where('category_id', $category_ids[$i])->where('level', $levels[$i])->set(array(
                        'category_id' => $category_ids[$i],
                        'level' => $levels[$i],
                        'name' => $names[$i],
                        'criteria' => $criteria[$i]
                    ))->execute();
                }
            }

            $levels_delete = array_filter($levels_before, function ($var) use ($levels_update) {return !array_key_exists($var['category_id'].$var['level'], $levels_update); });
            foreach ($levels_delete as $del)
            {
                DB::delete($table_name)->where('category_id', $del['category_id'])->where('level', $del['level'])->execute();
            }
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'レベルの更新に失敗しました。: '.$type);
        }

        return array('bool' => true);

    }
}
