<?php

namespace Fuel\Tasks;

class Ctfscore
{

    public function create_database($args = NULL)
    {
        $this->_do_database("create");
    }


    public function delete_database($args = NULL)
    {
        $this->_do_database("delete");
    }


    private function _do_database($act = NULL)
    {
        /* read config parameters from db.php */
        \Config::load('db', true);
        $active = \Config::get('db.active');
        if (! $database = \Config::get('db.'.$active.'.connection.database'))
        {
            echo "Failed to get a database name from db.php file.\n";
            return;
        }

        /* error occurs when the database doesn't exist,
        so clear the database name in the config */
        \Config::set('db.'.$active.'.connection.database', '');

        if ($act == "create") {
            \DBUtil::create_database($database);
            echo "Database created: ".$database."\n";
        }
        elseif ($act == "delete") {
            \DBUtil::drop_database($database);
            echo "Database deleted: ".$database."\n";
        }
    }


    public function init_all_tables()
    {
        /* delete all tables */
        $this->delete_admin_bonus_point_table();
        $this->delete_gained_levels_table();
        $this->delete_levels_table();
        $this->delete_reviews_table();
        $this->delete_hints_table();
        $this->delete_gained_table();
        $this->delete_history_table();
        $this->delete_news_table();
        $this->delete_users_table();
        $this->delete_attachment_table();
        $this->delete_success_image_table();
        $this->delete_success_text_table();
        $this->delete_success_random_text_table();
        $this->delete_failure_random_text_table();
        $this->delete_flags_table();
        $this->delete_puzzles_table();
        $this->delete_times_table();
        /* create all tables */
        $this->create_users_table();
        $this->create_puzzles_table();
        $this->create_hint_table();
        $this->create_flags_table();
        $this->create_attachment_table();
        $this->create_success_image_table();
        $this->create_success_text_table();
        $this->create_success_random_text_table();
        $this->create_failure_random_text_table();
        $this->create_times_table();
        $this->create_gained_table();
        $this->create_history_table();
        $this->create_reviews_table();
        $this->create_hints_table();
        $this->create_levels_table();
        $this->create_gained_levels_table();
        $this->create_news_table();
        $this->create_admin_bonus_point_table();
    }


    public function create_users_table()
    {
        // get the tablename
        \Config::load('simpleauth', true);
        $table = \Config::get('simpleauth.table_name', 'users');

        // make sure the configured DB is used
        \DBUtil::set_connection(\Config::get('simpleauth.db_connection', null));

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        // table users
        \DBUtil::create_table($table, array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'username' => array('type' => 'varchar', 'constraint' => 50),
            'password' => array('type' => 'varchar', 'constraint' => 255),
            'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
            'email' => array('type' => 'varchar', 'constraint' => 255),
            'last_login' => array('type' => 'varchar', 'constraint' => 25),
            'login_hash' => array('type' => 'varchar', 'constraint' => 255),
            'profile_fields' => array('type' => 'text'),
            'already_news_id' => array('type' => 'id', 'default' => 0),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            'totalpoint' => array('type' => 'int', 'default' => 0),
            'pointupdated_at' => array('type' => 'datetime'),
        ), array('id'));

        // reset any DBUtil connection set
        \DBUtil::set_connection(null);

