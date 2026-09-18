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
<?php $examenNumber = $this->db->count_all('examen')+1; ?>
<div class="row">
    <div class="col-md-9" style="left:180px;">
		<div class="panel panel-primary">
             <a href="<?php echo base_url(); ?>laboratorist/examen" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>
            <h5 class="panel mt-n" style="text-align: center; color: black;"><?php echo get_phrase('nouvelle-analyse'); ?></h5>
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>laboratorist/examen/create" class="p-20" id="form-validate" enctype="multipart/form-data">				
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase("numéro-d'examen"); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="examen_number" name="examen_number"  data-validation="" value="<?php echo "0".$examenNumber; ?>" readonly >
                            </div>
                        </div> 
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                            <select name="patient_id" class="form-control" id="patient_id" data-validation="required"
                                    data-patient-autocomplete="1" data-placeholder="Rechercher un patient (nom, prénom, téléphone)">
                            </select>
                            </div>
                        </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_test"><?php echo get_phrase('test'); ?><sup class="color-danger">*</sup></label>
                        <select name="libelle_examen[]" class="form-control select2 libelle_examen" id="libelle_examen" data-validation="required">
                       <optgroup>
                        <option data-price="">Sélectionner le test</option>
                        <?php
                        $tests = $this->db->get('test')->result_array();
                        $tests = array_reverse($tests); // Inverser l'ordre des tests
                        foreach ($tests as $row2):
                            ?>
                       <option value="<?php echo $row2['libelle_examen']; ?>"
                         data-price="<?php echo $row2['unite_mesure']; ?>">
                        <?php echo $row2['libelle_examen']; ?>
                       </option>
                      <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="test"><?php echo get_phrase('unité-de-mesure'); ?></label>
                        <input type="text" name="unite_mesure[]" class="form-control select2 unite_mesure" id="unite_mesure" data-validation="">
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                var libelleSelect = document.getElementById('libelle_examen');
                var uniteInput = document.getElementById('unite_mesure');
                libelleSelect.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var unite = selectedOption.getAttribute('data-price');
                uniteInput.value = unite_mesure || ''; // Met à jour le champ de prix unitaire
                });
                });
                </script>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="test"><?php echo get_phrase('intervalle'); ?></label>
                        <input type="text" name="intervalle[]" class="form-control select2 intervalle" id="intervalle"data-validation="">
                    </div>
                </div>
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="categorie_id"><?php echo get_phrase('catégorie'); ?></label>
                        <select name="categorie_id" class="form-control select2" id="js-states" data-validation="">
                       <optgroup>
                        <option>Sélectionner une catégorie</option>
                        <?php
                        $categories = $this->db->get('categorie_test')->result_array();
                        $categories = array_reverse($categories); // Inverser l'ordre des categories
                        foreach ($categories as $row2):
                            ?>
                       <option value="<?php echo $row2['categorie_id']; ?>">
                        <?php echo $row2['libelle_categ']; ?>
                        </option>
                                <?php endforeach; ?>
                        </optgroup>
                        </select>
                    </div>
                </div>  
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('resultat'); ?></label>
                        <input type="text" class="form-control" id="resultat_examen" name="resultat_examen[]" data-validation="">
                    </div>
                </div>                   
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="examen"><?php echo get_phrase('groupe-sanguin'); ?></label>
                        <select name="blood_examen" class="form-control" id="js-states"  data-validation="">
                       <optgroup label="<?php echo get_phrase('sélectionner_un_groupe_sanguin'); ?>">  
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        </optgroup>
                        </select>  
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department_id"><?php echo get_phrase('statut'); ?></label>
                        <select name="statut_examen" class="form-control" id="js-states"  data-validation="">
                       <optgroup label="<?php echo get_phrase('sélectionner_un_statut'); ?>">  
                        <option value="En instance">En instance</option>
                        <option value="En cours">En cours</option>
                        <option value="Terminé">Terminé</option>
                        <option value="Validé">Validé</option>
                        <option value="A livrer">A livrer</option>
                        <option value="Annulé">Annulé</option>
                        </optgroup>
                        </select>  
                    </div>
                </div>  
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="name13"><?php echo get_phrase("date-d'examen"); ?><sup class="color-danger">*</sup></label>
                    <input type="text" class="form-control" id="date_examen" name="date_examen"  data-validation="required"
                    value="<?php date_default_timezone_set('Africa/Niamey'); 
                    echo date("d-m-Y H:i:s"); ?>" readonly>
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