<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(lb.`branch_id`) AS `count` "
            . "FROM `list_branch` lb "
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
                . $this->generate_sql_like_string("branch_code", $search, "lb")
                . " OR " . $this->generate_sql_like_string("branch_name", $search, "lb")
                . " OR " . $this->generate_sql_like_string("branch_address", $search, "lb")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "id|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "branch_code": {
                $sort_sql = "lb.`branch_code` $sort->direction";
                break;
            }
            case "branch_name": {
                $sort_sql = "lb.`branch_name` $sort->direction";
                break;
            }
            case "seat_capacity": {
                $sort_sql = "lb.`seat_capacity` $sort->direction";
                break;
            }
            default: {
                $sort_sql = "lb.`branch_id` $sort->direction";
                break;
            }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT lb.* "
            . "FROM `list_branch` lb "
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

    public function get_detail(int $id): GSResponse
    {
        $response = new GSResponse();
        $sql = "SELECT lb.* "
            . "FROM `list_branch` lb "
            . "WHERE lb.`branch_id` = ? ";
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
        if (!isset($input->is_franchise)) {
            $input->is_franchise = 0;
        }
        $input->credit_limit = str_replace(",", "", $input->credit_limit);
        $input->consign_limit = str_replace(",", "", $input->consign_limit);
        $sql = "INSERT INTO `list_branch` "
            . "(`branch_code` "
            . ", `branch_name` "
            . ", `branch_address` "
            . ", `is_franchise` "
            . ", `credit_term` "
            . ", `credit_limit` "
            . ", `consign_limit` "
            . ", `seat_capacity`) "
            . "VALUES(?, ?, ?, ?, ?, ?, ?, ?);";
        $query = $this->db->query($sql, [
            $input->branch_code,
            $input->branch_name,
            $input->branch_address,
            $input->is_franchise,
            $input->credit_term,
            $input->credit_limit,
            $input->consign_limit,
            $input->seat_capacity
        ]);
        if ($query) {
            $response->id = $this->db->insert_id();
            $this->update_activity($input->log_id, $input->branch_name, "Branch/view_detail?id={$response->id}", json_encode($input));
            $response->success = true;
            $response->message = "New branch added.";
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    public function update($input): GSResponse
    {
        $response = new GSResponse();
        if (!isset($input->is_franchise)) {
            $input->is_franchise = 0;
        }
        $input->credit_limit = str_replace(",", "", $input->credit_limit);
        $input->consign_limit = str_replace(",", "", $input->consign_limit);
        $input_check = $this->check_input_exists($input, array("branch_name", "branch_code", "branch_id"));
        if ($input_check->valid === true) {
            $sql = "UPDATE `list_branch` "
                . "SET `branch_code` = ? "
                . ", `branch_name` = ? "
                . ", `branch_address` = ? "
                . ", `is_franchise` = ? "
                . ", `credit_term` = ? "
                . ", `credit_limit` = ? "
                . ", `consign_limit` = ? "
                . ", `seat_capacity` = ? "
                . "WHERE `branch_id` = ?;";
            $query = $this->db->query($sql, [
                $input->branch_code,
                $input->branch_name,
                $input->branch_address,
                $input->is_franchise,
                $input->credit_term,
                $input->credit_limit,
                $input->consign_limit,
                $input->seat_capacity,
                $input->branch_id
            ]);
            if ($query) {
                $response->id = $input->branch_id;
                $this->update_activity($input->log_id, $input->branch_name, "Branch/view_detail?id={$response->id}", json_encode($input));
                $response->success = true;
                $response->message = "Branch data updated.";
            } else {
                $response->message = "Query failed.";
            }
        }
        return $response;
    }
}
