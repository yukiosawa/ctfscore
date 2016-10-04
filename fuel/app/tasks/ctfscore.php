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


    public function delete_table($table = NULL)
    {
        if ($table != NULL)
        {
            \DBUtil::drop_table($table);
            echo "Table deleted: ".$table."\n";
        }
        else
        {
            echo "Table unspecified.\n";
        }
    }
    

    public function init_all_tables()
    {
        /* delete all tables */
        $tables = array(
            'static_pages',
            'config_chart_colors',
            'assets',
            'config',
            'admin_bonus_point',
            'news',
            'gained_levels',
            'levels',
            'hints',
            'reviews',
            'history',
            'gained',
            'times',
            'failure_random_text',
            'success_random_text',
            'success_text',
            'success_image',
            'attachment',
            'flags',
            'puzzles',
            'categories',
            'users',
        );
        foreach ($tables as $table)
        {
            $this->delete_table($table);
        }
        
        /* create all tables */
        $this->create_users_table();
        $this->create_categories_table();
        $this->create_puzzles_table();
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
        $this->create_config_table();
        $this->create_assets_table();
        $this->create_config_chart_colors_table();
        $this->create_static_pages_table();
    }


    public function create_users_table()
    {
        // get the tablename
        \Config::load('simpleauth', true);
        $table = \Config::get('simpleauth.table_name', 'users');

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
            'already_news_id' => array('type' => 'int', 'default' => 0),
            'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            'totalpoint' => array('type' => 'int', 'default' => 0),
            'pointupdated_at' => array('type' => 'datetime'),
        ), array('id'));

        echo "Table created: ".$table."\n";
    }


    public function create_categories_table()
    {
        $table = 'categories';

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
                'category' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
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
                'category_id' => array('type' => 'int'),
                'title' => array('type' => 'varchar', 'constraint' => 255),
                'content' => array('type' => 'varchar', 'constraint' => 1000),
            ),
            /* primary_keys */
            array('puzzle_id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'category_id',
                    'reference' => array(
                        'table' => 'categories',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                )
            )
        );

        echo "Table created: ".$table."\n";
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
                'has_bonus' => array('type' => 'int'),
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
                'category_id' => array('type' => 'int'),
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
                array(
                    'key' => 'category_id',
                    'reference' => array(
                        'table' => 'categories',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                )
            )
        );

        echo "Table created: ".$table."\n";
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
                'category_id' => array('type' => 'int'),
                'level' => array('type' => 'int'),
                'name' => array('type' => 'varchar', 'constraint' => 50),
                'criteria' => array('type' => 'int'),
            ),
            /* primary_keys */
            array('category_id', 'level'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'category_id',
                    'reference' => array(
                        'table' => 'categories',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                )
            )
        );

        echo "Table created: ".$table."\n";
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
                'submitted_at' => array('type' => 'datetime'),
                'puzzle_id' => array('type' => 'int'),
                'answer' => array('type' => 'varchar', 'constraint' => 255),
                'result_event' => array('type' => 'varchar', 'constraint' => 255),
                'result_description' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('uid', 'submitted_at'),
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


    public function create_config_table()
    {
        $table = 'config';

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
                'type' => array('type' => 'varchar', 'constraint' => 255),
                'name' => array('type' => 'varchar', 'constraint' => 255),
                'value' => array('type' => 'varchar', 'constraint' => 255),
                'description' => array('type' => 'varchar', 'constraint' => 1000),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
    }


    public function create_assets_table()
    {
        $table = 'assets';

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
                'name' => array('type' => 'varchar', 'constraint' => 255),
                'type' => array('type' => 'varchar', 'constraint' => 255),
                'is_random' => array('type' => 'int', 'constraint' => 1),
                'sub_dir' => array('type' => 'varchar', 'constraint' => 255),
                'filename' => array('type' => 'varchar', 'constraint' => 255),
                'description' => array('type' => 'varchar', 'constraint' => 1000),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
    }


    public function create_config_chart_colors_table()
    {
        $table = 'config_chart_colors';

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
                'rank' => array('type' => 'int', 'constraint' => 11),
                'color' => array('type' => 'varchar', 'constraint' => 255),
            ),
            /* primary_keys */
            array('id')
        );

        echo "Table created: ".$table."\n";
    }


    public function create_static_pages_table()
    {
        $table = 'static_pages';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'name' => array('type' => 'varchar', 'constraint' => 255),
                'display_name' => array('type' => 'varchar', 'constraint' => 255),
                'path' => array('type' => 'varchar', 'constraint' => 255),
                'content' => array('type' => 'varchar', 'constraint' => 1000),
                'display_order' => array('type' => 'int'),
                'is_active' => array('type' => 'int')
            ),
            /* primary_keys */
            array('name')
        );

        echo "Table created: ".$table."\n";
    }


    // 現在の回答済問題から獲得総スコアを再計算する
    public function refresh_gained_points()
    {
        \Model_Puzzle::refresh_gained_points();
        echo "Updated the tables: 'gained', 'users'\n";
    }


    public function refresh_gained_levels()
    {
        \Model_Score::refresh_gained_levels();
        echo "Updated the table: 'gained_levels'\n";
    }
}
/* End of file tasks/ctfscore.php */
