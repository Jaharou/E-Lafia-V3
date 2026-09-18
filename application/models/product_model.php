<?php
class Product_model extends CI_Model {
    public function get_stock_quantity($medicine_id) {
        $this->db->select('qte_prod');
        $this->db->from('medicine');
        $this->db->where('medicine_id', $medicine_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->qte_prod;
        }
        return 0;
    }
}
?>