<?php
/* 	
 * 	Tamplate: Add Report
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
$patient_info = $this->db->get('patient')->result_array(); 
$doctor_info = $this->db->get('doctor')->result_array();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3><?php echo get_phrase('nouveau_rapport'); ?></h3>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>nurse/report/create" method="post" enctype="multipart/form-data" >
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('type'); ?></span>						
                            <select name="type" class="form-control">
                                <option value=""><?php echo get_phrase('sélectionner le type de rapport'); ?></option>
                                <option value="operation"><?php echo get_phrase('opération'); ?></option>
                                <option value="birth"><?php echo get_phrase('naissance'); ?></option>
                                <option value="death"><?php echo get_phrase('décés'); ?></option>
                            </select>
					</div>
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('description'); ?></span>
						 <textarea name="description" class="form-control" id="field-ta"></textarea>
					</div>
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('date'); ?></span>
						<input name="timestamp" type="date" class="form-control" placeholder="" aria-describedby="basic-addon5">
					</div>
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('patient'); ?></span>
                            <select name="patient_id" class="form-control">
                                <option value=""><?php echo get_phrase('sélectionner_le_patient'); ?></option>
                                <?php 
                                 $patients = $this->db->get('patient')->result_array();
                                 $patients = array_reverse($patients); // Inverser l'ordre des patients
                                foreach ($patients as $row) { ?>
                                    <option value="<?php echo $row['patient_id']; ?>"><?php echo $row['name'].' '.$row['prenom']; ?></option>
                                <?php } ?>
                            </select>
					</div>
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('docteur'); ?></span>
                            <select name="doctor_id" class="form-control">
                                <option value=""><?php echo get_phrase('sélectionner_le_docteur'); ?></option>
                                <?php foreach ($doctor_info as $row) { ?>
                                    <option value="<?php echo $row['doctor_id']; ?>"><?php echo $row['name']; ?></option>
                                <?php } ?>
                            </select>
					</div>
                    <div class="col-sm-12 control-label">
                        <input name="submit" type="submit" class="btn btn-primary" value="sauvegarder">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
$output .= ob_get_clean();
echo $output;