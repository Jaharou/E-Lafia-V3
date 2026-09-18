<?php
/*  
 *  Tamplate: Edit Patient
 *  @author : Raju Ahmed
 *  Date    : 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen" >
    <script src="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.js"></script>
</head>
<?php $output = ''; ?>
<?php ob_start(); ?>
<?php
$single_traitement_info = $this->db->get_where('traitement', array('traitement_id' => $param2))->result_array();
foreach ($single_traitement_info as $row):
?>
<div class="row">
    <div class="col-md-8" style="left: 180px;">
        <div class="panel panel-primary">
             <a href="<?php echo base_url(); ?>receptionist/traitement" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>                     
            <div class="panel-body p-70">
            <form method="post" action="<?php echo base_url(); ?>receptionist/traitement/update/<?php echo $row['traitement_id']; ?>" class="p-70" id="form-validate" enctype="multipart/form-data">                
                <div class="row">       
                    <div class="panel-heading" style="text-align:center;">
                    <div class="panel-title"> 
                        <h3><?php echo get_phrase('Modification-et-ajout'); ?></h3>
                    </div>
                    </div><br>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('numéro_de_reçu'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="traitement_number"  value="<?php echo $row['traitement_number']; ?>" >
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
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('date_traitement'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="date_traitement" class="form-control" id="date_traitement" value="<?php echo $row['date_traitement']; ?>">
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
                                placeholder="<?php echo get_phrase('veuillez-saisir-le-nom-de-la-personne-physique-ou-moral'); ?>" id="prise_en_charge">
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
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('statut_de_paiement'); ?></label>
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
                        <label for="note" class="col-sm-3 control-label"><?php echo get_phrase('note'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="entypo-info-circled"></i></span>
                             <input type="text" class="js-states form-control" id="note" name="note"  value="<?php echo $row['note']; ?>">
                            </div>
                        </div>
                    </div>
                    </div>
                    <br>
                    <span style="color: green; font-size: 25px;">Espace de nouveau traitement</span>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="button" class="btn btn-primary btn-sm btn-icon icon-left" onClick="add_entry()">
                                <?php echo get_phrase('ajouter'); ?>
                                <i class="entypo-plus"></i>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="vente_entry_temp" style="display:none;">
                        <div class="form-group traitement_entry">
                            <label for="traitement" class="col-sm-3 control-label">
                                <?php echo get_phrase('nouveau-traitement'); ?></label>
                            <div class="col-sm-2">       
                                <select name="libelle_prod[]" class="js-states form-control select2 libelle_prod libelle_prod" id="produit_id">
                                    <optgroup>
                                        <option>Sélectionner un traitement</option>
                                        <?php
                                        $produits = $this->db->get('produit')->result_array();
                                        $produits = array_reverse($produits);
                                        foreach ($produits as $row2): ?>
                                            <option value="<?php echo htmlspecialchars($row2['libelle_prod']) ; ?>" data-price="<?php echo $row2['amount_prod']; ?>">
                                                <?php echo htmlspecialchars($row2['libelle_prod']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control amount_prod" name="amount_prod[]" value="" placeholder="<?php echo get_phrase('prix-unitaire'); ?>" id="amount" readonly>
                            </div>
                            <div class="col-sm-2">
                            <input type="text" class="js-states form-control quantite" id="qte" name="quantite[]" placeholder="<?php echo get_phrase('quantité'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control prixTrait" id="prix" name="prixTrait[]" value="0" placeholder="<?php echo get_phrase('montant_total'); ?>" readonly>
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
                    <div id="traitement_entry">
                        <?php
                        $traitement_entries = json_decode($row['traitement_entries']);
                            foreach ($traitement_entries as $traitement_entry) { ?>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label">
                       <?php echo get_phrase('traitement'); ?></label>
                        <div class="col-sm-2">
                            <input type="text" class="js-states form-control" name="libelle_prod[]"  
                            value="<?php echo $traitement_entry->description; ?>" >
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="amount_prod[]" class="form-control" placeholder="<?php echo get_phrase('prix-unitaire'); ?>" id="amount_prod"
                            value="<?php echo $traitement_entry->amount; ?>">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="quantite[]" class="form-control" placeholder="<?php echo get_phrase('quantité'); ?>" id="quantite"
                            value="<?php echo $traitement_entry->qte; ?>">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="prixTrait[]" class="form-control" placeholder="<?php echo get_phrase('montant_total'); ?>" id="prixTrait"
                            value="<?php echo $traitement_entry->prixTrait; ?>">
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
                        <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
                        <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
                    </div>                
                    </div>
                 </form>    
            </div>              
        </div>              
    </div>
    <script>
        // CREATING BLANK INVOICE ENTRY
        var blank_vente_entry = '';
        $(document).ready(function () {
            blank_vente_entry = $('#vente_entry_temp').html();
            $('#vente_entry_temp').remove();
        });

        function add_entry() {
            $("#traitement_entry").append(blank_vente_entry);
        }

        // REMOVING INVOICE ENTRY
        function deleteParentElement(n) {
            n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
        }

        // Ensure the DOM is fully loaded before accessing elements
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener to dynamically added elements
            $(document).on('change', '.libelle_prod', function () {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                $(this).closest('.traitement_entry').find('.amount_prod').val(price ? price : '');
            });
        });

         $(document).ready(function() {
        // Lorsque l'utilisateur change la quantité ou sélectionne un traitement
        $('#traitement_entry').on('input', '.quantite, .amount_prod', function() {
            calculateTotal($(this).closest('.form-group'));
        });

        // Fonction pour calculer le montant total
        function calculateTotal($entry) {
            var quantity = parseFloat($entry.find('.quantite').val()) || 0;
            var unitPrice = parseFloat($entry.find('.amount_prod').val()) || 0;
            var totalPrice = quantity * unitPrice;
            $entry.find('.prixTrait').val(totalPrice.toFixed()); // Arrondir à 2 décimales
        }
    });

    </script>               
</div>
<?php endforeach; ?>
<?php 
$output .= ob_get_clean();
echo $output;