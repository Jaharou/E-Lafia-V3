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
			<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th><?php echo get_phrase('patient');?></th>
								<th><?php echo get_phrase('test'); ?></th>
								<th><?php echo get_phrase('resultat');?></th>
								<th><?php echo get_phrase('groupe_sanguin'); ?></th>
								<th><?php echo get_phrase('statut_examen');?></th>
								<th><?php echo get_phrase('date-examen');?></th>
								<th class="col-md-2"><?php echo get_phrase('options');?></th>
							</tr>
						</thead>
						    <tbody>							
								<?php foreach ($examen_info as $row): ?>   
									<tr>
										<td>
					                    <?php
					                    $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
					                    $prenom = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->prenom;
					                    echo $name.' '.$prenom;
					                    ?>
					                    </td>
					                    <td> 
					                    <?php
					                    $examen_entries = json_decode($row['examen_entries']);
					                    foreach ($examen_entries as $examen_entry) {
					                        echo $examen_entry->description;
					                        echo " "; } ?>
					                	</td>
										<td> 
					                    <?php
					                    $examen_entries = json_decode($row['examen_entries']);
					                    foreach ($examen_entries as $examen_entry) {
					                        echo $examen_entry->resultat;
					                        echo " "; } ?>
					                	</td>
										<td><?php echo $row['blood_examen'] ?></td>
										<td><?php echo $row['statut_examen']?></td>
										<td><?php echo $row['creation_time'] ?></td>
										<td>
											<a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>InvoiceExamenComptable/examenComptable_print/<?php echo $row['id_examen']; ?>" title="Impression">
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
</div>
<?php 
$output .= ob_get_clean();
echo $output;