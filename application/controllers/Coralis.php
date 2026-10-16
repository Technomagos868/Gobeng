<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coralis extends GS_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("public_model");
        $this->load->library('session');
        $this->load->library('email');
        $this->load->library('form_validation');
    }
    public function index($data = [])
    {
        $data["page_title"] = "Home Page - GonzCalendar";
        $data["css_list"] = ["home_content.css"];
        $this->load->view('Public/navbar', $data);
        $this->load->view('Public/home_content', $data);
        $this->load->view('Public/footer', $data);
    }

    public function list($data = [])
    {
        $data["page_title"] = "List Siswa - GonzCalendar";
        $data["css_list"] = ["list.css"];
        $data["js_list"] = ["list.js"];
        $this->load->model("Students_model", "students_model");
        $data["student_list"] = $this->students_model->get_list_all();
        $this->load->view('Public/navbar', $data);
        $this->load->view("Public/list", $data);
        $this->load->view('Public/footer', $data);
    }

    public function lobby()
    {
        $data["page_title"] = "Lobby Page - GonzCalendar";
        $data["css_list"] = ["lobby.css"];
        $data["js_list"] = ["list.js"];
        $this->load->view('Public/navbar', $data);
        $this->load->view('Public/lobby_content', $data);
        $this->load->view('Public/footer', $data);

    }
    public function tentang()
    {
        $data["css_list"] = ["tentang.css"];
        $data["page_title"] = "Tentang - GonzCalendar";
        $this->load->view('Public/navbar', $data);
        $this->load->view('Public/tentang', $data);
        $this->load->view('Public/footer', $data);
    }
    public function get_started($data = [])
    {
        $this->load->model("Classroom_model", "classroom_model");
        $data["class_list"] = $this->classroom_model->get_list_all();
        $this->load->view('Public/navbar', $data);
        $this->load->view("Public/signup");
        $this->load->view('Public/footer', $data);
    }
    public function login($data = [])
    {
        $this->load->view('Public/navbar', $data);
        $this->load->view("public/signin");
        $this->load->view('Public/footer', $data);
    }

    public function footer()
    {
        $this->load->view("Public/footer");
    }
    public function forgot()
    {
        $this->load->view("");
    }
    public function submit()
    {
        $this->load->view("Public/lobby");
    }
    public function home()
    {
        $this->load->view("Public/home_content");
    }

    //FORGOT PASSWORD START
    public function view_forgot_password($data = [])
    {
        $this->load->view('public_forgot_password', $data);
    }

    public function process_request_reset($data = [])
    {
        $input = (object) $this->input->post();
        $this->form_validation->set_rules('admin_email', 'Email', 'required|trim|valid_email');
        if ($this->form_validation->run() == TRUE) {
            $check = $this->public_model->reset_authentication($input);
            $data["message_response"] = $check->message;
            $this->view_forgot_password($data);
        } else {
            $data["bounced"] = $input;
            $this->view_forgot_password($data);
        }
    }

    public function password_reset($data = [])
    {
        $token = $this->input->get("token");
        $check = $this->public_model->get_password_reset_info($token);
        if ($check->success === TRUE && $check->valid === TRUE) {
            $data["valid"] = TRUE;
        } else {
            $data["valid"] = FALSE;
        }
        $data["message"] = $check->message;
        $data["token"] = $token;
        $this->load->view("public_reset_password", $data);
    }

    public function process_password_reset($data = [])
    {
        $input = (object) $this->input->post();
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]|trim');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required|min_length[6]|trim|matches[new_password]', ['matches' => 'Passwords you entered are not the same.']);
        if ($this->form_validation->run() == TRUE) {
            $check = $this->public_model->change_password_via_reset($input);
            if ($check->success === TRUE) {
                $this->session->set_flashdata('message_response', $check->message);
                redirect('gemstone');
            } else {
                $data["valid"] = TRUE;
                $data["bounced"] = $input;
                $data["message_response"] = $check->message;
                $this->load->view("public_reset_password", $data);
            }
        } else {
            $data["valid"] = TRUE;
            $data["bounced"] = $input;
            $this->load->view("public_reset_password", $data);
        }
    }
    //FORGOT PASSWORD END

}
