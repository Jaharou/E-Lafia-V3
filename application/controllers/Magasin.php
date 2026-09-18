<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Magasin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }
    
    function index() 
    {
        if ($this->session->userdata('magasin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
        
        $data['page_name']      = 'dashboard';
        $data['page_title']     = get_phrase('tableau_de_board');
        $this->load->view('backend/index', $data);
    }
    
    function dashboard() {
        if ($this->session->userdata('magasin_login') != 1) {
           // $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        $page_data['page_name'] = 'dashboard';
        $page_data['page_title'] = get_phrase('magasin_dashboard');
        $this->load->view('backend/index', $page_data);
    }

    function fournisseur_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('magasin_login') != 1) {
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
        if ($this->session->userdata('magasin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_fournisseur_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'magasin/fournisseur');
        }

        if ($task == "update") {
                $this->crud_model->update_fournisseur_info($fournisseur_id);
                $this->session->set_flashdata('message', get_phrase("l'informations a été mises à jour avec succès"));
                redirect(base_url() . 'magasin/fournisseur');
        }

        if ($task == "delete") {
            $this->crud_model->delete_fournisseur_info($fournisseur_id);
            redirect(base_url() . 'magasin/fournisseur');
        }

        $data['fournisseur_info'] = $this->crud_model->select_fournisseur_info();
        $data['page_name'] = 'manage_fournisseur';
        $data['page_title'] = get_phrase('fournisseur');
        $this->load->view('backend/index', $data);
    }
    
    function medicine($task = "", $medicine_id = "")
    {
        if ($this->session->userdata('magasin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_medicine_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'magasin/medicine');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_medicine_info($medicine_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'magasin/medicine');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_medicine_info($medicine_id);
            redirect(base_url() .'magasin/medicine');
        }
        
        $data['medicine_info']  = $this->crud_model->select_medicine_info();
        $data['page_name']      = 'manage_medicine';
        $data['page_title']     = get_phrase('medicine');
        $this->load->view('backend/index', $data);
    }

    function stock_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('magasin_login') != 1) {
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
        if ($this->session->userdata('magasin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_stock_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'magasin/stock');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_stock_info($stock_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'magasin/stock');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_stock_info($stock_id);
            redirect(base_url() .'magasin/stock');
        }
        
        $data['stock_info']  = $this->crud_model->select_stock_info();
        $data['page_name']      = 'manage_stock';
        $data['page_title']     = get_phrase('stock');
        $this->load->view('backend/index', $data);
    }
    
    function profile($task = "")
    {
        if ($this->session->userdata('magasin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
        
        $magasin_id      = $this->session->userdata('login_user_id');
        if ($task == "update")
        {
            $this->crud_model->update_magasin_info($magasin_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'magasin/profile');
        }
        
        if ($task == "change_password")
        {
            $password               = $this->db->get_where('magasin', array('magasin_id' => $magasin_id))->row()->password;
            $old_password           = sha1($this->input->post('old_password'));
            $new_password           = $this->input->post('new_password');
            $confirm_new_password   = $this->input->post('confirm_new_password');
            
            if($password==$old_password && $new_password==$confirm_new_password)
            {
                $data['password']   = sha1($new_password);
                
                $this->db->where('magasin_id',$magasin_id);
                $this->db->update('magasin',$data);
                
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'magasin/profile');
            }
            else
            {
                $this->session->set_flashdata('message' , get_phrase('mot_de_passe_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'magasin/profile');
            }
        }
        
        $data['page_name']          = 'edit_profile';
        $data['page_title']         = get_phrase('profil');
        $this->load->view('backend/index', $data);
    }
}