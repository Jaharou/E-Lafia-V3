<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud_model_labo extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function clear_cache() {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
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
        $data['invoice_number']     = $this->input->post('invoice_number');
        $data['patient_id']         = $this->input->post('patient_id');
        $data['creation_timestamp'] = $this->input->post('creation_timestamp');
        $data['due_timestamp']      = $this->input->post('due_timestamp');
        $data['discount_amount']    = $this->input->post('discount_amount');
        $data['net_amount']    = $this->input->post('net_amount');
        $data['avance']    = $this->input->post('avance'); 
        $data['reste_payer']    = $this->input->post('reste_payer');
        $data['status']             = $this->input->post('status');
        $data['prise_en_charge']             = $this->input->post('prise_en_charge');
        $data['pourcentage_prise']             = $this->input->post('pourcentage_prise');
        $data['mode_paiement']             = $this->input->post('mode_paiement');
        $data['note']            = $this->input->post('note');

        //.....................partie consultations.........................//

        $invoice_entries            = array();
        $descriptions               = $this->input->post('entry_description');
        $amounts                    = $this->input->post('entry_amount');
        $number_of_entries          = sizeof($descriptions);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($descriptions[$i] != "" && $amounts[$i] != "")
            {
                $new_entry          = array('description' => $descriptions[$i], 'amount' => $amounts[$i]);
                array_push($invoice_entries, $new_entry);
            }
        }
        $data['invoice_entries']    = json_encode($invoice_entries);

        //.....................partie examens.........................//

        $examen            = array();
        $libelles               = $this->input->post('libelle_examen');
        $amounts                    = $this->input->post('amount_examen');
        $number_of_entries          = sizeof($libelles);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($libelles[$i] != "" && $amounts[$i] != "")
            {
                $new_entry          = array('description' => $libelles[$i], 'amount' => $amounts[$i]);
                array_push($examen, $new_entry);
            }
        }
        $data['examen']    = json_encode($examen);

        //.....................partie traitement.........................//

        $traitement            = array();
        $libelles               = $this->input->post('libelle_traitement');
        $amounts                    = $this->input->post('amount_traitement');
        $number_of_entries          = sizeof($libelles);
        
        for ($i = 0; $i < $number_of_entries; $i++)
        {
            if ($libelles[$i] != "" && $amounts[$i] != "")
            {
                $new_entry          = array('description' => $libelles[$i], 'amount' => $amounts[$i]);
                array_push($traitement, $new_entry);
            }
        }
        $data['traitement']    = json_encode($traitement);

        $this->db->insert('invoice', $data);
    }

  
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
    function select_designation_info()
    {
        return $this->db->get('designation')->result_array();
    }

    function select_test_info()
    {
        return $this->db->get('test')->result_array();
    }
    function select_examen_info()
    {
        return $this->db->get('examen')->result_array();
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
    

    function save_test_info()
    {
        $data['libelle_examen']  = $this->input->post('libelle_examen');
        $data['amount_examen']  = $this->input->post('amount_examen');
        
        $this->db->insert('test',$data);
        
    }

    function save_examen_info()
    {
        $data['patient_id']  = $this->input->post('patient_id');
        $data['id_test']  = $this->input->post('id_test');
        $data['resultat_examen']  = $this->input->post('resultat_examen');
        $data['blood_examen']  = $this->input->post('blood_examen');
        $data['date_examen']  = $this->input->post('date_examen');
        $data['statut_examen']  = $this->input->post('statut_examen');
        
        $this->db->insert('examen',$data);
        
    }
    function delete_test_info($id_test)
    {
        $this->db->where('id_test',$id_test);
        $this->db->delete('test');
    }

    /*function delete_examen_info($examen_id)
    {
        $this->db->where('examen_id',$examen_id);
        $this->db->delete('examen');
    }*/
    
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
        $data['birth_date']     = strtotime($this->input->post('birth_date'));
        $data['age']            = $this->input->post('age');
        $data['blood_group'] 	= $this->input->post('blood_group');
        
        $this->db->insert('patient',$data);
        
        $patient_id  =   $this->db->insert_id();
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/patient_image/" . $patient_id . '.jpg');
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
        $data['birth_date']     = strtotime($this->input->post('birth_date'));
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
        $data['birth_date']     = strtotime($this->input->post('birth_date'));
        $data['age']            = $this->input->post('age');
        $data['blood_group'] 	= $this->input->post('blood_group'); 
        $this->db->where('patient_id',$patient_id);
        $this->db->update('patient',$data);
        
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/patient_image/" . $patient_id . '.jpg');
    }
    
    function delete_patient_info($patient_id)
    {
        $this->db->where('patient_id',$patient_id);
        $this->db->delete('patient');
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
        $data['name']                   = $this->input->post('name');
        $data['medicine_category_id']   = $this->input->post('medicine_category_id');
        $data['dci']            = $this->input->post('dci');
        $data['dosage']            = $this->input->post('dosage');
        $data['condit']            = $this->input->post('condit');
        $data['forme']            = $this->input->post('forme');
        $data['price']                  = $this->input->post('price');
        $data['status'] 		= $this->input->post('status');
        
        $this->db->insert('medicine',$data);
    }
    
    function select_medicine_info()
    {
        return $this->db->get('medicine')->result_array();
    }
    
    function update_medicine_info($medicine_id)
    {
        $data['name']                   = $this->input->post('name');
        $data['medicine_category_id']   = $this->input->post('medicine_category_id');
        $data['dci']            = $this->input->post('dci');
        $data['dosage']            = $this->input->post('dosage');
        $data['condit']            = $this->input->post('condit');
        $data['forme']            = $this->input->post('forme');
        $data['price']                  = $this->input->post('price');
        $data['status'] 		= $this->input->post('status');
        
        $this->db->where('medicine_id',$medicine_id);
        $this->db->update('medicine',$data);
    }
    
    function delete_medicine_info($medicine_id)
    {
        $this->db->where('medicine_id',$medicine_id);
        $this->db->delete('medicine');
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
        $data['prescription_timestamp']      = strtotime($this->input->post('prescription_timestamp'));
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
