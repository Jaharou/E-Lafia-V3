<?php
/* 
 * Template: Manage Invoice
 * @author : Raju Ahmed
 * Date    : 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}

// Fixe le fuseau horaire pour que le calcul de la fenêtre 9h-9h ci-dessous
// soit correct quelle que soit la configuration par défaut du serveur.
date_default_timezone_set('Africa/Niamey');
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
</head>
<br>
<div class="col-md-12">
    <div class="panel">
        <a class="btn bg-black btn-wide icon-only pull-right" href="<?php echo base_url(); ?>receptionist/invoice_crud/add"> 
            <i class="fa fa-plus" aria-hidden="true"></i>
            <?php echo get_phrase('ajouter'); ?>
        </a>
        <header style="color: dark; font-size: 23px;">Liste des consultations</header>
        <div style="clear:both;"></div>
        <div class="panel-body p-20">
            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo get_phrase('N°'); ?></th>
                        <th><?php echo get_phrase('créer-par'); ?></th>
                        <th><?php echo get_phrase('numéro-de-reçu'); ?></th>
                        <th><?php echo get_phrase('date-de-creation'); ?></th>
                        <th><?php echo get_phrase('patient'); ?></th>
                        <th><?php echo get_phrase('désignation'); ?></th>
                        <th><?php echo get_phrase('montant-total'); ?></th>
                        <th><?php echo get_phrase('statut'); ?></th>
                        <th><?php echo get_phrase('option'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Liste des consultations de la fenêtre en cours (du plus
                    // récent au plus ancien). Fenêtre 9h-9h : la liste se
                    // réinitialise chaque jour à 9h du matin (comportement
                    // historique demandé). La jointure reste en LEFT JOIN
                    // pour qu'une consultation dont le patient aurait été
                    // supprimé ne disparaisse pas silencieusement.
                    $today_9am = new DateTime(date('Y-m-d') . ' 09:00:00');
                    if (new DateTime() < $today_9am) {
                        $window_start = (clone $today_9am)->modify('-1 day');
                    } else {
                        $window_start = $today_9am;
                    }
                    $window_end = (clone $window_start)->modify('+1 day');

                    // receptionist_id est stocké sous DEUX formats selon
                    // l'ancienneté de la donnée : ancien = prénom en texte
                    // libre (ex: "Nafissa"), nouveau = identifiant numérique
                    // vers la table receptionist (ex: "6", ou "0"/vide si non
                    // renseigné côté formulaire). On charge une seule fois la
                    // table receptionist pour résoudre les id numériques.
                    $receptionist_names = [];
                    foreach ($this->db->select('receptionist_id, name')->get('receptionist')->result_array() as $r) {
                        $receptionist_names[$r['receptionist_id']] = $r['name'];
                    }

                    $this->db->select('invoice.*, patient.name AS patient_name, patient.prenom');
                    $this->db->from('invoice');
                    $this->db->join('patient', 'patient.patient_id = invoice.patient_id', 'left');
                    $this->db->where('invoice.creation_datetime >=', $window_start->format('Y-m-d H:i:s'));
                    $this->db->where('invoice.creation_datetime <', $window_end->format('Y-m-d H:i:s'));
                    $this->db->order_by('invoice.invoice_id', 'DESC');
                    $invoices = $this->db->get()->result_array();
                    $i = 0;
                    foreach ($invoices as $invoice) {
                        $i++;

                        $name = !empty($invoice['patient_name']) ? $invoice['patient_name'] : 'Patient';
                        $prenom = !empty($invoice['prenom']) ? $invoice['prenom'] : 'non renseigné';

                        $rid = $invoice['receptionist_id'];
                        if (empty($rid) || $rid === 'Sélectionner votre nom') {
                            $receptionist_name = 'non renseigné';
                        } elseif (ctype_digit((string) $rid)) {
                            $receptionist_name = $receptionist_names[(int) $rid] ?? 'non renseigné';
                        } else {
                            $receptionist_name = $rid;
                        }

                        echo "<tr>
                                <td>" . $i . "</td>
                                <td>" . htmlspecialchars($receptionist_name) . "</td>
                                <td>" . htmlspecialchars($invoice['invoice_number']) . "</td>
                                <td>" . htmlspecialchars($invoice['creation_datetime']) . "</td>
                                <td>" . htmlspecialchars($name . " " . $prenom) . "</td>
                                
                                <td>";

                        // Décoder et afficher les libellés des prestations
                        $invoice_entries = json_decode($invoice['invoice_entries']);
                        $total_amount = 0;
                        if (is_array($invoice_entries)) {
                            foreach ($invoice_entries as $entry) {
                                echo htmlspecialchars(isset($entry->description) ? $entry->description : '') . " ";
                            }
                        }

                        echo "  </td>
                                <td>";
                        if (is_array($invoice_entries)) {
                            foreach ($invoice_entries as $entry) {
                                $total_amount += isset($entry->net_amount) ? (float) $entry->net_amount : 0;
                            }
                        }
                        echo $total_amount;
                        echo "  </td>
                                <td>" . htmlspecialchars($invoice['status']) . "</td>
                                <td>
                                    <a href=\"" . base_url() . "receptionist/invoice_crud/edit/" . $invoice['invoice_id'] . "\" class=\"btn btn-success btn-rounded icon-only\" title=\"Modifier\">
                                        <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                    </a>
                                    <a class=\"btn btn-warning btn-rounded icon-only\" href=\"" . base_url() . "Invoice/invoice_print/" . $invoice['invoice_id'] . "\" title=\"Impression\">
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
    // Le tri (du plus récent au plus ancien) est fait directement en SQL
    // (ORDER BY invoice_id DESC) ; plus besoin d'inverser les lignes côté
    // client. Le filtrage 24h (fenêtre 9h-9h) est fait en PHP/SQL ci-dessus,
    // pas en JS : la liste est donc déjà correcte au chargement de la page.
</script>
