<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<div class="row">
    <?php $traitementNumber = $this->db->count_all('traitement')+1; ?>
    <div class="col-md-8" style="left: 240px;">
        <div class="panel panel-primary" data-collapsed="0">
            <a href="<?php echo base_url(); ?>admin/traitement" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>
            <h5 class="panel mt-n" style="text-align: center; color: black;"><?php echo get_phrase('nouveau-traitement'); ?></h5>
            <div class="panel-body p-20">
                <form method="post" action="<?php echo base_url(); ?>admin/traitement/create" class="p-10" id="form-validate" enctype="multipart/form-data">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('numéro'); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="traitement_number" name="traitement_number"  data-validation="" value="<?php
                    echo "0".$traitementNumber; ?>" readonly >
                </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="patient_id"><?php echo get_phrase('patient'); ?><sup class="color-danger">*</sup></label>
                    <select name="patient_id" class="form-control select2" id="js-states" data-validation="required">
                           <optgroup>
                            <option>Sélectionner le patient</option>
                            <?php
                            $patients = $this->db->get('patient')->result_array();
                            $patients = array_reverse($patients); // Inverser l'ordre des patients
                            foreach ($patients as $row2):
                                ?>
                           <option value="<?php echo $row2['patient_id']; ?>">
                            <?php echo $row2['name'].' '.$row2['prenom']; ?>
                        </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="produit_id"><?php echo get_phrase('prestation'); ?><sup class="color-danger">*</sup></label>
                        <select name="libelle_prod[]" class="form-control select2" id="libelle_prod" data-validation="required">
                       <optgroup>
                        <option data-price="">Sélectionner le produit</option>
                        <?php
                        $produits = $this->db->get('produit')->result_array();
                        $produits = array_reverse($produits); // Inverser l'ordre des produits
                        foreach ($produits as $row2):
                            ?>
                       <option value="<?php echo $row2['libelle_prod']; ?>"
                         data-price="<?php echo $row2['amount_prod']; ?>">
                        <?php echo $row2['libelle_prod']; ?>
                       </option>
                      <?php endforeach; ?>
                        </optgroup>
                        </select>
                    </div>
                </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('prix-unitaire'); ?></label>
                    <input type="text" class="form-control" id="amount_prod" name="amount_prod[]" >
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                var libelleSelect = document.getElementById('libelle_prod');
                var amountInput = document.getElementById('amount_prod');
                libelleSelect.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var amount = selectedOption.getAttribute('data-price');
                amountInput.value = amount || ''; // Met à jour le champ de prix unitaire
                });
                });
            </script>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('quantité'); ?></label>
                    <input type="text" class="form-control" id="quantite" name="quantite[]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="amount_consult"><?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="prixTrait" name="prixTrait[]" value="0" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date"><?php echo get_phrase('date-de-traitement'); ?><sup class="color-danger">*</sup></label>
                    <input type="text" class="form-control" id="date_traitement" name="date_traitement"  data-validation="required" value="<?php 
                    date_default_timezone_set('Africa/Niamey'); 
                    echo date("d-m-Y H:i:s"); ?>" >
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('% remise'); ?><sup class="color-danger"></sup></label>
                    <input type="number" class="form-control" id="discount_amount" name="discount_amount" value="0"  data-validation="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('prise-en-charge'); ?></label>
                    <input type="text" class="form-control" id="discount_amount" name="prise_en_charge" data-validation="" placeholder="Veillez saisir le nom de la personne" title ="Veillez saisir le nom de la personne physique ou morale">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('% prise-en-charge'); ?><sup class="color-danger"></sup></label>
                    <input type="number" class="form-control" id="pourcentage_prise" name="pourcentage_prise" value="0"  data-validation="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('mode-paiement'); ?></label>
                    <select class="form-control" id="js-states" name="mode-paiement">
                        <optgroup>
                        <option value="Espèce"><?php echo get_phrase('Espèce'); ?></option>
                        <option value="Chèque"><?php echo get_phrase('Chèque'); ?></option>
                        <option value="Carte-bancaire"><?php echo get_phrase('Carte-bancaire'); ?></option>
                        <option value="Virement"><?php echo get_phrase('Virement'); ?></option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="statuT"><?php echo get_phrase('statut'); ?><sup class="color-danger"></sup></label>
                    <select class="form-control" id="status" name="status" data-validation="">
                     <optgroup>
                      <option value="payé"><?php echo get_phrase('payé'); ?></option>
                        <option value="impayé"><?php echo get_phrase('impayé'); ?></option>
                     </optgroup>
                    </select>  
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                <label for="receptionist_id"><?php echo get_phrase('utilisteur'); ?></label>
                <select name="receptionist_id" class="form-control select2" id="js-states" data-validation="">
                    <optgroup>
                    <option>Sélectionner votre nom</option>
                    <?php
                    $receptionistes = $this->db->get('receptionist')->result_array();
                    $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
                    foreach ($receptionistes as $row2):
                        ?>
                   <option value="<?php echo $row2['receptionist_name']; ?>">
                    <?php echo $row2['receptionist_name']; ?>
                    </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
                </div>
            </div> 
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
    // Ensure the DOM is fully loaded before accessing elements
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listener to dynamically added elements
        $(document).on('change', '.libelle_prod', function () {
            const selectedOption = this.options[this.selectedIndex];
            const amount_prod = selectedOption.getAttribute('data-price');
            $(this).closest('.traitement_entry').find('.amount').val(amount_prod ? amount_prod : '');
        });
    });

    // Récupérer les éléments du DOM de ventes
    var amountInput = document.getElementById("amount_prod");
    var quantiteInput = document.getElementById("quantite");
    //var priseInput = document.getElementById("pourcentage_prise");
    var prixTraitInput = document.getElementById("prixTrait");
    // Écouter les modifications des entrées de montant et remise
    amountInput.addEventListener("input", calculerPrixTrait);
    quantiteInput.addEventListener("input", calculerPrixTrait);
    //priseInput.addEventListener("input", calculerMontantNet);

    function calculerPrixTrait() {
      // Récupérer les valeurs entrées
      var amount_prod = parseFloat(amountInput.value);
      var quantite = parseFloat(quantiteInput.value);
      //var prise = parseFloat(priseInput.value);

      // Calculer le montant net
      var prixTrait = (amount_prod * quantite);

      // Afficher le montant net arrondi à 2 décimales
      prixTraitInput.value = prixTrait.toFixed(0);
    }

    </script>

</div>
