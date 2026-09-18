<?php
if (!defined('BASEPATH')) exit('Direct script access denied.');
$doctor_info = $this->crud_model->select_doctor_info();
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><i class="fa fa-calendar"></i> Agenda des rendez-vous</div>
            </div>
            <div class="panel-body p-20">
                <!-- Formulaire de filtre -->
                <form method="post" action="<?php echo base_url('receptionist/appointment/filter'); ?>"
                      class="form-inline" style="margin-bottom:16px;">
                    <div class="form-group" style="margin-right:8px;">
                        <label>Médecin&nbsp;</label>
                        <select name="doctor_id" class="form-control">
                            <option value="all">Tous les médecins</option>
                            <?php foreach ($doctor_info as $doc): ?>
                                <option value="<?php echo (int)$doc['doctor_id']; ?>"
                                    <?php echo (isset($doctor_id) && $doctor_id == $doc['doctor_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doc['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Du&nbsp;</label>
                        <input type="date" name="start_timestamp" class="form-control"
                               value="<?php echo isset($start_timestamp) ? date('Y-m-d', $start_timestamp) : date('Y-m-d', strtotime('-30 days')); ?>">
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Au&nbsp;</label>
                        <input type="date" name="end_timestamp" class="form-control"
                               value="<?php echo isset($end_timestamp) ? date('Y-m-d', $end_timestamp) : date('Y-m-d'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filtrer</button>
                </form>

                <!-- Formulaire d'ajout -->
                <form method="post" action="<?php echo base_url('receptionist/appointment/create'); ?>"
                      class="form-inline" style="margin-bottom:16px;padding:12px;background:#f8f9fa;border-radius:6px;">
                    <strong style="display:block;margin-bottom:8px;">Nouveau rendez-vous</strong>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Médecin&nbsp;</label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($doctor_info as $doc): ?>
                                <option value="<?php echo (int)$doc['doctor_id']; ?>">
                                    <?php echo htmlspecialchars($doc['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Patient&nbsp;</label>
                        <select name="patient_id" class="form-control" id="appt_patient_id"
                                data-patient-autocomplete="1"
                                data-placeholder="Rechercher un patient"
                                style="min-width:220px;" required>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Date&nbsp;</label>
                        <input type="date" name="date_timestamp" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-right:8px;">
                        <label>Heure&nbsp;</label>
                        <input type="time" name="time_timestamp" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Ajouter
                    </button>
                </form>

                <!-- Tableau des rendez-vous -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>Médecin</th>
                                <th>Date &amp; heure</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($appointment_info)):
                            $i = 0;
                            foreach ($appointment_info as $appt):
                                $i++;
                                $patient = $this->db->get_where('patient', array('patient_id' => $appt['patient_id']))->row();
                                $doctor  = $this->db->get_where('doctor',  array('doctor_id'  => $appt['doctor_id']))->row();
                                $patient_name = $patient ? htmlspecialchars($patient->name . ' ' . $patient->prenom) : 'N/A';
                                $doctor_name  = $doctor  ? htmlspecialchars($doctor->name)  : 'N/A';
                                // date_timestamp peut être un timestamp Unix ou une chaîne
                                $ts = $appt['date_timestamp'];
                                $date_affiche = is_numeric($ts) ? date('d/m/Y H:i', (int)$ts) : htmlspecialchars($ts);
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $patient_name; ?></td>
                                <td><?php echo $doctor_name; ?></td>
                                <td><?php echo $date_affiche; ?></td>
                                <td><span class="label label-success"><?php echo htmlspecialchars($appt['status'] ?? 'approuvé'); ?></span></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center">Aucun rendez-vous pour cette période.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
