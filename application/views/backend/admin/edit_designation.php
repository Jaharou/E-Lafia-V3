<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/modernizr/modernizr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<?php
$designation_info = $this->db->get('designation')->result_array();
$single_designation_info   = $this->db->get_where('designation', array('entry_description_id ' => $param2))->result_array();
foreach ($single_designation_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:240px;">

            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>admin/designation" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3 style="text-align:center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>

                <div class="panel-body">

                    <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>admin/designation/update/<?php echo $row['entry_description_id']; ?>" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('nom'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="libelle" class="form-control" id="libelle" value="<?php echo $row['libelle']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="amount" class="form-control" id="amount_designation" value="<?php echo $row['amount']; ?>">
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