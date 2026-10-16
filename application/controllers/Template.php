<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Template extends GS_Controller
{
    var $module_name = "Template";
    var $module_view_folder_name = "Template";
    var $model_name = "Template_model";
    var $action_code = "00-000";
    var $detail_breadcrumb_title = "template_name";

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
        $data['pagination'] = $this->draw_pagination($page, $count, "view_list?sort=$sort&search=$search");
        $data['list'] = $this->model->get_list_paged($page, $sort, $search);
        $this->navigate("{$this->module_view_folder_name}/list", $data, "{$this->module_name} List");
    }

    public function view_detail($data = [])
    {
        $this->validate_action_access("{$this->action_code}D");
        $data["allow_edit"] = $this->model->check_action_access("{$this->action_code}E")->valid;
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success === TRUE && $check->found === TRUE) {
            $data['info'] = $check->data;
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
}
