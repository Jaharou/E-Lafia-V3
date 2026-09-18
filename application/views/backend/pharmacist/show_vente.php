<?php
$edit_data = $this->db->get_where('vente', array('vente_id' => $param2))->result_array();

foreach ($edit_data as $row) {
    $medicine_info = $this->db->get_where('medicine', array('medicine_id' => $row['medicine_id']))->result_array();
?>
<style>
        @media print{
            #impressionBouton {
                display: none;
            }
                  }
</style>
    <div id="ordonnancePatient" class="impressionSection">
        
                <img class="pull-right" src="<?php echo base_url(); ?>uploads/PHAR.jpg" alt="PHAR" class="PHAR" style="height: 90px;width: 120px; white-space: normal; position: absolute; margin-left:445px;"><br>
    
        <table width="100%" border="0">
     
            <div>
                    <?php 
                      echo $system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description
                    ; ?> <br>
                    <?php 
                    echo $contact = $this->db->get_where('settings', array('type' => 'phone'))->row()->description;
                     ?> <br>
                     <?php echo $address = $this->db->get_where('settings', array('type' => 'address'))->row()->description; ?><br>
                   <?php echo $system_nif = $this->db->get_where('settings', array('type' => 'system_nif'))->row()->description; ?>
            </div><hr>
            <div class="pull-right">
                 
            </div>
                
            <tr> 

                <td align="right" valign="top">
                    
                    <?php echo 'Date: ' . $row['date_vente']; ?><br>
                </td>
            </tr>
        </table>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-body">
                        <td align="left" valign="top">
                        <?php foreach ($medicine_info as $row2) { ?>
                            <?php echo 'produit: ' . $row2['name']; ?><br>
                        <?php } ?>
                        </td>
                        <p><?php echo get_phrase('quantité'); ?> :
                        <?php echo $row['qte_produit']; ?></p>
                        <p><?php echo get_phrase('prix_unitaire'); ?> :
                        <?php echo $row['pu']; ?></p>
                        <p><?php echo get_phrase('prix_total'); ?> :
                        <?php echo $row['prix_total']; ?></p>
                        <p><?php echo get_phrase('dosage'); ?> :
                        <?php echo $row['dosage']; ?></p>
                        <p><?php echo get_phrase('%Remise'); ?> :
                        <?php echo $row['discount_amount']; ?></p>
                        <p><?php echo get_phrase('prise_en_charge'); ?> :
                        <?php echo $row['prise_en_charge']; ?></p>
                        <p><?php echo get_phrase('%Prise_en_charge'); ?> :
                        <?php echo $row['pourcentage_prise']; ?></p>
                        <!--<p></?php echo get_phrase('Montant_net_à_payer'); ?> :
                        </?php echo $row['pourcentage_prise']; ?></p>-->
                        <p><?php echo get_phrase('statut'); ?> :
                        <?php echo $row['status']; ?></p>
                        <hr>
                      <footer style="position: absolute; margin-left:438px;">
                    <b class="pull-right"><?php echo get_phrase('Le Pharmacien'); ?> :</b><br>
                        <!--<p class="pull-right"></?php $name = $this->db->get_where('pharmacist', array('pharmacist_id' => $row['pharmacist_id']))->row()->name;
                         echo $name; ?></p>-->
                    </footer>
                    </div><br><br>
                    
                </div>
            </div>
        </div>

       
    </div>
     <button onclick="printRecu()" id="impressionBouton">Imprimer</button>
<?php } ?>


