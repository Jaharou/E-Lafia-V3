<?php
/*  
 *  Tamplate: Show Payment History
 *  @author : Raju Ahmed
 *  Date    : 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); $today=date('Y-m-d'); ?>
<?php $patient_info = $this->db->get('patient')->result_array(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <a href="<?php echo base_url(); ?>doctor/type_crud/add" class="btn bg-primary btn-wide pull-right">
                <?php echo get_phrase('ordonnance_type'); ?>
        </a>
             <a href="<?php echo base_url(); ?>doctor/type" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>                     
            <div class="panel-body p-20">
            <form method="post" action="<?php echo base_url(); ?>doctor/type/create" class="p-20" id="form-validate" enctype="multipart/form-data">             
                    <!--<h5 class="mt-n"></?php echo get_phrase('Les informations'); ?></h5>-->
        <fieldset class="panel-primary"> 
                <legend class="panel-primary">Patient</legend>
                    

                    <div class="col-md-12">
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

<div class="col-md-6">
    <div class="form-group">
        <label for="age"><?php echo get_phrase('age'); ?><sup class="color-danger"></sup></label>
        <input type="number" class="form-control" id="age" name="age" readonly>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Écoutez l'événement "change" de la liste déroulante
        $('#js-states').on('change', function() {
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
        <label for="name13"><?php echo get_phrase('date'); ?><sup class="color-danger"></sup></label>
        <input type="text" class="form-control" id="name13" name="ordo_timestamp"  data-validation="" value="<?php 
        date_default_timezone_set('Africa/Niamey'); 
        echo date("d-m-Y H:i:s"); ?>"
        readonly >
    </div>
</div>
</fieldset>     
                                  
<div class="panel-body p-20">
                  <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="col-md-1"><?php echo get_phrase('n°'); ?></th>
                                <th class="col-md-4" style="text-align: center;"><?php echo get_phrase('produits'); ?></th>
                                <th class="col-md-2" style="text-align: center;"><?php echo get_phrase('Nbres'); ?></th>
                                <th class="col-md-2" style="text-align: center;"><?php echo get_phrase('P. Unit'); ?></th>
                                <th class="col-md-2" style="text-align: center;"><?php echo get_phrase('P. Total'); ?></th>
                                <th class="col-md-3"><?php echo get_phrase('options'); ?></th>
                            </tr>
                        </thead>
                            <tbody>
                                 <?php foreach ($type_info as $row): ?>   
                                        <tr>
                                            <td><?php echo $row['id_type'] ?></td>
                                            <td style="text-align:center;"><?php echo $row['produit'] ?></td>
                                            <td><?php echo $row['nbrs'] ?></td>
                                            <td><?php echo $row['prix'] ?></td>
                                            <td><?php echo $row['total'] ?></td>
                                            <td>
                                                <a class="btn btn-danger btn-rounded icon-only" href="#" onclick="confirm_modal('<?php echo base_url(); ?>admin/type/delete/<?php echo $row['id_type']; ?>');">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>  
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                          </tbody>
                        </table>                
            </div>
<?php 
$output .= ob_get_clean();
echo $output;