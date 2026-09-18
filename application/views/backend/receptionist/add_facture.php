<?php
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
?>
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<?php $output = ''; ?>
<?php ob_start(); ?>
<div class="row">
    <?php $factureNumber = $this->db->count_all('facture')+1; ?>
    <div class="col-md-9" style="left:180px; top: 5px;">
        <div class="panel panel-primary">
            <div style="clear:both;"></div>
            <h4 class="panel mt-n" style="text-align: center; background-color: whitesmoke;"><?php echo get_phrase('nouvelle-facture'); ?></h4>
            <div class="panel-body p-20">
                <form method="post" action="<?php echo base_url(); ?>receptionist/facture/create" class="p-20" id="factureForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('numéro-de-facture'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="facture_number" name="facture_number" value="<?php echo "0".$factureNumber; ?>" readonly >
                            </div>
                            <div>
                                <label for="patient">Sélectionnez un patient:</label>
                                <select class="form-control" id="patient_id" name="patient_id" required
                                        data-patient-autocomplete="1" data-placeholder="Rechercher un patient (nom, prénom, téléphone)">
                                </select>
                            </div>
                            <!-- Champs pour stocker les JSON -->
                            <input name="invoice_entries[]" id="invoice_entries" style="display:none;">
                            <input name="examen_entries[]" id="examen_entries" style="display:none;">
                            <input name="traitement_entries[]" id="traitement_entries" style="display:none;">
                            <input name="invoice_ventes[]" id="invoice_ventes" style="display:none;">

                            <div id="patientDetails">
                                <!-- Les détails des factures et examens seront affichés ici -->
                            </div>
                            <div>
                                <label for="total_amount">Montant Total:</label>
                                <input type="text" class="js-states form-control" id="total_amount" name="total_amount" readonly>
                            </div>
                            <div>
                                <label for="total_amount">%Réduction:</label>
                                <input type="text" class="js-states form-control" id="remise" name="remise">
                            </div>
                            <div>
                                <label for="remise_amount">Montant de la remise:</label>
                                <input type="text" class="js-states form-control" id="remise_amount" name="remise_amount" readonly>
                            </div>
                            <div>
                                <label for="amount_net">Montant net:</label>
                                <input type="text" class="js-states form-control" id="amount_net" name="amount_net" readonly>
                            </div>

                            <div>
                                <label for="status">Statut:</label>
                                <select class="js-states form-control" id="status" name="status" required>
                                    <option value="Non payé">Non payé</option>
                                    <option value="Payé">Payé</option>
                                    <option value="Annulé">Annulé</option>
                                </select>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="btn-group pull-right mt-10" role="group">
                                        <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
                                        <button type="submit" class="btn bg-black btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
    <script type="text/javascript">
