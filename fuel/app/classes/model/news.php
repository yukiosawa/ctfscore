<?php

class Model_News extends Model
{
    public static function get_news($id = null)
    {
        $query = DB::select(
            array('news.id', 'id'),
            array('news.comment', 'comment'),
            array('users.username', 'username'),
            array('news.updated_at', 'updated_at')
        )->from('news');

        if (!is_null($id))
        {
            $query->where('news.id', $id);
        }

        $query->join('users', 'LEFT')
            ->on('news.uid', '=', 'users.id')
            ->order_by('news.updated_at', 'desc');
        $result = $query->execute()->as_array();

        return $result;
    }


    public static function create_news($comment, $uid)
    {
        $result_id = '';
        $now = Model_Score::get_current_time();

        try
        {
            DB::start_transaction();
            $result = DB::insert('news')->set(array(
                'comment' => $comment,
                'uid' => $uid,
                'updated_at' => $now
            ))->execute();
            DB::commit_transaction();
            // INSERT実行の戻り値は
            // return array(
            //     lastInsertedId, // AUTO_INCREMENTなフィールドにセットされたID
            //     rowCount // 挿入された行数
            // );
            $result_id = $result[0];
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }

        return $result_id;
    }


    public static function update_news($id, $comment, $uid)
    {
        $result = '';
        $now = Model_Score::get_current_time();

        try
        {
            DB::start_transaction();
            $result = DB::update('news')->set(array(
                'id' => $id,
                'comment' => $comment,
                'uid' => $uid,
                'updated_at' => $now
            ))->where('id', $id)->execute();
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }


    public static function delete_news($id)
    {
        return DB::delete('news')->where('id', $id)->execute();
    }


    public static function get_already($userid)
    {
        $result = DB::select('already_news_id')->from('users')
            ->where('id', $userid)
            ->execute()->as_array();
        $already = $result[0]['already_news_id'];

        $result = DB::select(DB::expr('COUNT(id)'))->from('news')
            ->where('id', '>', $already)
            ->execute()->as_array();
        return $result[0]['COUNT(id)'];
    }


    public static function update_already($userid, $newsid)
    {
        $result = DB::select('already_news_id')->from('users')
            ->where('id', $userid)
            ->execute()->as_array();
        $already = $result[0]['already_news_id'];

        if ($newsid > $already) {
            try
            {
                DB::start_transaction();
                $result = DB::update('users')->set(array(
                    'already_news_id' => $newsid
                ))->where('id', $userid)->execute();
                DB::commit_transaction();
            }
            catch (Exception $e)
            {
                DB::rollback_transaction();
                throw $e;
            }
        }
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
        $val = Validation::forge($factory);

        if ($factory == 'create' || $factory == 'edit')
        {
            $val->add('comment', 'コメント')
                ->add_rule('required')
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

