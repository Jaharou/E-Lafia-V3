<?php
/* 	
 * 	Tamplate: Manage Bed
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
	       </?php if($menu_check == 'from_mesure') : ?>
            <!--<a class="btn bg-primary btn-wide icon-only pull-right" onclick="showAjaxModal('</?php echo base_url(); ?>modal/popup/add_mesure/');">
             <i class="fa fa-plus-square"></i>
             </?php echo get_phrase('soins'); ?>
            </a>-->

    <?php if($menu_check == 'from_mesure') : ?>
        

		<a href="<?php echo base_url(); ?>nurse/mesure_crud/add" class="btn bg-black btn-wide pull-right"> <i class="fa fa-plus"></i>
				<?php echo get_phrase('ajouter'); ?>
		</a>
			
				<div style="clear:both;"></div>
				<hr>		
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>							
								<th><?php echo get_phrase('date');?></th>
								<th><?php echo get_phrase('patient');?></th>
								<th><?php echo get_phrase('poids');?></th>
								<th><?php echo get_phrase('taille');?></th>
								<th><?php echo get_phrase('temperature');?></th>
								<th><?php echo get_phrase('Tension_artérialle_diastolique');?></th>
                                <th><?php echo get_phrase('Tension_artérialle_systolique');?></th>
								<th><?php echo get_phrase('options');?></th>
							</tr>
						</thead>
						    <tbody>								
								<?php foreach ($mesure_info as $row): ?>   
									<tr>
										<td><?php echo $row['date_mesure'] ?></td>
										<td>
										<?php $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
										$prenom = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->prenom;
											echo $name.' '.$prenom;?>
									</td>
										<td><?php echo $row['motif'] ?></td>
										<td>
										  <?php
											$name = $this->db->get_where('doctor', array('doctor_id' => $row['doctor_id']))->row()->name;
											echo $name;
											?>
										</td>
										<td><?php echo $row['observation'] ?></td>
										<td><?php echo $row['statut'] ?></td>
										<td><?php echo $row['statut'] ?></td>
										<td>
											<a  onclick="showAjaxModal('<?php echo base_url();?>modal/popup/edit_mesure/<?php echo $row['mesure_id']?>');" 
												class="btn btn-success btn-rounded icon-only">
													<i class="fa fa-pencil"></i>
											</a>
												<a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>nurse/mesure/delete/<?php echo $row['mesure_id']; ?>');">
													<i class="fa fa-trash-o"></i>
												</a>


										</td>
									</tr>
								<?php endforeach; ?>
						  </tbody>
						</table>				
			</div>
			<?php endif; ?>				
		</div>				
	</div>
</div>



<?php 
$output .= ob_get_clean();
echo $output;