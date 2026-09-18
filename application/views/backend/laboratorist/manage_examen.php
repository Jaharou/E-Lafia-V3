<?php
/* 
 * Template: Manage Invoice
 * @author : Raju Ahmed
 * Date    : 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<br>
<div class="col-md-12">
    <div class="panel">
        <a href="<?php echo base_url(); ?>laboratorist/examen_crud/add"
             class="btn bg-black btn-wide icon-only pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?php echo get_phrase('ajouter'); ?>
        </a>
        <header style="color: dark; font-size: 23px;">Liste des examens</header>
        <div style="clear:both;"></div>
        <div class="panel-body p-20">
            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo get_phrase('N°'); ?></th>
                        <th><?php echo get_phrase('numéro-de-reçu'); ?></th>
                        <th><?php echo get_phrase('date-de-creation'); ?></th>
                        <th><?php echo get_phrase('patient'); ?></th>
                        <th><?php echo get_phrase('test'); ?></th>
                        <th><?php echo get_phrase('résultat'); ?></th>
                        <th><?php echo get_phrase('unité'); ?></th>
                        <th><?php echo get_phrase('statut'); ?></th>
                        <th><?php echo get_phrase('option'); ?></th>
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
                    $this->db->select('examen.*, patient.name, patient.prenom');
                    $this->db->from('examen');
                    $this->db->join('patient', 'patient.patient_id = examen.patient_id');
                    $this->db->where("STR_TO_DATE(creation_time, '%d-%m-%Y %H:%i:%s') BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");
                    $exams = $this->db->get()->result_array();
                    $i = 0; // Initialize the counter

                    foreach ($exams as $examen) {
                        $i++; // Increment the counter for each exam

                        // Retrieve the patient's name and prenom using the patient_id from the exam
                        $patient = $this->db->get_where('patient', array('patient_id' => $examen['patient_id']))->row();
                        $name = $patient->name;
                        $prenom = $patient->prenom;

                        echo "<tr>
                                <td>" . $i . "</td>
                                <td>" . $examen['examen_number'] . "</td>
                                <td>" . $examen['creation_time'] . "</td>
                                <td>" . $name . " " . $prenom . "</td>
                                <td>";

                        // Decode and display the exam entries descriptions
                        $examen_entries = json_decode($examen['examen_entries']);
                        foreach ($examen_entries as $entry) {
                            echo $entry->description . " ";
                        }

                        echo "  </td>
                                <td>";
                                
                        // Decode and display the exam entries results
                        foreach ($examen_entries as $entry) {
                            echo $entry->resultat . " ";
                        }

                        echo "  </td>
                                <td>";
                                
                        // Decode and display the exam entries units
                        foreach ($examen_entries as $entry) {
                            echo $entry->unite . " ";
                        }

                        echo "  </td>
                                <td>" . $examen['status'] . "</td>
                                <td>
                                    <a href=\"" . base_url() . "laboratorist/examen_crud/edit/" . $examen['id_examen'] . "\" class=\"btn btn-success btn-rounded icon-only\" title=\"Modifier\">
                                        <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                    </a>
                                    <a class=\"btn btn-warning btn-rounded icon-only\" href=\"" . base_url() . "InvoiceExamenLabo/examenLabo_print/" . $examen['id_examen'] . "\" title=\"Impression\">
                                        <i class=\"fa fa-print\"></i>
                                    </a>
                                    
                                </td>
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
