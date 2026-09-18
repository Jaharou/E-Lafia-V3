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
<?php $output = ''; ?>
<?php ob_start(); $today=date('Y-m-d'); ?>
	<div class="row">
        <!--<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a class="dashboard-stat bg-primary" href="</?php echo base_url(); ?>magasin/medicine">
                <span class="name"></?php echo get_phrase('Produits-total-disponibles') ?></span>
                <span class="number counter"></?php echo $this->db->query("SELECT SUM(qte_prod) AS total FROM medicine")->result_array()[0]['total']; ?></span>
                <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
            </a>
        </div>
	    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
	    	<a class="dashboard-stat bg-primary" href="</?php echo base_url(); ?>magasin/medicine">
	        <span class="name"></?php echo get_phrase('produits-total-restants') ?></span>
	        <span class="number counter">
	            </?php
	            // Récupérer la somme des quantités des produits ajoutés (entrées)
	            $queryEntree = $this->db->query("SELECT SUM(qte_produit) AS total_qte_entree FROM stock WHERE movement_type = 'entrée'");
	            $totalQuantiteEntree = $queryEntree->row()->total_qte_entree;

	            // Récupérer la somme des quantités des produits sortis (ventes)
	            $queryDisponible = $this->db->query("SELECT SUM(qte_prod) AS total_qte_prod FROM medicine");
	            $totalQuantiteProd = $queryDisponible->row()->total_qte_prod;

	            // Calculer la quantité de produits disponibles après les ventes
	            $produitsRestants = $totalQuantiteProd - $totalQuantiteEntree;

	            // Afficher la quantité disponible (en évitant d'afficher un nombre négatif)
	            echo $produitsRestants > 0 ? $produitsRestants : 0;
	            ?>
	        </span>
	        <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
	    	</a>
		</div>
	    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
	        <a class="dashboard-stat bg-danger" href="</?php echo base_url(); ?>magasin/medicine">
	            <span class="name"></?php echo get_phrase('Produits-total-expirés') ?></span>
	            <span class="number counter"></?php echo $this->db->query("SELECT COUNT(expiration_date) AS total FROM medicine")->result_array()[0]['total']; ?></span>
	            <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
	        </a>
	    </div>-->  
		<br>
		<div class="row">
		    <div class="col-md-12">
				<div class="panel">		
					<div class="panel-body p-20">
		    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
					<tr>									
						<th><?php echo get_phrase('fournisseur'); ?></th>	
					    <th><?php echo get_phrase('produit'); ?></th>
						<th><?php echo get_phrase('catégorie'); ?></th>
						<th><?php echo get_phrase('DCI'); ?></th>
						<th><?php echo get_phrase('dosage'); ?></th>
						<th><?php echo get_phrase('condition'); ?></th>
						<th><?php echo get_phrase('forme'); ?></th>
						<th><?php echo get_phrase('quantité'); ?></th>
						<th><?php echo get_phrase('montant'); ?></th>
						<th><?php echo get_phrase('statut'); ?></th>
					</tr>
				</thead>
			    <tbody>			
					<?php
					$this->db->order_by('medicine_id', 'DESC');
	                $this->db->limit(10);
	                $medicine_info   = $this->db->get('medicine')->result_array(); 
					foreach ($medicine_info as $row): ?>   
					<tr>
						<td>
	                    <?php
	                    $name = $this->db->get_where('fournisseur' , array('fournisseur_id' => $row['fournisseur_id'] ))->row()->name;
	                    echo $name;
	                    ?>
	                    </td>
						<td><?php echo $row['name'] ?></td>
						<td><?php echo $row['medicine_category'] ?></td>
					   	<td><?php echo $row['dci'] ?></td>
					   	<td><?php echo $row['dosage'] ?></td>
					   	<td><?php echo $row['condit'] ?></td>
					   	<td><?php echo $row['forme'] ?></td>
					   	<td><?php echo $row['qte_prod'] ?></td>
						<td><?php echo $row['amount'] ?></td>
						<td><?php echo $row['status'] ?></td>
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