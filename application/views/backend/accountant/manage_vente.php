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
			<div style="clear:both;"></div>	
			<div class="panel-body p-20">
					<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
            <th><?php echo get_phrase('n°vente'); ?></th>
		    <th><?php echo get_phrase('produit'); ?></th>
            <th><?php echo get_phrase('dosage'); ?></th> 
		    <th><?php echo get_phrase('prix-unitaire'); ?></th>
		    <th><?php echo get_phrase('quantité'); ?></th>		
		    <th><?php echo get_phrase('montant-total'); ?></th>
			<th><?php echo get_phrase('date-vente'); ?></th>	
            <th class="col-sm-2"><?php echo get_phrase('options'); ?></th>
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
                foreach ($invoice_ventes as $invoice_entry){
                    echo $invoice_entry->description;
                    echo " "; } ?>
                </td>
                <td><?php echo $row['dosage'] ?></td>
                <td> 
                <?php
                $invoice_ventes = json_decode($row['invoice_ventes']);
                foreach ($invoice_ventes as $invoice_entry){
                    echo $invoice_entry->amount;
                    echo " "; } ?>
                </td>
				<td> 
                <?php
                $invoice_ventes = json_decode($row['invoice_ventes']);
                foreach ($invoice_ventes as $invoice_entry){
                    echo $invoice_entry->qte;
                    echo " "; } ?>
                </td>
				<td> 
                <?php
                $invoice_ventes = json_decode($row['invoice_ventes']);
                foreach ($invoice_ventes as $invoice_entry){
                    echo $invoice_entry->Ptotal;
                    echo " "; } ?>
                </td>
			   <td><?php echo $row['date_vente'] ?></td>
			   <td>
                    <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceVenteComptable/vente_print/<?php echo $row['vente_id']; ?>" title="Impression">
                        <i class="fa fa-print"></i>	
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




