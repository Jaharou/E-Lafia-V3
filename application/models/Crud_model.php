<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function clear_cache() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    /**
     * Convertit une date saisie dans divers formats (ISO, d-m-Y H:i:s, d/m/Y, m/d/Y, ...)
     * en format DATETIME MySQL ('Y-m-d H:i:s'). Utilisé pour alimenter les nouvelles
     * colonnes datetime (creation_datetime, creation_time) à partir de champs qui,
     * historiquement, envoyaient une date en texte libre.
     *
     * @param string $raw          Valeur brute reçue du formulaire.
     * @param bool   $default_now  Si true et que $raw est vide/invalide, retourne la date/heure courante
     *                             (utile pour une colonne NOT NULL). Sinon retourne null.
     * @return string|null
     */
    private function parse_date_to_sql($raw, $default_now = false)
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return $default_now ? date('Y-m-d H:i:s') : null;
        }

        $formats = array('Y-m-d', 'Y-m-d H:i:s', 'd-m-Y H:i:s', 'd-m-Y', 'm/d/Y', 'd/m/Y');
        foreach ($formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $raw);
            if ($dt instanceof DateTime) {
                return $dt->format('Y-m-d H:i:s');
            }
        }

        $ts = strtotime($raw);
        if ($ts !== false) {
            return date('Y-m-d H:i:s', $ts);
        }

        return $default_now ? date('Y-m-d H:i:s') : null;
    }

    function get_type_name_by_id($type, $type_id = '', $field = 'name') {
        $this->db->where($type . '_id', $type_id);
        $query = $this->db->get($type);
        $result = $query->result_array();
        foreach ($result as $row)
            return $row[$field];
        //return	$this->db->get_where($type,array($type.'_id'=>$type_id))->row()->$field;	
    }
    function system_info($info = ''){
        return $this->db->get_where('settings', array('type' => $info))->row()->description;
    }

    public function check_recaptcha()
    {
        if (isset($_POST["g-recaptcha-response"])) {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => get_frontend_settings('recaptcha_secretkey'),
                'response' => $_POST["g-recaptcha-response"]
            );
            $query = http_build_query($data);
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                        "Content-Length: " . strlen($query) . "\r\n" .
                        "User-Agent:MyAgent/1.0\r\n",
                    'method' => 'POST',
                    'content' => $query
                )
            );
            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captcha_success = json_decode($verify);
            if ($captcha_success->success == false) {
                return false;
            } else if ($captcha_success->success == true) {
                return true;
            }
        } else {
            return false;
        }
    }
    // Create a new invoice.
    function create_invoice() 
    {
        //die(var_dump($this->input->post('invoice_number')));
        $data['title']              = $this->input->post('title');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['invoice_number']     = $this->input->post('invoice_number');
        $data['patient_id']         = $this->input->post('patient_id');
        $creation_raw               = $this->input->post('creation_datetime');
        if (empty($creation_raw)) {
            $creation_raw           = $this->input->post('creation_timestamp'); // certains formulaires (comptable) utilisent encore ce nom de champ
        }
        $data['creation_datetime']  = $this->parse_date_to_sql($creation_raw, true);
        $data['due_timestamp']      = $this->input->post('due_timestamp');
        $data['discount_amount']    = $this->input->post('discount_amount');
        /*$data['avance']    = $this->input->post('avance'); 
        $data['reste_payer']    = $this->input->post('reste_payer');*/
        $data['status']             = $this->input->post('status');
        $data['prise_en_charge']      = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']  = $this->input->post('pourcentage_prise');
        $data['mode_paiement']          = $this->input->post('mode_paiement');
        $data['note']            = $this->input->post('note');

        $invoice_entries            = array();
        $descriptions               = $this->input->post('entry_description');
        $amounts                    = $this->input->post('entry_amount');
        $consults                    = $this->input->post('qte_consult');
        $nets                    = $this->input->post('net_amount');
        $number_of_entries          = sizeof($descriptions);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" && $amounts[$i] != "" || $consults[$i] != "" || $nets[$i] != "")
            {
                $new_entry          = array('description' => $descriptions[$i], 'amount' => $amounts[$i], 'qte_consult' => $consults[$i], 'net_amount' => $nets[$i]);
                array_push($invoice_entries, $new_entry);
            }
        }
        $data['invoice_entries']    = json_encode($invoice_entries);

        // Nouveau champ total_amount : pré-calculé à partir des entrées (net_amount), pour éviter de refaire la boucle à chaque affichage
        $total_amount = 0;
        foreach ($invoice_entries as $entry) {
            $total_amount += floatval($entry['net_amount']);
        }
        $data['total_amount'] = round($total_amount, 2);
        $data['net_amount']   = round($total_amount, 2);

        $this->db->insert('invoice', $data);
    }
    
    function select_invoice_info()
    {
        
        return $this->db->query("SELECT * FROM invoice  ORDER BY invoice_id DESC LIMIT 11500")->result_array();
    }
    
    function select_invoice_info_by_patient_id()
    {
        $patient_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('invoice', array('patient_id' => $patient_id))->result_array();
    }

    function update_invoice($invoice_id)
    {
        $data['title']              = $this->input->post('title');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['invoice_number']     = $this->input->post('invoice_number');
        $data['patient_id']         = $this->input->post('patient_id');
        $creation_raw               = $this->input->post('creation_datetime');
        if (empty($creation_raw)) {
            $creation_raw           = $this->input->post('creation_timestamp');
        }
        $data['creation_datetime']  = $this->parse_date_to_sql($creation_raw, true);
        $data['due_timestamp']      = $this->input->post('due_timestamp');
        $data['discount_amount']    = $this->input->post('discount_amount');
        /*$data['avance']             = $this->input->post('avance');
        $data['reste_payer']        = $this->input->post('reste_payer');*/
        $data['status']             = $this->input->post('status');
        $data['prise_en_charge']    = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']  = $this->input->post('pourcentage_prise');
        $data['mode_paiement']      = $this->input->post('mode_paiement');
        $data['note']               = $this->input->post('note');

        $invoice_entries            = array();
        $descriptions               = $this->input->post('entry_description');
        $amounts                    = $this->input->post('entry_amount');
        $consults                    = $this->input->post('qte_consult');
        $nets                    = $this->input->post('net_amount');
        $number_of_entries          = sizeof($descriptions);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" || $amounts[$i] != "" || $consults[$i] != "" || $nets[$i] != "")
            {
                $new_entry          = array('description' => $descriptions[$i], 'amount' => $amounts[$i], 'qte_consult' => $consults[$i], 'net_amount' => $nets[$i]);
                array_push($invoice_entries, $new_entry);
            }
        }
        $data['invoice_entries']    = json_encode($invoice_entries);

        // Nouveau champ total_amount : pré-calculé à partir des entrées (net_amount)
        $total_amount = 0;
        foreach ($invoice_entries as $entry) {
            $total_amount += floatval($entry['net_amount']);
        }
        $data['total_amount'] = round($total_amount, 2);
        $data['net_amount']   = round($total_amount, 2);

        $this->db->where('invoice_id', $invoice_id);
        $this->db->update('invoice', $data);
    }

    function delete_invoice($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete('invoice');
    }

    function calculate_total_amount($invoice_number)
    {
        // Utilise désormais la colonne pré-calculée total_amount (repli sur net_amount si non renseignée)
        $total_amount           = 0;
        $invoice                = $this->db->get_where('invoice', array('invoice_number' => $invoice_number))->result_array();
        foreach ($invoice as $row)
        {
            $total_amount  += ($row['total_amount'] > 0) ? floatval($row['total_amount']) : floatval($row['net_amount']);
        }
        return $total_amount;
    }

    function calculate_total_prise($invoice_number)
    {
        $total_amount           = 0;
        $invoice                = $this->db->get_where('invoice', array('invoice_number' => $invoice_number))->result_array();
        foreach ($invoice as $row)
        {
            $invoice_entries    = json_decode($row['invoice_entries']);
            foreach ($invoice_entries as $invoice_entry)
                $total_amount  += intval($invoice_entry->amount);
        $prise_charge      = $total_amount * $row['pourcentage_prise'] / 100;
        }
        return $prise_charge;
    }

    function calculate_invoice_total_remise_amount($invoice_number)
    {
        $total_amount           = 0;
        $invoice                = $this->db->get_where('invoice', array('invoice_number' => $invoice_number))->result_array();
        foreach ($invoice as $row)
        {
            $invoice_entries    = json_decode($row['invoice_entries']);
            foreach ($invoice_entries as $invoice_entry)
                $total_amount  += intval($invoice_entry->amount);
            $remise_amount    = $total_amount * $row['discount_amount'] / 100;
            
        }
        return $remise_amount;
    }

  function calculate_invoice_total_amount($invoice_number)
    {
        // Utilise désormais la colonne pré-calculée total_amount (repli sur net_amount si non renseignée)
        $total_amount           = 0;
        $invoice                = $this->db->get_where('invoice', array('invoice_number' => $invoice_number))->result_array();
        foreach ($invoice as $row)
        {
            $total_amount  += ($row['total_amount'] > 0) ? floatval($row['total_amount']) : floatval($row['net_amount']);
            $remise_amount    = $total_amount * $row['discount_amount'] / 100;
            $prise_charge   = $total_amount * $row['pourcentage_prise'] / 100;
            $grand_total        = $total_amount - $remise_amount - $prise_charge;
        }

        return $grand_total;
    }

    //......................Fin partie invoice.......................

    //......................Debut partie examen.......................

    function save_examen_info()
    {
        $data['examen_number']      = $this->input->post('examen_number');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['patient_id']         = $this->input->post('patient_id');
        $data['id_test']            = $this->input->post('id_test');
        $data['blood_examen']       = $this->input->post('blood_examen');
        $data['statut_examen']      = $this->input->post('statut_examen');
        // Ancien champ date_examen (texte) -> nouveau champ creation_time (datetime, NOT NULL).
        $data['creation_time']      = $this->parse_date_to_sql($this->input->post('date_examen'), true);
        $data['status']             = ($this->input->post('status'));
        $data['discount_amount']    = $this->input->post('discount_amount');
        $data['prise_en_charge']    = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']  = $this->input->post('pourcentage_prise');
        $data['mode_paiement']      = $this->input->post('mode_paiement');
        $data['note']               = $this->input->post('note');

        $examen_entries             = array();
        $descriptions               = $this->input->post('libelle_examen');
        $resultats                  = $this->input->post('resultat_examen');
        $unites                     = $this->input->post('unite_mesure');
        $intervalles                = $this->input->post('intervalle');
        $amounts                    = $this->input->post('amount_examen');
        $qtes                    = $this->input->post('qte_examen');
        $amountTs                    = $this->input->post('amountT');
        $number_of_entries          = sizeof($descriptions);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" && $resultats[$i] != "" || $unites[$i] != "" || $intervalles[$i] != "" || $amounts[$i] != "" || $qtes[$i] != "" || $amountTs[$i] != "")
            {
                $new_entry          = array('description' => $descriptions[$i], 'resultat' => $resultats[$i], 'unite' => $unites[$i], 'intervalle' => $intervalles[$i], 'montant' => $amounts[$i], 'qte_examen' => $qtes[$i], 'amountT' => $amountTs[$i]);
                array_push($examen_entries, $new_entry);
            }
        }
        $data['examen_entries']    = json_encode($examen_entries);

        // Nouveau champ total_amount : pré-calculé à partir des entrées (amountT)
        $total_amount_examen = 0;
        foreach ($examen_entries as $entry) {
            $total_amount_examen += floatval($entry['amountT']);
        }
        $data['total_amount'] = round($total_amount_examen, 2);

        $this->db->insert('examen',$data);
        }
        
            function select_examen_info()
        { 
            return $this->db->query("SELECT * FROM examen ORDER BY id_examen DESC LIMIT 115000")->result_array();
        }

        function delete_examen_info($id_examen)
            {
        $this->db->where('id_examen',$id_examen);
        $this->db->delete('examen');
            }

         function select_examen_info_by_id( $id_examen = '' )
        {
            return $this->db->get_where('examen', array('id_examen' => $id_examen))->result_array();
        }
        function update_examen_info($id_examen)
        {
        $data['examen_number']          = $this->input->post('examen_number');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['patient_id']                = $this->input->post('patient_id');
        $data['id_test']                      = $this->input->post('id_test');
        $data['blood_examen']            = $this->input->post('blood_examen');
        // Ancien champ date_examen (texte) -> nouveau champ creation_time (datetime, NOT NULL).
        $data['creation_time']      = $this->parse_date_to_sql($this->input->post('date_examen'), true);
        $data['status']             = ($this->input->post('status'));
        $data['discount_amount']    = $this->input->post('discount_amount');
        $data['prise_en_charge']    = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']  = $this->input->post('pourcentage_prise');
        $data['mode_paiement']      = $this->input->post('mode_paiement');
        $data['note']               = $this->input->post('note');

        $examen_entries          = array();
        $descriptions            = $this->input->post('libelle_examen');
        $unites                  = $this->input->post('unite_mesure');
        $resultats               = $this->input->post('resultat_examen');
        $intervalles             = $this->input->post('intervalle');
        $amounts                 = $this->input->post('amount_examen');
        $qtes                 = $this->input->post('qte_examen');
        $amountTs                 = $this->input->post('amountT');
        $number_of_entries       = sizeof($descriptions);
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" || $resultats[$i] != "" || $unites[$i] != "" || $intervalles[$i] != "" || $amounts[$i] != "" || $qtes[$i] != "" || $amountTs[$i] != "" )
            {
                $new_entry          = array('description' => $descriptions[$i], 'unite' => $unites[$i], 'resultat' => $resultats[$i], 'intervalle' => $intervalles[$i], 'montant' => $amounts[$i], 'qte_examen' => $qtes[$i], 'amountT' => $amountTs[$i]);
                array_push($examen_entries, $new_entry);
            }
        }
        $data['examen_entries']    = json_encode($examen_entries);

        // Nouveau champ total_amount : pré-calculé à partir des entrées (amountT)
        $total_amount_examen = 0;
        foreach ($examen_entries as $entry) {
            $total_amount_examen += floatval($entry['amountT']);
        }
        $data['total_amount'] = round($total_amount_examen, 2);

        $this->db->where('id_examen',$id_examen);
        $this->db->update('examen',$data);
    }

    function calculate_total_amount_examen($examen_number)
    {
        // Utilise désormais la colonne pré-calculée total_amount
        $total_amount           = 0;
        $examen                = $this->db->get_where('examen', array('examen_number' => $examen_number))->result_array();
        foreach ($examen as $row)
        {
            $total_amount  += floatval($row['total_amount']);
        }
        return $total_amount;
    }

    function calculate_total_prise_examen($examen_number)
    {
        // Utilise désormais la colonne pré-calculée total_amount
        $total_amount           = 0;
        $examen                = $this->db->get_where('examen', array('examen_number' => $examen_number))->result_array();
        foreach ($examen as $row)
        {
            $total_amount  += floatval($row['total_amount']);
        $prise_charge      = $total_amount * intval($row['pourcentage_prise']) / 100;
        }
        return $prise_charge;
    }

    function calculate_invoice_total_remise_amount_examen($examen_number)
    {
        $total_amount           = 0;
        $examen                = $this->db->get_where('examen', array('examen_number' => $examen_number))->result_array();
        foreach ($examen as $row)
        {
            $examen_entries    = json_decode($row['examen_entries']);
            foreach ($examen_entries as $examen_entry)
                $total_amount  += intval($examen_entry->montant);
            $remise_amount    = $total_amount * intval($row['discount_amount']) / 100;
            
        }
        return $remise_amount;
    }

  function calculate_invoice_total_amount_examen($examen_number)
    {
        // Utilise désormais la colonne pré-calculée total_amount
        $total_amount           = 0;
        $examen                = $this->db->get_where('examen', array('examen_number' => $examen_number))->result_array();
        foreach ($examen as $row)
        {
            $total_amount  += floatval($row['total_amount']);
            $remise_amount    = $total_amount * intval($row['discount_amount']) / 100;
            $prise_charge   = $total_amount * intval($row['pourcentage_prise']) / 100;
            $grand_total        = $total_amount - $remise_amount - $prise_charge;
        }

        return $grand_total;
    }
    //......................Fin partie examen.........................

    function save_facture_info()
    {
        $facture_number = $this->input->post('facture_number');
        $patient_id = $this->input->post('patient_id');
        $remise = $this->input->post('remise');
        $remise_amount = $this->input->post('remise_amount');
        $total_amount = $this->input->post('total_amount');
        $amount_net = $this->input->post('amount_net');
        $status = $this->input->post('status');
        $invoice_entries = $this->input->post('invoice_entries');
        $examen_entries = $this->input->post('examen_entries');
        $traitement_entries = $this->input->post('traitement_entries');
        $invoice_ventes = $this->input->post('invoice_ventes');

        $data = [
            'facture_number' => $facture_number,
            'patient_id' => $patient_id,
            'date_facture' => date('d-m-Y H:i:s'),
            'remise' => $remise,
            'remise_amount' => $remise_amount,
            'total_amount' => $total_amount,
            'amount_net' => $amount_net,
            'status' => $status,
            'invoice_entries' => json_encode($invoice_entries),
            'examen_entries' => json_encode($examen_entries),
            'traitement_entries' => json_encode($traitement_entries),
            'invoice_ventes' => json_encode($invoice_ventes),
            ];

            $this->db->insert('facture',$data);

            // Vérifiez si l'insertion a réussi
            if ($this->db->affected_rows() > 0) {
            echo "Données insérées avec succès.";
                } else {
            echo "Erreur lors de l'insertion des données.";
                }
            if (!$this->db->affected_rows()) {
            echo $this->db->_error_message(); // Affiche l'erreur SQL
                }
                }
        
        function select_facture_info()
            { 
            return $this->db->query("SELECT * FROM facture ORDER BY facture_id DESC")->result_array();
            }
        function select_facture_info_by_id( $facture_id = '' )
            {
            return $this->db->get_where('facture', array('facture_id' => $facture_id))->result_array();
            }

        function get_facture_by_patient_id($patient_id) {
            $this->db->where('patient_id', $patient_id);
            $query = $this->db->get('facture'); // Remplacez 'facture' par le nom de votre table
            return $query->row(); // Retourne la première ligne trouvée ou null si aucune ligne trouvée
            }

        function delete_facture_info($facture_id) {
            $this->db->where('facture_id',$facture_id);
            $this->db->delete('facture');
            }

        function update_facture_info($facture_id) {
            $facture_number = $this->input->post('facture_number');
            $patient_id = $this->input->post('patient_id');
            $remise = $this->input->post('remise');
            $remise_amount = $this->input->post('remise_amount');
            $total_amount = $this->input->post('total_amount');
            $amount_net = $this->input->post('amount_net');
            $status = $this->input->post('status');
            $invoice_entries = $this->input->post('invoice_entries');
            $examen_entries = $this->input->post('examen_entries');
            $traitement_entries = $this->input->post('traitement_entries');
            $invoice_ventes = $this->input->post('invoice_ventes');

            $data = [
                'facture_number' => $facture_number,
                'patient_id' => $patient_id,
                'date_facture' => date('d-m-Y H:i:s'),
                'remise' => $remise,
                'remise_amount' => $remise_amount,
                'total_amount' => $total_amount,
                'amount_net' => $amount_net,
                'status' => $status,
                'invoice_entries' => json_encode($invoice_entries),
                'examen_entries' => json_encode($examen_entries),
                'traitement_entries' => json_encode($traitement_entries),
                'invoice_ventes' => json_encode($invoice_ventes),
                ];
            $this->db->where('facture_id',$facture_id);
            $this->db->update('facture',$data);
            }

     //......................Debut partie decaissement.......................

    function save_decaissement_info()
    {
        $data['decaissement_number'] = $this->input->post('decaissement_number');
        $data['date_decaissement']  = $this->input->post('date_decaissement');
        $data['libelle_decaiss']    = $this->input->post('libelle_decaiss');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['motif']              = $this->input->post('motif');
        $data['type']               = $this->input->post('type');
        $data['montant']            = $this->input->post('montant');
        $data['mode']               = $this->input->post('mode');
        $data['note']               = $this->input->post('note');
        
        $this->db->insert('decaissement',$data);
    }
    
    function select_decaissement_info()
    {
        return $this->db->get('decaissement')->result_array();
    }
     function select_decaissement_info_by_id( $decaissement_id = '' )
    {
        return $this->db->get_where('decaissement', array('decaissement_id' => $decaissement_id))->result_array();
    }
    function update_decaissement_info($decaissement_id)
    {
        $data['decaissement_number'] = $this->input->post('decaissement_number');
        $data['date_decaissement']  = $this->input->post('date_decaissement');
        $data['libelle_decaiss']    = $this->input->post('libelle_decaiss');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['motif']              = $this->input->post('motif');
        $data['type']               = $this->input->post('type');
        $data['montant']            = $this->input->post('montant');
        $data['mode']               = $this->input->post('mode');
        $data['note']               = $this->input->post('note');
        
        $this->db->where('decaissement_id',$decaissement_id);
        $this->db->update('decaissement',$data);
    }
    
    function delete_decaissement_info($decaissement_id)
    {
        $this->db->where('decaissement_id',$decaissement_id);
        $this->db->delete('decaissement');
    }
    //......................Fin partie decaissement.........................

    //......................Debut partie test.......................

    function save_test_info()
    {
        $data['libelle_examen']       = $this->input->post('libelle_examen');
        $data['categorie_id']           = $this->input->post('categorie_id');
        $data['unite_mesure']         = $this->input->post('unite_mesure');
        $data['amount']          = $this->input->post('amount');
        
        $this->db->insert('test',$data);
    }
    
    function select_test_info()
    {
        return $this->db->get('test')->result_array();
    }
     function select_test_info_by_id( $id_test = '' )
    {
        return $this->db->get_where('test', array('id_test' => $id_test))->result_array();
    }
    function update_test_info($id_test)
    {
        $data['libelle_examen']        = $this->input->post('libelle_examen');
        $data['categorie_id']          = $this->input->post('categorie_id');
        $data['unite_mesure']          = $this->input->post('unite_mesure');
        $data['amount']         = $this->input->post('amount');
        
        $this->db->where('id_test',$id_test);
        $this->db->update('test',$data);
    }
    
    function delete_test_info($id_test)
    {
        $this->db->where('id_test',$id_test);
        $this->db->delete('test');
    }
    //......................Fin partie test.........................

    //......................Debut partie produit.......................

    function save_produit_info()
    {
        $data['libelle_prod'] = $this->input->post('libelle_prod');
        $data['amount_prod'] = $this->input->post('amount_prod');
        
        $this->db->insert('produit',$data);
    }
    
    function select_produit_info()
    {
        return $this->db->get('produit')->result_array();
    }
     function select_produit_info_by_id( $produit_id = '' )
    {
        return $this->db->get_where('produit', array('produit_id' => $produit_id))->result_array();
    }
    function update_produit_info($produit_id)
    {
        $data['libelle_prod'] = $this->input->post('libelle_prod');
        $data['amount_prod'] = $this->input->post('amount_prod');
        
        $this->db->where('produit_id',$produit_id);
        $this->db->update('produit',$data);
    }
    
    function delete_produit_info($produit_id)
    {
        $this->db->where('produit_id',$produit_id);
        $this->db->delete('produit');
    }
    //......................Fin partie produit.........................

    //......................Debut partie stock.......................

    function save_stock_info()
    {
        $data['medicine_id']        = $this->input->post('medicine_id');
        $data['movement_type']      = $this->input->post('movement_type');
        $data['prix']               = $this->input->post('prix');
        $data['qte_stock']          = $this->input->post('qte_stock');
        $data['qte_produit']        = $this->input->post('qte_produit');
        $data['date_liv']           = $this->input->post('date_liv');
        $this->db->insert('stock',$data);
    }
    
    function select_stock_info()
    {
        return $this->db->get('stock')->result_array();
    }
     function select_stock_info_by_id( $stock_id = '' )
    {
        return $this->db->get_where('stock', array('stock_id' => $stock_id))->result_array();
    }
    function update_stock_info($stock_id)
    {
        $data['medicine_id']        = $this->input->post('medicine_id');
        $data['movement_type']      = $this->input->post('movement_type');
        $data['prix']               = $this->input->post('prix');
        $data['qte_stock']          = $this->input->post('qte_stock');
        $data['qte_produit']        = $this->input->post('qte_produit');
        $data['date_liv']           = $this->input->post('date_liv');
        
        $this->db->where('stock_id',$stock_id);
        $this->db->update('stock',$data);
    }
    
    function delete_stock_info($stock_id)
    {
        $this->db->where('stock_id',$stock_id);
        $this->db->delete('stock');
    }
    //......................Fin partie stock.........................

    //......................Debut partie traitement.......................

    function save_traitement_info()
    {
        $data['traitement_number']  = $this->input->post('traitement_number');
        $data['receptionist_id']    = $this->input->post('receptionist_id');
        $data['patient_id']         = $this->input->post('patient_id');
        $data['date_traitement']    = ($this->input->post('date_traitement'));
        $data['status']             = ($this->input->post('status'));
        $data['discount_amount']    = $this->input->post('discount_amount');
        $data['prise_en_charge']    = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']  = $this->input->post('pourcentage_prise');
        $data['mode_paiement']      = $this->input->post('mode_paiement');
        $data['note']               = $this->input->post('note');

        $traitement_entries      = array();
        $traitement_entries      = array();
        $descriptions            = $this->input->post('libelle_prod');
        $amounts                 = $this->input->post('amount_prod');
        $quantities              = $this->input->post('quantite');
        $totals                  = $this->input->post('prixTrait');
        $number_of_entries       = sizeof($descriptions);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" && $amounts[$i] != "" || $quantities[$i] != "" || $totals[$i] != "" )
            {
                $new_entry          = array('description' => $descriptions[$i], 'amount' => $amounts[$i], 'qte' => $quantities[$i], 'prixTrait' => $totals[$i]);
                array_push($traitement_entries, $new_entry);
            }
        }
        $data['traitement_entries']    = json_encode($traitement_entries);
        
        $this->db->insert('traitement',$data);
        }
        
            function select_traitement_info()
        { 
            return $this->db->query("SELECT * FROM traitement ORDER BY traitement_id DESC")->result_array();
        }

        function delete_traitement_info($traitement_id)
            {
        $this->db->where('traitement_id',$traitement_id);
        $this->db->delete('traitement');
            }

         function select_traitement_info_by_id( $traitement_id = '' )
        {
            return $this->db->get_where('traitement', array('traitement_id' => $traitement_id))->result_array();
        }
        function update_traitement_info($traitement_id)
        {
        $data['traitement_number'] = $this->input->post('traitement_number');
        $data['receptionist_id']   = $this->input->post('receptionist_id');
        $data['patient_id']        = $this->input->post('patient_id');
        $data['date_traitement']   = ($this->input->post('date_traitement'));
        $data['status']            = ($this->input->post('status'));
        $data['discount_amount']   = $this->input->post('discount_amount');
        $data['prise_en_charge']   = $this->input->post('prise_en_charge');
        $data['pourcentage_prise'] = $this->input->post('pourcentage_prise');
        $data['mode_paiement']     = $this->input->post('mode_paiement');
        $data['note']              = $this->input->post('note');

        $traitement_entries        = array();
        $descriptions              = $this->input->post('libelle_prod');
        $amounts                   = $this->input->post('amount_prod');
        $quantities                = $this->input->post('quantite');
        $totals                    = $this->input->post('prixTrait');
        $number_of_entries         = sizeof($descriptions);
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" || $amounts[$i] != "" || $quantities[$i] != "" || $totals[$i] != "" )
            {
                $new_entry          = array('description' => $descriptions[$i], 'amount' => $amounts[$i], 'qte' => $quantities[$i], 'prixTrait' => $totals[$i]);
                array_push($traitement_entries, $new_entry);
            }
        }
        $data['traitement_entries']    = json_encode($traitement_entries);

        $this->db->where('traitement_id',$traitement_id);
        $this->db->update('traitement',$data);
    }

    function calculate_total_amount_traitement($traitement_number)
    {
        $total_amount           = 0;
        $traitement                = $this->db->get_where('traitement', array('traitement_number' => $traitement_number))->result_array();
        foreach ($traitement as $row)
        {
            $traitement_entries    = json_decode($row['traitement_entries']);
            foreach ($traitement_entries as $traitement_entry)
                $total_amount  += intval($traitement_entry->amount);
        }
        return $total_amount;
    }

    function calculate_total_prise_traitement($traitement_number)
    {
        $total_amount           = 0;
        $traitement                = $this->db->get_where('traitement', array('traitement_number' => $traitement_number))->result_array();
        foreach ($traitement as $row)
        {
             $traitement_entries    = json_decode($row['traitement_entries']);
            foreach ($traitement_entries as $traitement_entry)
                $total_amount  += intval($traitement_entry->amount);
        $prise_charge      = $total_amount * intval($row['pourcentage_prise']) / 100;
        }
        return $prise_charge;
    }

    function calculate_invoice_total_remise($traitement_number)
    {
        $total_amount           = 0;
        $traitement                = $this->db->get_where('traitement', array('traitement_number' => $traitement_number))->result_array();
        foreach ($traitement as $row)
        {
            $traitement_entries    = json_decode($row['traitement_entries']);
            foreach ($traitement_entries as $traitement_entry)
                $total_amount  += intval($traitement_entry->amount);
            $remise    = $total_amount * intval($row['discount_amount']) / 100;
            
        }
        return $remise;
    }

  function calculate_invoice_total_amount_traitement($traitement_number)
    {
        $total_amount           = 0;
        $traitement                = $this->db->get_where('traitement', array('traitement_number' => $traitement_number))->result_array();
        foreach ($traitement as $row)
        {
            $traitement_entries    = json_decode($row['traitement_entries']);
            foreach ($traitement_entries as $traitement_entry)
                $total_amount  += intval($traitement_entry->amount);
            $remise    = $total_amount * intval($row['discount_amount']) / 100;
            $prise_charge   = $total_amount * intval($row['pourcentage_prise']) / 100;
            $grand_total        = $total_amount - $remise - $prise_charge;
        }

        return $grand_total;
    }
    //......................Fin partie traitement.........................

    //......................Debut partie vente.......................
    function save_vente_info() {
            // Récupérer les données du formulaire
            $data['vente_number']     = $this->input->post('vente_number');
            $data['patient_id']       = $this->input->post('patient_id');
            $data['receptionist_id']  = $this->input->post('receptionist_id');
            $data['date_vente']       = $this->input->post('date_vente');
            $data['discount_amount']  = $this->input->post('discount_amount');
            $data['prise_en_charge']  = $this->input->post('prise_en_charge');
            $data['pourcentage_prise'] = $this->input->post('pourcentage_prise');
            $data['mode_paiement']     = $this->input->post('mode_paiement');
            $data['status']            = $this->input->post('status');

            // Traiter les informations des produits vendus
            $invoice_ventes = array();
            $medicine_ids = $this->input->post('produit');
            $amounts = $this->input->post('amount');
            $quantities = $this->input->post('qte_vente');
            $totals = $this->input->post('Ptotal'); 
            $number_of_entries = sizeof($medicine_ids); 

            // Démarrer une transaction pour assurer l'intégrité des données
            $this->db->trans_start();

            for ($i = 0; $i < $number_of_entries; $i++) {
                if ($medicine_ids[$i] != "" && $amounts[$i] != "" && $quantities[$i] != "" && $totals[$i] != "") {
                    $medicine_id = $medicine_ids[$i];
                    $quantity_requested = $quantities[$i];

                    // Vérifier le stock disponible pour ce produit
                    $this->db->select('qte_produit');
                    $this->db->from('stock');
                    $this->db->where('medicine_id', $medicine_id); // Utilisation de 'medicine_id' pour correspondre aux deux tables
                    $stock = $this->db->get()->row();

                    // Récupérer le produit à partir de l'ID du produit
                    $product = $this->db->get_where('medicine', ['medicine_id' => $medicine_id])->row();

                    // Vérifier si le produit existe
                    if (!$product) {
                        $this->session->set_flashdata('error', "Produit introuvable.");
                        redirect(base_url('pharmacist/vente'));
                        return;
                    }

                    // Récupérer le nom du produit
                    $name = $product->name;

                    // Vérifier la quantité de stock
                    if ($stock && $stock->qte_produit >= $quantity_requested) {
                        // Mise à jour du stock
                        $this->db->set('qte_produit', 'qte_produit - ' . (int)$quantity_requested, FALSE);
                        $this->db->where('medicine_id', $medicine_id);
                        $this->db->update('stock');
                    } else {
                        $this->session->set_flashdata('error', "Le stock pour le produit: $name est insuffisant. Disponible: " . ($stock ? $stock->qte_produit : 0));
                        redirect(base_url('pharmacist/vente'));
                        return;
                    }
                    // Ajouter l'entrée de vente dans l'array JSON si le stock est suffisant
                    $new_entry = array(
                        'produit' => $medicine_id, 
                        'amount' => $amounts[$i], 
                        'qte_vente' => $quantities[$i], 
                        'Ptotal' => $totals[$i]
                    );
                    array_push($invoice_ventes, $new_entry);
                }
            }

            // Encoder les ventes en JSON
            $data['invoice_ventes'] = json_encode($invoice_ventes);

            // Insérer les données de vente dans la table 'vente'
            $insert = $this->db->insert('vente', $data);

            if ($insert) {
                // Mettre à jour le stock pour chaque produit vendu
                for ($i = 0; $i < $number_of_entries; $i++) {
                    if ($medicine_ids[$i] != "" && $quantities[$i] != "") {
                        $medicine_id = $medicine_ids[$i];
                        $quantity_requested = $quantities[$i];

                        // Mettre à jour le stock du produit (soustraction)
                        $this->db->set('qte_produit', 'qte_produit - ' . (int)$quantity_requested, FALSE);
                        $this->db->where('medicine_id', $medicine_id);
                        $this->db->update('stock');

                        // Vérifier si un mouvement de stock existe déjà pour ce produit et ce type de mouvement
                        $this->db->where('medicine_id', $medicine_id);
                        $this->db->where('movement_type', 'sortie');
                        $existing_stock = $this->db->get('stock')->row();

                        if ($existing_stock) {
                            // Mettre à jour le mouvement de stock existant
                            $this->db->set('qte_produit', 'qte_produit + ' . (int)$quantity_requested, FALSE); // Ajouter à la quantité existante
                            date_default_timezone_set('Africa/Niamey');
                            $this->db->set('date_liv', date('d-m-Y H:i:s')); // Mettre à jour la date de mouvement
                            $this->db->where('medicine_id', $medicine_id);
                            $this->db->where('movement_type', 'sortie');
                            $this->db->update('stock');
                        } else {
                            // Insérer un nouveau mouvement de stock de type 'sortie' s'il n'existe pas
                            $stock = array(
                                'medicine_id' => $medicine_id,
                                'movement_type' => 'sortie',
                                'qte_produit' => $quantity_requested,
                                'date_liv' => date('d-m-Y H:i:s')
                            );
                            $this->db->insert('stock', $stock);
                        }
                    }
                }

                // Valider la transaction
                $this->db->trans_complete();

                // Vérifier si la transaction a réussi
                if ($this->db->trans_status() === FALSE) {
                    // La transaction a échoué
                    $this->session->set_flashdata('error', "Erreur lors de l'enregistrement de la vente.");
                } else {
                    // Transaction réussie
                    $this->session->set_flashdata('success', "Vente enregistrée avec succès.");
                }
            } else {
                $this->session->set_flashdata('error', "Erreur lors de l'insertion de la vente.");
            }

            // Redirection vers la page de vente
            redirect(base_url('pharmacist/vente'));
        }
    
        function select_vente_info()
        { 
            return $this->db->query("SELECT * FROM vente ORDER BY vente_id DESC")->result_array();
        }
         function select_vente_info_by_id( $vente_id = '' )
        {
            return $this->db->get_where('vente', array('vente_id' => $vente_id))->result_array();
        }

        function update_vente_info($vente_id)
        {
            $data['vente_number']     = $this->input->post('vente_number');
            $data['patient_id']       = $this->input->post('patient_id');
            $data['receptionist_id']  = $this->input->post('receptionist_id');
            $data['date_vente']       = $this->input->post('date_vente');
            $data['discount_amount']  = $this->input->post('discount_amount');
            $data['prise_en_charge']  = $this->input->post('prise_en_charge');
            $data['pourcentage_prise'] = $this->input->post('pourcentage_prise');
            $data['mode_paiement']    = $this->input->post('mode_paiement');
            $data['status']           = $this->input->post('status');

            // Récupérer les nouveaux produits à ajouter
            $medicine_ids = $this->input->post('medicine_id');
            $amounts      = $this->input->post('amount');
            $quantities   = $this->input->post('quantite');
            $totals       = $this->input->post('Ptotal');
            $number_of_entries = sizeof($medicine_ids);

            // Démarrer une transaction pour garantir la cohérence
            $this->db->trans_start();

            // Récupérer les anciens produits associés à cette vente
            $old_vente = $this->db->get_where('vente', ['vente_id' => $vente_id])->row();
            $existing_invoice_ventes = [];
            if ($old_vente) {
                $existing_invoice_ventes = json_decode($old_vente->invoice_ventes, true) ?: [];
            }

            // Ajouter les nouveaux produits
            for ($i = 0; $i < $number_of_entries; $i++) {
                if (!empty($medicine_ids[$i]) && !empty($quantities[$i]) && !empty($totals[$i])) {
                    $medicine_id = $medicine_ids[$i];
                    $quantity_requested = $quantities[$i];

                    // Vérifier le stock disponible pour ce produit
                    $this->db->select('qte_produit');
                    $this->db->from('stock');
                    $this->db->where('medicine_id', $medicine_id);
                    $stock = $this->db->get()->row();

                    // Récupérer le nom du produit par son ID
                    $this->db->select('name');
                    $this->db->from('medicine');
                    $this->db->where('medicine_id', $medicine_id);
                    $query = $this->db->get();

                    // Vérifier si le produit existe et récupérer son nom
                    if ($query->num_rows() > 0) {
                        $product_name = $query->row()->name;
                    } else {
                        // Si le produit n'est pas trouvé, on peut définir un nom par défaut ou gérer l'erreur
                        $product_name = 'Produit inconnu';
                    }

                    // Vérification des stocks
                    if ($stock && $stock->qte_produit >= $quantity_requested) {
                        // Mettre à jour le stock
                        $this->db->set('qte_produit', 'qte_produit - ' . (int)$quantity_requested, FALSE);
                        $this->db->where('medicine_id', $medicine_id);
                        $this->db->update('stock');
                    } else {
                        // Afficher le nom du produit dans le message d'erreur
                        $this->session->set_flashdata('error', "Stock insuffisant pour le produit : $product_name.");
                        redirect(base_url('pharmacist/vente'));
                        return;
                    }

                    // Ajouter le nouveau produit à la liste
                    $new_entry = array(
                        'produit'   => $medicine_id,
                        'amount'    => $amounts[$i],
                        'qte_vente' => $quantities[$i],
                        'Ptotal'    => $totals[$i]
                    );
                    array_push($existing_invoice_ventes, $new_entry);
                }
            }

            $data['invoice_ventes'] = json_encode($existing_invoice_ventes);

            // Mettre à jour les données de vente
            $this->db->where('vente_id', $vente_id);
            $this->db->update('vente', $data);

            $this->db->trans_complete();

            // Vérifier le statut de la transaction
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', "Erreur lors de la mise à jour de la vente.");
            } else {
                $this->session->set_flashdata('success', "Vente mise à jour avec succès. Nouveaux produits ajoutés.");
            }

            // Redirection
            redirect(base_url('pharmacist/vente'));
        }
 
        function delete_vente_info($vente_id)
        {
            $this->db->where('vente_id',$vente_id);
            $this->db->delete('vente');
        }

        function calculate_prix_total($vente_number)
        {
            $prix_total           = 0;
            $vente                = $this->db->get_where('vente', array('vente_number' => $vente_number))->result_array();
        foreach ($vente as $row)
        {
            $invoice_ventes    = json_decode($row['invoice_ventes']);
            foreach ($invoice_ventes as $vente_entry)
                $prix_total  += intval($vente_entry->Ptotal);
        }

        return $prix_total;
        }

    function calculate_total_prise_vente($vente_number)
        {
        $prix_total           = 0;
        $vente                = $this->db->get_where('vente', array('vente_number' => $vente_number))->result_array();
        foreach ($vente as $row)
        {
            $invoice_ventes    = json_decode($row['invoice_ventes']);
            foreach ($invoice_ventes as $vente_entry)
            $prix_total += intval($vente_entry->Ptotal);
            $prise_charge         = $prix_total * $row['pourcentage_prise'] / 100;
        }
        return $prise_charge;
    }

    function calculate_vente_total_remise_amount($vente_number)
    {
            $prix_total           = 0;
        $vente                = $this->db->get_where('vente', array('vente_number' => $vente_number))->result_array();
        foreach ($vente as $row){
            $invoice_ventes    = json_decode($row['invoice_ventes']);
            foreach ($invoice_ventes as $vente_entry)
            $prix_total += intval($vente_entry->Ptotal);
            $remise_amount     = $prix_total * $row['discount_amount'] / 100; 
        }
        return $remise_amount;
    }

  function calculate_vente_prix_total($vente_number)
    {
        $prix_total           = 0;
        $vente                = $this->db->get_where('vente', array('vente_number' => $vente_number))->result_array();
        foreach ($vente as $row)
        {
            $invoice_ventes    = json_decode($row['invoice_ventes']);
            foreach ($invoice_ventes as $vente_entry)    
            $prix_total += intval($vente_entry->Ptotal);
            $remise_amount     = $prix_total * $row['discount_amount'] / 100;
            $prise_charge     = $prix_total * $row['pourcentage_prise'] / 100;
            $grand_total       = $prix_total - $remise_amount - $prise_charge;
        }
        return $grand_total;
    }
    //......................Fin partie vente...............................

    //......................Debut partie fournisseur.......................

    function save_fournisseur_info()
    {
        $data['name']                    = $this->input->post('name');
        $data['contact']                      = $this->input->post('contact');
        $data['adresse']                      = $this->input->post('adresse');
        $data['email']                      = $this->input->post('email');

        $this->db->insert('fournisseur',$data);
    }
    
    function select_fournisseur_info()
    {
        return $this->db->get('fournisseur')->result_array();
    }
     function select_fournisseur_info_by_id( $fournisseur_id = '' )
    {
        return $this->db->get_where('fournisseur', array('fournisseur_id' => $fournisseur_id))->result_array();
    }
    function update_fournisseur_info($fournisseur_id)
    {
        $data['name']                    = $this->input->post('name');
        $data['contact']                      = $this->input->post('contact');
        $data['adresse']                      = $this->input->post('adresse');
        $data['email']                      = $this->input->post('email');
        
        $this->db->where('fournisseur_id',$fournisseur_id);
        $this->db->update('fournisseur',$data);
    }
    
    function delete_fournisseur_info($fournisseur_id)
    {
        $this->db->where('fournisseur_id',$fournisseur_id);
        $this->db->delete('fournisseur');
    }
    //......................Fin partie stock.........................

  
    //////system settings//////
    function update_system_settings() {
        $data['description'] = $this->input->post('system_name');
        $this->db->where('type', 'system_name');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('system_title');
        $this->db->where('type', 'system_title');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('address');
        $this->db->where('type', 'address');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('phone');
        $this->db->where('type', 'phone');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('paypal_email');
        $this->db->where('type', 'paypal_email');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('currency');
        $this->db->where('type', 'currency');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('system_email');
        $this->db->where('type', 'system_email');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('buyer');
        $this->db->where('type', 'buyer');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('welcome_message');
        $this->db->where('type', 'welcome_message');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('service_short_text');
        $this->db->where('type', 'service_short_text');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('system_name');
        $this->db->where('type', 'system_name');
        $this->db->update('settings', $data);

         $data['description'] = $this->input->post('recaptcha_status');
        $this->db->where('type', 'recaptcha_status');
        $this->db->update('settings', $data);

         $data['description'] = $this->input->post('recaptcha_sitekey');
        $this->db->where('type', 'recaptcha_sitekey');
        $this->db->update('settings', $data);

         $data['description'] = $this->input->post('recaptcha_secretkey');
        $this->db->where('type', 'recaptcha_secretkey');
        $this->db->update('settings', $data);


        $data['description'] = $this->input->post('language');
        $this->db->where('type', 'language');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('facebook');
        $this->db->where('type', 'facebook');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('twitter');
        $this->db->where('type', 'twitter');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('instagram');
        $this->db->where('type', 'instagram');
        $this->db->update('settings', $data);

        $data['description'] = $this->input->post('youtube');
        $this->db->where('type', 'youtube');
        $this->db->update('settings', $data);
       
    }
    
    // SMS settings.
    function update_sms_settings() {
        
        $data['description'] = $this->input->post('clickatell_user');
        $this->db->where('type', 'clickatell_user');
        $this->db->update('settings', $data);
        
        $data['description'] = $this->input->post('clickatell_password');
        $this->db->where('type', 'clickatell_password');
        $this->db->update('settings', $data);
        
        $data['description'] = $this->input->post('clickatell_api_id');
        $this->db->where('type', 'clickatell_api_id');
        $this->db->update('settings', $data);
    }

    /////creates log/////
    function create_log($data) {
        $data['timestamp'] = strtotime(date('Y-m-d') . ' ' . date('H:i:s'));
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
        $location = new SimpleXMLElement(file_get_contents('http://freegeoip.net/xml/' . $_SERVER["REMOTE_ADDR"]));
        $data['location'] = $location->City . ' , ' . $location->CountryName;
        $this->db->insert('log', $data);
    }

    ////////BACKUP RESTORE/////////
    function create_backup($type) {
        $this->load->dbutil();


        $options = array(
            'format' => 'txt', // gzip, zip, txt
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n"               // Newline character used in backup file
        );


        if ($type == 'all') {
            $tables = array('');
            $file_name = 'system_backup';
        } else {
            $tables = array('tables' => array($type));
            $file_name = 'backup_' . $type;
        }

        $backup = & $this->dbutil->backup(array_merge($options, $tables));


        $this->load->helper('download');
        force_download($file_name . '.sql', $backup);
    }

    /////////RESTORE TOTAL DB/ DB TABLE FROM UPLOADED BACKUP SQL FILE//////////
    function restore_backup() {
        move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/backup.sql');
        $this->load->dbutil();


        $prefs = array(
            'filepath' => 'uploads/backup.sql',
            'delete_after_upload' => TRUE,
            'delimiter' => ';'
        );
        $restore = & $this->dbutil->restore($prefs);
        unlink($prefs['filepath']);
    }

    /////////DELETE DATA FROM TABLES///////////////
    function truncate($type) {
        if ($type == 'all') {
            $this->db->truncate('student');
            $this->db->truncate('mark');
            $this->db->truncate('teacher');
            $this->db->truncate('subject');
            $this->db->truncate('class');
            $this->db->truncate('exam');
            $this->db->truncate('grade');
        } else {
            $this->db->truncate($type);
        }
    }

    ////////IMAGE URL//////////
    function get_image_url($type = '', $id = '') {
        if (file_exists('uploads/' . $type . '_image/' . $id . '.jpg'))
            $image_url = base_url() . 'uploads/' . $type . '_image/' . $id . '.jpg';
        else
            $image_url = base_url() . 'uploads/user.jpg';

        return $image_url;
    }
     function select_slider_info()
    {
        return $this->db->get('slider')->result_array();
    }
    function save_slider_info()
    {
        $data['title']       = $this->input->post('title');
        $data['content']    = $this->input->post('content');
        
        $this->db->insert('slider',$data);
         $slider_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/slider/" . $slider_id . '.jpg');
    }
     function delete_slider_info($slider_id)
    {
        $this->db->where('slider_id',$slider_id);
        $this->db->delete('slider');
    }    
     function select_openig_hours_info()
    {
        return $this->db->get('opening_ours')->result_array();
    }
     function save_openig_hours_info()
    {
        $data['open_day']       = $this->input->post('open_day');
        $data['open_time']    = $this->input->post('open_time');
        
        $this->db->insert('opening_ours',$data);
        
    }
     function delete_openig_hours_info($openig_hours_id)
    {
        $this->db->where('id',$openig_hours_id);
        $this->db->delete('opening_ours');
    } 
     function select_services_info()
    {
        return $this->db->get('services')->result_array();
    }
    function select_type_info()
    {
        return $this->db->get('type')->result_array();
    }
     function save_services_info()
    {
        $data['service_title']  = $this->input->post('service_title');
        $data['description']    = $this->input->post('description');
        
        $this->db->insert('services',$data);
        
    }
    function save_designation_info()
    {
        $data['libelle']  = $this->input->post('libelle');
        $data['amount']  = $this->input->post('amount');
        
        $this->db->insert('designation',$data); 
    }
    function select_designation_info()
    {
        return $this->db->get('designation')->result_array();
    }
    function select_designation_info_by_id( $entry_description_id = '' )
    {
        return $this->db->get_where('designation', array('entry_description_id' => $entry_description_id))->result_array();
    }
    function update_designation_info($entry_description_id)
    {
        $data['libelle']        = $this->input->post('libelle');
        $data['amount']         = $this->input->post('amount');
        
        $this->db->where('entry_description_id',$entry_description_id);
        $this->db->update('designation',$data);
    }
    function delete_designation_info($entry_description_id)
    {
        $this->db->where('entry_description_id',$entry_description_id);
        $this->db->delete('designation');
    }
    function save_type_info()
    {
        $data['patient_id']  = $this->input->post('patient_id');
        $data['produit']  = $this->input->post('produit');
        $data['prix']  = $this->input->post('prix');
        $data['nbrs']  = $this->input->post('nbrs');
        $data['total']  = $this->input->post('total');
        
        $this->db->insert('type',$data);
        
    }
     function delete_services_info($services_id)
    {
        $this->db->where('service_id',$services_id);
        $this->db->delete('services');
    } 
    function save_department_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['description']    = $this->input->post('description');
        
        $this->db->insert('department',$data);
         $department_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/department_image/" . $department_id . '.jpg');
    }
    
    function select_department_info()
    {
        return $this->db->get('department')->result_array();
    }
    
    function update_department_info($department_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['description'] 	= $this->input->post('description');
        
        $this->db->where('department_id',$department_id);
        $this->db->update('department',$data);
    }
    
    function delete_department_info($department_id)
    {
        $this->db->where('department_id',$department_id);
        $this->db->delete('department');
    }
    
    function save_doctor_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        $data['department_id'] 	= $this->input->post('department_id');
        $data['profile'] 	= $this->input->post('profile');
        
        $this->db->insert('doctor',$data);
        
        $doctor_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/doctor_image/" . $doctor_id . '.jpg');
    }
    
    function select_doctor_info()
    {
        return $this->db->get('doctor')->result_array();
    }
     function select_doctor_info_by_id( $doctor_id = '' )
    {
        return $this->db->get_where('doctor', array('doctor_id' => $doctor_id))->result_array();
    }
    function update_doctor_info($doctor_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        $data['department_id'] 	= $this->input->post('department_id');
        $data['profile'] 	= $this->input->post('profile');
        if ($this->input->post('password') != '') {
            $data['password']       = sha1($this->input->post('password'));
        }       
        $this->db->where('doctor_id',$doctor_id);
        $this->db->update('doctor',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/doctor_image/" . $doctor_id . '.jpg');
    }
    
    function delete_doctor_info($doctor_id)
    {
        $this->db->where('doctor_id',$doctor_id);
        $this->db->delete('doctor');
    }
    
    function save_patient_info()
    {
        $data['civilite']       = $this->input->post('civilite');
        $data['service_id']         = $this->input->post('service_id');
        $data['name'] 		= $this->input->post('name');
        $data['prenom']       = $this->input->post('prenom');
        $data['email'] 		= $this->input->post('email');
        $data['tel_contacter']      = $this->input->post('tel_contacter');
        $data['address_patient'] 	= $this->input->post('address_patient');
        $data['profession']       = $this->input->post('profession');
        $data['personne_contacter']       = $this->input->post('personne_contacter');
        $data['doctor_id']       = $this->input->post('doctor_id');
        $data['phone']          = $this->input->post('phone');
        $data['sex']            = $this->input->post('sex');
        $data['situation_famil']       = $this->input->post('situation_famil');
        $data['birth_date']     = $this->input->post('birth_date');
        $data['age']            = $this->input->post('age');
        $data['blood_group'] 	= $this->input->post('blood_group');
        
        $this->db->insert('patient',$data);
        
        $patient_id  =   $this->db->insert_id();
        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/patient_image/" . $patient_id . '.jpg');
        }
        return $patient_id;
    }
    function save_patient_info_home()
    {
        $data['civilite']       = $this->input->post('civilite');
        $data['service_id']         = $this->input->post('service_id');
        $data['name']       = $this->input->post('name');
        $data['prenom']       = $this->input->post('prenom');
        $data['email']      = $this->input->post('email');
        $data['tel_contacter']      = $this->input->post('tel_contacter');
        $data['address_patient']    = $this->input->post('address_patient');
        $data['profession']       = $this->input->post('profession');
        $data['personne_contacter']       = $this->input->post('personne_contacter');
        $data['doctor_id']       = $this->input->post('doctor_id');
        $data['phone']          = $this->input->post('phone');
        $data['sex']            = $this->input->post('sex');
        $data['situation_famil']       = $this->input->post('situation_famil');
        $data['birth_date']     = $this->input->post('birth_date');
        $data['age']            = $this->input->post('age');
        $data['blood_group']    = $this->input->post('blood_group');
        //$patient_info = array_reverse($patient_info);
        
        $this->db->insert('patient',$data);
        
        $patient_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/patient_image/" . $patient_id . '.jpg');

        $data_1['timestamp']  = strtotime($this->input->post('date_timestamp').' '.$this->input->post('time_timestamp') );
        $data_1['doctor_id']  = $this->input->post('doctor_id');
        $data_1['patient_id'] = $patient_id ;
        $data_1['status']     = 'pending';
        
        $this->db->insert('appointment',$data_1);
    }
    function select_patient_info()
    {
        $this->db->from('patient');
        $this->db->order_by('patient_id', 'desc');
        return $this->db->get()->result_array();
    }
    
    function select_patient_info_by_patient_id( $patient_id = '' )
    {
        return $this->db->get_where('patient', array('patient_id' => $patient_id))->result_array();
    }
            
    function update_patient_info($patient_id)
    {
       $data['civilite']       = $this->input->post('civilite');
       $data['service_id']         = $this->input->post('service_id');
        $data['name'] 		= $this->input->post('name');
        $data['prenom']       = $this->input->post('prenom');
        $data['email'] 		= $this->input->post('email');
        $data['tel_contacter']      = $this->input->post('tel_contacter');
        $data['address_patient'] 	= $this->input->post('address_patient');
        $data['profession']       = $this->input->post('profession');
        $data['personne_contacter']       = $this->input->post('personne_contacter');
        $data['doctor_id']       = $this->input->post('doctor_id');
        $data['phone']          = $this->input->post('phone');
        $data['sex']            = $this->input->post('sex');
        $data['situation_famil']       = $this->input->post('situation_famil');
        $data['birth_date']     = $this->input->post('birth_date');
        $data['age']            = $this->input->post('age');
        $data['blood_group'] 	= $this->input->post('blood_group'); 
        $this->db->where('patient_id',$patient_id);
        $this->db->update('patient',$data);
        
        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/patient_image/" . $patient_id . '.jpg');
        }
    }
    
    function delete_patient_info($patient_id)
    {
        $this->db->where('patient_id',$patient_id);
        $this->db->delete('patient');
    }
    
    function save_nurse_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->insert('nurse',$data);
        
        $nurse_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/nurse_image/" . $nurse_id . '.jpg');
    }
    
    function select_nurse_info()
    {
        return $this->db->get('nurse')->result_array();
    }
     function select_nurse_info_by_id( $nurse_id = '' )
    {
        return $this->db->get_where('nurse', array('nurse_id' => $nurse_id))->result_array();
    }
    function update_nurse_info($nurse_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        if ($this->input->post('password') != '') {
            $data['password']       = sha1($this->input->post('password'));
        } 
        $this->db->where('nurse_id',$nurse_id);
        $this->db->update('nurse',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/nurse_image/" . $nurse_id . '.jpg');
    }
    
    function delete_nurse_info($nurse_id)
    {
        $this->db->where('nurse_id',$nurse_id);
        $this->db->delete('nurse');
    }

    //............................Debut Magasinier..........................

    function save_magasin_info()
    {
        $data['name']       = $this->input->post('name');
        $data['prenom_magasinier']       = $this->input->post('prenom_magasinier');
        $data['phone_magasinier']       = $this->input->post('phone_magasinier');
        $data['adres_magasinier']    = $this->input->post('adres_magasinier');
        $data['email']      = $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        
        $this->db->insert('magasin',$data);  
    }
    
    function select_magasin_info()
    {
        return $this->db->get('magasin')->result_array();
    }
    function select_magasin_info_by_id( $magasin_id = '' )
    {
        return $this->db->get_where('magasin', array('magasin_id' => $magasin_id))->result_array();
    }
    function update_magasin_info($magasin_id)
    {
        $data['name']       = $this->input->post('name');
        $data['prenom_magasinier']       = $this->input->post('prenom_magasinier');
        $data['phone_magasinier']       = $this->input->post('phone_magasinier');
        $data['adres_magasinier']    = $this->input->post('adres_magasinier');
        $data['email']      = $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        
        $this->db->where('magasin_id',$magasin_id);
        $this->db->update('magasin',$data);
        
    }
    
    function delete_magasin_info($magasin_id)
    {
        $this->db->where('magasin_id',$magasin_id);
        $this->db->delete('magasin');
    }
    //............................Fin Magasinier............................
    
    function save_pharmacist_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->insert('pharmacist',$data);
        
        $pharmacist_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/pharmacist_image/" . $pharmacist_id . '.jpg');
    }
    
    function select_pharmacist_info()
    {
        return $this->db->get('pharmacist')->result_array();
    }
    function select_pharmacist_info_by_id( $pharmacist_id = '' )
    {
        return $this->db->get_where('pharmacist', array('pharmacist_id' => $pharmacist_id))->result_array();
    }
    function update_pharmacist_info($pharmacist_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->where('pharmacist_id',$pharmacist_id);
        $this->db->update('pharmacist',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/pharmacist_image/" . $pharmacist_id . '.jpg');
    }
    
    function delete_pharmacist_info($pharmacist_id)
    {
        $this->db->where('pharmacist_id',$pharmacist_id);
        $this->db->delete('pharmacist');
    }
    
    function save_laboratorist_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->insert('laboratorist',$data);
        
        $laboratorist_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/laboratorist_image/" . $laboratorist_id . '.jpg');
    }
    
    function select_laboratorist_info()
    {
        return $this->db->get('laboratorist')->result_array();
    }
     function select_laboratorist_id_info_by_id( $laboratorist_id = '' )
    {
        return $this->db->get_where('laboratorist', array('laboratorist_id' => $laboratorist_id))->result_array();
    }
    function update_laboratorist_info($laboratorist_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->where('laboratorist_id',$laboratorist_id);
        $this->db->update('laboratorist',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/laboratorist_image/" . $laboratorist_id . '.jpg');
    }
    
    function delete_laboratorist_info($laboratorist_id)
    {
        $this->db->where('laboratorist_id',$laboratorist_id);
        $this->db->delete('laboratorist');
    }
    
    function save_accountant_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->insert('accountant',$data);
        
        $accountant_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/accountant_image/" . $accountant_id . '.jpg');
    }
    
    function select_accountant_info()
    {
        return $this->db->get('accountant')->result_array();
    }
     function select_accountant_id_info_by_id( $accountant_id = '' )
    {
        return $this->db->get_where('accountant', array('accountant_id' => $accountant_id))->result_array();
    }
    function update_accountant_info($accountant_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->where('accountant_id',$accountant_id);
        $this->db->update('accountant',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/accountant_image/" . $accountant_id . '.jpg');
    }
    
    function delete_accountant_info($accountant_id)
    {
        $this->db->where('accountant_id',$accountant_id);
        $this->db->delete('accountant');
    }
    
    function save_receptionist_info()
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['password']       = sha1($this->input->post('password'));
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->insert('receptionist',$data);
        
        $receptionist_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/receptionist_image/" . $receptionist_id . '.jpg');
    }
    
    function select_receptionist_info()
    {
        return $this->db->get('receptionist')->result_array();
    }
      function select_receptionist_info_by_id( $receptionist_id = '' )
    {
        return $this->db->get_where('receptionist', array('receptionist_id' => $receptionist_id))->result_array();
    }
    function update_receptionist_info($receptionist_id)
    {
        $data['name'] 		= $this->input->post('name');
        $data['email'] 		= $this->input->post('email');
        $data['address'] 	= $this->input->post('address');
        $data['phone']          = $this->input->post('phone');
        
        $this->db->where('receptionist_id',$receptionist_id);
        $this->db->update('receptionist',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/receptionist_image/" . $receptionist_id . '.jpg');
    }
    
    function delete_receptionist_info($receptionist_id)
    {
        $this->db->where('receptionist_id',$receptionist_id);
        $this->db->delete('receptionist');
    }
    
    function save_bed_allotment_info()
    {
        $data['bed_id']                 = $this->input->post('bed_id');
        $data['patient_id'] 		    = $this->input->post('patient_id');
        $data['allotment_timestamp'] 	= strtotime($this->input->post('allotment_timestamp'));
        $data['discharge_timestamp']    = strtotime($this->input->post('discharge_timestamp'));
        
        $this->db->insert('bed_allotment',$data);
    }
    
    function select_bed_allotment_info()
    {
        return $this->db->get('bed_allotment')->result_array();
    }
    
    function update_bed_allotment_info($bed_allotment_id)
    {
        $data['bed_id']                 = $this->input->post('bed_id');
        $data['patient_id'] 		= $this->input->post('patient_id');
        $data['allotment_timestamp'] 	= strtotime($this->input->post('allotment_timestamp'));
        $data['discharge_timestamp']    = strtotime($this->input->post('discharge_timestamp'));
        
        $this->db->where('bed_allotment_id',$bed_allotment_id);
        $this->db->update('bed_allotment',$data);
    }
    
    function delete_bed_allotment_info($bed_allotment_id)
    {
        $this->db->where('bed_allotment_id',$bed_allotment_id);
        $this->db->delete('bed_allotment');
    }
    
    function select_blood_bank_info()
    {
        return $this->db->get('blood_bank')->result_array();
    }
    
    function update_blood_bank_info($blood_group_id)
    {
        $data['status']    = $this->input->post('status');
        
        $this->db->where('blood_group_id',$blood_group_id);
        $this->db->update('blood_bank',$data);
    }
    
    function save_report_info()
    {
        $data['type'] 		= $this->input->post('type');
        $data['description']    = $this->input->post('description');
        $data['timestamp']      = strtotime($this->input->post('timestamp'));
        $data['patient_id']     = $this->input->post('patient_id');
        
        $login_type             = $this->session->userdata('login_type');
        if($login_type=='nurse')
            $data['doctor_id']  = $this->input->post('doctor_id');
        else $data['doctor_id'] = $this->session->userdata('login_user_id');
        
        $this->db->insert('report',$data);
    }
    
    function select_report_info()
    {
        return $this->db->get('report')->result_array();
    }
    
    function update_report_info($report_id)
    {
        $data['type'] 		= $this->input->post('type');
        $data['description']    = $this->input->post('description');
        $data['timestamp']      = strtotime($this->input->post('timestamp'));
        $data['patient_id']     = $this->input->post('patient_id');
        
        $login_type             = $this->session->userdata('login_type');
        if($login_type=='nurse')
            $data['doctor_id']  = $this->input->post('doctor_id');
        else $data['doctor_id'] = $this->session->userdata('login_user_id');
        
        $this->db->where('report_id',$report_id);
        $this->db->update('report',$data);
    }
    
    function delete_report_info($report_id)
    {
        $this->db->where('report_id',$report_id);
        $this->db->delete('report');
    }
    
    function save_bed_info()
    {
        $data['bed_number']     = $this->input->post('bed_number');
        $data['type'] 		= $this->input->post('type');
        $data['description']    = $this->input->post('description');
        
        $this->db->insert('bed',$data);
    }
    
    function select_bed_info()
    {
        return $this->db->get('bed')->result_array();
    }
    
    function update_bed_info($bed_id)
    {
        $data['bed_number']     = $this->input->post('bed_number');
        $data['type'] 		= $this->input->post('type');
        $data['description']    = $this->input->post('description');
        
        $this->db->where('bed_id',$bed_id);
        $this->db->update('bed',$data);
    }
    
    function delete_bed_info($bed_id)
    {
        $this->db->where('bed_id',$bed_id);
        $this->db->delete('bed');
    }
    
    function save_blood_donor_info()
    {
        $data['name']                       = $this->input->post('name');
        $data['email']                      = $this->input->post('email');
        $data['address']                    = $this->input->post('address');
        $data['phone']                      = $this->input->post('phone');
        $data['sex']                        = $this->input->post('sex');
        $data['age']                        = $this->input->post('age');
        $data['blood_group']                = $this->input->post('blood_group');
        $data['last_donation_timestamp']    = strtotime($this->input->post('last_donation_timestamp'));
        
        $this->db->insert('blood_donor',$data);
    }
    
    function select_blood_donor_info()
    {
        return $this->db->get('blood_donor')->result_array();
    }
     function select_blood_donor_info_by_id( $blood_donor_id = '' )
    {
        return $this->db->get_where('blood_donor', array('blood_donor_id' => $blood_donor_id))->result_array();
    }
    function update_blood_donor_info($blood_donor_id)
    {
        $data['name']                       = $this->input->post('name');
        $data['email']                      = $this->input->post('email');
        $data['address']                    = $this->input->post('address');
        $data['phone']                      = $this->input->post('phone');
        $data['sex']                        = $this->input->post('sex');
        $data['age']                        = $this->input->post('age');
        $data['blood_group']                = $this->input->post('blood_group');
        $data['last_donation_timestamp']    = strtotime($this->input->post('last_donation_timestamp'));
        
        $this->db->where('blood_donor_id',$blood_donor_id);
        $this->db->update('blood_donor',$data);
    }
    
    function delete_blood_donor_info($blood_donor_id)
    {
        $this->db->where('blood_donor_id',$blood_donor_id);
        $this->db->delete('blood_donor');
    }
    
    function save_medicine_category_info()
    {
        $data['name_categorie'] 		= $this->input->post('name_categorie');
        $data['description']    = $this->input->post('description');
        
        $this->db->insert('medicine_category',$data);
    }
    
    function select_medicine_category_info()
    {
        return $this->db->get('medicine_category')->result_array();
    }
    
    function update_medicine_category_info($medicine_category_id)
    {
        $data['name_categorie'] 		= $this->input->post('name_categorie');
        $data['description'] 	= $this->input->post('description');
        
        $this->db->where('medicine_category_id',$medicine_category_id);
        $this->db->update('medicine_category',$data);
    }
    
    function delete_medicine_category_info($medicine_category_id)
    {
        $this->db->where('medicine_category_id',$medicine_category_id);
        $this->db->delete('medicine_category');
    }
    
    function save_medicine_info()
    {
        $data['fournisseur_id']    = $this->input->post('fournisseur_id');
        $data['name']              = $this->input->post('name');
        $data['medicine_category'] = $this->input->post('medicine_category');
        $data['dci']               = $this->input->post('dci');
        $data['dosage']            = $this->input->post('dosage');
        $data['condit']            = $this->input->post('condit');
        $data['forme']             = $this->input->post('forme');
        $data['qte_prod']          = $this->input->post('qte_prod');
        $data['price']             = $this->input->post('price');
        $data['status'] 		   = $this->input->post('status');
        $data['date_commande']     = $this->input->post('date_commande');
        $data['amount']            = $this->input->post('amount');
        $data['expiration_date']   = $this->input->post('expiration_date');
        
        $this->db->insert('medicine',$data);
    }
    
    function select_medicine_info()
    {
        return $this->db->get('medicine')->result_array();
    }
    
    function update_medicine_info($medicine_id)
    {
        $data['fournisseur_id']    = $this->input->post('fournisseur_id');
        $data['name']              = $this->input->post('name');
        $data['medicine_category'] = $this->input->post('medicine_category');
        $data['dci']               = $this->input->post('dci');
        $data['dosage']            = $this->input->post('dosage');
        $data['condit']            = $this->input->post('condit');
        $data['forme']             = $this->input->post('forme');
        $data['qte_prod']          = $this->input->post('qte_prod');
        $data['price']             = $this->input->post('price');
        $data['status'] 		   = $this->input->post('status');
        $data['date_commande']     = $this->input->post('date_commande');
        $data['amount']            = $this->input->post('amount');
        $data['expiration_date']   = $this->input->post('expiration_date');
        
        $this->db->where('medicine_id',$medicine_id);
        $this->db->update('medicine',$data);
    }
    
    function delete_medicine_info($medicine_id)
    {
        $this->db->where('medicine_id',$medicine_id);
        $this->db->delete('medicine');
    }
    
    function save_appointment_info()
    {
        $data['date_timestamp']  = $this->input->post('date_timestamp').' '.$this->input->post('time_timestamp');
        $data['status']     = 'approuvé';
        $data['patient_id'] = $this->input->post('patient_id');
        
        if($this->session->userdata('login_type') == 'doctor')
            $data['doctor_id']  = $this->session->userdata('login_user_id');
        else
            $data['doctor_id']  = $this->input->post('doctor_id');
        
        $this->db->insert('appointment',$data);
        
        // Notify patient with sms.
        $notify = $this->input->post('notify');
        if($notify != '') {
            $patient_name   =   $this->db->get_where('patient',
                                array('patient_id' => $data['patient_id']))->row()->name;
            $doctor_name    =   $this->db->get_where('doctor',
                                array('doctor_id' => $data['doctor_id']))->row()->name;
            $date           =   date('l, d F Y', $data['timestamp']);
            $time           =   date('g:i a', $data['timestamp']);
            $message        =   $patient_name . ', you have an appointment with doctor ' . $doctor_name . ' on ' . $date . ' at ' . $time . '.';
            $receiver_phone =   $this->db->get_where('patient',
                                array('patient_id' => $data['patient_id']))->row()->phone;
            
            $this->sms_model->send_sms($message, $receiver_phone);
        }
    }
    
    function save_requested_appointment_info()
    {
        $data['date_timestamp']  = strtotime($this->input->post('date_timestamp').' '.$this->input->post('time_timestamp') );
        $data['doctor_id']  = $this->input->post('doctor_id');
        $data['patient_id'] = $this->session->userdata('login_user_id');
        $data['status']     = 'pending';
        
        $this->db->insert('appointment',$data);
    }
    
    function select_appointment_info_by_doctor_id()
    {
        $doctor_id = $this->session->userdata('login_user_id');
        
        $this->db->order_by('date_timestamp' , 'desc');
        $this->db->where('doctor_id' , $doctor_id);
        $this->db->where('status' , 'approved');
        
        return $this->db->get('appointment')->result_array();
    }
    
    function select_appointment_info_by_patient_id()
    {
        $patient_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('appointment', array('patient_id' => $patient_id, 'status' => 'approved'))->result_array();
    }
    
    function select_appointment_info($doctor_id = '', $start_timestamp = '', $end_timestamp = '')
    {
        $response = array();
        if($doctor_id == 'all') {
            $this->db->order_by('doctor_id', 'asc');
            $this->db->order_by('date_timestamp', 'desc');
            $appointments = $this->db->get_where('appointment', array('status' => 'approved'))->result_array();
            foreach ($appointments as $row) {
                if($row['date_timestamp'] >= $start_timestamp && $row['date_timestamp'] <= $end_timestamp)
                    array_push ($response, $row);
            }
        }
        else {
            $this->db->order_by('date_timestamp', 'desc');
            $appointments = $this->db->get_where('appointment', array('doctor_id' => $doctor_id, 'status' => 'approved'))->result_array();
            foreach ($appointments as $row) {
                if($row['timestamp'] >= $start_timestamp && $row['timestamp'] <= $end_timestamp)
                    array_push ($response, $row);
            }
        }
        return $response;
    }
    
    function select_pending_appointment_info_by_patient_id()
    {
        $patient_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('appointment', array('patient_id' => $patient_id, 'status' => 'pending'))->result_array();
    }
    
    function select_requested_appointment_info_by_doctor_id()
    {
        $doctor_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('appointment', array('doctor_id' => $doctor_id, 'status' => 'pending'))->result_array();
    }
    
    function select_requested_appointment_info()
    {
        $this->db->order_by('doctor_id', 'asc');
        return $this->db->get_where('appointment', array('status' => 'pending'))->result_array();
    }
    
    function select_patient_info_by_doctor_id()
    {
        $doctor_id = $this->session->userdata('login_user_id');
        $this->db->group_by ('patient_id');
        return $this->db->get_where('appointment', array('doctor_id' => $doctor_id, 'status' => 'approved'))->result_array();


           

    }
    
    function select_appointments_between_loggedin_patient_and_doctor()
    {
        $patient_id = $this->session->userdata('login_user_id');
        
        $this->db->group_by('doctor_id');
        return $this->db->get_where('appointment', array('patient_id' => $patient_id, 'status' => 'approved'))->result_array();
    }
    
    function update_appointment_info($appointment_id)
    {
        $data['date_timestamp']  = strtotime($this->input->post('date_timestamp').' '.$this->input->post('time_timestamp') );
        $data['patient_id'] = $this->input->post('patient_id');
        
        $this->db->where('appointment_id',$appointment_id);
        $this->db->update('appointment',$data);
        
        // Notify patient with sms.
        $notify = $this->input->post('notify');
        if($notify != '') {
            $doctor_id      =   $this->session->userdata('login_user_id');
            $patient_name   =   $this->db->get_where('patient',
                                array('patient_id' => $data['patient_id']))->row()->name;
            $doctor_name    =   $this->db->get_where('doctor',
                                array('doctor_id' => $doctor_id))->row()->name;
            $date           =   date('l, d F Y', $data['timestamp']);
            $time           =   date('g:i a', $data['timestamp']);
            $message        =   $patient_name . ', your appointment with doctor ' . $doctor_name . ' has been updated to ' . $date . ' at ' . $time . '.';
            $receiver_phone =   $this->db->get_where('patient',
                                array('patient_id' => $data['patient_id']))->row()->phone;
            
            $this->sms_model->send_sms($message, $receiver_phone);
        }
    }
    
    function approve_appointment_info($appointment_id)
    {
        $data['timestamp']  = strtotime($this->input->post('date_timestamp').' '.$this->input->post('time_timestamp') );
        $data['status']     = 'approved';
        
        if($this->session->userdata('login_type') == 'receptionist')
            $data['doctor_id'] = $this->input->post('doctor_id');
        
        $this->db->where('appointment_id',$appointment_id);
        $this->db->update('appointment',$data);
        
        // Notify patient with sms.
        $notify = $this->input->post('notify');
        if($notify != '') {
            $doctor_id      =   $this->db->get_where('appointment',
                                array('appointment_id' => $appointment_id))->row()->doctor_id;
            $patient_id     =   $this->db->get_where('appointment',
                                array('appointment_id' => $appointment_id))->row()->patient_id;
            $patient_name   =   $this->db->get_where('patient',
                                array('patient_id' => $patient_id))->row()->name;
            $doctor_name    =   $this->db->get_where('doctor',
                                array('doctor_id' => $doctor_id))->row()->name;
            $date           =   date('l, d F Y', $data['timestamp']);
            $time           =   date('g:i a', $data['timestamp']);
            $message        =   $patient_name . ', your requested appointment with doctor ' . $doctor_name . ' on ' . $date . ' at ' . $time . ' has been approved.';
            $receiver_phone =   $this->db->get_where('patient',
                                array('patient_id' => $patient_id))->row()->phone;
            
            $this->sms_model->send_sms($message, $receiver_phone);
        }
    }
    
    function delete_appointment_info($appointment_id)
    {
        $this->db->where('appointment_id',$appointment_id);
        $this->db->delete('appointment');
    }

    function save_ordonnance_info()
    {
        setlocale(LC_TIME, 'fr_FR');
        $date_format = strftime('%d-%m-%Y %H:%M:%S');

        $date_prescription = $this->input->post('date_prescription_hidden');
// Insérez $date_prescription dans la base de données

        $data['libelle']     = $this->input->post('libelle');
        $data['medicine_id']     = json_encode($this->input->post('medicine_id'));
        $data['posologie']   = $this->input->post('posologie');
        $data['nbr_unite']     = $this->input->post('nbr_unite');
        $data['qsp']           = $this->input->post('qsp');
        
        $this->db->insert('type',$data);
    }
    
    function save_prescription_info()
    {
        
        $data['prescription_timestamp']   = $this->input->post('prescription_timestamp');
           // Insérez $date_prescription dans la base de données

        $data['patient_id']     = $this->input->post('patient_id');
        $data['medicine_id']     = json_encode($this->input->post('medicine_id'));
        $data['posologie']   = $this->input->post('posologie');
        $data['nbr_unite']     = $this->input->post('nbr_unite');
        $data['qsp']           = $this->input->post('qsp');
        $data['note']          = $this->input->post('note');
        $data['doctor_id']      = $this->session->userdata('login_user_id');
        
        $this->db->insert('prescription',$data);
    }
    
    function select_prescription_info_by_doctor_id()
    {
        $doctor_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('prescription', array('doctor_id' => $doctor_id))->result_array();
    }
    
    function select_medication_history( $patient_id = '' )
    {
        return $this->db->get_where('prescription', array('patient_id' => $patient_id))->result_array();
    }
    
    function select_prescription_info_by_patient_id()
    {
        $patient_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('prescription', array('patient_id' => $patient_id))->result_array();
    }

    function select_prescription_info_by_medicine_id()
    {
        $medicine_id = $this->session->userdata('login_user_id');
        return $this->db->get_where('prescription', array('medicine_id' => $medicine_id))->result_array();
    }
    
    function update_prescription_info($prescription_id)
    {
        $data['prescription_timestamp']      = $this->input->post('prescription_timestamp');
        $data['patient_id']     = $this->input->post('patient_id');
        $data['medicine_id']     = json_encode($this->input->post('medicine_id'));
        $data['posologie']   = $this->input->post('posologie');
        $data['nbr_unite']     = $this->input->post('nbr_unite');
        $data['qsp']           = $this->input->post('qsp');
        $data['note']           = $this->input->post('note');
        $data['doctor_id']      = $this->session->userdata('login_user_id');
        
        $this->db->where('prescription_id',$prescription_id);
        $this->db->update('prescription',$data);
    }
    
    function delete_prescription_info($prescription_id)
    {
        $this->db->where('prescription_id',$prescription_id);
        $this->db->delete('prescription');
    }
    
    function save_diagnosis_report_info()
    {
        $data['timestamp']          = strtotime($this->input->post('date_timestamp').' '.$this->input->post('time_timestamp') );
        $data['report_type']        = $this->input->post('report_type');
        $data['file_name']          = $_FILES["file_name"]["name"];
        $data['document_type']      = $this->input->post('document_type');
        $data['description']        = $this->input->post('description');
        $data['prescription_id']    = $this->input->post('prescription_id');
        
        $this->db->insert('diagnosis_report',$data);
        
        $diagnosis_report_id        = $this->db->insert_id();
        move_uploaded_file($_FILES["file_name"]["tmp_name"], "uploads/diagnosis_report/" . $_FILES["file_name"]["name"]);
    }
    
    function select_diagnosis_report_info()
    {
        return $this->db->get('diagnosis_report')->result_array();
    }
    
    function delete_diagnosis_report_info($diagnosis_report_id)
    {
        $this->db->where('diagnosis_report_id',$diagnosis_report_id);
        $this->db->delete('diagnosis_report');
    }
    
    function save_notice_info()
    {
        $data['title']              = $this->input->post('title');
        $data['description']          = rand(10000, 100000).$_FILES["notice"]["name"];
        if($this->input->post('start_timestamp') != ''){
            $data['start_timestamp']    = strtotime($this->input->post('start_timestamp'));
        }
        else {
            $data['start_timestamp']    = '';
        }
        move_uploaded_file($_FILES["notice"]["tmp_name"], "uploads/notice/" . $data['description']);
        $this->db->insert('notice',$data);
    }
    
    function select_notice_info()
    {
        return $this->db->get('notice')->result_array();
    }
    
    function update_notice_info($notice_id)
    {
        $data['title']              = $this->input->post('title');
        $data['description']        = $this->input->post('description');
        if($this->input->post('start_timestamp') != '')
            $data['start_timestamp']    = strtotime($this->input->post('start_timestamp'));
        else 
            $data['start_timestamp']    = '';
        if($this->input->post('end_timestamp') != '')
            $data['end_timestamp']      = strtotime($this->input->post('end_timestamp'));
        else
            $data['end_timestamp']      = $data['start_timestamp'];
        
        $this->db->where('notice_id',$notice_id);
        $this->db->update('notice',$data);
    }
    
    function delete_notice_info($notice_id)
    {
        $this->db->where('notice_id',$notice_id);
        $this->db->delete('notice');
    }
    
    ////////private message//////
    function send_new_private_message() {
        $message    = $this->input->post('message');
        $timestamp  = strtotime(date("Y-m-d H:i:s"));

        $reciever   = $this->input->post('reciever');
        $sender     = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');

        //check if the thread between those 2 users exists, if not create new thread
        $num1 = $this->db->get_where('message_thread', array('sender' => $sender, 'reciever' => $reciever))->num_rows();
        $num2 = $this->db->get_where('message_thread', array('sender' => $reciever, 'reciever' => $sender))->num_rows();

        if ($num1 == 0 && $num2 == 0) {
            $message_thread_code                        = substr(md5(rand(100000000, 20000000000)), 0, 15);
            $data_message_thread['message_thread_code'] = $message_thread_code;
            $data_message_thread['sender']              = $sender;
            $data_message_thread['reciever']            = $reciever;
            $this->db->insert('message_thread', $data_message_thread);
        }
        if ($num1 > 0)
            $message_thread_code = $this->db->get_where('message_thread', array('sender' => $sender, 'reciever' => $reciever))->row()->message_thread_code;
        if ($num2 > 0)
            $message_thread_code = $this->db->get_where('message_thread', array('sender' => $reciever, 'reciever' => $sender))->row()->message_thread_code;


        $data_message['message_thread_code']    = $message_thread_code;
        $data_message['message']                = $message;
        $data_message['sender']                 = $sender;
        $data_message['timestamp']              = $timestamp;
        $this->db->insert('message', $data_message);

        return $message_thread_code;
    }

    function send_reply_message($message_thread_code) {
        $message    = $this->input->post('message');
        $timestamp  = strtotime(date("Y-m-d H:i:s"));
        $sender     = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');


        $data_message['message_thread_code']    = $message_thread_code;
        $data_message['message']                = $message;
        $data_message['sender']                 = $sender;
        $data_message['timestamp']              = $timestamp;
        $this->db->insert('message', $data_message);
    }

    function mark_thread_messages_read($message_thread_code) {
        // mark read only the oponnent messages of this thread, not currently logged in user's sent messages
        $current_user = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        $this->db->where('sender !=', $current_user);
        $this->db->where('message_thread_code', $message_thread_code);
        $this->db->update('message', array('read_status' => 1));
    }

    function count_unread_message_of_thread($message_thread_code) {
        $unread_message_counter = 0;
        $current_user = $this->session->userdata('login_type') . '-' . $this->session->userdata('login_user_id');
        $messages = $this->db->get_where('message', array('message_thread_code' => $message_thread_code))->result_array();
        foreach ($messages as $row) {
            if ($row['sender'] != $current_user && $row['read_status'] == '0')
                $unread_message_counter++;
        }
        return $unread_message_counter;
    }
}
