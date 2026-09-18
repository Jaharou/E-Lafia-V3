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
		    <header style="color: dark; font-size: 23px;">Vente</header>
			<a href="<?php echo base_url(); ?>admin/vente_crud/add"
             class="btn bg-black btn-wide icon-only pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?php echo get_phrase('ajouter'); ?>
            </a>
			<div style="clear:both;"></div>	
			<div class="panel-body p-20">
					<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo get_phrase('N°'); ?></th>
            <th><?php echo get_phrase('numéro-de-reçu'); ?></th>
		    <th><?php echo get_phrase('produit'); ?></th>
            <th><?php echo get_phrase('p.-unitaire'); ?></th>
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
                        echo $vente_entry->qte;
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
                    <a 
                    href="<?php echo base_url(); ?>admin/vente_crud/edit/<?php echo $row['vente_id']; ?>" 
                        class="btn btn-success btn-rounded icon-only" title="Modifier">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceVente/vente_print/<?php echo $row['vente_id']; ?>" title="Impression">
                        <i class="fa fa-print"></i>	
                    </a>                   
                    <a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>admin/vente/delete/<?php echo $row['vente_id']; ?>');" title="Supprimer">
                        <i class="fa fa-trash-o"></i>
                    </a>
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




