<?php
$vente_info = $this->db->get('vente')->result_array();
$single_vente_info   = $this->db->get_where('vente', array('vente_id' => $param2))->result_array();
foreach ($single_vente_info as $row) {
?>

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-primary" data-collapsed="0">

                <div class="panel-heading">
                    <div class="panel-title">
                        <h3><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>

                <div class="panel-body">

                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>pharmacist/vente/update/<?php echo $row['vente_id']; ?>" method="post" enctype="multipart/form-data">
                        

                        <div class="form-group">
                            <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('nom_produit'); ?></label>
                            <div class="col-sm-9">
                                <select name="medicine_id" class="js-states form-control select2" id="js-states">
                         <?php $medicines = $this->db->get('medicine')->result_array();
                                foreach ($medicines as $row2): ?>
                                    <option value="<?php echo $row2['medicine_id']; ?>"  
                                        <?php if ($row['medicine_id'] == $row2['medicine_id']) echo 'selected'; ?>>
                                            <?php echo $row2['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('quantité'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="qte_produit" class="form-control" id="field-1" value="<?php echo $row['qte_produit']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('dosage'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="dosage" class="form-control" id="dosage" value="<?php echo $row['dosage']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix_unitaire'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="pu" class="form-control" id="field-1" value="<?php echo $row['pu']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix_total'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="prix_total" class="form-control" id="field-1" value="<?php echo $row['prix_total']; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date_vente'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="date_vente" class="form-control" id="date_vente" value="<?php echo $row['date_vente']; ?>">
                        </div>
                    </div>

                    <!-------------------Debut partie vente------------------->
            <hr>
                <span style="color: green; font-size: 25px;">Vente</span>
                    <!-- INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry">
                        <?php
                        $invoice_ventes = json_decode($row['invoice_ventes']);
                            foreach ($invoice_ventes as $invoice_entry) { ?>
                            <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label">
                               <?php echo get_phrase('produit'); ?></label>
                                <div class="col-sm-5">
                                 <input type="text" class="form-control" name="medicine[]"  
                                 value="<?php echo $invoice_entry->description; ?>" 
                                 placeholder="<?php echo get_phrase('désignation'); ?>" >
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="pu[]"  
                                    value="<?php echo $invoice_entry->amount; ?>" 
                                    placeholder="<?php echo get_phrase('montant'); ?>" >
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <!-- INVOICE ENTRY ENDS HERE-->

                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry_temp">
                        <div class="form-group">
                            <label for="Consultation" class="col-sm-3 control-label"><?php echo get_phrase('nouvelle_désignation'); ?></label>

                            <div class="col-sm-5">
                                
                            <select name="medicine[]" class="js-states form-control select2" id="medicine">
                            <optgroup>
                            <option></option>
                                <?php
                                $medicines = $this->db->get('medicine')->result_array();
                                $medicines = array_reverse($medicines);
                                foreach ($medicines as $row2):
                                    ?>
                            <option value="<?php echo $row2['name']; ?>">
                                <?php echo $row2['name']; ?>
                            </option>
                                <?php endforeach; ?>
                            </optgroup>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="pu[]"  value="" 
                                placeholder="<?php echo get_phrase('montant'); ?>" >
                            </div>
                        </div>
                  </div>
            <!-- TEMPORARY INVOICE ENTRY ENDS HERE-->
            <hr>
    <!------------------------Fin partie vente-------------------------------->

                <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="submit" class="btn btn-info" id="submit-button">
                                <?php echo get_phrase('mettre_à_jour_le_reçu'); ?></button>
                            <span id="preloader-form"></span>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    </form>

                </div>

            </div>

        </div>
    </div>
<?php } ?>

<script>  

    // CREATING BLANK INVOICE ENTRY
    var blank_invoice_entry = '';
    $(document).ready(function () {
        blank_invoice_entry = $('#invoice_entry_temp').html();
        $('#invoice_entry_temp').remove();
    });

    function add_entry()
    {
        $("#invoice_entry").append(blank_invoice_entry);
    }

    // REMOVING INVOICE ENTRY
    function deleteParentElement(n) {
        n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
    }

</script>