<?php
/* 	
 * 	Tamplate: Edit Patient
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php
$single_fournisseur_info = $this->db->get_where('fournisseur', array('fournisseur_id' => $param2))->result_array();
foreach ($single_fournisseur_info as $row):
?>
<div class="row">
    <div class="col-md-8" style="left: 180px;">
		<div class="panel">
             <a href="<?php echo base_url(); ?>magasin/fournisseur" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>						
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>magasin/fournisseur/update/<?php echo $row['fournisseur_id']; ?>" class="p-20" id="form-validate" enctype="multipart/form-data">				
                    <div class="row panel panel-primary" data-collapsed="0">       
                  <div class="panel-heading">
                    <div class="panel-title" >
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('informations générales'); ?>
                    </div>
                    </div><br>
                        
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('nom'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="name13" name="name"  data-validation="required" value="<?php echo $row['name']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('téléphone'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="contact" name="contact"  data-validation="required" value="<?php echo $row['contact']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('adresse'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="adresse" name="adresse"  data-validation="" value="<?php echo $row['adresse']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('email'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="email"  data-validation="" value="<?php echo $row['email']; ?>">
                            </div>
                        </div>
                       </div>			
						<div class="col-md-12">
						  <div class="btn-group pull-right mt-10" role="group">
						        <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
						        <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
						    </div>				  
						</div>
       			 </form>	
			</div>				
		</div>				
	</div>				
</div>
<?php endforeach; ?>
<?php 
$output .= ob_get_clean();
echo $output;