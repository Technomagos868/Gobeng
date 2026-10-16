<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Division_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(gd.`division_id`) AS `count` "
            . "FROM `gemstone_division` gd "
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
                . $this->generate_sql_like_string("division_name", $search, "gd")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "id|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "division_name": {
                    $sort_sql = "gd.`division_name` $sort->direction";
                    break;
                }
            case "position_count": {
                    $sort_sql = "`position_count` $sort->direction";
                    break;
                }
            case "admin_count": {
                    $sort_sql = "`admin_count` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "gd.`division_id` $sort->direction";
                    break;
                }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT gd.* "
            . ", (SELECT COUNT(gac.`admin_tier_id`) FROM `gemstone_access_control` gac WHERE gac.`division_id` = gd.`division_id`) AS `position_count` "
            . ", (SELECT COUNT(ga.`admin_id`) FROM `gemstone_admin` ga JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` WHERE gac.`division_id` = gd.`division_id`) AS `admin_count` "
            . "FROM `gemstone_division` gd "
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
        $sql = "SELECT gd.* "
            . "FROM `gemstone_division` gd "
            . "WHERE 1 "
            . "ORDER BY gd.`division_name` ASC";
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
        $sql = "SELECT gd.* "
            . "FROM `gemstone_division` gd "
            . "WHERE gd.`division_id` = ? ";
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
        $sql = "INSERT INTO `gemstone_division` "
            . "(`division_name`) "
            . "VALUES(?);";
        $query = $this->db->query($sql, [
            $input->division_name
        ]);
        if ($query) {
            $response->id = $this->db->insert_id();
            $response->success = true;
            $response->message = "New division added.";
            $this->update_activity($input->log_id, "$input->division_name", "Division/view_detail?id=$response->id", json_encode($input));
        } else {
            $response->message = "Query failed.";
        }
        return $response;
    }

    public function update(object $input): GSResponse
    {
        $response = new GSResponse();
        $input_check = $this->check_input_exists($input, ["division_name", "division_id"]);
        if ($input_check->valid === true) {
            $sql = "UPDATE `gemstone_division` "
                . "SET `division_name` = ? "
                . "WHERE `division_id` = ?;";
            $query = $this->db->query($sql, [
                $input->division_name,
                $input->division_id
            ]);
            if ($query) {
                $response->success = true;
                $response->id = $input->division_id;
                $response->message = "Division data updated.";
                $this->update_activity($input->log_id, "$input->division_name", "Division/view_detail?id=$response->id", json_encode($input));
            } else {
                $response->message = "Query failed.";
            }
        } else {
            $response->message = "Incomplete input.";
        }
        return $response;
    }

    public function get_position_count(int $division_id): int
    {
        $sql = "SELECT COUNT(gac.`admin_tier_id`) AS `count` "
            . "FROM `gemstone_access_control` gac "
            . "WHERE gac.`division_id` = ?;";
        $query = $this->db->query($sql, [$division_id]);
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    public function get_position_list_paged(int $division_id, int $page = 1, string $sort = "id|asc"): GSResponse
    {
        $response = new GSResponse();
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
            case "admin_count": {
                    $sort_sql = "`admin_count` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "gac.`admin_tier_id` $sort->direction";
                    break;
                }
        }
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT gac.*, gac.`admin_tier_id` AS `position_id` "
            . ", (SELECT COUNT(`admin_id`) FROM `gemstone_admin` ga WHERE ga.`admin_tier_id` = gac.`admin_tier_id`) AS `admin_count` "
            . "FROM `gemstone_access_control` gac "
            . "WHERE gac.`division_id` = ? "
            . "ORDER BY $sort_sql "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql, [$division_id]);
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

    public function get_admin_count(int $position_id): int
    {
        $sql = "SELECT COUNT(ga.`admin_id`) AS `count` "
            . "FROM `gemstone_admin` ga "
            . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
            . "WHERE gac.`division_id` = ?;";
        $query = $this->db->query($sql, [$position_id]);
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    public function get_admin_list_paged(int $division_id, int $page = 1, string $sort = "id|asc"): GSResponse
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
            . "JOIN `gemstone_access_control` gac ON gac.`admin_tier_id` = ga.`admin_tier_id` "
            . "WHERE gac.`division_id` = ? "
            . "ORDER BY $sort_sql "
            . "LIMIT $start, $ipp";
        $query = $this->db->query($sql, [$division_id]);
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
}
