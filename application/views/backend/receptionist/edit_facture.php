<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/modernizr/modernizr.min.js"></script>
</head>
<?php
$facture_info = $this->db->get('facture')->result_array();
$single_facture_info   = $this->db->get_where('facture', array('facture_id' => $param2))->result_array();
foreach ($single_facture_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:230px;">
            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>receptionist/facture" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="">
                        <h3 style="text-align: center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>
                <div class="panel-body p-20">
                    <form role="form" class="form-horizontal p-20 form-groups-bordered" action="<?php echo base_url(); ?>receptionist/examen/update/<?php echo $row['facture_id']; ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase("numéro-de-facture"); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="js-states form-control" name="facture_number"  value="<?php echo $row['facture_number']; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('patient'); ?></label>
                            <div class="col-sm-9">
                                <select name="patient_id" class="form-control" id="patient_id"
                                        data-patient-autocomplete="1" data-placeholder="Rechercher un patient (nom, prénom, téléphone)">
                                <?php $current_patient = $this->db->get_where('patient', array('patient_id' => $row['patient_id']))->row_array();
                                if ($current_patient): ?>
                                    <option value="<?php echo $current_patient['patient_id']; ?>" selected>
                                        <?php echo trim($current_patient['name'].' '.$current_patient['prenom']); ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                        </div> 
                        <div class="form-group">
                          <label for="facture" class="col-sm-3 control-label"><?php echo get_phrase('date-de-facture'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="date_facture" class="form-control" id="date_facture" value="<?php echo $row['date_facture']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="amount" class="col-sm-3 control-label"><?php echo get_phrase('consultation'); ?></label>
                            <div class="col-sm-9">
                                <?php foreach ($invoice_entries as $entry) { ?>
                                    <input type="text" name="invoice_description[]" class="form-control" value="<?php echo $entry['description']; ?>">
                                    <input type="text" name="invoice_amount[]" class="form-control" value="<?php echo $entry['amount']; ?>">
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="amount" class="col-sm-3 control-label"><?php echo get_phrase('examen'); ?></label>
                            <div class="col-sm-9">
                                <?php foreach ($examen_entries as $entry) { ?>
                                    <input type="text" name="examen_description[]" class="form-control" value="<?php echo $entry['description']; ?>">
                                    <input type="text" name="examen_amount[]" class="form-control" value="<?php echo $entry['amount']; ?>">
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label"><?php echo get_phrase('montant-total'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="total_amount" class="form-control" id="total_amount" value="<?php echo $row['total_amount']; ?>">
                        </div>
                        </div>
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('statut-de-paiement'); ?></label>
                        <div class="col-sm-9">
                            <select name="status" class="js-states form-control selectboxit" id="status">
                                <option value="Payé" <?php if ($row['status'] == 'Payé') echo 'selected'; ?> >
                                    <?php echo get_phrase('Payé'); ?>
                                </option>
                                <option value="Non payé"<?php if ($row['status'] == 'Non payé') echo 'selected'; ?>>
                                    <?php echo get_phrase('Non-payé'); ?>
                                </option>
                                <option value="Annulé"<?php if ($row['status'] == 'Annulé') echo 'selected'; ?>>
                                    <?php echo get_phrase('Annulé'); ?>
                                </option>
                            </select>
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="échèance" class="col-sm-3 control-label"><?php echo get_phrase("date-d'échèance"); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="updated_at" class="form-control" id="updated_at" value="<?php echo $row['updated_at']; ?>">
                        </div>
                        </div>
                        <div class="col-md-12">
                        <div class="btn-group pull-right mt-10" role="group">
                            <button type="reset" class="btn btn-gray btn-wide">
                            <i class="fa fa-times"></i>Annuler</button>
                            <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
                        </div>                
                    </div>
                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
