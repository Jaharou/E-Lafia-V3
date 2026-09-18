<?php
if (!defined('BASEPATH')) {
    exit('Direct script access denied.');
}

class JournalPaiementAdmin extends CI_Controller {

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

    public function journalAdmin_print() {
        if ($this->session->userdata('receptionist_login') == 1 || $this->session->userdata('admin_login') == 1) {
        // Initialisation des données et des paramètres
        $user_id = $this->session->userdata('login_user_id');
        $user_role = $this->session->userdata('receptionist_login') == 1 ? 'receptionist' : 'admin';
        $authorised_user = $this->db->get_where($user_role, array($user_role . '_id' => $user_id))->row();
        $authorised_name = $authorised_user->name;

        // Récupérer receptionist_id depuis un paramètre GET ou POST
        $receptionist_id = $this->input->post('receptionist_id') ? $this->input->post('receptionist_id') : null;

        // Autres informations système
        $settings = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $system_name = $settings->description;
        $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
        $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description;
        $system_email = $this->db->get_where('settings', array('type' => 'system_email'))->row()->description;
        $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description;
        $currency = $this->db->get_where('settings', array('type' => 'system_currency_id'))->row()->description;
        $currency_symbol = $currency;

        // Récupération des factures avec filtre receptionist_id s'il est défini
        $this->db->select('invoice.*, patient.name AS name, patient.prenom AS prenom, receptionist.name AS receptionist_name');
        $this->db->from('invoice');
        $this->db->join('patient', 'patient.patient_id = invoice.patient_id');
        $this->db->join('receptionist', 'receptionist.receptionist_id = invoice.receptionist_id', 'left'); // Joindre la table receptionist
        if ($receptionist_id) {
            $this->db->where('invoice.receptionist_id', $receptionist_id);
        }
        $this->db->order_by('invoice.creation_datetime', 'DESC');
        $invoice_info = $this->db->get()->result_array();

        // Calcul du montant total des factures
        $total_invoice_amount = 0;
        foreach ($invoice_info as $row) {
            $invoice_entries = json_decode($row['invoice_entries']);
            foreach ($invoice_entries as $invoice_entry) {
                $total_invoice_amount += floatval($invoice_entry->amount);
            }
        }

        // Récupération des examens avec filtre receptionist_id s'il est défini
        $this->db->select('examen.*, patient.name AS name, patient.prenom AS prenom, receptionist.name AS receptionist_name');
        $this->db->from('examen');
        $this->db->join('patient', 'patient.patient_id = examen.patient_id');
        $this->db->join('receptionist', 'receptionist.receptionist_id = examen.receptionist_id', 'left'); // Joindre la table receptionist
        if ($receptionist_id) {
            $this->db->where('examen.receptionist_id', $receptionist_id);
        }
        $this->db->order_by('examen.creation_time', 'DESC');
        $examen_info = $this->db->get()->result_array();

        // Calcul du montant total des examens
        $total_examen_amount = 0;
        foreach ($examen_info as $row) {
            $examen_entries = json_decode($row['examen_entries']);
            foreach ($examen_entries as $examen_entry) {
                $total_examen_amount += floatval($examen_entry->montant);
            }
        }

        // Calcul du montant total des décaissements
        $this->db->select('montant');
        $this->db->from('decaissement');

        if ($receptionist_id) {
            $this->db->where('decaissement.receptionist_id', $receptionist_id);
        }

        $decaissement_info = $this->db->get()->result_array();

        $total_decaissement_amount = 0;
        foreach ($decaissement_info as $row) {
            $total_decaissement_amount += floatval($row['montant']);
        }

        // Calcul du total combiné des factures et examens
        $total_combined_amount = $total_invoice_amount + $total_examen_amount - $total_decaissement_amount;

            // Initialiser le PDF
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);

            // Fonction d'entête
            function header_value($pdf, $system_name, $contact, $address, $system_email, $system_nif) {
                $pdf->SetFont('times', '', 10);
                $pdf->Image('uploads/logo.jpg', 130, 6, 75, 50);
                $x = 10;
                $y = 15;
                $width = 190;
                $height = 8;
                $pdf->Rect($x, $y, $width, $height * 4);
                $pdf->SetXY($x, $y);
                $pdf->SetY(6);
                $pdf->setFillColor(230, 230, 230);
                $pdf->Ln();
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(125, $height, '', 0, 0, 'L');
                $pdf->Ln();
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(65, $height, 'Contact:' . ' ' . $contact, 0, 0, 'R');
                $pdf->Ln();
                $pdf->SetFont('times', 'B', 12);
                $pdf->Cell(70, $height, $address, 0, 0, 'R');
                $pdf->Ln();
                $pdf->SetFont('times', 'B', 12);
                $pdf->Cell(65, $height, $system_email, 0, 1, 'R');
                $pdf->Ln(0);
                $pdf->SetFont('times', 'B', 12);
                $pdf->Cell(65, $height, $system_nif, 0, 1, 'R');
                $pdf->Ln();
            }

            // Appel de la fonction d'entête
            header_value($pdf, $system_name, $contact, $address, $system_email, $system_nif);
            $pdf->SetY(50);

            // Titre du document
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 10, 'Rapport des consultations et examens', 1, 1, 'C');
            $pdf->Ln(0);

