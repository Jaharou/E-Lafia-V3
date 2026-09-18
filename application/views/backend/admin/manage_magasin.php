<?php
/* 	
 * 	Tamplate: Manage Pharmacist
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
			<a href="<?php echo base_url(); ?>admin/magasin_crud/add" class="btn btn-info btn-wide pull-right"> <i class="fa fa-plus"></i>
				<?php echo get_phrase('ajouter un magasinier'); ?>
			</a>
				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>									
								<th><?php echo get_phrase('nom');?></th>
								<th><?php echo get_phrase('prénom');?></th>
								<th><?php echo get_phrase('téléphone');?></th>
								<th><?php echo get_phrase('adresse');?></th>
								<th><?php echo get_phrase('email');?></th>
								
								
								<th><?php echo get_phrase('options');?></th>
							</tr>
						</thead>
						    <tbody>										
								<?php foreach ($magasin_info as $row): ?>   
									<tr>
										
										<td><?php echo $row['name']?></td>
										<td><?php echo $row['prenom_magasinier']?></td>
										<td><?php echo $row['phone_magasinier']?></td>
										<td><?php echo $row['adres_magasinier']?></td>
										<td><?php echo $row['email']?></td>
										
										
										<td>
								<a href="<?php echo base_url(); ?>admin/magasin_crud/edit/<?php echo $row['magasin_id']; ?>" class="btn btn-success btn-rounded icon-only" title="Modifier">
							  <i class="fa fa-pencil"></i>
										</a> 
										
							<a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>PharmacistPrint/pharmacist_fpdf/<?php echo $row['magasin_id']; ?>" title="Impression">
							<i class="fa fa-print"></i>			
										</a>                          
							<a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>admin/magasin/delete/<?php echo $row['magasin_id']; ?>');" title="Supprimer">
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
<?php 
$output .= ob_get_clean();
echo $output;