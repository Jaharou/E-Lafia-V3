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
			<a href="<?php echo base_url(); ?>admin/test_crud/add" class="btn btn-info btn-wide pull-right"> <i class="fa fa-plus"></i>
				<?php echo get_phrase('ajouter'); ?>
			</a>
				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
	  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php echo get_phrase('nom'); ?></th>
					<th><?php echo get_phrase('catégorie'); ?></th>
					<th><?php echo get_phrase('unité-de-mesure'); ?></th>
					<th><?php echo get_phrase('montant'); ?></th>
					<th><?php echo get_phrase('options'); ?></th>
				</tr>
			</thead>
			<tbody>
			 <?php foreach ($test_info as $row): ?>   
			<tr>
				<td><?php echo $row['libelle_examen'] ?></td>
				<td>
				    <?php 
				    $query = $this->db->get_where('categorie_test', array('categorie_id' => $row['categorie_id'] ));
				    if ($query->num_rows() > 0) {
				        $libelle_categ = $query->row()->libelle_categ;
				        echo $libelle_categ;
				    } else {
				        echo ""; // Gestion du cas où la catégorie n'existe pas
				    }
				    ?>
				</td>

				<td><?php echo $row['unite_mesure'] ?></td>
				<td style="text-align:center;"><?php echo $row['amount'] ?></td>
				<td>
					<a 
                        href="<?php echo base_url(); ?>admin/test_crud/edit/<?php echo $row['id_test']; ?>"
                        class="btn btn-success btn-rounded icon-only" title="Modifier">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
					<a class="btn btn-danger btn-rounded icon-only" href="#"onclick="confirm_modal('<?php echo base_url(); ?>admin/test/delete/<?php echo $row['id_test']; ?>');"
						title="Supprimer">
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