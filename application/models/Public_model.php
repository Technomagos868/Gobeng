<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Public_model extends GS_Model
{
    public function reset_authentication($input)
    {
        $response = new GSResponse();
        $sql = "SELECT * "
            . "FROM `gemstone_admin` "
            . "WHERE `admin_email` = ? ";
        $query = $this->db->query($sql, [$input->admin_email]);
        if ($query && $query->num_rows() > 0) {
            $result = $query->row();
            $response->success = TRUE;
            $token = base64_encode(random_bytes(16));
            $sql = "INSERT INTO `gemstone_reset_token` "
                . "(`token`, "
                . "`user_id`, "
                . "`username`, "
                . "`email`, "
                . "`is_active`, "
                . "`date_created`) "
                . "VALUES (?, ?, ?, ?, 1, NOW())";
            $query = $this->db->query($sql, [
                $token,
                $result->admin_id,
                $result->admin_name,
                $result->admin_email
            ]);
            if ($query) {
                $data["user_info"] = $result->admin_name;
                $data["link"] = base_url("password_reset?token=" . $token);
                $email = $this->load->view("email_reset_token", $data, TRUE);
                $this->send_email($result->admin_email, "Gemstone - Password Reset Token", $email);
                $response->message = "Email with reset token sent. Please check your email.";
            } else {
                $response->message = "Something went wrong. Please try again later.";
            }
        } else {
            $response->message = "This user never exist, please register!";
        }
        return $response;
    }

    public function get_password_reset_info($token)
    {
        $response = $this->prepare_response();
        $sql = "SELECT * "
            . "FROM `gemstone_reset_token` "
            . "WHERE `token` = ? AND `is_active` = 1 ";
        $query = $this->db->query($sql, [$token]);
        if ($query && $query->num_rows() > 0) {
            $response->success = TRUE;
            $response->valid = TRUE;
            $response->user_id = $query->row()->user_id;
        } else {
            $sql = "UPDATE `gemstone_reset_token` "
                . "SET `is_active` = 0 "
                . "WHERE `date_created` < NOW();";
            $this->db->query($sql);
        }
        return $response;
    }

    public function change_password_via_reset($input)
    {
        $response = $this->prepare_response();
        $token_data = $this->get_password_reset_info($input->token);
        if ($token_data->success === TRUE) {
            $user_id = $token_data->user_id;
            $hashed_pass = password_hash($input->new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE `gemstone_admin` "
                . "SET `password` = ? "
                . "WHERE `admin_id` = ?;";
            $query = $this->db->query($sql, [$hashed_pass, $user_id]);
            if ($query) {
                $sql = "UPDATE `gemstone_reset_token` "
                    . "SET `is_active` = 0 "
                    . "WHERE `token` = ?;";
                $this->db->query($sql, [$input->token]);
                $response->success = TRUE;
                $response->message = "Password successfully changed. Please login again to confirm your identity.";
            } else {
                $response->message = "Something wrong along the way. Please try again later.";
            }
        } else {
            $response->message = "Something wrong along the way. Please try again later.";
        }
        return $response;
    }
}
