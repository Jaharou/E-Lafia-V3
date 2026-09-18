<?php
/* 	
 * 	Tamplate: Manage Medicine
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
			<button onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/add_medicine/');" 
					class="btn btn-primary pull-right" style= width:140px;>
						<?php echo get_phrase('ajouter'); ?>
			</button>
			<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
					<tr>
					   <th><?php echo get_phrase('fournisseur'); ?></th>
					    <th><?php echo get_phrase('produit'); ?></th>
						<th><?php echo get_phrase('DCI'); ?></th>
						<th><?php echo get_phrase('dosage'); ?></th>
						<th><?php echo get_phrase('condition'); ?></th>
						<th><?php echo get_phrase('forme'); ?></th>
						<th><?php echo get_phrase('quantité-commandée'); ?></th>
						<th><?php echo get_phrase('montant-de-produit'); ?></th>
						<th><?php echo get_phrase('statut'); ?></th>
						<th><?php echo get_phrase("date-d'expiration"); ?></th>
						<th><?php echo get_phrase('options'); ?></th>
					</tr>
				</thead>
				    <tbody>								
						<?php foreach ($medicine_info as $row): ?>   
						<tr>
							<td>
		                    	<?php $name = $this->db->get_where('fournisseur' , array('fournisseur_id' => $row['fournisseur_id'] ))->row()->name;
		                    	echo $name;
		                    ?>
                            </td>
							<td><?php echo $row['name'] ?></td>
						    <td><?php echo $row['dci'] ?></td>
						    <td><?php echo $row['dosage'] ?></td>
						    <td><?php echo $row['condit'] ?></td>
						    <td><?php echo $row['forme'] ?></td>
						    <td><?php echo $row['qte_prod'] ?></td>
						    <td><?php echo $row['price'] ?></td>
							<td><?php echo $row['status'] ?></td>
							<td><?php echo $row['expiration_date'] ?></td>
							<td>
								<a  onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/edit_medicine/<?php echo $row['medicine_id'] ?>');" 
									 class="btn btn-default btn-sm btn-icon icon-left" title="Modifier"><i class="fa fa-user-md"></i>Modifier
								</a>
								<a class="btn btn-danger btn-sm btn-icon icon-left" href="#" onclick="confirm_modal('<?php echo base_url(); ?>doctor/medicine/delete/<?php echo $row['medicine_id']; ?>');" title="Supprimer"><i class="fa fa-user-md"></i>Supprimer
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