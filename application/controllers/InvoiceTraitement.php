<?php
/*  
 *  Class for INVOICE
 *  @author : Raju Ahmed
 *  Date    : 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}

class InvoiceTraitement extends CI_Controller {

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
    public function traitement_print($param1 = ''){ 
        if ( $this->session->userdata('receptionist_login') == 1 || $this->session->userdata('admin_login') == 1 ):
            $user_id = $this->session->userdata('login_user_id');
            if( $this->session->userdata('receptionist_login') == 1 ){
                $athorised_email = $this->db->get_where('receptionist', array('receptionist_id' => $user_id))->row()->name;
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
            
            $traitement_number = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->traitement_number;
            $traitement_id = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->traitement_id;
            $patient_id = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->patient_id;
             $traitement_entry = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->traitement_entries;
            $status = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->status;
            $date_traitement = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->date_traitement;
            $discount_amount = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->discount_amount;       
            $prise_en_charge = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->prise_en_charge;
            $mode_paiement = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->mode_paiement;
            $pourcentage_prise = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->pourcentage_prise;
            $note = $this->db->get_where('traitement', array('traitement_id' => $param1))->row()->note;

            //$unite_mesure = $this->db->get_where('test', array('id_test' => $id_test))->row()->unite_mesure;

            $name = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->name;
            $prenom = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->prenom;
            $age = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->age;
            $address_patient = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->address_patient;
            $phone = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->phone;
            
            
            //s$qrcode = $param1 . $student_id['student_id'] . $class_id['name'] . $student_name['roll'];
            $time = $date_traitement;
            
            $total_amount       = 0;
            $traitement_entries    = json_decode($traitement_entry);    
                $i = 1;
             foreach ($traitement_entries as $traitemententry){
                 $i++;
                $total_amount += intval($traitemententry->amount);
                $traitement_entries = $traitemententry->description;
                $traitement_entries = $traitemententry->qte;
                $currency_symbol . $traitemententry->amount;
                
            }
            
            $total_amount  = $this->crud_model->calculate_total_amount_traitement($traitement_number);
            $remise_amount = $this->crud_model->calculate_invoice_total_remise($traitement_number);
            $prise_charge = $this->crud_model->calculate_total_prise_traitement($traitement_number); 
            $grand_total = $this->crud_model->calculate_invoice_total_amount_traitement($traitement_number);
            //$reste_total = $this->crud_model->calculate_invoice_total_amount_reste($invoice_number);
 
            $this->fpdf = new FPDF();
            $this->fpdf->setMargins('10', '20', '0');
            $this->fpdf->aliasNbPages();
            $this->fpdf->addPage('P', 'A5');
            $this->fpdf->SetFont('times', 'B', 12);

            $this->traitement_numero( utf8_decode("Reçu de paiement N°"). $traitement_number);
            $this->fpdf->Ln(6);

            /*$this->value_test(utf8_decode('Unité de mesure: '). $unite_mesure);
            $this->fpdf->Ln(6);*/

            $this->value_info(utf8_decode('Patient: '). $name . ' '. $prenom, utf8_decode('Age: '). $age. (' ans'), 'Adresse: '. $address_patient, utf8_decode('Téléphone: '). $phone);
            $this->fpdf->Ln(6);

            $this->date_traitement('Date: '. $time);
            $this->fpdf->Ln();

            $this->CreateTable_header($currency_symbol);
            $traitement_entries    = json_decode($traitement_entry);
            foreach ($traitement_entries as $traitemententry) {
            $this->CreateTable_row($traitemententry->amount, '1', utf8_decode($traitemententry->qte), '', utf8_decode($traitemententry->description), $traitemententry->amount);
             }
             $this->invioce_footer_amount_traitement($total_amount .'  FCFA   ');
             $this->fpdf->Ln();

            $this->invioce_footer_traitement($grand_total .'  FCFA', $currency_symbol, $remise_amount .'  FCFA', $prise_charge .'  FCFA');
            $this->fpdf->Ln(5);
            
            $this->invioce_footer_atho($athorised_email, $currency_symbol);
            $this->fpdf->Ln(5);

            
            $this->header_value('', $system_name, 'logo', $contact, $address, $system_email, utf8_decode("Reçu de paiement N°"). $traitement_number, $system_nif);
             $this->fpdf->Ln(6);
            $this->invoice_prise_en_charge( 'Prise en charge  ..............................................................................: '.''. utf8_decode($prise_en_charge ).'  '.'......................................', 'Pourcentage de prise en charge  .....................................................:'.' '.$pourcentage_prise .'%'.'  '.'...................................', 'Pourcentage de remise  ...................................................................:'.' '.$discount_amount .'%'.'  '.'...............................', 'Mode de paiement  .........................................................................: '.''. utf8_decode($mode_paiement ).'  '. '.........................');

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

    public function traitement_numero($traitement_number = '' ) {
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 12);
        $this->fpdf->Cell(59, -10,$traitement_number, 1, 0, 'L');
    }

    public function date_traitement($data2 = '' ) {
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

    public function invoice_prise_en_charge($prise_en_charge = '', $pourcentage_prise = '', $discount_amount = '', $mode_paiement = '') {
        $x = 10; 
        $y = 60;
        $width = 0;
        $height = 0; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(50, $height,$prise_en_charge, 0, 0, 'L');
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(50, $height, $pourcentage_prise, 0, 0, 'L');
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(50, $height, $discount_amount, 0, 0, 'L'); 
        $this->fpdf->Ln(6);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(50, $height,$mode_paiement, 0, 1, 'L');
        //$this->fpdf->Ln();
    }
    
    /*public function invoice_note($note = '') {
        $this->fpdf->setFillColor(255,201,120);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(180, 10,$note, 1, 1, 'L');
        $this->fpdf->Ln();
    }*/

    public function invioce_footer_traitement($gtotal = '', $currency = '', $remise_amount ='', $prise_charge = '') {
        //$this->fpdf->setFillColor(255,201,120);
        $x = 1; 
        $y = 122;
        $width = 0;
        $height = 0; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 1);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->Ln(26);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(100, 5, utf8_decode("Montant prise en charge"), 1, 0, 'L', true);
        $this->fpdf->Cell(35, 5, $prise_charge . "", 1, 0, 'C', true);
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(100, 5, utf8_decode("Montant réduction"), 1, 0, 'L', true);
        $this->fpdf->Cell(35, 5, $remise_amount . "", 1, 0, 'C', true);
        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('times', 'B', 13);
        $this->fpdf->Cell(100, 9, utf8_decode("Montant total net à la charge du patient"), 1, 0, 'L', true);
       $this->fpdf->Cell(35, 9, $gtotal . "", 1, 0, 'C', true);
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
        $this->fpdf->Ln(1);
        $this->fpdf->Cell(0, 5, utf8_decode('Analyses médicales'),0,1,'C');
        $this->fpdf->Cell(50, 5, "Produit"."", 1, 0, 'L', true);
        $this->fpdf->Cell(20, 5, ("PU")."", 1, 0, 'C', true);
        $this->fpdf->Cell(10, 5, utf8_decode("Qté")."", 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, utf8_decode("Résultat")."", 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, "Sous_total"."", 1, 0, 'C', true);
        //$this->fpdf->Cell(30, 6, utf8_decode("Net à payer")."", 1, 0, 'R', true);
        $this->fpdf->Ln();
    }

    public function CreateTable_row($amount = '', $qte = '', $resultat = '', $discount_amount = '', $traitement_entries = '') {
        $this->fpdf->SetFillColor(255);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(50, 5, $traitement_entries, 1, 0, 'L', true);
        $this->fpdf->Cell(20, 5, $amount, 1, 0, 'C', true);
        $this->fpdf->Cell(10, 5, $qte, 1, 0, 'C', true);
        $this->fpdf->Cell(30, 5, $resultat, 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, $amount, 1, 0, 'C', true);
        //$this->fpdf->Cell(25, 6, $amount ."", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function invioce_footer_amount_traitement($total_amount = '') {
        //$this->fpdf->setFillColor(255,201,120);
        $this->fpdf->SetFont('times', 'B', 11);
        $this->fpdf->Cell(100, 5, utf8_decode("Montant total"), 1, 0, 'L', true);
        $this->fpdf->Cell(35, 5, $total_amount . "", 1, 0, 'R', true); 
        $this->fpdf->Ln(6);  
    }

    public function invioce_footer_atho($atho = '') {
    $this->fpdf->SetTextColor(0);
    $this->fpdf->SetFont('times', 'B', 10);
    // Positionner le texte en bas de la page
    $this->fpdf->SetY(-30); // Ajustez cette valeur selon vos besoins
    $this->fpdf->Cell(0, 6, utf8_decode("Le caissier: ") . utf8_decode($atho), 0, 0, 'R');
    $this->fpdf->Ln(5);
    }

}