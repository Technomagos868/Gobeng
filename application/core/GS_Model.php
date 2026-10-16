<?php

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================

defined('BASEPATH') || exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GS_Model extends CI_Model
{

    public $trans_exist = false;
    public $trans_level = 0;

    //===============================================================
    //Replace the following variable with your prefered value
    //===============================================================
    //var $super_admin_username = "aquaterra";
    //var $super_admin_password = "coralis3101";
    //last update 3.9.0
    public function trans_begin()
    {
        if ($this->trans_exist === false) {
            $this->db->trans_begin();
        }
        $this->trans_exist = true;
        $this->trans_level++;
    }

    //last update 3.9.0
    public function trans_status()
    {
        return $this->db->trans_status();
    }

    //last update 3.9.0
    public function trans_commit()
    {
        if ($this->trans_exist === true && $this->trans_level > 0) {
            $this->trans_level--;
            if ($this->trans_level <= 0) {
                $this->db->trans_commit();
                $this->trans_exist = false;
            }
        }
    }

    //last update 3.9.0
    public function trans_rollback()
    {
        if ($this->trans_exist === true && $this->trans_level > 0) {
            $this->trans_level--;
            if ($this->trans_level <= 0) {
                $this->db->trans_rollback();
                $this->trans_exist = false;
            }
        }
    }

    //last update 3.9.9
    public function logout($target = 0, $log_id = false)
    {
        if ($this->session->userdata("admin_id") != null) {
            if (is_numeric($target) && $target !== 0) {
                $sql = "UPDATE `gemstone_session_token` "
                    . "SET `logged_out` = 1 "
                    . "WHERE `admin_id` = ? "
                    . "ORDER BY `last_active` DESC "
                    . "LIMIT 1;";
                $this->db->query($sql, array($target));
            } else {
                $target = $this->session->userdata("admin_id");
                $sql = "UPDATE `gemstone_session_token` "
                    . "SET `logged_out` = 1 "
                    . "WHERE `session_token` = ? AND `admin_id` = ?;";
                $this->db->query($sql, array($this->session->userdata("session_token"), $target));
            }
            if ($target == $this->session->userdata("admin_id")) {
                $action_id = $this->check_action_access("00-003")->action_id;
                $sql = "INSERT INTO `gemstone_activity_log` (`log_date`, `actor`, `action_id`, `legacy_log`) "
                    . "VALUES (NOW(), ?, ?, 0);";
                $this->db->query($sql, [$this->session->userdata("admin_id"), $action_id]);
                $this->session->unset_userdata("admin_id");
                $this->session->unset_userdata("admin_name");
                $this->session->unset_userdata("admin_tier_id");
                $this->session->unset_userdata("admin_level");
                $this->session->unset_userdata("admin_title");
                $this->session->unset_userdata("division_id");
                $this->session->unset_userdata("division_name");
                $this->session->unset_userdata("column_visibility");
                $this->session->unset_userdata("breadcrumb");
            } else {
                $admin_info = $this->get_admin_detail($target)->data;
                $this->update_activity($log_id, "Forced $admin_info->admin_name to Logout", "view_admin_detail?id=$target");
            }
        }
    }

    //last update 3.4.3
    public function get_gemstone_settings()
    {
        $response = new stdClass();
        $sql = "SELECT * FROM `gemstone_config` WHERE `configuration_id` = ? LIMIT 1";
        $query = $this->db->query($sql, [CONFIGURATION_ID]);
        if ($query) {
            $response = $query->row();
        }
        return $response;
    }

    //last update 3.7.1
    public function update_gemstone_settings($input)
    {
        $response = $this->prepare_response();
        if (!isset($input->is_maintenance)) {
            $input->is_maintenance = 0;
        }
        if (!isset($input->work_mon)) {
            $input->work_mon = 0;
        }
        if (!isset($input->work_tue)) {
            $input->work_tue = 0;
        }
        if (!isset($input->work_wed)) {
            $input->work_wed = 0;
        }
        if (!isset($input->work_thu)) {
            $input->work_thu = 0;
        }
        if (!isset($input->work_fri)) {
            $input->work_fri = 0;
        }
        if (!isset($input->work_sat)) {
            $input->work_sat = 0;
        }
        if (!isset($input->work_sun)) {
            $input->work_sun = 0;
        }
        $sql = "UPDATE `gemstone_config` "
            . "SET `item_per_page` = ? "
            . ", `working_hour_start` = ? "
            . ", `working_hour_end` = ? "
            . ", `work_mon` = ? "
            . ", `work_tue` = ? "
            . ", `work_wed` = ? "
            . ", `work_thu` = ? "
            . ", `work_fri` = ? "
            . ", `work_sat` = ? "
            . ", `work_sun` = ? "
            . ", `is_maintenance` = ? "
            . "WHERE `configuration_id` = ?;";
        $query = $this->db->query($sql, [
            $input->item_per_page,
            $input->working_hour_start,
            $input->working_hour_end,
            $input->work_mon,
            $input->work_tue,
            $input->work_wed,
            $input->work_thu,
            $input->work_fri,
            $input->work_sat,
            $input->work_sun,
            $input->is_maintenance,
            CONFIGURATION_ID
        ]);
        if ($query) {
            $response->message = "Settings updated.";
            $response->success = true;
            $this->log_activity(11, "Changes Gemstone Settings");
            if (isset($input->is_maintenance) && $input->is_maintenance == 1) {
                $this->force_logout_all(true);
            }
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    //last update 2.6.10
    public function get_dashboard_statistic()
    {
        $response = $this->prepare_response();
        $response->admin_count = $this->get_admin_count();
        $response->activity_rate = $this->get_admin_activity_rate();
        return $response;
    }

    //last update 2.5
    public function check_input_exists($input_array, $field_list, $strict = true)
    {
        $response = new stdClass();
        $response->invalid_input = array();
        if ($strict) {
            $response->valid = true;
            foreach ($field_list as $field) {
                if (!property_exists($input_array, $field)) {
                    $response->valid = false;
                    $response->invalid_input[] = $field;
                }
            }
        } else {
            $response->valid = false;
            foreach ($field_list as $field) {
                if (!property_exists($input_array, $field)) {
                    $response->invalid_input[] = $field;
                } else {
                    $response->valid = true;
                }
            }
        }
        return $response;
    }

    //last update 2.5
    public function check_duplicate($table_name, $field_name, $value)
    {
        $sql = "SELECT * "
            . "FROM `$table_name` "
            . "WHERE `$field_name` = ?;";
        $result = $this->db->query($sql, array($value));

        if ($result->num_rows() > 0) {
            return true;
        }
        return false;
    }

    //last update 2.5
    public function check_other_duplicate($table_name, $field_name, $value, $exception_field_name, $exception_value)
    {
        $sql = "SELECT * "
            . "FROM `$table_name` "
            . "WHERE `$field_name` = ? AND `$exception_field_name` !=  ?;";
        $result = $this->db->query($sql, array($value, $exception_value));
        if ($result->num_rows() > 0) {
            return true;
        }
        return false;
    }

    //last update 3.9.4
    public function log_activity($action_id, $description = null, $link = null)
    {
        $actor = $this->session->userdata("admin_id");
        if (!empty($actor)) {
            $sql = "INSERT INTO `gemstone_activity_log` (`log_date`, `actor`, `action_id`, `description`, `target_link`, `legacy_log`) "
                . "VALUES(NOW(), ?, ?, ?, ?, 0);";
            $query = $this->db->query($sql, [$actor, $action_id, $description, $link]);
            if ($query) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //last update 3.9.4
    public function update_activity($log_id, $label, $link = null, $meta_data = null)
    {
        if ($log_id !== false) {
            $sql = "UPDATE `gemstone_activity_log` "
                . "SET `label` = ? "
                . ", `target_link` = ? "
                . ", `meta_data` = ? "
                . ", `legacy_log` = 0 "
                . "WHERE `log_id` = ?;";
            $query = $this->db->query($sql, [$label, $link, $meta_data, $log_id]);
            if ($query) {
                return true;
            }
        }
        return false;
    }

    //last update 2.5
    public function generate_activity_link($link_target, $link_text, $active_link = true)
    {
        if ($active_link) {
            return "<span class='gs-table-link' onclick='gsNavigate(\"$link_target\")'>$link_text</span>";
        } else {
            return "<span class='gs-table-link'>$link_text (No Link)</span>";
        }
    }

    //last update 3.4.0
    public function get_item_per_page()
    {
        $sql = "SELECT `item_per_page` "
            . "FROM `gemstone_config` "
            . "WHERE `configuration_id` = "
            . CONFIGURATION_ID . ";";

        $result = $this->db->query($sql);

        if ($result) {
            return $result->row()->item_per_page;
        } else {
            return false;
        }
    }

    //last update 3.4.0
    public function get_default_language()
    {
        $sql = "SELECT `default_language` "
            . "FROM `gemstone_config` "
            . "WHERE `configuration_id` = "
            . CONFIGURATION_ID . ";";

        $result = $this->db->query($sql);

        if ($result) {
            return $result->row()->default_language;
        } else {
            return false;
        }
    }

    //last update 3.5.5
    public function check_temp_file_uploaded($field_name, $required = true)
    {
        $response = $this->prepare_response();
        $response->multiple = false;
        $response->valid = false;
        $response->uploaded = false;
        $response->uploaded_list = [];
        $response->error_list = [];
        if (isset($_FILES[$field_name])) {
            if (is_array($_FILES[$field_name]['name'])) {
                $response->is_multiple = true;
                $error_count = 0;
                $success_count = 0;
                $response->multiple = true;
                for ($i = 0; $i < count($_FILES[$field_name]['name']); $i++) {
                    if (is_uploaded_file($_FILES[$field_name]['tmp_name'][$i])) {
                        $success_count++;
                        $uploaded = new GSFile();
                        $uploaded->temp_name = $_FILES[$field_name]['tmp_name'][$i];
                        $uploaded->type = $_FILES[$field_name]['type'][$i];
                        $uploaded->name = $_FILES[$field_name]['name'][$i];
                        $uploaded->size = $_FILES[$field_name]['size'][$i];
                        $uploaded->extension = pathinfo($uploaded->name, PATHINFO_EXTENSION);
                        $uploaded->error = false;
                        $allowed = ALLOWED_UPLOAD_TYPE;
                        $allowed = explode("|", $allowed);
                        if (in_array($uploaded->extension, $allowed)) {
                            $response->uploaded_list[] = $uploaded;
                        } else {
                            $uploaded->error = "Extension not allowed.";
                            $response->error_list[] = $uploaded;
                        }
                    } else {
                        $error_count++;
                    }
                }
                if ($success_count > 0) {
                    $response->success = true;
                    $response->uploaded = true;
                    $response->valid = true;
                    if ($error_count > 0) {
                        $response->message = "Some files cannot be uploaded.";
                    }
                } else {
                    $response->message = "Upload failed.";
                }
            } else {
                if (!empty($_FILES[$field_name]['name'])) {
                    if (is_uploaded_file($_FILES[$field_name]['tmp_name'])) {
                        $response->success = true;
                        $response->uploaded = true;
                        $uploaded = new GSFile();
                        $uploaded->temp_name = $_FILES[$field_name]['tmp_name'];
                        $uploaded->type = $_FILES[$field_name]['type'];
                        $uploaded->name = $_FILES[$field_name]['name'];
                        $uploaded->size = $_FILES[$field_name]['size'];
                        $uploaded->extension = pathinfo($uploaded->name, PATHINFO_EXTENSION);
                        $allowed = ALLOWED_UPLOAD_TYPE;
                        $allowed = explode("|", $allowed);
                        if (in_array($uploaded->extension, $allowed)) {
                            $response->valid = true;
                            $response->uploaded_list[] = $uploaded;
                            $response->message = "File OK.";
                        } else {
                            $response->message = "Extension not allowed.";
                        }
                    } else {
                        $response->message = "File upload failed.";
                    }
                } else {
                    if ($required === false) {
                        $response->valid = true;
                    }
                    $response->message = "No file uploaded.";
                }
            }
        } else {
            $response->message = "Input field undefined.";
        }
        return $response;
    }

    //last update 3.2.0a
    public function prepare_image_upload($custom_path = false, $new_file_name = false, $overwrite = false)
    {
        $config['allowed_types'] = ALLOWED_UPLOAD_TYPE;
        $config['max_size'] = MAX_UPLOAD_SIZE;
        $config['max_width'] = MAX_UPLOAD_WIDTH;
        $config['max_height'] = MAX_UPLOAD_HEIGHT;
        if ($custom_path !== false) {
            if (is_string($custom_path)) {
                $config['upload_path'] = $custom_path;
            }
        } else {
            $config['upload_path'] = './uploads/';
        }
        if ($new_file_name !== false && is_string($new_file_name)) {
            $config['file_name'] = $new_file_name;
        }
        if ($overwrite === true) {
            $config['overwrite'] = true;
        }
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
    }

    //last update 2.6.6
    public function generate_random_string($size = false)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = strlen($characters);
        $random_string = "";
        if ($size === false || !is_integer($size)) {
            $size = 26;
        }
        for ($i = 0; $i < $size; $i++) {
            $random_string .= $characters[rand(0, $length - 1)];
        }
        return $random_string;
    }

    //last update 2.6.8
    public function check_numeric($value, $default = 1)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            return $default;
        }
    }

    //last_update 3.9.4
    public function prepare_response()
    {
        return new GSResponse();
    }

    //last update 3.1.4
    public function generate_sql_like_string($field_name, $keywords, $table_alias = false)
    {
        $string = "";
        $delimiter = "";
        if (is_array($keywords) && count($keywords) > 0) {
            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (is_string($keyword) && $keyword != "") {
                    if ($table_alias === false) {
                        $string .= $delimiter . "`$field_name` LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
                    } else {
                        $string .= $delimiter . "$table_alias.`$field_name` LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
                    }
                    $delimiter = " OR ";
                }
            }
        } elseif (is_string($keywords)) {
            if (strpos($keywords, ",")) {
                $keywords = explode(",", $keywords);
                foreach ($keywords as $keyword) {
                    $keyword = trim($keyword);
                    if ($keyword != "") {
                        if ($table_alias === false) {
                            $string .= $delimiter . "`$field_name` LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
                        } else {
                            $string .= $delimiter . "$table_alias.`$field_name` LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
                        }
                        $delimiter = " OR ";
                    }
                }
            } else {
                $keywords = trim($keywords);
                if ($keywords != "") {
                    if ($table_alias === false) {
                        $string .= "`$field_name` LIKE '%" . $this->db->escape_like_str($keywords) . "%'";
                    } else {
                        $string .= "$table_alias.`$field_name` LIKE '%" . $this->db->escape_like_str($keywords) . "%'";
                    }
                }
            }
        }
        if ($string != "") {
            return "(" . $string . ") ";
        } else {
            return $string;
        }
    }

    //last update 3.7.1
    public function get_working_hour()
    {
        $sql = "SELECT `working_hour_start` AS `start`, `working_hour_end` AS `end` "
            . "FROM `gemstone_config` "
            . "WHERE `configuration_id` = ?;";
        $query = $this->db->query($sql, [CONFIGURATION_ID]);
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        } else {
            $response = new stdClass();
            $response->start = "00:00:00";
            $response->end = "00:00:00";
            return $response;
        }
    }

    //last update 3.2.2
    public function get_server_path($file_name)
    {
        if ($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
            $write_url = LOCAL_PATH . $file_name;
        } else {
            $write_url = SERVER_PATH . $file_name;
        }
        return $write_url;
    }

    //last update 3.2.2
    public function export_table_as_csv($table_name, $filename = false)
    {
        $response = new stdClass();
        $header_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table_name' AND TABLE_SCHEMA = '" . DB_NAME . "'";
        $query = $this->db->query($header_sql);
        if ($query->result()) {
            if ($filename === false) {
                $filename = $table_name . ".csv";
            }
            $header_array = array();
            $result = $query->result();
            foreach ($result as $col) {
                $header_array[] = $col->COLUMN_NAME;
            }
            $path = $this->get_server_path($filename);
            $file = fopen($path, 'w');
            fputcsv($file, array("Export Data From Table: $table_name"), ";");
            fputcsv($file, $header_array, CSV_DELIMITER);
            $content_sql = "SELECT * FROM $table_name;";
            $content = $this->db->query($content_sql);
            $index = 1;
            if ($content->num_rows() > 0) {
                $list_item = $content->result();
                foreach ($list_item as $line) {
                    $array_line = (array) $line;
                    fputcsv($file, $array_line, CSV_DELIMITER);
                    $index++;
                }
            } else {
                fputcsv($file, ["No row found."], CSV_DELIMITER);
            }
            fclose($file);
            $this->log_activity(10, $table_name);
            $response->message = "Exported " . ($index - 1) . " rows sucessfully.";
            $response->success = true;
            $response->filename = $filename;
            $response->path = $path;
        } else {
            $response->message = "Table not found.";
        }
        return $response;
    }

    //last update 3.4.3
    public function get_user_agent()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        // First get the platform
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Edg/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Chrome";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }
        return array(
            'user_agent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    //last update 3.4.3
    public function get_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    //last update 3.4.0
    public function get_language_list()
    {
        $sql = "SELECT * FROM `gemstone_language` gl "
            . "WHERE 1;";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 3.4.3
    public function send_email($recipient, $subject, $content)
    {
        try {
            $response = new stdClass();
            $response->success = false;
            $response->sent_count = 0;
            $response->recipient_count = 1;
            $response->multiple = false;
            if (is_array($recipient)) {
                $response->multiple = true;
                $response->recipient_count = count($recipient);
            }

            $config['protocol'] = "smtp";
            $config['smtp_host'] = SMTP_HOST;
            $config['smtp_port'] = SMTP_PORT;
            $config['smtp_user'] = SMTP_USER;
            $config['smtp_pass'] = SMTP_PASS;
            $config['smtp_crypto'] = "tls";
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['newline'] = "\r\n";
            $config['crlf'] = "\r\n";
            $this->load->library('email');
            $this->email->initialize($config);

            if ($response->multiple) {
                foreach ($recipient as $email) {
                    $this->email->from(SMTP_USER, SMTP_NAME);
                    $this->email->to($email);
                    $this->email->subject($subject);
                    $this->email->message($content);
                    if ($this->email->send()) {
                        $response->sent_count++;
                    }
                }
                if ($response->sent_count == $response->recipient_count) {
                    $response->success = true;
                }
            } else {
                $this->email->from(SMTP_USER, SMTP_NAME);
                $this->email->to($recipient);
                $this->email->subject($subject);
                $this->email->message($content);
                if ($this->email->send()) {
                    $response->success = true;
                    $response->sent_count = 1;
                }
            }

            return $response;
        } catch (RuntimeException $e) {
            log_message('error', $e->getMessage());
        }
    }

    //last update 3.5.4
    public function export_sql_as_csv($header, $sql, $filename = false, $title = false)
    {
        $response = new stdClass();
        if ($filename === false) {
            $filename = "export.csv";
        }
        $path = $this->get_server_path($filename);
        $file = fopen($path, 'w');
        if ($title === false) {
            $title = "Exported Data";
        }
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file, [$title], CSV_DELIMITER);
        fputcsv($file, ["Exported Date ", date("d M Y H:i:s")], CSV_DELIMITER);
        fputcsv($file, $header, CSV_DELIMITER);
        $content = $this->db->query($sql);
        $index = 1;
        if ($content->num_rows() > 0) {
            $list_item = $content->result();
            foreach ($list_item as $line) {
                $array_line = (array) $line;
                if (isset($array_line['sku'])) {
                    $array_line['sku'] = "'" . $array_line['sku'];
                }
                if (isset($array_line['account_number'])) {
                    $array_line['account_number'] = "'" . $array_line['account_number'];
                }
                fputcsv($file, $array_line, CSV_DELIMITER);
                $index++;
            }
        } else {
            fputcsv($file, ["No row found."], CSV_DELIMITER);
        }
        fclose($file);
        $this->log_activity(10);
        $response->message = "Exported " . ($index - 1) . " rows sucessfully.";
        $response->success = true;
        $response->filename = $filename;
        $response->path = $path;
        return $response;
    }

    //last update 3.5.4
    public function export_array_as_csv($header, $array, $filename = false, $title = false)
    {
        $response = new stdClass();
        if ($filename === false) {
            $filename = "export.csv";
        }
        $path = $this->get_server_path($filename);
        $file = fopen($path, 'w');
        if ($title === false) {
            $title = "Exported Data";
        }
        $index = 1;
        fputcsv($file, [$title], CSV_DELIMITER);
        fputcsv($file, ["Exported Date ", date("d M Y H:i:s")], CSV_DELIMITER);
        fputcsv($file, $header, CSV_DELIMITER);
        if (is_array($array) && count($array) > 0) {
            $array = (object) $array;
            foreach ($array as $line) {
                $array_line = (array) $line;
                fputcsv($file, $array_line, CSV_DELIMITER);
                $index++;
            }
        } else {
            fputcsv($file, ["No row found."], CSV_DELIMITER);
        }
        fclose($file);
        $this->log_activity(10);
        $index--;
        if ($index <= 0) {
            $index = 0;
        }
        $response->message = "Exported " . $index . " rows sucessfully.";
        $response->success = true;
        $response->filename = $filename;
        $response->path = $path;
        return $response;
    }

    //last update 3.5.4
    public function system_search($keyword)
    {
        $response = $this->prepare_response();
        $response->items = [];
        $search = $this->search_table("gemstone_admin", $keyword, ["admin_name", "admin_fullname", "admin_email"], "admin");
        if ($search->success === true) {
            $response->items[] = $search->data;
        }
        return $response;
    }

    //last update 3.5.4
    public function search_table($table_name, $keyword, $fields, $type_name = false, $options = [])
    {
        $response = $this->prepare_response();
        $data = new stdClass();
        if ($type_name === false) {
            $data->type = $table_name;
        } else {
            $data->type = $type_name;
        }
        $select_sql = "*";
        $join_sql = "";
        $mandatory_sql = "";
        $sort_sql = "";
        $group_sql = "";
        $limit_sql = "";
        $alias = "x";
        if (is_array($options) && !empty($options)) {
            if (isset($options["alias"])) {
                $alias = $options["alias"];
            }
            if (isset($options["select_sql"])) {
                $select_sql = $options["select_sql"];
            }
            if (isset($options["join_sql"])) {
                $join_sql = $options["join_sql"];
            }
            if (isset($options["mandatory_sql"])) {
                $mandatory_sql = $options["mandatory_sql"] . " AND ";
            }
            if (isset($options["sort_field"])) {
                $sort_sql = "ORDER BY $alias.`" . $options["sort_field"] . "` ";
                if (isset($options["sort_direction"])) {
                    $sort_sql .= strtoupper($options["sort_direction"]);
                }
            }
            if (isset($options["group_sql"])) {
                $group_sql = $options["group_sql"];
            }
            if (isset($options["limit"])) {
                $limit_sql = $options["limit"];
            }
        }
        if (is_array($fields) && count($fields) > 0) {
            $like_sql = "";
            $delimiter = "";
            foreach ($fields as $column) {
                if (strpos($column, ".") === false) {
                    $like_sql .= $delimiter . $this->generate_sql_like_string($column, $keyword, $alias);
                } else {
                    $column = explode(".", $column, 2);
                    $like_sql .= $delimiter . $this->generate_sql_like_string($column[1], $keyword, $column[0]);
                }
                $delimiter = " OR ";
            }
        }
        $sql = "SELECT $select_sql "
            . "FROM `" . $table_name . "` $alias "
            . "$join_sql "
            . "WHERE $mandatory_sql ($like_sql) "
            . "$group_sql "
            . "$sort_sql "
            . "$limit_sql ";
        $query = $this->db->query($sql);
        if ($query) {
            $response->success = true;
            $data->found = $query->num_rows();
            $data->list = $query->result();
            $response->data = $data;
        }
        return $response;
    }

    //last update 3.7.0
    public function get_db_structure()
    {
        $db_name = $this->db->database;
        $sql = "SHOW TABLES FROM `$db_name`";
        $query = $this->db->query($sql);
        if ($query) {
            $table_list = $query->result();
            foreach ($table_list as $table) {
                $col_name = "Tables_in_$db_name";
                $table->table_name = $table->$col_name;
                $sql2 = "DESCRIBE $table->table_name ";
                $query2 = $this->db->query($sql2);
                if ($query2) {
                    $table->column = $query2->result();
                } else {
                    $table->column = false;
                }
            }
            return $table_list;
        } else {
            return false;
        }
    }

    //last update 3.9.2
    public function check_module_installed($module)
    {
        $response = new GSResponse();
        $response->installed = false;
        if (is_subclass_of($module, "GS_Module")) {
            $module_detail = $module->get_module_detail();
            $sql = "SELECT * "
                . "FROM `gemstone_module` gm "
                . "WHERE gm.`module_code` = ? AND gm.`module_name` = ?;";
            $query = $this->db->query($sql, [$module_detail->module_code, $module_detail->module_name]);
            if ($query) {
                $response->success = true;
                if ($query->num_rows() > 0) {
                    $response->installed = true;
                    $response->installed_module = $query->row();
                }
            } else {
                $response->message = "Module install check failed.";
            }
        } else {
            $response->message = "Invalid module class.";
        }
        return $response;
    }

    //last update 3.9.2
    public function log_module_install($module)
    {
        $response = new GSResponse();
        if (is_subclass_of($module, "GS_Module")) {
            $module_detail = $module->get_module_detail();
            $check = $this->check_module_installed($module);
            if ($check->success === true) {
                if ($check->installed === true) {
                    $last_module = $check->installed_module;
                    $sql = "UPDATE `gemstone_module` "
                        . "SET `module_code` = ? "
                        . ", `module_name` = ? "
                        . ", `module_version` = ? "
                        . ", `module_description` = ? "
                        . ", `update_date` = NOW() "
                        . "WHERE `module_id` = ?;";
                    $query = $this->db->query($sql, [$module_detail->module_code, $module_detail->module_name, $module_detail->module_version, $module_detail->module_description, $last_module->module_id]);
                } else {
                    $sql = "INSERT INTO `gemstone_module` (`module_code`, `module_name`, `module_version`, `module_description`, `install_date`, `update_date`) "
                        . "VALUES (?, ?, ?, ?, NOW(), NULL);";
                    $query = $this->db->query($sql, [$module_detail->module_code, $module_detail->module_name, $module_detail->module_version, $module_detail->module_description]);
                }
                if ($query) {
                    $response->success = true;
                    $response->message = "Module instalation logged.";
                } else {
                    $response->message = "Failed to log module installation.";
                }
            } else {
                $response->message = $check->message;
            }
        } else {
            $response->message = "Invalid module class.";
        }
        return $response;
    }

    //last update 3.9.2
    public function log_module_patch($module, $patch_version_number)
    {
        $response = new GSResponse();
        if (is_subclass_of($module, "GS_Module")) {
            $module_detail = $module->get_module_detail();
            $check = $this->check_module_installed($module);
            if ($check->success === true) {
                if ($check->installed === true) {
                    $last_module = $check->installed_module;
                    $sql = "UPDATE `gemstone_module` "
                        . "SET `module_code` = ? "
                        . ", `module_name` = ? "
                        . ", `module_version` = ? "
                        . ", `module_description` = ? "
                        . ", `update_date` = NOW() "
                        . "WHERE `module_id` = ?;";
                    $query = $this->db->query($sql, [$module_detail->module_code, $module_detail->module_name, $patch_version_number, $module_detail->module_description, $last_module->module_id]);
                    if ($query) {
                        $response->success = true;
                        $response->message = "Module patch logged.";
                    } else {
                        $response->message = "Failed to log module patch.";
                    }
                } else {
                    $response->message = "Failed to patch module. Module not installed yet.";
                }
            } else {
                $response->message = $check->message;
            }
        } else {
            $response->message = "Invalid module class.";
        }
        return $response;
    }

    //last update 3.9.2
    public function log_module_uninstall($module)
    {
        $response = new GSResponse();
        if (is_subclass_of($module, "GS_Module")) {
            $check = $this->check_module_installed($module);
            if ($check->success === true) {
                if ($check->installed === true) {
                    $last_module = $check->installed_module;
                    $sql = "DELETE FROM `gemstone_module` "
                        . "WHERE `module_id` = ?;";
                    $query = $this->db->query($sql, [$last_module->module_id]);
                    if ($query) {
                        $response->success = true;
                        $response->message = "Module uninstall logged.";
                    } else {
                        $response->message = "Failed to log module uninstall.";
                    }
                } else {
                    $response->message = "Failed to uninstall module. Module not installed yet.";
                }
            } else {
                $response->message = $check->message;
            }
        } else {
            $response->message = "Invalid module class.";
        }
        return $response;
    }

    //last update 3.9.3
    public function download_as_excel($file_name, $header_array, $body_array)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        for ($i = 0; $i < count($header_array); $i++) {
            $sheet->setCellValue(numtobase26($i) . '1', $header_array[$i]);
        }
        $row_index = 2;
        foreach ($body_array as $row) {
            for ($i = 0; $i < count($header_array); $i++) {
                $sheet->setCellValue(numtobase26($i) . $row_index, $row[$i]);
            }
            $row_index++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    //last update 3.9.3
    public function get_reserved_function_list($framework_feature_only = false)
    {
        $framework_feature_list = [
            "__construct",
            "index",
            "get_css_list",
            "get_js_list",
            "navigate",
            "add_module",
            "load_module",
            "check_certificate",
            "login",
            "expired",
            "logout",
            "force_logout",
            "redirect",
            "view_400",
            "view_404",
            "view_root_404",
            "draw_message",
            "draw_quicksearch",
            "draw_pagination",
            "draw_popup",
            "validate_access",
            "generate_anti_cache_value",
            "set_breadcrumb",
            "push_breadcrumb",
            "draw_breadcrumb",
            "clear_breadcrumb",
            "view_last_page",
            "mark_active_tab",
            "update_tab",
            "get_page_input",
            "get_tab_input",
            "get_sort_input",
            "get_custom_input",
            "reload_current_page",
            "download",
            "process_download_file",
            "process_system_search",
            "view_db",
            "view_uac",
            "process_download_uac",
            "download_as_excel",
            "extend_session",
            "get_instance"
        ];
        $uac_feature_list = [
            "view_dashboard",
            "view_gemstone_settings",
            "process_update_gemstone_settings",
            "view_admin_management",
            "view_admin_detail",
            "view_admin_add",
            "view_admin_edit",
            "process_admin_add",
            "process_admin_edit",
            "process_admin_delete",
            "process_admin_deactivate",
            "process_admin_reactivate",
            "process_admin_force_logout_all"
        ];
        if ($framework_feature_only === false) {
            $returned_list = array_merge($framework_feature_list, $uac_feature_list);
        } else {
            $returned_list = $framework_feature_list;
        }
        return $returned_list;
    }

    //last update 3.9.4
    public function get_sort_direction($sort_input)
    {
        $sort_object = new stdClass();
        $sort_object->keyword = "";
        $sort_object->direction = "ASC";
        $sort_array = explode("|", $sort_input);
        if (count($sort_array) >= 2) {
            $sort_object->direction = strtoupper($sort_array[1]);
        }
        $sort_object->keyword = $sort_array[0];
        if ($sort_object->direction !== "ASC" && $sort_object->direction !== "DESC") {
            $sort_object->direction = "ASC";
        }
        return $sort_object;
    }

    //POSITION START
    //last update 3.9.3
    // public function get_position_count()
    // {
    //     $sql = "SELECT COUNT(gac.`admin_tier_id`) AS `count` "
    //         . "FROM `gemstone_access_control` gac "
    //         . "WHERE 1;";
    //     $query = $this->db->query($sql);
    //     if ($query) {
    //         return $query->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.3
    // public function get_position_list_paged($page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "title": {
    //             $sort_sql = "gac.`level_title` $sort->direction";
    //             break;
    //         }
    //         case "division": {
    //             $sort_sql = "gd.`division_name` $sort->direction";
    //             break;
    //         }
    //         case "level": {
    //             $sort_sql = "gac.`admin_level` $sort->direction";
    //             break;
    //         }
    //         case "size": {
    //             $sort_sql = "`size` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "gac.`admin_tier_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $qty_sql = "(SELECT COUNT(ga.`admin_id`) "
    //         . "FROM `gemstone_admin` ga "
    //         . "WHERE ga.`admin_tier_id` = gac.`admin_tier_id`)";
    //     $sql = "SELECT gac.`admin_tier_id` "
    //         . ", gac.`admin_level` "
    //         . ", gac.`division_id` "
    //         . ", gd.`division_name` "
    //         . ", gac.`level_title` "
    //         . ", $qty_sql AS `qty` "
    //         . "FROM `gemstone_access_control` gac "
    //         . "JOIN `gemstone_division` gd ON gac.`division_id` = gd.`division_id` "
    //         . "WHERE 1 "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp";
    //     $query = $this->db->query($sql);
    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //last update 3.9.3
    // public function get_position_detail($admin_tier_id)
    // {
    //     $response = new GSResponse();
    //     $sql = "SELECT * "
    //         . "FROM `gemstone_access_control` gac "
    //         . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
    //         . "WHERE gac.`admin_tier_id` = ?;";
    //     $query = $this->db->query($sql, [$admin_tier_id]);
    //     if ($query) {
    //         $response->success = true;
    //         $response->data = $query->row();
    //     } else {
    //         $response->message = "Query failed.";
    //     }
    //     return $response;
    // }

    //last update 3.9.3
    // public function add_position($input)
    // {
    //     $response = new GSResponse();
    //     $input_check = $this->check_input_exists($input, ["level_title", "admin_level", "division_id", "fulltime"]);
    //     if ($input_check->valid === true) {
    //         $duplicate = $this->check_duplicate("gemstone_access_control", "level_title", $input->level_title);
    //         if ($duplicate === false) {
    //             $sql = "INSERT INTO `gemstone_access_control` (`level_title`, `admin_level`, `division_id`, `fulltime`) "
    //                 . "VALUES (?, ?, ?, ?);";
    //             $query = $this->db->query($sql, [$input->level_title, $input->admin_level, $input->division_id, $input->fulltime]);
    //             if ($query) {
    //                 $response->success = true;
    //                 $response->id = $this->db->insert_id();
    //                 $response->message = "New position added.";
    //             } else {
    //                 $response->message = "Failed to add position. Please contact system administrator.";
    //             }
    //         } else {
    //             $response->message = "Position title already exist.";
    //         }
    //     } else {
    //         $response->message = "Incomplete input.";
    //     }
    //     return $response;
    // }

    //last update 3.9.3
    // public function edit_position($input)
    // {
    //     $response = new GSResponse();
    //     $input_check = $this->check_input_exists($input, ["level_title", "admin_level", "division_id", "fulltime", "admin_tier_id"]);
    //     if ($input_check->valid === true) {
    //         $duplicate = $this->check_other_duplicate("gemstone_access_control", "level_title", $input->level_title, "admin_tier_id", $input->admin_tier_id);
    //         if ($duplicate === false) {
    //             $sql = "UPDATE `gemstone_access_control` "
    //                 . "SET `level_title` = ? "
    //                 . ", `admin_level` = ? "
    //                 . ", `division_id` = ? "
    //                 . ", `fulltime` = ? "
    //                 . "WHERE `admin_tier_id` = ?;";
    //             $query = $this->db->query($sql, [$input->level_title, $input->admin_level, $input->division_id, $input->fulltime, $input->admin_tier_id]);
    //             if ($query) {
    //                 $response->success = true;
    //                 $response->id = $input->admin_tier_id;
    //                 $response->message = "Position detail changed.";
    //             } else {
    //                 $response->message = "Failed to update position detail. Please contact system administrator.";
    //             }
    //         } else {
    //             $response->message = "Position title already exist.";
    //         }
    //     } else {
    //         $response->message = "Incomplete input.";
    //     }
    //     return $response;
    // }

    //last update 3.9.3
    // public function get_position_admin_count($admin_tier_id)
    // {
    //     $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
    //         . "FROM `gemstone_admin` ga "
    //         . "WHERE ga.`admin_tier_id` = ?;";
    //     $query = $this->db->query($sql, [$admin_tier_id]);
    //     if ($query) {
    //         return $query->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.3
    // public function get_position_admin_list_paged($admin_tier_id, $page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "name": {
    //             $sort_sql = "ga.`admin_name` $sort->direction";
    //             break;
    //         }
    //         case "active": {
    //             $sort_sql = "ga.`last_active` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "ga.`admin_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $sql = "SELECT ga.`admin_id` "
    //         . ", ga.`admin_name` "
    //         . ", ga.`last_active` "
    //         . "FROM `gemstone_admin` ga "
    //         . "WHERE ga.`admin_tier_id` = ? "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp;";
    //     $query = $this->db->query($sql, [$admin_tier_id]);

    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //last update 3.9.4
    // public function get_position_access_list($admin_tier_id, $keyword = false)
    // {
    //     if (empty($keyword)) {
    //         $keyword_sql = "1";
    //     } else {
    //         $keyword_sql = $this->generate_sql_like_string("action_name", $keyword, "ga") . " OR " . $this->generate_sql_like_string("action_code", $keyword, "ga");
    //     }
    //     $sub_query = "(SELECT * FROM `gemstone_access_rule` x WHERE x.`position_id` = ?)";
    //     $sql = "SELECT ga.`action_id` "
    //         . ", ga.`action_code` "
    //         . ", ga.`action_name` "
    //         . ", gar.`rule_id` "
    //         . ", gar.`authorized_date` "
    //         . ", gar.`authorizing_admin_id` "
    //         . ", gad.`admin_name` AS `authorizer_name` "
    //         . "FROM `gemstone_action` ga "
    //         . "LEFT JOIN $sub_query gar ON gar.`action_id` = ga.`action_id` "
    //         . "LEFT JOIN `gemstone_admin` gad ON gad.`admin_id` = gar.`authorizing_admin_id` "
    //         . "WHERE $keyword_sql "
    //         . "ORDER BY ga.`action_code` ASC;";
    //     $query = $this->db->query($sql, [$admin_tier_id]);
    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return [];
    //     }
    // }

    //POSITION END
    //DIVISION START
    //last update 3.9.3
    // public function get_all_division_list()
    // {
    //     $sql = "SELECT * "
    //         . "FROM `gemstone_division` "
    //         . "WHERE 1 "
    //         . "ORDER BY `division_name` ASC;";
    //     $query = $this->db->query($sql);
    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //last update 3.9.3
    // public function get_division_count()
    // {
    //     $sql = "SELECT COUNT(gd.`division_id`) AS `count` "
    //         . "FROM `gemstone_division` gd "
    //         . "WHERE 1;";
    //     $query = $this->db->query($sql);
    //     if ($query) {
    //         return $query->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.3
    // public function get_division_list_paged($page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "name": {
    //             $sort_sql = "gd.`division_name` $sort->direction";
    //             break;
    //         }
    //         case "size": {
    //             $sort_sql = "`size` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "gd.`division_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $size_sql = "(SELECT COUNT(ga.`admin_id`) "
    //         . "FROM `gemstone_admin` ga "
    //         . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
    //         . "WHERE gac.`division_id` = gd.`division_id`)";
    //     $sql = "SELECT gd.`division_id` "
    //         . ", gd.`division_name` "
    //         . ", $size_sql AS `size` "
    //         . "FROM `gemstone_division` gd "
    //         . "WHERE 1 "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp";
    //     $query = $this->db->query($sql);
    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //last update 3.9.3
    // public function get_division_detail($division_id)
    // {
    //     $response = new GSResponse();
    //     $sql = "SELECT * "
    //         . "FROM `gemstone_division` gd "
    //         . "WHERE gd.`division_id` = ?;";
    //     $query = $this->db->query($sql, [$division_id]);
    //     if ($query) {
    //         $response->success = true;
    //         $response->data = $query->row();
    //     } else {
    //         $response->message = "Query failed.";
    //     }
    //     return $response;
    // }

    //last update 3.9.3
    // public function add_division($input)
    // {
    //     $response = new GSResponse();
    //     $input_check = $this->check_input_exists($input, ["division_name"]);
    //     if ($input_check->valid === true) {
    //         $duplicate = $this->check_duplicate("gemstone_division", "division_name", $input->division_name);
    //         if ($duplicate === false) {
    //             $sql = "INSERT INTO `gemstone_division` (`division_name`) "
    //                 . "VALUES (?);";
    //             $query = $this->db->query($sql, [$input->division_name]);
    //             if ($query) {
    //                 $response->success = true;
    //                 $response->id = $this->db->insert_id();
    //                 $response->message = "New division added.";
    //             } else {
    //                 $response->message = "Failed to add division. Please contact system administrator.";
    //             }
    //         } else {
    //             $response->message = "Division name already exist.";
    //         }
    //     } else {
    //         $response->message = "Incomplete input.";
    //     }
    //     return $response;
    // }

    //last update 3.9.3
    // public function edit_division($input)
    // {
    //     $response = new GSResponse();
    //     $input_check = $this->check_input_exists($input, ["division_name", "division_id"]);
    //     if ($input_check->valid === true) {
    //         $duplicate = $this->check_other_duplicate("gemstone_division", "division_name", $input->division_name, "division_id", $input->division_id);
    //         if ($duplicate === false) {
    //             $sql = "UPDATE `gemstone_division` "
    //                 . "SET `division_name` = ? "
    //                 . "WHERE `division_id` = ?;";
    //             $query = $this->db->query($sql, [$input->division_name, $input->division_id]);
    //             if ($query) {
    //                 $response->success = true;
    //                 $response->id = $input->division_id;
    //                 $response->message = "Division detail changed.";
    //             } else {
    //                 $response->message = "Failed to update division detail. Please contact system administrator.";
    //             }
    //         } else {
    //             $response->message = "Division name already exist.";
    //         }
    //     } else {
    //         $response->message = "Incomplete input.";
    //     }
    //     return $response;
    // }

    //last update 3.9.4
    // public function get_division_position_count($division_id)
    // {
    //     $sql = "SELECT COUNT(gac.`admin_tier_id`) AS `count` "
    //         . "FROM `gemstone_access_control` gac "
    //         . "WHERE gac.`division_id` = ?;";
    //     $query = $this->db->query($sql, [$division_id]);
    //     if ($query) {
    //         return $query->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.4
    // public function get_division_position_list_paged($division_id, $page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "position": {
    //             $sort_sql = "gac.`level_title` $sort->direction";
    //             break;
    //         }
    //         case "qty": {
    //             $sort_sql = "`qty` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "gac.`admin_tier_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $qty_sql = "(SELECT COUNT(ga.`admin_id`) "
    //         . "FROM `gemstone_admin` ga "
    //         . "WHERE ga.`admin_tier_id` = gac.`admin_tier_id`)";
    //     $sql = "SELECT gac.`admin_tier_id` "
    //         . ", gac.`admin_level` "
    //         . ", gac.`level_title` "
    //         . ", $qty_sql AS `qty` "
    //         . "FROM `gemstone_access_control` gac "
    //         . "WHERE gac.`division_id` = ? "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp;";
    //     $query = $this->db->query($sql, [$division_id]);

    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //last update 3.9.3
    // public function get_division_admin_count($division_id)
    // {
    //     $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
    //         . "FROM `gemstone_admin` ga "
    //         . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
    //         . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
    //         . "WHERE gd.`division_id` = ?;";
    //     $query = $this->db->query($sql, [$division_id]);
    //     if ($query) {
    //         return $query->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.3
    // public function get_division_admin_list_paged($division_id, $page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "name": {
    //             $sort_sql = "ga.`admin_name` $sort->direction";
    //             break;
    //         }
    //         case "position": {
    //             $sort_sql = "gac.`level_title` $sort->direction";
    //             break;
    //         }
    //         case "active": {
    //             $sort_sql = "ga.`last_active` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "ga.`admin_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $sql = "SELECT ga.`admin_id` "
    //         . ", ga.`admin_name` "
    //         . ", ga.`last_active` "
    //         . ", gac.`level_title` "
    //         . "FROM `gemstone_admin` ga "
    //         . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
    //         . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
    //         . "WHERE gd.`division_id` = ? "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp;";
    //     $query = $this->db->query($sql, [$division_id]);

    //     if ($query) {
    //         return $query->result();
    //     } else {
    //         return false;
    //     }
    // }

    //DIVISION END
    //ACTION START
    //last update 3.9.4
    // public function revoke_action_access($action_id, $position_id)
    // {
    //     $response = new GSResponse();
    //     $sql = "DELETE FROM `gemstone_access_rule` "
    //         . "WHERE `position_id` = ? AND `action_id` = ?;";
    //     $query = $this->db->query($sql, [$position_id, $action_id]);
    //     if ($query) {
    //         $response->success = true;
    //         $response->message = "Access revoked.";
    //     } else {
    //         $response->message = "Failed to revoke access. Please contact system administrator.";
    //     }
    //     return $response;
    // }

    //last update 3.9.4
    // public function grant_action_access($action_id, $position_id)
    // {
    //     $response = new GSResponse();
    //     $sql = "INSERT INTO `gemstone_access_rule` (`position_id`, `action_id`, `authorized_date`, `authorizing_admin_id`) "
    //         . "VALUES (?, ?, NOW(), ?)";
    //     $query = $this->db->query($sql, [$position_id, $action_id, $this->session->userdata("admin_id")]);
    //     if ($query) {
    //         $response->success = true;
    //         $response->message = "Access granted.";
    //     } else {
    //         $response->message = "Failed to grant access. Please contact system administrator.";
    //     }
    //     return $response;
    // }

    //last update 3.9.4
    public function check_action_access($action_id)
    {
        $response = new GSResponse();
        $response->action_id = false;
        if (is_numeric($action_id) && strpos($action_id, "-") === false) {
            $response->action_id = $action_id;
        } else {
            $sql = "SELECT ga.`action_id` "
                . "FROM `gemstone_action` ga "
                . "WHERE ga.`action_code` = ?";
            $query = $this->db->query($sql, [$action_id]);
            if ($query && $query->num_rows() > 0) {
                $response->action_id = $query->row()->action_id;
            } else {
                $response->message = "Action not found.";
            }
        }
        if ($this->session->userdata("position_id") == 1) {
            $response->valid = true;
            $response->message = "You are logged in as Super Admin.";
        } else {
            $sql = "SELECT gar.`rule_id`, gar.`action_id` "
                . "FROM `gemstone_access_rule` gar "
                . "WHERE gar.`action_id` = ? AND gar.`position_id` = ?;";
            $query = $this->db->query($sql, [$response->action_id, $this->session->userdata("position_id")]);
            if ($query) {
                $response->success = true;
                if ($query->num_rows() > 0) {
                    $response->valid = true;
                    $response->message = "You are authorized to use this function.";
                } else {
                    $response->message = "You are not authorized to use this function.";
                }
            } else {
                $response->message = "Action access check failed.";
            }
        }
        return $response;
    }

    //ACTION END
    //ADMIN AND SESSION START
    //last update 3.9.4
    public function get_all_admin_list()
    {
        $sql = "SELECT ga.`admin_id` "
            . ", ga.`admin_name` "
            . "FROM `gemstone_admin` ga "
            . "WHERE 1 "
            . "ORDER BY ga.`admin_name` ASC;";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 3.9.4
    // public function get_admin_count($admin_status_id = false)
    // {
    //     if ($admin_status_id !== false) {
    //         $where_sql = "WHERE ga.`admin_status` = " . $this->db->escape($admin_status_id);
    //     } else {
    //         $where_sql = "WHERE 1";
    //     }
    //     $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
    //         . "FROM  `gemstone_admin` ga "
    //         . $where_sql;
    //     $result = $this->db->query($sql);
    //     if ($result) {
    //         return $result->row()->count;
    //     } else {
    //         return 0;
    //     }
    // }

    //last update 3.9.4
    // public function get_admin_list_paged($page, $sort)
    // {
    //     $ipp = $this->get_item_per_page();
    //     $start = ($page - 1) * $ipp;
    //     $sort = $this->get_sort_direction($sort);
    //     switch ($sort->keyword) {
    //         case "name": {
    //             $sort_sql = "ga.`admin_name` $sort->direction";
    //             break;
    //         }
    //         case "fullname": {
    //             $sort_sql = "ga.`admin_fullname` $sort->direction";
    //             break;
    //         }
    //         case "position": {
    //             $sort_sql = "gac.`level_title` $sort->direction";
    //             break;
    //         }
    //         default: {
    //             $sort_sql = "ga.`admin_id` $sort->direction";
    //             break;
    //         }
    //     }
    //     $sql = "SELECT ga.`admin_name` "
    //         . ", ga.`admin_fullname` "
    //         . ", ga.`admin_id` "
    //         . ", gac.`level_title` "
    //         . ", gac.`admin_level` "
    //         . ", ga.`admin_status` "
    //         . "FROM `gemstone_admin` ga "
    //         . "JOIN `gemstone_access_control` gac ON ga.`admin_tier_id` = gac.`admin_tier_id` "
    //         . "ORDER BY $sort_sql "
    //         . "LIMIT $start, $ipp;";
    //     $query = $this->db->query($sql);

    //     return $query->result();
    // }

    //last update 3.9.4
    public function get_admin_detail($admin_id)
    {
        $response = $this->prepare_response();
        $sql = "SELECT ga.`admin_id` "
            . ", ga.`admin_name` "
            . ", ga.`admin_fullname` "
            . ", ga.`admin_phone` "
            . ", ga.`admin_email` "
            . ", ga.`last_active` "
            . ", ga.`admin_tier_id` "
            . ", ga.`admin_status` "
            . ", ga.`photo_path` "
            . ", gac.`admin_level` "
            . ", gac.`level_title` "
            . ", gac.`fulltime` "
            . ", gd.`division_id` "
            . ", gd.`division_name` "
            . "FROM  `gemstone_admin` ga "
            . "JOIN `gemstone_access_control` gac ON ga.`admin_tier_id` = gac.`admin_tier_id` "
            . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
            . "WHERE `admin_id` = ?;";
        $query = $this->db->query($sql, array($admin_id));
        if ($query) {
            if ($query->num_rows() > 0) {
                $response->success = true;
                $response->data = $query->row();
            } else {
                $response->message = "No data found.";
            }
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    //last update 3.9.4
    public function add_admin($input)
    {
        $response = new GSResponse();
        $error = false;
        $check = $this->check_input_exists($input, array("admin_name", "admin_fullname", "admin_phone", "admin_email", "admin_tier_id", "new_password"));
        if ($check->valid) {
            $duplicate = $this->check_duplicate("gemstone_admin", "admin_name", $input->admin_name);
            $check_at = strpos($input->admin_name, "@");
            if ($duplicate === false && $check_at === false) {
                $empty_email = false;
                $duplicate_email = false;
                if (!empty($input->admin_email)) {
                    $duplicate_email = $this->check_duplicate("gemstone_admin", "admin_email", $input->admin_email);
                } else {
                    $empty_email = true;
                }
                if ($duplicate_email === false || $empty_email) {
                    $sql = "INSERT INTO `gemstone_admin` (`admin_name`, `admin_fullname`, `admin_phone`, `admin_email`,  `admin_tier_id`, `password`, `admin_status`) "
                        . "VALUE (?, ?, ?, ?, ?, ?, 1);";
                    $hashed_pass = password_hash($input->new_password, PASSWORD_BCRYPT);
                    $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $hashed_pass));
                    if ($result) {
                        $response->id = $this->db->insert_id();
                        if (isset($input->admin_photo)) {
                            $file_check = $this->check_temp_file_uploaded("admin_photo", false);
                            if ($file_check->valid) {
                                if ($file_check->uploaded) {
                                    $file_name = "admin_photo_" . $response->id;
                                    $photo_path = base_url() . "uploads/admin_photo/" . $file_name . "." . $file_check->extension;
                                    $this->prepare_image_upload("./uploads/admin_photo/", $file_name, true);
                                    if ($this->upload->do_upload('admin_photo')) {
                                        $upload_success = true;
                                        $sql = "UPDATE `gemstone_admin` "
                                            . "SET `photo_path` = ? "
                                            . "WHERE `admin_id` = ?;";
                                        $this->db->query($sql, array($photo_path, $response->id));
                                    } else {
                                        $upload_success = false;
                                    }
                                } else {
                                    $upload_success = true;
                                }
                                if ($upload_success === false) {
                                    $error = true;
                                    $response->message = "Uploading image failed.";
                                }
                            } else {
                                $response->message = "Uploading temp image failed. " . $file_check->message;
                            }
                        }
                    } else {
                        $error = true;
                        $response->message = "Query failed.";
                    }
                    if ($error === false) {
                        $response->success = true;
                        $response->message = "New admin added.";
                        $this->update_activity($input->log_id, "$input->admin_name", "view_admin_detail?id=$response->id", json_encode($input));
                    }
                } else {
                    $response->message = "Email already used for another account.";
                }
            } else {
                if ($check_at !== false) {
                    $response->message = "Cannot use email as admin name.";
                } else {
                    $response->message = "Duplicate admin name.";
                }
            }
        } else {
            $response->message = "Not enough data provided.";
            $response->invalid_input = $check->invalid_input;
        }
        return $response;
    }

    //last update 3.9.4
    public function update_admin_info($input)
    {
        $response = $this->prepare_response();
        $error = false;
        $response->id = $input->admin_id;
        $check = $this->check_input_exists($input, array("admin_name", "admin_fullname", "admin_phone", "admin_email", "admin_tier_id", "new_password", "admin_id"));
        if ($check->valid) {
            if ($input->admin_id == 1) {
                $input->admin_tier_id = 1;
            }
            if ($this->get_admin_tier_level($input->admin_tier_id) <= $this->session->userdata("admin_level")) {
                $duplicate = $this->check_other_duplicate("gemstone_admin", "admin_name", $input->admin_name, "admin_id", $input->admin_id);
                $check_at = strpos($input->admin_name, "@");
                if ($duplicate === false && $check_at === false) {
                    $empty_email = false;
                    $duplicate_email = false;
                    if (!empty($input->admin_email)) {
                        $duplicate_email = $this->check_other_duplicate("gemstone_admin", "admin_email", $input->admin_email, "admin_id", $input->admin_id);
                    } else {
                        $empty_email = true;
                    }
                    if ($duplicate_email === false || $empty_email) {
                        if ($input->new_password == "") {
                            $sql = "UPDATE `gemstone_admin` "
                                . "SET `admin_name` = ?, `admin_fullname` = ?, `admin_phone` = ?, `admin_email` = ?, `admin_tier_id` = ? "
                                . "WHERE `admin_id` = ?;";
                            $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $input->admin_id));
                        } else {
                            $hashed_pass = password_hash($input->new_password, PASSWORD_BCRYPT);
                            $sql = "UPDATE `gemstone_admin` "
                                . "SET `admin_name` = ?, `admin_fullname` = ?, `admin_phone` = ?, `admin_email` = ?, `admin_tier_id` = ?, `password` = ? "
                                . "WHERE `admin_id` = ?;";
                            $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $hashed_pass, $input->admin_id));
                        }
                        if ($result) {
                            if (isset($input->admin_photo)) {
                                $file_check = $this->check_temp_file_uploaded("admin_photo", false);
                                if ($file_check->valid) {
                                    if ($file_check->uploaded) {
                                        $file_name = "admin_photo_" . $response->id;
                                        $photo_path = base_url() . "uploads/admin_photo/" . $file_name . "." . $file_check->extension;
                                        $this->prepare_image_upload("./uploads/admin_photo/", $file_name, true);
                                        if ($this->upload->do_upload('admin_photo')) {
                                            $sql = "UPDATE `gemstone_admin` "
                                                . "SET `photo_path` = ? "
                                                . "WHERE `admin_id` = ?;";
                                            $this->db->query($sql, array($photo_path, $input->admin_id));
                                        } else {
                                            $error = true;
                                        }
                                    }
                                } else {
                                    $response->message = "Uploading temp image failed. " . $file_check->message;
                                }
                            }
                            if ($error === false) {
                                $response->success = true;
                                $response->message = "Admin info updated.";
                                $this->update_activity($input->log_id, "$input->admin_name", "view_admin_detail?id=$response->id", json_encode($input));
                            } else {
                                $response->message = "Uploading image failed.";
                            }
                        } else {
                            $response->message = "Query failed.";
                        }
                    } else {
                        $response->message = "Email already used for another account.";
                    }
                } else {
                    if ($check_at !== false) {
                        $response->message = "Cannot use email as admin name.";
                    } else {
                        $response->message = "Duplicate admin name.";
                    }
                }
            } else {
                $response->message = "Cannot change position to higher level than you currently are.";
            }
        } else {
            $response->message = "Not enough data provided.";
            $response->invalid_input = $check->invalid_input;
        }
        return $response;
    }

    //last update 3.9.4
    public function deactivate_admin($admin_id, $log_id = false)
    {
        $response = $this->prepare_response();
        $sql = "UPDATE `gemstone_admin` "
            . "SET `admin_status` = 2 "
            . "WHERE `admin_id` = ?;";
        $query = $this->db->query($sql, array($admin_id));
        if ($query) {
            $response->success = true;
            $response->message = "Admin deactivated";
            $admin_info = $this->get_admin_detail($admin_id);
            $this->update_activity($log_id, $admin_info->data->admin_name, "view_admin_detail?id=$admin_id");
            $this->logout($admin_id);
        } else {
            $response->message = "Query failed.";
        }

        return $response;
    }

    //last update 3.9.4
    public function reactivate_admin($admin_id, $log_id = false)
    {
        $response = $this->prepare_response();
        $sql = "UPDATE `gemstone_admin` "
            . "SET `admin_status` = 1 "
            . "WHERE `admin_id` = ?;";
        $query = $this->db->query($sql, array($admin_id));
        if ($query) {
            $response->success = true;
            $response->message = "Admin reactivated";
            $admin_info = $this->get_admin_detail($admin_id);
            $this->update_activity($log_id, $admin_info->data->admin_name, "view_admin_detail?id=$admin_id");
        } else {
            $response->message = "Query failed.";
        }

        return $response;
    }

    //last update 3.9.3
    public function get_admin_activity_list($filter = "|||")
    {
        $filter_sql = $this->get_admin_activity_filter($filter);
        $sql = "SELECT gal.`log_id` "
            . ", gal.`log_date` "
            . ", gal.`actor` "
            . ", ga.`admin_name` "
            . ", gal.`action_id` "
            . ", act.`action_name` "
            . ", gal.`description` "
            . ", gal.`target_link` "
            . ", gal.`label` "
            . ", gal.`legacy_log` "
            . "FROM `gemstone_activity_log` gal "
            . "JOIN `gemstone_action` act ON act.`action_id` = gal.`action_id` "
            . "JOIN `gemstone_admin` ga ON ga.`admin_id` = gal.`actor` "
            . "WHERE 1 "
            . $filter_sql
            . "ORDER BY gal.`log_date` DESC";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 3.9.4
    public function get_admin_activity_filter($filter = "|||")
    {
        $filter_sql = "";
        if ($filter !== "|||") {
            $filter = explode("|", $filter);
            if ($filter[0] != "" && $filter[1] != "") {
                $filter[0] = $filter[0] . " 00:00:00";
                $filter[1] = $filter[1] . " 23:59:59";
                $filter_sql .= "AND (gal.`log_date` BETWEEN {$this->db->escape($filter[0])} AND {$this->db->escape($filter[1])}) ";
            }
            if ($filter[2] != "all") {
                $filter_sql .= "AND gal.`actor` = {$this->db->escape($filter[2])} ";
            }
            if ($filter[3] != "") {
                $filter_sql .= "AND " . $this->generate_sql_like_string("action_name", $filter[3], "act") . " ";
            }
        }
        return $filter_sql;
    }

    //last update 2.6.5
    public function get_admin_level_title($admin_title)
    {
        $response = $this->prepare_response();
        $sql = "SELECT * "
            . "FROM `gemstone_access_control` "
            . "WHERE `level_title` = ?;";
        $query = $this->db->query($sql, array($admin_title));
        if ($query) {
            $response->success = true;
            $response->found = false;
            if ($query->num_rows() > 0) {
                $response->found = true;
                $response->data = $query->row();
            } else {
                $response->message = "Title not found.";
            }
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    //last update 2.6.9
    public function get_admin_tier_level($admin_tier_id)
    {
        $sql = "SELECT `admin_level` "
            . "FROM `gemstone_access_control` "
            . "WHERE `admin_tier_id` = ?;";
        $query = $this->db->query($sql, $admin_tier_id);
        if ($query) {
            if ($query->num_rows() == 1) {
                return $query->row()->admin_level;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //last update 3.8.7
    public function get_admin_id($username)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
            $sql = "SELECT `admin_id` "
                . "FROM  `gemstone_admin` "
                . "WHERE `admin_name` = ?;";
        } else {
            $sql = "SELECT `admin_id` "
                . "FROM  `gemstone_admin` "
                . "WHERE `admin_email` = ?;";
        }
        $result = $this->db->query($sql, array($username));

        if ($result->num_rows() > 0) {
            return $result->row()->admin_id;
        } else {
            return false;
        }
    }

    //last checked 2.4
    public function get_admin_level($username)
    {
        $sql = "SELECT uac.`admin_level` "
            . "FROM  `gemstone_admin` adm "
            . "JOIN `gemstone_access_control` uac "
            . "ON uac.`admin_tier_id` = adm.`admin_tier_id` "
            . "WHERE `admin_name` = ?;";
        $result = $this->db->query($sql, array($username));

        if ($result->num_rows() > 0) {
            return $result->row()->admin_level;
        } else {
            return false;
        }
    }

    //last checked 2.4
    public function get_admin_tier_id($username)
    {
        $sql = "SELECT `admin_tier_id` "
            . "FROM  `gemstone_admin` "
            . "WHERE `admin_name` = ?;";
        $result = $this->db->query($sql, array($username));

        if ($result->num_rows() > 0) {
            return $result->row()->admin_tier_id;
        } else {
            return false;
        }
    }

    //last checked 2.4
    public function get_level_title($admin_tier_id)
    {
        $sql = "SELECT `level_title` "
            . "FROM  `gemstone_access_control` "
            . "WHERE `admin_tier_id` = ?;";
        $result = $this->db->query($sql, array($admin_tier_id));

        if ($result->num_rows() > 0) {
            return $result->row()->level_title;
        } else {
            return false;
        }
    }

    //last checked 2.4
    public function get_title_level($level_title)
    {
        $sql = "SELECT `admin_level` "
            . "FROM  `gemstone_access_control` "
            . "WHERE `level_title` = ?;";
        $result = $this->db->query($sql, array($level_title));

        if ($result->num_rows() > 0) {
            return $result->row()->admin_level;
        } else {
            return false;
        }
    }

    //last update 3.7.1
    public function force_logout_all($clear_session = false)
    {
        $sql = "UPDATE `gemstone_session_token` "
            . "SET `logged_out` = 1 "
            . "WHERE `logged_out` = 0";
        $this->db->query($sql);
        if ($clear_session === true) {
            $sql = "DELETE FROM `gemstone_session_token` WHERE 1;";
            $this->db->query($sql);
        }
    }

    //last update 3.1.2
    public function generate_session_token()
    {
        return bin2hex(openssl_random_pseudo_bytes(13));
    }

    //last_update 3.1.2
    public function check_session_token($admin_id)
    {
        $response = new stdClass();
        $response->valid = false;
        $response->message = "Unknown failure.";
        $active_session = $this->get_active_session($admin_id);
        if ($active_session !== false) {
            if (SESSION_MODE === "EXCLUSIVE") {
                $response->message = "The account you are trying to access is currently in use.";
            } elseif (SESSION_MODE === "MULTI_SESSION") {
                $response->valid = true;
                $session_count = $this->get_active_session_count($admin_id);
                if ($session_count > 1) {
                    $response->message = "There are $session_count sessions currently active with this credential.";
                } else {
                    $response->message = "This is the only session currently active with this credential.";
                }
            } else {
                $response->message = "Invalid server configuration. Contact Coralis Studio.";
            }
        } else {
            $response->message = "Logged in successfully.";
            $response->valid = true;
        }
        if ($response->valid === true) {
            $this->log_new_session($admin_id);
        }

        return $response;
    }

    //last_update 3.1.2
    public function get_active_session_count($admin_id)
    {
        $sql = "SELECT COUNT(`session_token`) AS `count` "
            . "FROM `gemstone_session_token` "
            . "WHERE `logged_out` = 0 AND `admin_id` = ? AND `last_active` > (NOW() - INTERVAL " . TIMEOUT_PERIOD . " MINUTE);";
        $query = $this->db->query($sql, array($admin_id));
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    //last_update 3.1.2
    public function get_active_session($admin_id)
    {
        $sql = "SELECT * "
            . "FROM `gemstone_session_token` "
            . "WHERE `logged_out` = 0 AND `admin_id` = ? AND `last_active` > (NOW() - INTERVAL " . TIMEOUT_PERIOD . " MINUTE) "
            . "ORDER BY `last_active` DESC "
            . "LIMIT 1;";
        $query = $this->db->query($sql, array($admin_id));
        if ($query) {
            if ($query->num_rows() > 0) {
                return $query->row();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //last_update 3.1.2
    public function log_new_session($admin_id)
    {
        $this->load->library('user_agent');
        $device_info = $this->agent->agent . "|"
            . $this->agent->platform . "|"
            . $this->agent->is_browser . "|"
            . $this->agent->is_robot . "|"
            . $this->agent->is_mobile . "|"
            . $this->agent->browser . "|"
            . $this->agent->version;
        $access_ip = $this->input->ip_address();
        $token = $this->generate_session_token();
        $sql = "INSERT INTO `gemstone_session_token` (`session_token`, `admin_id`, `device_info`, `access_ip`, `session_start`, `last_active`) "
            . "VALUES(?,?,?,?,NOW(),NOW());";
        $query = $this->db->query($sql, array($token, $admin_id, $device_info, $access_ip));
        if ($query) {
            $this->session->set_userdata("session_token", $token);
            $this->session->set_userdata("last_active", date("Y-m-d H:i:s"));
            return true;
        } else {
            return false;
        }
    }

    //last update 3.4.2
    public function get_online_admin_list()
    {
        $sql = "SELECT ga.`admin_id`, ga.`admin_name`, gst.`last_active`, gst.`session_start` "
            . "FROM `gemstone_session_token` gst "
            . "JOIN `gemstone_admin` ga ON ga.`admin_id` = gst.`admin_id` "
            . "WHERE gst.`logged_out` = 0 AND gst.`last_active` > (NOW() - INTERVAL 15 MINUTE) "
            . "GROUP BY gst.`admin_id`;";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 3.4.2
    public function get_my_recent_activity_count($days = 7)
    {
        $sql = "SELECT COUNT(`log_id`)AS `count`, (CAST(`log_date` AS DATE)) AS `date` "
            . "FROM `gemstone_activity_log` "
            . "WHERE `actor` = ? AND `log_date` >= (NOW() - INTERVAL $days DAY) "
            . "GROUP BY (CAST(`log_date` AS DATE))";
        $query = $this->db->query($sql, [$this->session->userdata("admin_id")]);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 3.1.5
    public function update_last_active()
    {
        $sql = "UPDATE `gemstone_session_token` SET `last_active` = NOW() WHERE `session_token` = ?;";
        $this->db->query($sql, array($this->session->userdata("session_token")));
    }

    //last update 3.5.4
    public function check_session_validity()
    {
        $valid = false;
        $sql = "SELECT * "
            . "FROM `gemstone_session_token` gst "
            . "JOIN `gemstone_admin` ga ON ga.`admin_id` = gst.`admin_id` "
            . "WHERE gst.`session_token` = ? AND ga.`admin_status` = 1 "
            . "LIMIT 1;";
        $query = $this->db->query($sql, array($this->session->userdata("session_token")));
        if ($query && $query->num_rows() > 0 && $query->row()->logged_out != 1) {
            $valid = true;
        }

        return $valid;
    }

    //last update  3.0b
    public function get_admin_activity_rate()
    {
        $sql = "SELECT ga.`admin_id`, ga.`admin_name`, COUNT(gal.`log_id`) AS `count` "
            . "FROM `gemstone_admin` ga "
            . "LEFT JOIN `gemstone_activity_log` gal "
            . "ON ga.`admin_id` = gal.`actor` "
            . "GROUP BY gal.`actor` "
            . "ORDER BY `count` DESC, gal.`log_date` DESC "
            . "LIMIT 0, 500";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return $this->db->last_query();
        }
    }

    //last update 2.5
    public function get_all_admin_level()
    {
        $sql = "SELECT * "
            . "FROM  `gemstone_access_control`;";
        $result = $this->db->query($sql);

        return $result->result();
    }

    //last update 3.9.4
    public function get_activity_log($admin_id, $page, $sort)
    {
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;

        if ($sort == "action") {
            $sort_text = " ORDER BY gac.`action_name` ASC, gal.`log_id` DESC ";
        } else {
            $sort_text = " ORDER BY gal.`log_date` DESC, gal.`log_id` DESC ";
        }

        $sql = "SELECT * "
            . "FROM `gemstone_activity_log` gal "
            . "JOIN `gemstone_admin` ga ON ga.`admin_id` = gal.`actor` "
            . "JOIN `gemstone_action` gac ON gac.`action_id` = gal.`action_id` "
            . "WHERE gal.`actor` = ? "
            . $sort_text
            . "LIMIT $start, $ipp;";
        $query = $this->db->query($sql, [$admin_id]);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    //last update 2.5
    // public function get_activity_count($admin_id)
    // {
    //     $sql = "SELECT COUNT(`log_id`) AS `count` "
    //         . "FROM  `gemstone_activity_log` "
    //         . "WHERE `actor` = ?;";
    //     $result = $this->db->query($sql, array($admin_id));
    //     if ($result) {
    //         return $result->row()->count;
    //     } else {
    //         return false;
    //     }
    // }

    //last checked 2.4
    public function initialize_admin()
    {
        $username = $this->super_admin_username;
        $password = $this->super_admin_password;
        $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

        if ($this->check_super_admin_exist() === false) {
            $sql = "INSERT INTO `gemstone_admin` (`admin_name`, `admin_fullname`, `admin_email`, `admin_tier_id`, `last_active`,`password`) "
                . "VALUE (?, 'Super Admin', gemstone@coralisstudio.com', 1, NOW(), ?);";
            return $this->db->query($sql, array($username, $hashed_pass));
        } else {
            return false;
        }
    }

    //last update 3.8.7
    public function check_login($username, $password)
    {
        $response = new stdClass();
        $response->success = false;
        $response->message = "";
        $hash = $this->get_hash($username);
        if (empty($password)) {
            $response->message = "Password cannot be empty";
            return $response;
        }
        if (password_verify($password, $hash)) {
            $id = $this->get_admin_id($username);
            $this->load->model("gemstone_model");
            $check = $this->gemstone_model->get_admin_detail($id);
            if ($check->success && $check->data->admin_status == 1) {
                $time_valid = false;
                $working_hour = $this->get_working_hour();
                $start_time = new DateTime(date("Y-m-d $working_hour->start"));
                $end_time = new DateTime(date("Y-m-d $working_hour->end"));
                $current_time = new DateTime(date("Y-m-d H:i:s"));
                $day_code = "work_" . strtolower(date("D"));
                $settings = $this->get_gemstone_settings();
                if ($current_time >= $start_time && $current_time < $end_time && $settings->$day_code == 1) {
                    $time_valid = true;
                }
                $admin_info = $check->data;
                if ($time_valid === true || $admin_info->admin_level >= 5) {
                    $this->session->set_userdata("admin_id", $id);
                    $this->session->set_userdata("admin_name", $admin_info->admin_name);
                    $this->session->set_userdata("position_id", $admin_info->admin_tier_id);
                    $this->session->set_userdata("admin_tier_id", $admin_info->admin_tier_id);
                    $this->session->set_userdata("admin_level", $admin_info->admin_level);
                    $this->session->set_userdata("admin_title", $admin_info->level_title);
                    $this->session->set_userdata("division_id", $admin_info->division_id);
                    $this->session->set_userdata("division_name", $admin_info->division_name);
                    $sql = "UPDATE `gemstone_admin` "
                        . "SET `last_active` = NOW() "
                        . "WHERE `admin_name` = ?;";
                    $this->db->query($sql, array($username));
                    $response->success = true;
                    $response->admin_id = $id;
                    $response->admin_tier_id = $admin_info->admin_tier_id;
                    $this->log_activity(1);
                } else {
                    $response->message = "Cannot log in outside of working hour.<br>Contact administrator for more information.";
                }
            } else {
                $response->message = "Akun anda diblokir.<br>Hubungi system administrator untuk informasi lebih lanjut.";
            }
        } else {
            $response->message = "Invalid username / password";
        }

        return $response;
    }

    //last checked 3.2.0a
    public function check_super_admin_exist()
    {
        $sql = "SELECT * "
            . "FROM  `gemstone_admin` "
            . "WHERE `admin_name` = ?;";
        $result = $this->db->query($sql, array($this->super_admin_username));

        if ($result->num_rows() > 0) {
            return true;
        }
        return false;
    }

    //last checked 3.2.0a
    public function get_hash($username)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
            $sql = "SELECT `password` "
                . "FROM  `gemstone_admin` "
                . "WHERE `admin_name` = ?;";
        } else {
            $sql = "SELECT `password` "
                . "FROM  `gemstone_admin` "
                . "WHERE `admin_email` = ?;";
        }
        $query = $this->db->query($sql, array($username));
        if ($query && $query->num_rows() > 0) {
            return $query->row()->password;
        } else {
            return false;
        }
    }

    //ADMIN AND SESSION END
    //last updated 3.9.9
    public function get_gemstone_action_list()
    {
        $sql = "SELECT * FROM `gemstone_action` WHERE 1";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }
}

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================