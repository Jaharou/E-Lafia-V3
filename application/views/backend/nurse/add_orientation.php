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
<div class="row panel panel-primary">
<h3 class="mt-n"><?php echo get_phrase('orientation'); ?></h3>
    <div class="col-md-12">
        <div class="panel">
            <div style="clear:both;"></div>                     
            <div class="panel-body p-20">
                
            <form method="post" action="<?php echo base_url(); ?>nurse/orientation/create" class="p-20" id="form-validate" enctype="multipart/form-data">   
                    
         <div class="row">
            <div class="row panel panel-primary col-md-12"> 
                <legend class="panel-primary">Info patient</legend>
                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry_temp">
                        <div class="form-group">
                            <label for="field-1" class="col-sm-12 control-label"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>

                            <div class="col-sm-12">
                                
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
            <!--<div class="col-sm-2">
            <a class="btn bg-primary btn-wide icon-only pull-right" onclick="showAjaxModal('</?php echo base_url(); ?>modal/popup/add_patient/');">
            <i class="fa fa-plus" aria-hidden="true"></i>
            </?php echo get_phrase('ajouter'); ?>
            </a>
        </div>-->
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="name13"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
        <input type="number" class="form-control" id="age" name="age" readonly>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label for="name13"><?php echo get_phrase('sexe'); ?><sup class="color-danger"></sup></label>
        <input type="text" class="form-control" id="sex" name="sex" readonly>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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

            if (selectedPatient) {
                $('#sex').val(selectedPatient.sex);
            } else {
                // Si le patient n'est pas trouvé, videz le champ de texte
                $('#sex').val('');
            }
        });
    });
</script>

<div class="col-md-12">
    <div class="form-group">
        <label for="prescription"><?php echo get_phrase('date'); ?></label>
        <?php
        date_default_timezone_set('Africa/Niamey');
        $currentDate = date("d-m-Y H:i:s");
        ?>
        <input type="text" class="form-control" id="date_consultation" name="date_consultation" value="<?php 
                                date_default_timezone_set('Africa/Niamey'); 
                                echo date("d-m-Y H:i:s"); ?>" readonly>    
   </div>
</div>
</div>

<div class="row panel panel-primary">
<div class="col-sm-12">
        <div class="form-group">
            <label for="name13"><?php echo get_phrase('motif'); ?><sup class="color-danger">*</sup></label>
            <input type="text" class="form-control" id="motif" name="bed_number" required>
        </div>
</div>
                  
<div class="col-md-12">
<div class="form-group">
<label for="field-1" class="col-sm-8 control-label"><?php echo get_phrase('médecin'); ?></label>

<div class="col-sm-6">

<select name="doctor_id" class="form-control select2" id="doctor_id" required>
<optgroup>
<option>Sélectionner le médecin</option>
<?php
$medecines = $this->db->get('doctor')->result_array();
$medecines = array_reverse($medecines); // Inverser l'ordre des medecines
foreach ($medecines as $row2):
?>
<option value="<?php echo $row2['doctor_id']; ?>">
<?php echo $row2['name']; ?>
</option>
<?php endforeach; ?>
</optgroup>
</select>
</div>
<div class="form-group">
<label for="field-1" class="control-label"><?php echo get_phrase('salle'); ?></label>
<div class="col-sm-6">
<select name="salle" class="form-control" id="salle"  data-validation="">
    <optgroup label="<?php echo get_phrase('sélectionner le type'); ?>">   
    <option value="ward"><?php echo get_phrase('salle'); ?></option>
    <option value="cabin"><?php echo get_phrase('bloc'); ?></option>
    <option value="icu"><?php echo get_phrase('chambre'); ?></option>
    </optgroup>
</select>
</div>
</div>
</div>
</div>
                
<div class="col-md-6">
  <div class="form-group">
    <label for="field-1" class="control-label"><?php echo get_phrase('numéro'); ?></label>
       <input type="text" class="form-control" id="bed_number" name="bed_number" data-validation="">
    </div>
</div>

<div class="form-group">
  <label for="field-1" class="control-label"><?php echo get_phrase('statut'); ?></label>
    <div class="col-sm-6">
        <select name="statut" class="form-control" id="statut"  data-validation="required">
            <optgroup label="<?php echo get_phrase('sélectionner le type'); ?>">   
            <option value="En_attente"><?php echo get_phrase('En_attente'); ?></option>
            <option value="En_consultation"><?php echo get_phrase('En_consultation'); ?></option>
            <option value="Terminer"><?php echo get_phrase('Terminer'); ?></option>
            </optgroup>
        </select>
    </div>
</div>
                    
<div class="col-md-12">
    <div class="form-group">
        <label for="name12"><?php echo get_phrase('observation'); ?></label>
        <input type="text" class="form-control" id="name12" name="description" data-validation="">
    </div>
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