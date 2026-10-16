<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Division extends GS_Controller
{
    var $module_name = "Division";
    var $module_view_folder_name = "Division";
    var $model_name = "Division_model";
    var $action_code = "00-021";
    var $detail_breadcrumb_title = "division_name";

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
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success === TRUE && $check->found === TRUE) {
            $data['info'] = $check->data;
            $data["page"] = $page = $this->get_page_input($data, "1, 1");
            $page = explode(",", $page);
            $data["sort"] = $sort = $this->get_sort_input($data, "id|asc");
            $data["search"] = $this->get_custom_input("search", $data, "");
            $data["position_list"] = $this->model->get_position_list_paged($id, $page[0], $sort)->data;
            $position_count = $this->model->get_position_count($id);
            $data["position_pagination"] = $this->draw_pagination($page, $position_count, "Division/view_detail?id={$id}", 10, 0);
            $data["admin_list"] = $this->model->get_admin_list_paged($id, $page[1], $sort)->data;
            $admin_count = $this->model->get_admin_count($id);
            $data["admin_pagination"] = $this->draw_pagination($page, $admin_count, "Division/view_detail?id={$id}", 10, 1);
            $this->model->update_activity($uac->log_id, "{$data['info']->{$this->detail_breadcrumb_title}}", "Division/view_detail?id={$id}");
            $this->navigate("{$this->module_view_folder_name}/detail", $data, $data['info']->{$this->detail_breadcrumb_title});
        } else {
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_list($data);
        }
    }

    public function view_add($data = [])
    {
        $this->validate_action_access("{$this->action_code}A", FALSE);
        $this->navigate("{$this->module_view_folder_name}/add", $data, "Add {$this->module_name}");
    }

    public function view_edit($data = [])
    {
        $this->validate_action_access("{$this->action_code}E", FALSE);
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success == TRUE && $check->found == TRUE) {
            $data['info'] = $check->data;
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
        $data['id'] = $input->division_id;
        $check = $this->model->update($input);
        if ($check->success === TRUE) {
            $data['message'] = $this->draw_message($check->message, "success");
            $this->view_detail($data);
        } else {
            $data['bounced'] = $input;
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_edit($data);
        }
    }
}
