<?php
$edit_data = $this->db->get_where('prescription', array('prescription_id' => $param2))->result_array();

foreach ($edit_data as $row) {
    $patient_info = $this->db->get_where('patient', array('patient_id' => $row['patient_id']))->result_array();
?>
<style>
        @media print{
            #impressionBouton {
                display: none;
            }
                  }
</style>
    <div id="ordonnancePatient" class="impressionSection">
        
                <img class="pull-right" src="<?php echo base_url(); ?>uploads/logo.jpeg" alt="logo" class="logo" style="height: 90px;width: 120px; white-space: normal; position: absolute; margin-left:445px;"><br>
    
        <table width="100%" border="0">
     
            <div>
                    <?php 
                      echo $system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description
                    ; ?> <br>
                    <?php 
                    echo $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
                     ?> <br>
                     <?php echo $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description; ?><br>
                   <?php echo $system_email = $this->db->get_where('settings', array('type' => 'system_email'))->row()->description; ?>
            </div><hr>
            <div class="pull-right">
                 
            </div>
                
            <tr> 


                <td align="left" valign="top">
                    <?php foreach ($patient_info as $row2) { ?>
                        <?php echo 'Patient: ' . $row2['name'] . ' ' . $row2['prenom']; ?><br>
                        <?php echo 'Age: ' . $row2['age']; ?><br>
                        <?php echo 'Sexe: ' . $row2['sex']; ?><br>
                    <?php } ?>
                </td>

                <td align="right" valign="top">
                    
                    <?php echo 'Date: ' . $row['prescription_timestamp']; ?><br>
                </td>
            </tr>
        </table>
        <h3 class="panel panel-primary" style="text-align: center; color: success;">Ordonnance médicale</h3>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-body">
                        <b><?php echo get_phrase('les mesures'); ?> :</b>
                        <p><?php echo $row['posologie']
                        ; ?></p>
                        <p><?php echo $row['nbr_unite']; ?></p>
                        <p><?php echo $row['qsp']; ?></p>
                        <hr>
                        <b><?php echo get_phrase('les médicaments'); ?> :</b>
                        <p>
                            <?php
                            if (!is_numeric($row['medicine_id'])) {
                                $mede = json_decode($row['medicine_id'], true);
                                foreach ($mede as $key => $value) {
                                    $medicine_query = $this->db->get_where('medicine', array('medicine_id' => $value));

                                    if ($medicine_query->num_rows() > 0) {
                                        $medicine = $medicine_query->row();
                                        $name = $medicine->name;
                                        echo "$name <br> ";
                                    } else {
                                        echo "médicament non trouvé";
                                    }
                                }
                            } else {
                                $medicine_query = $this->db->get_where('medicine', array('medicine_id' => $row['medicine_id']));

                                if ($medicine_query->num_rows() > 0) {
                                    $medicine = $medicine_query->row();
                                    $name = $medicine->name;
                                    echo $name;
                                } else {
                                    echo "médicament non trouvé";
                                }
                            }
                            ?>
                        </p>
                        <hr>
                        <b><?php echo get_phrase('note'); ?> :</b>
                        <p><?php echo $row['note']; ?></p>
                        <hr>
                      <footer style="position: absolute; margin-left:438px;">
                    <b class="pull-right"><?php echo get_phrase('Le Médecin'); ?> :</b><br>
                        <p class="pull-right"><?php $name = $this->db->get_where('doctor', array('doctor_id' => $row['doctor_id']))->row()->name;
                         echo $name; ?></p>
                    </footer>
                    </div><br><br>
                    
                </div>
            </div>
        </div>

       
    </div>
     <button onclick="printRecu()" id="impressionBouton">Imprimer</button>
<?php } ?>


