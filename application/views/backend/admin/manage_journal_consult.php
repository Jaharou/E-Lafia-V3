
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
<?php ob_start();  ?>
    <?php 
        $insert_payments   = $this->db->get('payment')->result_array();
            foreach ($insert_payments as $row): ; ?>
            
                <?php endforeach; ?>
                <h3>Paiement consultation et examen</h3>
                <br>
                <?php
                        
                    // Exécuter une requête pour récupérer les consultations
                    $consultations = $this->db->query("SELECT * FROM invoice 
                       ")->result_array();

                    // Compter le nombre total de consultations
                    $totalConsultations = 0;
                    foreach ($consultations as $row) {
                        $entries = json_decode($row['invoice_entries'], true);
                        if (is_array($entries)) {
                            $totalConsultations += count($entries);
                        }
                    }
                ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('consultations-total') ?></span>
                        <span class="number counter"><?php echo ($totalConsultations); ?></span>
                        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                    </a>
                </div>

                <?php
                        
                // Exécuter une requête pour récupérer les consultations
                $consultations = $this->db->query("SELECT * FROM invoice 
                    WHERE due_timestamp=2025")->result_array();

                // Calculer le montant total des consultations
                $totalNet = 0;
                foreach ($consultations as $row) {
                    $entries = json_decode($row['invoice_entries'], true);
                    if (is_array($entries)) {
                        foreach ($entries as $entry) {
                            if (isset($entry['net_amount'])) {
                                $totalNet += floatval($entry['net_amount']);
                            }
                        }
                    }
                }
                // Afficher le montant total des consultations
                ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('montant-total-consultations') ?></span>
                        <span class="number counter"><?php echo ($totalNet).'F'; ?></span>
                        <span class="bg-icon"><i class="fa fa-money"></i></span>
                    </a>
                </div>

                <?php
                    
                // Exécuter une requête pour récupérer les examens
                $exams = $this->db->query("SELECT * FROM examen 
                    ")->result_array();
                // Compter le nombre total de exams
                $totalExamens = 0;
                foreach ($exams as $row) {
                    $entries = json_decode($row['examen_entries'], true);
                    if (is_array($entries)) {
                        $totalExamens += count($entries);
                    }
                }
                ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('examens-total') ?></span>
                        <span class="number counter"><?php echo ($totalExamens); ?></span>
                        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                    </a>
                </div>

                <?php
                
                // Exécuter une requête pour récupérer les examens
                $exams = $this->db->query("SELECT * FROM examen 
                    ")->result_array();

                // Calculer le amountT total des exams
                $totalAmountT = 0;
                foreach ($exams as $row) {
                    $entries = json_decode($row['examen_entries'], true);
                    if (is_array($entries)) {
                        foreach ($entries as $entry) {
                            if (isset($entry['amountT'])) {
                                $totalAmountT += floatval($entry['amountT']);
                            }
                        }
                    }
                }
                // Afficher le amountT total des consultations
                ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <a class="dashboard-stat bg-primary" href="#">
                        <span class="name"><?php echo get_phrase('montant-total-examens') ?></span>
                        <span class="number counter"><?php echo ($totalAmountT).'F'; ?></span>
                        <span class="bg-icon"><i class="fa fa-money"></i></span>
                    </a>
                </div>
            <br>

            <div class="col-md-12">
                <div class="panel">    
                    <div class="panel-body p-20">
                <table id="example-two" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo get_phrase('N°'); ?></th>
                        <th><?php echo get_phrase('créer-par'); ?></th>
                        <th><?php echo get_phrase('patient'); ?></th>
                        <th style="text-align: center;"><?php echo get_phrase('date'); ?></th>
                        <th><?php echo get_phrase('prestation'); ?></th>
                        <th><?php echo get_phrase('montant-total'); ?></th>
                        <th><?php echo get_phrase('statut'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;

                    // Récupération des informations des consultations

                    $this->db->select('invoice.*, patient.name, patient.prenom');
                    $this->db->from('invoice');
                    $this->db->join('patient', 'patient.patient_id = invoice.patient_id');
                    $this->db->where('invoice.creation_datetime=2025');
                    $this->db->order_by('invoice.creation_datetime', 'DESC');
                    $invoice_info = $this->db->get()->result_array();
                    // Affichage des données des factures
                    foreach ($invoice_info as $row): $i++; ?>   
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $row['receptionist_id']; ?></td>
                            <td><?php echo $row['name'] . ' ' . $row['prenom']; ?></td>
                            <td><?php echo $row['creation_datetime']; ?></td>
                            <td>
                                <?php
                                $invoice_entries = json_decode($row['invoice_entries']);
                                foreach ($invoice_entries as $invoice_entry) {
                                    echo $invoice_entry->description . " ";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $total_amount = 0;
                                foreach ($invoice_entries as $invoice_entry) {
                                    //echo $invoice_entry->net_amount . " ";
                                    $total_amount += $invoice_entry->net_amount;
                                }
                                 echo $total_amount;
                                ?>
                            </td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>                           
                    <?php endforeach; ?>

                    <?php 
                        // Récupération des informations des examens
                    $this->db->select('examen.*, patient.name, patient.prenom');
                    $this->db->from('examen');
                    $this->db->join('patient', 'patient.patient_id = examen.patient_id');
                    $this->db->order_by('examen.creation_time', 'DESC');
                    $examen_info = $this->db->get()->result_array();
                        // Affichage des données des examens
                    foreach ($examen_info as $row): $i++; ?>   
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $row['receptionist_id']; ?></td>
                            <td><?php echo $row['name'] . ' ' . $row['prenom']; ?></td>
                            <td><?php echo $row['creation_time']; ?></td>
                            <td>
                                <?php
                                $examen_entries = json_decode($row['examen_entries']);
                                foreach ($examen_entries as $examen_entry) {
                                    echo $examen_entry->description . " ";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $total_examen = 0;
                                foreach ($examen_entries as $examen_entry) {
                                    //echo $examen_entry->amountT . " ";
                                    $total_examen += $examen_entry->amountT;
                                }
                                 echo $total_examen;
                                ?>
                            </td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>                           
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Total:</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>                
        </div>              
    </div>
</div>
<script>
    // Inverser l'ordre des lignes du tableau après le chargement de la page
    document.addEventListener("DOMContentLoaded", function () {
        var table = document.getElementById("example");
        var tbody = table.getElementsByTagName("tbody")[0];
        var rows = Array.from(tbody.getElementsByTagName("tr")).reverse();
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    });
</script>
        <?php 
    $output .= ob_get_clean();
echo $output;