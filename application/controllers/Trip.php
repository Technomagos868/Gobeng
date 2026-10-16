<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Trip extends GS_Controller
{
    var $module_name = "trip";
    var $module_view_folder_name = "trip";
    var $model_name = "trip_model";
    var $action_code = "03-001";
    var $detail_breadcrumb_title = "host_name"; 

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
            $trip_count = $this->model->get_trip_count($id);
            $data["passenger_list"] = $this->model->get_passenger_list_by_trip_id($id);
            $data["pagination"] = $this->draw_pagination($page, $trip_count, "Trip/view_detail?id={$id}", 10, 0);
            $this->model->update_activity($uac->log_id, "{$data['info']->{$this->detail_breadcrumb_title}}", "Trip/view_detail?id={$id}");
            $this->navigate("{$this->module_view_folder_name}/detail", $data, $data['info']->{$this->detail_breadcrumb_title});
        } else {
            $data['message'] = $this->draw_message($check->message, "error");
            $this->view_list($data);
        }
    }

    public function view_add($data = [])
    {
        $this->validate_action_access("{$this->action_code}A", FALSE);
        $this->load->model("Ridetype_model", "ridetype_model");
        $data['list_ride_type'] = $this->ridetype_model->get_list_all();
        $this->navigate("{$this->module_view_folder_name}/add", $data, "Add {$this->module_name}");
    }

    public function view_edit($data = [])
    {
        $this->validate_action_access("{$this->action_code}E", FALSE);
        $id = $this->get_custom_input("id", $data, 0);
        $check = $this->model->get_detail($id);
        if ($check->success == TRUE && $check->found == TRUE) {
            $data['info'] = $check->data;
            $this->load->model("Ridetype_model", "ridetype_model");
            $data['list_ride_type'] = $this->ridetype_model->get_list_all();
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
        $data['id'] = $input->trip_id;
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
