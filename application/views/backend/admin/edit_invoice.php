<?php
/* 	
 * 	Tamplate: Edit Patient
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/modernizr/modernizr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php
$single_invoice_info = $this->db->get_where('invoice', array('invoice_id' => $param2))->result_array();
foreach ($single_invoice_info as $row):
?>
<div class="row">
    <div class="col-md-8" style="left: 180px;">
		<div class="panel panel-primary">
             <a href="<?php echo base_url(); ?>admin/invoice_manage" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>						
			<div class="panel-body p-70">
			<form method="post" action="<?php echo base_url(); ?>admin/invoice_manage/update/<?php echo $row['invoice_id']; ?>" class="p-70" id="form-validate" enctype="multipart/form-data">
                <div class="row">       
                    <div class="panel-heading" style="text-align:center;">
                    <div class="panel-title"> 
                        <h3><?php echo get_phrase('Modification-et-ajout'); ?></h3>
                    </div>
                    </div><br>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('titre-de-reçu'); ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="js-states form-control" name="title" id="title" data-validate="required"
                                   data-message-required="<?php echo get_phrase('valeur_requise'); ?>" 
                                   value="<?php echo $row['title']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('numéro-de-reçu'); ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="js-states form-control" name="invoice_number"  value="<?php echo $row['invoice_number']; ?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 js-states control-label"><?php echo get_phrase('patient'); ?></label>
                        <div class="col-sm-8">
                            <select name="patient_id" class="js-states form-control select2" id="patient_id">
                         <?php $patients = $this->db->get('patient')->result_array();
                                foreach ($patients as $row2): ?>
                                    <option value="<?php echo $row2['patient_id']; ?>"  
                                        <?php if ($row['patient_id'] == $row2['patient_id']) echo 'selected'; ?>>
                                            <?php echo $row2['name'].' '.$row2['prenom']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date-de-creation'); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-calendar"></i></span>
                                <input type="text" class="js-states form-control datepicker" name="creation_timestamp"  
                                value="<?php echo $row['creation_datetime']; ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase("date_d'échéance"); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-calendar"></i></span>
                                <input type="text" class="js-states form-control datepicker" name="due_timestamp"  
                                    value="<?php echo $row['due_timestamp']; ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('%remise'); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" name="discount_amount"  
                                value="<?php echo $row['discount_amount']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prise_en_charge'); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                            <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                                <input type="text" class="js-states form-control amount" name="prise_en_charge" value="<?php echo $row['prise_en_charge']; ?>" 
                                placeholder="<?php echo get_phrase('veuillez-saisir-le-nom-de-la-personne-physique-ou-moral'); ?> " id="prise_en_charge">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('%prise_en_charge'); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" name="pourcentage_prise"  
                                value="<?php echo $row['pourcentage_prise']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('statut_de_paiement'); ?></label>
                        <div class="col-sm-8">
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
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('mode-de-paiement'); ?></label>
                              <div class="col-sm-8">
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
                        <label for="note" class="col-sm-3 control-label"><?php echo get_phrase('note'); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" id="note" name="note"  value="<?php echo $row['note']; ?>">
                            </div>
                        </div>
                    </div>
                    </div>
                	<hr>
                    <span style="color: green; font-size: 25px;">Espace consultation</span><hr>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="button" class="btn btn-primary btn-sm btn-icon icon-left" onClick="add_entry()">
                                <?php echo get_phrase('ajouter'); ?>
                                <i class="entypo-plus"></i>
                            </button>
                        </div>
                    </div><br>
                    
                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry_temp" style="display:none;">
                        <div class="form-group invoice_entry">
                            <label for="Consultation" class="col-sm-3 control-label"><?php echo get_phrase('nouvelle_consultation'); ?></label>
                            <div class="col-sm-5">       
                                <select name="entry_description[]" class="js-states form-control select2 libelle" id="libelle">
                                    <optgroup>
                                        <option></option>
                                        <?php
                                        $designations = $this->db->get('designation')->result_array();
                                        $designations = array_reverse($designations);
                                        foreach ($designations as $row2): ?>
                                            <option value="<?php echo htmlspecialchars($row2['libelle']); ?>" data-price="<?php echo $row2['amount']; ?>">
                                                <?php echo htmlspecialchars($row2['libelle']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="js-states form-control amount" name="entry_amount[]" value="" placeholder="<?php echo get_phrase('montant'); ?>" id="amount" >
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
                    <div id="invoice_entry">
                        <?php
                        $invoice_entries = json_decode($row['invoice_entries']);
                            foreach ($invoice_entries as $invoice_entry) { ?>
                            <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label">
                               <?php echo get_phrase('prestation'); ?></label>
                                <div class="col-sm-5">
                                 <input type="text" class="js-states form-control" name="entry_description[]"  
                                 value="<?php echo $invoice_entry->description; ?>" >
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="js-states form-control" name="entry_amount[]"  
                                    value="<?php echo $invoice_entry->amount; ?>" >
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
       			 </form>	
			</div>				
		</div>				
	</div>
    <script>
    // CREATING BLANK INVOICE ENTRY
    var blank_invoice_entry = '';
    $(document).ready(function () {
        blank_invoice_entry = $('#invoice_entry_temp').html();
        $('#invoice_entry_temp').remove();
    });

    function add_entry() {
        $("#invoice_entry").append(blank_invoice_entry);
    }

    // REMOVING INVOICE ENTRY
    function deleteParentElement(n) {
        n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
    }

    // Ensure the DOM is fully loaded before accessing elements
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listener to dynamically added elements
        $(document).on('change', '.libelle', function () {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            $(this).closest('.invoice_entry').find('.amount').val(price ? price : '');
        });
    });
    </script>				
</div>
<?php endforeach; ?>
<?php 
$output .= ob_get_clean();
echo $output;