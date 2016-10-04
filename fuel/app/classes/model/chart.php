<?php

class Model_Chart extends Model
{

    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
        $val = Validation::forge($factory);

        if ($factory == 'profile') {
            $usernames = Input::post('usernames');
            foreach ($usernames as $key => $value)
            {
                $val->add('usernames['.$key.']', 'ユーザ名')
                    ->add_rule('max_length', 50);
            }
        }

        return $val;
    }
}
