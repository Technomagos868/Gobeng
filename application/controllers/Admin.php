<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Admin extends GS_Controller
{
    var $module_name = "Admin";
    var $module_view_folder_name = "Admin";
    var $model_name = "Admin_model";
    var $action_code = "00-005";
    var $detail_breadcrumb_title = "admin_name";

    public function __construct()
    {
        parent::__construct();
        $this->load->model($this->model_name, "model");
    }

    public function index()
    {
        $this->load->view('gemstone_login');
    }

    public function view_list($data = [])
    {
        $this->validate_action_access("{$this->action_code}");
        $data["allow_add"] = $this->model->check_action_access("{$this->action_code}A")->valid;
        $data["allow_detail"] = $this->model->check_action_access("{$this->action_code}D")->valid;
        $data["allow_activity_report"] = $this->gemstone_model->check_action_access("{$this->action_code}REPT")->valid;
        $data["allow_deactivate"] = $this->gemstone_model->check_action_access("{$this->action_code}DEAC")->valid;
        $data["allow_reactivate"] = $this->gemstone_model->check_action_access("{$this->action_code}REAC")->valid;
        $data["allow_force_logout"] = $this->gemstone_model->check_action_access("00-004A")->valid;
        $data["allow_position_list"] = $this->gemstone_model->check_action_access("00-015")->valid;
        $data["allow_division_list"] = $this->gemstone_model->check_action_access("00-021")->valid;
        $data['page'] = $page = $this->get_page_input($data, 1);
        $page = explode(",", $page);
        $data['sort'] = $sort = $this->get_sort_input($data, 'id');
        $data["search"] = $search = $this->get_custom_input("search", $data, "");
        $count = $this->model->get_count($search);
        $data['list'] = $this->model->get_list_paged($page[0], $sort, $search);
        $data['pagination'] = $this->draw_pagination($page[0], $count, "{$this->module_view_folder_name}/view_list?sort=$sort&search=$search");
        $this->navigate("{$this->module_view_folder_name}/list", $data, "{$this->module_name} List");
    }

    public function view_detail($data = [])
    {
        $id = $this->get_custom_input("id", $data, 0);
        if ($this->session->userdata("admin_id") == $id) {
            $uac = $this->validate_action_access("{$this->action_code}DS");
            $data["allow_edit"] = $this->model->check_action_access("{$this->action_code}ES")->valid;
        } else {
            $uac = $this->validate_action_access("{$this->action_code}D");
            $data["allow_edit"] = $this->model->check_action_access("{$this->action_code}E")->valid;
        }
        $check = $this->model->get_detail($id);
        if ($check->success === TRUE && $check->found === TRUE) {
            $data['info'] = $check->data;
            $this->model->update_activity($uac->log_id, "{$check->data->admin_name}", "Admin/view_detail?id={$id}");
            $data["page"] = $page = $this->get_page_input($data, 1);
            $page = explode(",", $page);
            $data['sort'] = $sort = $this->get_sort_input($data, 'id');
            $data["search"] = $search = $this->get_custom_input("search", $data, "");
            $data["tab"] = $this->get_tab_input($data, 1);
            $activity_count = $this->model->get_activity_count($id, $search);
            $data["activity_pagination"] = $this->draw_pagination($page[0], $activity_count, "Admin/view_detail?id={$id}&search=$search", 10, 1);
            $data["activity_list"] = $this->model->get_activity_log_paged($id, $page[0], $search)->data;
            $this->navigate("{$this->module_view_folder_name}/detail", $data, $data['info']->{$this->detail_breadcrumb_title});
        } else {
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_list($data);
        }
    }

    public function view_add($data = [])
    {
        $this->validate_action_access("{$this->action_code}A", FALSE);
        $this->load->model("Position_model", "position_model");
        $data['admin_level'] = $this->position_model->get_list_all();
        $this->navigate("{$this->module_view_folder_name}/add", $data, "Add {$this->module_name}");
    }

    public function view_edit($data = [])
    {
        $id = $this->get_custom_input("id", $data, 0);
        if ($this->session->userdata("admin_id") == $id) {
            $this->validate_action_access("{$this->action_code}ES", false);
        } else {
            $this->validate_action_access("{$this->action_code}E", false);
        }
        $check = $this->model->get_detail($id);
        if ($check->success == TRUE && $check->found == TRUE) {
            $data['info'] = $check->data;
            $this->load->model("Position_model", "position_model");
            $data['admin_level'] = $this->position_model->get_list_all();
            $this->navigate("{$this->module_view_folder_name}/edit", $data, "Edit {$this->module_name}");
        } else {
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_list($data);
        }
    }

    public function process_add($data = [])
    {
        $uac = $this->validate_action_access("{$this->action_code}A");
        $input = (object) $this->input->post();
        $input->log_id = $uac->log_id;
        $check = $this->model->add($input);
        if ($check->success === TRUE) {
            $data['id'] = $check->id;
            $data['message'] = $this->draw_message($check->message, "success");
            $this->view_detail($data);
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_add($data);
        }
    }

    public function process_edit($data = [])
    {
        $id = $this->get_custom_input("admin_id", $data, 0);
        if ($this->session->userdata("admin_id") == $id) {
            $uac = $this->validate_action_access("{$this->action_code}ES");
        } else {
            $uac = $this->validate_action_access("{$this->action_code}E");
        }
        $input = (object) $this->input->post();
        $input->log_id = $uac->log_id;
        $check = $this->model->update($input);
        $data['id'] = $check->id;
        if ($check->success === TRUE) {
            $data['message'] = $this->draw_message($check->message, "success");
            $this->view_detail($data);
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_edit($data);
        }
    }

    public function view_activity_report($data = [])
    {
        $this->validate_action_access("{$this->action_code}REPT");
        $data["start"] = $start = $this->get_custom_input("start", $data, date("Y-m-d", strtotime("-7 DAYS")));
        $data["end"] = $end = $this->get_custom_input("end", $data, date("Y-m-d"));
        $data["filter"] = $filter = $this->get_custom_input("filter", $data, "$start|$end|all|");
        $data["admin_list"] = $this->model->get_list_all();
        $data["activity_list"] = $this->model->get_admin_activity_list($filter);
        $this->navigate("{$this->module_view_folder_name}/activity_report", $data, "{$this->module_name} Activity Report");
    }
}
