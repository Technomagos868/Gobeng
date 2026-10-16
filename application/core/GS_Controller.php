<?php

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================

defined('BASEPATH') || exit('No direct script access allowed');
require_once "gemstone_config.php";

class GS_Controller extends CI_Controller
{

    private $css_list = [];
    private $js_list = [];
    private $module_list = [];
    private $spa_lock = true;

    //last update 3.1.4
    public function __construct()
    {
        parent::__construct();
        $this->output->set_header('X-Frame-Options: DENY');
        $this->load->model('gemstone_model');
        $this->check_certificate();
        $this->add_module("GSResponse", "third_party/Coralis/Gemstone");
        $this->add_module("GSFile", "third_party/Coralis/Gemstone");
        $this->load_module("GSResponse");
        $this->load_module("GSFile");
        //===============================================================
        //Run once at the start of the development then remove this code.
        //===============================================================
        //$this->gemstone_model->initialize_admin();
        //===============================================================
    }

    //last update 3.9.6
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
            $data["allowed_view_dashboard"] = $this->gemstone_model->check_action_access("00-002")->valid;
            $data["allowed_admin_list"] = $this->gemstone_model->check_action_access("00-005")->valid;
            $data["allowed_gemstone_settings"] = $this->gemstone_model->check_action_access("00-013")->valid;
            $this->load->view("gemstone_home", $data);
        }
    }

    //CORE FUNCTIONS START
    //last update 3.9.1
    public function get_css_list()
    {
        return $this->css_list;
    }

    //last update 3.9.1
    public function get_js_list()
    {
        return $this->js_list;
    }

    //last update 3.9.2
    public function add_module($module_name, $module_path)
    {
        if (is_string($module_name) && is_string($module_path)) {
            $this->module_list[$module_name] = $module_path;
        }
    }

    //last update 3.7.1
    public function load_module($module_name, $auto_path = false)
    {
        if ($auto_path === true && !array_key_exists($module_name, $this->module_list)) {
            $this->add_module($module_name, "third_party/Coralis/" . $module_name);
        }
        foreach ($this->module_list as $name => $path) {
            if ($module_name == $name) {
                $this->load->add_package_path(APPPATH . $path);
                $model_file_path = APPPATH . $path . "/models/" . $name . "_model.php";
                if (file_exists($model_file_path)) {
                    $this->load->model(strtolower($name) . "_model");
                } else {
                    log_message("debug", "FILE NOT FOUND: " . $model_file_path);
                }
                $lib_file_path = APPPATH . $path . "/libraries/" . $name . ".php";
                if (file_exists($lib_file_path)) {
                    $this->load->library($name);
                } else {
                    log_message("debug", "FILE NOT FOUND: " . $lib_file_path);
                }
                $css_file_path = APPPATH . $path . "/assets/css/" . strtolower($name) . ".css";
                if (file_exists($css_file_path)) {
                    $asset_path = APPPATH . "../assets/css/" . strtolower($name) . ".css";
                    if (!file_exists($asset_path)) {
                        $copy_status = copy($css_file_path, $asset_path);
                    } else {
                        $copy_status = true;
                    }
                    if ($copy_status) {
                        $this->css_list[] = "/assets/css/" . strtolower($name) . ".css";
                    }
                } else {
                    log_message("debug", "FILE NOT FOUND: " . $css_file_path);
                }
                $js_file_path = APPPATH . $path . "/assets/js/" . strtolower($name) . ".js";
                if (file_exists($js_file_path)) {
                    $asset_path = APPPATH . "../assets/js/" . strtolower($name) . ".js";
                    if (!file_exists($asset_path)) {
                        $copy_status = copy($js_file_path, $asset_path);
                    } else {
                        $copy_status = true;
                    }
                    if ($copy_status) {
                        $this->js_list[] = "/assets/js/" . strtolower($name) . ".js";
                    }
                } else {
                    log_message("debug", "FILE NOT FOUND: " . $js_file_path);
                }
            }
        }
    }

    //last update 3.2
    public function draw_message($message, $type)
    {
        if ($type != "error" && $type != "success") {
            $type = "error";
        }
        return "<div class='gs-message $type'>$message</div>";
    }

    //last update 3.2.3
    public function draw_quicksearch($content, $button = false, $action = false, $img_path = false)
    {
        $response = "";
        if ($action !== false) {
            $response .= "<div class='gs-qs-item' onclick='$action'>";
        } else {
            $response .= "<div class='gs-qs-item'>";
        }
        if ($img_path !== false && $img_path != "") {
            $response .= "<img src='$img_path' class='gs-qs-image'>";
        }
        if (strlen($content ?? '') > 50) {
            $content = substr($content, 0, 50) . "..";
        }
        $response .= $content;
        if ($button !== false) {
            $response .= "<span class='gs-qs-button add'></span>";
        }
        $response .= "</div>";
        return $response;
    }

    //last update 3.9.0
    public function draw_pagination($current, $count, $link, $range = 10, $tab_index = 1)
    {
        $page = null;
        if ($tab_index > 1) {
            $tab_index = 1;
        }
        $tab_label = $tab_index;
        $result = "";
        if (is_array($current) || strpos($current, ",") !== false) {
            if (!is_array($current)) {
                $current = explode(",", $current);
            }
            $page_array = $current;
            $page = $page_array[$tab_index];
        } else {
            $page = $current;
        }
        $current = (int) $this->gemstone_model->check_numeric($page);
        $ipp = $this->gemstone_model->get_item_per_page();
        $lastPage = ceil($count / $ipp);

        $start_index = (($current - 1) * $ipp) + 1;
        $end_index = $current * $ipp;
        if ($end_index > $count) {
            $end_index = $count;
        }
        if ($count == 0) {
            $start_index = 0;
        }
        $result .= "<div class='gs-pagination-stat'>Showing item $start_index - $end_index of $count items</div>";
        $result .= "<div class='gs-pagination'>Page: ";
        $bridge = "?";
        if (strpos($link, '?') !== false) {
            $bridge = "&";
        }
        if ($current != 1) {
            $page_array[$tab_index] = 1;
            $page_label = implode(",", $page_array);
            $result .= "<div class='gs-pagination-button' onclick='gsNavigate(\"$link" . $bridge . "p=$page_label&tab=$tab_label\")'>First</div>";
        }
        for ($i = $range; $i > 0; $i--) {
            $j = $current - $i;
            if ($j > 0 && $j != $current) {
                $page_array[$tab_index] = $j;
                $page_label = implode(",", $page_array);
                $result .= "<div class='gs-pagination-button' onclick='gsNavigate(\"$link" . $bridge . "p=$page_label&tab=$tab_label\")'>$j</div>";
            }
        }
        $result .= "<div class='gs-pagination-current'>$current</div> ";
        for ($i = 0; $i < $range; $i++) {
            $j = $current + $i;
            if ($j <= $lastPage && $j != $current) {
                $page_array[$tab_index] = $j;
                $page_label = implode(",", $page_array);
                $result .= "<div class='gs-pagination-button' onclick='gsNavigate(\"$link" . $bridge . "p=$page_label&tab=$tab_label\")'>$j</div>";
            }
        }
        if ($current != $lastPage && $lastPage > 1) {
            $page_array[$tab_index] = $lastPage;
            $page_label = implode(",", $page_array);
            $result .= "<div class='gs-pagination-button' onclick='gsNavigate(\"$link" . $bridge . "p=$page_label&tab=$tab_label\")'>Last</div>";
        }
        $result .= "</div>";

        return $result;
    }

    //last update 3.2
    public function draw_popup($title, $message, $type, $button_text)
    {
        if ($type != "error" && $type != "success" && $type != "plain") {
            $type = "plain";
        }

        $html = "<div class='gs-popup-overlay $type'>";
        $html .= "<div class='gs-popup-container'>";
        $html .= "<div class='gs-popup-title $type'>";
        $html .= $title;
        $html .= "</div>";
        $html .= "<div class='gs-popup-message'>";
        $html .= $message;
        $html .= "</div>";
        $html .= "<div class='gs-popup-close' onclick='gsClosePopup()'>$button_text</div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    //last update 2.5.2
    public function generate_anti_cache_value()
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = strlen($characters);
        $random_string = "";
        for ($i = 0; $i < 26; $i++) {
            $random_string .= $characters[rand(0, $length - 1)];
        }
        return $random_string;
    }

    //last update 3.3.1
    public function set_breadcrumb($title = false, $clean_start = false)
    {
        $CI = &get_instance();
        $break_down_url = $CI->config->site_url($CI->uri->uri_string());
        $break_down_url = explode("/", $break_down_url);
        $url = $break_down_url[count($break_down_url) - 1];
        if ($_SERVER['QUERY_STRING']) {
            $link = $url . '?' . $_SERVER['QUERY_STRING'];
        } else {
            $link = $url;
        }
        return $this->push_breadcrumb($link, $title, $clean_start);
    }

    //last update 3.3.1
    public function push_breadcrumb($link = false, $title = false, $clean_start = false)
    {
        $html = "";
        $tab_token = $this->input->post("tabToken");
        $breadcrumb = $this->session->userdata("breadcrumb");
        if ($link == "view_last_page") {
            $current_tab = $breadcrumb[$tab_token];
            $link = $current_tab[count($current_tab) - 2]->link;
            array_pop($current_tab);
        }
        if ($clean_start === true) {
            $breadcrumb[$tab_token] = [];
            $this->session->set_userdata("breadcrumb", $breadcrumb);
        }
        if (!isset($breadcrumb[$tab_token]) && !empty($this->session->last_page)) {
            $last_page = $this->session->last_page;
            $param_string = explode("?", $last_page);
            $function_name = $param_string[0];
            $parameter = [];
            if (count($param_string) > 1) {
                $pair_list = explode("&", $param_string[1]);
                for ($i = 0; $i < count($pair_list); $i++) {
                    $pair = explode("=", urldecode($pair_list[$i]));
                    $parameter[$pair[0]] = $pair[1];
                }
            }
            $link = $function_name;
            $crumb = new stdClass();
            $crumb->link = $link;
            if ($title === false) {
                $title = $link;
            }
            $crumb->title = $title;
            $crumb->parameter = $parameter;
            $initial = [];
            array_push($initial, $crumb);
            $breadcrumb[$tab_token] = $initial;
        }
        if ($link !== false) {
            $param_string = explode("?", $link);
            $function_name = $param_string[0];
            $parameter = [];
            if (count($param_string) > 1) {
                $pair_list = explode("&", $param_string[1]);
                for ($i = 0; $i < count($pair_list); $i++) {
                    $pair = explode("=", urldecode($pair_list[$i]));
                    $parameter[$pair[0]] = $pair[1];
                }
            }
            $link = $function_name;

            if (!empty($breadcrumb[$tab_token])) {
                $index = array_search($link, array_column($breadcrumb[$tab_token], "link"));
                if ($index !== false) {
                    $breadcrumb[$tab_token] = array_splice($breadcrumb[$tab_token], 0, $index);
                }
            }
            $crumb = new stdClass();
            $crumb->link = $link;
            if ($title === false) {
                $title = $link;
            }
            $crumb->title = $title;
            $crumb->parameter = $parameter;
            $lastCrumb = $breadcrumb[$tab_token];
            array_push($lastCrumb, $crumb);
            $breadcrumb[$tab_token] = $lastCrumb;
            $this->session->set_userdata("breadcrumb", $breadcrumb);
        }
        $html = $this->draw_breadcrumb();

        $first_page = $this->input->get("first_page");
        if (!$first_page) {
            $this->set_last_page();
        }
        return $html;
    }

    // last update 3.10.5
    public function set_last_page()
    {
        $breadcrumb = $this->session->breadcrumb;
        $tab_token = $this->input->post("tabToken");
        $last_page = end($breadcrumb[$tab_token]);
        $page = $last_page->link;

        $parameter = "";
        $delimiter = "?";
        foreach ($last_page->parameter as $key => $value) {
            $parameter .= "$delimiter$key=$value";
            $delimiter = "&";
        }

        $now_page = "$page$parameter";
        $this->session->set_userdata("last_page", $now_page);
    }

    //last update 3.1.1
    public function draw_breadcrumb()
    {
        $html = "<div class='gs-breadcrumb-container'>";
        $main_breadcrumb = $this->session->userdata("breadcrumb");
        $tab_token = $this->input->post("tabToken");
        if (isset($main_breadcrumb[$tab_token])) {
            $breadcrumb = $main_breadcrumb[$tab_token];
            if ($breadcrumb !== false && is_array($breadcrumb)) {
                if (count($breadcrumb) > 1) {
                    $html .= "<span class='gs-breadcrumb-back-button' onclick='Gemstone.back()'></span>";
                }
                $delimiter = "";
                foreach ($breadcrumb as $crumb) {
                    if (count($crumb->parameter) > 0) {
                        $param_string = "?";
                        $separator = "";
                        foreach ($crumb->parameter as $key => $value) {
                            $param_string .= $separator . $key . "=" . $value;
                            $separator = "&";
                        }
                        $html .= $delimiter . "<span class='gs-breadcrumb-link' onclick='Gemstone.navigate(\"$crumb->link$param_string\")'>$crumb->title</span>";
                    } else {
                        $html .= $delimiter . "<span class='gs-breadcrumb-link' onclick='Gemstone.navigate(\"$crumb->link\")'>$crumb->title</span>";
                    }
                    $delimiter = "<span>></span>";
                }
            }
        }
        $html .= "</div>";
        return $html;
    }

    //last update 2.6
    public function clear_breadcrumb()
    {
        $this->session->unset_userdata("breadcrumb");
    }

    //last update 3.1.1
    public function view_last_page($data = [])
    {
        $main_breadcrumb = $this->session->userdata("breadcrumb");
        $tab_token = $this->input->post("tabToken");
        if (isset($main_breadcrumb[$tab_token])) {
            $breadcrumb = $main_breadcrumb[$tab_token];
            if (count($breadcrumb) > 1) {
                $trackback = 2;
            } else {
                $trackback = 1;
            }
            $crumb = $breadcrumb[count($breadcrumb) - $trackback];
            $function_name = $crumb->link;
            $link = explode("/", $crumb->link);
            if ($link[0] == "GS_Controller") {
                $function_name = $link[1];
            }
            $parameter = $crumb->parameter;
            // $index = 0;
            // foreach ($parameter as $key => $value) {
            //     if ($index == 0) {
            //         $function_name .= "?$key=$value";
            //     } else {
            //         $function_name .= "&$key=$value";
            //     }
            //     $index++;
            // }
        } else {
            $function_name = "view_dashboard";
            $parameter = [];
        }
        echo $this->$function_name();
    }

    //last update 2.7.1
    public function mark_active_tab($tab_index)
    {
        $tab_index = $this->gemstone_model->check_numeric($tab_index);
        return "<div class='gs-tab-helper' active-tab='$tab_index'></div>";
    }

    //last update 3.1.3
    public function update_tab()
    {
        $tab = (int) $this->input->post("tab");
        $tab_token = $this->input->post("tabToken");
        $main_breadcrumb = $this->session->userdata("breadcrumb");
        $check = $main_breadcrumb[$tab_token];
        if (!empty($check)) {
            $breadcrumb = $check;
            $count = count($breadcrumb);
            $new_crumb = $breadcrumb[$count - 1];
            $new_crumb->parameter["tab"] = $tab;
            $breadcrumb = array_splice($breadcrumb, 0, $count);
            $breadcrumb[] = $new_crumb;
        }
    }

    //last update 3.1.3
    public function get_page_input($data = [], $default = 1)
    {
        $found = false;
        $page = null;
        if ($data !== FALSE) {
            if (array_key_exists("p", $data)) {
                $page = $data["p"];
                $found = TRUE;
            }
            if (array_key_exists("page", $data)) {
                $page = $data["page"];
                $found = TRUE;
            }
        }
        if ($found === false) {
            $page = $this->input->get_post("p");
            if (empty($page)) {
                $page = $this->input->get_post("page");
                if (!empty($page)) {
                    $found = true;
                }
            } else {
                $found = true;
            }
        }
        if ($found) {
            if ($page) {
                return $page;
            } else {
                return $default;
            }
        } else {
            return $default;
        }
    }

    //last update 3.1.3
    public function get_tab_input($data = [], $default = 1)
    {
        $found = false;
        $tab = null;
        if (!empty($data) && array_key_exists("tab", $data)) {
            $tab = $data["tab"];
            $found = true;
        }
        if ($found === false) {
            $tab = $this->input->get_post("tab");
            if (!empty($tab)) {
                $found = true;
            }
        }
        if ($found) {
            if (is_numeric($tab)) {
                return $tab;
            } else {
                return $default;
            }
        } else {
            return $default;
        }
    }

    //last update 3.1.3
    public function get_sort_input($data = [], $default = "id|asc")
    {
        $found = false;
        $sort = null;
        if (!empty($data) && array_key_exists("sort", $data)) {
            $sort = $data["sort"];
            $found = true;
        }
        if ($found === false) {
            $sort = $this->input->get_post("sort");
            if (!empty($sort)) {
                $found = true;
            }
        }
        if ($found) {
            return $sort;
        } else {
            return $default;
        }
    }

    //last update 3.2.2
    public function get_custom_input($key, $data = [], $default = "1")
    {
        $found = false;
        $input = null;
        if (!empty($data)) {
            if (is_array($data)) {
                if (array_key_exists($key, $data)) {
                    $input = $data[$key];
                    $found = true;
                }
            } elseif (is_object($data)) {
                $converted = (array) $data;
                if (array_key_exists($key, $converted)) {
                    $input = $converted[$key];
                    $found = true;
                }
            }
        }
        if ($found === false) {
            $input = $this->input->get_post($key);
            if (!empty($input)) {
                $found = true;
            }
        }
        if ($found) {
            return $input;
        } else {
            return $default;
        }
    }

    //last update 3.1.4
    public function reload_current_page($data = [])
    {
        $main_breadcrumb = $this->session->userdata("breadcrumb");
        $tab_token = $this->input->post("tabToken");
        $breadcrumb = $main_breadcrumb[$tab_token];
        if (!empty($breadcrumb)) {
            $crumb = $breadcrumb[count($breadcrumb) - 1];
            $function_name = $crumb->link;
            $parameter = $crumb->parameter;
        } else {
            $function_name = "view_dashboard";
            $parameter = [];
        }
        if (!empty($data)) {
            $passed_data = [$data, $parameter];
        } else {
            $passed_data = [$parameter];
        }
        call_user_func_array(array($this, $function_name), $passed_data);
    }

    //last update 3.2.2
    public function download()
    {
        $filename = $this->input->get("filename");
        $path = $this->input->get("path");
        if ($filename != "" && $path != "") {
            header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
            header("Content-Type: application/force-download");
            header("Content-Length: " . filesize($path));
            header("Connection: close");
        }
    }

    //last update 3.5.4
    public function process_download_file($data = [])
    {
        $this->validate_access(false);
        $file_url = $this->get_custom_input("link", $data, false);
        if ($file_url !== false) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_url));
            echo file_get_contents($file_url);
            exit;
        }
    }

    //last update 3.5.4
    public function process_system_search($data = [])
    {
        $this->validate_access();
        $data["keyword"] = $keyword = $this->get_custom_input("keyword", $data, false);
        if ($keyword !== false) {
            $data["result"] = $this->gemstone_model->system_search($keyword);
        }
        $tab = $this->get_tab_input($data);
        $data["active_tab"] = $this->mark_active_tab($tab);
        $data["breadcrumb"] = $this->push_breadcrumb("process_system_search?tab=$tab&keyword=$keyword", "Search Result: $keyword");
        $this->load->view("gemstone_system_search", $data);
    }

    //last update 3.9.4
    public function extend_session()
    {
        $this->validate_action_access("00-001");
        $raw_saved_data = $this->get_custom_input("form", false, false);
        $method = $this->get_custom_input("method", false, false);
        if ($raw_saved_data !== false && $method !== false) {
            $saved_data = json_decode($raw_saved_data);
            $this->session->set_userdata($method, $saved_data);
        }
        echo "Session extended.";
    }

    public function get_autosave_data($method)
    {
        return $this->session->userdata($method);
    }

    //last update 3.9.4
    public function set_spa_lock($new_status)
    {
        if ($new_status === false || $new_status == 0 || $new_status === "0") {
            $this->spa_lock = false;
        } else {
            $this->spa_lock = true;
        }
    }

    //last update 3.9.3
    public function navigate($page_name, $data, $crumbs_title = false, $clear_crumbs = false)
    {
        if ($crumbs_title === false) {
            $crumbs_title = ucwords(str_replace("_", "", $page_name));
        }
        $class = debug_backtrace()[1]["class"];
        if ($class == "GS_Controller") {
            $class = "Gemstone";
        }
        $function = $class . "/" . debug_backtrace()[1]["function"];
        $caller_function_name = $function;
        $crumb_link = "{$caller_function_name}";
        $delimiter = "?";
        foreach ($_GET as $key => $value) {
            $crumb_link .= $delimiter . "{$key}=" . urlencode($value) . "";
            $delimiter = "&";
        }
        $id = $this->get_custom_input("id", $data, false);
        if ($id !== false) {
            $crumb_link .= $delimiter . "id=" . $id;
            $delimiter = "&";
        }
        $page = $this->get_page_input($data, false);
        if ($page !== false) {
            $crumb_link .= $delimiter . "p=" . $page;
            $delimiter = "&";
        }
        $sort = $this->get_sort_input($data, false);
        if ($sort !== false) {
            $crumb_link .= $delimiter . "sort=" . $sort;
            $delimiter = "&";
        }
        $tab = $this->get_tab_input($data, false);
        if ($tab !== false) {
            $crumb_link .= $delimiter . "tab=" . $tab;
            $delimiter = "&";
        }
        $search = $this->get_custom_input("search", $data, false);
        if ($search !== false) {
            $crumb_link .= $delimiter . "search=" . urlencode($search);
        }
        $data["active_tab"] = $this->mark_active_tab($tab);
        $data['breadcrumb'] = $this->push_breadcrumb($crumb_link, $crumbs_title, $clear_crumbs);
        $this->load->view($page_name, $data);
    }

    //CORE FUNCTIONS END
    //DASHBOARD AND SETTINGS START
    //last update 3.9.4
    public function view_dashboard($data = [])
    {
        $this->validate_action_access("00-002");
        $this->load->model("Admin_model", "admin_model");
        $data["admin_count"] = $this->admin_model->get_count();
        $data["my_activity"] = $this->gemstone_model->get_my_recent_activity_count();
        $data["online_admin"] = $this->gemstone_model->get_online_admin_list();
        $this->navigate('dashboard', $data, "Dashboard", true);
    }

    //last update 3.9.4
    public function view_gemstone_settings($data = [])
    {
        $this->validate_action_access("00-013");
        $data["settings"] = $this->gemstone_model->get_gemstone_settings();
        $this->navigate('gemstone_settings', $data, "Settings", true);
    }

    //last update 3.4.3
    public function process_update_gemstone_settings()
    {
        $this->validate_action_access("00-014");
        $input = (object) $this->input->post();
        $check = $this->gemstone_model->update_gemstone_settings($input);
        if ($check->success === true) {
            $data["message"] = $this->draw_message($check->message, "success");
        } else {
            $data["message"] = $this->draw_message($check->message, "error");
        }
        $this->view_gemstone_settings($data);
    }

    //DASHBOARD AND SETTINGS END
    //UAC START
    //LOGIN START
    //last update 3.9.4
    public function login()
    {
        if (is_numeric($this->session->userdata("admin_id"))) {
            $this->gemstone_model->logout();
        }
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $login_check = $this->gemstone_model->check_login($username, $password);
        $maintenance_check = $this->gemstone_model->get_gemstone_settings()->is_maintenance;
        if ($login_check->success === true && ($maintenance_check == 0 || $login_check->admin_tier_id == 1)) {
            $session_check = $this->gemstone_model->check_session_token($login_check->admin_id);
            if ($session_check->valid === true) {
                if ($this->gemstone_model->check_action_access("00-001")->valid === true) {
                    redirect('gemstone');
                } else {
                    $data['error'] = "Your account is not authorized to login right now. Contact system administrator for detail.";
                    $this->load->view('gemstone_login', $data);
                }
            } else {
                $this->session->unset_userdata("admin_id");
                $this->session->unset_userdata("admin_name");
                $this->session->unset_userdata("position_id");
                $this->session->unset_userdata("admin_tier_id");
                $this->session->unset_userdata("admin_level");
                $this->session->unset_userdata("admin_title");
                $this->session->unset_userdata("division_id");
                $this->session->unset_userdata("division_name");
                $data['error'] = $session_check->message;
                $this->load->view('gemstone_login', $data);
            }
        } else {
            $data['error'] = $login_check->message;
            if ($login_check->success === true && $maintenance_check == 1) {
                $data['error'] = "System is under maintenance. Only Super admin is allowed to login.";
            }
            $this->load->view('gemstone_login', $data);
        }
    }

    //last checked 3.9.4
    public function expired()
    {
        $this->session->unset_userdata("admin_id");
        $this->session->unset_userdata("admin_name");
        $this->session->unset_userdata("position_id");
        $this->session->unset_userdata("admin_tier_id");
        $this->session->unset_userdata("admin_level");
        $this->session->unset_userdata("admin_title");
        $this->session->unset_userdata("division_id");
        $this->session->unset_userdata("division_name");
        $data['error'] = "Your session has is expired. Please login again to continue";
        $this->load->view('gemstone_login', $data);
    }

    //last update 3.1.5
    public function logout()
    {
        $this->gemstone_model->logout();
        redirect('admin');
    }

    //last update 3.1.5
    public function force_logout($data = [])
    {
        $log_id = $this->validate_action_access("00-004")->log_id;
        $data["id"] = $target = $this->get_custom_input("id", $data, 0);
        $this->gemstone_model->logout($target, $log_id);
        $this->view_admin_detail($data);
    }

    //LOGIN END
    //last update 3.9.3
    public function view_uac_management($data = [])
    {
        $this->validate_action_access("00-015");
        $data['page'] = $page = $this->get_page_input($data, "1, 1");
        $page = explode(",", $page);
        $data['sort'] = $sort = $this->get_sort_input($data);
        $data["allow_add_position"] = $this->gemstone_model->check_action_access("00-016")->valid;
        $data["allow_view_position"] = $this->gemstone_model->check_action_access("00-017")->valid;
        $data["allow_add_division"] = $this->gemstone_model->check_action_access("00-021")->valid;
        $data["allow_view_division"] = $this->gemstone_model->check_action_access("00-022")->valid;
        $position_count = $this->gemstone_model->get_position_count();
        $division_count = $this->gemstone_model->get_division_count();
        $data['position_list'] = $this->gemstone_model->get_position_list_paged($page[0], $sort);
        $data['division_list'] = $this->gemstone_model->get_division_list_paged($page[1], $sort);
        $data['position_pagination'] = $this->draw_pagination($page, $position_count, "view_uac_management?sort=" . $sort, 10, 0);
        $data['division_pagination'] = $this->draw_pagination($page, $division_count, "view_uac_management?sort=" . $sort, 10, 1);
        $this->navigate("gemstone_uac_management", $data, "UAC Management");
    }

    //POSITION START
    //last update 3.9.3
    public function view_position_add($data = [])
    {
        $this->validate_action_access("00-016", false);
        $data["division_list"] = $this->gemstone_model->get_all_division_list();
        $this->navigate("gemstone_position_add", $data, "Add Position");
    }

    //last update 3.9.3
    public function view_position_detail($data = [])
    {
        $this->validate_action_access("00-017");
        $id = $this->get_custom_input("id", $data, false);
        if ($id !== false) {
            $check = $this->gemstone_model->get_position_detail($id);
            if ($check->success === true) {
                $data["position_detail"] = $check->data;
                $data["allow_edit_position"] = $this->gemstone_model->check_action_access("00-018")->valid;
                $data['page'] = $page = $this->get_page_input($data, "1");
                $page = explode(",", $page);
                $data['sort'] = $sort = $this->get_sort_input($data);
                $data['search'] = $search = $this->get_custom_input("search", $data, "");
                $admin_count = $this->gemstone_model->get_position_admin_count($id);
                $data['admin_list'] = $this->gemstone_model->get_position_admin_list_paged($id, $page[0], $sort);
                $data['access_list'] = $this->gemstone_model->get_position_access_list($id, $search);
                $data['pagination'] = $this->draw_pagination($page, $admin_count, "view_position_detail?id=$id&tab=1&sort=" . $sort);
                $this->navigate("gemstone_position_detail", $data, $data["position_detail"]->level_title);
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
                $this->view_uac_management($data);
            }
        } else {
            $data["message"] = $this->draw_message("Invalid position.", "error");
            $this->view_uac_management($data);
        }
    }

    //last update 3.9.3
    public function view_position_edit($data = [])
    {
        $this->validate_action_access("00-018", false);
        $id = $this->get_custom_input("id", $data, false);
        if ($id !== false) {
            $check = $this->gemstone_model->get_position_detail($id);
            if ($check->success === true) {
                $data["position_detail"] = $check->data;
                $data["division_list"] = $this->gemstone_model->get_all_division_list();
                $this->navigate("gemstone_position_edit", $data, "Edit");
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
                $this->view_uac_management($data);
            }
        } else {
            $data["message"] = $this->draw_message("Invalid position.", "error");
            $this->view_uac_management($data);
        }
    }

    //last update 3.9.3
    public function process_position_add()
    {
        $this->validate_action_access("00-016");
        $input = (object) $this->input->post();
        $check = $this->gemstone_model->add_position($input);
        if ($check->success === true) {
            $data["message"] = $this->draw_message($check->message, "success");
            $data["id"] = $check->id;
            $this->view_position_detail($data);
        } else {
            $data["bounced"] = $input;
            $data["message"] = $this->draw_message($check->message, "error");
            $this->navigate("gemstone_position_add", $data, "Add Position");
        }
    }

    //last update 3.9.3
    public function process_position_edit()
    {
        $this->validate_action_access("00-018");
        $input = (object) $this->input->post();
        $data["id"] = $input->admin_tier_id;
        $check = $this->gemstone_model->edit_position($input);
        if ($check->success === true) {
            $data["message"] = $this->draw_message($check->message, "success");
            $this->view_position_detail($data);
        } else {
            $data["message"] = $this->draw_message($check->message, "error");
            $this->view_position_edit($data);
        }
    }

    //POSITION END
    //DIVISION START
    //last update 3.9.3
    public function view_division_add($data = [])
    {
        $this->validate_action_access("00-021", false);
        $this->navigate("gemstone_division_add", $data, "Add Division");
    }

    //last update 3.9.3
    public function view_division_detail($data = [])
    {
        $this->validate_action_access("00-022");
        $id = $this->get_custom_input("id", $data, false);
        if ($id !== false) {
            $check = $this->gemstone_model->get_division_detail($id);
            if ($check->success === true) {
                $data["division_detail"] = $check->data;
                $data["allow_edit_division"] = $this->gemstone_model->check_action_access("00-023")->valid;
                $data['page'] = $page = $this->get_page_input($data);
                $data['sort'] = $sort = $this->get_sort_input($data);
                $position_count = $this->gemstone_model->get_division_position_count($id);
                $admin_count = $this->gemstone_model->get_division_admin_count($id);
                $data['position_list'] = $this->gemstone_model->get_division_position_list_paged($id, $page, $sort);
                $data['admin_list'] = $this->gemstone_model->get_division_admin_list_paged($id, $page, $sort);
                $data['position_pagination'] = $this->draw_pagination($page, $position_count, "view_division_detail?id=$id&tab=1&sort=" . $sort);
                $data['admin_pagination'] = $this->draw_pagination($page, $admin_count, "view_division_detail?id=$id&tab=2&sort=" . $sort);
                $this->navigate("gemstone_division_detail", $data, $data["division_detail"]->division_name);
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
                $this->view_uac_management($data);
            }
        } else {
            $data["message"] = $this->draw_message("Invalid division.", "error");
            $this->view_uac_management($data);
        }
    }

    //last update 3.9.3
    public function view_division_edit($data = [])
    {
        $this->validate_action_access("00-023", false);
        $id = $this->get_custom_input("id", $data, false);
        if ($id !== false) {
            $check = $this->gemstone_model->get_division_detail($id);
            if ($check->success === true) {
                $data["division_detail"] = $check->data;
                $this->navigate("gemstone_division_edit", $data, "Edit");
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
                $this->view_uac_management($data);
            }
        } else {
            $data["message"] = $this->draw_message("Invalid division.", "error");
            $this->view_uac_management($data);
        }
    }

    //last update 3.9.3
    public function process_division_add()
    {
        $this->validate_action_access("00-021");
        $input = (object) $this->input->post();
        $check = $this->gemstone_model->add_division($input);
        if ($check->success === true) {
            $data["message"] = $this->draw_message($check->message, "success");
            $data["id"] = $check->id;
            $this->view_division_detail($data);
        } else {
            $data["bounced"] = $input;
            $data["message"] = $this->draw_message($check->message, "error");
            $this->navigate("gemstone_division_add", $data, "Add Position");
        }
    }

    //last update 3.9.3
    public function process_division_edit()
    {
        $this->validate_action_access("00-023");
        $input = (object) $this->input->post();
        $data["id"] = $input->division_id;
        $check = $this->gemstone_model->edit_division($input);
        if ($check->success === true) {
            $data["message"] = $this->draw_message($check->message, "success");
            $this->view_division_detail($data);
        } else {
            $data["message"] = $this->draw_message($check->message, "error");
            $this->view_division_edit($data);
        }
    }

    //last update 3.9.4
    public function process_grant_action_access($data = [])
    {
        $this->validate_action_access("00-019");
        $action_id = $this->get_custom_input("action", $data, false);
        $data["id"] = $position_id = $this->get_custom_input("position", $data, false);
        $data["search"] = $this->get_custom_input("search", $data, false);
        $data["tab"] = 2;
        if ($action_id !== false && $position_id !== false) {
            $check = $this->gemstone_model->grant_action_access($action_id, $position_id);
            if ($check->success === true) {
                $data["message"] = $this->draw_message($check->message, "success");
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
            }
        } else {
            $data["message"] = $this->draw_message("Invalid input.", "error");
        }
        $this->view_position_detail($data);
    }

    //last update 3.9.4
    public function process_revoke_action_access($data = [])
    {
        $this->validate_action_access("00-020");
        $action_id = $this->get_custom_input("action", $data, false);
        $data["id"] = $position_id = $this->get_custom_input("position", $data, false);
        $data["search"] = $this->get_custom_input("search", $data, false);
        $data["tab"] = 2;
        if ($action_id !== false && $position_id !== false) {
            $check = $this->gemstone_model->revoke_action_access($action_id, $position_id);
            if ($check->success === true) {
                $data["message"] = $this->draw_message($check->message, "success");
            } else {
                $data["message"] = $this->draw_message($check->message, "error");
            }
        } else {
            $data["message"] = $this->draw_message("Invalid input.", "error");
        }
        $this->view_position_detail($data);
    }

    //DIVISION END
    //ACTION START

    /**
      //last update 3.5.4
     * @param mixed $param_1
     * @param mixed $param_2
     * @param boolean $flexible
     * @return boolean    */
    public function validate_access($param_1 = true, $param_2 = false, $flexible = false)
    {
        $is_ajax = "";
        if (!empty($this->input->post("ajax"))) {
            $is_ajax = strtoupper($this->input->post("ajax"));
        }
        $force_ajax = true;
        $is_valid = false;
        if ($this->session->userdata("admin_id") != "") {
            //SUPER ADMIN TEST is not logged
            if ($this->session->userdata("admin_tier_id") != 1) {
                if ($this->gemstone_model->check_session_validity()) {
                    $required_division = "";
                    $required_level = 1;
                    $required_title = "";
                    if ($param_1 === false) {
                        $is_valid = true;
                        $force_ajax = false;
                    } elseif (is_integer($param_1)) {
                        $required_level = $param_1;
                        if ($param_2 === true) {
                            $flexible = true;
                            if ($required_level <= $this->session->userdata("admin_level")) {
                                $is_valid = true;
                            }
                        } else {
                            if ($required_level == $this->session->userdata("admin_level")) {
                                $is_valid = true;
                            }
                        }
                    } elseif (is_string($param_1)) {
                        $compare_type = strtoupper($param_1);
                        if ($compare_type == "DIVISION") {
                            $required_division = $param_2;
                            if (is_array($required_division)) {
                                foreach ($required_division as &$item) {
                                    $item = strtoupper($item);
                                }

                                $index = array_search(strtoupper($this->session->userdata("division_name")), $required_division);
                                if ($index !== false) {
                                    $is_valid = true;
                                }
                            } else {
                                $required_division = strtoupper($required_division);
                                if ($required_division == strtoupper($this->session->userdata("division_name"))) {
                                    $is_valid = true;
                                }
                            }
                        } elseif ($compare_type == "LEVEL") {
                            $required_level = $param_2;
                            if (is_array($required_level)) {
                                foreach ($required_level as &$item) {
                                    $item = (int) $item;
                                }
                                $index = array_search((int) $this->session->userdata("admin_level"), $required_level);
                                if ($index !== false) {
                                    $is_valid = true;
                                }
                            } else {
                                $required_level = (int) $required_level;
                                if ($flexible === true) {
                                    if ($required_level <= $this->session->userdata("admin_level")) {
                                        $is_valid = true;
                                    }
                                } else {
                                    if ($required_level == $this->session->userdata("admin_level")) {
                                        $is_valid = true;
                                    }
                                }
                            }
                        } elseif ($compare_type == "TITLE") {
                            $required_title = $param_2;
                            if (is_array($required_title)) {
                                $required_level = 26;
                                foreach ($required_title as &$item) {
                                    $item = strtoupper($item);
                                    $check = $this->gemstone_model->get_admin_level_title($item);
                                    if ($check->success === true && $check->found === true && $check->data->admin_level < $required_level) {
                                        $required_level = $check->data->admin_level;
                                    }
                                }
                                $index = array_search(strtoupper($this->session->userdata("admin_title")), $required_title);
                                if ($index !== false || (int) $this->session->userdata("admin_level") > $required_level) {
                                    $is_valid = true;
                                }
                            } else {
                                $required_title = strtoupper($required_title);
                                if ($flexible === true) {
                                    $check = $this->gemstone_model->get_admin_level_title($required_title);
                                    if ($check->success === true && $check->found === true) {

                                        $required_level = $check->data->admin_level;
                                        if ($required_level < (int) $this->session->userdata("admin_level") || $required_title == strtoupper($this->session->userdata("admin_title"))) {
                                            $is_valid = true;
                                        }
                                    }
                                } else {
                                    if ($required_title == strtoupper($this->session->userdata("admin_title"))) {
                                        $is_valid = true;
                                    }
                                }
                            }
                        } elseif ($compare_type == "BLACK_LIST") {
                            $raw_list = $param_2;
                            if (is_array($raw_list)) {
                                $black_list = [];
                                foreach ($raw_list as $item) {
                                    $object = new stdClass();
                                    $raw = explode("|", $item);
                                    $object->division = strtoupper($raw[0]);
                                    $level_list = explode(",", $raw[1]);
                                    $object->level = $level_list;
                                    $object->flexible = $raw[2];
                                    $black_list[] = $object;
                                }
                                $index = array_search(strtoupper($this->session->userdata("division_name")), array_column($black_list, "division"));
                                if ($index === false) {
                                    $is_valid = true;
                                } else {
                                    if ($black_list[$index]->flexible === false) {
                                        $level_index = array_search(strtoupper($this->session->userdata("admin_level")), $black_list[$index]->level);
                                        if ($level_index === false) {
                                            $is_valid = true;
                                        }
                                    } else {
                                        if ($black_list[$index]->level < $this->session->userdata("admin_level")) {
                                            $is_valid = true;
                                        }
                                    }
                                }
                            } else {
                                $is_valid = false;
                            }
                        } elseif ($compare_type == "WHITE_LIST") {
                            $raw_list = $param_2;
                            if (is_array($raw_list)) {
                                $white_list = [];
                                foreach ($raw_list as $item) {
                                    $object = new stdClass();
                                    $raw = explode("|", $item);
                                    $object->division = strtoupper($raw[0]);
                                    $level_list = explode(",", $raw[1]);
                                    $object->level = $level_list;
                                    $object->flexible = $raw[2];
                                    $white_list[] = $object;
                                }
                                $index = array_search(strtoupper($this->session->userdata("division_name")), array_column($white_list, "division"));
                                if ($index !== false) {
                                    if ($white_list[$index]->flexible === false) {
                                        $level_index = array_search(strtoupper($this->session->userdata("admin_level")), $white_list[$index]->level);
                                        if ($level_index !== false) {
                                            $is_valid = true;
                                        }
                                    } else {
                                        if ($white_list[$index]->level[0] <= $this->session->userdata("admin_level")) {
                                            $is_valid = true;
                                        }
                                    }
                                }
                            } else {
                                $is_valid = false;
                            }
                        } else {
                            $is_valid = false;
                        }
                        if (func_num_args() == 3 && $flexible === false) {
                            $force_ajax = false;
                        }
                    } elseif ($param_1 === true) {
                        $is_valid = true;
                    }
                } else {
                    $this->expired();
                }
            } else {
                $is_valid = true;
                if ($param_1 === false) {
                    $force_ajax = false;
                }
                if (func_num_args() == 3 && $flexible === false) {
                    $force_ajax = false;
                }
            }
        }

        if ($is_valid === true) {
            if ($is_ajax === true || $is_ajax == "true" || $force_ajax === false) {
                $last_active = strtotime($this->session->userdata("last_active"));
                $check_time = strtotime(date("Y-m-d H:i:s"));
                $diff = $check_time - $last_active;
                $minute_passed = floor($diff / 60);
                if ($minute_passed <= TIMEOUT_PERIOD) {
                    $this->gemstone_model->update_last_active();
                    $this->session->set_userdata("last_active", date("Y-m-d H:i:s"));
                    return true;
                } else {
                    redirect("admin/expired");
                }
            } else {
                echo $this->view_400();
                exit();
            }
        } else {
            if ($is_ajax === true || $is_ajax == "true" || $force_ajax === false) {
                $this->view_404();
            } else {
                $this->view_404(false, false);
            }
        }
    }

    //last update 3.1.4
    public function check_certificate()
    {
        $uri = base_url();
        $cert_source = $_ENV["GSCERT"];
        $cert = substr($cert_source, 260, 60);
        if (!password_verify($uri, $cert)) {
            show_404();
        }
    }

    //last update 2.5
    public function redirect()
    {
        $this->view_404(true);
    }

    //last update 3.0
    public function view_400()
    {
        return $this->load->view("gemstone_400", "", true);
    }

    //last update 3.2.0a
    public function view_404($obsolete_page = false, $is_ajax = true, $missing_method = false)
    {
        if ($this->session->userdata("admin_id") != "") {
            $data['logged_in'] = true;
            if ($obsolete_page) {
                $data['obsolete'] = true;
            }
            if ($is_ajax !== true) {
                $data["bad_request"] = true;
            }
            if ($missing_method === true) {
                $data["missing_method"] = true;
            }
            $data["breadcrumb"] = $this->draw_breadcrumb();
            $this->load->view("gemstone_404", $data);
        } else {
            redirect("admin/expired");
        }
    }

    //last update 3.0a
    public function view_root_404()
    {
        $this->view_404(false, false, true);
    }

    //last update 3.9.4
    public function validate_action_access($action_id, $log_activity = true)
    {
        $response = new GSResponse();
        $response->log_id = false;
        $is_valid = false;
        if (!empty($this->input->post("ajax"))) {
            $is_ajax = strtoupper($this->input->post("ajax"));
        } else {
            $is_ajax = false;
        }
        if ($this->session->userdata("admin_id") != "") {
            if ($this->gemstone_model->check_session_validity()) {
                $check = $this->gemstone_model->check_action_access($action_id);
                $is_valid = $check->valid;
                $response->message = $check->message;
                if ($is_valid && $log_activity) {
                    $response->log_id = $this->gemstone_model->log_activity($check->action_id);
                }
            } else {
                $response->message = "FAIL: SESSION EXPIRED";
                $this->expired();
            }
        } else {
            $response->message = "FAIL: NO SESSION";
        }

        if ($is_valid === true) {
            $last_active = strtotime($this->session->userdata("last_active"));
            $check_time = strtotime(date("Y-m-d H:i:s"));
            $diff = $check_time - $last_active;
            $minute_passed = floor($diff / 60);
            if ($minute_passed <= TIMEOUT_PERIOD) {
                $this->gemstone_model->update_last_active();
                $this->session->set_userdata("last_active", date("Y-m-d H:i:s"));
            } else {
                redirect("admin/expired");
            }
            if ($is_ajax === true || $is_ajax == "TRUE" || $this->spa_lock === false) {
                return $response;
            } else {
                $response->message = "FAIL: NON AJAX REQUEST";
                echo $this->view_400();
                exit();
            }
        } else {
            if ($is_ajax === true || $is_ajax == "true" || $this->spa_lock === false) {
                $this->view_404();
            } else {
                $this->view_404(false, false);
            }
        }
        return $response;
    }

    //ACTION END
    //last update 3.9.4
    public function view_uac()
    {
        $this->set_spa_lock(false);
        $this->validate_action_access("00-024");
        $data["function_list"] = get_class_methods($this);
        $data["reserved_function_list"] = $this->gemstone_model->get_reserved_function_list(true);
        $data["admin_level_list"] = $this->gemstone_model->get_all_admin_level();
        $this->load->view("gemstone_uac_table", $data);
    }

    //last update 3.9.9
    public function process_download_uac()
    {
        $this->set_spa_lock(false);
        $this->validate_action_access("00-025");
        $function_list = $this->gemstone_model->get_gemstone_action_list();
        $admin_level_list = $this->gemstone_model->get_all_admin_level();
        $header = ["Feature"];
        $body = [];
        foreach ($admin_level_list as $level) {
            $header[] = $level->level_title;
        }
        foreach ($function_list as $f_name) {
            $row = [];
            $function_name = $f_name->action_code . " | " . $f_name->action_name;
            $row[] = $function_name;
            foreach ($admin_level_list as $level) {
                $row[] = "";
            }
            $body[] = $row;
        }
        $this->gemstone_model->download_as_excel(PROJECT_NAME . " UAC TABLE", $header, $body);
    }

    //last update 3.9.4
    public function view_db()
    {
        $this->set_spa_lock(false);
        $this->validate_action_access("00-026");
        $data["db_list"] = $this->gemstone_model->get_db_structure();
        $this->load->view("gemstone_db", $data);
    }

    //UAC END
    //ADMIN START
    //last update 3.9.4
    public function view_admin_management($data = [])
    {
        $this->validate_action_access("00-005");
        $data["allow_add_new"] = $this->gemstone_model->check_action_access("00-006")->valid;
        $data["allow_force_logout"] = $this->gemstone_model->check_action_access("00-004A")->valid;
        $data["allow_access_control"] = $this->gemstone_model->check_action_access("00-015")->valid;
        $data["allow_admin_report"] = $this->gemstone_model->check_action_access("00-027")->valid;
        $data["allow_deactivate"] = $this->gemstone_model->check_action_access("00-009")->valid;
        $data["allow_reactivate"] = $this->gemstone_model->check_action_access("00-010")->valid;
        $data["allow_see_admin"] = $this->gemstone_model->check_action_access('00-008')->valid;
        $data["page"] = $page = $this->get_page_input($data, "1");
        $page = explode(",", $page);
        $data["sort"] = $sort = $this->get_sort_input($data);
        $count = $this->gemstone_model->get_admin_count();
        $data["admin_list"] = $this->gemstone_model->get_admin_list_paged($page[0], $sort);
        $data["pagination"] = $this->draw_pagination($page, $count, "view_admin_management?sort=" . $sort);
        $this->navigate('gemstone_admin_management', $data, "Admin Management", true);
    }

    //last update 3.9.4
    public function view_admin_detail($data = [])
    {
        $data["id"] = $id = $this->get_custom_input("id", $data, false);
        if ($this->session->userdata("admin_id") == $id) {
            $log_id = $this->validate_action_access("00-007")->log_id;
            $data["allow_edit"] = $this->gemstone_model->check_action_access("00-011")->valid;
        } else {
            $log_id = $this->validate_action_access("00-008")->log_id;
            $data["allow_edit"] = $this->gemstone_model->check_action_access("00-012")->valid;
        }
        $data["allow_force_logout"] = $this->gemstone_model->check_action_access("00-004")->valid;
        $data["allow_push_notification"] = $this->gemstone_model->check_action_access("00-028")->valid;
        $check = $this->gemstone_model->get_admin_detail($id);
        if ($check->success) {
            if ($check->data->admin_level <= $this->session->userdata("admin_level")) {
                $data["admin_info"] = $check->data;
                $this->gemstone_model->update_activity($log_id, $data["admin_info"]->admin_name, "view_admin_detail?id=$id");
                $data['anti_cache_random'] = $this->generate_anti_cache_value();
                $data['page'] = $page = $this->get_page_input($data, "1");
                $page = explode(",", $page);
                $sort = $this->get_sort_input($data);
                $data['log'] = $this->gemstone_model->get_activity_log($id, $page[0], $sort);
                $count = $this->gemstone_model->get_activity_count($id);
                $data['pagination'] = $this->draw_pagination($page, $count, "view_admin_detail?id=$id&tab=1&sort=" . $sort);
                $this->navigate("gemstone_admin_detail", $data, $data["admin_info"]->admin_name);
            } else {
                $data["error"] = $this->draw_message("You are not authorized to view this page.", "error");
                $this->view_admin_management($data);
            }
        } else {
            $data["error"] = $this->draw_message($check->message, "error");
            $this->view_admin_management($data);
        }
    }

    //last update 3.9.4
    public function view_admin_add($data = [])
    {
        if (!isset($data["bounced"])) {
            $data["bounced"] = $this->get_autosave_data("view_admin_add");
        }
        $this->validate_action_access("00-006", false);
        $data['admin_level'] = $this->gemstone_model->get_all_admin_level();
        $this->navigate('gemstone_admin_add', $data, "Add New Admin");
    }

    //last update 3.9.4
    public function view_admin_edit($data = [])
    {
        $data["id"] = $id = $this->get_custom_input("id", $data, false);
        if ($this->session->userdata("admin_id") == $id) {
            $this->validate_action_access("00-011", false);
        } else {
            $this->validate_action_access("00-012", false);
        }
        $check = $this->gemstone_model->get_admin_detail($id);
        if ($check->success) {
            $data['admin_level'] = $this->gemstone_model->get_all_admin_level();
            $data['admin_info'] = $check->data;
            $this->navigate('gemstone_admin_edit', $data, "Edit Admin");
        } else {
            $data["error"] = $this->draw_message($check->message, "error");
            $this->view_admin_management($data);
        }
    }

    //last update 3.9.4
    public function process_admin_add()
    {
        $input = (object) $this->input->post();
        $input->log_id = $this->validate_action_access("00-006")->log_id;
        $result = $this->gemstone_model->add_admin($input);
        if ($result->success) {
            $data['success'] = $this->draw_message($result->message, "success");
            $data['id'] = $result->id;
            $this->view_admin_detail($data);
        } else {
            $data['error'] = $this->draw_message($result->message, "error");
            $data['bounced'] = (object) $this->input->post();
            $this->view_admin_add($data);
        }
    }

    //last update 3.9.4
    public function process_admin_edit()
    {
        $input = (object) $this->input->post();
        if ($this->session->userdata("admin_id") == $input->admin_id) {
            $input->log_id = $this->validate_action_access("00-011")->log_id;
        } else {
            $input->log_id = $this->validate_action_access("00-012")->log_id;
        }
        $data['id'] = $input->admin_id;
        $result = $this->gemstone_model->update_admin_info($input);
        if ($result->success) {
            $data['success'] = $this->draw_message($result->message, "success");
            $this->view_admin_detail($data);
        } else {
            $data['error'] = $this->draw_message($result->message, "error");
            $this->view_admin_edit($data);
        }
    }

    //last update 3.9.4
    public function process_admin_deactivate()
    {
        $log_id = $this->validate_action_access("00-009")->log_id;
        $target = $this->input->get('id');
        $result = $this->gemstone_model->deactivate_admin($target, $log_id);
        if ($result->success) {
            $data["success"] = $this->draw_message($result->message, "success");
        } else {
            $data['error'] = $this->draw_message($result->message, "error");
        }
        $this->view_admin_management($data);
    }

    //last update 2.6.8
    public function process_admin_reactivate()
    {
        $log_id = $this->validate_action_access("00-010")->log_id;
        $target = $this->input->get('id');
        $result = $this->gemstone_model->reactivate_admin($target, $log_id);
        if ($result->success) {
            $data["success"] = $this->draw_message($result->message, "success");
        } else {
            $data['error'] = $this->draw_message($result->message, "error");
        }
        $this->view_admin_management($data);
    }

    //last update 3.7.1
    public function process_admin_force_logout_all()
    {
        $this->validate_action_access("00-004A");
        $this->gemstone_model->force_logout_all();
        $this->logout();
    }

    //last update 3.9.4
    public function view_admin_activity_report($data = [])
    {
        $this->validate_action_access("00-027");
        $data["start"] = $start = $this->get_custom_input("start", $data, date("Y-m-d", strtotime("-7 DAYS")));
        $data["end"] = $end = $this->get_custom_input("end", $data, date("Y-m-d"));
        $data["filter"] = $filter = $this->get_custom_input("filter", $data, "$start|$end|all|");
        $data["admin_list"] = $this->gemstone_model->get_all_admin_list();
        $data["activity_list"] = $this->gemstone_model->get_admin_activity_list($filter);
        $this->navigate('gemstone_admin_activity_report', $data, "Admin Activity Report");
    }

    //ADMIN END
}

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================
