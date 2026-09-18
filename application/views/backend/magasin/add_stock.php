
<head>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css" media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.css"/>
    <script src="<?php echo base_url(); ?>assets/js/jquery/jquery-2.2.4.min.js"></script>
</head>
<?php 
$stock_info = $this->db->get('stock')->result_array(); 
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel-primary">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3 style="margin-left: 400px;"><?php echo get_phrase('nouveau-stock'); ?></h3>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>magasin/stock/create" method="post" enctype="multipart/form-data">
                    
                    <!-- Sélection du produit -->
                    <div class="form-group">
                        <label for="medicine_id" class="col-sm-3 control-label">
                            <?php echo get_phrase('produit'); ?><sup class="color-danger">*</sup>
                        </label>
                        <div class="col-sm-9">
                            <select name="medicine_id" class="form-control select2" id="medicine_id" data-validation="required">
                                <option value="" data-price="">Sélectionner le produit</option>
                                <?php
                                $medicines = $this->db->get('medicine')->result_array();
                                foreach (array_reverse($medicines) as $row2): 
                                ?>
                                    <option value="<?php echo $row2['medicine_id']; ?>" data-price="<?php echo $row2['price']; ?>">
                                        <?php echo $row2['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>                 
                        </div>
                    </div>
                    
                    <!-- Champ de prix unitaire -->
                    <div class="form-group">
                        <label for="prix" class="col-sm-3 control-label">
                            <?php echo get_phrase('prix-unitaire'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="prix" name="prix" >
                        </div>
                    </div>

                    <!-- Script pour afficher le prix unitaire avec des logs de débogage -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var medicineSelect = document.getElementById('medicine_id');
                            var priceInput = document.getElementById('prix');

                            medicineSelect.addEventListener('change', function() {
                                var selectedOption = this.options[this.selectedIndex];
                                var price = selectedOption.getAttribute('data-price');
                                
                                if (price) {
                                    console.log("Prix trouvé :", price); // Affiche le prix dans la console pour vérification
                                    priceInput.value = price;
                                } else {
                                    console.warn("Prix non trouvé pour cette option."); // Message si aucun prix trouvé
                                    priceInput.value = ''; // Remet à zéro le champ de prix
                                }
                            });
                        });
                    </script>

                    <!-- Champ de quantité à stocker -->
                    <div class="form-group">
                        <label for="qte_produit" class="col-sm-3 control-label">
                            <?php echo get_phrase('quantité-à-stocker'); ?><sup class="color-danger">*</sup>
                        </label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="qte_produit" name="qte_produit" required>
                        </div>
                    </div>

                    <!-- Sélection du type de mouvement -->
                    <div class="form-group">
                        <label for="movement_type" class="col-sm-3 control-label">
                            <?php echo get_phrase('type-de-mouvement'); ?><sup class="color-danger">*</sup>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control" id="movement_type" name="movement_type" required>
                                <option value="sortie">Sortie</option>
                                <option value="entrée">Entrée</option>
                            </select>
                        </div>
                    </div>

                    <!-- Champ de date de livraison -->
                    <div class="form-group">
                        <label for="date_liv" class="col-sm-3 control-label">
                            <?php echo get_phrase('date'); ?>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="date_liv" name="date_liv" value="<?php echo date('d-m-Y'); ?>" required>
                        </div>
                    </div>
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
</div>
