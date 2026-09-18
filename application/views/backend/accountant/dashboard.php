<?php
/* 	
 * 	Tamplate: Notice
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); $today=date('Y-m-d'); ?>
	<div class="row">
                <p style="margin-left: 10px;">Recette receptioniste</p>     
                    <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('patient-par-jour') ?></span>
                            <span class="number counter"><?php echo $this->db->query("SELECT COUNT(*) AS total FROM patient WHERE DATE(created_at)='$today'")->result_array()[0]['total']; ?></span>
                            <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
                        </a>
                    </div>
                     <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-danger" href="<?php echo base_url(); ?>accountant/patient">
                            <span class="name"><?php echo get_phrase('patient-total') ?></span>
                            <span class="number counter"><?php echo $this->db->query("SELECT COUNT(*) AS total FROM patient")->result_array()[0]['total']; ?></span>
                            <span class="bg-icon"><i class="fa fa-wheelchair"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('d-M-Y');
                    $invoice_entries = $this->db->query("SELECT invoice_entries FROM invoice WHERE DATE(STR_TO_DATE(creation_datetime, '%d-%b-%Y')) = CURDATE()")->result_array();
                        $totalInvoices = 0;
                        foreach ($invoice_entries as $row) {
                        $data = json_decode($row['invoice_entries'], true);
                        if (is_array($data)) {
                            $totalInvoices += count($data);
                        }
                    }
                    ?>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('consultation-par-jour') ?></span>
                            <span class="number counter"><?php echo $totalInvoices; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('d-M-Y');
                    $invoice_entries = $this->db->query("SELECT invoice_entries FROM invoice WHERE DATE(STR_TO_DATE(creation_datetime, '%d-%b-%Y')) = CURDATE()")->result_array();
                    $total = 0;
                    foreach ($invoice_entries as $row) {
                        $data = json_decode($row['invoice_entries'], true); 
                        if (is_array($data)) {
                            foreach ($data as $invoice) {
                                if (isset($vente['amount'])) {
                                    $total += $invoice['amount'];
                                }
                            }
                        }
                    }
                    ?>
                    <div style="margin-top:10px" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                     <a class="dashboard-stat bg-success" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total-pour-aujourd\'hui') ?></span>
                            <span class="number counter"><?php echo $total; ?></span>
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer toutes les entrées d'examen
                    $invoice_entries = $this->db->query("SELECT invoice_entries FROM invoice")->result_array();
                    $totalInvoices = 0;
                    foreach ($invoice_entries as $row) {
                        $data = json_decode($row['invoice_entries'], true);
                        if (is_array($data)) {
                            $totalInvoices += count($data);
                        }
                    }
                    ?>
                    <div style="margin-top:10px" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('consultation-total') ?></span>
                            <span class="number counter"><?php echo $totalInvoices; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>
                    <?php
                    //         Récupérer les données JSON
                    $invoice_entries = $this->db->query('SELECT invoice_entries FROM invoice')->result_array();
                    //Décodez et traitez les données JSON pour calculer le total
                    $total = 0;
                    foreach ($invoice_entries as $row) {
                        $data = json_decode($row['invoice_entries'], true);
                        // Vérifier si $data est un tableau et contient plusieurs ventes
                        if (is_array($data)) {
                            foreach ($data as $invoice) {
                                // Supposons que chaque invoice dans le tableau JSON contienne une clé 'Ptotal'
                                if (isset($invoice['amount'])) {
                                    $total += $invoice['amount'];
                                }
                            }
                        }
                    }

                    //               Affichage du total
                    ?>
                    <div style="margin-top:10px" class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <a class="dashboard-stat bg-success" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total') ?></span><br>
                            <span class="number counter"><?php echo $total; ?></span>
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
				<br>
				<p>Recette pharmacien</p>
                <?php
                // Récupérer la date d'aujourd'hui
                $today = date('d-M-Y');
                // Récupérer les données JSON pour la date d'aujourd'hui
                $invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente WHERE DATE(STR_TO_DATE(date_vente, '%d-%b-%Y')) = CURDATE()")->result_array();
                // Décodez et traitez les données JSON pour compter le nombre de ventes du jour
                $totalVentes = 0;
                foreach ($invoice_ventes as $row) {
                    $data = json_decode($row['invoice_ventes'], true);
                    // Vérifier si $data est un tableau et contient plusieurs ventes
                    if (is_array($data)) {
                        $totalVentes += count($data);
                    }
                }
                ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <a class="dashboard-stat bg-danger" href="#">
                        <span class="name"><?php echo get_phrase('vente-par-jour') ?></span>
                        <span class="number counter"><?php echo $totalVentes; ?></span>
                        <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                    </a>
                </div>

                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('d-M-Y');
                    // Récupérer les données JSON pour la date d'aujourd'hui
                    $invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente WHERE DATE(STR_TO_DATE(date_vente, '%d-%b-%Y')) = CURDATE()")->result_array();
                    // Décodez et traitez les données JSON pour calculer le total pour la date d'aujourd'hui
                    $total = 0;
                    foreach ($invoice_ventes as $row) {
                        $data = json_decode($row['invoice_ventes'], true);    
                        // Vérifier si $data est un tableau et contient plusieurs ventes
                        if (is_array($data)) {
                            foreach ($data as $vente) {
                                // Supposons que chaque vente dans le tableau JSON contienne une clé 'Ptotal'
                                if (isset($vente['Ptotal'])) {
                                    $total += $vente['Ptotal'];
                                }
                            }
                        }
                    }
                    ?>
                    <div style="margin-top:0px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total-pour-aujourd\'hui') ?></span>
                            <span class="number counter"><?php echo $total; ?></span> 
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer toutes les entrées de vente
                    $invoice_ventes = $this->db->query("SELECT invoice_ventes FROM vente")->result_array();
                    $totalVentes = 0;
                    foreach ($invoice_ventes as $row) {
                        $data = json_decode($row['invoice_ventes'], true);
                        if (is_array($data)) {
                            $totalVentes += count($data);
                        }
                    }
                    ?>
                    <div style="margin-top:10px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('vente-total') ?></span>
                            <span class="number counter"><?php echo $totalVentes; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>               
                    <?php
                    //          Récupérer les données JSON
                    $invoice_ventes = $this->db->query('SELECT invoice_ventes FROM vente')->result_array();

                    //Décodez et traitez les données JSON pour calculer le total
                    $total = 0;
                    foreach ($invoice_ventes as $row) {
                        $data = json_decode($row['invoice_ventes'], true);
                        // Vérifier si $data est un tableau et contient plusieurs ventes
                        if (is_array($data)) {
                            foreach ($data as $vente) {
                                // Supposons que chaque vente dans le tableau JSON contienne une clé 'Ptotal'
                                if (isset($vente['Ptotal'])) {
                                    $total += $vente['Ptotal'];
                                }
                            }
                        }
                    }

                    //               Affichage du total
                    ?>
                    <div style="margin-top:10px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-success" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total') ?></span><br>
                            <span class="number counter"><?php echo $total; ?></span>
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
                    <br>
                    <p style="margin-left:10px;">Recette examen</p>
                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('d-M-Y');
                    $examen_entries = $this->db->query("SELECT examen_entries FROM examen WHERE DATE(STR_TO_DATE(creation_time, '%d-%b-%Y')) = CURDATE()")->result_array();
                        $totalExamens = 0;
                        foreach ($examen_entries as $row) {
                        $data = json_decode($row['examen_entries'], true);
                        if (is_array($data)) {
                            $totalExamens += count($data);
                        }
                    }
                    ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-danger" href="#">
                            <span class="name"><?php echo get_phrase('examen-par-jour') ?></span>
                            <span class="number counter"><?php echo $totalExamens; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>

                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('d-M-Y');
                    // Récupérer les données JSON pour la date d'aujourd'hui
                    $examen_entries = $this->db->query("SELECT examen_entries FROM examen WHERE DATE(STR_TO_DATE(creation_time, '%d-%b-%Y')) = CURDATE()")->result_array();
                    // Décodez et traitez les données JSON pour calculer le total pour la date d'aujourd'hui
                    $total = 0;
                    foreach ($examen_entries as $row) {
                        $data = json_decode($row['examen_entries'], true);    
                        // Vérifier si $data est un tableau et contient plusieurs ventes
                        if (is_array($data)) {
                            foreach ($data as $examen) {
                                // Supposons que chaque examen dans le tableau JSON contienne une clé 'Ptotal'
                                if (isset($examen['Ptotal'])) {
                                    $total += $examen['Ptotal'];
                                }
                            }
                        }
                    }
                    ?>
                    <div style="margin-top:0px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-primary" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total-pour-aujourd\'hui') ?></span>
                            <span class="number counter"><?php echo $total; ?></span> 
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer toutes les entrées d'examen
                    $examen_entries = $this->db->query("SELECT examen_entries FROM examen")->result_array();
                    $totalExamens = 0;
                    foreach ($examen_entries as $row) {
                        $data = json_decode($row['examen_entries'], true);
                        if (is_array($data)) {
                            $totalExamens += count($data);
                        }
                    }
                    ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-top:10px">
                        <a class="dashboard-stat bg-danger" href="#">
                            <span class="name"><?php echo get_phrase('examen-total') ?></span>
                            <span class="number counter"><?php echo $totalExamens; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>               
                    <?php
                    //          Récupérer les données JSON
                    $examen_entries = $this->db->query('SELECT examen_entries FROM examen')->result_array();

                    //Décodez et traitez les données JSON pour calculer le total
                    $total = 0;
                    foreach ($examen_entries as $row) {
                        $data = json_decode($row['examen_entries'], true);
                        // Vérifier si $data est un tableau et contient plusieurs ventes
                        if (is_array($data)) {
                            foreach ($data as $examen) {
                                // Supposons que chaque examen dans le tableau JSON contienne une clé 'Ptotal'
                                if (isset($examen['amount'])) {
                                    $total += $examen['amount'];
                                }
                            }
                        }
                    }

                    //               Affichage du total
                    ?>
                    <div style="margin-top:10px" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a class="dashboard-stat bg-success" href="#">
                            <span class="name"><?php echo get_phrase('Montant-total') ?></span><br>
                            <span class="number counter"><?php echo $total; ?></span>
                            <span class="bg-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
                        </a>
                    </div>
                    <br><br><br>
                </div>

<?php 
$output .= ob_get_clean();
echo $output;