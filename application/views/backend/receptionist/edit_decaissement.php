<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/modernizr/modernizr.min.js"></script>
</head>
<?php
$decaissement_info = $this->db->get('decaissement')->result_array();
$single_decaissement_info   = $this->db->get_where('decaissement', array('decaissement_id' => $param2))->result_array();
foreach ($single_decaissement_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:230px;">
            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>receptionist/decaissement" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="">
                        <h3 style="text-align: center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>
                <div class="panel-body p-20">
                    <form role="form" class="form-horizontal p-20 form-groups-bordered" action="<?php echo base_url(); ?>receptionist/decaissement/update/<?php echo $row['decaissement_id']; ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase("numéro"); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="js-states form-control" name="decaissement_number"  value="<?php echo $row['decaissement_number']; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="date" class="col-sm-3 control-label"><?php echo get_phrase('date'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="date_decaissement" class="form-control" id="date_decaissement" value="<?php echo $row['date_decaissement']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="tiers" class="col-sm-3 control-label"><?php echo get_phrase('tiers/patient'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="libelle_decaiss" class="form-control" id="libelle_decaiss" value="<?php echo $row['libelle_decaiss']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('compte'); ?></label>
                            <div class="col-sm-9">
                                <select name="receptionist_id" class="js-states form-control select2" id="js-states">
                                <?php $receptionistes = $this->db->get('receptionist')->result_array();
                                foreach ($receptionistes as $row2): ?>
                                    <option value="<?php echo $row2['receptionist_id']; ?>"  
                                        <?php if ($row['receptionist_id'] == $row2['receptionist_id']) echo 'selected'; ?>>
                                            <?php echo $row2['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="mot" class="col-sm-3 control-label"><?php echo get_phrase('motif'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="motif" class="form-control" id="motif" value="<?php echo $row['motif']; ?>">
                        </div>
                    </div>
                                            
                    <div class="form-group">
                      <label for="typ" class="col-sm-3 control-label"><?php echo get_phrase('type'); ?></label>
                        <div class="col-sm-9">
                            <select name="type" class="form-control" id="js-states"  data-validation="">
                               <optgroup label="<?php echo get_phrase('sélectionner-un-type'); ?>">  
                                <option value="Piaement fournisseur" <?php if ($row['type'] == 'Piaement fournisseur') echo 'selected'; ?> >
                                    <?php echo get_phrase('Piaement fournisseur'); ?>
                                </option>
                                <option value="Paiement patient" <?php if ($row['type'] == 'Paiement patient') echo 'selected'; ?> >
                                    <?php echo get_phrase('Paiement patient'); ?>
                                </option>
                                <option value="Charge" <?php if ($row['type'] == 'Charge') echo 'selected'; ?> >
                                    <?php echo get_phrase('Charge'); ?>
                                </option>
                                <option value="Dépense" <?php if ($row['type'] == 'Dépense') echo 'selected'; ?> >
                                    <?php echo get_phrase('Dépense'); ?>
                                </option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="amount" class="col-sm-3 control-label"><?php echo get_phrase('montant'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="montant" class="form-control" id="montant" value="<?php echo $row['montant']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('mode-de-paiement'); ?></label>
                              <div class="col-sm-9">
                                <select name="mode" class="js-states form-control select2" id="mode">
                                    <optgroup>  
                                    <option value="Espèce" <?php if ($row['mode'] == 'Espèce') echo 'selected'; ?> >
                                    <?php echo get_phrase('Espèce'); ?>
                                    </option>
                                    <option value="Chèque" <?php if ($row['mode'] == 'Chèque') echo 'selected'; ?> >
                                    <?php echo get_phrase('Chèque'); ?>
                                </option>
                                    <option value="Carte-bancaire" <?php if ($row['mode'] == 'Carte_bancaire') echo 'selected'; ?> >
                                     <?php echo get_phrase('Carte_bancaire'); ?>
                                    </option>
                                    <option value="Virement" <?php if ($row['mode'] == 'Virement') echo 'selected'; ?> >
                                    <?php echo get_phrase('Virement'); ?>
                                    </option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="not_examen" class="col-sm-3 control-label"><?php echo get_phrase('note'); ?></label>
                        <div class="col-sm-9">
                            <textarea type="text" name="note" class="form-control" id="note"><?php echo $row['note']; ?></textarea>
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
