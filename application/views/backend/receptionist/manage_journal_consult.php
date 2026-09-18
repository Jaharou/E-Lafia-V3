
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
                <h3>Journal de paiement consultation et examen</h3>
                <div class="d-flex justify-content-between ">
                <a class="btn btn-primary btn-wide icon-only" href="<?php echo base_url(); ?>JournalPaiement/journal_print/<?php echo $row['payment_id']; ?>" title="Journal de paiements">
                    Imprimer</a>
                </div>
                <br>
            <?php
                    // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Obtenir la date et l'heure actuelles
            $currentDateTime = new DateTime();

            // Définir l'heure de début de la période de 24 heures
            $startDateTime = clone $currentDateTime;
            $startDateTime->setTime(9, 0, 0);

            // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
            if ($currentDateTime < $startDateTime) {
                $startDateTime->modify('-1 day');
            }

            // Définir l'heure de fin de la période de 24 heures
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+1 day');

            // Formater les dates pour les requêtes SQL
            $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

            // Exécuter une requête pour récupérer les consultations dans la période de 24 heures
            $consultations = $this->db->query("SELECT * FROM invoice 
                WHERE creation_datetime BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();

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
                    <span class="name"><?php echo get_phrase('consultations-24h') ?></span>
                    <span class="number counter"><?php echo ($totalConsultations); ?></span>
                    <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                </a>
            </div>

            <?php
                        // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Obtenir la date et l'heure actuelles
            $currentDateTime = new DateTime();
            // Définir l'heure de début de la période de 24 heures
            $startDateTime = clone $currentDateTime;
            $startDateTime->setTime(9, 0, 0);

            // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
            if ($currentDateTime < $startDateTime) {
                $startDateTime->modify('-1 day');
            }
            // Définir l'heure de fin de la période de 24 heures
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+1 day');
            // Formater les dates pour les requêtes SQL
            $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

            // Exécuter une requête pour récupérer les consultations dans la période de 24 heures
            $consultations = $this->db->query("SELECT * FROM invoice 
                WHERE creation_datetime BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();
            // Calculer le montant total des consultations
            $totalAmount = 0;
            foreach ($consultations as $row) {
                $entries = json_decode($row['invoice_entries'], true);
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        if (isset($entry['net_amount'])) {
                            $totalAmount += floatval($entry['net_amount']);
                        }
                    }
                }
            }
            // Afficher le montant total des consultations
            ?>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <a class="dashboard-stat bg-primary" href="#">
                    <span class="name"><?php echo get_phrase('montant-total-consultat-24h') ?></span>
                    <span class="number counter"><?php echo ($totalAmount).'F'; ?></span>
                    <span class="bg-icon"><i class="fa fa-money"></i></span>
                </a>
            </div>

            <?php
                    // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Obtenir la date et l'heure actuelles
            $currentDateTime = new DateTime();

            // Définir l'heure de début de la période de 24 heures
            $startDateTime = clone $currentDateTime;
            $startDateTime->setTime(9, 0, 0);

            // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
            if ($currentDateTime < $startDateTime) {
                $startDateTime->modify('-1 day');
            }

            // Définir l'heure de fin de la période de 24 heures
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+1 day');

            // Formater les dates pour les requêtes SQL
            $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

            // Exécuter une requête pour récupérer les consultations dans la période de 24 heures
            $exams = $this->db->query("SELECT * FROM examen 
                WHERE creation_time BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();

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
                    <span class="name"><?php echo get_phrase('examens-24h') ?></span>
                    <span class="number counter"><?php echo ($totalExamens); ?></span>
                    <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                </a>
            </div>

            <?php
                    // Définir le fuseau horaire pour les heures correctes
            date_default_timezone_set('Africa/Niamey');

            // Obtenir la date et l'heure actuelles
            $currentDateTime = new DateTime();
            // Définir l'heure de début de la période de 24 heures
            $startDateTime = clone $currentDateTime;
            $startDateTime->setTime(9, 0, 0);

            // Si l'heure actuelle est avant 9h, on prend la période de 24h précédente
            if ($currentDateTime < $startDateTime) {
                $startDateTime->modify('-1 day');
            }

            // Définir l'heure de fin de la période de 24 heures
            $endDateTime = clone $startDateTime;
            $endDateTime->modify('+1 day');

            // Formater les dates pour les requêtes SQL
            $startDateTimeFormatted = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeFormatted = $endDateTime->format('Y-m-d H:i:s');

            // Exécuter une requête pour récupérer les exams dans la période de 24 heures
            $exams = $this->db->query("SELECT * FROM examen 
                WHERE creation_time BETWEEN '$startDateTimeFormatted' AND '$endDateTimeFormatted'")->result_array();

            // Calculer le montant total des exams
            $totalMontant = 0;
            foreach ($exams as $row) {
                $entries = json_decode($row['examen_entries'], true);
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        if (isset($entry['amountT'])) {
                            $totalMontant += floatval($entry['amountT']);
                        }
                    }
                }
            }
            // Afficher le montant total des examens
            ?>

            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <a class="dashboard-stat bg-primary" href="#">
                    <span class="name"><?php echo get_phrase('montant-total-examens-24h') ?></span>
                    <span class="number counter"><?php echo ($totalMontant).'F'; ?></span>
                    <span class="bg-icon"><i class="fa fa-money"></i></span>
                </a>
            </div>
            <br>
            
            <div class="col-md-12">
            <div class="panel">
                <div class="panel-body p-20">
                    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?php echo get_phrase('N°'); ?></th>
                            <th><?php echo get_phrase('créer-par'); ?></th>
                            <th><?php echo get_phrase('date'); ?></th>
                            <th><?php echo get_phrase('patient'); ?></th>
                            <th><?php echo get_phrase('prestation'); ?></th>
                            <th><?php echo get_phrase('montant-total'); ?></th>
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
                        $this->db->where("creation_datetime BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
                        $invoices = $this->db->get()->result_array();

                        // Récupération des informations des examens pour la période de 24 heures
                        $this->db->select('examen.*, patient.name, patient.prenom');
                        $this->db->from('examen');
                        $this->db->join('patient', 'patient.patient_id = examen.patient_id');
                        $this->db->where("creation_time BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
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
                                <td>" . $invoice['creation_datetime'] . "</td>
                                <td>" . $name . " " . $prenom . "</td>
                                <td>";

                        // Décoder et afficher les descriptions des entrées de la facture
                        $invoice_entries = json_decode($invoice['invoice_entries'], true); // Utiliser 'true' pour obtenir un tableau associatif
                        foreach ($invoice_entries as $entry) {
                            echo $entry['description'] . " ";
                        }

                        echo "  </td>
                                <td>";
                                $total_amount = 0;
                        // Décoder et afficher les montants des entrées de la facture
                        foreach ($invoice_entries as $entry) {
                            //echo $entry['net_amount'] . " ";
                            $total_amount += $entry['net_amount'];
                        }
                            echo $total_amount;
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
                                <td>" . $exam['creation_time'] . "</td>
                                <td>" . $name . " " . $prenom . "</td>
                                <td>";

                        // Décoder et afficher les descriptions des entrées d'examen
                        $exam_entries = json_decode($exam['examen_entries'], true); // Utiliser 'true' pour obtenir un tableau associatif
                        foreach ($exam_entries as $entry) {
                            echo $entry['description'] . " ";
                        }

                        echo "  </td>
                                <td>";
                                $total_amount = 0;
                        // Décoder et afficher les montants des entrées d'examen
                        foreach ($exam_entries as $entry) {
                            //echo $entry['amountT'] . " ";
                            $total_amount += $entry['amountT'];
                        }
                            echo $total_amount;
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