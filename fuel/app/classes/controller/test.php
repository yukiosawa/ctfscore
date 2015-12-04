<?php

class Controller_Test extends Controller_Template
{
    public function action_submit_complete()
    {
        $data = array();
        $result = 'success';
        $puzzle_id = '';
        $error_msg = '';
        $image_names = array();
        $text = '';
        $levels = '';
        $is_first_winner = false;
        $first_bonus_img = '';
        $is_complete = true;
        $complete_img = Config::get('ctfscore.puzzles.images.complete_img');
        $complete_sound = Config::get('ctfscore.sound.complete_sound');

        $image_urls = array();

        $data['result'] = $result;
        $data['puzzle_id'] = $puzzle_id;
        $data['image_names'] = $image_names;
        $data['text'] = $text;
        $data['levels'] = $levels;
        $data['is_first_winner'] = $is_first_winner;
        $data['first_bonus_img'] = $first_bonus_img;
        $data['is_complete'] = $is_complete;
        $data['complete_img'] = $complete_img;
        $data['complete_sound'] = $complete_sound;
        $data['sound_on'] = Cookie::get('sound_on', '1');

        $this->template->title = '回答結果';
        $this->template->content = View::forge('score/complete', $data);
        $this->template->content->set_safe('image_urls', $image_urls);
        $this->template->content->set_safe('errmsg', $error_msg);
        $this->template->footer = View::forge('score/footer');
    }
}
