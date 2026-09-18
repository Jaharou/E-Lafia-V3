
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
                <h3>Liste du journal de paiements sur 24H</h3>
                <br>
       
            <div class="col-md-12">
            <div class="panel">
                <div class="panel-body p-20">
                    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?php echo get_phrase('N°'); ?></th>
                            <th><?php echo get_phrase('créer-par'); ?></th>
                            <th><?php echo get_phrase('patient'); ?></th>
                            <th><?php echo get_phrase('date'); ?></th>
                            <th><?php echo get_phrase('prestation'); ?></th>
                            <th><?php echo get_phrase('montant'); ?></th>
                            <th><?php echo get_phrase('statut'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Définir le fuseau horaire pour les heures correctes
                        date_default_timezone_set('Africa/Niamey');

                        // Créer un objet DateTime pour maintenant
                        $now = new DateTime();

                        // Définir les heures de début et de fin
                        $start_of_day = new DateTime();
                        $end_of_day = new DateTime();

                        // Définir le début et la fin de la période en fonction de l'heure actuelle
                        if ($now->format('H') >= 9) {
                            $start_of_day->setTime(9, 0);
                            $end_of_day->setTime(9, 0)->modify('+1 day');
                        } else {
                            $start_of_day->setTime(9, 0)->modify('-1 day');
                            $end_of_day->setTime(9, 0);
                        }

                        $start_of_day_mysql = $start_of_day->format('Y-m-d H:i:s');
                        $end_of_day_mysql = $end_of_day->format('Y-m-d H:i:s');

                        // Récupération des informations des factures pour la période de 24 heures
                        $this->db->select('invoice.*, patient.name, patient.prenom');
                        $this->db->from('invoice');
                        $this->db->join('patient', 'patient.patient_id = invoice.patient_id');
                        $this->db->where("STR_TO_DATE(creation_datetime, '%d-%m-%Y %H:%i:%s') BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
                        $invoices = $this->db->get()->result_array();

                        // Récupération des informations des examens pour la période de 24 heures
                        $this->db->select('examen.*, patient.name, patient.prenom');
                        $this->db->from('examen');
                        $this->db->join('patient', 'patient.patient_id = examen.patient_id');
                        $this->db->where("STR_TO_DATE(creation_time, '%d-%m-%Y %H:%i:%s') BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
                        $exams = $this->db->get()->result_array();

                        // Affichage des factures
                        $i = 1; // Compteur pour les factures
                        foreach ($invoices as $invoice) {
                         // Incrémenter le compteur pour chaque facture

                        // Récupérer le nom et prénom du patient en utilisant l'ID du patient depuis la facture
                        $patient = $this->db->get_where('patient', array('patient_id' => $invoice['patient_id']))->row();
                        $name = $patient->name;
                        $prenom = $patient->prenom;

                        echo "<tr>
                                <td>" . $i++ . "</td>
                                <td>" . $invoice['receptionist_id'] . "</td>
                                <td>" . $name . " " . $prenom . "</td>
                                <td>" . $invoice['creation_datetime'] . "</td>
                                <td>";

                        // Décoder et afficher les descriptions des entrées de la facture
                        $invoice_entries = json_decode($invoice['invoice_entries'], true); // Utiliser 'true' pour obtenir un tableau associatif
                        foreach ($invoice_entries as $entry) {
                            echo $entry['description'] . " ";
                        }

                        echo "  </td>
                                <td>";

                        // Décoder et afficher les montants des entrées de la facture
                        foreach ($invoice_entries as $entry) {
                            echo $entry['amount'] . " ";
                        }

                        echo "  </td>
                                <td>" . $invoice['status'] . "</td>
                              </tr>";
                    }


                    // Affichage des examens
                    foreach ($exams as $exam) {
                        // Incrémenter le compteur pour chaque examen

                        // Récupérer le nom et prénom du patient en utilisant l'ID du patient depuis l'examen
                        $patient = $this->db->get_where('patient', array('patient_id' => $exam['patient_id']))->row();
                        $name = $patient->name;
                        $prenom = $patient->prenom;

                        echo "<tr>
                                <td>" .  $i++ . "</td>
                                <td>" . $exam['receptionist_id'] . "</td>
                                <td>" . $name . " " . $prenom . "</td>
                                <td>" . $exam['creation_time'] . "</td>
                                <td>";

                        // Décoder et afficher les descriptions des entrées d'examen
                        $exam_entries = json_decode($exam['examen_entries'], true); // Utiliser 'true' pour obtenir un tableau associatif
                        foreach ($exam_entries as $entry) {
                            echo $entry['description'] . " ";
                        }

                        echo "  </td>
                                <td>";

                        // Décoder et afficher les montants des entrées d'examen
                        foreach ($exam_entries as $entry) {
                            echo $entry['montant'] . " ";
                        }

                        echo "  </td>
                                <td>" . $exam['status'] . "</td>
                              </tr>";
                    }

                ?>
            </tbody>
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