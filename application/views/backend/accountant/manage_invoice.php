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
		  <header style="color: dark; font-size: 23px;">Reçus</header>
			
         	<div style="clear:both;"></div>	
			<div class="panel-body p-20">
					<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th><?php echo get_phrase('N°'); ?></th>
            <th><?php echo get_phrase('numéro de reçu'); ?></th>
            <th><?php echo get_phrase('patient'); ?></th>
            <th><?php echo get_phrase('date_de_creation'); ?></th>
            <th><?php echo get_phrase('type_de_prestation'); ?></th>
            <th><?php echo get_phrase('désignation'); ?></th>
            <th><?php echo get_phrase('statut'); ?></th>
            <th><?php echo get_phrase("date d'échéance"); ?></th>	
            <th class="col-md-2"><?php echo get_phrase('options'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i=0;
        foreach ($invoice_info as $row): $i++; ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $row['invoice_number'] ?></td>
                <td>
                    <?php
                    $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
                    $prenom = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->prenom;
                    echo $name.' '.$prenom;
                    ?>
                </td>
                <td><?php echo $row['creation_datetime'] ?></td>
                

                <td>
    <table>
        <?php
        $titles = json_decode($row['title']);
        if (is_array($titles)) {
            foreach ($titles as $title) {
                echo "<tr><td>" . htmlspecialchars($row['title']) . "</td></tr>";
            }
        } else {
            echo "<tr><td>" . htmlspecialchars($titles) . "</td></tr>";
        }
        //echo "Raw title data: " . htmlspecialchars($row['title']);
        echo "<tr><td>" . htmlspecialchars($row['title']) . "</td></tr>";
        echo "";
        ?>
    </table>
</td>


                <td> 
                    <?php
                    $invoice_entries = json_decode($row['invoice_entries']);
                    foreach ($invoice_entries as $invoice_entry) {
                        echo $invoice_entry->description;
                        echo " ";
                    }
                    ?>
                </td>
                <td><?php echo $row['status'] ?></td>
                <td><?php echo $row['due_timestamp'] ?></td>
                <td>
                    <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceComptable/invoice_print/<?php echo $row['invoice_id']; ?>" title="Impression">
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