        echo "Table created: ".$table."\n";
    }


    public function delete_users_table()
    {
        // get the tablename
        \Config::load('simpleauth', true);
        $table = \Config::get('simpleauth.table_name', 'users');

        // make sure the configured DB is used
        \DBUtil::set_connection(\Config::get('simpleauth.db_connection', null));

        // drop the admin_users table
        \DBUtil::drop_table($table);

        // reset any DBUtil connection set
        \DBUtil::set_connection(null);

        echo "Table deleted: ".$table."\n";
    }


    public function create_puzzles_table()
    {
        $table = 'puzzles';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'puzzle_id' => array('type' => 'int'),
                'point' => array('type' => 'int'),
                'bonus_point' => array('type' => 'int'),
                'category' => array('type' => 'varchar', 'constraint' => 255),
                'title' => array('type' => 'varchar', 'constraint' => 255),
                'content' => array('type' => 'varchar', 'constraint' => 1000),
            ),
            /* primary_keys */
            array('puzzle_id')
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_puzzles_table()
    {
        $table = 'puzzles';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_flags_table()
    {
        $table = 'flags';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'puzzle_id' => array('type' => 'int'),
                'flag' => array('type' => 'varchar', 'constraint' => 255, 'charset' => 'binary'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'puzzle_id',
                    'reference' => array(
                        'table' => 'puzzles',
                        'column' => 'puzzle_id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                )
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_flags_table()
    {
        $table = 'flags';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_attachment_table()
    {
        $table = 'attachment';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields  */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'puzzle_id' => array('type' => 'int'),
                'filename' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'puzzle_id',
                    'reference' => array(
                        'table' => 'puzzles',
                        'column' => 'puzzle_id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_attachment_table()
    {
        $table = 'attachment';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_success_image_table()
    {
        $table = 'success_image';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'puzzle_id' => array('type' => 'int'),
                'filename' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'puzzle_id',
                    'reference' => array(
                        'table' => 'puzzles',
                        'column' => 'puzzle_id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_success_image_table()
    {
        $table = 'success_image';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_success_text_table()
    {
        $table = 'success_text';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'puzzle_id' => array('type' => 'int'),
                'text' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'puzzle_id',
                    'reference' => array(
                        'table' => 'puzzles',
                        'column' => 'puzzle_id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_success_text_table()
    {
        $table = 'success_text';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_success_random_text_table()
    {
        $table = 'success_random_text';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'text' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_success_random_text_table()
    {
        $table = 'success_random_text';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_failure_random_text_table()
    {
        $table = 'failure_random_text';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'auto_increment' => true),
                'text' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_failure_random_text_table()
    {
        $table = 'failure_random_text';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_gained_table()
    {
        $table = 'gained';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'uid' => array('type' => 'int'),
                'puzzle_id' => array('type' => 'int'),
                'point' => array('type' => 'int'),
                'bonus_point' => array('type' => 'int'),
                'category' => array('type' => 'varchar', 'constraint' => 255),
                'totalpoint' => array('type' => 'int'),
                'gained_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('uid', 'puzzle_id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_gained_table()
    {
        $table = 'gained';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_gained_levels_table()
    {
        $table = 'gained_levels';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                'uid' => array('type' => 'int'),
                'category' => array('type' => 'varchar', 'constraint' => 255),
                'level' => array('type' => 'int'),
                'gained_at' => array('type' => 'datetime'),
                'is_current' => array('type' => 'int'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_gained_levels_table()
    {
        $table = 'gained_levels';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_levels_table()
    {
        $table = 'levels';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'category' => array('type' => 'varchar', 'constraint' => 255),
                'level' => array('type' => 'int'),
                'name' => array('type' => 'varchar', 'constraint' => 50),
                'criteria' => array('type' => 'int'),
            ),
            /* primary_keys */
            array('category', 'level')
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_levels_table()
    {
        $table = 'levels';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_times_table()
    {
        $table = 'times';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table($table, array(
            'start_time' => array('type' => 'datetime'),
            'end_time' => array('type' => 'datetime'),
        ));

        echo "Table created: ".$table."\n";
    }


    public function delete_times_table()
    {
        $table = 'times';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_history_table()
    {
        $table = 'history';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'uid' => array('type' => 'int', 'constraint' => 11),
                'posted_at' => array('type' => 'datetime'),
                'posted_answer' => array('type' => 'varchar', 'constraint' => 255),
                'result' => array('type' => 'varchar', 'constraint' => 10)
            ),
            /* primary_keys */
            array('uid', 'posted_at'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_history_table()
    {
        $table = 'history';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_reviews_table()
    {
        $table = 'reviews';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                'puzzle_id' => array('type' => 'int'),
                'score' => array('type' => 'int', 'constraint' => 4),
                'comment' => array('type' => 'varchar', 'constraint' => 1000),
                'secret_comment' => array('type' => 'varchar', 'constraint' => 1000),
                'uid' => array('type' => 'int', 'constraint' => 11),
                'updated_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_reviews_table()
    {
        $table = 'reviews';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_hints_table()
    {
        $table = 'hints';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }
        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'puzzle_id' => array('type' => 'int'),
                'uid' => array('type' => 'int'),
                'comment' => array('type' => 'varchar', 'constraint' => 1000),
                'created_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('puzzle_id', 'uid'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }

    public function delete_hints_table()
    {
        $table = 'hints';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_news_table()
    {
        $table = 'news';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                'comment' => array('type' => 'varchar', 'constraint' => 1000),
                'uid' => array('type' => 'int', 'constraint' => 11),
                'updated_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function create_admin_bonus_point_table()
    {
        $table = 'admin_bonus_point';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                'uid' => array('type' => 'int'),
                'bonus_point' => array('type' => 'int'),
                'comment' => array('type' => 'varchar', 'constraint' => 1000),
                'updated_by' => array('type' => 'varchar', 'constraint' => 50),
                'updated_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_admin_bonus_point_table()
    {
        $table = 'admin_bonus_point';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function delete_news_table()
    {
        $table = 'news';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function update_times($timelist = NULL)
    {
        if ($timelist == NULL){
            echo "Usage: php oil r ctfscore:update_times file\n";
            return;
        }
        require $timelist;
        $table = 'times';
        $start_time = $times['start_time'];
        $end_time = $times['end_time'];
        if (count(\DB::select()->from($table)->execute()) < 1)
        {
            \DB::insert($table)->set(array(
                'start_time' => $start_time,
                'end_time' => $end_time
            ))->execute();
            echo "Time inserted: START=".$start_time.": END=".$end_time."\n";
        }
        else
        {
            \DB::update($table)->set(array(
                'start_time' => $start_time,
                'end_time' => $end_time
            ))->execute();
            echo "Time updated: START=".$start_time.": END=".$end_time."\n";
        }
    }


    /* Update puzzles from the php file specified by argument. */
    public function update_puzzles($puzzlelist = NULL)
    {
        if ($puzzlelist == NULL){
            echo "Usage: php oil r ctfscore:update_puzzles file\n";
            return;
        }
        require $puzzlelist;
        foreach ($puzzles as $puzzle) {
            $this->update_puzzle($puzzle);
        }
    }


    /* Update the puzzle specified by arguments. */
    public function update_puzzle($puzzle = NULL)
    {
        $puzzle_id = $puzzle['puzzle_id'];
        try
        {
            \DB::start_transaction();

            // 既に登録があれば更新、なければ新規
            $q1 = '';
            if (count(\DB::select()->from('puzzles')->where('puzzle_id', $puzzle_id)->execute()) > 0)
            {
                $q1 = \DB::update('puzzles');
                $q1->where('puzzle_id', $puzzle_id);
            }
            else
            {
                $q1 = \DB::insert('puzzles');
            }
            $q1->set(array(
                'puzzle_id' => $puzzle_id,
                'point' => $puzzle['point'],
                'bonus_point' => $puzzle['bonus_point'],
                'category' => $puzzle['category'],
                'title' => $puzzle['title'],
                'content' => $puzzle['content'],
            ))->execute();

            // flagは複数可能
            // 既に登録済のデータは全削除したあと、新規登録
            \DB::delete('flags')->where('puzzle_id', $puzzle_id)->execute();
            foreach ($puzzle['flag'] as $flag)
            {
                \DB::insert('flags')->set(array(
                    'puzzle_id' => $puzzle_id,
                    'flag' => $flag,
                ))->execute();
            }

            // 添付ファイルは複数可能
            // 既に登録済のデータは全削除したあと、新規登録
            \DB::delete('attachment')->where('puzzle_id', $puzzle_id)->execute();
            foreach ($puzzle['attachment'] as $attach)
            {
                \DB::insert('attachment')->set(array(
                    'puzzle_id' => $puzzle_id,
                    'filename' => $attach,
                ))->execute();
            }

            // 正解時に表示する画像ファイル
            // 既に登録済のデータは全削除したあと、新規登録
            \DB::delete('success_image')->where('puzzle_id', $puzzle_id)->execute();
            if ($puzzle['success_image'])
            {
                \DB::insert('success_image')->set(array(
                    'puzzle_id' => $puzzle_id,
                    'filename' => $puzzle['success_image'],
                ))->execute();
            }

            // 正解時に表示するテキストメッセージ
            // 既に登録済のデータは全削除したあと、新規登録
            \DB::delete('success_text')->where('puzzle_id', $puzzle_id)->execute();
            if ($puzzle['success_text'])
            {
                \DB::insert('success_text')->set(array(
                    'puzzle_id' => $puzzle_id,
                    'text' => $puzzle['success_text'],
                ))->execute();
            }

            \DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            throw $e;
        }

        echo "Puzzle updated: ".$puzzle_id.":".$puzzle['point'].":".$puzzle['bonus_point'].":".$puzzle['category'].":".$puzzle['title'].":".$puzzle['content']."\n";
    }


    /* Insert users from the php file specified by argument. */
    public function insert_users($userlist = NULL)
    {
        if ($userlist == NULL){
            echo "Usage: php oil r ctfscore:insert_users file\n";
            return;
        }
        require $userlist;
        foreach ($users as $user){
            $username = $user["username"];
            $password = $user["password"];
            $admin = $user["admin"];
            $this->insert_user($username, $password, $admin);
        }
    }


    /* Insert the user specified by arguments */
    public function insert_user($username = NULL, $password = NULL, $admin = false)
    {
        if (($username == NULL) || ($password == NULL)){
            echo "Usage: php oil r ctfscore:insert_user username password\n";
            return;
        }
        try {
            $auth = \Auth::instance();
            $dummyemail = rand() . '@dummy.com';
            $group = null;
            if ($admin) {
                $group = \Config::get('ctfscore.admin.admin_group_id');
            }
            if ($auth->create_user($username, $password, $dummyemail, $group)) {
                echo "User inserted: ".$username.":".$group."\n";
                return;
            }
            $errmsg = "Failed to insert: " . $username . "\n";
        } catch (SimpleUserUpdateException $e) {
            $errmsg = $e->getMessage();
        }
        echo $errmsg;
    }


    /* Insert texts from the php file specified by argument. */
    public function insert_random_texts($textlist = NULL)
    {
        if ($textlist == NULL){
            echo "Usage: php oil r ctfscore:insert_random_texts file\n";
            return;
        }
        require $textlist;
        foreach ($random_texts['success'] as $success){
            $this->insert_random_text('success_random_text', $success);
        }
        foreach ($random_texts['failure'] as $failure){
            $this->insert_random_text('failure_random_text', $failure);
        }
    }


    /* Insert the text specified by arguments */
    public function insert_random_text($table = NULL, $text = NULL)
    {
        if (($table == NULL) || ($text == NULL)){
            echo "Usage: php oil r ctfscore:insert_random_text table text\n";
            return;
        }

        \DB::insert($table)->set(array(
            'text' => $text,
        ))->execute();
        echo "Random text inserted: ".$table.":".$text."\n";
    }


    /* Update levels from the php file specified by argument. */
    public function update_levels($levellist = NULL)
    {
        if ($levellist == NULL){
            echo "Usage: php oil r ctfscore:update_levels file\n";
            return;
        }
        require $levellist;
        foreach ($levels as $level){
            $this->update_level($level);
        }
    }


    /* Update the level specified by arguments */
    public function update_level($level = NULL)
    {
        // 既に登録があれば更新、なければ新規
        $c = $level['category'];
        $l = $level['level'];
        $query = '';
        if (count(\DB::select()->from('levels')->where('category', $c)->where('level', $l)->execute()) > 0)
        {
            $query = \DB::update('levels');
            $query->where('category', $c)->where('level', $l);
        }
        else
        {
            $query = \DB::insert('levels');
        }
        $query->set(array(
            'category' => $c,
            'level' => $l,
            'name' => $level['name'],
            'criteria' => $level['criteria'],
        ))->execute();

        echo "Level updated: ".$c.":".$l.":".$level['name'].":".$level['criteria']."\n";
    }


    /* Refresh gained levels based on the levels table */
    public function refresh_gained_levels()
    {
        // 全ての獲得済レベルをクリアする
        \DB::update('gained_levels')->value('is_current', false)
            ->where('is_current', true)
            ->execute();
        // ユーザごとにレベルを再計算
        $users = \DB::select()->from('users')->execute()->as_array();
        foreach ($users as $user)
        {
            $uid = $user['id'];
            $username = $user['username'];
            $updated_levels = \Model_Score::set_level_gained($uid);
            echo "Level refreshed: ".$username;
            foreach ($updated_levels as $category => $name)
            {
                echo ":".$category."=>".$name;
            }
            echo "\n";
        }
    }

}
/* End of file tasks/ctfscore.php */
