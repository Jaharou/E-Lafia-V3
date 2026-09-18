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
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		    <a class="dashboard-stat bg-primary" href="#">
		        <span class="name"><?php echo get_phrase('Produits-total-ajoutés-pour-cette-semaine') ?></span>
		        <span class="number counter">
		            <?php 
		                $query = $this->db->query("
		                    SELECT SUM(qte_prod) AS total 
		                    FROM medicine 
		                    WHERE STR_TO_DATE(date_commande, '%d-%m-%Y') >= DATE_SUB(NOW(), INTERVAL 168 HOUR) ");
		                echo $query->result_array()[0]['total'];
		            ?>
		        </span>
		        <span class="bg-icon"><i class="fa fa-medkit"></i></span>
		    </a>
		</div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a class="dashboard-stat bg-primary" href="#">
                <span class="name"><?php echo get_phrase('montant-total-de-produits') ?></span>
                <span class="number counter">	
                	<?php 
		                $query = $this->db->query("
		                    SELECT SUM(amount) AS total 
		                    FROM medicine 
		                    WHERE STR_TO_DATE(date_commande, '%d-%m-%Y') >= DATE_SUB(NOW(), INTERVAL 168 HOUR) ");
		                echo $query->result_array()[0]['total'];
            		?>
                </span>
                <span class="bg-icon"><i class="fa fa-money"></i></span>
            </a>
        </div>

	    <div class="col-md-12">
			<div class="panel">
				<button onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/add_medicine/');" class="btn btn-primary pull-right" style= "width:140px; margin-top: 20px;">
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
						<th><?php echo get_phrase('quantité'); ?></th>
						<th><?php echo get_phrase('montant'); ?></th>
						<th><?php echo get_phrase("date"); ?></th>
						<th><?php echo get_phrase('statut'); ?></th>
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
						    <td><?php echo $row['amount'] ?></td>
						    <td><?php echo $row['date_commande'] ?></td>
							<td><?php echo $row['status'] ?></td>
							<td>
								<a  onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/edit_medicine/<?php echo $row['medicine_id'] ?>');" 
									 class="btn btn-primary btn-sm btn-icon icon-left" title="Modifier"><i class="fa fa-pencil"></i>
								</a>
								<a class="btn btn-danger btn-sm btn-icon" href="#" onclick="confirm_modal('<?php echo base_url(); ?>pharmacist/medicine/delete/<?php echo $row['medicine_id']; ?>');" title="Supprimer"><i class="fa fa-trash-o"></i>
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