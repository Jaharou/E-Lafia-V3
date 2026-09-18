<?php
class Invoice_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insert_invoice_entry($data) {
        $this->db->insert('invoice_entries', $data);
    }

    public function get_all_invoice() {
        $query = $this->db->get('invoice');
        return $query->result_array();
    }
}
?>

