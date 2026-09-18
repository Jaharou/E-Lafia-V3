<?php
$medicine_category_info = $this->db->get('medicine_category')->result_array();
$single_medicine_info   = $this->db->get_where('medicine', array('medicine_id' => $param2))->result_array();
foreach ($single_medicine_info as $row) {
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
                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>pharmacist/medicine/update/<?php echo $row['medicine_id']; ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('nom'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" id="field-1" value="<?php echo $row['name']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('fournisseur'); ?></label>
                            <div class="col-sm-9">
                                <select name="fournisseur_id" class="js-states form-control select2" id="js-states">
                         <?php $fournisseurs = $this->db->get('fournisseur')->result_array();
                                foreach ($fournisseurs as $row2): ?>
                                    <option value="<?php echo $row2['fournisseur_id']; ?>"  
                                        <?php if ($row['fournisseur_id'] == $row2['fournisseur_id']) echo 'selected'; ?>>
                                            <?php echo $row2['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('medicine_category'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="medicine_category" class="form-control" id="medicine_category" value="<?php echo $row['medicine_category']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('DCI'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="dci" class="form-control" id="field-1" value="<?php echo $row['dci']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('dosage'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="dosage" class="form-control" id="field-1" value="<?php echo $row['dosage']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('condition'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="condit" class="form-control" id="field-1" value="<?php echo $row['condit']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('forme'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="forme" class="form-control" id="field-1" value="<?php echo $row['forme']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="price" class="form-control" id="field-1" value="<?php echo $row['price']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date-de-commande'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="date_commande" class="form-control" id="date_commande" value="<?php echo $row['date_commande']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('quantité-commandée'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="qte_prod" class="form-control" id="qte_prod" value="<?php echo $row['qte_prod']; ?>">
                        </div>
                    </div>   
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('montant'); ?></label>
                        <div class="col-sm-9">
                            <input type="number" name="amount" class="form-control" id="field-1" value="<?php echo $row['amount']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('statut'); ?></label>
                        <div class="col-sm-9">
                            <select name="status" class="js-states form-control" id="js-states"  data-validation="required">
                                    <optgroup value="<?php echo $row['status']; ?>">
                                        <option>Sélectionner un statut</option>
                                    <option value="disponible" <?php if ($row['status'] == 'disponible') echo 'selected'; ?> >
                                    <?php echo get_phrase('Disponible'); ?>
                                    </option>
                                    <option value="indisponible" <?php if ($row['status'] == 'indisponible') echo 'selected'; ?> >
                                    <?php echo get_phrase('Indisponible'); ?>
                                    </option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase("date-d'expiration"); ?></label>
                            <div class="col-sm-9">
                            <input type="date" name="expiration_date" class="form-control" id="expiration_date" value="<?php echo $row['expiration_date']; ?>">
                            </div>
                        </div>
                        <div class="col-sm-3 control-label col-sm-offset-3">
                            <input type="submit" class="btn btn-success" value="Modifier">
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
<?php } ?>