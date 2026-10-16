<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trip_model extends GS_Model
{
    public function get_count(string $search = ""): int
    {
        $where_sql = $this->filter_list($search);
        $sql = "SELECT COUNT(lt.`ride_type_id`) AS `count` "
            . "FROM `list_ride_type` lt "
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
                . $this->generate_sql_like_string("division_name", $search, "lt")
                . ")";
        }
        return $where_sql;
    }

    public function get_list_paged(int $page = 1, string $sort = "id|asc", string $search = ""): array
    {
        $sort = $this->get_sort_direction($sort);
        switch ($sort->keyword) {
            case "Trip_name": {
                    $sort_sql = "lt.`Trip_name` $sort->direction";
                    break;
                }
            default: {
                    $sort_sql = "lt.`trip_id` $sort->direction";
                    break;
                }
        }
        $where_sql = $this->filter_list($search);
        $ipp = $this->get_item_per_page();
        $start = ($page - 1) * $ipp;
        $sql = "SELECT lt.* "
            . "FROM `list_trip` lt "
            . "JOIN list_ride_type lrt ON lrt.ride_type_id = lt.ride_type_id "
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
        $sql = "SELECT lt.* "
            . "FROM `list_trip` lt "
            . "WHERE 1 "
            . "ORDER BY lt.`trip_id` ASC";
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
        $sql = "SELECT lt.*, lrt.* "
            . "FROM `list_trip` lt "
            . "JOIN list_ride_type lrt ON lrt.ride_type_id = lt.ride_type_id "
            . "WHERE lt.`trip_id` = ? ";
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

    public function get_trip_count(int $division_id): int
    {
        $sql = "SELECT COUNT(gac.`ride_type_id`) AS `count` "
            . "FROM `list_trip` gac "
            . "WHERE gac.`trip_id` = ?;";
        $query = $this->db->query($sql, [$division_id]);
        if ($query) {
            return $query->row()->count;
        } else {
            return 0;
        }
    }

    public function add(object $input): GSResponse
    {
        $response = new GSResponse();
        $sql = "INSERT INTO `list_trip` "
            . "(`host_name`, `destination`, `departure_time`, `meetpoint`, `ride_type_id`, `capacity`) "
            . "VALUES(?, ?, ?, ?, ?, ?);";
        $query = $this->db->query($sql, [
            $input->host_name,
            $input->destination,
            $input->departure_time,
            $input->meetpoint,
            $input->ride_type_id,
            $input->capacity,
        ]);
        if ($query) {
            $response->id = $this->db->insert_id();
            $response->success = true;
            $response->message = "New Trip added.";
            $this->update_activity($input->log_id, "$input->host_name", "Trip/view_detail?id=$response->id", json_encode($input));
        } else {
            $response->message = "Add Trip failed.";
        }
        return $response;
    }

    public function update(object $input): GSResponse
    {
        $response = new GSResponse();
        $input_check = $this->check_input_exists($input, ["host_name", "trip_id"]);
        if ($input_check->valid === true) {
            $sql = "UPDATE `list_trip` "
                . "SET `host_name` = ? "
                . ",`destination` = ? "
                . ",`meetpoint` = ? "
                . ",`ride_type_id` = ? "
                . ",`departure_time` = ? "
                . ",`capacity` = ? "
                . "WHERE `trip_id` = ?;";
            $query = $this->db->query($sql, [
                $input->host_name,
                $input->destination,
                $input->meetpoint,
                $input->ride_type_id,
                $input->departure_time,
                $input->capacity,
                $input->trip_id

            ]);
            if ($query) {
                $response->success = true;
                $response->id = $input->trip_id;
                $response->message = "Trip data updated.";
                $this->update_activity($input->log_id, "$input->host_name", "$input->meetpoint", "$input->ride_type_id", "$input->departure_time", "$input->capacity", "$input->trip_id", "Trip/view_detail?id=$response->id", json_encode($input));
            } else {
                $response->message = "Query failed.";
            }
        } else {
            $response->message = "Incomplete input.";
        }
        return $response;
    }


    public function get_passenger_list_by_trip_id(int $trip_id): Array{
    $passenger_list = [];
    $sql = "SELECT * FROM `list_passenger` WHERE `trip_id` = ?;";
    $query = $this->db->query($sql, [$trip_id]);
    if($query){
        $passenger_list = $query->result();
    }
    return $passenger_list;
    }


    

    
}
