<?php
/* 	
 * 	Tamplate: Manage Department
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
			<a href="<?php echo base_url(); ?>admin/type_crud/add" class="btn btn-info btn-wide pull-right"> <i class="fa fa-plus"></i>
				<?php echo get_phrase('ajouter'); ?>
			</a>
				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th class="col-md-1"><?php echo get_phrase('n°'); ?></th>
								<th class="col-md-4" style="text-align: center;"><?php echo get_phrase('produits'); ?></th>
								<th class="col-md-2" style="text-align: center;"><?php echo get_phrase('Nombres'); ?></th>
								<th class="col-md-4" style="text-align: center;"><?php echo get_phrase('P. Unitaire'); ?></th>
								<th class="col-md-4" style="text-align: center;"><?php echo get_phrase('P. Total'); ?></th>
								<th class="col-md-3"><?php echo get_phrase('options'); ?></th>
							</tr>
						</thead>
						    <tbody>
								 <?php foreach ($type_info as $row): ?>   
										<tr>
											<td><?php echo $row['id_type'] ?></td>
											<td style="text-align:center;"><?php echo $row['produit'] ?></td>
											<td><?php echo $row['nbrs'] ?></td>
											<td><?php echo $row['prix'] ?></td>
											<td><?php echo $row['total'] ?></td>
											<td>
												<a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>admin/type/delete/<?php echo $row['id_type']; ?>');" title="Supprimer">
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