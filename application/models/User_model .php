<?php
class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function validate($username, $password) {
        $hashed_password = md5($password);
        $this->db->where('username', $username);
        $this->db->where('password', $hashed_password);
        $query = $this->db->get('users');

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }
}
