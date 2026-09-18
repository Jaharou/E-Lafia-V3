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
        $page_data['lab_dashboard'] = $this->_get_dashboard_data();
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Données du tableau de bord laboratoire, centralisées ici plutôt que
     * dispersées dans la vue (même approche que Receptionist).
     *
     * Les compteurs passent par UNE requête d'agrégation : l'ancienne vue
     * chargeait toutes les lignes `examen` en PHP pour compter les entrées
     * JSON, ce qui devenait lent à mesure que la table grossissait.
     *
     * NB : les comparaisons de dates sont faites par MySQL (CURDATE) sur la
     * colonne DATETIME `creation_time`. L'ancienne vue utilisait
     * STR_TO_DATE(creation_time, '%d-%b-%Y'), hérité de l'époque où la
     * colonne était du texte : depuis la migration en DATETIME, cette
     * conversion échouait systématiquement et le compteur du jour affichait
     * toujours 0.
     */
    private function _get_dashboard_data()
    {
        $stats = $this->db->query("
            SELECT
                COUNT(*) AS examens_total,
                SUM(DATE(creation_time) = CURDATE()) AS examens_jour,
                SUM(CASE WHEN JSON_VALID(examen_entries)
                         THEN JSON_LENGTH(examen_entries) ELSE 0 END) AS analyses_total,
                SUM(CASE WHEN DATE(creation_time) = CURDATE() AND JSON_VALID(examen_entries)
                         THEN JSON_LENGTH(examen_entries) ELSE 0 END) AS analyses_jour,
                SUM(CASE WHEN DATE(creation_time) = CURDATE()
                         THEN total_amount ELSE 0 END) AS recette_jour,
                SUM(CASE WHEN YEAR(creation_time) = YEAR(CURDATE())
                          AND MONTH(creation_time) = MONTH(CURDATE())
                         THEN total_amount ELSE 0 END) AS recette_mois,
                SUM(statut_examen IN ('En instance', 'En cours', 'En attente', 'Pending')) AS en_attente
            FROM examen
        ")->row_array();

        $dashboard = array(
            'patients_total'  => (int) $this->db->count_all('patient'),
            'examens_total'   => (int) $stats['examens_total'],
            'examens_jour'    => (int) $stats['examens_jour'],
            'analyses_total'  => (int) $stats['analyses_total'],
            'analyses_jour'   => (int) $stats['analyses_jour'],
            'recette_jour'    => (float) $stats['recette_jour'],
            'recette_mois'    => (float) $stats['recette_mois'],
            'en_attente'      => (int) $stats['en_attente'],
            'donneurs_total'  => (int) $this->db->count_all('blood_donor'),
            'examens_attente' => array(),
            'examens_recents' => array(),
            'patients_recents'=> array(),
            'tendance'        => array(),
        );

        $dashboard['examens_attente'] = $this->db
            ->select('examen.id_examen, examen.examen_number, examen.creation_time, examen.statut_examen, patient.name, patient.prenom')
            ->from('examen')
            ->join('patient', 'patient.patient_id = examen.patient_id', 'left')
            ->where_in('examen.statut_examen', array('En instance', 'En cours', 'En attente', 'Pending'))
            ->order_by('examen.id_examen', 'DESC')
            ->limit(6)
            ->get()
            ->result_array();

        $dashboard['examens_recents'] = $this->db
            ->select('examen.id_examen, examen.examen_number, examen.examen_entries, examen.blood_examen, examen.statut_examen, examen.creation_time, examen.total_amount, patient.name, patient.prenom')
            ->from('examen')
            ->join('patient', 'patient.patient_id = examen.patient_id', 'left')
            ->order_by('examen.id_examen', 'DESC')
            ->limit(8)
            ->get()
            ->result_array();

        $dashboard['patients_recents'] = $this->db
            ->select('patient_id, name, prenom, phone, sex, age')
            ->from('patient')
            ->order_by('patient_id', 'DESC')
            ->limit(6)
            ->get()
            ->result_array();

        // Tendance sur 14 jours. Les journées sans examen sont complétées à
        // zéro côté PHP, sinon le graphique saute ces dates.
        $rows = $this->db->query("
            SELECT DATE(creation_time) AS jour,
                   COUNT(*) AS nb_examens,
                   SUM(total_amount) AS recette
            FROM examen
            WHERE creation_time >= (CURDATE() - INTERVAL 13 DAY)
              AND creation_time <  (CURDATE() + INTERVAL 1 DAY)
            GROUP BY DATE(creation_time)
        ")->result_array();

        $map = array();
        foreach ($rows as $r) {
            $map[$r['jour']] = $r;
        }
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i day"));
            $dashboard['tendance'][] = array(
                'jour'    => date('d/m', strtotime($d)),
                'examens' => isset($map[$d]) ? (int) $map[$d]['nb_examens'] : 0,
                'recette' => isset($map[$d]) ? (float) $map[$d]['recette'] : 0,
            );
        }

        return $dashboard;
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

    /**
     * Endpoint AJAX (POST) — liste des patients au format DataTables server-side.
     * Recherche et pagination sont effectuées en base, pas en PHP/JS, afin de
     * rester rapide même avec plusieurs milliers de patients.
     *
     * Route par défaut CodeIgniter : laboratorist/patient_datatable
     */
    function patient_datatable()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('laboratorist_login') != 1) {
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
                    'edit_url'    => base_url() . 'laboratorist/patient_crud/edit/' . $row['patient_id'],
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
     * utilisés dans les formulaires d'examen, de vente, de traitement, etc.
     * Répond au format attendu par Select2 :
     *   { results: [{id, text, phone, age, sex}], pagination: {more: bool} }
     *
     * Paramètres GET : q (terme recherché), page (1-based, optionnel)
     */
    function patient_search()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->session->userdata('laboratorist_login') != 1) {
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

        if ($this->session->userdata('laboratorist_login') != 1) {
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
