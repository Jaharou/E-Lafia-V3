<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
</head>
<div class="row">
    <div class="col-md-8" style="margin-top: 0px;left: 170px;">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
              <div class="panel-title">
                <i class="entypo-plus-circled"></i>
                <?php echo get_phrase('nouvelle-consultation'); ?>
              </div>
            </div>
            <div style="clear:both;"></div><br>
            <a href="<?php echo base_url(); ?>receptionist/invoice_manage" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>                     
            <div class="panel-body p-20">
                <form method="post" action="<?php echo base_url(); ?>receptionist/invoice_add/create" class="p-20" id="form-validate" enctype="multipart/form-data">
                    <?php $invoiceNumber = $this->db->count_all('invoice')+1; ?>
                <div class="row panel panel-primary">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="title_recu"><?php echo get_phrase('titre-de-reçu'); ?><sup class="color-danger"></sup></label>
                        <select name="title" class="form-control" id="name13"  data-validation="">
                            <optgroup> 
                            <option ><?php echo get_phrase("reçu-d'encaissement"); ?></option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('numéro-de-reçu'); ?><sup class="color-danger"></sup></label>
                        <input type="text" class="form-control" id="name13" name="invoice_number"  data-validation="" value="<?php echo "0".$invoiceNumber; ?>" readonly >
                    </div>
                </div>  
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('date-de-creation'); ?><sup class="color-danger">*</sup></label>
                        <input type="date" class="form-control" id="name13" name="creation_datetime"  data-validation="required" value="<?php 
                        date_default_timezone_set('Africa/Niamey'); 
                        echo date("d-m-Y H:i:s"); ?>" >
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                        <select name="patient_id" class="form-control" id="patient_id" data-validation="required"
                                data-patient-autocomplete="1" data-placeholder="Rechercher un patient (nom, prénom, téléphone)" id="js-states">
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('désignation'); ?></label>
                        <select name="entry_description[]" class="form-control select2" id="entry_description" id="js-states">
                            <optgroup>
                                <option value="" data-price="">Sélectionner une prestation</option>
                                <?php
                                $designations = $this->db->get('designation')->result_array();
                                $designations = array_reverse($designations); // Inverser l'ordre des désignations
                                foreach ($designations as $row2): ?>
                                    <option value="<?php echo $row2['libelle']; ?>" data-price="<?php echo $row2['amount']; ?>">
                                        <?php echo $row2['libelle']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('prix-unitaire'); ?></label>
                        <input type="text" class="form-control" id="entry_amount" name="entry_amount[]">
                    </div>
                </div>
                <script>
                        document.addEventListener('DOMContentLoaded', function() {
                        var descriptionSelect = document.getElementById('entry_description');
                        var amountInput = document.getElementById('entry_amount');

                        descriptionSelect.addEventListener('change', function() {
                        var selectedOption = this.options[this.selectedIndex];
                        var price = selectedOption.getAttribute('data-price');
                        amountInput.value = price || ''; // Met à jour le champ de prix unitaire
                        });
                    });
                </script>                       
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('quantité'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="qte_consult" name="qte_consult[]" value="" data-validation="required">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="net_amount" name="net_amount[]" value="0"  data-validation="" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('% remise'); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="0"  data-validation="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase('prise-en-charge'); ?></label>
                        <input type="text" class="form-control" id="discount_amount" name="prise_en_charge" data-validation="" placeholder="Veillez saisir le nom de la personne" title ="Veillez saisir le nom de la personne physique ou morale">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name13"><?php echo get_phrase("% prise-en-charge"); ?><sup class="color-danger"></sup></label>
                        <input type="number" class="form-control" id="pourcentage_prise" name="pourcentage_prise" value="0"  data-validation="">
                    </div>
                </div>
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="name13"><?php echo get_phrase('mode-paiement'); ?></label>
                            <select class="form-control" id="js-states" name="mode_paiement">
                                <optgroup>
                                <option value="Espèce"><?php echo get_phrase('Espèce'); ?></option>
                                <option value="Chèque"><?php echo get_phrase('Chèque'); ?></option>
                                <option value="Carte-bancaire"><?php echo get_phrase('Carte_bancaire'); ?></option>
                                <option value="Virement"><?php echo get_phrase('Virement'); ?></option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status"><?php echo get_phrase('statut'); ?><sup class="color-danger"></sup></label>
                            <select class="form-control" id="js-states" name="status" data-validation="">
                             <optgroup>
                              <option value="payé"><?php echo get_phrase('payé'); ?></option>
                                <option value="impayé"><?php echo get_phrase('impayé'); ?></option>
                             </optgroup>
                            </select>  
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name13"><?php echo get_phrase("periode-d'échéance"); ?><sup class="color-danger"></sup></label>
                            <input type="date" class="form-control" id="name13" name="due_timestamp"  data-validation="" value="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="receptionist_id"><?php echo get_phrase('utilisteur'); ?></label>
                            
                            <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="">
                                
                                <option>Sélectionner votre nom</option>
                                <?php
                                $receptionistes = $this->db->get('receptionist')->result_array();
                                $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
                                foreach ($receptionistes as $row2):
                                    ?>
                               <option value="<?php echo $row2['name']; ?>">
                                <?php echo $row2['name']; ?>
                                </option>
                                    <?php endforeach; ?>
                               
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="panel-heading panel-primary">
                        <span style="color: green; font-size: 25px;">Nouvelle-prestation</span>

                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <button type="button" class="btn btn-primary btn-sm btn-icon icon-left" onClick="add_entry()">
                                <?php echo get_phrase('ajouter'); ?>
                                <i class="entypo-plus"></i>
                            </button>
                        </div>
                    </div>
                    <br>
                    <!-- TEMPORARY INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry_temp" style="display:none;">
                        <div class="form-group invoice_entry">
                            <label for="Consultation" class="col-sm-2 control-label"><?php echo get_phrase('désignation'); ?></label>
                            <div class="col-sm-3">       
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
                            <div class="col-sm-2">
                                <input type="text" class="form-control amount" name="entry_amount[]" value="" placeholder="<?php echo get_phrase('P-U'); ?>" id="amount" readonly>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control qte_consult" name="qte_consult[]" value="" placeholder="<?php echo get_phrase('quantité'); ?>" id="qte_consult" title="Quantité">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="js-states form-control net_amount" name="net_amount[]" value="" placeholder="<?php echo get_phrase('montant-total'); ?>" id="net_amount" > 
                            </div>
                            <div class="col-sm-1">
                                <button type="button" class="btn btn-danger" onclick="deleteParentElement(this)"><i class="fa fa-trash-o"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- TEMPORARY INVOICE ENTRY ENDS HERE-->
                    <hr>
                    <!-- INVOICE ENTRY STARTS HERE-->
                    <div id="invoice_entry">
                        <?php
                        $invoice_id = 1; // par exemple
                        $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->result_array();
                        $query = $this->db->get('invoice');
                        $row = $query->row_array();

                        if ($row && isset($row['invoice_entries'])) {
                            $invoice_entries = json_decode($row['invoice_entries']);
                            if (is_array($invoice_entries)) {
                                foreach ($invoice_entries as $invoice_entry) { ?>
                                    <div class="form-group">
                                        <label for="field-1" class="col-sm-3 control-label">
                                            <?php echo get_phrase('désignation'); ?>
                                        </label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="entry_description[]" value="<?php echo htmlspecialchars($invoice_entry->description); ?>" placeholder="<?php echo get_phrase('désignation'); ?>">
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="entry_amount[]" value="<?php echo htmlspecialchars($invoice_entry->amount); ?>" placeholder="<?php echo get_phrase('montant'); ?>">
                                        </div>
                                        <div class="col-sm-1">
                                            <button type="button" class="btn btn-danger" onclick="deleteParentElement(this)"><i class="fa fa-trash-o"></i></button>
                                        </div>
                                    </div>
                                <?php }
                            }
                        } else {
                            echo "<p>No invoice entries found.</p>";
                        }
                        ?>
                    </div>
                    <!-- INVOICE ENTRY ENDS HERE-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group pull-right mt-10" role="group">
                                <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
                                <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
                            </div>                
                        </div>
                    </div>
                </form>    
            </div>              
        </div>              
    </div>
    <script>
    // Récupérer les éléments du DOM de consultations
var montantInput = document.getElementById("entry_amount");
var qteConsultInput = document.getElementById("qte_consult");
//var priseInput = document.getElementById("pourcentage_prise");
var montantNetInput = document.getElementById("net_amount");
// Écouter les modifications des entrées de montant et remise
montantInput.addEventListener("input", calculerMontantNet);
qteConsultInput.addEventListener("input", calculerMontantNet);
//priseInput.addEventListener("input", calculerMontantNet);

function calculerMontantNet() {
  // Récupérer les valeurs entrées
  var montant = parseFloat(montantInput.value);
  var qteConsult = parseFloat(qteConsultInput.value);
  //var prise = parseFloat(priseInput.value);

   //Calculer le montant net
  var montantNet = (montant * qteConsult);

  // Afficher le montant net arrondi à 2 décimales
  montantNetInput.value = montantNet.toFixed(0);
}

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
    $(document).ready(function() {
        // Lorsque l'utilisateur change la quantité ou sélectionne un examen
        $('#invoice_entry').on('input', '.qte_consult, .amount', function() {
            calculateTotal($(this).closest('.form-group')); 
        });

        // Fonction pour calculer le montant total
        function calculateTotal($entry) {
            var qte_consult = parseFloat($entry.find('.qte_consult').val()) || 0;
            var unitPrice = parseFloat($entry.find('.amount').val()) || 0;
            var totalPrice = qte_consult * unitPrice;
            $entry.find('.net_amount').val(totalPrice.toFixed()); // Arrondir à 2 décimales
            }
        });
    </script>
</div>
