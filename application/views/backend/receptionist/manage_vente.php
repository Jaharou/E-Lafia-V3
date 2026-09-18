<?php
/* 
 * Template: Manage Invoice
 * @author : Raju Ahmed
 * Date    : 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
</head>
<?php $output = ''; ?>
<?php ob_start(); ?>
            
    <?php
        // Récupérer la somme des quantités des produits ajoutés (entrées)
        $queryEntree = $this->db->query("SELECT SUM(qte_produit) AS total_qte_entree FROM stock WHERE movement_type = 'entrée'");
        $totalQuantiteEntree = $queryEntree->row()->total_qte_entree;

        // Récupérer la somme des quantités des produits sortis (ventes)
        $querySortie = $this->db->query("SELECT SUM(qte_produit) AS total_qte_sortie FROM stock WHERE movement_type = 'sortie'");
        $totalQuantiteSortie = $querySortie->row()->total_qte_sortie;

        // Calculer la quantité de produits disponibles après les ventes
        $produitsDisponibles = $totalQuantiteEntree - $totalQuantiteSortie;

        // Afficher la quantité disponible (en évitant d'afficher un nombre négatif)
        //echo $produitsDisponibles > 0 ? $produitsDisponibles : 0;
    ?>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a class="dashboard-stat bg-primary" href="<?php echo base_url(); ?>magasin/stock">
            <span class="name"><?php echo get_phrase('produits-disponibles-à-vendre') ?></span>
            <span class="number counter">
                <?php 
                $result = $this->db->query("SELECT SUM(qte_produit) AS total FROM stock WHERE movement_type = 'sortie'")->result_array();
                $total_quantity_sortie = $result[0]['total'];
                echo $total_quantity_sortie ? $total_quantity_sortie : 0;
                ?>
            </span>
            <span class="bg-icon"><i class="fa fa-cart-plus"></i></span>
        </a>
    </div>

            <?php
            // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Récupérer la date et l'heure actuelles
            $currentDateTime = new DateTime();

            // Définir l'heure de début et de fin de la période de 24 heures (9h aujourd'hui à 9h demain)
            $startDateTime = clone $currentDateTime;
            $startDateTime->setTime(9, 0, 0);
            if ($currentDateTime < $startDateTime) {
                $startDateTime->modify('-1 day');
            }
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+1 day');

            // Formater les dates pour les requêtes SQL
            $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

            // Exécuter une requête pour récupérer les ventes dans la période de 24 heures
            $ventes = $this->db->query("SELECT invoice_ventes FROM vente 
                WHERE STR_TO_DATE(date_vente, '%d-%m-%Y %H:%i:%s') 
                BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();

            // Compter le nombre total de produits vendus
            $totalVentes = 0;
            foreach ($ventes as $row) {
                $entries = json_decode($row['invoice_ventes'], true);
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        $totalVentes += $entry['qte_vente']; // Assuming 'qte_vente' is the key for the quantity sold in your JSON data
                    }
                }
            }
            ?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <a class="dashboard-stat bg-primary" href="#">
                    <span class="name"><?php echo get_phrase('produits-vendus-24h') ?></span>
                    <span class="number counter"><?php echo $totalVentes; ?></span>
                    <span class="bg-icon"><i class="fa fa-medkit"></i></span>
                </a>
            </div>

            <div class="col-md-12">
            <div class="panel">
                <br>
                <a href="<?php echo base_url(); ?>receptionist/vente_crud/add"
                 class="btn btn-primary btn-rounded icon-only pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <?php echo get_phrase('ajouter'); ?>
                </a>
                <div style="clear:both;"></div> 
                    <div class="panel-body p-20">
            <?php
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
            $this->db->select('*');
            $this->db->from('vente');
            $this->db->where("STR_TO_DATE(date_vente, '%d-%m-%Y %H:%i:%s') BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
            $vente_info = $this->db->get()->result_array();
            ?>
            <br>

            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo get_phrase('N°'); ?></th>
                    <th><?php echo get_phrase('numéro-de-reçu'); ?></th>
                    <th><?php echo get_phrase('patient'); ?></th>
                    <th><?php echo get_phrase('produit'); ?></th>
                    <th><?php echo get_phrase('P-unitaire'); ?></th>
                    <th><?php echo get_phrase('quantité'); ?></th>
                    <th><?php echo get_phrase('montant-total'); ?></th>
                    <th><?php echo get_phrase('date-vente'); ?></th>  
                    <th class="col-md-2"><?php echo get_phrase('options'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $i=0;
            foreach ($vente_info as $row): $i++; ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $row['vente_number'] ?></td>
                    <td>
                        <?php
                        $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
                        $prenom = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->prenom;
                        echo $name.' '.$prenom;
                        ?>
                    </td>
                    <td>  
                        <?php 
                        $invoice_ventes = json_decode($row['invoice_ventes']);
                        foreach ($invoice_ventes as $vente_entry) {
                            // Récupérer le medicine_id depuis l'entrée
                            $medicine_id = $vente_entry->produit; 
                            // Effectuer une requête pour récupérer le nom du produit via son ID
                            $this->db->select('name');
                            $this->db->from('medicine');
                            $this->db->where('medicine_id', $medicine_id);
                            $query = $this->db->get();
                            $result = $query->row();
                            
                            // Vérifier si le produit a été trouvé
                            if ($result) {
                                $name = $result->name;
                                // Afficher le nom du produit
                                echo $name;
                            } else {
                                // Afficher un message si le produit n'est pas trouvé
                                echo "Produit non trouvé";
                            }
                            
                            echo " ";
                        }
                        ?>
                    </td>

                    <td> <?php $invoice_ventes = json_decode($row['invoice_ventes']);
                        foreach ($invoice_ventes as $vente_entry) {
                            echo $vente_entry->amount;
                            echo " "; } ?>
                    </td>
                    <td> 
                        <?php
                        $invoice_ventes = json_decode($row['invoice_ventes']);
                        foreach ($invoice_ventes as $vente_entry) {
                            echo $vente_entry->qte_vente;
                            echo " "; } ?>
                    </td>
                    <td> 
                        <?php
                        $invoice_ventes = json_decode($row['invoice_ventes']);
                        foreach ($invoice_ventes as $vente_entry) {
                            echo $vente_entry->Ptotal;
                            echo " "; } ?>
                    </td>
                   <td><?php echo $row['date_vente'] ?></td>
                    <td>
                        <!--<a 
                        href="</?php echo base_url(); ?>receptionist/vente_crud/edit/</?php echo $row['vente_id']; ?>" 
                            class="btn btn-success btn-rounded icon-only" title="Modifier">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </a>-->
                        <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceVenteReceptioniste/vente_print/<?php echo $row['vente_id']; ?>" title="Impression">
                            <i class="fa fa-print"> Imprimer</i> 
                        </a>                   
                        
                    </td>
                </tr>                           
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>              
    </div>              
</div>
<script>
    // Inverser l'ordre des lignes du tableau après le chargement de la page
    document.addEventListener("DOMContentLoaded", function () {
        var table = document.getElementById("example");
        var tbody = table.getElementsByTagName("tbody")[0];
        var rows = Array.from(tbody.getElementsByTagName("tr")).reverse();
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    });
</script>
