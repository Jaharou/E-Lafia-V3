<?php
class StockController extends CI_Controller {

    public function check_stock() {
        // Initialiser la variable pour les ventes totales
        $totalVentes = 0;

        // Récupérer la somme des quantités des produits ajoutés (entrées)
        $querySortie = $this->db->query("SELECT SUM(qte_produit) AS total_qte_sortie FROM stock WHERE movement_type = 'sortie'");
        $totalQuantiteSortie = $querySortie->row()->total_qte_sortie;

        // Récupérer les ventes (quantité vendue) de la table `vente`
        $queryVentes = $this->db->query("SELECT invoice_ventes FROM vente");
        $ventes = $queryVentes->result_array();

        // Calculer le total des quantités vendues
        foreach ($ventes as $row) {
            $entries = json_decode($row['invoice_ventes'], true); // Décoder le champ JSON
            if (is_array($entries)) {
                foreach ($entries as $entry) {
                    // Vérifier que la clé 'qte' existe dans le JSON
                    if (isset($entry['qte'])) {
                        $totalVentes += $entry['qte']; // Ajouter la quantité vendue
                    }
                }
            }
        }

        // Vérifier si les ventes dépassent la quantité disponible
        if ($totalVentes > $totalQuantiteSortie) {
            // Définir un message d'erreur dans une session flashdata
            $this->session->set_flashdata('error_message', 'Quantité vendue dépasse la quantité disponible en stock. Opération annulée.');
            
            // Rediriger vers la page principale ou une autre page appropriée
            redirect('stockcontroller/index'); // Remplacez 'stockcontroller/index' par votre chemin réel
        } else {
            // Calculer la quantité de produits disponibles après les ventes
            $produitsDisponibles = $totalQuantiteSortie - $totalVentes;

            // Définir un message de succès dans une session flashdata pour afficher la quantité disponible
            $this->session->set_flashdata('produits_disponibles', $produitsDisponibles > 0 ? $produitsDisponibles : 0);
            
            // Rediriger vers la page principale ou une autre page appropriée
            redirect('stockcontroller/index'); // Remplacez 'stockcontroller/index' par votre chemin réel
        }
    }

    public function index() {
        // Charger la vue avec les messages de session
        $this->load->view('stock_view');
    }
}
