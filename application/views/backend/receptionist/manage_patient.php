<?php
/* 	
 * 	Tamplate: Manage Patient
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 *
 *  Mise à jour : recherche et pagination effectuées côté serveur
 *  (endpoint receptionist/patient_datatable) au lieu de charger tous les
 *  patients dans le HTML. L'ajout d'un patient se fait désormais dans une
 *  fenêtre modale chargée en AJAX, sans rechargement de page.
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
			<header style="color: dark; font-size: 23px;">Patients</header>

          <a class="btn bg-black btn-wide icon-only pull-right" href="#"
             onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/add_patient/'); return false;">
		  <i class="fa fa-plus" aria-hidden="true"></i>
		  <?php echo get_phrase('ajouter'); ?>
		 </a>

				<div style="clear:both;"></div>			
			<div class="panel-body p-20">
				  <table id="table-patients" class="display table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>		
								<th><?php echo get_phrase('N°');?></th>					
								<th><?php echo get_phrase('nom');?></th>
								<th><?php echo get_phrase('prénom');?></th>
								<th><?php echo get_phrase('téléphone');?></th>
								<th><?php echo get_phrase('options');?></th>
							</tr>
						</thead>
						<tbody>
							<!-- Les lignes sont chargées via AJAX (recherche + pagination côté serveur) -->
						</tbody>
					  </table>				
			</div>				
		</div>				
	</div>				
</div>

<script src="<?php echo base_url(); ?>assets/js/receptionist/patient_manage.js"></script>
<script>
$(document).ready(function () {
    PatientManage.init({
        datatableUrl: '<?php echo base_url(); ?>receptionist/patient_datatable',
        ajaxFormUrl:  '<?php echo base_url(); ?>receptionist/patient_ajax/',
        editUrlBase:  '<?php echo base_url(); ?>modal/popup/edit_patient/'
    });
    PatientManage.initDataTable('#table-patients');
    PatientManage.bindAjaxForms(document);
});
</script>

<?php 
$output .= ob_get_clean();
echo $output;
