<?php
if (!defined('BASEPATH')) {
    exit('Direct script access denied.');
}

class JournalPaiementExamen extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('fpdf');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    public function index() {
        // Redirection or default functionality
    }

    public function journalExamen_print() {
    if ($this->session->userdata('receptionist_login') == 1 || $this->session->userdata('admin_login') == 1) {
        // Initialisation des données et des paramètres
        $user_id = $this->session->userdata('login_user_id');
        $user_role = $this->session->userdata('receptionist_login') == 1 ? 'receptionist' : 'admin';
        $authorised_email = $this->db->get_where($user_role, array($user_role . '_id' => $user_id))->row()->name;
        $settings = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $system_name = $settings->description;
        $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
        $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description;
        $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description;
        $currency = $this->db->get_where('settings', array('type' => 'system_currency_id'))->row()->description;
        $currency_symbol = $currency;

        // Initialisation de FPDF
        $this->fpdf = new FPDF();
        $this->fpdf->setMargins('10', '20', '5');
        $this->fpdf->aliasNbPages();
        $this->fpdf->SetFont('times', 'B', 12);
        $this->fpdf->addPage('P', 'A4');

        // Construction du contenu du PDF
        $this->header_value('', $system_name, 'logo', $contact, $address, 'Journal de Paiement', $system_nif);
        $this->fpdf->Ln(6);
        $this->CreateTable_header($currency_symbol);

        // Extraction et affichage des factures par jour
        $today = date('d-M-Y');
        $examen_entries = $this->db->query("
            SELECT 
                e.examen_number,
                p.name AS patient_name,
                p.prenom AS patient_prenom,
                e.creation_time,
                e.examen_entries,
                e.status
            FROM 
                examen e
            JOIN 
                patient p ON e.patient_id = p.patient_id
            WHERE 
                DATE(e.creation_time) = CURDATE()
        ")->result_array();

        foreach ($examen_entries as $row) {
            // Décoder les données JSON
            $examen_data = json_decode($row['examen_entries'], true);
            $total_amount = 0;
            $description_list = '';

            // Parcourir les éléments du tableau JSON
            foreach ($examen_data as $item) {
                $description_list .= $item['description'] . ', ';
                $total_amount += isset($item['amount']) ? $item['amount'] : 0;
            }

            // Nettoyer la liste des descriptions
            $description_list = rtrim($description_list, ', ');

            // Afficher les données dans le PDF
            $this->CreateTable_row(
                $row['examen_number'],
                $row['patient_name'],
                $row['patient_prenom'],
                $row['creation_time'],
                $description_list,
                $row['status'],
                $total_amount
            );
        }

        $this->fpdf->Ln(5);
        $this->invioce_footer_atho($authorised_email, $currency_symbol);
        $this->fpdf->Ln(5);

        // Sortie du document PDF
        $this->fpdf->Output();
    }
}

    public function header_value($barcode_text = '', $title_tab = '', $logo = '', $contact = '', $address = '', $mail_title = '', $system_nif) {
        // Initialiser le PDF et ajouter une nouvelle page
        
        $this->fpdf->SetY(10);

        //________________   Logo ____________________________
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Image('uploads/' . $logo . '.jpg', 165, 9, 30); // Ajustez les coordonnées et la taille du logo ici

        // Coordonnées de départ pour les informations de contact
        $x = 10; // Coordonnée X de départ après le logo
        $y = 10; // Coordonnée Y de départ
        $width = 185;
            $height = 12;

        $this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y); 

        //________________ Header Text ____________________________
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Ln();
        $this->fpdf->Cell(0, 5, 'Contact: ' . $contact, 0, 1, 'L');
        $this->fpdf->Cell(0, 5, $address, 0, 1, 'L');
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->Cell(0, 5, 'NIF: ' . $system_nif, 0, 1, 'L');
        $this->fpdf->Ln(5); // Espace supplémentaire après les informations de contact
    }

    public function CreateTable_header($currency = '') {
        $x = 10; 
        $y = 36;
        $width = 135;
        $height = 10; 
        //$this->fpdf->Rect($x, $y, $width * 1, $height * 2);
        $this->fpdf->SetXY($x, $y);
        $this->fpdf->SetFont('times', 'B', 10);
        $this->fpdf->setFillColor(184, 207, 229);
        $this->fpdf->Cell(20, 5, utf8_decode("N° de reçu"), 1, 0, 'L', true);
        $this->fpdf->Cell(40, 5, "Patient", 1, 0, 'C', true);
        $this->fpdf->Cell(35, 5, utf8_decode("Date"), 1, 0, 'C', true);
        $this->fpdf->Cell(45, 5, utf8_decode("Test"), 1, 0, 'C', true);
        $this->fpdf->Cell(20, 5, utf8_decode("Statut"), 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, "Montant", 1, 0, 'C', true);
        $this->fpdf->Ln();
    }

    public function CreateTable_row($examen_number = '', $name = '', $prenom = '', $creation_datetime = '', $invoice_entries = '', $status = '', $amount = '') {
        $this->fpdf->SetFillColor(255);
        $this->fpdf->SetTextColor(0);
        $this->fpdf->SetFont('times', '', 10);
        $this->fpdf->Cell(20, 5, $examen_number, 1, 0, 'L', true);
        $this->fpdf->Cell(40, 5, $name . ' ' . $prenom, 1, 0, 'C', true);
        $this->fpdf->Cell(35, 5, $creation_datetime, 1, 0, 'C', true);
        $this->fpdf->Cell(45, 5, utf8_decode($invoice_entries), 1, 0, 'C', true);
        $this->fpdf->Cell(20, 5, utf8_decode($status), 1, 0, 'C', true);
        $this->fpdf->Cell(25, 5, $amount, 1, 0, 'C', true);
        $this->fpdf->Ln(5);
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
?>
