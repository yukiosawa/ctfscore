<?php

class Controller_Admin_Mgmt extends Controller
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


    public static function read_audio_paths($dir = null)
    {
	try {
	    $paths = array();
            // dir直下のファイルすべて
            $files = File::read_dir(DOCROOT.$dir, 1, array(
		'!^\.', // 隠しファイルは除く
		'!.*' => 'dir', // ディレクトリは除く
            ));
            foreach ($files as $file) {
		$paths[] = $dir.'/'.$file;
            }
	    return $paths;
	}
	catch (InvalidPathException $e)
	{
            // 無視する
	}
    }

    
    public function action_index()
    {
	// 各種音源ファイル
	$first_winner_files = '';
	$success_files = '';
	$failure_files = '';
	$levelup_files = '';
	$notice_files = '';
	if (Config::get('ctfscore.sound.is_active_on_success'))
	{
	    $success_dir = Config::get('ctfscore.sound.success_dir');
	    $success_files = Controller_Admin_Mgmt::read_audio_paths($success_dir);
	    $first_winner_dir = Config::get('ctfscore.sound.first_winner_dir');
	    $first_winner_files = Controller_Admin_Mgmt::read_audio_paths($first_winner_dir);
	}
	if (Config::get('ctfscore.sound.is_active_on_failure'))
	{
	    $failure_dir = Config::get('ctfscore.sound.failure_dir');
	    $failure_files = Controller_Admin_Mgmt::read_audio_paths($failure_dir);
	}
	if (Config::get('ctfscore.sound.is_active_on_levelup'))
	{
	    $levelup_dir = Config::get('ctfscore.sound.levelup_dir');
	    $levelup_files = Controller_Admin_Mgmt::read_audio_paths($levelup_dir);
	}
	if (Config::get('ctfscore.sound.is_active_on_notice'))
	{
	    $notice_dir = Config::get('ctfscore.sound.notice_dir');
	    $notice_files = Controller_Admin_Mgmt::read_audio_paths($notice_dir);
	}
	$data['first_winner_files'] = $first_winner_files;
	$data['success_files'] = $success_files;
	$data['failure_files'] = $failure_files;
	$data['levelup_files'] = $levelup_files;
	$data['notice_files'] = $notice_files;
	return View::forge('mgmt/index', $data);
    }
}