            // Table des factures
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 10, utf8_decode('N°'), 1);
            //$pdf->Cell(25, 10, utf8_decode('Créer par:'), 1);
            $pdf->Cell(45, 10, 'Patient', 1);
            $pdf->Cell(38, 10, 'Date', 1, 0, 'C');
            $pdf->Cell(55, 10, 'Prestation', 1);
            $pdf->Cell(25, 10, 'Montant', 1, 0, 'C');
            $pdf->Cell(17, 10, 'Statut', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 10);
            $i = 0;

            foreach ($invoice_info as $row) {
                $i++;
                $invoice_entries = json_decode($row['invoice_entries']);
                $rowCount = count($invoice_entries); // Nombre de prestations

                // Calculer la hauteur totale de la cellule en fonction du nombre de prestations
                $cellHeight = $rowCount * 10; // Chaque ligne occupe 10 unités de hauteur

                // Vérifier si l'on dépasse la hauteur de la page avant d'ajouter les nouvelles cellules
                if ($pdf->GetY() + $cellHeight > 270) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 10);
                }

                // Stocker la position actuelle du curseur
                $currentX = $pdf->GetX();
                $currentY = $pdf->GetY();

                // Cellule pour le numéro (correspond à la hauteur totale des prestations)
                $pdf->MultiCell(10, $cellHeight, $i, 1);

                // Positionner le curseur pour la cellule suivante (nom et prénom)
                $pdf->SetXY($currentX + 10, $currentY);
                $pdf->MultiCell(45, $cellHeight, $row['name'] . ' ' . $row['prenom'], 1);

                // Positionner le curseur pour la cellule suivante (date)
                $pdf->SetXY($currentX + 55, $currentY);
                $pdf->MultiCell(38, $cellHeight, $row['creation_datetime'], 1);

                // Initialiser les coordonnées de départ pour les prestations et montants
                $startX = $currentX + 93;
                $startY = $pdf->GetY() - $cellHeight;

                // Parcourir les prestations et les montants
                foreach ($invoice_entries as $index => $invoice_entry) {
                    // Cellule pour chaque prestation (une ligne par prestation)
                    $pdf->SetXY($startX, $startY + $index * 10);
                    $pdf->Cell(55, 10, utf8_decode($invoice_entry->description), 1);

                    // Cellule pour chaque montant correspondant
                    $pdf->SetXY($startX + 55, $startY + $index * 10);
                    $pdf->Cell(25, 10, $invoice_entry->amount, 1, 0, 'C');
                }

                // Cellule pour le statut (correspond à la hauteur totale des prestations)
                $pdf->SetXY($startX + 80, $startY);
                $pdf->MultiCell(17, $cellHeight, utf8_decode($row['status']), 1);

                // Avancer à la ligne suivante uniquement après avoir traité toutes les prestations
                 $pdf->SetY($currentY + $cellHeight);
                
                }


                // Table des examens
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(10, 10, utf8_decode('N°'), 1);
                $pdf->Cell(45, 10, 'Patient', 1);
                $pdf->Cell(38, 10, 'Date', 1, 0, 'C');
                $pdf->Cell(55, 10, 'Examen', 1);
                $pdf->Cell(25, 10, 'Montant', 1, 0, 'C');
                $pdf->Cell(17, 10, 'Statut', 1);
                $pdf->Ln();

                $pdf->SetFont('Arial', '', 10);
                $i = 0;
                foreach ($examen_info as $row) {
                $i++;
                $examen_entries = json_decode($row['examen_entries']);
                $rowCount = count($examen_entries); // Nombre de prestations

                // Calculer la hauteur totale de la cellule en fonction du nombre de prestations
                $cellHeight = $rowCount * 10; // Chaque ligne occupe 10 unités de hauteur

                // Vérifier si l'on dépasse la hauteur de la page avant d'ajouter les nouvelles cellules
                if ($pdf->GetY() + $cellHeight > 270) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 10);
                }

                // Stocker la position actuelle du curseur
                $currentX = $pdf->GetX();
                $currentY = $pdf->GetY();

                // Cellule pour le numéro (correspond à la hauteur totale des prestations)
                $pdf->MultiCell(10, $cellHeight, $i, 1);

                // Cellule pour le nom et prénom du patient
                $pdf->SetXY($pdf->GetX() + 10, $pdf->GetY() - $cellHeight);
                $pdf->MultiCell(45, $cellHeight, $row['name'] . ' ' . $row['prenom'], 1);

                // Cellule pour la date d'examen
                $pdf->SetXY($pdf->GetX() + 55, $pdf->GetY() - $cellHeight);
                $pdf->MultiCell(38, $cellHeight, $row['creation_time'], 1);

                // Initialiser les coordonnées de départ pour les prestations et montants
                $startX = $pdf->GetX() + 93;
                $startY = $pdf->GetY() - $cellHeight;

                // Parcourir les prestations et les montants
                foreach ($examen_entries as $index => $examen_entry) {
                    // Cellule pour chaque prestation (une ligne par prestation)
                    $pdf->SetXY($startX, $startY + $index * 10);
                    $pdf->Cell(55, 10, utf8_decode($examen_entry->description), 1);

                    // Cellule pour chaque montant correspondant
                    $pdf->SetXY($startX + 55, $startY + $index * 10);
                    $pdf->Cell(25, 10, $examen_entry->montant, 1, 0, 'C');
                }

                // Cellule pour le statut (correspond à la hauteur totale des prestations)
                $pdf->SetXY($startX + 80, $startY);
                $pdf->MultiCell(17, $cellHeight, utf8_decode($row['status']), 1);
                // Se positionner pour la prochaine ligne
               $pdf->SetY($currentY + $cellHeight);
            }


            // Ajouter le montant total des factures et des examens au pied de page
            $pdf->SetY(-60);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 7, 'Total des consultations: ' . $total_invoice_amount .' FCFA ', ',', ' ' . ' ' . $currency_symbol, 0, 1, 'R');
            $pdf->Cell(0, 7, 'Total des examens: ' . $total_examen_amount .' FCFA ', ',', ' ' . ' ' . $currency_symbol, 0, 1, 'R');
            // Ajouter le montant total des décaissements au pied de page
            $pdf->Cell(0, 7, utf8_decode('Total des décaissements: ') . $total_decaissement_amount .' FCFA ', ',', ' ' . ' ' . $currency_symbol, 0, 1, 'R');
            $pdf->Cell(0, 7, utf8_decode('Total net: ') . $total_combined_amount .' FCFA ', ',', ' ' . ' ' . $currency_symbol, 0, 1, 'R');

            // Fonction de pied de page
            function footer_value($pdf, $authorised_name) {
                $pdf->SetY(-28);
                $pdf->SetFont('Arial', 'I', 12);
                $pdf->Cell(0, 7, utf8_decode('Imprimé par: ') . utf8_decode($authorised_name) . ' le ' . utf8_decode(date('d-m-Y à H:i:s')), 0, 0, 'L');
                $pdf->Cell(0, 7, 'Page ' . $pdf->PageNo() . '', 0, 0, 'R');
            }

            // Appel de la fonction de pied de page
            footer_value($pdf, $authorised_name);

            // Afficher le PDF
            $pdf->Output();
        } else {
            // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
            redirect('login', 'refresh');
        }
    }
}
?>
