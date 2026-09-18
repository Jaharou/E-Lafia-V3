<?php
/* 	
 * 	Tamplate: Notice
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
		<head>
		    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
		    >
		    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
		</head>
		<?php $output = ''; ?>
		<?php ob_start(); $today=date('Y-m-d'); ?>
		<div class="row">         
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-danger" href="<?php echo base_url(); ?>magasin/stock">
                            <span class="name"><?php echo get_phrase('total-de-produits-reçu') ?></span>
                            <span class="number counter"><?php echo $this->db->query("SELECT COUNT(*) AS total FROM stock")->result_array()[0]['total']; ?></span>
                            <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
                        </a>
                    </div>
                    <?php
					// Récupérer la date d'aujourd'hui
					$today = date('d-M-Y');
					// Récupérer les données JSON pour la date d'aujourd'hui
					$invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente WHERE DATE(STR_TO_DATE(date_vente, '%d-%b-%Y')) = CURDATE()")->result_array();
					// Décodez et traitez les données JSON pour compter le nombre de ventes du jour
					$totalVentes = 0;
					foreach ($invoice_ventes as $row) {
					    $data = json_decode($row['invoice_ventes'], true);
					    // Vérifier si $data est un tableau et contient plusieurs ventes
					    if (is_array($data)) {
					        $totalVentes += count($data);
					    }
					}
					?>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					    <a class="dashboard-stat bg-success" href="#">
					        <span class="name"><?php echo get_phrase('vente-par-jour') ?></span>
					        <span class="number counter"><?php echo $totalVentes; ?></span>
					        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
					    </a>
					</div>
                    <?php
					// Récupérer la date d'aujourd'hui
					/*$today = date('d-M-Y');
					// Récupérer les données JSON pour la date d'aujourd'hui
					$invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente WHERE DATE(STR_TO_DATE(date_vente, '%d-%b-%Y')) = CURDATE()")->result_array();
					// Décodez et traitez les données JSON pour calculer le total pour la date d'aujourd'hui
					$total = 0;
					foreach ($invoice_ventes as $row) {
					    $data = json_decode($row['invoice_ventes'], true); 
					    // Vérifier si $data est un tableau et contient plusieurs ventes
					    if (is_array($data)) {
					        foreach ($data as $vente) {
					            // Supposons que chaque vente dans le tableau JSON contienne une clé 'Ptotal'
					            if (isset($vente['Ptotal'])) {
					                $total += $vente['Ptotal'];
					            }
					        }
					    }
					}*/
					?>
					<!--<div style="margin-top:0px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					    <a class="dashboard-stat bg-success" href="#">
					        <span class="name"></?php echo get_phrase('Montant-total-pour-aujourd\'hui') ?></span>
					        <span class="number counter"></?php echo $total; ?></span>
					        <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
					    </a>
					</div>-->
					<?php
					// Récupérer toutes les entrées de vente
					$invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente")->result_array();
					$totalVentes = 0;
					foreach ($invoice_ventes as $row) {
					    $data = json_decode($row['invoice_ventes'], true);
					    if (is_array($data)) {
					        $totalVentes += count($data);
					    }
					}
					?>
					<div style="margin-top:0px" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
					        <span class="name"><?php echo get_phrase('vente-total') ?></span>
					        <span class="number counter"><?php echo $totalVentes; ?></span>
					        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
					    </a>
					</div>
                    </div>                  
					<br>
					<div class="row">
					    <div class="col-md-12">
							<div class="panel">		
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
								<th><?php echo get_phrase('options'); ?></th>
							</tr>
						</thead>
						    <tbody>								
							<?php
							$this->db->order_by('vente_id', 'DESC');
					            $this->db->limit(10);
					            $vente_info   = $this->db->get('vente')->result_array();
									foreach ($vente_info as $row): ?>   
								<tr>
									<td><?php echo $row['vente_id'] ?></td>
									<td> 
					                <?php
					                $invoice_ventes = json_decode($row['invoice_ventes']);
					                foreach ($invoice_ventes as $invoice_entry) {
					                    echo $invoice_entry->description;
					                    echo " "; } ?>
					                </td>
					                <td><?php echo $row['dosage'] ?></td>
									<td> 
					                <?php
					                $invoice_ventes = json_decode($row['invoice_ventes']);
					                foreach ($invoice_ventes as $invoice_entry) {
					                    echo $invoice_entry->amount;
					                    echo " "; } ?>
					                </td>
									<td> 
					                <?php
					                $invoice_ventes = json_decode($row['invoice_ventes']);
					                foreach ($invoice_ventes as $invoice_entry) {
					                    echo $invoice_entry->qte;
					                    echo " "; } ?>
					                </td>
									<td> 
					                <?php
					                $invoice_ventes = json_decode($row['invoice_ventes']);
					                foreach ($invoice_ventes as $invoice_entry) {
					                    echo $invoice_entry->Ptotal;
					                    echo " "; } ?>
					                </td>
								   <td><?php echo $row['date_vente'] ?></td>
								   
									<td>
									<a  onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/edit_stock/<?php echo $row['vente_id'] ?>');" 
										 class="btn btn-default btn-sm btn-icon icon-left" title="Modifier">
											<i class="fa fa-user-md"></i>
											Modifier
									</a>
										<a class="btn btn-danger btn-rounded icon-only" href="#"onclick="confirm_modal('<?php echo base_url(); ?>magasin/stock/delete/<?php echo $row['vente_id']; ?>');" title="Supprimer">
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