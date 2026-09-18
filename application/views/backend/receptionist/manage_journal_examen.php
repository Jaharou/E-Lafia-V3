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
            $examen_info   = $this->db->get('examen')->result_array();
                foreach ($examen_info as $row): ; ?>
                
                <?php endforeach; ?>
                <div class="d-flex justify-content-between align-items-center">
                <h3>Journal de paiements</h3>
                <a class="btn bg-primary btn-wide icon-only" href="<?php echo base_url(); ?>JournalPaiementExamen/journalExamen_print/<?php echo $row['id_examen']; ?>" title="Journal de paiements">
                    Imprimer 
                </a>
                <br><br>
                </div>
                <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('Y-m-d');
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
                        <a class="dashboard-stat bg-success" href="#">
                            <span class="name"><?php echo get_phrase('examen-par-jour') ?></span>
                            <span class="number counter"><?php echo $totalExamens; ?></span>
                            <span class="bg-icon"><i class="fa fa-user-md"></i></span>
                        </a>
                    </div>
                    <?php
                    // Récupérer la date d'aujourd'hui
                    $today = date('Y-m-d');
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
                                if (isset($examen['amount'])) {
                                    $total += $examen['amount'];
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
                
                <div class="col-md-12">
                <div class="panel">   
                    <div class="panel-body p-20">
                            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>                                  
                                <th><?php echo get_phrase('numéro-de-reçu'); ?></th>
                                <th><?php echo get_phrase('patient'); ?></th>
                                <th><?php echo get_phrase('test'); ?></th>
                                <th><?php echo get_phrase('date'); ?></th>
                                <th><?php echo get_phrase('statut'); ?></th>
                                <th><?php echo get_phrase('montant'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $this->db->order_by('id_examen', 'DESC');
                            //$this->db->limit(5);
                            $examen_info   = $this->db->get('examen')->result_array();  
                            foreach ($examen_info as $row):
                            ?>   
                            <tr>
                                <td><?php echo $row['examen_number'] ?></td>
                                <td>
                                    <?php $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
                                    $prenom = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->prenom;
                                        echo $name.' '.$prenom;?>
                                </td>
                                <td> 
                                    <?php
                                    $examen_entries = json_decode($row['examen_entries']);
                                    foreach ($examen_entries as $examen_entry) {
                                        echo $examen_entry->description;
                                        echo " "; } ?>
                                </td>
                                <td><?php echo $row['creation_time'] ?></td>
                                <td><?php echo $row['status'] ?></td>
                                <td> 
                                    <?php
                                    $examen_entries = json_decode($row['examen_entries']);
                                    foreach ($examen_entries as $examen_entry) {
                                        echo $examen_entry->amount;
                                        echo " "; } ?>
                                </td>
                                </tr>                           
                                <?php endforeach; ?>
                            </tbody>
                        </table>                
                    </div>              
                </div>              
            </div>
    <?php 
$output .= ob_get_clean();
echo $output;