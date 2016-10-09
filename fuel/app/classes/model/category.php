<?php

class Model_Category extends Model
{
    public static function _init()
    {
        // DB設定がなければデフォルト値で初期化
        $id = Model_Config::get_value('total_category_id');
        if (count(DB::select()->from('categories')->where('id', $id)->execute()) == 0)
        {
            DB::start_transaction();
            DB::insert('categories')->set(array('id' => $id, 'category' => 'Dummy_For_Total_ Category'))->execute();
            DB::commit_transaction();
        }
    }


    // カテゴリ一覧を取得
    public static function get_categories($id = null)
    {
        $query = DB::select()->from('categories')
            ->where('id', '!=', Model_Config::get_value('total_category_id'))
            ->order_by('id', 'asc');
        if ($id)
        {
            $query->where('id', $id);
        }
        $result = $query->execute()->as_array();
        return $result;
    }


    public static function create_category($category)
    {
        try
        {
            DB::start_transaction();
            $result = $query = DB::insert('categories')->set(array('category' => $category))->execute();
            DB::commit_transaction();
            // INSERT実行の戻り値は
            // return array(
            //     lastInsertedId, // AUTO_INCREMENTなフィールドにセットされたID
            //     rowCount // 挿入された行数
            // );
            $id = $result[0];
        }
        catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return;
        }

        return $id;
    }


    public static function update_category($id, $category)
    {
        try
        {
            DB::start_transaction();
            $result = DB::update('categories')->set(array('category' => $category))->where('id', $id)->execute();
            DB::commit_transaction();
        } catch (Exception $e) {
            // ロールバック
            DB::rollback_transaction();
            return array('bool' => false, 'errmsg' => 'カテゴリ更新に失敗しました。');
        }

        return $result;
    }


    public static function delete_category($id)
    {
        $result = DB::delete('categories')->where('id', $id)->execute();
        // カテゴリに紐づく問題も削除されるので、全ユーザの獲得済ポイントを更新
        Model_Puzzle::refresh_gained_points();
        return $result;
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

        if ($factory == 'create')
        {
	    $val->add('category', 'カテゴリ')
                ->add_rule('required')
		->add_rule('max_length', 255);
        }
	else if ($factory == 'edit')
	{
	    $val->add('category_id', 'カテゴリID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
	    $val->add('category', 'カテゴリ')
                ->add_rule('required')
		->add_rule('max_length', 255);
	}
        else if ($factory == 'delete')
        {
            $val->add('category_id', 'カテゴリID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
        }

	return $val;
    }

}

