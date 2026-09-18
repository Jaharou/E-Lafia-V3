<div class="row">
    <div class="col-md-8" style="margin-top: 0px;left: 220px;">
        <div class="panel">
            <a href="<?php echo base_url(); ?>admin/test" class="btn btn-info btn-wide pull-left"><i class="fa fa-arrow-left"></i><?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>                     
            <div class="panel-body p-25">
            <form method="post" action="<?php echo base_url(); ?>admin/test/create" class="p-25" id="form-validate" enctype="multipart/form-data">   
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="libelle"><?php echo get_phrase('libellé'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="js-states form-control" id="libelle_examen" name="libelle_examen" data-validation="required" placeholder="libelle">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cate"><?php echo get_phrase('catégorie'); ?></label>
                                <select name="categorie_id" class="js-states form-control" id="categorie_id"  data-validation="">
                                    <optgroup>
                                    <option></option>  
                                <?php
                                $categories = $this->db->get('categorie_test')->result_array();
                                foreach ($categories as $row2):
                                ?>
                                <option value="<?php echo $row2['categorie_id'];   ?>">
                                <?php echo $row2['libelle_categ']; ?>
                                </option>
                                <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit"><?php echo get_phrase('unité-de-mesure'); ?></label>
                                <input type="text" class="js-states form-control" id="unite" name="unite_mesure" data-validation="" placeholder="unité">
                            </div>
                        </div>      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount_test"><?php echo get_phrase('prix'); ?></label>
                                <input type="text" class="js-states form-control" id="amount" name="amount" data-validation="" placeholder="Prix">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group pull-right mt-10" role="group">
                                <button type="reset" class="btn btn-gray btn-wide"><i class="fa fa-times"></i>Annuler</button>
                                <button type="submit" class="btn btn-info btn-wide"><i class="fa fa-arrow-right"></i>Sauvegarder</button>
                            </div>                
                        </div>
                    </div>
                 </form>    
            </div>              
        </div>              
    </div>              
</div>