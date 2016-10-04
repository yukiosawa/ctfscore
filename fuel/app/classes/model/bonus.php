<?php

class Model_Bonus extends Model
{
    public static function get_bonus($id = null)
    {
	$query = DB::select(
	    array('admin_bonus_point.id', 'id'),
	    array('users.username', 'username'),
            array('admin_bonus_point.bonus_point', 'bonus_point'),
	    array('admin_bonus_point.comment', 'comment'),
            array('admin_bonus_point.updated_by', 'updated_by'),
	    array('admin_bonus_point.updated_at', 'updated_at')
	)->from('admin_bonus_point');

        if (!is_null($id))
        {
            $query->where('admin_bonus_point.id', $id);
        }

	$query->join('users', 'LEFT')
	      ->on('admin_bonus_point.uid', '=', 'users.id')
	      ->order_by('admin_bonus_point.updated_at', 'desc');
	$result = $query->execute()->as_array();

	return $result;
    }


    public static function create_bonus($uid, $point, $comment)
    {
        $now = Model_Score::get_current_time();
        $totalpoint = DB::select('totalpoint')
            ->from('users')->where('id', $uid)
            ->execute()->as_array()[0]['totalpoint'];
        $newpoint = $totalpoint + $point;
        $username = Auth::get_screen_name();
        try {
            DB::start_transaction();
            $result = DB::insert('admin_bonus_point')->set(array(
                'uid' => $uid,
                'bonus_point' => $point,
                'comment' => $comment,
                'updated_by' => $username,
                'updated_at' => $now
            ))->execute();
	    $result_id = $result[0];
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
        return $result_id;
    }


    public static function delete_bonus($id)
    {
        $result = -1;
        $bonus = DB::select()->from('admin_bonus_point')->where('id', $id)->execute()->as_array();
        if (count($bonus) < 1)
        {
            return $result;
        }
        $uid = $bonus[0]['uid'];

        $now = Model_Score::get_current_time();
        $totalpoint = DB::select('totalpoint')
            ->from('users')->where('id', $uid)
            ->execute()->as_array()[0]['totalpoint'];
        $newpoint = $totalpoint - $bonus[0]['bonus_point'];
        try {
            DB::start_transaction();
            $result = DB::delete('admin_bonus_point')->where('id', $id)->execute();
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
        return $result;
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if ($factory == 'create')
	{
            $val->add('username', 'ユーザ名')
                ->add_rule('required')
                ->add_rule('max_length', 15)
                // 英数字
                ->add_rule('match_pattern', '/^[a-zA-Z0-9]+$/');
            $val->add('bonus_point', 'ボーナス点')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
	    $val->add('comment', 'コメント')
		->add_rule('min_length', 1)
		->add_rule('max_length', 1000);
	}
	else if ($factory == 'delete')
	{
	    $val->add('id', 'ID')
		->add_rule('required')
		->add_rule('numeric_max', 10000)
		->add_rule('numeric_min', 1);
	}

	return $val;
    }
}

