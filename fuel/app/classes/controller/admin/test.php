<?php

class Controller_Admin_Test extends Controller_Template
{
    public function before()
    {
        parent::before();

        // 管理者グループのみ許可
        if (!Controller_Auth::is_admin())
        {
            Response::redirect('auth/invalid');
        }
    }

    public function action_submit_complete()
    {
        $data = array();
        $answer = '';
        $result = Config::get('ctfscore.answer_result.success');
        $puzzle_id = '';
        $error_msg = '';
        $text = '';
        $image_url = '';
        $sound_url = '';
        $levels = '';
        $is_first_winner = false;
        $first_bonus_img_url = '';
        $is_complete = true;
        $complete_img_url = Model_Config::get_asset_images('complete_img')[0]['url'];
        $complete_sound_url = Model_Config::get_asset_sounds('complete_sound')[0]['url'];

        $data['result'] = $result;
        $data['puzzle_id'] = $puzzle_id;
        $data['text'] = $text;
        $data['image_url'] = $image_url;
        $data['sound_url'] = $sound_url;
        $data['is_first_winner'] = $is_first_winner;
        $data['first_bonus_img_url'] = $first_bonus_img_url;
        $data['is_complete'] = $is_complete;
        $data['complete_img_url'] = $complete_img_url;
        $data['complete_sound_url'] = $complete_sound_url;
        $data['sound_on'] = Cookie::get('sound_on', '1');

        $this->template->title = '回答結果';
        $this->template->content = View::forge('score/complete', $data);
        if (!$complete_img_url)
        {
            $this->template->content->set_safe('errmsg', '全完時の画像設定されていないため、通常の問題正解時と同じ画面が表示されます。');
        }
        $this->template->footer = View::forge('score/footer');
    }


    public function action_diploma($username = null)
    {
        if ($username === null)
        {
            // 管理者の賞状はないので1位ユーザのデータを使う
            $result = DB::select()->from('users')->order_by('totalpoint', 'desc')->execute()->as_array();
            if (count($result) < 1)
            {
                Response::redirect('score/view');
            }
            else
            {
                Response::redirect(Uri::current().'/'.$result[0]['username']);
            }
        }

        $data['username'] = ($username === null) ? Auth::get_screen_name() : $username;
        $profile = Model_Score::get_profile_detail(array($data['username']));
        if (empty($profile)) {
            Response::redirect('score/view');
        }

        $diploma_sound_url = '';
        if (Model_Config::get_value('is_active_sound') != 0)
        {
            $diploma_sound_url = Model_Config::get_asset_sounds('diploma_sound')[0]['url'];
        }

        $data['profile'] = $profile[0];
        $data['score'] = Model_Score::get_score_ranking($data['username']);
        $data['ctf_name'] = Model_Config::get_value('ctf_name');
        $data['diploma_sound_url'] = $diploma_sound_url;
        $data['sound_on'] = Cookie::get('sound_on', '1');
        $this->template->title = '賞状';
        $this->template->content = View::forge('score/diploma', $data);
        $this->template->footer = '';
    }
}
