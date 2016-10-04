<?php

class Model_Users extends Model
{
    public static function get_users($id = null, $username = null)
    {
	$query = DB::select('id', 'username', 'group', 'created_at', 'updated_at')->from('users');
        if (!is_null($id))
        {
            $query->where('id', $id);
        }
        else if (!is_null($username))
        {
            $query->where('username', $username);
        }
	$result = $query->execute()->as_array();

	return $result;
    }


    public static function create_user($username, $password, $admin)
    {
        $result = false;
        
        $dummyemail = rand() . '@dummy.com';
        $password = $password ? $password : 'dummypassword';
        // 一般ユーザはグループ1 (SimpleAuthのデフォルト値)
        $group = $admin == true ? Model_Config::get_value('admin_group_id') : 1;
        
        try
        {
            $result = Auth::create_user($username, $password, $dummyemail, $group);
        }
        catch (SimpleUserUpdateException $e)
        {
            $errmsg = $e->getMessage();
        }

        // レベル初期値を反映しておく
        Model_Score::set_level_gained($result);

        return array('created_id' => $result, 'errmsg' => $errmsg);
    }


    public static function update_user($username, $admin)
    {
        $result = false;

        // 一般ユーザはグループ1 (SimpleAuthのデフォルト値)
        $group = $admin == true ? Model_Config::get_value('admin_group_id') : 1;

        try
        {
            $result = Auth::update_user(array('group' => $group), $username);
        }
        catch (SimpleUserUpdateException $e)
        {
            $errmsg = $e->getMessage();
        }

        return array('bool' => $result, 'errmsg' => $errmsg);
    }
    

    public static function delete_user($username)
    {
        $result = false;
        
        try
        {
            $result = Auth::delete_user($username);
        }
        catch (SimpleUserUpdateException $e)
        {
            $errmsg = $e->getMessage();
        }

        return array('bool' => $result, 'errmsg' => $errmsg);
    }


    public static function reset_password($username)
    {
        $result = '';

        try
        {
            $result = Auth::reset_password($username);
        }
        catch (SimpleUserUpdateException $e)
        {
            $errmsg = $e->getMessage();
        }

        return array('new_password' => $result, 'errmsg' => $errmsg);
    }


    public static function change_password($old_password, $new_password)
    {
        $result = false;

        try
        {
            $result = Auth::change_password($old_password, $new_password);
        }
        catch (SimpleUserUpdateException $e)
        {
            $errmsg = $e->getMessage();
        }

        return array('bool' => $result, 'errmsg' => $errmsg);
    }
    

    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if ($factory == 'create' || $factory == 'edit')
	{
            $val->add('username', 'ユーザ名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                // 英数字
                ->add_rule('match_pattern', '/^[a-zA-Z0-9]+$/');
            $val->add('admin', '管理者権限')
                ->add_rule('numeric_max', 1)
                ->add_rule('numeric_min', 1);
	}
	else if ($factory == 'delete' || $factory == 'pwreset')
	{
	    $val->add('username', 'ユーザ名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                // 英数字
                ->add_rule('match_pattern', '/^[a-zA-Z0-9]+$/');
	}

	return $val;
    }
}

