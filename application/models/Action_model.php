<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Action_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(ga.`action_id`) AS `count` "
            . "FROM `gemstone_action` ga "
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
                . $this->generate_sql_like_string("action_code", $search, "ga")
                . " OR " . $this->generate_sql_like_string("action_name", $search, "ga")
                . " OR " . $this->generate_sql_like_string("action_description", $search, "ga")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "action_code|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "action_code": {
                    $sort_sql = "ga.`action_code` $sort->direction";
                    break;
                }
            case "action_name": {
                    $sort_sql = "ga.`action_name` $sort->direction";
                    break;
                }
            case "description": {
                    $sort_sql = "ga.`action_description` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "ga.`action_id` $sort->direction";
                    break;
                }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT ga.* "
            . "FROM `gemstone_action` ga "
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
    public function get_access_by_position_paged(int $position_id, int $page = 1, $sort = "action_code|asc", string $search = ""): GSResponse
    {
        $response = new GSResponse();
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "action_code": {
                    $sort_sql = "ga.`action_code` $sort->direction";
                    break;
                }
            case "action_name": {
                    $sort_sql = "ga.`action_name` $sort->direction";
                    break;
                }
            case "description": {
                    $sort_sql = "ga.`action_description` $sort->direction";
                    break;
                }
            case "authorized_by": {
                    $sort_sql = "`authorized_by` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "ga.`action_id` $sort->direction";
                    break;
                }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT ga.* "
            . ", gar.`position_id` "
            . ", gar.`authorizing_admin_id` "
            . ", COALESCE(gar.`authorized_date`, '') AS `authorized_date` "
            . ", adm.`admin_name` AS `authorized_by` "
            . "FROM `gemstone_action` ga "
            . "LEFT JOIN `gemstone_access_rule` gar ON gar.`action_id` = ga.`action_id` AND gar.`position_id` = ? "
            . "LEFT JOIN `gemstone_admin` adm ON adm.`admin_id` = gar.`authorizing_admin_id` "
            . "WHERE 1 $where_sql "
            . "ORDER BY $sort_sql "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql, [$position_id]);
        if ($query) {
            $response->success = TRUE;
            if ($query->num_rows() > 0) {
                $response->found = TRUE;
                $response->data = $query->result();
                $response->message = "Action access fetched.";
            } else {
                $response->message = "Action access not found.";
            }
        } else {
            $response->message = "Something went wrong, contact system administrator. Error Code: E001.";
        }
        return $response;
    }
    public function get_list_all(): array
    {
        $sql = "SELECT ga.* "
            . "FROM `gemstone_action` ga "
            . "WHERE 1 "
            . "ORDER BY ga.`action_code` ASC";
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
        $sql = "SELECT ga.* "
            . "FROM `gemstone_action` ga "
            . "WHERE ga.`action_id` = ? ";
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
            $response->message = "Something went wrong, contact system administrator. Error Code: E002.";
        }
        return $response;
    }

    public function add(object $input): GSResponse
    {
        $response = new GSResponse();
        $sql = "INSERT INTO `gemstone_action` "
            . "(`action_code` "
            . ", `action_name` "
            . ", `action_description`) "
            . "VALUES(?, ?, ?);";
        $query = $this->db->query($sql, [
            $input->action_code,
            $input->action_name,
            $input->action_description
        ]);
        if ($query) {
            $response->id = $this->db->insert_id();
            $response->success = true;
            $response->message = "New action added.";
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    public function update(object $input): GSResponse
    {
        $response = new GSResponse();
        $input_check = $this->check_input_exists($input, array("action_code", "action_name", "action_description", "action_id"));
        if ($input_check->valid === true) {
            $sql = "UPDATE `gemstone_action` "
                . "SET `action_code` = ? "
                . ", `action_name` = ? "
                . ", `action_description` = ? "
                . "WHERE `action_id` = ?;";
            $query = $this->db->query($sql, [
                $input->action_code,
                $input->action_name,
                $input->action_description,
                $input->action_id
            ]);
            if ($query) {
                $response->success = true;
                $response->id = $input->action_id;
                $response->message = "Action data updated.";
            } else {
                $response->message = "Query failed.";
            }
        }
        return $response;
    }
}
