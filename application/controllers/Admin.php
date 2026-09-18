<?php
/* 	
 * 	Class Admin
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');

        /* cache control */
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    /*     * *default function, redirects to login page if no admin logged in yet** */

    public function index() {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($this->session->userdata('admin_login') == 1)
            redirect(base_url() . 'admin/dashboard', 'refresh');
    }
    function department_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_department';
            $page_data['page_title'] = get_phrase('department');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_department';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('department');
            $this->load->view('backend/index', $page_data);
        }
    }
    function doctor_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_doctor';
            $page_data['page_title'] = get_phrase('doctor');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_doctor';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('doctor');
            $this->load->view('backend/index', $page_data);
        }
    }
      function patient_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
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
    
    function openig_hours_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_openig_hours';
            $page_data['page_title'] = get_phrase('openig_hours');
            $this->load->view('backend/index', $page_data);
        }
    }
    function slider_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_slider';
            $page_data['page_title'] = get_phrase('slider');
            $this->load->view('backend/index', $page_data);
        }
    }
    function services_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_services';
            $page_data['page_title'] = get_phrase('services');
            $this->load->view('backend/index', $page_data);
        }
    }
    function designation_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_designation';
            $page_data['page_title'] = get_phrase('nouvelle-désignation');
            $this->load->view('backend/index', $page_data);
        }
        elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_designation';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('designation');
            $this->load->view('backend/index', $page_data);
        }   
    }

    function designation($task = "", $entry_description_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            
            if ($test == null) {
                $this->crud_model->save_designation_info();
                $this->session->set_flashdata('message', get_phrase('test_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/designation');
        }
        if ($task == "update") {
                $this->crud_model->update_designation_info($entry_description_id);
                $this->session->set_flashdata('message', get_phrase('designation_info_updated_successfuly'));
                redirect(base_url() . 'admin/designation');
        }

        if ($task == "delete") {
            $this->crud_model->delete_designation_info($designation);
            redirect(base_url() . 'admin/designation');
        }

        $data['designation_info'] = $this->crud_model->select_designation_info();
        $data['page_name'] = 'manage_designation';
        $data['page_title'] = get_phrase('admin');
        $this->load->view('backend/index', $data);
    }

    function test_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_test';
            $page_data['page_title'] = get_phrase('nouveau-test');
            $this->load->view('backend/index', $page_data);
        }
        elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_test';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('test');
            $this->load->view('backend/index', $page_data);
        }   
    }

    function test($task = "", $id_test = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            
            if ($test == null) {
                $this->crud_model->save_test_info();
                $this->session->set_flashdata('message', get_phrase('test_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/test');
        }
        if ($task == "update") {
                $this->crud_model->update_test_info($id_test);
                $this->session->set_flashdata('message', get_phrase('test_info_updated_successfuly'));
                redirect(base_url() . 'admin/test');
        }

        if ($task == "delete") {
            $this->crud_model->delete_test_info($id_test);
            redirect(base_url() . 'admin/test');
        }

        $data['test_info'] = $this->crud_model->select_test_info();
        $data['page_name'] = 'manage_test';
        $data['page_title'] = get_phrase('admin');
        $this->load->view('backend/index', $data);
    }

    /*function examen_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_examen';
            $page_data['page_title'] = get_phrase('nouveau-examen');
            $this->load->view('backend/index', $page_data);
        }   
    }*/
    function type_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_type';
            $page_data['page_title'] = get_phrase('nouvelle-ordonnance');
            $this->load->view('backend/index', $page_data);
        }   
    }
    function actes_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_actes';
            $page_data['page_title'] = get_phrase('nouvel-acte-médical');
            $this->load->view('backend/index', $page_data);
        }
    }
      function nurse_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_nurse';
            $page_data['page_title'] = get_phrase('nurse');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_nurse';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('nurse');
            $this->load->view('backend/index', $page_data);
        }
    }
     function accountant_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_accountant';
            $page_data['page_title'] = get_phrase('accountant');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_accountant';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('accountant');
            $this->load->view('backend/index', $page_data);
        }
    }
     function receptionist_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_receptionist';
            $page_data['page_title'] = get_phrase('receptionist');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_receptionist';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('receptionist');
            $this->load->view('backend/index', $page_data);
        }
    }
     function laboratorist_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_laboratorist';
            $page_data['page_title'] = get_phrase('laborantin');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_laboratorist';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('laborantin');
            $this->load->view('backend/index', $page_data);
        }
    }

    function magasin_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_magasin';
            $page_data['page_title'] = get_phrase('magasinier');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_magasin';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('magasinier');
            $this->load->view('backend/index', $page_data);
        }
    }
      function pharmacist_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_pharmacist';
            $page_data['page_title'] = get_phrase('pharmacien');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_pharmacist';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('pharmacien');
            $this->load->view('backend/index', $page_data);
        }
    }

    function invoice_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_invoice';
            $page_data['page_title'] = get_phrase('reçu');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_invoice';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('consultation');
            $this->load->view('backend/index', $page_data);
        }
    }

    function invoice_add($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
        $this->session->set_userdata('last_page', current_url());
        redirect(base_url(), 'refresh');
        }
        $this->load->model('invoice_model');
        $data['invoice'] = $this->invoice_model->get_all_invoice();
        $this->load->view('backend/admin/add_invoice', $data);

        if ($task == "create") {
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'admin/invoice_manage');
        }
        $data['page_name'] = 'add_invoice';
        $data['page_title'] = get_phrase('reçu');
        $this->load->view('backend/index', $data);
        }

    function invoice_manage($task = "", $invoice_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        
        if ($task == "create") {
            
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'admin/invoice_manage');
        }

        if ($task == "update") {
            $this->crud_model->update_invoice($invoice_id);
            $this->session->set_flashdata('message', get_phrase("mises à jour des informations a été éffectué avec succès"));
            redirect(base_url() . 'admin/invoice_manage');
        }

        if ($task == "delete") {
            $this->crud_model->delete_invoice($invoice_id);
            redirect(base_url() . 'admin/invoice_manage');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_invoice';
        $data['page_title'] = get_phrase('consultation');
        $this->load->view('backend/index', $data);
    }

    function manage_journal_consult($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_journal_consult';
        $data['page_title'] = get_phrase('journal');
        $this->load->view('backend/index', $data);
    }

    function motice_crud($task = "", $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_notice';
            $page_data['page_title'] = get_phrase('notice');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_notice';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('notice');
            $this->load->view('backend/index', $page_data);
        }
    }
    /*     * *ADMIN DASHBOARD** */

    function dashboard() {
        if ($this->session->userdata('admin_login') != 1) {
           // $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        $page_data['page_name'] = 'dashboard';
        $page_data['page_title'] = get_phrase('admin_dashboard');
        $page_data['admin_dashboard'] = $this->_get_dashboard_data(
            (string) $this->input->get('start_date', TRUE),
            (string) $this->input->get('end_date', TRUE)
        );
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Données du tableau de bord administrateur.
     *
     * L'ancienne version exécutait quatre "SELECT * FROM invoice/examen"
     * (soit toutes les lignes, colonnes JSON comprises) puis recomptait tout
     * en PHP pour n'afficher que quatre nombres. Ici, deux requêtes
     * d'agrégation suffisent, et elles s'appuient sur les colonnes
     * net_amount / total_amount déjà calculées lors de la migration.
     *
     * Les dates sont comparées par MySQL sur les colonnes DATETIME
     * (creation_datetime / creation_time). L'ancien code utilisait
     * STR_TO_DATE(..., '%d-%b-%Y %H:%i:%s'), hérité de l'époque où ces
     * colonnes étaient du texte : depuis leur passage en DATETIME, la
     * conversion échouait et les filtres ne renvoyaient plus rien.
     */
    private function _get_dashboard_data($start_date = '', $end_date = '')
    {
        $inv = $this->db->query("
            SELECT
                COUNT(*) AS nb_total,
                SUM(DATE(creation_datetime) = CURDATE()) AS nb_jour,
                SUM(total_amount) AS ca_total,
                SUM(CASE WHEN DATE(creation_datetime) = CURDATE() THEN total_amount ELSE 0 END) AS ca_jour,
                SUM(CASE WHEN YEAR(creation_datetime) = YEAR(CURDATE())
                          AND MONTH(creation_datetime) = MONTH(CURDATE())
                         THEN total_amount ELSE 0 END) AS ca_mois,
                SUM(status = 'impayé') AS nb_impayees
            FROM invoice
        ")->row_array();

        $exa = $this->db->query("
            SELECT
                COUNT(*) AS nb_total,
                SUM(DATE(creation_time) = CURDATE()) AS nb_jour,
                SUM(total_amount) AS ca_total,
                SUM(CASE WHEN DATE(creation_time) = CURDATE() THEN total_amount ELSE 0 END) AS ca_jour,
                SUM(CASE WHEN YEAR(creation_time) = YEAR(CURDATE())
                          AND MONTH(creation_time) = MONTH(CURDATE())
                         THEN total_amount ELSE 0 END) AS ca_mois,
                SUM(statut_examen IN ('En instance','En cours','En attente','Pending')) AS nb_attente
            FROM examen
        ")->row_array();

        $dashboard = array(
            'patients_total'      => (int) $this->db->count_all('patient'),
            'consultations_total' => (int) $inv['nb_total'],
            'consultations_jour'  => (int) $inv['nb_jour'],
            'consultations_ca'    => (float) $inv['ca_total'],
            'consultations_ca_jour' => (float) $inv['ca_jour'],
            'consultations_ca_mois' => (float) $inv['ca_mois'],
            'consultations_impayees' => (int) $inv['nb_impayees'],
            'examens_total'       => (int) $exa['nb_total'],
            'examens_jour'        => (int) $exa['nb_jour'],
            'examens_ca'          => (float) $exa['ca_total'],
            'examens_ca_jour'     => (float) $exa['ca_jour'],
            'examens_ca_mois'     => (float) $exa['ca_mois'],
            'examens_attente'     => (int) $exa['nb_attente'],
            'traitements_total'   => (int) $this->db->count_all('traitement'),
            'personnel'           => array(
                'doctor'        => (int) $this->db->count_all('doctor'),
                'receptionist'  => (int) $this->db->count_all('receptionist'),
                'laboratorist'  => (int) $this->db->count_all('laboratorist'),
                'pharmacist'    => (int) $this->db->count_all('pharmacist'),
            ),
            'periode'             => null,
        );

        $dashboard['ca_jour'] = $dashboard['consultations_ca_jour'] + $dashboard['examens_ca_jour'];
        $dashboard['ca_mois'] = $dashboard['consultations_ca_mois'] + $dashboard['examens_ca_mois'];

        // ------------------------------------------------------------------
        // Filtre par période (formulaire du tableau de bord).
        // Les dates passent par des requêtes préparées : l'ancienne version
        // concaténait directement $_GET dans le SQL, ce qui exposait la base
        // à une injection.
        // ------------------------------------------------------------------
        if ($start_date !== '' && $end_date !== '' && strtotime($start_date) && strtotime($end_date)) {
            $from = date('Y-m-d 00:00:00', strtotime($start_date));
            $to   = date('Y-m-d 23:59:59', strtotime($end_date));

            $p_inv = $this->db->query("
                SELECT COUNT(*) AS nb, COALESCE(SUM(total_amount),0) AS ca
                FROM invoice WHERE creation_datetime BETWEEN ? AND ?
            ", array($from, $to))->row_array();

            $p_exa = $this->db->query("
                SELECT COUNT(*) AS nb, COALESCE(SUM(total_amount),0) AS ca
                FROM examen WHERE creation_time BETWEEN ? AND ?
            ", array($from, $to))->row_array();

            $dashboard['periode'] = array(
                'du'                  => $from,
                'au'                  => $to,
                'consultations_nb'    => (int) $p_inv['nb'],
                'consultations_ca'    => (float) $p_inv['ca'],
                'examens_nb'          => (int) $p_exa['nb'],
                'examens_ca'          => (float) $p_exa['ca'],
                'total_ca'            => (float) $p_inv['ca'] + (float) $p_exa['ca'],
            );
        }

        // ------------------------------------------------------------------
        // Tendance sur 14 jours (consultations + examens).
        // ------------------------------------------------------------------
        $inv_rows = $this->db->query("
            SELECT DATE(creation_datetime) AS jour, COUNT(*) AS nb, SUM(total_amount) AS ca
            FROM invoice
            WHERE creation_datetime >= (CURDATE() - INTERVAL 13 DAY)
              AND creation_datetime <  (CURDATE() + INTERVAL 1 DAY)
            GROUP BY DATE(creation_datetime)
        ")->result_array();

        $exa_rows = $this->db->query("
            SELECT DATE(creation_time) AS jour, COUNT(*) AS nb, SUM(total_amount) AS ca
            FROM examen
            WHERE creation_time >= (CURDATE() - INTERVAL 13 DAY)
              AND creation_time <  (CURDATE() + INTERVAL 1 DAY)
            GROUP BY DATE(creation_time)
        ")->result_array();

        $map_inv = array(); foreach ($inv_rows as $r) { $map_inv[$r['jour']] = $r; }
        $map_exa = array(); foreach ($exa_rows as $r) { $map_exa[$r['jour']] = $r; }

        $dashboard['tendance'] = array();
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i day"));
            $dashboard['tendance'][] = array(
                'jour'          => date('d/m', strtotime($d)),
                'consultations' => isset($map_inv[$d]) ? (int) $map_inv[$d]['nb'] : 0,
                'examens'       => isset($map_exa[$d]) ? (int) $map_exa[$d]['nb'] : 0,
                'recette'       => (isset($map_inv[$d]) ? (float) $map_inv[$d]['ca'] : 0)
                                 + (isset($map_exa[$d]) ? (float) $map_exa[$d]['ca'] : 0),
            );
        }

        // ------------------------------------------------------------------
        // Listes courtes.
        // ------------------------------------------------------------------
        $dashboard['consultations_recentes'] = $this->db
            ->select('invoice.invoice_id, invoice.invoice_number, invoice.creation_datetime, invoice.status, invoice.total_amount, patient.name, patient.prenom')
            ->from('invoice')
            ->join('patient', 'patient.patient_id = invoice.patient_id', 'left')
            ->order_by('invoice.invoice_id', 'DESC')
            ->limit(8)->get()->result_array();

        $dashboard['examens_recents'] = $this->db
            ->select('examen.id_examen, examen.examen_number, examen.creation_time, examen.statut_examen, examen.total_amount, patient.name, patient.prenom')
            ->from('examen')
            ->join('patient', 'patient.patient_id = examen.patient_id', 'left')
            ->order_by('examen.id_examen', 'DESC')
            ->limit(6)->get()->result_array();

        return $dashboard;
    }

    /**
     * Endpoint AJAX (POST) commun aux listes de l'administration :
     * admin/list_datatable/patient, /examen, /invoice, /traitement.
     *
     * Recherche, tri et pagination sont délégués à MySQL. Les vues
     * n'envoient donc plus l'intégralité des tables au navigateur.
     */
    function list_datatable($entity = '')
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('admin_login') != 1) {
            http_response_code(403);
            echo json_encode(array('error' => 'Non autorisé'));
            exit;
        }

        $this->load->model('Admin_list_model');
        if (!$this->Admin_list_model->config($entity)) {
            http_response_code(404);
            echo json_encode(array('error' => 'Liste inconnue'));
            exit;
        }

        $post = $this->input->post(null, TRUE);
        $params = array(
            'draw'   => isset($post['draw']) ? (int) $post['draw'] : 1,
            'start'  => isset($post['start']) ? (int) $post['start'] : 0,
            'length' => isset($post['length']) ? (int) $post['length'] : 25,
            'search' => array(
                'value' => isset($post['search']['value']) ? trim(strip_tags($post['search']['value'])) : ''
            ),
            'order'  => isset($post['order']) ? $post['order'] : array(),
        );

        try {
            $rows = $this->Admin_list_model->get_datatables($entity, $params);
            $data = array();
            foreach ($rows as $row) {
                $data[] = $this->_format_list_row($entity, $row);
            }

            echo json_encode(array(
                'draw'            => $params['draw'],
                'recordsTotal'    => $this->Admin_list_model->count_all($entity),
                'recordsFiltered' => $this->Admin_list_model->count_filtered($entity, $params),
                'data'            => $data,
            ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(array(
                'draw' => $params['draw'], 'recordsTotal' => 0, 'recordsFiltered' => 0,
                'data' => array(), 'error' => $e->getMessage(),
            ), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /** Met en forme une ligne de liste pour le JSON renvoyé à DataTables. */
    private function _format_list_row($entity, $row)
    {
        $patient = trim((isset($row['name']) ? $row['name'] : '') . ' ' . (isset($row['prenom']) ? $row['prenom'] : ''));
        if ($patient === '') {
            $patient = 'Patient non renseigné';
        }

        switch ($entity) {

            case 'patient':
                return array(
                    'id'        => (int) $row['patient_id'],
                    'photo'     => $this->crud_model->get_image_url('patient', $row['patient_id']),
                    'name'      => trim($row['name'] . ' ' . $row['prenom']),
                    'phone'     => $row['phone'],
                    'email'     => $row['email'],
                    'view_url'  => base_url('modal/popup/view_patient/' . $row['patient_id']),
                    'edit_url'  => base_url('admin/patient_crud/edit/' . $row['patient_id']),
                    'print_url' => base_url('PatientPrint/patient_fpdf/' . $row['patient_id']),
                    'delete_url'=> base_url('admin/patient/delete/' . $row['patient_id']),
                );

            case 'examen':
                return array(
                    'id'         => (int) $row['id_examen'],
                    'number'     => $row['examen_number'],
                    'patient'    => $patient,
                    'date'       => $row['creation_time'],
                    'amount'     => number_format((float) $row['total_amount'], 0, ',', ' '),
                    'statut'     => $row['statut_examen'],
                    'designation'=> $this->_entries_field($row['examen_entries'], 'description'),
                    'edit_url'   => base_url('admin/examen_crud/edit/' . $row['id_examen']),
                    'print_url'  => base_url('InvoiceExamenCaisse/examenCaisse_print/' . $row['id_examen']),
                    'delete_url' => base_url('admin/examen/delete/' . $row['id_examen']),
                );

            case 'invoice':
                return array(
                    'id'         => (int) $row['invoice_id'],
                    'number'     => $row['invoice_number'],
                    'patient'    => $patient,
                    'date'       => $row['creation_datetime'],
                    'amount'     => number_format((float) $row['total_amount'], 0, ',', ' '),
                    'statut'     => $row['status'],
                    'designation'=> $this->_entries_field($row['invoice_entries'], 'description'),
                    'edit_url'   => base_url('admin/invoice_crud/edit/' . $row['invoice_id']),
                    'print_url'  => base_url('Invoice/invoice_print/' . $row['invoice_id']),
                    'delete_url' => base_url('admin/invoice_manage/delete/' . $row['invoice_id']),
                );

            case 'traitement':
                return array(
                    'id'         => (int) $row['traitement_id'],
                    'number'     => $row['traitement_number'],
                    'patient'    => $patient,
                    'date'       => $row['date_traitement'],
                    'statut'     => $row['status'],
                    'designation'=> $this->_entries_field($row['traitement_entries'], 'description'),
                    'edit_url'   => base_url('admin/traitement_crud/edit/' . $row['traitement_id']),
                    'delete_url' => base_url('admin/traitement/delete/' . $row['traitement_id']),
                );
        }
        return $row;
    }

    /** Concatène un champ des entrées JSON, en ignorant le JSON invalide. */
    private function _entries_field($json, $field)
    {
        $entries = json_decode($json, true);
        if (!is_array($entries)) {
            return '';
        }
        $out = array();
        foreach ($entries as $entry) {
            if (is_array($entry) && isset($entry[$field]) && $entry[$field] !== '') {
                $out[] = $entry[$field];
            }
        }
        return implode(', ', $out);
    }

    /*     * ***LANGUAGE SETTINGS******** */

    function manage_language($param1 = '', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');

        if ($param1 == 'edit_phrase') {
            $page_data['edit_profile'] = $param2;
        }
        if ($param1 == 'update_phrase') {
            $language = $param2;
            $total_phrase = $this->input->post('total_phrase');
            for ($i = 1; $i < $total_phrase; $i++) {
                //$data[$language]	=	$this->input->post('phrase').$i;
                $this->db->where('phrase_id', $i);
                $this->db->update('language', array($language => $this->input->post('phrase' . $i)));
            }
            redirect(base_url() . 'admin/manage_language/edit_phrase/' . $language, 'refresh');
        }
        if ($param1 == 'do_update') {
            $language = $this->input->post('language');
            $data[$language] = $this->input->post('phrase');
            $this->db->where('phrase_id', $param2);
            $this->db->update('language', $data);
            $this->session->set_flashdata('message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin/manage_language/', 'refresh');
        }
        if ($param1 == 'add_phrase') {
            $data['phrase'] = $this->input->post('phrase');
            $this->db->insert('language', $data);
            $this->session->set_flashdata('message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin/manage_language/', 'refresh');
        }
        if ($param1 == 'add_language') {
            $language = $this->input->post('language');
            $this->load->dbforge();
            $fields = array(
                $language => array(
                    'type' => 'LONGTEXT'
                )
            );
            $this->dbforge->add_column('language', $fields);

            $this->session->set_flashdata('message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin/manage_language/', 'refresh');
        }
        if ($param1 == 'delete_language') {
            $language = $param2;
            $this->load->dbforge();
            $this->dbforge->drop_column('language', $language);
            $this->session->set_flashdata('message', get_phrase('settings_updated'));

            redirect(base_url() . 'admin/manage_language/', 'refresh');
        }
        $page_data['page_name'] = 'manage_language';
        $page_data['page_title'] = get_phrase('manage_language');
        //$page_data['language_phrases'] = $this->db->get('language')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    /*     * ***SITE/SYSTEM SETTINGS******** */

    function system_settings($param1 = '', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');

        if ($param1 == 'do_update') {
            $this->crud_model->update_system_settings();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/logo.png');
             move_uploaded_file($_FILES['welcome_message_image']['tmp_name'], 'uploads/fronted/welcome.png');
            $this->session->set_flashdata('message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin/system_settings/', 'refresh');
        }
        $page_data['page_name'] = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $page_data['settings'] = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    // SMS settings.
    function sms_settings($param1 = '') {

        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');

        if ($param1 == 'do_update') {
            $this->crud_model->update_sms_settings();
            $this->session->set_flashdata('message', get_phrase('settings_updated'));
            redirect(base_url() . 'admin/sms_settings/', 'refresh');
        }

        $page_data['page_name'] = 'sms_settings';
        $page_data['page_title'] = get_phrase('sms_settings');
        $this->load->view('backend/index', $page_data);
    }

    /*     * ****MANAGE OWN PROFILE AND CHANGE PASSWORD** */

    function manage_profile($param1 = '', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');

        if ($param1 == 'update_profile_info') {
            $data['name'] = $this->input->post('name');
            $data['email'] = $this->input->post('email');

            $this->db->where('admin_id', $this->session->userdata('login_user_id'));
            $this->db->update('admin', $data);

            $this->session->set_flashdata('message', get_phrase('profile_info_updated_successfuly'));
            redirect(base_url() . 'admin/manage_profile');
        }
        if ($param1 == 'change_password') {
            $current_password_input = sha1($this->input->post('password'));
            $new_password = sha1($this->input->post('new_password'));
            $confirm_new_password = sha1($this->input->post('confirm_new_password'));

            $current_password_db = $this->db->get_where('admin', array('admin_id' =>
                        $this->session->userdata('login_user_id')))->row()->password;

            if ($current_password_db == $current_password_input && $new_password == $confirm_new_password) {
                $this->db->where('admin_id', $this->session->userdata('login_user_id'));
                $this->db->update('admin', array('password' => $new_password));

                $this->session->set_flashdata('message', get_phrase('password_info_updated_successfuly'));
                redirect(base_url() . 'admin/manage_profile');
            } else {
                $this->session->set_flashdata('message', get_phrase('password_update_failed'));
                redirect(base_url() . 'admin/manage_profile');
            }
        }
        $page_data['page_name'] = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data'] = $this->db->get_where('admin', array('admin_id' => $this->session->userdata('login_user_id')))->result_array();
        $this->load->view('backend/index', $page_data);
    }
    function department($task = "", $department_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_department_info();
            $this->session->set_flashdata('message', get_phrase('department_info_saved_successfuly'));
            redirect(base_url() . 'admin/department');
        }

        if ($task == "update") {
            $this->crud_model->update_department_info($department_id);
            $this->session->set_flashdata('message', get_phrase('department_info_updated_successfuly'));
            redirect(base_url() . 'admin/department');
        }

        if ($task == "delete") {
            $this->crud_model->delete_department_info($department_id);
            redirect(base_url() . 'admin/department');
        }

        $data['department_info'] = $this->crud_model->select_department_info();
        $data['page_name'] = 'manage_department';
        $data['page_title'] = get_phrase('department');
        $this->load->view('backend/index', $data);
    }
    function slider($task = "", $slider_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_slider_info();
            $this->session->set_flashdata('message', get_phrase('slider_info_saved_successfuly'));
            redirect(base_url() . 'admin/slider');
        }

        if ($task == "update") {
            $this->crud_model->update_slider_info($slider_id);
            $this->session->set_flashdata('message', get_phrase('slider_info_updated_successfuly'));
            redirect(base_url() . 'admin/slider');
        }

        if ($task == "delete") {
            $this->crud_model->delete_slider_info($slider_id);
            redirect(base_url() . 'admin/slider');
        }

        $data['slider_info'] = $this->crud_model->select_slider_info();
        $data['page_name'] = 'manage_slider';
        $data['page_title'] = get_phrase('slider');
        $this->load->view('backend/index', $data);
    }
    function openig_hours($task = "", $openig_hours_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_openig_hours_info();
            $this->session->set_flashdata('message', get_phrase('info_saved_successfuly'));
            redirect(base_url() . 'admin/openig_hours');
        }

        if ($task == "delete") {
            $this->crud_model->delete_openig_hours_info($openig_hours_id);
            redirect(base_url() . 'admin/openig_hours');
        }

        $data['openig_hours_info'] = $this->crud_model->select_openig_hours_info();
        $data['page_name'] = 'manage_openig_hours';
        $data['page_title'] = get_phrase('openig_hours');
        $this->load->view('backend/index', $data);
    }
    function services($task = "", $services_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_services_info();
            $this->session->set_flashdata('message', get_phrase('info_saved_successfuly'));
            redirect(base_url() . 'admin/services');
        }

        if ($task == "delete") {
            $this->crud_model->delete_services_info($services_id);
            redirect(base_url() . 'admin/services');
        }

        $data['services_info'] = $this->crud_model->select_services_info();
        $data['page_name'] = 'manage_services';
        $data['page_title'] = get_phrase('services');
        $this->load->view('backend/index', $data);
    }

    function examen_crud($task = "" , $param2 = ""){ 
        if ($this->session->userdata('admin_login') != 1) {
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
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        } 

        if ($task == "create") {
            $this->crud_model->save_examen_info();
            $this->session->set_flashdata('message', get_phrase('informations a-été-enregistrées-avec-succès'));
            redirect(base_url() . 'admin/examen');
        }

        if ($task == "update") {
                $this->crud_model->update_examen_info($id_examen);
                $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
                redirect(base_url() . 'admin/examen');
        }

        if ($task == "delete") {
            $this->crud_model->delete_examen_info($id_examen);
            redirect(base_url() . 'admin/examen');
        }

        $data['examen_info'] = $this->crud_model->select_examen_info();
        $data['page_name'] = 'manage_examen';
        $data['page_title'] = get_phrase('examen');
        $this->load->view('backend/index', $data);
    }

    function vente_crud($task = "" , $param2 = ""){ 
        if ($this->session->userdata('admin_login') != 1) {
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

    function vente($task = "", $vente_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        } 

        if ($task == "create") {
            $this->crud_model->save_vente_info();
            $this->session->set_flashdata('message', get_phrase('informations a-été-enregistrées-avec-succès'));
            redirect(base_url() . 'admin/vente');
        }

        if ($task == "update") {
                $this->crud_model->update_vente_info($vente_id);
                $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
                redirect(base_url() . 'admin/vente');
        }

        if ($task == "delete") {
            $this->crud_model->delete_vente_info($vente_id);
            redirect(base_url() . 'admin/vente');
        }

        $data['vente_info'] = $this->crud_model->select_vente_info();
        $data['page_name'] = 'manage_vente';
        $data['page_title'] = get_phrase('vente');
        $this->load->view('backend/index', $data);
    }

    function manage_journal_vente($task = "") {
            if ($this->session->userdata('admin_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }

            $data['vente_info'] = $this->crud_model->select_vente_info();
            $data['page_name'] = 'manage_journal_vente';
            $data['page_title'] = get_phrase('journal');
            $this->load->view('backend/index', $data);
            }

    function produit_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($task == 'add') {
            $page_data['page_name'] = 'add_produit';
            $page_data['page_title'] = get_phrase('nouveau-produit');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_produit';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('produit');
            $this->load->view('backend/index', $page_data);
        }   
    }

    function produit($task = "", $produit_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        } 

        if ($task == "create") {
            $this->crud_model->save_produit_info();
            $this->session->set_flashdata('message', get_phrase('informations a-été-enregistrées-avec-succès'));
            redirect(base_url() . 'admin/produit');
        }

        if ($task == "update") {
          
                $this->crud_model->update_produit_info($produit_id);
                $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
            
                redirect(base_url() . 'admin/produit');
        }

        if ($task == "delete") {
            $this->crud_model->delete_produit_info($produit_id);
            redirect(base_url() . 'admin/produit');
        }

        $data['produit_info'] = $this->crud_model->select_produit_info();
        $data['page_name'] = 'manage_produit';
        $data['page_title'] = get_phrase('produit');
        $this->load->view('backend/index', $data);
    }

    function type($task = "", $id_type = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        } 

        if ($task == "create") {
            $this->crud_model->save_type_info();
            $this->session->set_flashdata('message', get_phrase('informations a-été-enregistrées-avec-succès'));
            redirect(base_url() . 'admin/type');
        }

        $data['type_info'] = $this->crud_model->select_type_info();
        $data['page_name'] = 'manage_type';
        $data['page_title'] = get_phrase('ordonnance_type');
        $this->load->view('backend/index', $data);
    }
    function actes($task = "", $actes_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_services_info();
            $this->session->set_flashdata('message', get_phrase('info_saved_successfuly'));
            redirect(base_url() . 'admin/actes');
        }

        if ($task == "update") {
          
                $this->crud_model->update_doctor_info($doctor_id);
                $this->session->set_flashdata('message', get_phrase('doctor_info_updated_successfuly'));
            
                redirect(base_url() . 'admin/actes');
        }

        if ($task == "delete") {
            $this->crud_model->delete_services_info($services_id);
            redirect(base_url() . 'admin/actes');
        }

        $data['services_info'] = $this->crud_model->select_services_info();
        $data['page_name'] = 'manage_actes';
        $data['page_title'] = get_phrase('actes-médicaux');
        $this->load->view('backend/index', $data);
    }
    function doctor($task = "", $doctor_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $doctor = $this->db->get_where('doctor', array('email' => $email))->row()->name;

            if ($doctor == null) {
                $this->crud_model->save_doctor_info();
                $this->session->set_flashdata('message', get_phrase('doctor_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/doctor');
        }

        if ($task == "update") {
          
                $this->crud_model->update_doctor_info($doctor_id);
                $this->session->set_flashdata('message', get_phrase('doctor_info_updated_successfuly'));
            
                redirect(base_url() . 'admin/doctor');
        }

        if ($task == "delete") {
            $this->crud_model->delete_doctor_info($doctor_id);
            redirect(base_url() . 'admin/doctor');
        }
        $data['doctor_info'] = $this->crud_model->select_doctor_info();
        $data['page_name'] = 'manage_doctor';
        $data['page_title'] = get_phrase('médecin');
        $this->load->view('backend/index', $data);
    }
    
    function magasin($task = "", $magasin_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $magasin = $this->db->get_where('magasin', array('email' => $email))->row()->name;
            if ($magasin == null) {
                $this->crud_model->save_magasin_info();
                $this->session->set_flashdata('message', get_phrase('magasin_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/magasin');
        }

        if ($task == "update") {
                $this->crud_model->update_magasin_info($magasin_id);
                $this->session->set_flashdata('message', get_phrase('magasin_info_updated_successfuly'));
                redirect(base_url() . 'admin/magasin');
        }

        if ($task == "delete") {
            $this->crud_model->delete_magasin_info($magasin_id);
            redirect(base_url() . 'admin/magasin');
        }

        $data['magasin_info'] = $this->crud_model->select_magasin_info();
        $data['page_name'] = 'manage_magasin';
        $data['page_title'] = get_phrase('magasinier');
        $this->load->view('backend/index', $data);
    }

    function patient($task = "", $patient_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $patient = $this->db->get_where('patient', array('email' => $email))->row()->name;
            if ($patient == null) {
                $this->crud_model->save_patient_info();
                $this->session->set_flashdata('message', get_phrase('patient_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/patient');
        }

        if ($task == "update") {
                $this->crud_model->update_patient_info($patient_id);
                $this->session->set_flashdata('message', get_phrase('patient_info_updated_successfuly'));
                redirect(base_url() . 'admin/patient');
        }

        if ($task == "delete") {
            $this->crud_model->delete_patient_info($patient_id);
            redirect(base_url() . 'admin/patient');
        }

        $data['patient_info'] = $this->crud_model->select_patient_info();
        $data['page_name'] = 'manage_patient';
        $data['page_title'] = get_phrase('patient');
        $this->load->view('backend/index', $data);
    }

    function nurse($task = "", $nurse_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $nurse = $this->db->get_where('nurse', array('email' => $email))->row()->name;
            if ($nurse == null) {
                $this->crud_model->save_nurse_info();
                $this->session->set_flashdata('message', get_phrase('nurse_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/nurse');
        }

        if ($task == "update") {
                $this->crud_model->update_nurse_info($nurse_id);
                $this->session->set_flashdata('message', get_phrase('nurse_info_updated_successfuly'));
                redirect(base_url() . 'admin/nurse');
        }

        if ($task == "delete") {
            $this->crud_model->delete_nurse_info($nurse_id);
            redirect(base_url() . 'admin/nurse');
        }

        $data['nurse_info'] = $this->crud_model->select_nurse_info();
        $data['page_name'] = 'manage_nurse';
        $data['page_title'] = get_phrase('infirmière');
        $this->load->view('backend/index', $data);
    }

    function pharmacist($task = "", $pharmacist_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = $_POST['email'];
            $pharmacist = $this->db->get_where('pharmacist', array('email' => $email))->row()->name;
            if ($pharmacist == null) {
                $this->crud_model->save_pharmacist_info();
                $this->session->set_flashdata('message', get_phrase('pharmacist_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/pharmacist');
        }

        if ($task == "update") {
                $this->crud_model->update_pharmacist_info($pharmacist_id);
                $this->session->set_flashdata('message', get_phrase('pharmacist_info_updated_successfuly'));
                redirect(base_url() . 'admin/pharmacist');
        }

        if ($task == "delete") {
            $this->crud_model->delete_pharmacist_info($pharmacist_id);
            redirect(base_url() . 'admin/pharmacist');
        }

        $data['pharmacist_info'] = $this->crud_model->select_pharmacist_info();
        $data['page_name'] = 'manage_pharmacist';
        $data['page_title'] = get_phrase('pharmacien');
        $this->load->view('backend/index', $data);
    }

    //.........................Debut partie traitement.................
        function traitement_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('admin_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }
            if ($task == 'add') {
                $page_data['page_name'] = 'add_traitement';
                $page_data['page_title'] = get_phrase('traitement');
                $this->load->view('backend/index', $page_data);
            }elseif ($task == 'edit') {
               $page_data['page_name'] = 'edit_traitement';
               $page_data['param2'] = $param2;
                $page_data['page_title'] = get_phrase('traitement');
                $this->load->view('backend/index', $page_data);
            }
        }

        function traitement($task = "", $traitement_id = "")
        {
            if ($this->session->userdata('admin_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_traitement_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'admin/traitement');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_traitement_info($traitement_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'admin/traitement');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_traitement_info($traitement_id);
                redirect(base_url() .'admin/traitement');
            }
            
            $data['traitement_info']  = $this->crud_model->select_traitement_info();
            $data['page_name']      = 'manage_traitement';
            $data['page_title']     = get_phrase('traitement');
            $this->load->view('backend/index', $data);
        }
        //.........................Fin partie traitement....................

    function laboratorist($task = "", $laboratorist_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == "create") {
            $email = $_POST['email'];
            $laboratorist = $this->db->get_where('laboratorist', array('email' => $email))->row()->name;
            if ($laboratorist == null) {
                $this->crud_model->save_laboratorist_info();
                $this->session->set_flashdata('message', get_phrase('laboratorist_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/laboratorist');
        }

        if ($task == "update") {
                $this->crud_model->update_laboratorist_info($laboratorist_id);
                $this->session->set_flashdata('message', get_phrase('laboratorist_info_updated_successfuly'));
                redirect(base_url() . 'admin/laboratorist');
        }

        if ($task == "delete") {
            $this->crud_model->delete_laboratorist_info($laboratorist_id);
            redirect(base_url() . 'admin/laboratorist');
        }

        $data['laboratorist_info'] = $this->crud_model->select_laboratorist_info();
        $data['page_name'] = 'manage_laboratorist';
        $data['page_title'] = get_phrase('laborantin');
        $this->load->view('backend/index', $data);
    }

    function accountant($task = "", $accountant_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == "create") {
            $email = $_POST['email'];
            $accountant = $this->db->get_where('accountant', array('email' => $email))->row()->name;
            if ($accountant == null) {
                $this->crud_model->save_accountant_info();
                $this->session->set_flashdata('message', get_phrase('accountant_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/accountant');
        }

        if ($task == "update") {
                $this->crud_model->update_accountant_info($accountant_id);
                $this->session->set_flashdata('message', get_phrase('accountant_info_updated_successfuly'));
                redirect(base_url() . 'admin/accountant');
        }

        if ($task == "delete") {
            $this->crud_model->delete_accountant_info($accountant_id);
            redirect(base_url() . 'admin/accountant');
        }

        $data['accountant_info'] = $this->crud_model->select_accountant_info();
        $data['page_name'] = 'manage_accountant';
        $data['page_title'] = get_phrase('comptable');
        $this->load->view('backend/index', $data);
    }

    function receptionist($task = "", $receptionist_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $name = $_POST['name'];
            $receptionist = $this->db->get_where('receptionist', array('name' => $name))->row()->name;
            if ($receptionist == null) {
                $this->crud_model->save_receptionist_info();
                $this->session->set_flashdata('message', get_phrase('receptionist_info_saved_successfuly'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'admin/receptionist');
        }

        if ($task == "update") {
                $this->crud_model->update_receptionist_info($receptionist_id);
                $this->session->set_flashdata('message', get_phrase('receptionist_info_updated_successfuly'));
                redirect(base_url() . 'admin/receptionist');
        }

        if ($task == "delete") {
            $this->crud_model->delete_receptionist_info($receptionist_id);
            redirect(base_url() . 'admin/receptionist');
        }

        $data['receptionist_info'] = $this->crud_model->select_receptionist_info();
        $data['page_name'] = 'manage_receptionist';
        $data['page_title'] = get_phrase('receptionniste');
        $this->load->view('backend/index', $data);
    }

    function payment_history($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        // Le module moderne charge les données via DataTables Server-Side.
        // Ne plus charger toutes les factures en mémoire ici.
        $data['page_name'] = 'historique_paiement';
        $data['page_title'] = get_phrase('historique-de-paiement');
        $this->load->view('backend/index', $data);
    }

    function bed_allotment($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['bed_allotment_info'] = $this->crud_model->select_bed_allotment_info();
        $data['page_name'] = 'show_bed_allotment';
        $data['page_title'] = get_phrase('allocation_de_lit');
        $this->load->view('backend/index', $data);
    }

    function blood_bank($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['blood_bank_info'] = $this->crud_model->select_blood_bank_info();
        $data['page_name'] = 'show_blood_bank';
        $data['page_title'] = get_phrase('banque_du_sang');
        $this->load->view('backend/index', $data);
    }

    function blood_donor($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['blood_donor_info'] = $this->crud_model->select_blood_donor_info();
        $data['page_name'] = 'show_blood_donor';
        $data['page_title'] = get_phrase('don_du_sang');
        $this->load->view('backend/index', $data);
    }

    function medicine($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['medicine_info'] = $this->crud_model->select_medicine_info();
        $data['page_name'] = 'show_medicine';
        $data['page_title'] = get_phrase('médicament');
        $this->load->view('backend/index', $data);
    }

    function operation_report($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'show_operation_report';
        $data['page_title'] = get_phrase("rapport_de_l'opération");
        $this->load->view('backend/index', $data);
    }

    function birth_report($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'show_birth_report';
        $data['page_title'] = get_phrase('rapport_de_naissance');
        $this->load->view('backend/index', $data);
    }

    function death_report($task = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'show_death_report';
        $data['page_title'] = get_phrase('rapport_de_décés');
        $this->load->view('backend/index', $data);
    }

    function notice($task = "", $notice_id = "") {
        if ($this->session->userdata('admin_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_notice_info();
            $this->session->set_flashdata('message', get_phrase('notice_info_saved_successfuly'));
            redirect(base_url() . 'admin/notice');
        }

        if ($task == "update") {
            $this->crud_model->update_notice_info($notice_id);
            $this->session->set_flashdata('message', get_phrase('notice_info_updated_successfuly'));
            redirect(base_url() . 'admin/notice');
        }

        if ($task == "delete") {
            $this->crud_model->delete_notice_info($notice_id);
            redirect(base_url() . 'admin/notice');
        }

        $data['notice_info'] = $this->crud_model->select_notice_info();
        $data['page_name'] = 'manage_notice';
        $data['page_title'] = get_phrase('noticeboard');
        $this->load->view('backend/index', $data);
    }

}
