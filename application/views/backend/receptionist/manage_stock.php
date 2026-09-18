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
	    <a class="dashboard-stat bg-primary" href="<?php echo base_url(); ?>receptionist/stock">
	        <span class="name"><?php echo get_phrase('stock-disponibles') ?></span>
	        <span class="number counter">
	            <?php
	            // Récupérer la somme des quantités des produits ajoutés (entrées)
	            $queryEntree = $this->db->query("SELECT SUM(qte_produit) AS total_qte_entree FROM stock WHERE movement_type = 'entrée'");
	            $totalQuantiteEntree = $queryEntree->row()->total_qte_entree;

	            // Récupérer la somme des quantités des produits sortis (ventes)
	            $querySortie = $this->db->query("SELECT SUM(qte_produit) AS total_qte_sortie FROM stock WHERE movement_type = 'sortie'");
	            $totalQuantiteSortie = $querySortie->row()->total_qte_sortie;

	            // Calculer la quantité de produits disponibles après les ventes
	            $produitsDisponibles = $totalQuantiteEntree - $totalQuantiteSortie;

	            // Afficher la quantité disponible (en évitant d'afficher un nombre négatif)
	            echo $produitsDisponibles > 0 ? $produitsDisponibles : 0;
	            ?>
	        </span>
	        <span class="bg-icon"><i class="fa fa-shopping-cart"></i></span>
	    </a>
	</div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a class="dashboard-stat bg-primary" href="#">
        <span class="name"><?php echo get_phrase('stock-à-vendre') ?></span>
        <span class="number counter">
            <?php
            // Initialiser la variable pour les ventes totales
            $totalVentes = 0;

            // Récupérer la somme des quantités des produits ajoutés (entrées)
            $querySortie = $this->db->query("SELECT SUM(qte_produit) AS total_qte_sortie FROM stock WHERE movement_type = 'sortie'");
            $totalQuantiteSortie = $querySortie->row()->total_qte_sortie;

            // Récupérer les ventes (quantité vendue) de la table `vente`
            $queryVentes = $this->db->query("SELECT invoice_ventes FROM vente");
            $ventes = $queryVentes->result_array();

            // Calculer le total des quantités vendues
            foreach ($ventes as $row) {
                $entries = json_decode($row['invoice_ventes'], true); // Décoder le champ JSON
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        // Vérifier que la clé 'qte' existe dans le JSON
                        if (isset($entry['qte'])) {
                            $totalVentes += $entry['qte']; // Ajouter la quantité vendue
                        }
                    }
                }
            }

            // Calculer la quantité de produits disponibles après les ventes
            $produitsDisponibles = $totalQuantiteSortie - $totalVentes;

            // Afficher la quantité disponible (en évitant d'afficher un nombre négatif)
            echo $produitsDisponibles > 0 ? $produitsDisponibles : 0;
            ?>
        </span>
        <span class="bg-icon"><i class="fa fa-shopping-cart"></i></span>
        </a>
    </div>                 
	</div>
	<br>
    <div class="col-md-12">
		<div class="panel">
			<button onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/add_stock/');" 
					class="btn btn-primary pull-right" style= width:140px;>
						<?php echo get_phrase('ajouter'); ?>
			</button>
				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
							   <th><?php echo get_phrase('produit'); ?></th>
							   <th><?php echo get_phrase('type-de-mouvement'); ?></th>
							   <th><?php echo get_phrase('prix-unitaire'); ?></th>
							   <th><?php echo get_phrase('quantité-en-stock'); ?></th>
								<th><?php echo get_phrase('date'); ?></th>
								<th><?php echo get_phrase('options'); ?></th>
							</tr>
						</thead>
						    <tbody>								
							<?php foreach ($stock_info as $row): ?>   
								<tr>
                                    <td>
				                    <?php $name = $this->db->get_where('medicine' , array('medicine_id' => $row['medicine_id'] ))->row()->name;
				                    	echo $name; ?>
				                    </td>
				                    <td><?php echo $row['movement_type'] ?></td>
				                    <td><?php echo $row['prix'] ?></td>
									<td><?php echo $row['qte_produit'] ?></td>
								    <td><?php echo $row['date_liv'] ?></td>
									<td>
									<a  onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/edit_stock/<?php echo $row['stock_id'] ?>');" 
										 class="btn btn-default btn-sm btn-icon icon-left" title="Modifier">
										<i class="fa fa-user-md"></i>Modifier
									</a>
									<a class="btn btn-danger btn-rounded icon-only" href="#"onclick="confirm_modal('<?php echo base_url(); ?>receptionist/stock/delete/<?php echo $row['stock_id']; ?>');" title="Supprimer">
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