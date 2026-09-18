<?php $medicine_category_info = $this->db->get('medicine_category')->result_array(); ?>
<div class="row">
    <div class="col-sm-12">

        <div class="panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <h3><?php echo get_phrase('nouveau-produit'); ?></h3>
                </div>
            </div>
            <div class="panel-body">

                <form role="form" class="form-horizontal form-groups-bordered" action="<?php echo base_url(); ?>pharmacist/medicine/create" method="post" enctype="multipart/form-data">
                <div class="col-sm-3">
                    <label for="js-states" class="col-sm-3 control-label"><?php echo get_phrase('fournisseur'); ?></label>
               <select name="fournisseur_id" class="form-control select2" id="js-states" data-validation="">
               <optgroup>
                <option>Sélectionner le fournisseur</option>
                <?php
                $fournisseurs = $this->db->get('fournisseur')->result_array();
                $fournisseurs = array_reverse($fournisseurs); // Inverser l'ordre des fournisseurs
                foreach ($fournisseurs as $row2):
                    ?>
               <option value="<?php echo $row2['fournisseur_id']; ?>">
                <?php echo $row2['name']; ?>
                    </option>
                    <?php endforeach; ?>
                </optgroup>
                </select>
            </div>
            <div class="col-sm-3">     
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('produit'); ?><sup class="color-danger">*</sup></label>
                <input type="text" class="form-control" id="field-1" name="name" required>
            </div>
            <div class="col-sm-3"> 
                <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('catégorie'); ?></label>
                    <input type="text" class="form-control" id="medicine_category" name="medicine_category" >
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('DCI'); ?></label>
                <input type="text" name="dci" class="form-control" id="field-1" >
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('dosage'); ?></label>
                <input type="text" name="dosage" class="form-control" id="field-1" >
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('condition'); ?></label>
                    <input type="text" name="condit" class="form-control" id="field-1" >
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('forme'); ?></label>
                    <input type="text" name="forme" class="form-control" id="field-1" >
            </div>
            
            <div class="col-sm-3">
                <label for="date_com" class="control-label">
                    <?php echo get_phrase('date-de-commande'); ?>
                </label>
                <input type="text" class="form-control" id="date_commande" name="date_commande" value="<?php echo date('d-m-Y'); ?>" required>
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="control-label"><?php echo get_phrase('quantité-commandée'); ?></label>
                <input type="text" name="qte_prod" class="form-control" id="qte_prod" >
            </div>
            <div class="col-sm-3">
                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('montant'); ?></label>
                <input type="number" name="amount" class="form-control" id="amount" >
            </div>
            <div class="col-sm-6">
                <label for="field-ta" class="col-sm-3 control-label"><?php echo get_phrase('statut'); ?></label>
                <select name="status" class="form-control">
                    <option value=""><?php echo get_phrase('sélectionner-un-statut'); ?></option>
                   <option value="Disponible"><?php echo get_phrase('disponible'); ?></option>
                   <option value="Indisponible"><?php echo get_phrase('indisponible'); ?></option>
                </select>
            </div>
            <!--<div class="col-sm-3">
                <label for="field-1" class="control-label"></?php echo get_phrase("date-d'expiration"); ?></label>
                <input type="date" name="expiration_date" class="form-control" id="expiration_date" >
            </div>-->
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