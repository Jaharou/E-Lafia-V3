<?php
/* 	
 * 	Tamplate: Manage Patient
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
			<header style="color: dark; font-size: 23px;">Fournisseur</header>
			<!--<a href="</?php echo base_url(); ?>receptionist/patient_crud/add" class="btn bg-black btn-wide pull-right"> <i class="fa fa-plus"></i>
				</?php echo get_phrase('ajouter'); ?>
			</a>-->

          <a class="btn bg-black btn-wide icon-only pull-right" href="<?php echo base_url(); ?>magasin/fournisseur_crud/add">
		  <i class="fa fa-plus" aria-hidden="true"></i>
		  <?php echo get_phrase('ajouter'); ?>
		 </a>

				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>		
								<th><?php echo get_phrase('N°');?></th>					
								<th><?php echo get_phrase('nom');?></th>
								<th><?php echo get_phrase('contact');?></th>
								<th><?php echo get_phrase('adresse');?></th>
								<th><?php echo get_phrase('email');?></th>
								
								<th><?php echo get_phrase('options');?></th>
							</tr>
						</thead>
						    <tbody>
								<?php
								$i=0; 
								foreach ($fournisseur_info as $row): $i++; ?>
									<tr>
										<td><?php echo $i ?></td>
										<td><?php echo $row['name']?></td>
										<td><?php echo $row['contact']?></td>
										<td><?php echo $row['adresse']?></td>
										<td><?php echo $row['email']?></td>
						      
								<td>
								<a href="<?php echo base_url(); ?>magasin/fournisseur_crud/edit/<?php echo $row['fournisseur_id']; ?>" class="btn btn-success btn-rounded icon-only" title="Modifier">
							     <i class="fa fa-pencil"></i>
								</a> 
											 
								<a class="btn btn-danger btn-rounded icon-only" href="#"onclick="confirm_modal('<?php echo base_url(); ?>magasin/fournisseur/delete/<?php echo $row['fournisseur_id']; ?>');" title="Supprimer">
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
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[0, "desc"]],
        "fnDrawCallback": function(oSettings) {
            var table = $('.table').dataTable().api();
            var rows = table.rows({ page: 'current' }).nodes();
            var count = 1;
            
            // Inverse l'ordre des lignes
            rows.each(function() {
                $(this).find('td:first').text(count++);
            });
        }
    });
});
</script>-->

<?php 
$output .= ob_get_clean();
echo $output;