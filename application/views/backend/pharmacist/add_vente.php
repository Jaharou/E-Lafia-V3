<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<div class="row">
    <?php $venteNumber = $this->db->count_all('vente')+1; ?>
    <div class="col-md-8" style="left: 240px;">
        <div class="panel panel-primary" data-collapsed="0">
            <a href="<?php echo base_url(); ?>pharmacist/vente" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>
            <h5 class="panel mt-n" style="text-align: center; color: black;"><?php echo get_phrase('nouvelle-vente'); ?></h5>
            <div class="panel-body p-20">
                <form method="post" action="<?php echo base_url(); ?>pharmacist/vente/create" class="p-10" id="form-validate" enctype="multipart/form-data">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('numéro-de-vente'); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="vente_number" name="vente_number"  data-validation="" value="<?php
                    echo "0".$venteNumber; ?>" readonly >
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
                    <label for="name13"><?php echo get_phrase('produit'); ?></label>
                    <select name="produit[]" class="form-control select2" id="stock_id">
                        <optgroup>
                            <option data-price="">Sélectionner un produit</option>
                            <?php
                            // Requête pour obtenir les informations nécessaires en joignant les tables
                            $this->db->select('stock.medicine_id, stock.prix, medicine.name');
                            $this->db->from('stock');
                            $this->db->where("movement_type = 'sortie'");
                            $this->db->join('medicine', 'medicine.medicine_id = stock.medicine_id');
                            $stocks = $this->db->get()->result_array();
                            $stocks = ($stocks); // Inverser l'ordre des produits
                            foreach ($stocks as $row2): ?>
                            <option value="<?php echo $row2['medicine_id']; ?>" data-price="<?php echo $row2['prix']; ?>">
                                <?php echo $row2['name']; ?>
                            </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('prix-unitaire'); ?></label>
                    <input type="number" class="form-control" id="pu" name="amount[]" readonly>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                var stockSelect = document.getElementById('stock_id');
                var priceInput = document.getElementById('pu');
                stockSelect.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var price = selectedOption.getAttribute('data-price');
                priceInput.value = price || ''; // Met à jour le champ de prix unitaire
                 });
                });
            </script>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name13"><?php echo get_phrase('quantité'); ?></label>
                    <input type="text" class="form-control" id="quantite" name="qte_vente[]">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="amount_consult"><?php echo get_phrase('montant-total'); ?><sup class="color-danger"></sup></label>
                    <input type="text" class="form-control" id="prixT" name="Ptotal[]" value="0" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date"><?php echo get_phrase('date-de-vente'); ?><sup class="color-danger">*</sup></label>
                    <input type="text" class="form-control" id="date_vente" name="date_vente"  data-validation="required" value="<?php 
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
                            $receptionistes = $this->db->get('pharmacist')->result_array();
                            $receptionistes = ($receptionistes); // Inverser l'ordre des receptionistes
                            foreach ($receptionistes as $row2):
                                ?>
                           <option value="<?php echo $row2['name']; ?>">
                            <?php echo $row2['name']; ?>
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
        $(document).on('change', '.libelle', function () {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            $(this).closest('.vente_entry').find('.prix').val(price ? price : '');
        });
    });

    // Récupérer les éléments du DOM de ventes
    var puInput = document.getElementById("pu");
    var quantiteInput = document.getElementById("quantite");
    //var priseInput = document.getElementById("pourcentage_prise");
    var prixTInput = document.getElementById("prixT");
    // Écouter les modifications des entrées de montant et remise
    puInput.addEventListener("input", calculerPrixT);
    quantiteInput.addEventListener("input", calculerPrixT);
    //priseInput.addEventListener("input", calculerMontantNet);

    function calculerPrixT() {
      // Récupérer les valeurs entrées
      var pu = parseFloat(puInput.value);
      var quantite = parseFloat(quantiteInput.value);
      //var prise = parseFloat(priseInput.value);

      // Calculer le montant net
      var prixT = (pu * quantite);

      // Afficher le montant net arrondi à 2 décimales
      prixTInput.value = prixT.toFixed(0);
    }

    // Récupérer les éléments du DOM
    /*var montantInput = document.getElementById("entry_amount");
    var remiseInput = document.getElementById("discount_amount");
    var priseInput = document.getElementById("pourcentage_prise");
    var montantNetInput = document.getElementById("net_amount");
    var avanceInput = document.getElementById("avance");
    var montantResteInput = document.getElementById("reste_payer");
    // Écouter les modifications des entrées de montant et remise
    avanceInput.addEventListener("input", calculerMontantReste);
    montantNetInput.addEventListener("input", calculerMontantReste);*/

    function calculerMontantReste() {
      // Récupérer les valeurs entrées
      var montantNet = parseFloat(montantNetInput.value);
      var avance = parseFloat(avanceInput.value);

      // Calculer le montant net
      var montantReste = montantNet - avance;

      // Afficher le montant net arrondi à 2 décimales
      montantResteInput.value = montantReste.toFixed(0);
    }
    </script>

</div>
