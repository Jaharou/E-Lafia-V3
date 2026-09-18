<?php
$test_info = $this->db->get('test')->result_array();
$single_test_info   = $this->db->get_where('test', array('id_test' => $param2))->result_array();
foreach ($single_test_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:240px;">

            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>admin/test" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3 style="text-align:center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>

                <div class="panel-body">

                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>admin/test/update/<?php echo $row['id_test']; ?>" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('nom'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="libelle_examen" class="form-control" id="libelle_examen" value="<?php echo $row['libelle_examen']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('catégorie'); ?></label>
                            <div class="col-sm-9">
                                <select name="categorie_id" class="js-states form-control select2" id="js-states">
                         <?php $categories = $this->db->get('categorie_test')->result_array();
                                foreach ($categories as $row2): ?>
                                    <option value="<?php echo $row2['categorie_id']; ?>"  
                                        <?php if ($row['categorie_id'] == $row2['categorie_id']) echo 'selected'; ?>>
                                            <?php echo $row2['libelle_categ']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('unité-de-mesure'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="unite_mesure" class="form-control" id="unite_mesure" value="<?php echo $row['unite_mesure']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="amount" class="form-control" id="amount" value="<?php echo $row['amount']; ?>">
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