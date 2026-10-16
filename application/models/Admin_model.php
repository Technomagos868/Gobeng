<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
            . "FROM `gemstone_admin` ga "
            . "WHERE 1 $where_sql";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    private function filter_list(string $search): string
    {
        $where_sql = "";
        if (!empty($search)) {
            $where_sql = "AND ("
                . $this->generate_sql_like_string("admin_name", $search, "ga")
                . " OR " . $this->generate_sql_like_string("admin_fullname", $search, "ga")
                . " OR " . $this->generate_sql_like_string("admin_phone", $search, "ga")
                . " OR " . $this->generate_sql_like_string("admin_email", $search, "ga")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "id|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "username": {
                $sort_sql = "ga.`admin_name` $sort->direction";
                break;
            }
            case "admin_fullname": {
                $sort_sql = "ga.`admin_fullname` $sort->direction";
                break;
            }
            case "level_title": {
                $sort_sql = "gac.`level_title` $sort->direction";
                break;
            }
            default: {
                $sort_sql = "ga.`admin_id` $sort->direction";
                break;
            }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT ga.*, gac.* "
            . "FROM `gemstone_admin` ga "
            . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
            . "WHERE 1 $where_sql "
            . "ORDER BY $sort_sql "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return [];
        }
    }
    public function get_list_all(): array
    {
        $sql = "SELECT ga.* "
            . "FROM `gemstone_admin` ga "
            . "WHERE 1 "
            . "ORDER BY `admin_name` ASC";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_detail(int $admin_id): GSResponse
    {
        $response = new GSResponse();
        $sql = "SELECT ga.*, gac.* "
            . "FROM `gemstone_admin` ga "
            . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
            . "WHERE ga.`admin_id` = ? ";
        $query = $this->db->query($sql, [$admin_id]);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->row();
            } else {
                $response->message = "Data not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E001.";
        }
        return $response;
    }

    public function get_assigned_branch(int $admin_id): GSResponse
    {
        $response = new GSResponse();
        $sql = "SELECT rba.*, lb.*, ga.`admin_name` AS `adding_admin_name` "
            . "FROM `rel_branch_admin` rba "
            . "JOIN `list_branch` lb ON lb.`branch_id` = rba.`branch_id` "
            . "JOIN `gemstone_admin` ga ON ga.`admin_id` = rba.`adding_admin_id` "
            . "WHERE rba.`admin_id` = ?;";
        $query = $this->db->query($sql, [$admin_id]);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->result();
                $response->message = "Assigned branch data fetched.";
            } else {
                $response->message = "Data not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E002.";
        }
        return $response;
    }

    public function add(object $input): GSResponse
    {
        $response = new GSResponse();
        $error = false;
        $check = $this->check_input_exists($input, array("admin_name", "admin_fullname", "admin_phone", "admin_email", "admin_tier_id", "password"));
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
                    $hashed_pass = password_hash($input->password, PASSWORD_BCRYPT);
                    $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $hashed_pass));
                    if ($result) {
                        $response->id = $this->db->insert_id();
                        $file_check = $this->check_temp_file_uploaded("photo_path", false);
                        if ($file_check == true) {
                            if ($file_check->uploaded) {
                                $file_name = "admin_photo_" . $response->id;
                                $photo_path = base_url() . "uploads/admin_photo/" . $file_name . "." . $file_check->extension;
                                $this->prepare_image_upload("./uploads/admin_photo/", $file_name, true);
                                if ($this->upload->do_upload('photo_path')) {
                                    $sql = "UPDATE `gemstone_admin` "
                                        . "SET `photo_path` = ? "
                                        . "WHERE `admin_id` = ?;";
                                    $this->db->query($sql, array($photo_path, $response->id));
                                } else {
                                    $error = true;
                                    $response->message = "Uploading image failed.";
                                }
                            }
                        } else {
                            $response->message = "Uploading temp image failed. " . $file_check->message;
                        }
                    } else {
                        $error = true;
                        $response->message = "Query failed.";
                    }
                    if ($error === false) {
                        $response->success = true;
                        $response->message = "New admin added.";
                        $this->update_activity($input->log_id, "$input->admin_name", "Admin/view_detail?id=$response->id", json_encode($input));
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

    public function update(object $input): GSResponse
    {
        $response = $this->prepare_response();
        $error = false;
        $response->id = $input->admin_id;
        $check = $this->check_input_exists($input, array("admin_name", "admin_fullname", "admin_phone", "admin_email", "admin_tier_id", "admin_id"));
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
                        if (!isset($input->password) || $input->password == "") {
                            $sql = "UPDATE `gemstone_admin` "
                                . "SET `admin_name` = ?, `admin_fullname` = ?, `admin_phone` = ?, `admin_email` = ?, `admin_tier_id` = ? "
                                . "WHERE `admin_id` = ?;";
                            $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $input->admin_id));
                        } else {
                            $hashed_pass = password_hash($input->password, PASSWORD_BCRYPT);
                            $sql = "UPDATE `gemstone_admin` "
                                . "SET `admin_name` = ?, `admin_fullname` = ?, `admin_phone` = ?, `admin_email` = ?, `admin_tier_id` = ?, `password` = ? "
                                . "WHERE `admin_id` = ?;";
                            $result = $this->db->query($sql, array($input->admin_name, $input->admin_fullname, $input->admin_phone, $input->admin_email, $input->admin_tier_id, $hashed_pass, $input->admin_id));
                        }
                        if ($result) {
                            $file_check = $this->check_temp_file_uploaded("photo_path", false);
                            if ($file_check == true) {
                                if ($file_check->uploaded) {
                                    $file_name = "admin_photo_" . $response->id;
                                    $photo_path = base_url() . "uploads/admin_photo/" . $file_name . "." . $file_check->extension;
                                    $this->prepare_image_upload("./uploads/admin_photo/", $file_name, true);
                                    if ($this->upload->do_upload('photo_path')) {
                                        $sql = "UPDATE `gemstone_admin` "
                                            . "SET `photo_path` = ? "
                                            . "WHERE `admin_id` = ?;";
                                        $this->db->query($sql, array($photo_path, $input->admin_id));
                                    } else {
                                        $error = true;
                                        $response->message = "Uploading image failed.";
                                    }
                                }
                            } else {
                                $response->message = "Uploading temp image failed. " . $file_check->message;
                            }
                            if ($error === false) {
                                $response->success = true;
                                $response->message = "Admin info updated.";
                                $this->update_activity($input->log_id, "$input->admin_name", "Admin/view_detail?id=$response->id", json_encode($input));
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

    public function search_admin(string $keyword): GSResponse
    {
        $response = new GSResponse();
        $sql = "SELECT * "
            . "FROM `gemstone_admin` ga "
            . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
            . "WHERE ga.`admin_name` LIKE '%" . $this->db->escape_like_str($keyword) . "%' "
            . "OR ga.`admin_fullname` LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
        $query = $this->db->query($sql);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->result();
            } else {
                $response->message = "Data not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E003.";
        }
        return $response;
    }

    private function filter_action(string $search): string
    {
        $where_sql = "";
        if (!empty($search)) {
            $where_sql = "AND ("
                . $this->generate_sql_like_string("action_name", $search, "ga")
                . " OR " . $this->generate_sql_like_string("description", $search, "gal")
                . " OR " . $this->generate_sql_like_string("target_link", $search, "gal")
                . " OR " . $this->generate_sql_like_string("label", $search, "gal")
                . ")";
        }
        return $where_sql;
    }

    public function get_activity_count(int $admin_id, string $search = ""): int
    {

        $where_sql = $this->filter_action($search);
        $sql = "SELECT COUNT(`log_id`) AS `count` "
            . "FROM  `gemstone_activity_log` gal "
            . "JOIN `gemstone_action` ga ON ga.`action_id` = gal.`action_id` "
            . "WHERE gal.`actor` = ? $where_sql;";
        $result = $this->db->query($sql, [$admin_id]);
        if ($result) {
            return $result->row()->count;
        } else {
            return false;
        }
    }

    public function get_activity_log_paged(int $admin_id, int $page = 1, string $search = ""): GSResponse
    {
        $response = new GSResponse();
        $where_sql = $this->filter_action($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT gal.*, ga.*, gal.`label` AS `reference` "
            . "FROM `gemstone_activity_log` gal "
            . "JOIN `gemstone_action` ga ON ga.`action_id` = gal.`action_id` "
            . "WHERE gal.`actor` = ? $where_sql "
            . "ORDER BY gal.`log_date` DESC "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql, [$admin_id]);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->result();
            } else {
                $response->message = "Data not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E004.";
        }
        return $response;
    }

}
