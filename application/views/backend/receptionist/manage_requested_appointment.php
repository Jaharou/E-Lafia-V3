<?php
if (!defined('BASEPATH')) exit('Direct script access denied.');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><i class="fa fa-clock-o"></i> Demandes de rendez-vous en attente</div>
            </div>
            <div class="panel-body p-20">
                <?php if (empty($requested_appointment_info)): ?>
                    <div class="alert alert-info">Aucune demande de rendez-vous en attente.</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Médecin</th>
                                <th>Date demandée</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($requested_appointment_info as $appt):
                            $i++;
                            $patient = $this->db->get_where('patient', array('patient_id' => $appt['patient_id']))->row();
                            $doctor  = $this->db->get_where('doctor',  array('doctor_id'  => $appt['doctor_id']))->row();
                            $patient_name = $patient ? htmlspecialchars($patient->name . ' ' . $patient->prenom) : 'Patient #'.$appt['patient_id'];
                            $doctor_name  = $doctor  ? htmlspecialchars($doctor->name)  : 'Médecin #'.$appt['doctor_id'];
                            $ts = $appt['date_timestamp'];
                            $date_affiche = is_numeric($ts) ? date('d/m/Y H:i', (int)$ts) : htmlspecialchars($ts);
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $patient_name; ?></td>
                                <td><?php echo $doctor_name; ?></td>
                                <td><?php echo $date_affiche; ?></td>
                                <td><span class="label label-warning">En attente</span></td>
                                <td>
                                    <a href="<?php echo base_url('receptionist/appointment_requested/approve/'.$appt['appointment_id']); ?>"
                                       class="btn btn-success btn-sm"
                                       onclick="return confirm('Approuver ce rendez-vous ?');">
                                        <i class="fa fa-check"></i> Approuver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
