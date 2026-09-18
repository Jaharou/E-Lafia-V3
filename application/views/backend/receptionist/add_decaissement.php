<?php
/* 	
 * 	Tamplate: Add Bed
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php $decaissementNumber = $this->db->count_all('decaissement')+1; ?>
<div class="row">
    <div class="col-md-9" style="left:180px;">
		<div class="panel panel-primary">
             <a href="<?php echo base_url(); ?>receptionist/decaissement" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>
            <h5 class="panel mt-n" style="text-align: center; color: black;"><?php echo get_phrase('nouveau-décaissement'); ?></h5>
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>receptionist/decaissement/create" class="p-20" id="form-validate" enctype="multipart/form-data">				
                    
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name13"><?php echo get_phrase("numéro"); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="examen_number" name="decaissement_number"  data-validation="" value="<?php echo "0".$decaissementNumber; ?>" >
                    </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="name13"><?php echo get_phrase("date-de-décaissement"); ?><sup class="color-danger">*</sup></label>
                    <input type="text" class="form-control" id="date_decaissment" name="date_decaissement"
                        value="<?php date_default_timezone_set('Africa/Niamey'); 
                        echo date("d-m-Y H:i:s"); ?>" >
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="decais"><?php echo get_phrase('tiers/patient'); ?><sup class="color-danger">*</sup></label>
                            <input name="libelle_decaiss" class="form-control select2" id="js-states" data-validation="required">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="mo"><?php echo get_phrase('motif'); ?><sup class="color-danger">*</sup></label>
                        <input name="motif" class="form-control select2 motif" id="motif" data-validation="required">
                        </div>
                    </div>
                    <div class="col-md-3">
                    <div class="form-group">
                        <label for="examen"><?php echo get_phrase('type-de-paiement'); ?></label>
                        <select name="type" class="form-control" id="type"  data-validation="">
                       <optgroup label="<?php echo get_phrase('sélectionner_un_groupe_sanguin'); ?>">  
                        <option value="Piaement fournisseur">Piaement fournisseur</option>
                        <option value="Paiement patient">Paiement patient</option>
                        <option value="Charge">Charge</option>
                        <option value="Dépense">Dépense</option>
                        </optgroup>
                        </select>  
                    </div>
                </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="name13"><?php echo get_phrase('montant'); ?></label>
                    <input type="text" class="form-control" id="montant" name="montant" data-validation="" >
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="recept"><?php echo get_phrase('compte'); ?></label>
                        <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="">
                       <optgroup>
                        <option>Sélectionner votre nom</option>
                        <?php
                        $receptionistes = $this->db->get('receptionist')->result_array();
                        $receptionistes = array_reverse($receptionistes); // Inverser l'ordre des receptionistes
                        foreach ($receptionistes as $row2):
                            ?>
                       <option value="<?php echo $row2['name']; ?>">
                        <?php echo $row2['name']; ?>
                        </option>
                        <?php endforeach; ?>
                        </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('mode_paiement'); ?></label>
                        <select class="form-control" id="js-states" name="mode">
                            <optgroup>
                            <option value="Espèce"><?php echo get_phrase('Espèce'); ?></option>
                            <option value="Chèque"><?php echo get_phrase('Chèque'); ?></option>
                            <option value="Carte-bancaire"><?php echo get_phrase('Carte_bancaire'); ?></option>
                            <option value="Virement"><?php echo get_phrase('Virement'); ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('note'); ?></label>
                        <textarea type="text" class="form-control" id="note" name="note" data-validation=""></textarea>
                    </div>
                </div> 
                <div class="row">
    		        <div class="col-md-12">
			        <div class="btn-group pull-right mt-10" role="group">
			        <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
			        <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
			    </div>				  
			</div>
		</div>
	</form>	
	</div>				
	</div>				
</div>				
</div>
<?php 
$output .= ob_get_clean();
echo $output;