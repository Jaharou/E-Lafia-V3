<?php
/* 	
 * 	Tamplate: Manage Invoice
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<?php $output = ''; ?>
<?php ob_start(); ?>

<div class="row">

    <div class="col-md-12">
		<div class="panel">
         <?php 
            $vente_info   = $this->db->get('vente')->result_array();
                foreach ($vente_info as $row): ; ?>
                
                <?php endforeach; ?>
                <h3>Journal de paiement pharmacie</h3>
                <div class="d-flex justify-content-between ">
                <a class="btn btn-primary btn-wide icon-only" href="<?php echo base_url(); ?>JournalVente/journal_vente_print/<?php echo $row['vente_id']; ?>" title="Journal de paiements">
                    Imprimer</a>
                </div>
                <br>

                <?php
                    // Définir le fuseau horaire pour les heures correctes
                date_default_timezone_set('Africa/Niamey');

                // Obtenir la date et l'heure actuelles
                $currentDateTime = new DateTime();

                // Définir l'heure de début de la période de 24 heures
                $startDateTime = clone $currentDateTime;
                $startDateTime->setTime(9, 0, 0);

                // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
                if ($currentDateTime < $startDateTime) {
                    $startDateTime->modify('-1 day');
                }

                // Définir l'heure de fin de la période de 24 heures
                $endDateTime = clone $startDateTime;
                $endDateTime->modify('+1 day');

                // Formater les dates pour les requêtes SQL
                $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
                $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

                // Exécuter une requête pour récupérer les ventes dans la période de 24 heures
                $ventes = $this->db->query("SELECT * FROM vente 
                    WHERE STR_TO_DATE(date_vente, '%d-%m-%Y %H:%i:%s') 
                    BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();

                // Compter le nombre total de ventes
                $totalVentes = 0;
                foreach ($ventes as $row) {
                    $entries = json_decode($row['invoice_ventes'], true);
                    if (is_array($entries)) {
                        $totalVentes += count($entries);
                    }
                }
                ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('total-ventes-24h') ?></span>
                        <span class="number counter"><?php echo ($totalVentes); ?></span>
                        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                    </a>
                </div>

                <?php
                // Définir le fuseau horaire pour les heures correctes
                date_default_timezone_set('Africa/Niamey');

                // Obtenir la date et l'heure actuelles
                $currentDateTime = new DateTime();
                // Définir l'heure de début de la période de 24 heures
                $startDateTime = clone $currentDateTime;
                $startDateTime->setTime(9, 0, 0);

                // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
                if ($currentDateTime < $startDateTime) {
                    $startDateTime->modify('-1 day');
                }
                // Définir l'heure de fin de la période de 24 heures
                $endDateTime = clone $startDateTime;
                $endDateTime->modify('+1 day');
                // Formater les dates pour les requêtes SQL
                $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
                $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

                // Exécuter une requête pour récupérer les ventes dans la période de 24 heures
                $ventes = $this->db->query("SELECT * FROM vente 
                    WHERE STR_TO_DATE(date_vente, '%d-%m-%Y %H:%i:%s') 
                    BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();
                // Calculer le montant total des ventes
                $totalPtotal = 0;
                foreach ($ventes as $row) {
                    $entries = json_decode($row['invoice_ventes'], true);
                    if (is_array($entries)) {
                        foreach ($entries as $entry) {
                            if (isset($entry['Ptotal'])) {
                                $totalPtotal += floatval($entry['Ptotal']);
                            }
                        }
                    }
                }
                // Afficher le montant total des ventes
                ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('montant-total-ventes-24h') ?></span>
                        <span class="number counter"><?php echo ($totalPtotal).'F'; ?></span>
                        <span class="bg-icon"><i class="fa fa-money"></i></span>
                    </a>
                </div>  
                <br>

    			<div style="clear:both;"></div>	
    			<div class="panel-body p-20">
                <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo get_phrase('N°'); ?></th>
                        <th><?php echo get_phrase('créer-par'); ?></th>
                        <th><?php echo get_phrase('date-vente'); ?></th>
                        <th><?php echo get_phrase('patient'); ?></th>
                        <th><?php echo get_phrase('produit'); ?></th>
                        <th><?php echo get_phrase('prix-unitaire'); ?></th>
                        <th><?php echo get_phrase('quantité'); ?></th>
                        <th><?php echo get_phrase('montant-total'); ?></th>    
                    </tr>
                </thead>
                <tbody>
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

                $i=0;
                foreach ($vente_info as $row): $i++; ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $row['receptionist_id'] ?></td>
                    <td><?php echo $row['date_vente'] ?></td>
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
                            $name = $query->row()->name;
                            // Afficher le nom du produit
                            echo $name . " ";
                        }
                        ?>
                    </td>
                    <td> 
                        <?php
                        $invoice_ventes = json_decode($row['invoice_ventes']);
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
                        $total_amount = 0;
                        foreach ($invoice_ventes as $vente_entry) {
                            //echo $vente_entry->Ptotal;
                            $total_amount += $vente_entry->Ptotal;
                            echo " "; } 
                            echo $total_amount;
                            ?>
                    </td>
                </tr>                           
                <?php endforeach; ?>
            </tbody>
            </table>
	    </div>				
	</div>				
</div>		
</div>

<!--<script>
    // Inverser l'ordre des lignes du tableau après le chargement de la page
    document.addEventListener("DOMContentLoaded", function () {
        var table = document.getElementById("example");
        var tbody = table.getElementsByTagName("tbody")[1];
        var rows = Array.from(tbody.getElementsByTagName("tr")).reverse();
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    });
</script>-->


<?php 
$output .= ob_get_clean();
echo $output;




