<?php
/* 	
 * 	Tamplate: Add Bed
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
} ?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php $patient_info = $this->db->get('patient')->result_array(); ?>
<a href="<?php echo base_url(); ?>nurse/mesure" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
<div style="clear:both;"></div>                     
<div class="panel-body p-20">
<form method="post" action="<?php echo base_url(); ?>nurse/mesure/create" class="p-20" id="form-validate" enctype="multipart/form-data">
<div class="row panel panel-primary">

<div class="row  col-md-6">

<div class="rom col-md-12">
 
                <legend class="panel-primary">Info patient</legend>
                    
                        <div class="form-group">
                        <label for="name13"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                        <select name="patient_id" class="form-control select2" id="patient_id" required>
                       <optgroup>
                        <option>Sélectionner le patient</option>
                        <?php
                        $patients = $this->db->get('patient')->result_array();
                        $patients = array_reverse($patients); // Inverser l'ordre des patients
                        foreach ($patients as $row2): ?>
                       <option value="<?php echo $row2['patient_id']; ?>">
                        <?php echo $row2['name'].' '.$row2['prenom']; ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>
    </div>

    <div class="form-group">
        <label for="name13"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
        <input type="number" class="form-control" id="age" name="age" readonly>
    </div>

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
   
                    
<div class="row col-md-6 pull-right" style="bottom: 0px;">
    <legend class="">Paramétres de santé</legend>
<table class="table table-hover">
  <thead>
    <tr>
      <th class="col-md-5" scope="col">Libellé</th>
      <th class="col-md-7" scope="col">Valeur</th>
    </tr>
  </thead>
  <tbody>
    <tr class="table-active form-group">
      <th scope="row">Poids</th>
      <td><input class="form-control" type="text" name="poid" id="poid"></td> 
    </tr>
    <tr class="form-group">
      <th scope="row">Taille</th>
      <td><input class="form-control" type="text" name="taille_patient  " id="taille_patient"></td>
    </tr>
    <tr class="form-group table-primary">
      <th scope="row">Température</th>
      <td><input class="form-control" type="text" name="temperature" id="temperature"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row">Tension artérialle diastolique</th>
      <td><input class="form-control" type="text" name="pression_diasto" id="pression_diasto"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row">Tension artérialle systolique</th>
      <td><input class="form-control" type="text" name="pression_systo" id="pression_systo"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row"><p style="color:rgb(26, 26, 26);">Indice de masse corporelle</p></th>
      <td><input class="form-control" type="text" name="indice_masse" id="indice_masse"></td>
    </tr>
  </tbody>
</table>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="btn-group pull-right mt-10" role="group">
            <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
            <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
        </div>                
    </div>
</div>
</div>             
</form>
</div>
<?php 
$output .= ob_get_clean();
echo $output;