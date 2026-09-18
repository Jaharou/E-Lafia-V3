<?php
$produit_info = $this->db->get('produit')->result_array();
$single_produit_info   = $this->db->get_where('produit', array('produit_id' => $param2))->result_array();
foreach ($single_produit_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:240px;">

            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>admin/produit" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3 style="text-align:center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>
                <div class="panel-body">

                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>admin/produit/update/<?php echo $row['produit_id']; ?>" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('libellé'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="libelle_prod" class="form-control" id="libelle_prod" value="<?php echo $row['libelle_prod']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="amount_prod" class="form-control" id="amount_prod" value="<?php echo $row['amount_prod']; ?>">
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