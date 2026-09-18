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
                <h3>Paiement pharmacie</h3>
                <div class="d-flex justify-content-between ">
                <!--<a class="btn btn-primary btn-wide icon-only" href="</?php echo base_url(); ?>JournalVenteAdmin/journalAdmin_vente_print/</?php echo $row['vente_id']; ?>" title="Journal de paiements">
                    Imprimer</a>-->
                </div>
                <br>

                <?php
                    
                // Exécuter une requête pour récupérer les consultations
                $consultations = $this->db->query("SELECT * FROM vente 
                   ")->result_array();

                // Compter le nombre total de consultations
                $totalVentes = 0;
                foreach ($consultations as $row) {
                    $entries = json_decode($row['invoice_ventes'], true);
                    if (is_array($entries)) {
                        $totalVentes += count($entries);
                        }
                    }
                ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('total-ventes') ?></span>
                        <span class="number counter"><?php echo ($totalVentes); ?></span>
                        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                    </a>
                </div>

                <?php
                        
                // Exécuter une requête pour récupérer les ventes
                $ventes = $this->db->query("SELECT * FROM vente 
                    ")->result_array();

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
                ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('montant-total-ventes') ?></span>
                        <span class="number counter"><?php echo ($totalPtotal).'F'; ?></span>
                        <span class="bg-icon"><i class="fa fa-money"></i></span>
                    </a>
                </div>  
                <br>

    			<div style="clear:both;"></div>	
    			<div class="panel-body p-20">
                <table id="example-three" class="display table table-striped table-bordered" cellspacing="0" width="100%">
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
                
                // Récupération des informations des ventes pour la période de 24 heures
                $this->db->select('vente.*, patient.name, patient.prenom');
                    $this->db->from('vente');
                    $this->db->join('patient', 'patient.patient_id = vente.patient_id');
                    $this->db->order_by('vente.date_vente', 'DESC');
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
                        $total_vente = 0;
                        foreach ($invoice_ventes as $vente_entry) {
                            //echo $vente_entry->Ptotal . " ";
                            $total_vente += $vente_entry->Ptotal;
                        }
                         echo $total_vente;
                        ?>
                    </td>
                </tr>                           
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align: right;">Total:</th>
                    <th></th>
                </tr>
            </tfoot>
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




