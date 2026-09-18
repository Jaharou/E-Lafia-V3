<?php
/*
 * Fragment modal : approuver un rendez-vous — module réceptionniste.
 * Chargé via modal/popup/approve_appointment/{appointment_id}.
 */
$single_appointment_info = $this->db->get_where('appointment', array('appointment_id' => $param2))->result_array();
foreach ($single_appointment_info as $row) {
    $patient = $this->db->get_where('patient', array('patient_id' => $row['patient_id']))->row();
    $doctors = $this->db->get('doctor')->result_array();
?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3><?php echo get_phrase('approuver-le-rendez-vous'); ?></h3>
                    </div>
                </div>
                <div class="panel-body">
                    <form role="form" class="form-horizontal form-groups-bordered" method="post"
                        action="<?php echo base_url(); ?>receptionist/appointment_requested/approve/<?php echo $row['appointment_id']; ?>"
                        enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo get_phrase('patient'); ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-static">
                                    <?php echo $patient ? htmlspecialchars($patient->name . ' ' . $patient->prenom) : 'Patient non renseigné'; ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="doctor_id" class="col-sm-3 control-label"><?php echo get_phrase('docteur'); ?></label>
                            <div class="col-sm-7">
                                <select name="doctor_id" id="doctor_id" class="form-control">
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?php echo $doc['doctor_id']; ?>" <?php if ($doc['doctor_id'] == $row['doctor_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($doc['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date'); ?></label>
                            <div class="col-sm-7">
                                <div class="date-and-time">
                                    <input type="text" name="date_timestamp" class="form-control datepicker" data-format="D, dd MM yyyy"
                                           value="<?php echo date("D, d M Y", $row['timestamp']); ?>">
                                    <input type="text" name="time_timestamp" class="form-control timepicker" data-template="dropdown"
                                           data-show-seconds="false" data-default-time="00:05 AM" data-show-meridian="false"
                                           data-minute-step="5" value="<?php echo date("H:i", $row['timestamp']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 control-label col-sm-offset-2">
                            <input type="submit" class="btn btn-success" value="<?php echo get_phrase('approuver'); ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
