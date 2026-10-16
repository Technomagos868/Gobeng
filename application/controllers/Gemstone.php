<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Gemstone extends GS_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Admin_model", "admin_model");
    }

    //===============================================================
    //=================START DEVELOPING WITH GEMSTONE================
    //================PLACE YOUR CODE BELOW THIS LINE================
    //===============================================================
    public function index()
    {
        if ($this->session->userdata("admin_id") == "" || $this->session->userdata("admin_id") === false) {
            $this->load->view('gemstone_login');
        } else {
            $data["gemstone_css_list"] = $this->get_css_list();
            $data["gemstone_js_list"] = $this->get_js_list();
            $user_agent = $this->gemstone_model->get_user_agent();
            if (strpos($user_agent["name"], "Chrome") === false && strpos($user_agent["name"], "Firefox") === false) {
                $data["gemstone_browser_warning"] = true;
            }
            $data["allowed_dashboard"] = $this->gemstone_model->check_action_access("00-002")->valid;
            $data["allowed_admin_list"] = $this->gemstone_model->check_action_access("00-005")->valid;
            $data["view_passenger_list"] = $this->gemstone_model->check_action_access("01-001")->valid;
            $data["add_new_passenger"] = $this->gemstone_model->check_action_access("01-001A")->valid;
            $data["delete_passenger"] = $this->gemstone_model->check_action_access("01-001D")->valid;
            $data["view_ridetype_list"] = $this->gemstone_model->check_action_access("02-001")->valid;
            $data["view_trip_list"] = $this->gemstone_model->check_action_access("03-001")->valid;
            $data["add_new_trip"] = $this->gemstone_model->check_action_access("03-001A")->valid;
            $data["delete_trip"] = $this->gemstone_model->check_action_access("03-001D")->valid;
            $data["allowed_gemstone_settings"] = $this->gemstone_model->check_action_access("00-013")->valid;
            $this->load->view("gemstone_home", $data);
        }
    }

    public function view_dashboard($data = [])
    {
        $this->validate_action_access("00-002");
        $data["allowed_admin_list"] = $this->gemstone_model->check_action_access("00-005")->valid;
        $data["my_activity"] = $this->gemstone_model->get_my_recent_activity_count();
        $data["online_admin"] = $this->gemstone_model->get_online_admin_list();
        $data["admin_count"] = $this->admin_model->get_count();
        $this->navigate('dashboard', $data, "Dashboard", true);
    }
}
