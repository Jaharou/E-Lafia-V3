<?php
$stock_info = $this->db->get('stock')->result_array();
$single_stock_info   = $this->db->get_where('stock', array('stock_id' => $param2))->result_array();
foreach ($single_stock_info as $row) {
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
                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>pharmacist/stock/update/<?php echo $row['stock_id']; ?>" method="post" enctype="multipart/form-data">
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
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('type-de-mouvement'); ?></label>
                            <div class="col-sm-9">
                            <select name="movement_type" class="js-states form-control selectboxit" id="movement_type">
                                
                                <option value="Sortie"<?php if ($row['movement_type'] == 'Sortie') echo 'selected'; ?>>
                                    <?php echo get_phrase('Sortie'); ?>
                                </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix-unitaire'); ?></label>
                            <div class="col-sm-9">
                                <input type="number" name="prix" class="form-control" id="field-1" value="<?php echo $row['prix']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('quantité-en-stock'); ?></label>
                            <div class="col-sm-9">
                                <input type="number" name="qte_stock" class="form-control" id="field-1" value="<?php echo $row['qte_stock']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('quantité-à-vendre'); ?></label>
                            <div class="col-sm-9">
                                <input type="number" name="qte_produit" class="form-control" id="field-1" value="<?php echo $row['qte_produit']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date'); ?></label>
                            <div class="col-sm-9">
                            <input type="text" name="date_liv" class="form-control" id="field-1" value="<?php echo $row['date_liv']; ?>">
                            </div>
                        </div>
                        <div class="col-sm-12 control-label">
                            <input type="submit" class="btn btn-success" value="Modifier">
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
<?php } ?>