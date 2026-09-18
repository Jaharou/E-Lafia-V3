<div class="row">
    <div class="col-md-8" style="margin-top: 20px;left: 220px;">
        <div class="panel">
            <a href="<?php echo base_url(); ?>admin/designation" class="btn btn-info btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
            <div style="clear:both;"></div>                     
            <div class="panel-body p-20">
            <form method="post" action="<?php echo base_url(); ?>admin/designation/create" class="p-20" id="form-validate" enctype="multipart/form-data">   
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="libelle"><?php echo get_phrase('libellé'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="libelle" name="libelle" data-validation="required" placeholder="libelle">
                            </div>
                        </div>
                            
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prix"><?php echo get_phrase('prix'); ?></label>
                                <input type="text" class="form-control" id="amount" name="amount" data-validation="" placeholder="Prix">
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