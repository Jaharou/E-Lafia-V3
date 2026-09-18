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
<?php $output = ''; ?>
<?php ob_start(); ?>

<div class="row">
    <div class="col-md-12">
		<div class="panel">
		  <header style="color: dark; font-size: 23px;">Vente</header>
			
         <a class="btn bg-black btn-wide icon-only pull-right" onclick="ajoutVenteAjaxModal('<?php echo base_url(); ?>modal/popup/pharmacist/vente_add/add');">
		  <i class="fa fa-plus" aria-hidden="true"></i>
		  <?php echo get_phrase('ajouter'); ?>
		 </a>

				<div style="clear:both;"></div>	
			<div class="panel-body p-20">
					<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo get_phrase('n°vente'); ?></th>
		   <th><?php echo get_phrase('nom_produit'); ?></th>
		   <th><?php echo get_phrase('quantité_produit'); ?></th>
		   <th><?php echo get_phrase('montant_total'); ?></th>		
		    <th><?php echo get_phrase('dosage'); ?></th>
			<th><?php echo get_phrase('date_vente'); ?></th>	
            <th class="col-md-2"><?php echo get_phrase('options'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i=0;
        foreach ($vente_info as $row): $i++; ?>
            <tr>
                <td><?php echo $row['vente_number'] ?></td>
				<td> 
                    <?php
                    $invoice_ventes = json_decode($row['invoice_ventes']);
                    foreach ($invoice_ventes as $invoice_entry) {
                        echo $invoice_entry->description;
                        echo " ";
                    }
                    ?>
                </td>
				<td><?php echo $row['qte_produit'] ?></td>
				<td><?php echo $row['prix_total'] ?></td>
				<td><?php echo $row['dosage'] ?></td>
			   <td><?php echo $row['date_vente'] ?></td>
                <td>
                    <a onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/edit_vente/<?php echo $row['vente_id'] ?>');" 
                        class="btn btn-success btn-rounded icon-only" title="Modifier">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceVente/vente_print/<?php echo $row['vente_id']; ?>" title="Impression">
                        <i class="fa fa-print"></i>	
                    </a>                   
                    <a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>pharmacist/vente/delete/<?php echo $row['vente_id']; ?>');" title="Supprimer">
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
<footer>
    
</footer>

    
				
</div>

<script>
    // Inverser l'ordre des lignes du tableau après le chargement de la page
    document.addEventListener("DOMContentLoaded", function () {
        var table = document.getElementById("example");
        var tbody = table.getElementsByTagName("tbody")[1];
        var rows = Array.from(tbody.getElementsByTagName("tr")).reverse();
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    });
</script>



<?php 
$output .= ob_get_clean();
echo $output;




