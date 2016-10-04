<?php

class Model_Config extends Model
{
    public static function _init()
    {
        // DB設定がなければデフォルト値で初期化
        $result = DB::select()->from('config')->execute()->as_array();
        if (count($result) == 0)
        {
            Model_Config::insert_default_values('config', Config::get('ctfscore.default_values'));
        }

        $result = DB::select()->from('assets')->execute()->as_array();
        if (count($result) == 0)
        {
            Model_Config::insert_default_values('assets', Config::get('ctfscore.default_assets'));
        }

        $result = DB::select()->from('config_chart_colors')->execute()->as_array();
        if (count($result) == 0)
        {
            Model_Config::insert_default_values('config_chart_colors', Config::get('ctfscore.default_chart_colors'));
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
    
    
    public static function get_config($id = null, $type = null)
    {
	$query = DB::select()->from('config');

        if ($id)
        {
            $query->where('id', $id);
        }
        if ($type)
        {
            $query->where('type', $type);
        }

	$result = $query->execute()->as_array();
	return $result;
    }


    public static function get_assets($name, $type, $is_random)
    {
        $query = DB::select()->from('assets');

        if ($name)
        {
            $query->where('name', $name);
        }
        if ($type)
        {
            $query->where('type', $type);
        }
        if (isset($is_random))
        {
            $query->where('is_random', $is_random);
        }

        $result = $query->execute()->as_array();
        return $result;
    }


    public static function get_config_chart_colors($id = null)
    {
	$query = DB::select()->from('config_chart_colors');

        if ($id)
        {
            $query->where('id', $id);
        }

	$result = $query->order_by('rank', 'asc')->execute()->as_array();
	return $result;
    }
    

    // asset画像を取得(ファイル名指定で設定している画像)
    public static function get_asset_images($name)
    {
        $images = Model_Config::get_assets($name, 'img', 0);
        return array_map(
            function ($var) {
                $url = Asset::get_file($var['filename'], 'img', $var['sub_dir']);
                $url = $url == false ? '' : $url;
                return array(
                    'name' => $var['name'],
                    'filename' => $var['filename'],
                    'url' => $url,
                    'description' => $var['description']
                );
            },
            $images
        );
    }


    // asset画像を取得(ランダム表示する画像)
    public static function get_asset_random_images($name)
    {
        $images = Model_Config::get_assets($name, 'img', 1);
        $dir1 = Config::get('asset.img_dir');

        foreach ($images as $image)
        {
            $files = Model_Config::get_files(DOCROOT.Config::get('asset.paths')[0].$dir1.$image['sub_dir']);
            $res[] = array(
                'name' => $image['name'],
                'description' => $image['description'],
                'assets' => array_map(
                    function ($var) use ($image) {
                        $url = Asset::get_file($var, 'img', $image['sub_dir']);
                        $url = $url == false ? '' : $url;
                        return array('filename' => $var, 'url' => $url);
                    }, $files)
            );
        }

        return $res;
    }


    // asset音を取得(ファイル名指定で設定している音)
    public static function get_asset_sounds($name)
    {
        $sounds = Model_Config::get_assets($name, 'audio', 0);
        return array_map(
            function ($var) {
                $url = Asset::get_file($var['filename'], 'audio', $var['sub_dir']);
                $url = $url == false ? '' : $url;
                return array(
                    'name' => $var['name'],
                    'filename' => $var['filename'],
                    'url' => $url,
                    'description' => $var['description']
                );
            },
            $sounds
        );
    }


    // asset音を取得(ランダム再生する音)
    public static function get_asset_random_sounds($name)
    {
        $sounds = Model_Config::get_assets($name, 'audio', 1);
        $dir1 = Config::get('asset.audio_dir');

        foreach ($sounds as $sound)
        {
            $files = Model_Config::get_files(DOCROOT.Config::get('asset.paths')[0].$dir1.$sound['sub_dir']);
            $res[] = array(
                'name' => $sound['name'],
                'description' => $sound['description'],
                'assets' => array_map(
                    function ($var) use ($sound) {
                        $url = Asset::get_file($var, 'audio', $sound['sub_dir']);
                        $url = $url == false ? '' : $url;
                        return array('filename' => $var, 'url' => $url);
                    }, $files)
            );
        }

        return $res;
    }


    public static function get_files($dir)
    {
        try
        {
            // dir直下のファイルすべて
            $files = File::read_dir($dir, 1, array(
                '!^\.', // 隠しファイルは除く
                '!.*' => 'dir', // ディレクトリは除く
            ));
            return $files;
        }
        catch (InvalidPathException $e)
        {
            // 無視する
            return;
        }
    }


    public static function get_asset_dir($type)
    {
        if ($type == 'img')
        {
            $dir1 = Config::get('asset.img_dir');
        }
        else if ($type == 'audio')
        {
            $dir1 = Config::get('asset.audio_dir');
        }
        else
        {
            return null;
        }

        return DOCROOT.Config::get('asset.paths')[0].$dir1;
    }
    

    public static function delete_asset_file($type, $sub_dir, $filename)
    {
        $asset_dir = Model_Config::get_asset_dir($type);
        $filepath = $asset_dir.$sub_dir.'/'.$filename;
        if (File::exists($filepath))
        {
            return File::delete($filepath);
        }
        else
        {
            return false;
        }
    }


    public static function get_value($name)
    {
	$query = DB::select('value')->from('config')->where('name', $name);
	$result = $query->execute()->as_array();
	return $result[0]['value'];
    }

    
    public static function update_config($id, $name, $value)
    {
        try
        {
            DB::start_transaction();
            $query = DB::update('config')->set(array('value' => $value));
            if ($id)
            {
                $query->where('id', $id);
            }
            if ($name)
            {
                $query->where('name', $name);
            }
            $result = $query->execute();
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }


    public static function update_asset($name, $filename)
    {
        if ($name == null)
        {
            return;
        }
        
        try
        {
            DB::start_transaction();
            $query = DB::update('assets')->set(array('filename' => $filename));
            $query->where('name', $name);
            $result = $query->execute();
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }


    public static function insert_config_chart_color($rank, $color)
    {
        $result = DB::select(DB::expr('MAX(rank)'))->from('config_chart_colors')->execute()->as_array();
        if ($result)
        {
            $max_rank = $result[0]['MAX(rank)'];
        }

        if ($max_rank < $rank)
        {
            $update_rank = $max_rank + 1;
            // ランク末尾に追加
            try
            {
                DB::start_transaction();
                list($insert_id, $rows_affected) = DB::insert('config_chart_colors')->set(array(
                    'rank' => $update_rank,
                    'color' => $color
                    ))->execute();
                DB::commit_transaction();
            }
            catch (Exception $e)
            {
                DB::rollback_transaction();
                throw $e;
            }
            return array($insert_id, $rows_affected);
        }

        // ランクの途中に挿入
        $tmp_colors = DB::select()->from('config_chart_colors')->where('rank', '>=', $rank)->execute()->as_array();
        try
        {
            DB::start_transaction();
            list($insert_id, $rows_affected)
                = DB::insert('config_chart_colors')->set(array(
                    'rank' => $rank,
                    'color' => $color
                ))->execute();

            foreach ($tmp_colors as $tmp_color)
            {
                $update_rank = $tmp_color['rank'] + 1;
                DB::update('config_chart_colors')->set(array(
                    'rank' => $update_rank
                ))->where('id', $tmp_color['id'])->execute();
            }
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return array($insert_id, $rows_affected);
    }
    

    public static function update_config_chart_color($id, $rank, $color)
    {
        try
        {
            DB::start_transaction();
            $result = DB::update('config_chart_colors')->set(array(
                'rank' => $rank,
                'color' => $color
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


    public static function delete_config_chart_color($id)
    {
        $result = DB::select('rank')->from('config_chart_colors')->where('id', $id)->execute()->as_array();
        if ($result)
        {
            $rank = $result[0]['rank'];
        }
        else
        {
            return;
        }
        $tmp_colors = DB::select()->from('config_chart_colors')->where('rank', '>', $rank)->execute()->as_array();

        try
        {
            DB::start_transaction();
            $result = DB::delete('config_chart_colors')->where('id', $id)->execute();

            // 削除したもの以降のランクを詰めていく
            foreach ($tmp_colors as $tmp_color)
            {
                $update_rank = $tmp_color['rank'] - 1;
                DB::update('config_chart_colors')->set(array(
                    'rank' => $update_rank
                ))->where('id', $tmp_color['id'])->execute();
            }
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }


    public static function set_time($start_time, $end_time)
    {
        $table = 'times';
        try
        {
            DB::start_transaction();
            if (count(DB::select()->from($table)->execute()) < 1)
            {
                $result = DB::insert($table)->set(array(
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ))->execute();
            }
            else
            {
                $result = DB::update($table)->set(array(
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ))->execute();
            }
            DB::commit_transaction();
        }
        catch (Exception $e)
        {
            DB::rollback_transaction();
            throw $e;
        }
        return $result;
    }


    public static function delete_time()
    {
        try
        {
            DB::start_transaction();
            $result = DB::delete('times')->execute();
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
            $val->add('id', 'ID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
	    $val->add('value', '設定値')
		->add_rule('max_length', 255);
	}
        else if ($factory == 'editcolor')
        {
            $val->add('id', 'ID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
            $val->add('rank', 'rank')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
	    $val->add('color', '色設定')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
        }
        else if ($factory == 'deletecolor')
        {
            $val->add('id', 'ID')
                ->add_rule('required')
                ->add_rule('numeric_max', 10000)
                ->add_rule('numeric_min', 1);
        }
        else if ($factory == 'deletefile')
        {
            $val->add('filename', 'filename')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
            $val->add('name', 'name')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
        }
        else if ($factory == 'fileupload')
        {
            $val->add('name', 'name')
                ->add_rule('required')
		->add_rule('min_length', 1)
		->add_rule('max_length', 255);
        }
        else if ($factory == 'settime')
        {
            $val->add('start_time', '開始時刻')
                ->add_rule('required')
                ->add_rule('valid_date');
            $val->add('end_time', '終了時刻')
                ->add_rule('required')
                ->add_rule('valid_date');
        }
        else if ($factory == 'edittexts')
        {
            $texts = Input::post('texts');
            foreach ($texts as $key => $value)
            {
                $val->add('texts['.$key.']', 'テキストメッセージ')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 255);
            }
        }
        else if ($factory == 'editlevels')
        {
            $category_id = Input::post('levels')['category_id'];
            foreach ($category_id as $key => $value)
            {
                $val->add('levels[category_id]['.$key.']', 'カテゴリID')
                    ->add_rule('required')
                    ->add_rule('numeric_min', 1)
                    ->add_rule('numeric_max', 1000);
            }
            $level = Input::post('levels')['level'];
            foreach ($level as $key => $value)
            {
                $val->add('levels[level]['.$key.']', 'レベル')
                    ->add_rule('required')
                    ->add_rule('numeric_min', 1)
                    ->add_rule('numeric_max', Model_Config::get_value('total_category_id'));
            }
            $name = Input::post('levels')['name'];
            foreach ($name as $key => $value)
            {
                $val->add('levels[name]['.$key.']', 'レベル名称')
                    ->add_rule('required')
                    ->add_rule('min_length', 1)
                    ->add_rule('max_length', 50);
            }
            $criteria = Input::post('levels')['criteria'];
            foreach ($criteria as $key => $value)
            {
                $val->add('levels[criteria]['.$key.']', '正答した問題数')
                    ->add_rule('required')
                    ->add_rule('numeric_min', 0)
                    ->add_rule('numeric_max', 100);
            }
        }

	return $val;
    }
}

