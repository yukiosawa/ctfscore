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
        if (Model_Config::get_value('is_active_management_diag_msg') == 0)
        {
            $diag_msg = false;
        }
        else
        {
            $diag_msg = true;
        }

        $data['diag_msg'] = $diag_msg;
        $data['gained_history'] = Model_History::get_gained_history();
        $data['status'] = Model_Score::get_ctf_time_status();
        return View::forge('admin/mgmt/index', $data);
    }
}

