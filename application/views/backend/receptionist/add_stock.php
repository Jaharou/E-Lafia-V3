<?php $stock_info = $this->db->get('stock')->result_array(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary" >
                <div class="panel-heading">
                    <div class="panel-title">
                        <h3 style="margin-left: 400px;"><?php echo get_phrase('nouveau-stock'); ?></h3>
                    </div>
                </div>
            <div class="panel-body">

                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>receptionist/stock/create" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('produit'); ?><sup class="color-danger">*</sup></label>
                        <div class="col-sm-9">
                       <select name="medicine_id" class="form-control select2" id="medicine_id" data-validation="required">
                       <optgroup>
                        <option value="" data-price="">Sélectionner le produit</option>
                        <?php
                        $medicines = $this->db->get('medicine')->result_array();
                        $medicines = array_reverse($medicines); // Inverser l'ordre des medicines
                        foreach ($medicines as $row2):
                            ?>
                       <option value="<?php echo $row2['medicine_id']; ?>" data-price="<?php echo $row2['price']; ?>">
                        <?php echo $row2['name']; ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>                 
        </div>
    </div>
    <div class="form-group">
        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('prix-unitaire'); ?></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="prix" name="prix">
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var medicineSelect = document.getElementById('medicine_id');
        var priceInput = document.getElementById('prix');

        medicineSelect.addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        priceInput.value = price || ''; // Met à jour le champ de prix unitaire
         });
        });
    </script>
     <div class="form-group">
        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('quantité-à-stocker'); ?><sup class="color-danger">*</sup></label>
        <div class="col-sm-9">
        <input type="number" class="form-control" id="field-1" name="qte_produit" >
        </div>
    </div>
    <div class="form-group">
        <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('type-de-mouvement'); ?><sup class="color-danger">*</sup></label>
        <div class="col-sm-9">
            <select class="js-states form-control" id="movement_type" name="movement_type">
                <option value="">Sélectionner un type</option>
                <option value="entrée">Entrée</option>
                <option value="sortie">Sortie</option>
            </select>
        </div>
    </div>                
    <div class="form-group">
        <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('date'); ?></label>
        <div class="col-sm-9">
            <input type="date" class="form-control" id="field-1" name="date_liv" value="<?php echo date('d-M-Y') ?>">
        </div>
    </div>               
    <div class="col-sm-3 control-label col-sm-offset-3">
        <input type="submit" class="btn btn-success" value="Valider">
    </div>
</form>
</div>
</div>
</div>
</div>