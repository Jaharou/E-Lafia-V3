<?php $medicine_category_info = $this->db->get('medicine_category')->result_array(); ?>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <h3><?php echo get_phrase('ordonnance-type'); ?></h3>
                </div>
            </div>

            <div class="panel-body">

                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>admin/type/create" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('produit'); ?><sup class="color-danger">*</sup></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="produit" name="produit" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('Nbres'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nbrs" name="nbrs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('P.Unit'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="prix" class="form-control" id="prix" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('P.Total'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="total" class="form-control" id="total" >
                        </div>
                    </div>

                    <div class="col-sm-3 control-label col-sm-offset-3">
                        <input type="submit" class="btn btn-success" value="Valider">
                    </div>
                </form>

            </div>

        </div>

    </div>
</div>