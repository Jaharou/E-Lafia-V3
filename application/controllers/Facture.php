<?php
if (!defined('BASEPATH')) {
    exit('Direct script access denied.');
}

class Facture extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('fpdf');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    public function index() {
        // Redirection ou fonctionnalité par défaut
    }

    public function facture_print($param1 = '') {
    if ($this->session->userdata('receptionist_login') == 1 || $this->session->userdata('admin_login') == 1) {
        // Initialisation des données et des paramètres
        $user_id = $this->session->userdata('login_user_id');
        $user_role = $this->session->userdata('receptionist_login') == 1 ? 'receptionist' : 'admin';
        $authorised_user = $this->db->get_where($user_role, array($user_role . '_id' => $user_id))->row();
        $authorised_name = $authorised_user->name;
        $settings = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $system_name = $settings->description;
        $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
        $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description;
        $system_email = $this->db->get_where('settings', array('type' => 'system_email'))->row()->description;
        $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description;
        $currency = $this->db->get_where('settings', array('type' => 'system_currency_id'))->row()->description;
        $currency_symbol = $currency;

        // Récupérer les données de la facture
        $facture_data = $this->db->select('facture.*, patient.name, patient.prenom')
            ->from('facture')
            ->join('patient', 'patient.patient_id = facture.patient_id')
            ->where('facture_id', $param1)
            ->get()
            ->row();

        // Informations de la facture
        $facture_number = $facture_data->facture_number;
        $patient_name = $facture_data->name . ' ' . $facture_data->prenom;
        $date_facture = $facture_data->date_facture;
        $total_amount = $facture_data->total_amount;
        $remise_amount = $facture_data->remise_amount;
        $amount_net = $facture_data->amount_net;
        $status = $facture_data->status;

        // Première désérialisation pour obtenir une chaîne JSON valide
        $invoice_entries_json = json_decode($facture_data->invoice_entries)[0];
        $examen_entries_json = json_decode($facture_data->examen_entries)[0];
        $traitement_entries_json = json_decode($facture_data->traitement_entries)[0];
        $invoice_ventes_json = json_decode($facture_data->invoice_ventes)[0];

        // Deuxième désérialisation pour obtenir un tableau d'objets
        $invoice_entries = json_decode($invoice_entries_json);
        $examen_entries = json_decode($examen_entries_json);
        $traitement_entries = json_decode($traitement_entries_json);
        $invoice_ventes = json_decode($invoice_ventes_json);

        // Mis à jour de la date
        $updated_at = $facture_data->updated_at;

        // Initialiser le PDF
        $pdf = new FPDF('P', 'mm', array(148, 210));  // Format A5
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 5);

        // Fonction d'entête
        function header_value($pdf, $system_name, $contact, $address, $system_email, $system_nif) {
        $pdf->SetFont('times', '', 11);  // Réduire la taille de la police pour s'adapter au format A5
        $pdf->Image('uploads/logo.jpg', 100, 5, 40, 45);  // Ajuster la taille et la position de l'image pour A5
        $x = 10;
        $y = 15;
        $width = 128;  // Ajuster la largeur du cadre
        $height = 6;   // Ajuster la hauteur du cadre
        $pdf->Rect($x, $y, $width, $height * 4);
        $pdf->SetXY($x, $y);
        $pdf->SetY(10);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Ln();
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(85, $height, '', 0, 0, 'L');
        $pdf->Ln();
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(60, $height, 'Contact:' . ' ' . $contact, 0, 0, 'R');
        $pdf->Ln();
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(65, $height, $address, 0, 0, 'R');
        $pdf->Ln();
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, $height, $system_email, 0, 1, 'R');
        $pdf->Ln(0);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(50, $height, $system_nif, 0, 1, 'R');
        $pdf->Ln();
        }

        header_value($pdf, $system_name, $contact, $address, $system_email, $system_nif);

        // Numéro de la facture
        

        // Afficher les détails de la facture
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Facture N°'). $facture_number, 0, 1, 'R');
        $pdf->Ln(-15);

        // Set font for the information
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetX(10); // Move cursor to the right of the 'Date' label
        $pdf->MultiCell(128, 6, "Date: $date_facture\nPatient: $patient_name\nStatus: " . utf8_decode($status), 1);
        $pdf->Ln(0);

        // Table des factures
        $pdf->SetFont('Arial', 'B', 10);
        // Définir la couleur de fond (par exemple, un bleu clair)
        $pdf->SetFillColor(230, 230, 230); // RGB (rouge, vert, bleu)
        $pdf->Cell(23, 5, 'Rubriques', 1, 0, '', true);
        $pdf->Cell(45, 5, 'Prestation', 1, 0, '', true);
        $pdf->Cell(22, 5, 'Prix unitaire', 1, 0, 'C', true);
        $pdf->Cell(17, 5, utf8_decode('Quantité'), 1, 0, '', true);
        $pdf->Cell(21, 5, 'Total', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255); // Blanc
        $pdf->Ln();
        
        // Afficher les prestations de la facture
        $pdf->SetFont('Arial', '', 10);
        $isConsultationPrinted = false;
        $isExamenPrinted = false;
        $isTraitementPrinted = false;
        $isMédicamentPrinted = false;

        foreach ($invoice_entries as $entry) {
            if (!$isConsultationPrinted) {
                $pdf->Cell(23, 5 * count($invoice_entries), 'Consultations', 1, 0, 'C'); // Affiche "Consultations" une seule fois, hauteur ajustée pour toutes les lignes
                $isConsultationPrinted = true;
            } else {
                $pdf->Cell(23, 5, '', 1, 0); // Cellule vide pour les lignes suivantes
            }
            $pdf->Cell(45, 5, utf8_decode($entry->description), 1);
            $pdf->Cell(22, 5, $entry->amount . ' ', 1, 0, 'C');
            $pdf->Cell(17, 5, $entry->qte_consult, 1, 0, 'C');
            $pdf->Cell(21, 5, $entry->net_amount . ' ', 1, 1, 'C');
        }

        // Afficher les examens de la facture
        foreach ($examen_entries as $entry) {
            if (!$isExamenPrinted) {
                $pdf->Cell(23, 5 * count($examen_entries), 'Examens', 1, 0, 'C'); // Affiche "Examens" une seule fois, hauteur ajustée pour toutes les lignes
                $isExamenPrinted = true;
            } else {
                $pdf->Cell(23, 5, '', 0, 0); // Cellule vide pour les lignes suivantes
            }
            $pdf->Cell(45, 5, utf8_decode($entry->description), 1);
            $pdf->Cell(22, 5, $entry->montant . ' ', 1, 0, 'C');
            $pdf->Cell(17, 5, $entry->qte_examen, 1, 0, 'C');
            $pdf->Cell(21, 5, $entry->amountT . ' ', 1, 1, 'C');
            }

        // Afficher les traitements de la facture
        foreach ($traitement_entries as $entry) {
            if (!$isTraitementPrinted) {
                $pdf->Cell(23, 5 * count($traitement_entries), 'Traitements', 1, 0, 'C'); // Affiche "Traitements" une seule fois, hauteur ajustée pour toutes les lignes
                $isTraitementPrinted = true;
            } else {
                $pdf->Cell(23, 5, '', 0, 0); // Cellule vide pour les lignes suivantes
            }
            $pdf->Cell(45, 5, utf8_decode($entry->description), 1);
            $pdf->Cell(22, 5, $entry->amount . ' ', 1, 0, 'C');
            $pdf->Cell(17, 5, $entry->qte, 1, 0, 'C');
            $pdf->Cell(21, 5, $entry->prixTrait . ' ', 1, 1, 'C');
        }

        // Afficher les Médicaments de la vente
        foreach ($invoice_ventes as $entry) {
            if (!$isMédicamentPrinted) {
                $pdf->Cell(23, 5 * count($invoice_ventes), utf8_decode('Médicaments'), 1, 0, 'C'); // Affiche "Médicaments" une seule fois, hauteur ajustée pour toutes les lignes
                $isMédicamentPrinted = true;
            } else {
                $pdf->Cell(23, 5, '', 0, 0); // Cellule vide pour les lignes suivantes
            }
            $pdf->Cell(45, 5, utf8_decode($entry->produit), 1);
            $pdf->Cell(22, 5, $entry->amount . ' ', 1, 0, 'C');
            $pdf->Cell(17, 5, $entry->qte_vente, 1, 0, 'C');
            $pdf->Cell(21, 5, $entry->Ptotal . ' ', 1, 1, 'C');
        }

        
        // Ajouter le montant total des factures des examens et des traitements au pied de page
            $pdf->SetY(-41);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(128, 5, utf8_decode('Montant total :'), 1, 0, 'L');
            $pdf->Cell(0, 5, $total_amount . ' FCFA', 0, 1, 'R');
            $pdf->Cell(128, 5, utf8_decode('Montant de réduction :'), 1, 0, 'L');
            $pdf->Cell(0, 5, $remise_amount . ' FCFA', 0, 1, 'R');
            $pdf->Cell(128, 5, utf8_decode('Montant net à payer :'), 1, 0, 'L');
            $pdf->Cell(0, 5, $amount_net . ' FCFA', 0, 1, 'R'); 


            // Fonction de pied de page
            function footer_value($pdf, $authorised_name) {
                $pdf->SetY(-26);
                $pdf->SetFont('Arial', 'I', 10);
                $pdf->Cell(0, 5, utf8_decode('Imprimé par: ') . utf8_decode($authorised_name) . ' le ' . utf8_decode(date('d-m-Y à H:i:s')), 0, 0, 'L');
                $pdf->Cell(0, 5, 'Page ' . $pdf->PageNo() . '', 0, 0, 'R');
            }

            // Appel de la fonction de pied de page
            footer_value($pdf, $authorised_name);

        // Afficher le PDF
        $pdf->Output();
    } else {
        redirect(base_url(), 'refresh');
    }
}
}
?>
