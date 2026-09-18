<?php
/* 	
 * 	Tamplate: Manage Blood Donor
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
			<a href="<?php echo base_url(); ?>receptionist/decaissement_crud/add"
             class="btn bg-black btn-wide icon-only pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?php echo get_phrase('ajouter'); ?>
        	</a>
				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
                    <tr>
                        <th><?php echo get_phrase("N°-d'ordre"); ?></th>
                        <th><?php echo get_phrase('numéro'); ?></th>
                        <th><?php echo get_phrase('date'); ?></th>
                        <th><?php echo get_phrase('tiers/patient'); ?></th>
                        <th><?php echo get_phrase('créer-par'); ?></th>
                        <th><?php echo get_phrase('motif'); ?></th>
                        <th><?php echo get_phrase('type'); ?></th>
                        <th><?php echo get_phrase('montant'); ?></th>
                        <th><?php echo get_phrase('mode-de-paiement'); ?></th>
                        <th><?php echo get_phrase('option'); ?></th>
                    </tr>
            	</thead>
			    <tbody>							
					<?php 
					$i = 0;
					foreach ($decaissement_info as $row): $i++; ?>   
					<tr>
						<td><?php echo $i ?></td>
	        			<td><?php echo $row['decaissement_number'] ?></td>
						<td><?php echo $row['date_decaissement'] ?></td>
	                    <td><?php echo $row['libelle_decaiss'] ?></td>
	                    <td><?php $name = $this->db->get_where('receptionist' , array('receptionist_id' => $row['receptionist_id'] ))->row()->name;
		                    echo $name;
		                    ?>
		                </td>
						<td><?php echo $row['motif'] ?></td>
						<td><?php echo $row['type'] ?></td>
						<td><?php echo $row['montant']?></td>
						<td><?php echo $row['mode'] ?></td>
						<td>
							<a href="<?php echo base_url(); ?>receptionist/decaissement_crud/edit/<?php echo $row['decaissement_id']; ?>" class="btn btn-success btn-rounded icon-only">
										<i class="fa fa-pencil"></i>
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
<?php 
$output .= ob_get_clean();
echo $output;