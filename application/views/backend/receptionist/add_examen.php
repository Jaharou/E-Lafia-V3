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
             <a href="<?php echo base_url(); ?>receptionist/examen" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>
            <h5 class="panel mt-n" style="text-align: center; color: black;"><?php echo get_phrase('nouvelle-analyse'); ?></h5>
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>receptionist/examen/create" class="p-20" id="form-validate" enctype="multipart/form-data">				
                    
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase("numéro-d'examen"); ?><sup class="color-danger"></sup></label>
                            <input type="text" class="form-control" id="examen_number" name="examen_number"  data-validation="" value="<?php echo "0".$examenNumber; ?>" >
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
                         data-price="<?php echo $row2['amount']; ?>">
                        <?php echo $row2['libelle_examen']; ?>
                       </option>
                      <?php endforeach; ?>
                        </optgroup>
                    </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('prix-unitaire'); ?></label>
                        <input type="text" class="form-control" id="amount_examen" name="amount_examen[]" data-validation="" >
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                var libelleSelect = document.getElementById('libelle_examen');
                var amountInput = document.getElementById('amount_examen');
                libelleSelect.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var amount = selectedOption.getAttribute('data-price');
                amountInput.value = amount || ''; // Met à jour le champ de prix unitaire
                });
                });
                </script>
                <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('quantité'); ?></label>
                    <input type="text" class="form-control" id="qte_examen" name="qte_examen[]" data-validation="required">
                </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="amount_consult"><?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
                        <input type="text" class="form-control" id="amountT" name="amountT[]" value="0" readonly>
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
                        <input type="text" class="form-control" id="resultat_examen" name="resultat_examen[]" data-validation="" readonly>
                    </div>
                </div>                   
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="examen"><?php echo get_phrase('groupe-sanguin'); ?></label>
                        <select name="blood_examen" class="form-control" id="js-states"  data-validation="">
                       <optgroup label="<?php echo get_phrase('sélectionner-un-groupe-sanguin'); ?>">  
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="department_id"><?php echo get_phrase('statut'); ?></label>
                        <select name="statut_examen" class="form-control" id="js-states"  data-validation="">
                       <optgroup label="<?php echo get_phrase('sélectionner-un-statut'); ?>">  
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
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="name13"><?php echo get_phrase("date-d'examen"); ?><sup class="color-danger">*</sup></label>
                    <input type="text" class="form-control" id="date_examen" name="date_examen"  data-validation="required"
                    value="<?php date_default_timezone_set('Africa/Niamey'); 
                    echo date("d-m-Y H:i:s"); ?>" >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('% remise'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="0"  data-validation="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('prise-en-charge'); ?></label>
                        <input type="text" class="form-control" id="discount_amount" name="prise_en_charge" data-validation="" placeholder="Veillez saisir le nom de la personne" title ="Veillez saisir le nom de la personne physique ou morale">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('% prise-en-charge'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="pourcentage_prise" name="pourcentage_prise" value="0"  data-validation="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('mode-paiement'); ?></label>
                        <select class="form-control" id="js-states" name="mode_paiement">
                            <optgroup>
                            <option value="Espèce"><?php echo get_phrase('Espèce'); ?></option>
                            <option value="Chèque"><?php echo get_phrase('Chèque'); ?></option>
                            <option value="Carte-bancaire"><?php echo get_phrase('Carte-bancaire'); ?></option>
                            <option value="Virement"><?php echo get_phrase('Virement'); ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status"><?php echo get_phrase('statut-de-paiement'); ?></label>
                        <select class="form-control" id="js-states" name="status">
                         <optgroup>
                          <option value="payé"><?php echo get_phrase('payé'); ?></option>
                            <option value="impayé"><?php echo get_phrase('impayé'); ?></option>
                            </optgroup>
                        </select>  
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="receptionist_id"><?php echo get_phrase('utilisteur'); ?></label>
                        <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="required">
                            <optgroup>
                            <option value="">Sélectionner votre nom</option>
                            <?php
                            $receptionistes = $this->db->get('receptionist')->result_array();
                            $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
                            foreach ($receptionistes as $row2): 
                                ?>
                           <option value="<?php echo $row2['receptionist_id']; ?>">
                            <?php echo $row2['name']; ?>
                            </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
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
<script>
    // Ensure the DOM is fully loaded before accessing elements
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listener to dynamically added elements
        $(document).on('change', '.libelle_examen', function () {
            const selectedOption = this.options[this.selectedIndex];
            const amount_examen = selectedOption.getAttribute('data-price');
            $(this).closest('.examen_entry').find('.amount').val(amount_examen ? amount_examen : '');
        });
    });

    // Récupérer les éléments du DOM de ventes
    var amountInput = document.getElementById("amount_examen");
    var qteInput = document.getElementById("qte_examen");
    //var priseInput = document.getElementById("pourcentage_prise");
    var amountTInput = document.getElementById("amountT");
    // Écouter les modifications des entrées de montant et remise
    amountInput.addEventListener("input", calculerAmountT);
    qteInput.addEventListener("input", calculerAmountT);
    //priseInput.addEventListener("input", calculerMontantNet);

    function calculerAmountT() {
      // Récupérer les valeurs entrées
      var amount_examen = parseFloat(amountInput.value);
      var qte_examen = parseFloat(qteInput.value);
      //var prise = parseFloat(priseInput.value);

      // Calculer le montant net
      var amountT = (amount_examen * qte_examen);

      // Afficher le montant net arrondi à 2 décimales
      amountTInput.value = amountT.toFixed(0);
    }

    </script>				
</div>
<?php 
$output .= ob_get_clean();
echo $output;