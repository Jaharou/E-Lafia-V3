<?php
/* 	
 * 	Class Accountant
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class Accountant extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    function index() {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'dashboard';
        $data['page_title'] = get_phrase('accountant_dashboard');
        $this->load->view('backend/index', $data);
    }

    function invoice_add($task = "") {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase('invoice_info_saved_successfuly'));
            redirect('accountant/invoice_add');
        }

        $data['page_name'] = 'add_invoice';
        $data['page_title'] = get_phrase('invoice');
        $this->load->view('backend/index', $data);
    }

    function invoice_manage($task = "", $invoice_id = "") {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "update") {
            $this->crud_model->update_invoice($invoice_id);
            $this->session->set_flashdata('message', get_phrase('invoice_info_updated_successfuly'));
            redirect('accountant/invoice_manage');
        }

        if ($task == "delete") {
            $this->crud_model->delete_invoice($invoice_id);
            redirect('accountant/invoice_manage');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_invoice';
        $data['page_title'] = get_phrase('invoice');
        $this->load->view('backend/index', $data);
    }

    function vente($task = "", $vente_id = "")
    {
        if ($this->session->userdata('accountant_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }        
        if ($task == "create")
        {
            $this->crud_model->save_vente_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'accountant/vente');
        }
        if ($task == "update")
        {
            $this->crud_model->update_vente_info($vente_id);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'accountant/vente');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_vente_info($vente_id);
            redirect(base_url() .'accountant/vente');
        }
        
        $data['vente_info']  = $this->crud_model->select_vente_info();
        $data['page_name']      = 'manage_vente';
        $data['page_title']     = get_phrase('vente');
        $this->load->view('backend/index', $data);
    }

    function examen($task = "", $id_examen = "")
    {
        if ($this->session->userdata('accountant_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
        if ($task == "create")
        {
            $this->crud_model->save_examen_info();
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() .'accountant/examen');
        }
        
        if ($task == "update")
        {
            $this->crud_model->update_examen_info($id_examen);
            $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() .'accountant/examen');
        }
        
        if ($task == "delete")
        {
            $this->crud_model->delete_examen_info($id_examen);
            redirect(base_url() .'accountant/examen');
        }
        
        $data['examen_info']  = $this->crud_model->select_examen_info();
        $data['page_name']      = 'manage_examen';
        $data['page_title']     = get_phrase('examen');
        $this->load->view('backend/index', $data);
    }

    function payment_history($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'show_payment_history';
        $data['page_title'] = get_phrase('historique-de-paiement');
        $this->load->view('backend/index', $data);
    }

    function profile($task = "") {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $accountant_id = $this->session->userdata('login_user_id');
        if ($task == "update") {
                $this->crud_model->update_accountant_info($accountant_id);
                $this->session->set_flashdata('message', get_phrase('profile_info_updated_successfuly'));
                redirect('accountant/profile');
        }

        if ($task == "change_password") {
            $password = $this->db->get_where('accountant', array('accountant_id' => $accountant_id))->row()->password;
            $old_password = sha1($this->input->post('old_password'));
            $new_password = $this->input->post('new_password');
            $confirm_new_password = $this->input->post('confirm_new_password');

            if ($password == $old_password && $new_password == $confirm_new_password) {
                $data['password'] = sha1($new_password);

                $this->db->where('accountant_id', $accountant_id);
                $this->db->update('accountant', $data);

                $this->session->set_flashdata('message', get_phrase('password_info_updated_successfuly'));
                redirect('accountant/profile');
            } else {
                $this->session->set_flashdata('message', get_phrase('password_update_failed'));
                redirect('accountant/profile');
            }
        }

        $data['page_name'] = 'edit_profile';
        $data['page_title'] = get_phrase('profile');
        $this->load->view('backend/index', $data);
    }

    function form($task = "") {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'form_create';
        $data['page_title'] = get_phrase('create_form');
        $this->load->view('backend/index', $data);
    }

    function get_form_element($element_type) {
        if ($this->session->userdata('accountant_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        echo $html = $this->db->get_where('form_element', array('type' => $element_type))->row()->html;
        //$this->load->view('backend/accountant/form_create_body', $html);
        //echo $element_type;
    }

}
