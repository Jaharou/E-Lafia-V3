<?php
/* 	
 * 	Class for INVOICE
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class InvoiceExamenMedecin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('fpdf');
        // $this->fpdf->load->library('invoicelibray');
        /* cache control */
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    /*     * *default functin, redirects to login page if no admin logged in yet** */

    public function index() {
        
    }

    /*     * ****PRINT INVOICE**** */
    public function examenMedecin_print($param1 = ''){ 
        if ( $this->session->userdata('doctor_login') == 1 || $this->session->userdata('admin_login') == 1 ):
			$user_id = $this->session->userdata('login_user_id');
			if( $this->session->userdata('doctor_login') == 1 ){
				$athorised_email = $this->db->get_where('doctor', array('doctor_id' => $user_id))->row()->name;
			}else{				
				$athorised_email = $this->db->get_where('admin', array('admin_id' => $user_id))->row()->name;
			}
			$system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
            $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
            $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description;
            $system_email = $this->db->get_where('settings', array('type' => 'system_email'))->row()->description;
            $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description;
			$currency = $this->db->get_where('settings', array('type' => 'system_currency_id'))->row()->description;
			$currency_symbol    = $currency;//$this->db->get_where('currency', array('currency_id' => $currency))->row()->currency_symbol;
            $examen_number = $this->db->get_where('examen', array('id_examen' => $param1))->row()->examen_number;
            $id_examen = $this->db->get_where('examen', array('id_examen' => $param1))->row()->id_examen;
            $id_test = $this->db->get_where('examen', array('id_examen' => $param1))->row()->id_test;
            $patient_id = $this->db->get_where('examen', array('id_examen' => $param1))->row()->patient_id;
             $examen_entry = $this->db->get_where('examen', array('id_examen' => $param1))->row()->examen_entries;
            $blood_examen = $this->db->get_where('examen', array('id_examen' => $param1))->row()->blood_examen;
            $statut_examen = $this->db->get_where('examen', array('id_examen' => $param1))->row()->statut_examen;
            $creation_time = $this->db->get_where('examen', array('id_examen' => $param1))->row()->creation_time;
            $note = $this->db->get_where('examen', array('id_examen' => $param1))->row()->note;

            //$unite_mesure = $this->db->get_where('test', array('id_test' => $id_test))->row()->unite_mesure;

            $name = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->name;
            $prenom = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->prenom;
            $age = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->age;
            $address_patient = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->address_patient;
            $phone = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->phone;
            
            $time = $creation_time;
			
            $this->fpdf = new FPDF();
            $this->fpdf->setMargins('10', '20', '0');
            $this->fpdf->aliasNbPages();
            $this->fpdf->addPage('P', 'A5');
            $this->fpdf->SetFont('times', 'B', 12);

            $this->examen_numero( utf8_decode("Reçu d'examen N°"). $examen_number);
            $this->fpdf->Ln();

            $this->value_info(utf8_decode('Patient: '). $name . ' '. $prenom, utf8_decode('Age: '). $age. (' ans'), 'Adresse: '. $address_patient, utf8_decode('Téléphone: '). $phone);
            $this->fpdf->Ln();

            $this->creation_time('Date: '. $time);
            $this->fpdf->Ln();

            $this->CreateTable_header($currency_symbol);
            $examen_entries    = json_decode($examen_entry);
            foreach ($examen_entries as $examenentry) {
            $this->CreateTable_row('1', utf8_decode($examenentry->resultat), utf8_decode($examenentry->unite), utf8_decode($examenentry->description), utf8_decode($examenentry->intervalle));
             }
            
            $this->invioce_footer_atho($athorised_email, $currency_symbol);
            $this->fpdf->Ln(5);

            
            $this->header_value('', $system_name, 'logo', $contact, $address, $system_email, utf8_decode("Reçu de paiement N°"). $examen_number, $system_nif);
             $this->fpdf->Ln(6);
            $this->fpdf->Output();
            //$this->fpdf->Output($param1 . '.pdf', 'D');
             endif;
             //redirect(base_url(), 'refresh');
    }
          
    public function header_value($system_name = '', $title_tab = '', $logo = '', $contact = '', $address = '', $system_email = '', $mail_title = '',$system_nif) {

        //________________   Logo ____________________________
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Image('uploads/' . $logo . '.jpg', 78, 3, 41 , 27);
        
        // $this->fpdf->QR($barcode_text, 170, 10, 16);
    
        //________________ Header Text ____________________________
        /*$this->fpdf->SetFont('times', 'B', 13);
        $this->fpdf->Cell(130, -70, $title_tab, 0, 0, 'R');*/
        
        $x = 72; // Coordonnée X de départ
        $y = 30; // Coordonnée Y de départ
        $width = 72; // Largeur de chaque cellule
        $height = 6; // Hauteur de chaque cellule

        $this->fpdf->Rect($x, $y, $width * 1, $height * 4);
        $this->fpdf->SetXY($x, $y); 
                 
        //$this->fpdf->Ln(6);
        $this->fpdf->SetY(12);
        $this->fpdf->setFillColor(230, 230, 230);
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(125, $height, $system_name, 0, 1, 'R');
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(125, $height, 'Contact: ' . $contact, 0, 0, 'R');
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(125, $height, $address, 0, 0, 'R');
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(125, $height, $system_email, 0, 1, 'R');
        $this->fpdf->Ln(0);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(125, $height, $system_nif, 0, 1, 'R');
        $this->fpdf->Ln();
    }

    public function value_test($unite_mesure = '' ) {
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 12);
        $this->fpdf->Cell(58, -10,$unite_mesure, 1, 0, 'L');
    }

    public function value_info($data1 = '', $age = '', $address_patient = '', $phone = '') {
    $this->fpdf->SetFont('times', '', 10);
    $x = 10;
    $y = 23;
    $width = 15;
    $height = 6;
    // Regrouper toutes les données dans un seul cadrage
    $this->fpdf->Rect($x, $y, $width * 4, $height * 4);
    $this->fpdf->SetXY($x, $y); // Définir la position pour le texte
    $this->fpdf->Cell($width, $height, $data1, 0, 0, 'L');
    $this->fpdf->Ln(6);
    $this->fpdf->Cell($width, $height, $age, 0, 0, 'L');
    $this->fpdf->Ln(6);
    $this->fpdf->SetFont('times', 'B', 10);
    $this->fpdf->Cell($width, $height, $address_patient, 0, 0, 'L');
    $this->fpdf->Ln(6);
    $this->fpdf->SetFont('times', '', 10);
    $this->fpdf->Cell($width, $height, $phone, 0, 1, 'L'); // Aller à la ligne après la dernière cellule
    $this->fpdf->Ln(10);
}

    public function examen_numero($examen_number = '' ) {
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 12);
        $this->fpdf->Cell(59, -10,$examen_number, 1, 0, 'L');
    }

    public function creation_time($data2 = '' ) {
        $x = 10; 
        $y = 54;
        $width = 5;
        $height = 0; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(60, -5,$data2, 1, 1, 'L');
        $this->fpdf->Ln(6);
    }

    public function CreateTable_header($currency = '') {
        $x = 10; 
        $y = 86;
        $width = 135;
        $height = 5; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->setFillColor(184, 207, 229);
        $this->fpdf->Cell(0,10, utf8_decode('Résultat des analyses médicales'),0,1,'C');
        $this->fpdf->Cell(40, 5, "Test"."", 1, 0, 'L', true);
        $this->fpdf->Cell(10, 5, utf8_decode("Qté")."", 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, utf8_decode("Résultat")."", 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, utf8_decode("Unité")."", 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, "Intervalle"."", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function CreateTable_row($qte = '', $resultat = '', $unite = '', $examen_entries = '', $intervalle = '') {
        $this->fpdf->SetFillColor(255);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(40, 5, $examen_entries, 1, 0, 'L', true);
        $this->fpdf->Cell(10, 5, $qte, 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, $resultat, 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, $unite, 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, $intervalle ."", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function invioce_footer_atho($atho = '') {
    $this->fpdf->SetTextColor(0);
    $this->fpdf->SetFont('times', 'B', 10);
    // Positionner le texte en bas de la page
    $this->fpdf->SetY(-27); // Ajustez cette valeur selon vos besoins
    $this->fpdf->Cell(0, 6, utf8_decode("Le Médecin: ") . utf8_decode($atho), 0, 0, 'R');
    $this->fpdf->Ln(5);
    }

}