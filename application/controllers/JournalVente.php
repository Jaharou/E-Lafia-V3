<?php
if (!defined('BASEPATH')) {
    exit('Direct script access denied.');
}

class JournalVente extends CI_Controller {

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

    public function journal_vente_print() {
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

            // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Créer un objet DateTime pour maintenant
            $now = new DateTime();

            // Définir les heures de début et de fin
            $start_of_day = new DateTime();
            $end_of_day = new DateTime();

            // Définir le début et la fin de la période en fonction de l'heure actuelle
            if ($now->format('H') >= 9) {
                $start_of_day->setTime(9, 0);
                $end_of_day->setTime(9, 0)->modify('+1 day');
            } else {
                $start_of_day->setTime(9, 0)->modify('-1 day');
                $end_of_day->setTime(9, 0);
            }

            $start_of_day_mysql = $start_of_day->format('Y-m-d H:i:s');
            $end_of_day_mysql = $end_of_day->format('Y-m-d H:i:s');

            // Récupération des informations des ventes pour la période de 24 heures
            $this->db->select('vente.*, patient.name, patient.prenom');
            $this->db->from('vente');
            $this->db->join('patient', 'patient.patient_id = vente.patient_id');
            $this->db->where("STR_TO_DATE(vente.date_vente, '%d-%m-%Y %H:%i:%s') >=", $start_of_day_mysql);
            $this->db->where("STR_TO_DATE(vente.date_vente, '%d-%m-%Y %H:%i:%s') <", $end_of_day_mysql);
            $this->db->order_by('vente.date_vente', 'DESC');
            $vente_info = $this->db->get()->result_array();

            // Calculer le montant total des factures
            $total_vente_Ptotal = 0;
            foreach ($vente_info as $row) {
                $invoice_ventes = json_decode($row['invoice_ventes']);
                foreach ($invoice_ventes as $vente_entry) {
                    $total_vente_Ptotal += floatval($vente_entry->Ptotal);
                }
            }

            // Initialiser le PDF
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);

            // Fonction d'entête
            function header_value($pdf, $system_name, $contact, $address, $system_email, $system_nif) {
                $pdf->SetFont('times', '', 10);
     $pdf->Image('uploads/logo.jpg', 158, 17, 41, 27);                $x = 10;
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
                $pdf->Cell(0, 10, 'Rapport des ventes de pharmacie', 1, 1, 'C');
                $pdf->Ln(0);

                // Table des factures
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(10, 10, 'N', 1);
                $pdf->Cell(40, 10, 'Patient', 1);
                $pdf->Cell(36, 10, 'Date', 1, 0, 'C');
                $pdf->Cell(50, 10, 'Produit', 1);
                //$pdf->Cell(23, 10, 'Prix unitaire', 1);
                $pdf->Cell(17, 10, utf8_decode('Quantité'), 1);
                $pdf->Cell(20, 10, 'Montant', 1, 0, 'C');
                $pdf->Cell(17, 10, 'Statut', 1);
                $pdf->Ln();

                $pdf->SetFont('Arial', '', 10);
                $i = 0;
                foreach ($vente_info as $row) {
                $i++;
                $invoice_ventes = json_decode($row['invoice_ventes']);
                $rowCount = count($invoice_ventes); // Nombre de prestations

                // Calculer la hauteur totale de la cellule en fonction du nombre de prestations
                $cellHeight = $rowCount * 10; // Chaque ligne occupe 10 unités de hauteur

                if ($pdf->GetY() + $cellHeight > 270) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 10);
                }

                // Stocker la position actuelle du curseur
                $currentX = $pdf->GetX();
                $currentY = $pdf->GetY();

                // Cellule pour le numéro (correspond à la hauteur totale des prestations)
                $pdf->MultiCell(10, $cellHeight, $i, 1);

                $pdf->SetXY($currentX + 10, $currentY);
                $pdf->Cell(40, $cellHeight, $row['name'] . ' ' . $row['prenom'], 1);

                // Positionner le curseur pour la cellule suivante (date)
                $pdf->SetXY($currentX + 50, $currentY);
                $pdf->MultiCell(36, $cellHeight, $row['date_vente'], 1);

                // Initialiser les coordonnées de départ pour les prestations et montants
                $startX = $currentX + 86;
                $startY = $currentY;

                // Parcourir les prestations et les montants
                
                foreach ($invoice_ventes as $index => $vente_entry) {  
                    $invoice_ventes = json_decode($row['invoice_ventes']);

                    foreach ($invoice_ventes as $index => $vente_entry) {
                        
                        //$total_vente_Ptotal += floatval($vente_entry->Ptotal);

                        // Récupérer le nom du produit en fonction de l'ID du produit
                        $medicine = $this->db->get_where('medicine', array('medicine_id' => $vente_entry->produit))->row();
                        
                        // Vérifier si le produit existe dans la base de données
                        if ($medicine) {
                            // Remplacer l'ID par le nom du produit
                            $vente_entry->produit = $medicine->name;
                        } else {
                            // Gérer le cas où l'ID du produit n'existe pas
                            $vente_entry->produit = "Produit inconnu";
                        }

                        // Affichage dans le PDF
                        $pdf->SetXY($startX, $startY + $i * 10);

                        $pdf->SetXY($startX, $startY + $index * 10);
                        $pdf->Cell(50, 10, utf8_decode($vente_entry->produit), 1);  // Afficher le nom du produit

                        // Cellule pour chaque montant correspondant
                        /*$pdf->SetXY($startX + 45, $startY + $index * 10);
                        $pdf->Cell(25, 10, $vente_entry->amount, 1, 0, 'C');*/

                        $pdf->SetXY($startX + 50, $startY + $index * 10);
                        $pdf->Cell(17, 10, utf8_decode($vente_entry->qte_vente), 1, 0, 'C');

                        // Cellule pour chaque montant correspondant
                        //$pdf->SetXY($startX + 87, $startY + $i * 10);

                        $pdf->SetXY($startX + 67, $startY + $index * 10);
                        $pdf->Cell(20, 10, $vente_entry->Ptotal, 1, 0, 'C');
                    }
                }

                // Cellule pour le statut (correspond à la hauteur totale des prestations)
                $pdf->SetXY($startX + 87, $startY);
                $pdf->MultiCell(17, $cellHeight, utf8_decode($row['status']), 1);

                // Avancer à la ligne suivante uniquement après avoir traité toutes les prestations
                $pdf->SetY($currentY + $cellHeight);
                }

                // Ajouter le montant total des factures et des examens au pied de page
                $pdf->SetY(-40);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 10, 'Montant total des ventes: ' . $total_vente_Ptotal, ',', ' ' . ' ' . $currency_symbol, 0, 1, 'R');
                
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
