<?php

class Model_Hint extends Model
{
    public static function get_hints($puzzle_id, $userid = null)
    {
        $query = DB::select(DB::expr('hints.*,users.username,puzzles.category,puzzles.title'))
            ->from('hints')
            ->join('users', 'LEFT')->on('hints.uid', '=', 'users.id')
            ->join('puzzles', 'LEFT')->on('hints.puzzle_id', '=', 'puzzles.puzzle_id')
            ->where('hints.puzzle_id', $puzzle_id);

        if ($userid) {
            $query->where('hints.uid', $userid);
        }

        return $query->execute()->as_array();
    }

    public static function get_hinted_all($userid)
    {
        $result = DB::select(DB::expr('puzzle_id'))->from('hints')
            ->where('uid', $userid)
            ->execute()->as_array();
        return array_map(function ($var) { return $var['puzzle_id']; }, $result);
    }

    public static function is_hinted($puzzle_id, $userid)
    {
        $result = DB::select()->from('hints')
            ->where('puzzle_id', $puzzle_id)
            ->where('uid', $userid)
            ->execute()->as_array();

        return empty($result) === false;
    }

    public static function get_hints_count()
    {
        $result = DB::select(DB::expr('puzzle_id, COUNT(puzzle_id)'))->from('hints')
            ->group_by('puzzle_id')
            ->execute()->as_array();
        $return = array();
        foreach ($result as $values) {
            $return[$values['puzzle_id']] = $values['COUNT(puzzle_id)'];
        }
        return $return;
    }

    public static function create_hint($values)
    {
        $values['created_at'] = DB::expr('NOW()');

        try {
            DB::start_transaction();
            DB::insert('hints')->set($values)->execute();
            DB::commit_transaction();
        } catch (Exception $e) {
            DB::rollback_transaction();
            throw $e;
        }
    }

    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
        $val = Validation::forge($factory);

        if ($factory == 'create') {
            $val->add('comment', 'コメント')
                ->add_rule('max_length', 1000);
        }

        return $val;
    }
}

