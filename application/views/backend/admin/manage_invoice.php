<?php
/*
 *  Liste : Consultations (administration)
 *
 *  Recherche, tri et pagination sont effectués côté serveur
 *  (admin/list_datatable/invoice). La table est volontairement vide au
 *  chargement : les lignes arrivent en AJAX, page par page. L'ancienne
 *  version envoyait la table entière dans le HTML, ce qui devenait très
 *  lent à mesure que la base grossissait.
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
            <a href="<?php echo base_url(); ?>admin/invoice_crud/add" class="btn btn-info btn-wide pull-right">
                <i class="fa fa-plus"></i> <?php echo get_phrase('ajouter'); ?>
            </a>
            <header style="color: dark; font-size: 23px;">Consultations</header>
            <div style="clear:both;"></div>
            <div class="panel-body p-20">
                <table data-admin-list="invoice" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo get_phrase('id');?></th>
                            <th><?php echo get_phrase('numéro-de-reçu');?></th>
                            <th><?php echo get_phrase('patient');?></th>
                            <th><?php echo get_phrase('date-de-creation');?></th>
                            <th><?php echo get_phrase('montant');?></th>
                            <th><?php echo get_phrase('statut');?></th>
                            <th><?php echo get_phrase('options');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Chargé en AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$output .= ob_get_clean();
echo $output;
