<?php
/* 	
 * 	Tamplate: Add Patient
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<div class="row">
    <div class="col-md-8" style="left: 180px;">
		<div class="panel">
            <header>Nouveau fournisseur</header><br>
             <a href="<?php echo base_url(); ?>magasin/fournisseur" class="btn bg-black btn-wide pull-left"><i class="fa fa-arrow-left"></i> <?php echo get_phrase('retour'); ?></a>
			<div style="clear:both;"></div>						
			<div class="panel-body p-5">
			<form method="post" action="<?php echo base_url(); ?>magasin/fournisseur/create" class="p-5" id="form-validate" enctype="multipart/form-data">
                       
                  <div class="panel-heading">
                    <div class="panel-title" >
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('informations générales'); ?>
                    </div>
                    </div><br>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('nom'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="name13" name="name"  data-validation="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('téléphone'); ?><sup class="color-danger">*</sup></label>
                                <input type="text" class="form-control" id="name13" name="contact"  data-validation="required">
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('adresse'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="adresse"  data-validation="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name13"><?php echo get_phrase('email'); ?><sup class="color-danger"></sup></label>
                                <input type="text" class="form-control" id="name13" name="email"  data-validation="">
                            </div>
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
	</div>				

<script>
    //Obtenez l'élément de formulaire de la date de naissance et d'âge
        var birth_dateInput = document.getElementById("birth_date");
        var ageInput = document.getElementById("age");
        // Ajouter un écouteur d'événements pour la modification de la date de naissance
        birth_dateInput.addEventListener("change", function() {
            // Obtenez la date de naissance à partir de l'élément de formulaire
            var birth_date = new Date(birth_dateInput.value);

            // Obtenez l'année actuelle
            var anneeActuelle = new Date().getFullYear();

            // Calculez l'âge en soustrayant l'année de naissance de l'année actuelle
            var age = anneeActuelle - birth_date.getFullYear();
            // Mettre à jour automatiquement le champ d'âge
            ageInput.value = age;
        });
</script>

<?php 
$output .= ob_get_clean();
echo $output;


