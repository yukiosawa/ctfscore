<?php

class Model_Staticpage extends Model
{
    public static function _init()
    {
        // DB設定がなければデフォルト値で初期化
        $result = Model_Staticpage::get();
        if (count($result) == 0)
        {
            Model_Staticpage::insert_default_values('static_pages', Config::get('ctfscore.default_static_pages'));
        }
    }


    public static function insert_default_values($table, $values)
    {
        foreach ($values as $value)
        {
            try
            {
                DB::start_transaction();
                DB::insert($table)->set($value)->execute();
                DB::commit_transaction();
            }
            catch (Exception $e)
            {
                DB::rollback_transaction();
                throw $e;
            }
        }
    }

    
    public static function get($name)
    {
        $query = DB::select()->from('static_pages');
        if ($name != null)
        {
            $query->where('name', $name);
        }
        $query->order_by('display_order', 'asc');
        $result = $query->execute()->as_array();
        return $result;
    }


    public static function update($name, $display_name, $content, $is_active)
    {
        try
        {
            DB::start_transaction();
            $result = DB::update('static_pages')->set(array(
                'display_name' => $display_name,
                'content' => $content,
                'is_active' => $is_active,
            ))->where('name', $name)->execute();
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }

    
    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if ($factory == 'edit')
	{
            $val->add('name', 'name')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
            $val->add('display_name', '名称')
		->add_rule('max_length', 255);
	    $val->add('content', 'ページ本文')
		->add_rule('max_length', 1000);
            $val->add('is_active', '有効化')
                ->add_rule('numeric_min', 1)
                ->add_rule('numeric_max', 1);
	}

	return $val;
    }

}

