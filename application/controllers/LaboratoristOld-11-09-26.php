<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Laboratorist extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->output->set_header('Last-Modified: ' . strtotime("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    function index() {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'dashboard';
        $data['page_title'] = get_phrase('laboratorist_dashboard');
        $this->load->view('backend/index', $data);
    }

    function dashboard() {
        if ($this->session->userdata('laboratorist_login') != 1) {
           // $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        $page_data['page_name'] = 'dashboard';
        $page_data['page_title'] = get_phrase('laboratorist_dashboard');
        $this->load->view('backend/index', $page_data);
    }

    function patient_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_patient';
            $page_data['page_title'] = get_phrase('patient');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_patient';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('patient');
            $this->load->view('backend/index', $page_data);
        }
    }
    function patient($task = "", $patient_id = "") {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            /*$name = $_POST['name'];
            $patient = $this->db->get_where('patient', array('name' => $name))->row()->name;*/
            if ($patient == null) {
                $this->crud_model->save_patient_info();
                $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            } else {
                $this->session->set_flashdata('message', get_phrase('nom en doublant'));
            }
            redirect(base_url() . 'laboratorist/patient');
        }

        if ($task == "update") {
                $this->crud_model->update_patient_info($patient_id);
                $this->session->set_flashdata('message', get_phrase("l'informations a été mises à jour avec succès"));
                redirect(base_url() . 'laboratorist/patient');
        }

        if ($task == "delete") {
            $this->crud_model->delete_patient_info($patient_id);
            redirect(base_url() . 'laboratorist/patient');
        }

        $data['patient_info'] = $this->crud_model->select_patient_info();
        $data['page_name'] = 'manage_patient';
        $data['page_title'] = get_phrase('patient');
        $this->load->view('backend/index', $data);
    }
    
    function blood_donor_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_blood_donor';
            $page_data['page_title'] = get_phrase('blood_donor');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_blood_donor';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('blood_donor');
            $this->load->view('backend/index', $page_data);
        }
    }
    function blood_bank($task = "", $blood_group_id = "") {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "update") {
            $this->crud_model->update_blood_bank_info($blood_group_id);
            $this->session->set_flashdata('message', get_phrase('blood_bank_info_updated_successfuly'));
            redirect(base_url() . 'laboratorist/blood_bank');
        }

        $data['blood_bank_info'] = $this->crud_model->select_blood_bank_info();
        $data['page_name'] = 'manage_blood_bank';
        $data['page_title'] = get_phrase('blood_bank');
        $this->load->view('backend/index', $data);
    }

    function blood_donor($task = "", $blood_donor_id = "") {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $blood_donor = $this->db->get_where('blood_donor', array('email' => $email))->row()->name;
            if ($blood_donor == null) {
                $this->crud_model->save_blood_donor_info();
                $this->session->set_flashdata('message', get_phrase('blood_donor_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'laboratorist/blood_donor');
        }

        if ($task == "update") {
                $this->crud_model->update_blood_donor_info($blood_donor_id);
                $this->session->set_flashdata('message', get_phrase('blood_donor_info_updated_successfuly'));
                redirect(base_url() . 'laboratorist/blood_donor');
        }

        if ($task == "delete") {
            $this->crud_model->delete_blood_donor_info($blood_donor_id);
            redirect(base_url() . 'laboratorist/blood_donor');
        }

        $data['blood_donor_info'] = $this->crud_model->select_blood_donor_info();
        $data['page_name'] = 'manage_blood_donor';
        $data['page_title'] = get_phrase('blood_donor');
        $this->load->view('backend/index', $data);
    }

    function examen_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_examen';
            $page_data['page_title'] = get_phrase('examen');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_examen';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('examen');
            $this->load->view('backend/index', $page_data);
        }
    }

    function examen($task = "", $id_examen = "") {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            /*$email = $_POST['email'];
            $examen = $this->db->get_where('examen', array('email' => $email))->row()->name;*/
            if ($examen == null) {
                $this->crud_model->save_examen_info();
                $this->session->set_flashdata('message', get_phrase('examen_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'laboratorist/examen');
        }

        if ($task == "update") {
                $this->crud_model->update_examen_info($id_examen);
                $this->session->set_flashdata('message', get_phrase('examen_info_updated_successfuly'));
                redirect(base_url() . 'laboratorist/examen');
        }

        if ($task == "delete") {
            $this->crud_model->delete_examen_info($id_examen);
            redirect(base_url() . 'laboratorist/examen');
        }

        $data['examen_info'] = $this->crud_model->select_examen_info();
        $data['page_name'] = 'manage_examen';
        $data['page_title'] = get_phrase('examen');
        $this->load->view('backend/index', $data);
    }

    function profile($task = "") {
        if ($this->session->userdata('laboratorist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $laboratorist_id = $this->session->userdata('login_user_id');
        if ($task == "update") {
            $this->crud_model->update_laboratorist_info($laboratorist_id);
            $this->session->set_flashdata('message', get_phrase('profile_info_updated_successfuly'));
            redirect(base_url() . 'laboratorist/profile');
        }

        if ($task == "change_password") {
            $password = $this->db->get_where('laboratorist', array('laboratorist_id' => $laboratorist_id))->row()->password;
            $old_password = sha1($this->input->post('old_password'));
            $new_password = $this->input->post('new_password');
            $confirm_new_password = $this->input->post('confirm_new_password');

            if ($password == $old_password && $new_password == $confirm_new_password) {
                $data['password'] = sha1($new_password);

                $this->db->where('laboratorist_id', $laboratorist_id);
                $this->db->update('laboratorist', $data);

                $this->session->set_flashdata('message', get_phrase('password_info_updated_successfuly'));
                redirect(base_url() . 'laboratorist/profile');
            } else {
                $this->session->set_flashdata('message', get_phrase('password_update_failed'));
                redirect(base_url() . 'laboratorist/profile');
            }
        }

        $data['page_name'] = 'edit_profile';
        $data['page_title'] = get_phrase('profile');
        $this->load->view('backend/index', $data);
    }

}
