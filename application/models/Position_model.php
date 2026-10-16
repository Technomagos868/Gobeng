<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Position_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(gac.`admin_tier_id`) AS `count` "
            . "FROM `gemstone_access_control` gac "
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
                . $this->generate_sql_like_string("level_title", $search, "gac")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "id|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "level_title": {
                    $sort_sql = "gac.`level_title` $sort->direction";
                    break;
                }
            case "admin_level": {
                    $sort_sql = "gac.`admin_level` $sort->direction";
                    break;
                }
            case "division_name": {
                    $sort_sql = "gd.`division_name` $sort->direction";
                    break;
                }
            case "admin_count": {
                    $sort_sql = "`admin_count` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "gac.`admin_tier_id` $sort->direction";
                    break;
                }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT gac.*, gd.* "
            . ", (SELECT COUNT(`admin_id`) FROM `gemstone_admin` ga WHERE ga.`admin_tier_id` = gac.`admin_tier_id`) AS `admin_count` "
            . "FROM `gemstone_access_control` gac "
            . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
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
        $level_sql = "gac.`admin_level` <= " . $this->session->userdata("admin_level") . " ";
        if ($this->check_action_access("00-015")->valid === true) {
            $level_sql = "1";
        }
        $sql = "SELECT gac.* "
            . "FROM `gemstone_access_control` gac "
            . "WHERE {$level_sql} "
            . "ORDER BY gac.`admin_level` DESC";
        $query = $this->db->query($sql);
        if ($query) {
            return $query->result();
        } else {
            return [];
        }
    }

    public function get_detail(int $id): GSResponse
    {
        $response = new GSResponse();
        $sql = "SELECT gac.*, gd.* "
            . "FROM `gemstone_access_control` gac "
            . "JOIN `gemstone_division` gd ON gd.`division_id` = gac.`division_id` "
            . "WHERE gac.`admin_tier_id` = ? ";
        $query = $this->db->query($sql, [$id]);
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

    public function add(object $input): GSResponse
    {
        $response = new GSResponse();
        if (!isset($input->fulltime)) {
            $input->fulltime = 0;
        }
        $sql = "INSERT INTO `gemstone_access_control` "
            . "(`division_id` "
            . ", `admin_level` "
            . ", `level_title` "
            . ", `fulltime`) "
            . "VALUES(?, ?, ?, ?);";
        $query = $this->db->query($sql, [
            $input->division_id,
            $input->admin_level,
            $input->level_title,
            $input->fulltime
        ]);
        if ($query) {
            $response->id = $this->db->insert_id();
            $response->success = true;
            $response->message = "New position added.";
            $this->update_activity($input->log_id, $input->level_title, "Position/view_detail?id=$response->id", json_encode($input));
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    public function update(object $input): GSResponse
    {
        $response = new GSResponse();
        if (!isset($input->fulltime)) {
            $input->fulltime = 0;
        }
        $input_check = $this->check_input_exists($input, array("division_id", "admin_level", "level_title", "admin_tier_id"));
        if ($input_check->valid === true) {
            $sql = "UPDATE `gemstone_access_control` "
                . "SET `division_id` = ? "
                . ", `admin_level` = ? "
                . ", `level_title` = ? "
                . ", `fulltime` = ? "
                . "WHERE `admin_tier_id` = ?;";
            $query = $this->db->query($sql, [
                $input->division_id,
                $input->admin_level,
                $input->level_title,
                $input->fulltime,
                $input->admin_tier_id
            ]);
            if ($query) {
                $response->success = true;
                $response->id = $input->admin_tier_id;
                $response->message = "Position data updated.";
                $this->update_activity($input->log_id, $input->level_title, "Position/view_detail?id=$response->id", json_encode($input));
            } else {
                $response->message = "Query failed.";
            }
        }
        return $response;
    }

    public function get_admin_count(int $position_id): int
    {
        $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
            . "FROM `gemstone_admin` ga "
            . "WHERE ga.`admin_tier_id` = ?;";
        $query = $this->db->query($sql, [$position_id]);
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    public function get_admin_list_paged(int $position_id, int $page = 1, string $sort = "id|asc"): GSResponse
    {
        $response = new GSResponse();
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "admin_name": {
                    $sort_sql = "ga.`admin_name` $sort->direction";
                    break;
                }
            case "admin_fullname": {
                    $sort_sql = "ga.`admin_fullname` $sort->direction";
                    break;
                }
            case "admin_phone": {
                    $sort_sql = "ga.`admin_phone` $sort->direction";
                    break;
                }
            case "admin_email": {
                    $sort_sql = "ga.`admin_email` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "ga.`admin_id` $sort->direction";
                    break;
                }
        }
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT ga.* "
            . "FROM `gemstone_admin` ga "
            . "WHERE ga.`admin_tier_id` = ? "
            . "ORDER BY $sort_sql "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql, [$position_id]);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->result();
            } else {
                $response->message = "Data not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E002.";
        }
        return $response;
    }

    public function grant_action_access(object $input): GSResponse
    {
        $response = new GSResponse();
        $response->id = $input->position_id;
        $sql = "INSERT INTO `gemstone_access_rule` (`position_id`, `action_id`, `authorized_date`, `authorizing_admin_id`) "
            . "VALUES (?, ?, NOW(), ?)";
        $query = $this->db->query($sql, [$input->position_id, $input->action_id, $this->session->userdata("admin_id")]);
        if ($query) {
            $response->success = true;
            $response->message = "Access granted.";
            $this->load->model("Action_model", "action_model");
            $action = $this->action_model->get_detail($input->action_id)->data;
            $position = $this->get_detail($input->position_id)->data;
            $this->update_activity($input->log_id, "{$position->level_title} > {$action->action_name}", "Position/view_detail?id=$response->id", json_encode($input));
        } else {
            $response->message = "Failed to grant access. Please contact system administrator.";
        }
        return $response;
    }
    public function revoke_action_access(object $input): GSResponse
    {
        $response = new GSResponse();
        $response->id = $input->position_id;
        $sql = "DELETE FROM `gemstone_access_rule` "
            . "WHERE `position_id` = ? AND `action_id` = ?;";
        $query = $this->db->query($sql, [$input->position_id, $input->action_id]);
        if ($query) {
            $response->success = true;
            $response->message = "Access revoked.";
            $this->load->model("Action_model", "action_model");
            $action = $this->action_model->get_detail($input->action_id)->data;
            $position = $this->get_detail($input->position_id)->data;
            $this->update_activity($input->log_id, "{$position->level_title} > {$action->action_name}", "Position/view_detail?id=$response->id", json_encode($input));
        } else {
            $response->message = "Failed to revoke access. Please contact system administrator.";
        }
        return $response;
    }
}
