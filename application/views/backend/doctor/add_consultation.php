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
             <a href="<?php echo base_url(); ?>doctor/consultation" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>						
			<div class="panel-body p-20">
			<form method="post" action="<?php echo base_url(); ?>doctor/consultation/create" class="p-20" id="form-validate" enctype="multipart/form-data">

        <div class="row panel panel-primary col-md-6"> 
            <legend class="panel-primary">Info patient</legend>
                    <div class="col-md-12">
                        <div class="form-group">
                        <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
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
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="age"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
        <input type="number" class="form-control" id="age" name="age" readonly>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="sex"><?php echo get_phrase('sexe'); ?><sup class="color-danger"></sup></label>
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
</div><br><br><br>

<!-- Début espace Médecin -->
<div class="row panel panel-primary col-md-6 pull-right" style="margin-left: 0px;">
<div class="col-md-12">
    
<div class="form-group">
        <label for="patient_id"><?php echo get_phrase('médecin'); ?><sup class="color-danger">*</sup></label>
        <select name="patient_id" class="form-control select2" id="patient_id" required>
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
</div>
                    
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
<!-- Fin d'espace Medécin -->

<!-- Début d'espace de paiement -->
<!--<div class="row col-md-3 panel panel-primary pull-right">
<div class="col-md-12">
    <div class="form-group">
        <label class="pull-left" for="amount"></?php echo get_phrase('montant'); ?></label>
        <input type="text" class="form-control" name="amount" id="amount" value="3000" readonly>
        
</div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="payement"></?php echo get_phrase('paiement'); ?><sup class="color-danger"></sup></label>
        <input type="number" class="form-control" name="payement" id="payement" value="0" readonly>
    </div>
</div>
                    
<div class="col-md-12">
    <div class="form-group">
        <label class="pull-left" for="reste_payer"></?php echo get_phrase('Reste_à_payer'); ?></label>
        
        <input type="text" class="form-control" name="reste_payer" id="reste_payer" value="3000" readonly>
        
</div>
</div>
</div>-->
<!-- fin d'espace de paiement -->


<!-- Début d'espace de paiement -->
<div class="row">
<div class="row panel panel-primary col-md-6" style="right: 540px; top:20px;">
<div class="col-md-12">
    <div class="form-group">
        <label for="motif"><?php echo get_phrase('motif'); ?><sup class="color-danger"></sup></label>
        <textarea type="text" class="form-control" name="type_consultation" id="type_consultation"></textarea>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="motif"><?php echo get_phrase('Examen_clinique'); ?><sup class="color-danger"></sup></label>
        <textarea type="text" class="form-control" name="examen_clinic" id="examen_clinic"></textarea>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <label for="motif"><?php echo get_phrase('Examen_paraclinique'); ?><sup class="color-danger"></sup></label>
        <textarea type="text" class="form-control" name="examen_paraclinic" id="examen_paraclinic"></textarea>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="motif"><?php echo get_phrase('diagnostic'); ?><sup class="color-danger"></sup></label>
        <textarea type="text" class="form-control" name="diagnostic" id="diagnostic"></textarea>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="traitement" type="textarea"><?php echo get_phrase('traitement'); ?><sup class="color-danger"></sup></label>
        
        <select name="traitement" class="form-control select2" id="traitement" required>
                   <optgroup>
                    <option>Sélectionner le traitement</option>
                    <?php
                     $traitements = $this->db->get('fiche_suivi')->result_array();
                        $traitements = array_reverse($traitements); // Inverser l'ordre des traitements
                        foreach ($traitements as $row2):
                            ?>
                       <option value="<?php echo $row2['fiche_id']; ?>">
                        <?php echo $row2['traitement']; ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>
        
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="motif"><?php echo get_phrase('Examen_demandé'); ?><sup class="color-danger"></sup></label>
        <textarea type="text" class="form-control" name="examen_a_faire" id="examen_a_faire"></textarea>
    </div>
</div>    
</div>
<!-- fin d'espace de paiement -->

<div class="row panel panel-primary col-md-6 pull-right" style="bottom: 550px;">
<legend class="col-md-12 panel-primary" scope="col">Paramètres</legend>
<table class="table table-hover">
  <thead>
    <tr>
      <th class="col-md-5" scope="col">Paramètre</th>
      <th class="col-md-2" scope="col">Valeur</th>
      <th class="col-md-5" scope="col">Remarque</th>
      
    </tr>
  </thead>
  <tbody>
    <tr class="table-active form-group">
      <th scope="row">Poids</th>
      <td><input class="form-control" type="text" name="poid" id="poid"></td>
      <td><input class="form-control" type="text" name="poid" id="poid"></td>
      
    </tr>
    <tr class="form-group">
      <th scope="row">Taille</th>
      <td><input class="form-control" type="text" name="taille_patient  " id="taille_patient    "></td>
      <td><input class="form-control" type="text" name="taille_patient  " id="taille_patient    "></td>
    </tr>
    <tr class="form-group table-primary">
      <th scope="row">Température</th>
      <td><input class="form-control" type="text" name="temperature" id="temperature"></td>
      <td><input class="form-control" type="text" name="temperature" id="temperature"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row">Pression artérialle diastolique</th>
      <td><input class="form-control" type="text" name="pression_diasto" id="pression_diasto"></td>
      <td><input class="form-control" type="text" name="pression_diasto" id="pression_diasto"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row">Pression artérialle systolique</th>
      <td><input class="form-control" type="text" name="pression_systo" id="pression_systo"></td>
      <td><input class="form-control" type="text" name="pression_systo" id="pression_systo"></td>
    </tr>
    <tr class="form-group table-secondary ">
      <th scope="row"><p style="color:rgb(26, 26, 26);">Indice de masse corporelle</p></th>
      <td><input class="form-control" type="text" name="indice_masse" id="indice_masse"></td>
      <td><input class="form-control" type="text" name="indice_masse" id="indice_masse"></td>
      
    </tr>
    
  </tbody>
</table>
</div>


    <div class="row">
        <div class="col-md-12" style="bottom: 400px;">
		    <div class="btn-group pull-right mt-10" role="group">
		        <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
		        <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
		    </div>				  
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