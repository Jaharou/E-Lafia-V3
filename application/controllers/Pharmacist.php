<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pharmacist extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }
    
    function index() 
    {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
        
        $data['page_name']      = 'dashboard';
        $data['page_title']     = get_phrase('tableau_de_board');
        $this->load->view('backend/index', $data);
    }
    
    function medicine_category($task = "", $medicine_category_id = "")
    {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_medicine_category_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .  'pharmacist/medicine_category');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_medicine_category_info($medicine_category_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .  'pharmacist/medicine_category');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_medicine_category_info($medicine_category_id);
            redirect(base_url() .  'pharmacist/medicine_category');
        }
        
        $data['medicine_category_info'] = $this->crud_model->select_medicine_category_info();
        $data['page_name']              = 'manage_medicine_category';
        $data['page_title']             = get_phrase('medicine_category');
        $this->load->view('backend/index', $data);
    }


function invoice_add($task = "") {
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'pharmacist/invoice_manage');
        }

        $data['page_name'] = 'add_invoice';
        $data['page_title'] = get_phrase('reçu');
        $this->load->view('backend/index', $data);
    }

    function invoice_manage($task = "", $invoice_id = "") {
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'pharmacist/invoice_manage');
        }

        if ($task == "update") {
            $this->crud_model->update_invoice($invoice_id);
            $this->session->set_flashdata('message', get_phrase("mises à jour des informations a été éffectué avec succès"));
            redirect(base_url() . 'pharmacist/invoice_manage');
        }

        if ($task == "delete") {
            $this->crud_model->delete_invoice($invoice_id);
            redirect(base_url() . 'pharmacist/invoice_manage');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_invoice';
        $data['page_title'] = get_phrase('reçu');
        $this->load->view('backend/index', $data);
    }


    function stock_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_stock';
            $page_data['page_title'] = get_phrase('stock');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_stock';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('stock');
            $this->load->view('backend/index', $page_data);
        }
    }

    function stock($task = "", $stock_id = "")
    {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_stock_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'pharmacist/stock');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_stock_info($stock_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'pharmacist/stock');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_stock_info($stock_id);
            redirect(base_url() .'pharmacist/stock');
        }
        
        $data['stock_info']  = $this->crud_model->select_stock_info();
        $data['page_name']      = 'manage_stock';
        $data['page_title']     = get_phrase('stock');
        $this->load->view('backend/index', $data);
    }

    //.........................Debut partie vente........................
        function vente_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('pharmacist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }
            if ($task == 'add') {
                $page_data['page_name'] = 'add_vente';
                $page_data['page_title'] = get_phrase('vente');
                $this->load->view('backend/index', $page_data);
            }elseif ($task == 'edit') {
               $page_data['page_name'] = 'edit_vente';
               $page_data['param2'] = $param2;
                $page_data['page_title'] = get_phrase('vente');
                $this->load->view('backend/index', $page_data);
            }
        }

        function vente($task = "", $vente_id = "")
            {
            if ($this->session->userdata('pharmacist_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_vente_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'pharmacist/vente');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_vente_info($vente_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'pharmacist/vente');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_vente_info($vente_id);
                redirect(base_url() .'pharmacist/vente');
            }
            
            $data['vente_info']     = $this->crud_model->select_vente_info();
            $data['page_name']      = 'manage_vente';
            $data['page_title']     = get_phrase('vente');
            $this->load->view('backend/index', $data);
        }

    function manage_journal_vente($task = "") {
            if ($this->session->userdata('pharmacist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }

            $data['vente_info'] = $this->crud_model->select_vente_info();
            $data['page_name'] = 'manage_journal_vente';
            $data['page_title'] = get_phrase('journal');
            $this->load->view('backend/index', $data);
            }
    //.........................Fin partie vente....................

    function fournisseur_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_fournisseur';
            $page_data['page_title'] = get_phrase('fournisseur');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_fournisseur';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('fournisseur');
            $this->load->view('backend/index', $page_data);
        }
    }
    
    function fournisseur($task = "", $fournisseur_id = "") {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_fournisseur_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'pharmacist/fournisseur');
        }

        if ($task == "update") {
                $this->crud_model->update_fournisseur_info($fournisseur_id);
                $this->session->set_flashdata('message', get_phrase("l'informations a été mises à jour avec succès"));
                redirect(base_url() . 'pharmacist/fournisseur');
        }

        if ($task == "delete") {
            $this->crud_model->delete_fournisseur_info($fournisseur_id);
            redirect(base_url() . 'pharmacist/fournisseur');
        }

        $data['fournisseur_info'] = $this->crud_model->select_fournisseur_info();
        $data['page_name'] = 'manage_fournisseur';
        $data['page_title'] = get_phrase('fournisseur');
        $this->load->view('backend/index', $data);
    }
    
    function medicine($task = "", $medicine_id = "")
    {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_medicine_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'pharmacist/medicine');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_medicine_info($medicine_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'pharmacist/medicine');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_medicine_info($medicine_id);
            redirect(base_url() .'pharmacist/medicine');
        }
        
        $data['medicine_info']  = $this->crud_model->select_medicine_info();
        $data['page_name']      = 'manage_medicine';
        $data['page_title']     = get_phrase('medicine');
        $this->load->view('backend/index', $data);
    }
    
    function profile($task = "")
    {
        if ($this->session->userdata('pharmacist_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
        
        $pharmacist_id      = $this->session->userdata('login_user_id');
        if ($task == "update")
        {
            $this->crud_model->update_pharmacist_info($pharmacist_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'pharmacist/profile');
        }
        
        if ($task == "change_password")
        {
            $password               = $this->db->get_where('pharmacist', array('pharmacist_id' => $pharmacist_id))->row()->password;
            $old_password           = sha1($this->input->post('old_password'));
            $new_password           = $this->input->post('new_password');
            $confirm_new_password   = $this->input->post('confirm_new_password');
            
            if($password==$old_password && $new_password==$confirm_new_password)
            {
                $data['password']   = sha1($new_password);
                
                $this->db->where('pharmacist_id',$pharmacist_id);
                $this->db->update('pharmacist',$data);
                
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'pharmacist/profile');
            }
            else
            {
                $this->session->set_flashdata('message' , get_phrase('mot_de_passe_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'pharmacist/profile');
            }
        }
        
        $data['page_name']          = 'edit_profile';
        $data['page_title']         = get_phrase('profile');
        $this->load->view('backend/index', $data);
    }

    function form($task = "") {
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'form_create';
        $data['page_title'] = get_phrase('create_form');
        $this->load->view('backend/index', $data);
    }

    function get_form_element($element_type) {
        if ($this->session->userdata('pharmacist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        echo $html = $this->db->get_where('form_element', array('type' => $element_type))->row()->html;
        //$this->load->view('backend/accountant/form_create_body', $html);
        //echo $element_type;
    }
}