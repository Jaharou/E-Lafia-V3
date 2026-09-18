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
</head>
<br>
<div class="col-md-12">
    <div class="panel">
        <a href="<?php echo base_url(); ?>receptionist/facture_crud/add"
             class="btn bg-black btn-wide icon-only pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?php echo get_phrase('ajouter'); ?>
        </a>
        <header style="color: dark; font-size: 23px;">Liste des factures</header>
        <div style="clear:both;"></div>
        <div class="panel-body p-20">
            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo get_phrase("N°-d'ordre"); ?></th>
                        <th><?php echo get_phrase('numéro'); ?></th>
                        <th><?php echo get_phrase('patient'); ?></th>
                        <th><?php echo get_phrase('date-de-facture'); ?></th>
                        <th><?php echo get_phrase('consultation'); ?></th>
                        <th><?php echo get_phrase('examen'); ?></th>
                        <th><?php echo get_phrase('traitement'); ?></th>
                        <th><?php echo get_phrase('vente'); ?></th>
                        <th><?php echo get_phrase('montant-total'); ?></th>
                        <th><?php echo get_phrase('statut'); ?></th>
                        <th><?php echo get_phrase("date-d'échéance"); ?></th>
                        <th><?php echo get_phrase('option'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (is_array($facture_info) && !empty($facture_info)) {
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
                        $this->db->select('facture.*, patient.name, patient.prenom');
                        $this->db->from('facture');
                        $this->db->join('patient', 'patient.patient_id = facture.patient_id', 'left'); // Utilise une jointure explicite
                        $this->db->where("STR_TO_DATE(date_facture, '%d-%m-%Y %H:%i:%s') BETWEEN '$start_of_day_mysql' AND '$end_of_day_mysql'");

                        // Exécuter la requête
                        $facture_info = $this->db->get()->result_array();
                        $i = 0;
                        foreach ($facture_info as $row): $i++; ?>   
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $row['facture_number'] ?></td>
                        <td>
                        <?php
                        $name = $this->db->get_where('patient', array('patient_id' => $row['patient_id']))->row()->name;
                        $prenom = $this->db->get_where('patient', array('patient_id' => $row['patient_id']))->row()->prenom;
                        echo $name.' '.$prenom;
                        ?>
                        </td>
                        <td><?php echo $row['date_facture'] ?></td>

                        <!-- Affichage des descriptions dans facture_consult_entries -->
                        <td>
                            <?php
                            // Décoder l'ensemble du tableau JSON
                            $invoice_entries_data = json_decode($row['invoice_entries'], true);  // Utiliser le deuxième paramètre 'true' pour obtenir un tableau associatif

                            if (is_array($invoice_entries_data)) {
                                foreach ($invoice_entries_data as $invoice_entry_json) {
                                    // Décoder chaque élément qui est une chaîne JSON
                                    $invoice_entries = json_decode($invoice_entry_json, true);

                                    if (is_array($invoice_entries)) {
                                        foreach ($invoice_entries as $entry) {
                                            if (isset($entry['description']) && isset($entry['net_amount'])) {
                                                echo $entry['description'] . " - " . $entry['net_amount'] . " FCFA<br>";
                                            } else {
                                                echo "Données incorrectes";
                                            }
                                        }
                                    } else {
                                        echo "Impossible de décoder les données JSON";
                                    }
                                }
                            } else {
                                echo "Données invalides ou absentes";
                            }
                            ?>
                        </td>
                        <!-- Affichage des descriptions dans examen_entries -->
                        <td>
                            <?php
                            // Décoder l'ensemble du tableau JSON
                            $examen_entries_data = json_decode($row['examen_entries'], true);  // Utiliser le deuxième paramètre 'true' pour obtenir un tableau associatif

                            if (is_array($examen_entries_data)) {
                                foreach ($examen_entries_data as $examen_entry_json) {
                                    // Décoder chaque élément qui est une chaîne JSON
                                    $examen_entries = json_decode($examen_entry_json, true);

                                    if (is_array($examen_entries)) {
                                        foreach ($examen_entries as $entry) {
                                            if (isset($entry['description']) && isset($entry['amountT'])) {
                                                echo $entry['description'] . " - " . $entry['amountT'] . " FCFA<br>";
                                            } else {
                                                echo "Données incorrectes";
                                            }
                                        }
                                    } else {
                                        echo "Impossible de décoder les données JSON";
                                    }
                                }
                            } else {
                                echo "Données invalides ou absentes";
                            }
                            ?>
                        </td>

                    <!--Affichage des descriptions dans traitement_entries-->
                        <td>
                            <?php
                            // Décoder l'ensemble du tableau JSON
                            $traitement_entries_data = json_decode($row['traitement_entries'], true);  // Utiliser le deuxième paramètre 'true' pour obtenir un tableau associatif

                            if (is_array($traitement_entries_data)) {
                                foreach ($traitement_entries_data as $traitement_entry_json) {
                                    // Décoder chaque élément qui est une chaîne JSON
                                    $traitement_entries = json_decode($traitement_entry_json, true);

                                    if (is_array($traitement_entries)) {
                                        foreach ($traitement_entries as $entry) {
                                            if (isset($entry['description']) && isset($entry['prixTrait'])) {
                                                echo $entry['description'] . " - " . $entry['prixTrait'] . " FCFA<br>";
                                            } else {
                                                echo "Données incorrectes"; 
                                            }
                                        }
                                    } else {
                                        echo "Impossible de décoder les données JSON";
                                    }
                                }
                            } else {
                                echo "Données invalides ou absentes";
                            }
                            ?>
                        </td>
                        <!-- Affichage des descriptions dans ventes_entries -->
                        <td>
                            <?php
                            // Décoder l'ensemble du tableau JSON
                            $invoice_ventes_data = json_decode($row['invoice_ventes'], true);  // Utiliser le deuxième paramètre 'true' pour obtenir un tableau associatif

                            if (is_array($invoice_ventes_data)) {
                                foreach ($invoice_ventes_data as $invoice_entry_json) {
                                    // Décoder chaque élément qui est une chaîne JSON
                                    $invoice_ventes = json_decode($invoice_entry_json, true);

                                    if (is_array($invoice_ventes)) {
                                        foreach ($invoice_ventes as $entry) {
                                            if (isset($entry['produit']) && isset($entry['Ptotal'])) {
                                                echo $entry['produit'] . " - " . $entry['Ptotal'] . " FCFA<br>";
                                            } else {
                                                echo "Données incorrectes";
                                            }
                                        }
                                    } else {
                                        echo "Impossible de décoder les données JSON";
                                    }
                                }
                            } else {
                                echo "Données invalides ou absentes";
                            }
                            ?>
                        </td>

                        <td><?php echo $row['total_amount'] ?></td>
                        <td><?php echo $row['status'] ?></td>
                        <td><?php echo $row['updated_at'] ?></td>
                        <td>
                            <!--<a href="</?php echo base_url(); ?>receptionist/facture_crud/edit/</?php echo $row['facture_id']; ?>" class="btn btn-success btn-rounded icon-only">
                                <i class="fa fa-pencil"></i>
                            </a>-->
                            <a class="btn btn-warning btn-rounded icon-only" href="<?php echo base_url(); ?>Facture/facture_print/<?php echo $row['facture_id']; ?>" title="Impression">
                                <i class="fa fa-print"></i>
                            </a>
                            <br><br>
                        </td>
                    </tr>
                        <?php endforeach; 
                            } else {
                                echo "<tr><td colspan='11'>Aucune données disponible</td></tr>";
                            }
                        ?>
                </tbody>
            </table>
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
</div>
