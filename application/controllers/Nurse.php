<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Nurse extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    function index() {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'dashboard';
        $data['page_title'] = get_phrase('tableau_de_board');
        $this->load->view('backend/index', $data);
    }
     
    function mesure_crud($task = "", $param2 = "", $param3 = ""){
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_mesure';
            $page_data['page_title'] = get_phrase('mesure');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_mesure';
           $page_data['param2'] = $param2;
           $page_data['param3'] = $param3;
           $page_data['page_title'] = get_phrase('mesure');
           $this->load->view('backend/index', $page_data);
        }
    }

    function orientation_crud($task = "", $param2 = "", $param3 = ""){
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_orientation';
            $page_data['page_title'] = get_phrase('orientation');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_orientation';
           $page_data['param2'] = $param2;
           $page_data['param3'] = $param3;
           $page_data['page_title'] = get_phrase('orientation');
           $this->load->view('backend/index', $page_data);
        }
    }

    function orientation($task = "", $orientation_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_orientation_info();
            $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() . 'nurse/orientation');
        }

        if ($task == "update") {
            $this->crud_model->update_orientation_info($bed_id);
            $this->session->set_flashdata('message', get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() . 'nurse/orientation');
        }

        if ($task == "delete") {
            $this->crud_model->delete_orientation_info($orientation_id);
            redirect(base_url() . 'nurse/orientation');
        }

        $data['orientation_info'] = $this->crud_model->select_orientation_info();
        $data['page_name'] = 'manage_orientation';
        $data['page_title'] = get_phrase('orientation');
        $this->load->view('backend/index', $data);
    }

    function mesure($task = "", $mesure_id = "" , $menu_check = '', $patient_id = '') {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_mesure_info();
            $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() . 'nurse/mesure');
        }

        if ($task == "update") {
            $this->crud_model->update_mesure_info($mesure_id);
            $this->session->set_flashdata('message', get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            if ($menu_check == 'from_mesure')
            redirect(base_url() . 'nurse/mesure');
        }

        if ($task == "delete") {
            $this->crud_model->delete_mesure_info($mesure_id);
            redirect(base_url() . 'nurse/mesure');
        }

        $data['mesure_info'] = $this->crud_model->select_mesure_info();
        $data['menu_check'] = 'from_mesure';
        $data['page_name'] = 'manage_mesure';
        $data['page_title'] = get_phrase('soins');
        $this->load->view('backend/index', $data);
    }

    function bed($task = "", $bed_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_bed_info();
            $this->session->set_flashdata('message', get_phrase('bed_info_saved_successfuly'));
            redirect(base_url() . 'nurse/bed');
        }

        if ($task == "update") {
            $this->crud_model->update_bed_info($bed_id);
            $this->session->set_flashdata('message', get_phrase('bed_info_updated_successfuly'));
            redirect(base_url() . 'nurse/bed');
        }

        if ($task == "delete") {
            $this->crud_model->delete_bed_info($bed_id);
            redirect(base_url() . 'nurse/bed');
        }

        $data['bed_info'] = $this->crud_model->select_bed_info();
        $data['page_name'] = 'manage_bed';
        $data['page_title'] = get_phrase('lit');
        $this->load->view('backend/index', $data);
    }

    function bed_allotment($task = "", $bed_allotment_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_bed_allotment_info();
            $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succésf'));
            redirect(base_url() . 'nurse/bed_allotment');
        }

        if ($task == "update") {
            $this->crud_model->update_bed_allotment_info($bed_allotment_id);
            $this->session->set_flashdata('message', get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() . 'nurse/bed_allotment');
        }

        if ($task == "delete") {
            $this->crud_model->delete_bed_allotment_info($bed_allotment_id);
            redirect(base_url() . 'nurse/bed_allotment');
        }

        $data['bed_allotment_info'] = $this->crud_model->select_bed_allotment_info();
        $data['page_name'] = 'manage_bed_allotment';
        $data['page_title'] = get_phrase('allocation_de_lit');
        $this->load->view('backend/index', $data);
    }

    function blood_bank($task = "", $blood_group_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "update") {
            $this->crud_model->update_blood_bank_info($blood_group_id);
            $this->session->set_flashdata('message', get_phrase('blood_bank_info_updated_successfuly'));
            redirect(base_url() . 'nurse/blood_bank');
        }

        $data['blood_bank_info'] = $this->crud_model->select_blood_bank_info();
        $data['page_name'] = 'manage_blood_bank';
        $data['page_title'] = get_phrase('banque_du_sang');
        $this->load->view('backend/index', $data);
    }

    function blood_donor($task = "", $blood_donor_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $blood_donor = $this->db->get_where('blood_donor', array('email' => $email))->row()->name;
            if ($blood_donor == null) {
                $this->crud_model->save_blood_donor_info();
                $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succésf'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'nurse/blood_donor');
        }

        if ($task == "update") {
            $this->crud_model->update_blood_donor_info($blood_donor_id);
            $this->session->set_flashdata('message', get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() . 'nurse/blood_donor');
        }

        if ($task == "delete") {
            $this->crud_model->delete_blood_donor_info($blood_donor_id);
            redirect(base_url() . 'nurse/blood_donor');
        }

        $data['blood_donor_info'] = $this->crud_model->select_blood_donor_info();
        $data['page_name'] = 'manage_blood_donor';
        $data['page_title'] = get_phrase('don_du_sang');
        $this->load->view('backend/index', $data);
    }

    function report($task = "", $report_id = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_report_info();
            $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succésf'));
            redirect(base_url() . 'nurse/report');
        }

        if ($task == "update") {
            $this->crud_model->update_report_info($report_id);
            $this->session->set_flashdata('message', get_phrase('informations_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() . 'nurse/report');
        }

        if ($task == "delete") {
            $this->crud_model->delete_report_info($report_id);
            redirect(base_url() . 'nurse/report');
        }

        $data['page_name'] = 'manage_report';
        $data['page_title'] = get_phrase('rapport');
        $this->load->view('backend/index', $data);
    }

    function profile($task = "") {
        if ($this->session->userdata('nurse_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $nurse_id = $this->session->userdata('login_user_id');
        if ($task == "update") {
            $this->crud_model->update_nurse_info($nurse_id);
            $this->session->set_flashdata('message', get_phrase('informations_de_profil_a_été_mis_à_jours_avec_succés'));
            redirect(base_url() . 'nurse/profile');
        }

        if ($task == "change_password") {
            $password = $this->db->get_where('nurse', array('nurse_id' => $nurse_id))->row()->password;
            $old_password = sha1($this->input->post('old_password'));
            $new_password = $this->input->post('new_password');
            $confirm_new_password = $this->input->post('confirm_new_password');

            if ($password == $old_password && $new_password == $confirm_new_password) {
                $data['password'] = sha1($new_password);

                $this->db->where('nurse_id', $nurse_id);
                $this->db->update('nurse', $data);

                $this->session->set_flashdata('message', get_phrase('informations_de_mot_passe_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() . 'nurse/profile');
            } else {
                $this->session->set_flashdata('message', get_phrase('le_mot_de_passe_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() . 'nurse/profile');
            }
        }

        $data['page_name'] = 'edit_profile';
        $data['page_title'] = get_phrase('profil');
        $this->load->view('backend/index', $data);
    }

}