$(document).ready(function() {
    $('#patient_id').change(function() {
        var patient_id = $(this).val();

        if (patient_id !== '') {
            $.ajax({
                url: '<?php echo base_url('receptionist/get_patient_details'); ?>',
                method: 'POST',
                data: { patient_id: patient_id },
                success: function(response) {
                    $('#patientDetails').html(response);

                    // Fonction pour calculer le montant total dynamiquement
                    function calculateTotalAmount() {
                        var totalAmount = 0;
                        $('#patientDetails p').each(function() {
                            var content = $(this).text();
                            var amountMatch = content.match(/;\s*\d+\s*;\s*(\d+)\s*FCFA/);
                            if (amountMatch && amountMatch[1]) {
                                totalAmount += parseFloat(amountMatch[1]);
                            }
                        });
                        $('#total_amount').val(totalAmount);
                    }

                    calculateTotalAmount();
                    $('#patientDetails').on('DOMSubtreeModified', function() {
                        calculateTotalAmount();
                    });
                }
            });
        } else {
            $('#patientDetails').html('');
            $('#total_amount').val('');
            $('#amount_net').val('');
            $('#remise_amount').val('');
        }
    });

    $('#remise').on('input', function() {
        calculateDiscountAndNetAmount();
    });

    function calculateDiscountAndNetAmount() {
        var totalAmount = parseFloat($('#total_amount').val()) || 0;
        var remise = parseFloat($('#remise').val()) || 0;
        var remiseAmount = (totalAmount * remise) / 100;
        var netAmount = totalAmount - remiseAmount;
        $('#remise_amount').val(remiseAmount);
        $('#amount_net').val(netAmount);
    }

    $('#factureForm').submit(function (e) {
        var invoiceEntries = [];
        var examenEntries = [];
        var traitementEntries = [];
        var invoiceVentes = [];
        var currentSection = '';

        $('#patientDetails').children().each(function () {
            if ($(this).is('h4')) {
                // Identifier correctement les sections
                var title = $(this).text().trim().toLowerCase();
                if (title.includes("consultation")) {
                    currentSection = 'consultations';
                } else if (title.includes("examen")) {
                    currentSection = 'examens';
                } else if (title.includes("traitement")) {
                    currentSection = 'traitements';
                } else if (title.includes("médicament")) {
                    currentSection = 'médicaments';
                } else {
                    currentSection = ''; // Aucune section valide
                }
            } else if ($(this).is('p')) {
                if (currentSection) {
                    var text = $(this).text().trim();
                    var description = text.split(":")[0].trim();
                    var amountMatch = text.match(/:\s*(\d+)\s*FCFA/);
                    var quantityMatch = text.match(/;\s*(\d+)\s*;/);
                    var amount = amountMatch ? parseFloat(amountMatch[1]) : 0;
                    var quantity = quantityMatch ? parseInt(quantityMatch[1]) : 0;

                    if (amount && quantity) {
                        var item = {
                            description: description,
                            amount: amount.toFixed(),
                            qte: quantity,
                            total: (amount * quantity).toFixed()
                        };

                        switch (currentSection) {
                            case 'consultations':
                                invoiceEntries.push({
                                    description: item.description,
                                    amount: item.amount,
                                    qte_consult: item.qte,
                                    net_amount: item.total
                                });
                                break;
                            case 'examens':
                                examenEntries.push({
                                    description: item.description,
                                    montant: item.amount,
                                    qte_examen: item.qte,
                                    amountT: item.total
                                });
                                break;
                            case 'traitements':
                                traitementEntries.push({
                                    description: item.description,
                                    amount: item.amount,
                                    qte: item.qte,
                                    prixTrait: item.total
                                });
                                break;
                            case 'médicaments':
                                invoiceVentes.push({
                                    produit: item.description,
                                    amount: item.amount,
                                    qte_vente: item.qte,
                                    Ptotal: item.total
                                });
                                break;
                        }
                    }
                }
            }
        });

        // Validation avant soumission
        if (invoiceEntries.length > 0 || examenEntries.length > 0 || traitementEntries.length > 0 || invoiceVentes.length > 0) {
            $('#invoice_entries').val(JSON.stringify(invoiceEntries));
            $('#examen_entries').val(JSON.stringify(examenEntries));
            $('#traitement_entries').val(JSON.stringify(traitementEntries));
            $('#invoice_ventes').val(JSON.stringify(invoiceVentes));
        } else {
            alert("Aucun détail de consultation, d'examen, de traitement ou de vente trouvé.");
            e.preventDefault();
        }
    });
});
</script>
</div>

<?php 
$output .= ob_get_clean();
echo $output;
?>

<script>
// Initialisation explicite du champ patient (Select2 AJAX)
// nécessaire car ce formulaire est rendu après includes_bottom.php
$(document).ready(function () {
    if (typeof PatientManage !== 'undefined') {
        PatientManage.autoInitAutocomplete();
    } else if ($.fn.select2) {
        // Fallback si PatientManage n'est pas disponible
        $('#patient_id').select2({
            width: '100%',
            placeholder: 'Rechercher un patient (nom, prénom, téléphone)',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: function () { return 'Tapez au moins 1 caractère…'; },
                searching: function () { return 'Recherche…'; },
                noResults: function () { return 'Aucun patient trouvé'; }
            },
            ajax: {
                url: '<?php echo base_url("receptionist/patient_search"); ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term || '', page: params.page || 1 }; },
                processResults: function (data) { return data; },
                cache: true
            }
        });
    }
});
</script>
