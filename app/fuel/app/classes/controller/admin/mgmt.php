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

    public function action_index()
    {
	return View::forge('mgmt/index');
    }
}

