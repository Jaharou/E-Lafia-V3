<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/modernizr/modernizr.min.js"></script>
</head>
<?php
$examen_info = $this->db->get('examen')->result_array();
$single_examen_info   = $this->db->get_where('examen', array('id_examen' => $param2))->result_array();
foreach ($single_examen_info as $row) {
?>

    <div class="row">
        <div class="col-md-8" style="left:230px;">
            <div class="panel panel-primary" data-collapsed="0">
                <a href="<?php echo base_url(); ?>receptionist/examen" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
                <div class="panel-heading">
                    <div class="">
                        <h3 style="text-align: center;"><?php echo get_phrase('modification'); ?></h3>
                    </div>
                </div>
                <div class="panel-body p-20">
                    <form role="form" class="form-horizontal p-20 form-groups-bordered" action="<?php echo base_url(); ?>receptionist/examen/update/<?php echo $row['id_examen']; ?>" method="post" enctype="multipart/form-data">
                        <div class="row">
                        <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase("numéro-d'examen"); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="js-states form-control" name="examen_number"  value="<?php echo $row['examen_number']; ?>" >
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
                        <label for="blood_examen" class="col-sm-3 control-label"><?php echo get_phrase('groupe_sanguin'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="blood_examen" class="form-control" id="blood_examen" value="<?php echo $row['blood_examen']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="date_examen" class="col-sm-3 control-label"><?php echo get_phrase('Date examen'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="date_examen" class="form-control" id="date_examen" value="<?php echo $row['creation_time']; ?>">
                        </div>
                    </div>                        
                    <div class="form-group">
                      <label for="statut_examen" class="col-sm-3 control-label"><?php echo get_phrase('statut'); ?></label>
                        <div class="col-sm-9">
                            <select name="statut_examen" class="form-control" id="js-states"  data-validation="">
                               <optgroup label="<?php echo get_phrase('sélectionner_un_statut'); ?>">  
                                <option value="En-instance" <?php if ($row['statut_examen'] == 'En-instance') echo 'selected'; ?> >
                                    <?php echo get_phrase('En-instance'); ?>
                                </option>
                                <option value="En-cours" <?php if ($row['statut_examen'] == 'En-course') echo 'selected'; ?> >
                                    <?php echo get_phrase('En-cours'); ?>
                                </option>
                                <option value="Terminé" <?php if ($row['statut_examen'] == 'Terminé') echo 'selected'; ?> >
                                    <?php echo get_phrase('Terminé'); ?>
                                </option>
                                <option value="Validé" <?php if ($row['statut_examen'] == 'Validé') echo 'selected'; ?> >
                                    <?php echo get_phrase('Validé'); ?>
                                </option>
                                <option value="Annulé" <?php if ($row['statut_examen'] == 'Annulé') echo 'selected'; ?> >
                                    <?php echo get_phrase('Annulé'); ?>
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
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('%remise'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" name="discount_amount"  
                                value="<?php echo $row['discount_amount']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prise_en_charge'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                            <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                                <input type="text" class="js-states form-control amount" name="prise_en_charge" value="<?php echo $row['prise_en_charge']; ?>" 
                                placeholder="<?php echo get_phrase('veuillez-saisir-le-nom-de-la-personne-physique-ou-moral'); ?> " id="prise_en_charge">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('%prise_en_charge'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" name="pourcentage_prise"  
                                value="<?php echo $row['pourcentage_prise']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('mode-de-paiement'); ?></label>
                      <div class="col-sm-9">
                        <select name="mode_paiement" class="js-states form-control select2" id="mode_paiement">
                            <optgroup>  
                            <option value="Espèce" <?php if ($row['mode_paiement'] == 'Espèce') echo 'selected'; ?> >
                            <?php echo get_phrase('Espèce'); ?>
                            </option>
                            <option value="Chèque" <?php if ($row['mode_paiement'] == 'Chèque') echo 'selected'; ?> >
                            <?php echo get_phrase('Chèque'); ?>
                        </option>
                            <option value="Carte-bancaire" <?php if ($row['mode_paiement'] == 'Carte_bancaire') echo 'selected'; ?> >
                             <?php echo get_phrase('Carte_bancaire'); ?>
                            </option>
                            <option value="Virement" <?php if ($row['mode_paiement'] == 'Virement') echo 'selected'; ?> >
                            <?php echo get_phrase('Virement'); ?>
                            </option>
                            </optgroup>
                        </select>
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('statut-de-paiement'); ?></label>
                        <div class="col-sm-9">
                            <select name="status" class="js-states form-control selectboxit" id="status">
                                <option value="payé" <?php if ($row['status'] == 'payé') echo 'selected'; ?> >
                                    <?php echo get_phrase('payé'); ?>
                                </option>
                                <option value="impayé"<?php if ($row['status'] == 'impayé') echo 'selected'; ?>>
                                    <?php echo get_phrase('impayé'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 js-states control-label"><?php echo get_phrase('utilisateur'); ?></label>
                        <div class="col-sm-9">

                            <select name="receptionist_id" class="js-states form-control select2" id="js-states">
                                <?php $receptionistes = $this->db->get('receptionist')->result_array();
                                foreach ($receptionistes as $row2): ?>
                                    <option value="<?php echo $row2['name']; ?>"  
                                        <?php if ($row['receptionist_id'] == $row2['name']) echo 'selected'; ?>>
                                            <?php echo $row2['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                        </div>
                    </div>
                    <hr>
                    <span style="color: green; font-size: 25px;">Espace examen</span><hr>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="button" class="btn btn-primary btn-sm btn-icon icon-left" onClick="add_entry()">
                                <?php echo get_phrase('ajouter'); ?>
                                <i class="entypo-plus"></i>
                            </button>
                        </div>
                    </div><br>
                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="examen_entry_temp" style="display:none;">
                        <div class="form-group examen_entry">
                            <label for="examen" class="col-sm-3 control-label"><?php echo get_phrase('nouveau-examen'); ?></label>
                            <div class="col-sm-2">       
                                <select name="libelle_examen[]" class="js-states form-control select2 libelle_examen" id="id_test">
                                    <optgroup>
                                        <option>Sélectionner un test</option>
                                        <?php
                                        $tests = $this->db->get('test')->result_array();
                                        $tests = array_reverse($tests);
                                        foreach ($tests as $row2): ?>
                                            <option value="<?php echo htmlspecialchars($row2['libelle_examen']); ?>"
                                                data-price="<?php echo $row2['amount']; ?>">
                                                <?php echo htmlspecialchars($row2['libelle_examen']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control amount" name="amount_examen[]" value="" placeholder="<?php echo get_phrase('PU'); ?>" id="amount_examen">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control qte_examen" name="qte_examen[]" value="" placeholder="<?php echo get_phrase('quantité'); ?>" id="qte_examen">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control amountT" name="amountT[]" value="" placeholder="<?php echo get_phrase('montant-total'); ?>" id="amountT">
                            </div> 
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-danger" onclick="deleteParentElement(this)" title="Supprimer">
                                <i class="fa fa-trash-o"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- TEMPORARY INVOICE ENTRY ENDS HERE-->
                    <hr>
                    <!-- INVOICE ENTRY STARTS HERE-->
                    <div id="examen_entry">
                        <?php
                        $examen_entries = json_decode($row['examen_entries']);
                            foreach ($examen_entries as $examen_entry) { ?>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label">
                       <?php echo get_phrase('examen'); ?></label>
                        <div class="col-sm-2">
                            <input type="text" class="js-states form-control" name="libelle_examen[]"  
                            value="<?php echo $examen_entry->description; ?>" >
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="amount_examen[]" class="form-control" placeholder="<?php echo get_phrase('montant'); ?>" id="amount_examen"
                            value="<?php echo $examen_entry->montant; ?>">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="qte_examen[]" class="form-control" placeholder="<?php echo get_phrase('quantité'); ?>" id="qte_examen"
                            value="<?php echo $examen_entry->qte_examen; ?>">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="amountT[]" class="form-control" placeholder="<?php echo get_phrase('montant-total'); ?>" id="amountT"
                            value="<?php echo $examen_entry->amountT; ?>">
                        </div>
                        <div class="col-sm-1">
                        <button type="button" class="btn btn-danger" onclick="deleteParentElement(this)" title="Supprimer"><i class="fa fa-trash-o"></i></button>
                        </div>
                    </div>
                        <?php
                    }
                    ?>
                    </div>
                    <!-- INVOICE ENTRY ENDS HERE-->
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
        <script type="text/javascript">
            // CREATING BLANK INVOICE ENTRY
            var blank_examen_entry = '';
            $(document).ready(function () {
                blank_examen_entry = $('#examen_entry_temp').html();
                $('#examen_entry_temp').remove();
            });

            function add_entry() {
                $("#examen_entry").append(blank_examen_entry);
            }

            // REMOVING INVOICE ENTRY
            function deleteParentElement(n) {
                n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
            }

        // Ensure the DOM is fully loaded before accessing elements
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener to dynamically added elements
            $(document).on('change', '.libelle_examen', function () {
                const selectedOption = this.options[this.selectedIndex];
                const amount_examen = selectedOption.getAttribute('data-price');
                $(this).closest('.examen_entry').find('.amount').val(amount_examen ? amount_examen : '');
            });
        });

        $(document).ready(function() {
        // Lorsque l'utilisateur change la quantité ou sélectionne un examen
        $('#examen_entry').on('input', '.qte_examen, .amount_examen', function() {
            calculateTotal($(this).closest('.form-group')); 
        });

        // Fonction pour calculer le montant total
        function calculateTotal($entry) {
            var qte_examen = parseFloat($entry.find('.qte_examen').val()) || 0;
            var unitPrice = parseFloat($entry.find('.amount').val()) || 0;
            var totalPrice = qte_examen * unitPrice;
            $entry.find('.amountT').val(totalPrice.toFixed()); // Arrondir à 2 décimales
            }
        });

    </script>
    </div>
    <?php
    }
    ?>
