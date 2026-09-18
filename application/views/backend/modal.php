<?php
/* 	
 * 	Tamplate: Modal
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>

<?php $output = ''; ?>
<?php ob_start(); ?>

<script type="text/javascript">
    /**
     * Affiche le modal "Nouveau patient". Le contenu du formulaire est déjà
     * inclus statiquement ci-dessous ; si une URL est fournie (usage legacy),
     * elle est utilisée pour rafraîchir le contenu du modal en AJAX au lieu
     * de recharger toute la page.
     */
    function patientAjoutAjaxModal(url)
    {
        if (url) {
            jQuery('#modal_patient .modal-body').html('<div style="text-align:center;margin-top:200px;"><img src="assets/images/preloader.gif" /></div>');
            jQuery('#modal_patient').modal('show', {backdrop: 'true'});
            $.ajax({
                url: url,
                success: function (response)
                {
                    jQuery('#modal_patient .modal-body').html(response);
                },
                error: function ()
                {
                    jQuery('#modal_patient .modal-body').html("<p style='text-align:center;margin-top:200px;color:red;'>Une erreur s'est produite. Veuillez réessayer plus tard.</p>");
                }
            });
        } else {
            jQuery('#modal_patient').modal('show', {backdrop: 'true'});
        }
    }
</script>
<?php $patient_id = $this->db->count_all('patient')+1; ?>
<div id="modal_patient" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
             
        <div class="panel">
            <div class="panel-heading">
              <div class="panel-title" style="background-color:darkgray; padding-top: 20px;">
                <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('Nouveau-patient'); ?>
               </div>
            </div>
            <div style="clear:both;"></div><br>      
            <div class="panel-body p-5">        
           <form method="post" action="<?php echo base_url(); ?>receptionist/patient/create" class="p-5" id="form-validate" enctype="multipart/form-data"
                 data-ajax-target="patient" data-task="create">
                       
                  <div class="panel panel-heading panel-info">
                    <div class="panel-title" style="text-align:center;">
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('informations-générales'); ?>
                    </div>
                    </div><br>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('référence-de-patient'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="patient_id"  data-validation="" value="<?php echo "0".$patient_id; ?>" readonly >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('civilité'); ?></label>
                            <select name="civilite" id="js-states" class="form-control">
                                    <optgroup >
                            <option value="M"><?php echo get_phrase('M'); ?></option>
                                <option value="Mlle"><?php echo get_phrase('Mlle'); ?></option>
                                <option value="Mme"><?php echo get_phrase('Mme'); ?></option>
                                    </optgroup>
                                </select>  
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('nom'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="name13" name="name"  data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prenom"><?php echo get_phrase('prénom'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="prenom" name="prenom"  data-validation="required">
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="birth_date"><?php echo get_phrase('date-de-naissance'); ?></label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date"  data-validation="" placeholder="dd-mm-yyyy">
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label for="age"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
                                <input type="number" class="form-control" id="age" name="age">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('sexe'); ?><sup class="color-danger"></sup></label>
                                <select name="sex" class="form-control" id="js-states"  data-validation="">
                                    <optgroup> 
                                <option class="fa fa-male" aria-hidden="true"><?php echo get_phrase('masculin'); ?></option>
                                <option class="fa fa-male" aria-hidden="true"><?php echo get_phrase('feminin'); ?></option>
                                    </optgroup>
                                </select>  
                            </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="name13"><?php echo get_phrase('situation-familiale'); ?><sup class="color-danger"></sup></label>
                                <select name="situation_famil" class="form-control" id="js-states"  data-validation="">
                                    <optgroup>
                                <option value="Celibataire"><?php echo get_phrase('Celibataire'); ?></option>
                            <option value="Marié"><?php echo get_phrase('Marié(e)'); ?></option>
                            <option value="Divorcé"><?php echo get_phrase('divorcé(e)'); ?></option>
                            <option value="Veuf(ve)"><?php echo get_phrase('Veuf(ve)'); ?></option>
                            </optgroup>
                                </select>  
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="profession"><?php echo get_phrase('profession'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="profession" name="profession"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="department_id"><?php echo get_phrase('groupe-sanguin'); ?></label>
                                <select name="blood_group" class="form-control" id="js-states"  data-validation="">
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
                                <label for="name13"><?php echo get_phrase('photo'); ?></label>
                                <input type="file" class="form-control" id="name13" name="image">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('téléphone-patient'); ?></label>
                                <input type="text" class="form-control" id="name13" name="phone"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('adresse'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="address_patient"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('email'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="email"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('password'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="password"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('personne-à-contacter'); ?></label>
                                <input type="text" class="form-control" id="js-states" name="personne_contacter"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('tél-personne-à-contacter'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="tel_contacter"  data-validation="">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('Médecin-traitant'); ?></label>
                                <select name="doctor_id" class="form-control" id="name13"  data-validation="">
                                    <optgroup>
                                <option></option>
                          <?php $doctors = $this->db->get('doctor')->result_array();
                            foreach ($doctors as $row2):?>
                                <option value="<?php echo $row2['doctor_id']; ?>">
                                    <?php echo $row2['name']; ?>
                                </option>
                            <?php endforeach; ?>
                                    </optgroup>
                                </select>  
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('categorie'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="categorie"  data-validation="">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('services'); ?><sup class="color-danger"></sup></label>
                                <select name="service_id" class="form-control" id="name13"  data-validation="">
                                    <optgroup>
                             <option></option>  
                             <?php $services = $this->db->get('services')->result_array();
                            foreach ($services as $row2):?>
                                <option value="<?php echo $row2['service_id']; ?>">
                                <?php echo $row2['service_title']; ?>
                                </option>
                            <?php endforeach; ?>
                                    </optgroup>
                                </select>  
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('assurance'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="assurance"  data-validation="">
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
                   <div class="modal-footer no-margin-top">
                 <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
               </div>
            </div>
         </div>                 

        <script>
    //Obtenez l'élément de formulaire de la date de naissance et d'âge
        var birth_dateInput = document.getElementById("birth_date");
        var ageInput = document.getElementById("age");
        // Ajouter un écouteur d'événements pour la modification de la date de naissance
        birth_dateInput.addEventListener("change", function() {
            // Obtenez la date de naissance à partir de l'élément de formulaire
            var birth_date = new Date(birth_dateInput.value);

            // Obtenez l'année actuelle
            var anneeActuelle = new Date().getFullYear();

            // Calculez l'âge en soustrayant l'année de naissance de l'année actuelle
            var age = anneeActuelle - birth_date.getFullYear();
            // Mettre à jour automatiquement le champ d'âge
            ageInput.value = age;
        });
</script>
 
<!----------------------------Debut partie invoice--------------------------->

<script type="text/javascript">
    function ajoutInvoiceAjaxModal(url)
    {
        const modalBody = jQuery('#modal_invoice .modal-body');
        
        // Show loading indicator
        modalBody.html('<div style="text-align:center;margin-top:200px;"><img src="assets/images/preloader.gif" /></div>');
        
        // Display the modal
        jQuery('#modal_invoice').modal('show', {backdrop: 'true'});        
        
        // Make the AJAX request
        $.ajax({
            url: url,
            success: function (response)
            {
                // Load response into modal body
                modalBody.html(response);
            },
            error: function (xhr, status, error)
            {
                // Display error message in case of failure
                modalBody.html("<p style='text-align:center;margin-top:200px;color:red;'>une erreur s'est produite. Veuillez réessayer plus tard.</p>");
            }
        });
    }
</script>
<?php $invoiceNumber = $this->db->count_all('invoice')+1; ?>
<div id="modal_invoice" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
        <div class="panel panel-darkgray" data-collapsed="0">
            <div class="panel-heading">
              <div class="panel-title" style="background-color:darkgray; padding-top: 20px; text-align:center;">
                <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('Nouveau_reçu'); ?>
               </div>
            </div>
            <div style="clear:both;"></div><br>                     
            <div class="panel-body p-20">
            <form method="post" action="<?php echo base_url(); ?>receptionist/invoice_add/create" class="p-20" id="form-validate" enctype="multipart/form-data">     
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="title_recu"><?php echo get_phrase('titre-de-reçu'); ?><sup class="color-danger"></sup></label>
                        <select name="title" class="form-control" id="name13"  data-validation="">
                            <optgroup> 
                            <option ><?php echo get_phrase("reçu-d'encaissement"); ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('numéro-de-reçu'); ?><sup class="color-danger"></sup></label>
                        <input type="text" class="form-control" id="name13" name="invoice_number"  data-validation="" value="<?php echo "0".$invoiceNumber; ?>" readonly >
                    </div>
                </div>  
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('date-de-creation'); ?><sup class="color-danger">*</sup></label>
                        <input type="date" class="form-control" id="name13" name="creation_datetime"  data-validation="required" value="<?php 
                        date_default_timezone_set('Africa/Niamey'); 
                        echo date("d-m-Y H:i:s"); ?>" >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                        <select name="patient_id" class="form-control select2" id="js-states" data-validation="required">
                            <optgroup>
                            <option>Sélectionner le patient</option>
                            <?php
                            $patients = $this->db->get('patient')->result_array();
                            $patients = array_reverse($patients); // Inverser l'ordre des patients
                            foreach ($patients as $row2):
                                ?>
                           <option value="<?php echo $row2['patient_id']; ?>">
                            <?php echo $row2['name'].' '.$row2['prenom']; ?>
                            </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            <div class="col-md-3">
        <div class="form-group">
        <label for="name13"><?php echo get_phrase('prestation'); ?></label>
        <select name="entry_description[]" class="form-control select2" id="entry_description">
            <optgroup>
                <option value="" data-price="">Sélectionner une prestation</option>
                <?php
                $designations = $this->db->get('designation')->result_array();
                $designations = array_reverse($designations); // Inverser l'ordre des désignations
                foreach ($designations as $row2): ?>
                    <option value="<?php echo $row2['libelle']; ?>" data-price="<?php echo $row2['amount']; ?>">
                        <?php echo $row2['libelle']; ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>
    </div>
</div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"><?php echo get_phrase('prix-unitaire'); ?></label>
            <input type="text" class="form-control" id="entry_amount" name="entry_amount[]" readonly>
        </div>
    </div>
    <script>
            document.addEventListener('DOMContentLoaded', function() {
            var descriptionSelect = document.getElementById('entry_description');
            var amountInput = document.getElementById('entry_amount');

            descriptionSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var price = selectedOption.getAttribute('data-price');
            amountInput.value = price || ''; // Met à jour le champ de prix unitaire
            });
        });
    </script>                       
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"><?php echo get_phrase('quantité'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="qte_consult" name="qte_consult[]" value=""  data-validation="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"><?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="net_amount" name="net_amount[]" value="0"  data-validation="" readonly>
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
            <label for="name13"><?php echo get_phrase("% prise-en-charge"); ?><sup class="color-danger"></sup></label>
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
                    <option value="Carte-bancaire"><?php echo get_phrase('Carte_bancaire'); ?></option>
                    <option value="Virement"><?php echo get_phrase('Virement'); ?></option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="status"><?php echo get_phrase('statut'); ?><sup class="color-danger"></sup></label>
                <select class="form-control" id="js-states" name="status" data-validation="">
                 <optgroup>
                  <option value="payé"><?php echo get_phrase('payé'); ?></option>
                    <option value="impayé"><?php echo get_phrase('impayé'); ?></option>
                 </optgroup>
                </select>  
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="name13"><?php echo get_phrase("periode-d'échéance"); ?><sup class="color-danger"></sup></label>
                <input type="date" class="form-control" id="name13" name="due_timestamp"  data-validation="" value="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="receptionist_id"><?php echo get_phrase('utilisteur'); ?></label>
                <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="">
                    <optgroup>
                    <option>Sélectionner votre nom</option>
                    <?php
                    $receptionistes = $this->db->get('receptionist')->result_array();
                    $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
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
        <div class="col-md-12">
            <div class="btn-group pull-right mt-10" role="group">
                <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
                <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
            </div>                
        </div> 
    </form>    
    </div>
    <div class="modal-footer no-margin-top">
    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
    </div>              
    </div>
    </div>
    <script>

// Récupérer les éléments du DOM de consultations
var montantInput = document.getElementById("entry_amount");
var qteConsultInput = document.getElementById("qte_consult");
//var priseInput = document.getElementById("pourcentage_prise");
var montantNetInput = document.getElementById("net_amount");
// Écouter les modifications des entrées de montant et remise
montantInput.addEventListener("input", calculerMontantNet);
qteConsultInput.addEventListener("input", calculerMontantNet);
//priseInput.addEventListener("input", calculerMontantNet);

function calculerMontantNet() {
  // Récupérer les valeurs entrées
  var montant = parseFloat(montantInput.value);
  var qteConsult = parseFloat(qteConsultInput.value);
  //var prise = parseFloat(priseInput.value);

   //Calculer le montant net
  var montantNet = (montant * qteConsult);

  // Afficher le montant net arrondi à 2 décimales
  montantNetInput.value = montantNet.toFixed(0);
}

</script>
 <!--------------------Fin partie invoice------------------------------------>

 <!----------------------------Debut partie examen--------------------------->

<!--<script type="text/javascript">
    function ajoutExamenAjaxModal(url)
    {
        const modalBody = jQuery('#modal_examen .modal-body');
        // Show loading indicator
        modalBody.html('<div style="text-align:center;margin-top:200px;"><img src="assets/images/preloader.gif" /></div>');
        // Display the modal
        jQuery('#modal_examen').modal('show', {backdrop: 'true'});
        // Make the AJAX request
        $.ajax({
            url: url,
            success: function (response)
            {
                // Load response into modal body
                modalBody.html(response);
            },
            error: function (xhr, status, error)
            {
                // Display error message in case of failure
                modalBody.html("<p style='text-align:center;margin-top:200px;color:red;'>une erreur s'est produite. Veuillez réessayer plus tard.</p>");
            }
        });
    }
</script>

</?php $examenNumber = $this->db->count_all('examen')+1; ?>
<div id="modal_examen" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
        <div class="panel panel-darkgray" data-collapsed="0">
            <div class="panel-heading">
              <div class="panel-title" style="background-color:darkgray; padding-top: 20px; text-align:center;">
                <i class="entypo-plus-circled"></i>
                    </?php echo get_phrase('Nouveau-examen'); ?>
               </div>
            </div>
            <div style="clear:both;"></div><br>                     
            <div class="panel-body p-20">
            <form method="post" action="</?php echo base_url(); ?>laboratorist/examen_add/create" class="p-20" id="form-validate" enctype="multipart/form-data">                
                    
               <div class="col-md-3">
                <div class="form-group">
                    <label for="patient_id"></?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                    <select name="patient_id" class="form-control select2" id="js-states" data-validation="required">
                   <optgroup>
                        <option>Sélectionner le patient</option>
                        </?php
                        $patients = $this->db->get('patient')->result_array();
                        $patients = array_reverse($patients); // Inverser l'ordre des patients
                        foreach ($patients as $row2):
                            ?>
                       <option value="</?php echo $row2['patient_id']; ?>">
                        </?php echo $row2['name'].' '.$row2['prenom']; ?>
                    </option>
                </?php endforeach; ?>
            </optgroup>
        </select>
    </div>
</div>                   
<div class="col-md-3">
    <div class="form-group">
        <label for="name13"></?php echo get_phrase('numéro-de-reçu'); ?><sup class="color-danger"></sup></label>
        <input type="text" class="form-control" id="name13" name="examen_number"  data-validation="" value="</?php echo "0".$examenNumber; ?>" readonly >
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label for="id_examen"></?php echo get_phrase('test'); ?></label>
        <select name="libelle_examen[]" class="form-control select2" id="examen">
            <optgroup>
                <option value="" data-price="">Sélectionner un test</option>
                </?php
                $tests = $this->db->get('test')->result_array();
                $tests = array_reverse($tests); // Inverser l'ordre des désignations
                foreach ($tests as $row2): ?>
                    <option value="</?php echo $row2['libelle_examen']; ?>" data-price="</?php echo $row2['amount']; ?>">
                        </?php echo $row2['libelle_examen']; ?>
                    </option>
                </?php endforeach; ?>
            </optgroup>
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label for="amount_exam"></?php echo get_phrase('prix-unitaire'); ?></label>
        <input type="text" class="form-control" id="amount_examen" name="amount_examen[]" readonly>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
        var examenSelect = document.getElementById('examen');
        var amountInput = document.getElementById('amount_examen');

        examenSelect.addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        amountInput.value = price || ''; // Met à jour le champ de prix unitaire
        });
    });
</script>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('quantité'); ?></label>
            <input type="text" class="form-control" id="qte_examen" name="qte_examen">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="amount_exam"></?php echo get_phrase('montant-net'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="net_amount_examen" name="net_amount_examen" value="0" readonly>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('resultat'); ?></label>
            <input type="text" class="form-control" id="name13" name="resultat_examen" data-validation="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="examen"></?php echo get_phrase('groupe-sanguin'); ?></label>
            <select name="blood_examen" class="form-control" id="js-states"  data-validation="">
           <optgroup label="</?php echo get_phrase('sélectionner_un_groupe_sanguin'); ?>">  
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
            <label for="name13"></?php echo get_phrase('date-examen'); ?></label>
            <input type="date" class="form-control" id="date_examen" name="date_examen" data-validation="">
        </div>
    </div>         
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('% remise'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="0"  data-validation="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('prise-en-charge'); ?></label>
            <input type="text" class="form-control" id="discount_amount" name="prise_en_charge" data-validation="" placeholder="Veillez saisir le nom de la personne" title ="Veillez saisir le nom de la personne physique ou morale">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('% prise-en-charge'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="pourcentage_prise" name="pourcentage_prise" value="0"  data-validation="">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('mode-paiement'); ?></label>
            <select class="form-control" id="js-states" name="mode_paiement">
                <optgroup>
                <option value="Espèce"></?php echo get_phrase('Espèce'); ?></option>
                <option value="Chèque"></?php echo get_phrase('Chèque'); ?></option>
                <option value="Carte-bancaire"></?php echo get_phrase('Carte_bancaire'); ?></option>
                <option value="Virement"></?php echo get_phrase('Virement'); ?></option>
                </optgroup>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="status"></?php echo get_phrase('statut'); ?><sup class="color-danger"></sup></label>
            <select class="form-control" id="js-states" name="status" data-validation="">
             <optgroup>
              <option value="payé"></?php echo get_phrase('payé'); ?></option>
                <option value="impayé"></?php echo get_phrase('impayé'); ?></option>
             </optgroup>
            </select>  
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase("periode-d'échéance"); ?><sup class="color-danger"></sup></label>
            <input type="date" class="form-control" id="name13" name="due_timestamp"  data-validation="" value="</?php echo date("d/m/Y"); ?>">
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
    <div class="modal-footer no-margin-top">
    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
    </div>              
    </div>
    </div>-->
    
 <!--------------------Fin partie examen------------------------------------> 

 <!--------------------Debut partie Vente------------------------------------> 
<!--<script type="text/javascript">
    function ajoutVenteAjaxModal(url)
    {
        const modalBody = jQuery('#modal_vente .modal-body');
        
        // Show loading indicator
        modalBody.html('<div style="text-align:center;margin-top:200px;"><img src="assets/images/preloader.gif" /></div>');
        
        // Display the modal
        jQuery('#modal_vente').modal('show', {backdrop: 'true'});        
        
        // Make the AJAX request
        $.ajax({
            url: url,
            success: function (response)
            {
                // Load response into modal body
                modalBody.html(response);
            },
            error: function (xhr, status, error)
            {
                // Display error message in case of failure
                modalBody.html("<p style='text-align:center;margin-top:200px;color:red;'>une erreur s'est produite. Veuillez réessayer plus tard.</p>");
            }
        });
    }
</script>

</?php $venteNumber = $this->db->count_all('vente')+1; ?>
<div id="modal_vente" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
        <div class="panel panel-darkgray" data-collapsed="0">
            <div class="panel-heading">
              <div class="panel-title" style="background-color:darkgray; padding-top: 20px; text-align:center;">
                <i class="entypo-plus-circled"></i>
                    </?php echo get_phrase('nouvelle-vente'); ?>
               </div>
            </div>
            <div style="clear:both;"></div><br>                     
            <div class="panel-body p-20">
            <form method="post" action="</?php echo base_url(); ?>pharmacist/vente/create" class="p-20" id="form-validate" enctype="multipart/form-data">    
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"></?php echo get_phrase('numéro-de-vente'); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="vente_number" name="vente_number"  data-validation="" value="</?php echo "0".$venteNumber; ?>" readonly >
                </div>
            </div>
            <div class="col-md-3">
            <div class="form-group">
                <label for="name13"></?php echo get_phrase('produit'); ?></label>
                <select name="stock[]" class="form-control select2" id="stock_id">
                <optgroup>
                <option value="" data-price="">Sélectionner un produit</option>
                </?php
                $stocks = $this->db->get('stock')->result_array();
                $stocks = array_reverse($stocks); // Inverser l'ordre des produits
                foreach ($stocks as $row2): ?>
                    <option value="</?php echo $row2['libelle']; ?>" data-price="</?php echo $row2['prix']; ?>">
                        </?php echo $row2['libelle']; ?>
                    </option>
                </?php endforeach; ?>
            </optgroup>
        </select>
    </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('prix-unitaire'); ?></label>
            <input type="text" class="form-control" id="pu" name="pu[]" readonly>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var stockSelect = document.getElementById('stock_id');
        var priceInput = document.getElementById('pu');

        stockSelect.addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        priceInput.value = price || ''; // Met à jour le champ de prix unitaire
         });
        });
    </script>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('quantité'); ?></label>
            <input type="text" class="form-control" id="quantite" name="quantite[]">
        </div>
    </div>
    <div class="col-md-3">
    <div class="form-group">
            <label for="amount_consult"></?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
            <input type="text" class="form-control" id="prixT" name="prixT[]" value="0" readonly>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
                <label for="field-1" class="control-label"></?php echo get_phrase('dosage'); ?></label>
        <input type="text" class="form-control" id="dosage" name="dosage" >
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="date"></?php echo get_phrase('date-de-vente'); ?><sup class="color-danger">*</sup></label>
            <input type="text" class="form-control" id="date_vente" name="date_vente"  data-validation="required" value="</?php 
            date_default_timezone_set('Africa/Niamey'); 
            echo date("d-m-Y H:i:s"); ?>" >
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('% remise'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="0"  data-validation="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('prise-en-charge'); ?></label>
            <input type="text" class="form-control" id="discount_amount" name="prise_en_charge" data-validation="" placeholder="Veillez saisir le nom de la personne" title ="Veillez saisir le nom de la personne physique ou morale">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="name13"></?php echo get_phrase('% prise-en-charge'); ?><sup class="color-danger"></sup></label>
            <input type="number" class="form-control" id="pourcentage_prise" name="pourcentage_prise" value="0"  data-validation="">
        </div>
    </div>
    <div class="col-md-3">
            <div class="form-group">
                <label for="name13"></?php echo get_phrase('mode-paiement'); ?></label>
                <select class="form-control" id="js-states" name="mode_paiement">
                    <optgroup>
                    <option value="Espèce"></?php echo get_phrase('Espèce'); ?></option>
                    <option value="Chèque"></?php echo get_phrase('Chèque'); ?></option>
                    <option value="Carte-bancaire"></?php echo get_phrase('Carte_bancaire'); ?></option>
                    <option value="Virement"></?php echo get_phrase('Virement'); ?></option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="statuT"></?php echo get_phrase('statut'); ?><sup class="color-danger"></sup></label>
                <select class="form-control" id="status" name="status" data-validation="">
                 <optgroup>
                  <option value="payé"></?php echo get_phrase('payé'); ?></option>
                    <option value="impayé"></?php echo get_phrase('impayé'); ?></option>
                 </optgroup>
                </select>  
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="receptionist_id"></?php echo get_phrase('utilisteur'); ?></label>
                <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="">
                    <optgroup>
                    <option>Sélectionner votre nom</option>
                    </?php
                    $receptionistes = $this->db->get('receptionist')->result_array();
                    $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
                    foreach ($receptionistes as $row2):
                        ?>
                   <option value="</?php echo $row2['name']; ?>">
                    </?php echo $row2['name']; ?>
                    </option>
                        </?php endforeach; ?>
                    </optgroup>
                </select>
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
    <div class="modal-footer no-margin-top">
    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
    </div>              
    </div>
    </div>-->
    <!--<script>
    // Récupérer les éléments du DOM de ventes
    var puInput = document.getElementById("pu");
    var quantiteInput = document.getElementById("quantite");
    //var priseInput = document.getElementById("pourcentage_prise");
    var prixTInput = document.getElementById("prixT");
    // Écouter les modifications des entrées de montant et remise
    puInput.addEventListener("input", calculerPrixT);
    quantiteInput.addEventListener("input", calculerPrixT);
    //priseInput.addEventListener("input", calculerMontantNet);

    function calculerPrixT() {
      // Récupérer les valeurs entrées
      var pu = parseFloat(puInput.value);
      var quantite = parseFloat(quantiteInput.value);
      //var prise = parseFloat(priseInput.value);

      // Calculer le montant net
      var prixT = (pu * quantite);

      // Afficher le montant net arrondi à 2 décimales
      prixTInput.value = prixT.toFixed(0);
    }

    // Récupérer les éléments du DOM
    /*var montantInput = document.getElementById("entry_amount");
    var remiseInput = document.getElementById("discount_amount");
    var priseInput = document.getElementById("pourcentage_prise");
    var montantNetInput = document.getElementById("net_amount");
    var avanceInput = document.getElementById("avance");
    var montantResteInput = document.getElementById("reste_payer");
    // Écouter les modifications des entrées de montant et remise
    avanceInput.addEventListener("input", calculerMontantReste);
    montantNetInput.addEventListener("input", calculerMontantReste);*/


    function calculerMontantReste() {
      // Récupérer les valeurs entrées
      var montantNet = parseFloat(montantNetInput.value);
      var avance = parseFloat(avanceInput.value);

      // Calculer le montant net
      var montantReste = montantNet - avance;

      // Afficher le montant net arrondi à 2 décimales
      montantResteInput.value = montantReste.toFixed(0);
    }

</script>-->


<script type="text/javascript">
    function showEditInvoiceAjaxModal(url)
    {       
        jQuery('#modal_edit .modal-body').html('<div style="text-align:center;margin-top:200px;"></div>');
        jQuery('#modal_edit').modal('show', {backdrop: 'true'});        
        $.ajax({
            url: url,
            success: function (response)
            {
                jQuery('#modal_edit .modal-body').html(response);
            }
        });
    }
</script>
<div id="modal_edit" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
    
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    </?php echo $system_name; ?>
                </div>
            </div>	
            <?php if ($account_type == 'admin' && $page_name == 'invoice') { ?>
                <div class="modal-body " style="height:520px; overflow:auto;">
                <?php } else { ?>	
                    <div class="modal-body " style="height:500px; overflow:auto;">					
                    <?php } ?>

                </div>
                <div class="modal-footer no-margin-top">
                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    function showAjaxModal(url)
    {       
        jQuery('#modal_ajax .modal-body').html('<div style="text-align:center;margin-top:200px;"></div>');
        jQuery('#modal_ajax').modal('show', {backdrop: 'true'});        
        $.ajax({
            url: url,
            success: function (response)
            {
                jQuery('#modal_ajax .modal-body').html(response);
            }
        });
    }
</script>
<div id="modal_ajax" class="modal fade col-md-8" tabindex="-1" style="margin-top: 5px;left: 360px; overflow: auto;">
    
        <div class="modal-content">
            <div class="modal-header no-padding">
                <div class="table-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="white">&times;</span>
                    </button>
                    </?php echo $system_name; ?>
                </div>
            </div>  
            <?php if ($account_type == 'admin' && $page_name == 'invoice') { ?>
                <div class="modal-body " style="height:520px; overflow:auto;">
                <?php } else { ?>   
                    <div class="modal-body " style="height:500px; overflow:auto;">                  
                    <?php } ?>

                </div>
                <div class="modal-footer no-margin-top">
                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function confirm_modal(delete_url)
        {
            jQuery('#modal-4').modal('show', {backdrop: 'static'});
            document.getElementById('delete_link').setAttribute('href', delete_url);
        }
    </script>
    <div class="modal fade" id="modal-4">
        <div class="modal-dialog">
            <div class="modal-content" style="margin-top:100px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="text-align:center;">Êtes-vous sûr de vouloir supprimer ces informations ?</h4>
                </div>
                <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                    <a href="#" class="btn btn-danger" id="delete_link"><?php echo get_phrase('supprimer'); ?></a>
                    <button type="button" class="btn btn-info" data-dismiss="modal"><?php echo get_phrase('annuler'); ?></button>
                </div>
            </div>
        </div>
    </div>
	<?php 
	$output .= ob_get_clean();
	echo $output;	