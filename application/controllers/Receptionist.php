<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Receptionist extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    function index() {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'dashboard';
        $data['page_title'] = get_phrase('receptionist_dashboard');
        $data['reception_dashboard'] = $this->_get_dashboard_data();
        $this->load->view('backend/index', $data);
    }

    /**
     * Données légères et centralisées du tableau de bord de réception.
     * Les anciennes requêtes SQL étaient dupliquées dans la vue et rendaient
     * le chargement fragile lorsque les formats de date différaient.
     */
    private function _get_dashboard_data()
    {
        $dashboard = array(
            'patients_total' => (int) $this->db->count_all('patient'),
            'consultations_total' => (int) $this->db->count_all('invoice'),
            'examens_total' => (int) $this->db->count_all('examen'),
            'medicaments_total' => (int) $this->db->count_all('medicine'),
            'rendez_vous_attente' => 0,
            'patients_recents' => array(),
            'consultations_recentes' => array(),
            'examens_attente' => array()
        );

        $dashboard['rendez_vous_attente'] = (int) $this->db
            ->group_start()
                ->where('status', 'Pending')
                ->or_where('status', 'Requested')
                ->or_where('status', 'En attente')
            ->group_end()
            ->count_all_results('appointment');

        $dashboard['patients_recents'] = $this->db
            ->select('patient_id, name, prenom, phone, sex, age')
            ->from('patient')
            ->order_by('patient_id', 'DESC')
            ->limit(6)
            ->get()
            ->result_array();

        $dashboard['consultations_recentes'] = $this->db
            ->select('invoice.invoice_id, invoice.invoice_number, invoice.creation_datetime, invoice.status, patient.name, patient.prenom')
            ->from('invoice')
            ->join('patient', 'patient.patient_id = invoice.patient_id', 'left')
            ->order_by('invoice.invoice_id', 'DESC')
            ->limit(6)
            ->get()
            ->result_array();

        $dashboard['examens_attente'] = $this->db
            ->select('examen.id_examen, examen.examen_number, examen.creation_time, examen.statut_examen, patient.name, patient.prenom')
            ->from('examen')
            ->join('patient', 'patient.patient_id = examen.patient_id', 'left')
            ->where_in('examen.statut_examen', array('En instance', 'En attente', 'Pending'))
            ->order_by('examen.id_examen', 'DESC')
            ->limit(6)
            ->get()
            ->result_array();

        return $dashboard;
    }

    function doctor_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('receptionist_login') != 1)
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

    function doctor($task = "", $doctor_id = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $email = trim((string) $this->input->post('email', TRUE));
            $doctor = $this->db->get_where('doctor', array('email' => $email))->row();

            if ($doctor == null) {
                $this->crud_model->save_doctor_info();
                $this->session->set_flashdata('message', get_phrase('informations a été enregistrées avec succès'));
            } else {
                $this->session->set_flashdata('message', get_phrase('email dupliqué'));
            }
            redirect(base_url() . 'receptionist/doctor');
        }

        if ($task == "update") {
          
                $this->crud_model->update_doctor_info($doctor_id);
                $this->session->set_flashdata('message', get_phrase('mise à jour a été éffectué avec succès'));
            
                redirect(base_url() . 'receptionist/doctor');
        }

        if ($task == "delete") {
            $this->crud_model->delete_doctor_info($doctor_id);
            redirect(base_url() . 'receptionist/doctor');
        }
        $data['doctor_info'] = $this->crud_model->select_doctor_info();
        $data['page_name'] = 'manage_doctor';
        $data['page_title'] = get_phrase('doctor');
        $this->load->view('backend/index', $data);
    }

   /*function prescription_crud($task = "", $param2 = "", $param3 = ""){
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_prescription';
            $page_data['page_title'] = get_phrase('prescription');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_prescription';
           $page_data['param2'] = $param2;
            $page_data['param3'] = $param3;
            $page_data['page_title'] = get_phrase('prescription');
            $this->load->view('backend/index', $page_data);
        }
    }

    function prescription($task = "", $prescription_id = "", $menu_check = '', $patient_id = '') {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_prescription_info();
            $this->session->set_flashdata('message', get_phrase('les informations a été enregistrées avec succès'));
            redirect(base_url() . 'receptionist/prescription');
        }

        if ($task == "update") {
            $this->crud_model->update_prescription_info($prescription_id);
            $this->session->set_flashdata('message', get_phrase('mise à jour a été éffectué avec succès'));
            if ($menu_check == 'from_prescription')
                redirect(base_url() . 'receptionist/prescription');
            else
                redirect(base_url() . 'receptionist/medication_history/' . $patient_id);
        }

        if ($task == "delete") {
            $this->crud_model->delete_prescription_info($prescription_id);
            if ($menu_check == 'from_prescription')
                redirect(base_url() . 'receptionist/prescription');
            else
                redirect(base_url() . 'receptionist/medication_history/' . $patient_id);
        }

        $data['prescription_info'] = $this->crud_model->select_prescription_info_by_doctor_id();
        $data['menu_check'] = 'from_prescription';
        $data['page_name'] = 'manage_prescription';
        $data['page_title'] = get_phrase('prescription');
        $this->load->view('backend/index', $data);
    }*/

     function patient_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('receptionist_login') != 1) {
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
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_patient_info();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'receptionist/patient');
        }

        if ($task == "update") {
                $this->crud_model->update_patient_info($patient_id);
                $this->session->set_flashdata('message', get_phrase("l'informations a été mises à jour avec succès"));
                redirect(base_url() . 'receptionist/patient');
        }

        if ($task == "delete") {
            $this->crud_model->delete_patient_info($patient_id);
            redirect(base_url() . 'receptionist/patient');
        }

        $data['patient_info'] = $this->crud_model->select_patient_info();
        $data['page_name'] = 'manage_patient';
        $data['page_title'] = get_phrase('patient');
        $this->load->view('backend/index', $data);
    }

    /**
     * Endpoint AJAX (POST) — liste des patients au format DataTables server-side.
     * Recherche et pagination sont effectuées en base, pas en PHP/JS, afin de
     * rester rapide même avec plusieurs milliers de patients.
     *
     * Route par défaut CodeIgniter : receptionist/patient_datatable
     */
    function patient_datatable()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('receptionist_login') != 1) {
            http_response_code(403);
            echo json_encode(array('error' => 'Non autorisé'));
            exit;
        }

        $this->load->model('Patient_model');

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
            $records_total    = $this->Patient_model->count_all();
            $records_filtered = $this->Patient_model->count_filtered($params);
            $rows             = $this->Patient_model->get_datatables($params);

            $data = array();
            foreach ($rows as $row) {
                $data[] = array(
                    'patient_id'  => $row['patient_id'],
                    'name'        => $row['name'],
                    'prenom'      => $row['prenom'],
                    'phone'       => $row['phone'],
                    'sex'         => $row['sex'],
                    'age'         => $row['age'],
                    'edit_url'    => base_url() . 'receptionist/patient_crud/edit/' . $row['patient_id'],
                );
            }

            echo json_encode(array(
                'draw' => $params['draw'],
                'recordsTotal' => $records_total,
                'recordsFiltered' => $records_filtered,
                'data' => $data,
            ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(array(
                'draw' => $params['draw'],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array(),
                'error' => $e->getMessage(),
            ), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Endpoint AJAX (GET) — autocomplétion patient pour les champs Select2
     * utilisés dans les formulaires d'examen, de facture, de vente, de
     * traitement, etc. Répond au format attendu par Select2 :
     *   { results: [{id, text, phone, age, sex}], pagination: {more: bool} }
     *
     * Paramètres GET : q (terme recherché), page (1-based, optionnel)
     */
    function patient_search()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('receptionist_login') != 1) {
            http_response_code(403);
            echo json_encode(array('results' => array()));
            exit;
        }

        $this->load->model('Patient_model');

        $term  = trim((string) $this->input->get('q', TRUE));
        $page  = max(1, (int) $this->input->get('page', TRUE));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $rows = $this->Patient_model->autocomplete($term, $limit, $offset);

        $has_more = count($rows) > $limit;
        if ($has_more) {
            $rows = array_slice($rows, 0, $limit);
        }

        $results = array();
        foreach ($rows as $row) {
            $label = trim($row['name'] . ' ' . $row['prenom']);
            $meta = array();
            if (!empty($row['phone'])) $meta[] = $row['phone'];
            if (!empty($row['age']))   $meta[] = $row['age'] . ' ans';
            if ($meta) {
                $label .= ' — ' . implode(' · ', $meta);
            }
            $results[] = array(
                'id'     => $row['patient_id'],
                'text'   => $label,
                'name'   => $row['name'],
                'prenom' => $row['prenom'],
                'phone'  => $row['phone'],
            );
        }

        echo json_encode(array(
            'results' => $results,
            'pagination' => array('more' => $has_more),
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Endpoint AJAX (POST) — création / mise à jour d'un patient sans
     * rechargement complet de page. Réutilise la logique existante du
     * Crud_model (mêmes règles, mêmes colonnes) mais répond en JSON au lieu
     * de faire un redirect().
     *
     * $task attendu : "create" ou "update"
     */
    function patient_ajax($task = "", $patient_id = "")
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('receptionist_login') != 1) {
            http_response_code(403);
            echo json_encode(array('success' => false, 'message' => get_phrase('Non autorisé')));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
            http_response_code(400);
            echo json_encode(array('success' => false, 'message' => 'Requête invalide.'));
            exit;
        }

        $name = trim((string) $this->input->post('name'));
        if ($name === '') {
            echo json_encode(array('success' => false, 'message' => get_phrase('le-nom-du-patient-est-obligatoire')));
            exit;
        }

        // Filet de sécurité : si un warning PHP imprévu (ex: upload de photo
        // absent) venait à s'afficher, il ne doit jamais corrompre la
        // réponse JSON envoyée au navigateur. On construit la réponse dans
        // une variable et on nettoie le tampon de sortie juste avant de
        // l'émettre.
        ob_start();
        $response = array('success' => false, 'message' => 'Action inconnue.');
        try {
            if ($task === 'create') {
                $this->crud_model->save_patient_info();
                $new_id = $this->db->insert_id();
                $saved = $this->crud_model->select_patient_info_by_patient_id($new_id);
                $patient = $saved ? $saved[0] : null;

                $response = array(
                    'success' => true,
                    'message' => get_phrase("l'informations a été enregistrées avec succès"),
                    'patient' => $patient ? array(
                        'id'   => $patient['patient_id'],
                        'text' => trim($patient['name'] . ' ' . $patient['prenom']),
                    ) : null,
                );
            } elseif ($task === 'update' && $patient_id !== '') {
                $this->crud_model->update_patient_info($patient_id);
                $saved = $this->crud_model->select_patient_info_by_patient_id($patient_id);
                $patient = $saved ? $saved[0] : null;

                $response = array(
                    'success' => true,
                    'message' => get_phrase("l'informations a été mises à jour avec succès"),
                    'patient' => $patient ? array(
                        'id'   => $patient['patient_id'],
                        'text' => trim($patient['name'] . ' ' . $patient['prenom']),
                    ) : null,
                );
            }
        } catch (Exception $e) {
            $response = array('success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage());
        }
        ob_end_clean();
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    function appointment($task = "", $doctor_id = 'all', $start_timestamp = "", $end_timestamp = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == 'filter') {
            $doctor_id = $this->input->post('doctor_id');
            $start_timestamp = strtotime($this->input->post('start_timestamp'));
            $end_timestamp = strtotime($this->input->post('end_timestamp'));
            redirect(base_url() . 'receptionist/appointment/search/' . $doctor_id . '/' . $start_timestamp . '/' . $end_timestamp);
        }

        if ($task == "create") {
            $this->crud_model->save_appointment_info();
            $this->session->set_flashdata('message', get_phrase('appointment_info_saved_successfuly'));
            redirect(base_url() . 'receptionist/appointment');
        }

        $data['doctor_id'] = $doctor_id;
        if ($start_timestamp == '')
            $data['start_timestamp'] = strtotime('today - 30 days');
        else
            $data['start_timestamp'] = $start_timestamp;
        if ($end_timestamp == '')
            $data['end_timestamp'] = strtotime('today');
        else
            $data['end_timestamp'] = $end_timestamp;

        $data['appointment_info'] = $this->crud_model->select_appointment_info($doctor_id, $data['start_timestamp'], $data['end_timestamp']);
        $data['page_name'] = 'show_appointment';
        $data['page_title'] = get_phrase('appointment');
        $this->load->view('backend/index', $data);
    }

    function appointment_requested($task = "", $appointment_id = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "approve") {
            $this->crud_model->approve_appointment_info($appointment_id);
            $this->session->set_flashdata('message', get_phrase('appointment_info_approved'));
            redirect(base_url() . 'receptionist/appointment_requested');
        }

        $data['requested_appointment_info'] = $this->crud_model->select_requested_appointment_info();
        $data['page_name'] = 'manage_requested_appointment';
        $data['page_title'] = get_phrase('requested_appointment');
        $this->load->view('backend/index', $data);
    }

    function remise_add($task = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->create_remise();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'receptionist/remise_manage');
        }

        $data['page_name'] = 'add_remise';
        $data['page_title'] = get_phrase('remise');
        $this->load->view('backend/index', $data);
    }

    function invoice_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('receptionist_login') != 1) {
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
            $page_data['page_title'] = get_phrase('reçu');
            $this->load->view('backend/index', $page_data);
        }
    }

    function invoice_add($task = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'receptionist/invoice_manage');
        }
        $data['page_name'] = 'add_invoice';
        $data['page_title'] = get_phrase('reçu');
        $this->load->view('backend/index', $data);
        }

    function invoice_manage($task = "", $invoice_id = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        
        if ($task == "create") {
            
            $this->crud_model->create_invoice();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'receptionist/invoice_manage');
        }

        if ($task == "update") {
            $this->crud_model->update_invoice($invoice_id);
            $this->session->set_flashdata('message', get_phrase("mises à jour des informations a été éffectué avec succès"));
            redirect(base_url() . 'receptionist/invoice_manage');
        }

        if ($task == "delete") {
            $this->crud_model->delete_invoice($invoice_id);
            redirect(base_url() . 'receptionist/invoice_manage');
        }

        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_invoice';
        $data['page_title'] = get_phrase('reçu');
        $this->load->view('backend/index', $data);
    }

    function examen_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('receptionist_login') != 1) {
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
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_examen_info();
            $this->session->set_flashdata('message', get_phrase('informations_a_été_enregistré_avec_succés'));
            redirect(base_url() . 'receptionist/examen');
        }

        if ($task == "update") {
                $this->crud_model->update_examen_info($id_examen);
                $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
                redirect(base_url() . 'receptionist/examen');
        }
    
        if ($task == "delete") {
            $this->crud_model->delete_examen_info($id_examen);
            redirect(base_url() . 'receptionist/examen');
        }

        $data['examen_info'] = $this->crud_model->select_examen_info();
        $data['page_name'] = 'manage_examen';
        $data['page_title'] = get_phrase('examen');
        $this->load->view('backend/index', $data);
    }

    function decaissement_crud($task = "" , $param2 = ""){
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
        if ($task == 'add') {
            $page_data['page_name'] = 'add_decaissement';
            $page_data['page_title'] = get_phrase('decaissement');
            $this->load->view('backend/index', $page_data);
        }elseif ($task == 'edit') {
           $page_data['page_name'] = 'edit_decaissement';
           $page_data['param2'] = $param2;
            $page_data['page_title'] = get_phrase('decaissement');
            $this->load->view('backend/index', $page_data);
        }
        }

    function decaissement($task = "", $decaissement_id = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        if ($task == "create") {
            $this->crud_model->save_decaissement_info();
            $this->session->set_flashdata('message', get_phrase('informations-a-été-enregistré-avec-succés'));
            redirect(base_url() . 'receptionist/decaissement');
        }

        if ($task == "update") {
                $this->crud_model->update_decaissement_info($decaissement_id);
                $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
                redirect(base_url() . 'receptionist/decaissement');
        }

        if ($task == "delete") {
            $this->crud_model->delete_decaissement_info($decaissement_id);
            redirect(base_url() . 'receptionist/decaissement');
        }

        $data['decaissement_info'] = $this->crud_model->select_decaissement_info();
        $data['page_name'] = 'manage_decaissement';
        $data['page_title'] = get_phrase('decaissement');
        $this->load->view('backend/index', $data);
        }
    
        function manage_journal_consult($task = "") {
            if ($this->session->userdata('receptionist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }

            $data['invoice_info'] = $this->crud_model->select_invoice_info();
            $data['page_name'] = 'manage_journal_consult';
            $data['page_title'] = get_phrase('journal');
            $this->load->view('backend/index', $data);
            }

        function manage_journal_vente($task = "") {
            if ($this->session->userdata('receptionist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }

            $data['vente_info'] = $this->crud_model->select_vente_info();
            $data['page_name'] = 'manage_journal_vente';
            $data['page_title'] = get_phrase('journal');
            $this->load->view('backend/index', $data);
            }

        function facture_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('receptionist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }
            if ($task == 'add') {
                $page_data['page_name'] = 'add_facture';
                $page_data['page_title'] = get_phrase('facture');
                $this->load->view('backend/index', $page_data);
            }elseif ($task == 'edit') {
               $page_data['page_name'] = 'edit_facture';
               $page_data['param2'] = $param2;
                $page_data['page_title'] = get_phrase('facture');
                $this->load->view('backend/index', $page_data);
            }
            }

        function facture($task = "", $facture_id = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
            }

        if ($task == "create") {
            // Vérifiez si une facture existe déjà pour ce patient (ou d'autres critères)
            $facture = $this->crud_model->get_facture_by_patient_id($this->input->post('patient_id'));

            if ($facture == null) {
                $this->crud_model->save_facture_info();
                $this->session->set_flashdata('message', get_phrase('informations-a-été-enregistré-avec-succés'));
            } else {
                $this->session->set_flashdata('message', get_phrase('duplicate_email'));
            }
            redirect(base_url() . 'receptionist/facture');
            }

        if ($task == "update") {
            $this->crud_model->update_facture_info($facture_id);
            $this->session->set_flashdata('message', get_phrase('informations-a-été-mise-à-jour-avec-succès'));
            redirect(base_url() . 'receptionist/facture');
            }

        if ($task == "delete") {
            $this->crud_model->delete_facture_info($facture_id);
            redirect(base_url() . 'receptionist/facture');
            }

            // Charger la vue partielle pour afficher les données du patient
            $data['facture_info'] = $this->crud_model->select_facture_info();
            $data['page_name'] = 'manage_facture';
            $data['page_title'] = get_phrase('facture');
            $this->load->view('backend/index', $data);
                }

            function get_facture_by_patient_id($patient_id) {
            $this->db->where('patient_id', $patient_id);
            $query = $this->db->get('facture'); // Remplacez 'facture' par le nom de votre table
            return $query->row(); // Retourne la première ligne trouvée ou null si aucune ligne trouvée
            }


        function get_patient_details() {
            if ($this->session->userdata('receptionist_login') != 1) {
                http_response_code(403);
                echo '<p class="reception-empty">Session expirée, veuillez vous reconnecter.</p>';
                exit;
            }

            $patient_id = $this->input->post('patient_id');
            if (empty($patient_id)) {
                echo '';
                exit;
            }

            // Chaque bloc est protégé individuellement : si une colonne a été
            // renommée/supprimée (ex: modification récente des tables
            // invoice / examen), le reste du détail patient continue à
            // s'afficher au lieu de faire échouer toute la requête AJAX.
            // NB : db_debug est temporairement désactivé le temps de ces
            // requêtes "à risque" car CodeIgniter interrompt sinon tout le
            // script (show_error) au lieu de simplement renvoyer FALSE.
            $output = '';
            $previous_db_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;

            try {
                $this->db->select('invoice_entries');
                $this->db->where('patient_id', $patient_id);
                $query = $this->db->get('invoice');
                $invoices = $query ? $query->result() : array();

                if (!empty($invoices)) {
                    $output .= '<h4>Consultations</h4>';
                    foreach ($invoices as $invoice) {
                        $entries = json_decode($invoice->invoice_entries, true);
                        if (is_array($entries)) {
                            foreach ($entries as $entry) {
                                $description = isset($entry['description']) ? $entry['description'] : '';
                                $amount      = isset($entry['amount']) ? $entry['amount'] : 0;
                                $qte         = isset($entry['qte_consult']) ? $entry['qte_consult'] : 1;
                                $net         = isset($entry['net_amount']) ? $entry['net_amount'] : $amount;
                                if ($description === '') continue;
                                $output .= '<p>' . htmlspecialchars($description) . ' : ' . htmlspecialchars($amount) . ' FCFA ' . ' ; ' . htmlspecialchars($qte) . ' ; ' . htmlspecialchars($net) . ' FCFA</p>';
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'get_patient_details() - invoice: ' . $e->getMessage());
            }

            try {
                $this->db->select('examen_entries');
                $this->db->where('patient_id', $patient_id);
                $query = $this->db->get('examen');
                $examens = $query ? $query->result() : array();

                if (!empty($examens)) {
                    $output .= '<h4>Examens</h4>';
                    foreach ($examens as $examen) {
                        $entries = json_decode($examen->examen_entries, true);
                        if (is_array($entries)) {
                            foreach ($entries as $entry) {
                                $description = isset($entry['description']) ? $entry['description'] : '';
                                $amount      = isset($entry['montant']) ? $entry['montant'] : 0;
                                $qte         = isset($entry['qte_examen']) ? $entry['qte_examen'] : 1;
                                $total       = isset($entry['amountT']) ? $entry['amountT'] : $amount;
                                if ($description === '') continue;
                                $output .= '<p>' . htmlspecialchars($description) . ' : ' . htmlspecialchars($amount) . ' FCFA ' . ' ; ' . htmlspecialchars($qte) . ' ; ' . htmlspecialchars($total) . ' FCFA</p>';
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'get_patient_details() - examen: ' . $e->getMessage());
            }

            try {
                $this->db->select('traitement_entries');
                $this->db->where('patient_id', $patient_id);
                $query = $this->db->get('traitement');
                $traitements = $query ? $query->result() : array();

                if (!empty($traitements)) {
                    $output .= '<h4>Traitements</h4>';
                    foreach ($traitements as $traitement) {
                        $entries = json_decode($traitement->traitement_entries, true);
                        if (is_array($entries)) {
                            foreach ($entries as $entry) {
                                $description = isset($entry['description']) ? $entry['description'] : '';
                                $amount      = isset($entry['amount']) ? $entry['amount'] : 0;
                                $qte         = isset($entry['qte']) ? $entry['qte'] : 1;
                                $total       = isset($entry['prixTrait']) ? $entry['prixTrait'] : $amount;
                                if ($description === '') continue;
                                $output .= '<p>' . htmlspecialchars($description) . ' : ' . htmlspecialchars($amount) . ' FCFA ' . ' ; ' . htmlspecialchars($qte) . ' ; ' . htmlspecialchars($total) . ' FCFA</p>';
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'get_patient_details() - traitement: ' . $e->getMessage());
            }

            try {
                $this->db->select('invoice_ventes');
                $this->db->where('patient_id', $patient_id);
                $query = $this->db->get('vente');
                $ventes = $query ? $query->result() : array();

                if (!empty($ventes)) {
                    $output .= '<h4>Médicaments</h4>';
                    foreach ($ventes as $vente) {
                        $entries = json_decode($vente->invoice_ventes, true);
                        if (is_array($entries)) {
                            foreach ($entries as $entry) {
                                if (empty($entry['produit'])) continue;
                                $this->db->select('name');
                                $this->db->where('medicine_id', $entry['produit']);
                                $medicine = $this->db->get('medicine')->row();
                                $name = !empty($medicine) ? $medicine->name : 'Médicament non trouvé';
                                $amount = isset($entry['amount']) ? $entry['amount'] : 0;
                                $qte    = isset($entry['qte_vente']) ? $entry['qte_vente'] : 1;
                                $total  = isset($entry['Ptotal']) ? $entry['Ptotal'] : $amount;
                                $output .= '<p>' . htmlspecialchars($name) . ' : ' . htmlspecialchars($amount) . ' FCFA ' . ' ; ' . htmlspecialchars($qte) . ' ; ' . htmlspecialchars($total) . ' FCFA</p>';
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'get_patient_details() - vente: ' . $e->getMessage());
            }

            if ($output === '') {
                $output = '<p class="reception-empty">Aucune consultation, examen, traitement ou vente enregistré pour ce patient.</p>';
            }

            $this->db->db_debug = $previous_db_debug;

            echo $output;
        }


        function medicine($task = "", $medicine_id = "")
            {
            if ($this->session->userdata('receptionist_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_medicine_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'receptionist/medicine');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_medicine_info($medicine_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'receptionist/medicine');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_medicine_info($medicine_id);
                redirect(base_url() .'receptionist/medicine');
            }
            
            $data['medicine_info']  = $this->crud_model->select_medicine_info();
            $data['page_name']      = 'manage_medicine';
            $data['page_title']     = get_phrase('medicine');
            $this->load->view('backend/index', $data);
        }

        //........................Debut partie stock...........................

        function stock_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('receptionist_login') != 1) {
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
            if ($this->session->userdata('receptionist_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_stock_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'receptionist/stock');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_stock_info($stock_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'receptionist/stock');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_stock_info($stock_id);
                redirect(base_url() .'receptionist/stock');
            }
            
            $data['stock_info']  = $this->crud_model->select_stock_info();
            $data['page_name']      = 'manage_stock';
            $data['page_title']     = get_phrase('stock');
            $this->load->view('backend/index', $data);
        }
    //.........................Fin partie stock..........................

    //.........................Debut partie vente........................
        function vente_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('receptionist_login') != 1) {
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
            if ($this->session->userdata('receptionist_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_vente_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'receptionist/vente');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_vente_info($vente_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'receptionist/vente');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_vente_info($vente_id);
                redirect(base_url() .'receptionist/vente');
            }
            
            $data['vente_info']  = $this->crud_model->select_vente_info();
            $data['page_name']      = 'manage_vente';
            $data['page_title']     = get_phrase('vente');
            $this->load->view('backend/index', $data);
        }
        //.........................Fin partie vente....................

        //.........................Debut partie traitement.................
        function traitement_crud($task = "" , $param2 = ""){
            if ($this->session->userdata('receptionist_login') != 1) {
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
            if ($this->session->userdata('receptionist_login') != 1)
            {
                $this->session->set_userdata('last_page' , current_url());
                redirect(base_url(), 'refresh');
            }
                    
            if ($task == "create")
            {
                $this->crud_model->save_traitement_info();
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_enregistré_avec_succés'));
                redirect(base_url() .'receptionist/traitement');
            }
            
            if ($task == "update")
            {
                $this->crud_model->update_traitement_info($traitement_id);
                $this->session->set_flashdata('message' , get_phrase('informations_a_été_mis_à_jours_avec_succés'));
                redirect(base_url() .'receptionist/traitement');
            }
            
            if ($task == "delete")
            {
                $this->crud_model->delete_traitement_info($traitement_id);
                redirect(base_url() .'receptionist/traitement');
            }
            
            $data['traitement_info']  = $this->crud_model->select_traitement_info();
            $data['page_name']      = 'manage_traitement';
            $data['page_title']     = get_phrase('traitement');
            $this->load->view('backend/index', $data);
        }
        //.........................Fin partie traitement....................

        function profile($task = "") {
            if ($this->session->userdata('receptionist_login') != 1) {
                $this->session->set_userdata('last_page', current_url());
                redirect(base_url(), 'refresh');
            }

            $receptionist_id = $this->session->userdata('login_user_id');
            if ($task == "update") {
                    $this->crud_model->update_receptionist_info($receptionist_id);
                    $this->session->set_flashdata('message', get_phrase('informations de profil a été mises à jour avec succès'));
                    redirect(base_url() . 'receptionist/profile');
            }

            if ($task == "change_password") {
                $password = $this->db->get_where('receptionist', array('receptionist_id' => $receptionist_id))->row()->password;
                $old_password = sha1($this->input->post('old_password'));
                $new_password = $this->input->post('new_password');
                $confirm_new_password = $this->input->post('confirm_new_password');

                if ($password == $old_password && $new_password == $confirm_new_password) {
                    $data['password'] = sha1($new_password);
                    $this->db->where('receptionist_id', $receptionist_id);
                    $this->db->update('receptionist', $data);

                    $this->session->set_flashdata('message', get_phrase('mot-de-passe-a-été-mis-à-jour-avec-succés'));
                    redirect(base_url() . 'receptionist/profile');
                } else {
                    $this->session->set_flashdata('message', get_phrase('password_update_failed'));
                    redirect(base_url() . 'receptionist/profile');
                }
            }

        $data['page_name'] = 'edit_profile';
        $data['page_title'] = get_phrase('profil');
        $this->load->view('backend/index', $data);
    }

    function form($task = "") {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        $data['page_name'] = 'form_create';
        $data['page_title'] = get_phrase('create_form');
        $this->load->view('backend/index', $data);
    }

    function get_form_element($element_type) {
        if ($this->session->userdata('receptionist_login') != 1) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }

        echo $html = $this->db->get_where('form_element', array('type' => $element_type))->row()->html;
        //$this->load->view('backend/accountant/form_create_body', $html);
        //echo $element_type;
    }

}
