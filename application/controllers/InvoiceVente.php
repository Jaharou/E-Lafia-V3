<?php
/* 	
 * 	Class for INVOICE
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class InvoiceVente extends CI_Controller {
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
    public function vente_print($param1 = '') { 
        if ($this->session->userdata('pharmacist_login') == 1 || $this->session->userdata('admin_login') == 1) {
            $user_id = $this->session->userdata('login_user_id');
            if ($this->session->userdata('pharmacist_login') == 1) {
                $athorised_email = $this->db->get_where('pharmacist', array('pharmacist_id' => $user_id))->row()->name;
            } else {                
                $athorised_email = $this->db->get_where('admin', array('admin_id' => $user_id))->row()->name;
            }

            $system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
            $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
            $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description;
            $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description;
            $currency = $this->db->get_where('settings', array('type' => 'system_currency_id'))->row()->description;
            $currency_symbol = $currency;
            $vente_data = $this->db->get_where('vente', array('vente_id' => $param1))->row();
            $patient_id = $this->db->get_where('vente', array('vente_id' => $param1))->row()->patient_id;

            $name = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->name;
            $prenom = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->prenom;
            $age = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->age;
            $address_patient = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->address_patient;
            $phone = $this->db->get_where('patient', array('patient_id' => $patient_id))->row()->phone;
            
            $vente_number    = $vente_data->vente_number;
            $vente_entry     = $vente_data->invoice_ventes;
            $date_vente      = $vente_data->date_vente;
            $discount_amount = $vente_data->discount_amount;       
            $prise_en_charge = $vente_data->prise_en_charge;
            $mode_paiement   = $vente_data->mode_paiement;
            $pourcentage_prise = $vente_data->pourcentage_prise;
            $note = $vente_data->note;
            
            $time = $date_vente;
            
            $prix_total  = $this->crud_model->calculate_prix_total($vente_number);
            $remise_amount = $this->crud_model->calculate_vente_total_remise_amount($vente_number);
            $prise_charge = $this->crud_model->calculate_total_prise_vente($vente_number); 
            $grand_total = $this->crud_model->calculate_vente_prix_total($vente_number);

            $this->fpdf = new FPDF();
            $this->fpdf->setMargins('10', '20', '0');
            $this->fpdf->aliasNbPages();
            $this->fpdf->addPage('P', 'A5');
            $this->fpdf->SetFont('times', 'B', 12);

            $this->vente_numero(utf8_decode('Reçu pharmacie N°') . $vente_number);
            $this->fpdf->Ln(30);
            $this->value_info(utf8_decode('Patient: '). $name . ' '. $prenom, utf8_decode('Age: '). $age. (' ans'), 'Adresse: '. $address_patient, utf8_decode('Téléphone: '). $phone);
            $this->fpdf->Ln(6);
            $this->date_vente('Date: ' . $time);
            $this->fpdf->Ln();
            $this->CreateTable_header($currency_symbol);
            $total_amount = 0;
            $invoice_ventes = json_decode($vente_entry);
            $i = 1;
            foreach ($invoice_ventes as $venteentry) {
                $i++;
                $total_amount += $venteentry->amount;
                // Récupérer le nom du produit en fonction de l'ID du produit
                $name = $this->db->get_where('medicine', array('medicine_id' => $venteentry->produit))->row()->name;
                // Utilisez $name au lieu de $venteentry->produit
                $venteentry->produit = $name;
            }
            foreach ($invoice_ventes as $venteentry) {
                // Affichez le nom du produit ici
                $this->CreateTable_row($venteentry->amount, '', $venteentry->qte, utf8_decode($venteentry->produit), $venteentry->Ptotal);
            }
            
            $this->invioce_footer_amount($prix_total . '  FCFA   ');
            $this->fpdf->Ln(6);
            $this->invioce_footer($grand_total . '  FCFA', $currency_symbol, $remise_amount . '  FCFA', $prise_charge . '  FCFA');
            $this->fpdf->Ln(5);
            $this->invioce_footer_atho($athorised_email, $currency_symbol);
            $this->fpdf->Ln(5);
            $this->fpdf->Ln();
            $this->header_value('', $system_name, 'logo', $contact, $address, utf8_decode('Reçu pharmacie N°') . $vente_number, $system_nif);
            $this->fpdf->Ln(6);
            $this->invoice_prise_en_charge_mode(
                'Prise en charge  ..............................................................................: '. utf8_decode($prise_en_charge),
                'Pourcentage de prise en charge  .....................................................: ' . $pourcentage_prise . '%',
                'Pourcentage de remise  ...................................................................: ' . $discount_amount . '%',
                'Mode de paiement  .........................................................................: ' . utf8_decode($mode_paiement)
            );

            $this->fpdf->Output();
            }
        }
          
    public function header_value($barcode_text = '', $title_tab = '', $logo = '', $contact = '', $address = '', $mail_title = '',$system_nif) {

        $this->fpdf->SetY(10);

        //________________   Logo ____________________________
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Image('uploads/' . $logo . '.jpg', 78, 3, 53, 27);        
        //________________ Header Text ____________________________
        
        $x = 73; // Coordonnée X de départ
        $y = 30; // Coordonnée Y de départ
        $width = 72; // Largeur de chaque cellule
        $height = 6; // Hauteur de chaque cellule
        $this->fpdf->Rect($x, $y, $width * 1, $height * 4);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->setFillColor(230, 230, 230);
        $this->fpdf->Ln();
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(125, $height, 'Contact: ' . $contact, 0, 0, 'R');
        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(130, $height, $address, 0, 0, 'R');
        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(115, $height, $system_nif, 0, 1, 'R');
        $this->fpdf->Ln(10);
    }

    public function vente_numero($vente_number = '' ) {
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 12);
        $this->fpdf->Cell(60, -8,$vente_number, 1, 0, 'L');
    }

    public function value_info($data1 = '', $age = '', $address_patient = '', $phone = '') {
        $this->fpdf->SetFont('times', '', 10);
        // Définir les coordonnées de départ pour le cadrage
        $x = 10; // Coordonnée X de départ
        $y = 23; // Coordonnée Y de départ
        $width = 15; // Largeur de chaque cellule
        $height = 6; // Hauteur de chaque cellule
        // Regrouper toutes les données dans un seul cadrage
        $this->fpdf->Rect($x, $y, $width * 4, $height * 4); // Cadrage pour tout le contenu

        // Remplir les cellules avec les données
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

    public function date_vente($data2 = '' ) {
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

    public function invoice_prise_en_charge_mode($prise_en_charge = '', $pourcentage_prise = '', $discount_amount = '', $mode_paiement = '') {
        $x = 10; 
        $y = 60;
        $width = 135;
        $height = 15; 
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
        $this->fpdf->Ln();
    }

    public function invioce_footer($gtotal = '', $currency = '', $remise_amount ='', $prise_charge = '') {
        //$this->fpdf->setFillColor(255,201,120);
        $x = 1; 
        $y = 122;
        $width = 0;
        $height = 0; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 1);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->Ln(40);
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
        $y = 96;
        $width = 135;
        $height = 5; 
        $this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->setFillColor(184, 207, 229);
        $this->fpdf->Cell(65, 5, "Produit"."", 1, 0, 'L', true);
        $this->fpdf->Cell(25, 5, ("PU")."", 1, 0, 'C', true);
        $this->fpdf->Cell(10, 5, utf8_decode("Qté")."", 1, 0, 'C', true);
        //$this->fpdf->Cell(30, 5, "%Prise en charge"."", 1, 0, 'C', true);
        $this->fpdf->Cell(35, 5, "Sous_total"."", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function CreateTable_row($qte = '', $pourcentage_prise = '', $amount = '', $invoice_ventes = '', $Ptotal = '') {
        $this->fpdf->SetFillColor(255);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(65, 5, $invoice_ventes, 1, 0, 'L', true);
        $this->fpdf->Cell(25, 5, $qte_vente, 1, 0, 'C', true);
        $this->fpdf->Cell(10, 5, $amount, 1, 0, 'C', true);
        $this->fpdf->Cell(35, 5, $Ptotal, 1, 0, 'C', true);
        //$this->fpdf->Cell(30, 6, $amount ."", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function invioce_footer_amount($prix_total = '') {
        //$this->fpdf->setFillColor(255,201,120);
        $this->fpdf->SetFont('times', 'B', 11);
        $this->fpdf->Cell(100, 5, utf8_decode("Montant total"), 1, 0, 'L', true);
        $this->fpdf->Cell(35, 5, $prix_total . "", 1, 0, 'R', true); 
        $this->fpdf->Ln(6);  
    }

    public function invioce_footer_atho($atho = '') {
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', 'B', 10);
        // Positionner le texte en bas de la page
        $this->fpdf->SetY(-27); // Ajustez cette valeur selon vos besoins
        $this->fpdf->Cell(0, 6, utf8_decode("Le pharmacien: ") . utf8_decode($atho), 0, 0, 'R');
        $this->fpdf->Ln(5);
        }   
    }



    