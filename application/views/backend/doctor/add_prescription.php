<?php
/* 	
 * 	Tamplate: Add Prescription
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php $patient_info = $this->db->get('patient')->result_array(); ?>
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-primary">
            <!--<a href="</?php echo base_url(); ?>doctor/type_crud/add" class="btn bg-primary btn-wide pull-right">
                </?php echo get_phrase('ordonnance_type'); ?>
            </a>-->
             <a href="<?php echo base_url(); ?>doctor/prescription" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>						
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>doctor/prescription/create" class="p-20" id="form-validate" enctype="multipart/form-data">				
                    
            <fieldset class="panel-primary"> 

                <legend class="panel-primary">Patient</legend>
                    <a class="btn bg-black btn-wide icon-only pull-right" onclick="patientAjoutAjaxModal('<?php echo base_url(); ?>modal/popup/receptionist/patient_crud/add');" style= "margin-top: 28px; margin-right: 12px; padding-right: 104px;">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      <?php echo get_phrase('ajouter'); ?>
                    </a>
                    <div class="col-md-10">
                        <div class="form-group">

                        <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                        <select name="patient_id" class="form-control select2" id="patient_id" required>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="age"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="age" name="age" readonly>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js">
                    $(document).ready(function() {
                        // Écoutez l'événement "change" de la liste déroulante
                        $('#patient_id').on('change', function() {
                            var selectedPatientId = $(this).val();

                            // Recherchez le patient correspondant dans la liste des patients
                            var selectedPatient = <?php echo json_encode($patients); ?>.find(function(patient) {
                                return patient.patient_id == selectedPatientId;
                            });

                            // Si le patient est trouvé, affichez son âge dans le champ de texte
                            if (selectedPatient) {
                                $('#age').val(selectedPatient.age);
                            } else {
                                // Si le patient n'est pas trouvé, videz le champ de texte
                                $('#age').val('');
                            }
                        });
                    });
                </script>    
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="prescription"><?php echo get_phrase('date'); ?></label>
                        <?php
                        date_default_timezone_set('Africa/Niamey');
                        $currentDate = date("d-m-Y H:i:s");
                        ?>
                        <input type="text" class="form-control" id="prescription" name="prescription_timestamp" value="<?php date_default_timezone_set('Africa/Niamey'); 
                            echo date("d-m-Y H:i:s"); ?>" readonly>
                    </div>
            </fieldset>
            <fieldset class="panel-primary">
            <legend class="panel-primary">Médicament</legend>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="medicine_id"><?php echo get_phrase('médicament'); ?><sup class="color-danger">*</sup></label>        
                        <select style="padding:30px;"  multiple name="medicine_id[]" class="form-control select2" id="medicine_id" data-validation="required" >
                                <optgroup>
                                    <option>Sélectionner un ou plusieurs</option>
                                    <option readonly></option>
                                    <?php
                                    $medicine_query = $this->db->get('medicine')->result_array();
                                    $medicine_query = array_reverse($medicine_query); // Inverser l'ordre des patients
                                    foreach ($medicine_query as $row2):
                                    ?>
                                    
                                   <option value="<?php echo $row2['medicine_id']; ?>">
                                    <?php echo $row2['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                <label for="name13"><?php echo get_phrase('posologie'); ?></label>
                <textarea type="text" class="form-control" id="posologie" name="posologie" onfocus="removePlaceholder()" placeholder="Saisir une ou plusieurs"></textarea>
                </div>

              <script>
                function removePlaceholder() {
                    var textarea = document.getElementById('posologie');
                    textarea.setAttribute('placeholder', '');
                }
              </script>

            </div>
            <div class="col-md-4">
                <div class="form-group">
                 <label for="nbr_unite"><?php echo get_phrase("nbres-d'unité"); ?></label>
                <textarea type="text" class="form-control" id="nbr_unite" name="nbr_unite" onfocus="removePlaceholder()" placeholder="Saisir un ou plusieurs"></textarea>
                </div>
                <script>
                function removePlaceholder() {
                    var textarea = document.getElementById('nbr_unite');
                    textarea.setAttribute('placeholder', '');
                }
              </script>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                  <label for="qsp"><?php echo get_phrase('QSP'); ?></label>
                  <textarea type="textarea" class="form-control" id="qsp" name="qsp" onfocus="removePlaceholder()" placeholder="Saisir un ou plusieurs"></textarea>
                </div>
              <script>
                function removePlaceholder() {
                    var textarea = document.getElementById('qsp');
                    textarea.setAttribute('placeholder', '');
                }
              </script>
            </div>
               <div class="col-md-12">
                    <div class="form-group">
                      <label for="name13"><?php echo get_phrase('note'); ?></label>
                  <textarea type="text" class="form-control" id="note" name="note" data-validation="">
                 </textarea>
                    </div>
                </div>
            </fieldset>
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