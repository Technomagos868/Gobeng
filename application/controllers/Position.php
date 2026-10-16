<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Position extends GS_Controller
{
    var $module_name = "Position";
    var $module_view_folder_name = "Position";
    var $model_name = "Position_model";
    var $action_code = "00-015";
    var $detail_breadcrumb_title = "level_title";

    public function __construct()
    {
        parent::__construct();
        $this->load->model($this->model_name, "model");
    }

    public function view_list($data = [])
    {
        $this->validate_action_access("{$this->action_code}");
        $data["allow_add"] = $this->model->check_action_access("{$this->action_code}A")->valid;
        $data["allow_detail"] = $this->model->check_action_access("{$this->action_code}D")->valid;
        $data['page'] = $page = $this->get_page_input($data, 1);
        $data['sort'] = $sort = $this->get_sort_input($data, 'id');
        $data["search"] = $search = $this->get_custom_input("search", $data, "");
        $count = $this->model->get_count($search);
        $data['pagination'] = $this->draw_pagination($page, $count, "{$this->module_view_folder_name}/view_list?sort=$sort&search=$search");
        $data['list'] = $this->model->get_list_paged($page, $sort, $search);
        $this->navigate("{$this->module_view_folder_name}/list", $data, "{$this->module_name} List");
    }

    public function view_detail($data = [])
    {
        $uac = $this->validate_action_access("{$this->action_code}D");
        $data["allow_edit"] = $this->model->check_action_access("{$this->action_code}E")->valid;
        $data["allow_grant"] = $this->model->check_action_access("{$this->action_code}GRN")->valid;
        $data["allow_revoke"] = $this->model->check_action_access("{$this->action_code}RVK")->valid;
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success === TRUE && $check->found === TRUE) {
            $data['info'] = $check->data;
            $this->gemstone_model->update_activity($uac->log_id, $check->data->level_title, "Position/view_detail?id={$id}");
            $data["page"] = $page = $this->get_page_input($data, "1,1");
            $page = explode(",", $page);
            $data["sort"] = $sort = $this->get_sort_input($data, "id|asc");
            $data["search"] = $search = $this->get_custom_input("search", $data, "");
            $data["admin_list"] = $this->model->get_admin_list_paged($id, $page[0], $sort)->data;
            $admin_count = $this->model->get_admin_count($id);
            $data["admin_pagination"] = $this->draw_pagination($page, $admin_count, "Position/view_detail?id={$id}", 10, 0);
            $this->load->model("Action_model", "action_model");
            // var_dump($page[1]);
            $data["action_list"] = $this->action_model->get_access_by_position_paged($id, $page[1], $sort, $search)->data;
            $action_count = $this->action_model->get_count($search);
            $data["action_pagination"] = $this->draw_pagination($page, $action_count, "Position/view_detail?id={$id}", 10, 1);
            $this->navigate("{$this->module_view_folder_name}/detail", $data, $data['info']->{$this->detail_breadcrumb_title});
        } else {
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_list($data);
        }
    }

    public function view_add($data = [])
    {
        $this->validate_action_access("{$this->action_code}A", FALSE);
        $this->load->model("Division_model", "division_model");
        $data["division_list"] = $this->division_model->get_list_all();
        $this->navigate("{$this->module_view_folder_name}/add", $data, "Add {$this->module_name}");
    }

    public function view_edit($data = [])
    {
        $this->validate_action_access("{$this->action_code}E", FALSE);
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success == TRUE && $check->found == TRUE) {
            $data['info'] = $check->data;
            $this->load->model("Division_model", "division_model");
            $data["division_list"] = $this->division_model->get_list_all();
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
        $uac = $this->validate_action_access("{$this->action_code}E");
        $input = (object) $this->input->post();
        $input->log_id = $uac->log_id;
        $check = $this->model->update($input);
        if ($check->success === TRUE) {
            $data['id'] = $check->id;
            $data['message'] = $this->draw_message($check->message, "success");
            $this->view_detail($data);
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_edit($data);
        }
    }
    public function grant_action_access($data = [])
    {
        $uac = $this->validate_action_access("{$this->action_code}GRN");
        $input = (object) $this->input->get();
        $input->log_id = $uac->log_id;
        $data['id'] = $input->position_id;
        $check = $this->model->grant_action_access($input);
        if ($check->success === TRUE) {
            $data['message'] = $this->draw_message($check->message, "success");
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
        }
        $this->view_detail($data);
    }
    public function revoke_action_access($data = [])
    {
        $uac = $this->validate_action_access("{$this->action_code}RVK");
        $input = (object) $this->input->get();
        $input->log_id = $uac->log_id;
        $data['id'] = $input->position_id;
        $check = $this->model->revoke_action_access($input);
        if ($check->success === TRUE) {
            $data['message'] = $this->draw_message($check->message, "success");
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
        }
        $this->view_detail($data);
    }
}
