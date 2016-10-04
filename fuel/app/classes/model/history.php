<?php

class Model_History extends Model
{
    public static function get_history_for_search($username = null, $puzzle_id = null, $result_event = null)
    {
        $query = DB::select(DB::expr('history.*,users.username,puzzles.title as puzzle_title'))->from('history')
            ->join('users', 'LEFT')
            ->on('history.uid', '=', 'users.id')
            ->join('puzzles', 'LEFT')
            ->on('history.puzzle_id', '=', 'puzzles.puzzle_id')
            ->order_by('history.submitted_at', 'desc');

        if ($username) {
            $query->where('users.username', $username);
        }

        if ($puzzle_id) {
            $query->where('history.puzzle_id', $puzzle_id);
        }

        if ($result_event)
        {
            $query->where('history.result_event', $result_event);
        }

        $result = $query->execute()->as_array();
        return $result;
    }


    public static function get_users()
    {
        $result = DB::select('username')->from('users')
            ->where(DB::expr('exists (select uid from history where users.id = history.uid)'))
            ->execute()->as_array();
        return array_map(function ($var) { return $var['username']; }, $result);
    }


    public static function get_gained_history($uid = null, $newest = false)
    {
//        $query = DB::select(DB::expr('gained.*, users.username, puzzles.title as puzzle_title'))->from('gained')
        $query = DB::select(DB::expr('gained.*, users.username, puzzles.*'))->from('gained')
            ->join('users', 'LEFT')
            ->on('gained.uid', '=', 'users.id')
            ->join('puzzles', 'LEFT')
            ->on('gained.puzzle_id', '=', 'puzzles.puzzle_id')
            ->order_by('gained.gained_at', 'desc');

        if ($uid)
        {
            $query->where('gained.uid', $uid);
        }
        if ($newest == true)
        {
            $query->where('gained.gained_at', DB::expr('(select max(gained_at) from gained)'));
        }

        $result = $query->execute()->as_array();
        return $result;
    }
}

