<?php
/* 	
 * 	Tamplate: Edit Appointment
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
$single_appointment_info = $this->db->get_where('appointment', array('appointment_id' => $param2))->result_array();
foreach ($single_appointment_info as $row):
?>
    <div class="col-md-10" style="margin-left: 100px;">
        <a href="<?php echo base_url(); ?>doctor/appointment" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3 style="text-align: center;"><?php echo get_phrase('modification'); ?></h3>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>doctor/appointment/update/<?php echo $row['appointment_id']; ?>" method="post" enctype="multipart/form-data" >
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('date'); ?></span>
						<input name="date_timestamp" type="text" value="<?php echo date("D, d M Y", strtotime($row['date_timestamp'])); ?>" class="form-control" placeholder="" aria-describedby="basic-addon5">
					</div>
					<div class="input-group mb-20">
						<span class="input-group-addon" id="basic-addon5"><?php echo get_phrase('heure'); ?></span>
						<input name="time_timestamp" type="time" value="<?php echo date("H:i", strtotime($row['date_timestamp'])); ?>" class="form-control" placeholder="" aria-describedby="basic-addon5">
					</div>						
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
                       <option value="<?php echo $row2['patient_id']; ?>" <?php if ($row['patient_id'] == $row2['patient_id']) echo 'selected'; ?>>
                        <?php echo $row2['name']; ?>
                        </option>
                                <?php endforeach; ?>
                            </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 control-label">
                        <input name="submit" type="submit" class="btn bg-black btn-wide" value="Modifier">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php 
$output .= ob_get_clean();
echo $output;