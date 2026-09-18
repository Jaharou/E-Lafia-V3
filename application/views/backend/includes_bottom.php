<?php
/* 	
 * 	Tamplate: Basic Script
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
  		<?php //COMMON JS FILES ?>
        <?php // jQuery est deja charge une fois dans includes_top.php (head). ?>
        <script src="<?php echo base_url(); ?>assets/js/jquery-ui/jquery-ui.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/wysihtml5-0.4.0pre.min.js"></script>
         <script src="<?php echo base_url(); ?>assets/js/bootstrap3-wysihtml5.js"></script>
        
        <script src="<?php echo base_url(); ?>assets/js/pace/pace.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lobipanel/lobipanel.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/iscroll/iscroll.js"></script>
      	<?php //PAGE JS FILES ?>		
        <script src="<?php echo base_url(); ?>assets/js/prism/prism.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/waypoint/waypoints.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/counterUp/jquery.counterup.min.js"></script>
        
        <script src="<?php echo base_url(); ?>assets/js/amcharts/amcharts.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/amcharts/serial.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/amcharts/pie.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/amcharts/plugins/animate/animate.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/amcharts/plugins/export/export.min.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/amcharts/plugins/export/export.css" type="text/css" media="all" />
        <script src="<?php echo base_url(); ?>assets/js/amcharts/themes/light.js"></script>

        <script src="<?php echo base_url(); ?>assets/js/toastr/toastr.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/icheck/icheck.min.js"></script>	     
        <script src="<?php echo base_url(); ?>assets/js/DataTables/datatables.min.js"></script>	
		<?php //THEME JS ?>
        <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/production-chart.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/traffic-chart.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/task-list.js"></script>	
        <script src="<?php echo base_url(); ?>assets/js/form-validator/jquery.form-validator.min.js"></script> 
         <script src="<?php echo base_url(); ?>assets/js/select2/select2.min.js"></script> 


<script>
    function printRecu() {
            var content = document.getElementById('ordonnancePatient').innerHTML;

            var a = window.open('','', 'height=500', 'width=500');
            a.document.write('<html>');
            a.document.write('<body>');
            a.document.write('<title> Ordonnance');
            a.document.write('</title>');
            a.document.write(content);
            a.document.write('</body>');
            a.document.write('</html>');
            a.document.close();
            a.print();
            //a.close();

        }
    
     $(function($) {
        $.validate({
            form : '#form-validate',
            modules : 'security'
        });
    });
      $(function($) {
        $.validate({
            form : '#form2-validate',
            modules : 'security'
        });
    });
    $(function($) {
        $(".js-states").select2();      
    });
</script>  
<?php if ($account_type === 'admin'): ?>
<script src="<?php echo base_url(); ?>assets/js/admin/admin_tables.js"></script>
<script>
    AdminTables.init({ baseUrl: '<?php echo base_url(); ?>' });
</script>
<?php endif; ?>

<?php if ($account_type === 'receptionist' || $account_type === 'laboratorist'): ?>
<script src="<?php echo base_url(); ?>assets/js/receptionist/patient_manage.js"></script>
<script>
    PatientManage.init({
        datatableUrl:    '<?php echo base_url($account_type . '/patient_datatable'); ?>',
        autocompleteUrl: '<?php echo base_url($account_type . '/patient_search'); ?>',
        ajaxFormUrl:     '<?php echo base_url($account_type . '/patient_ajax/'); ?>',
        editUrlBase:     '<?php echo base_url('modal/popup/edit_patient/'); ?>'
    });
    $(function () {
        // Autocomplétion patient : fonctionne sur toute la page (examen,
        // facture, vente, traitement...) grâce à l'attribut data-patient-autocomplete.
        PatientManage.autoInitAutocomplete();
        // Soumission AJAX des formulaires patient (création / modification),
        // y compris ceux chargés dynamiquement dans une fenêtre modale.
        PatientManage.bindAjaxForms();
        <?php if (isset($page_name) && $page_name === 'manage_patient'): ?>
        PatientManage.initDataTable('#table-patients');
        <?php endif; ?>
    });
</script>
<?php endif; ?>

<script>
    function addPrestation() {
        var description = $('#entry_description').val();
            var amount = $('#entry_amount2').val();
            console.log(description, amount, 'test');
            if (description === "" || amount === "") {
                alert("Veuillez remplir tous les champs.");
                return;
            }

            var formData = new FormData();
            formData.append('entry_description', description);
            formData.append('entry_amount', amount);

            $.ajax({
                url: '<?php echo base_url("invoice/add_invoice_entry"); ?>', // Chemin correct pour la méthode de contrôleur
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#invoice_entry_temp').append(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
    }
    $(document).ready(function() {
        $('#add_invoice_entry').click(function() {
            var description = $('#entry_description').val();
            var amount = $('#entry_amount').val();
            console.log(description, amount);
            if (description === "" || amount === "") {
                alert("Veuillez remplir tous les champs.");
                return;
            }

            var formData = new FormData();
            formData.append('entry_description', description);
            formData.append('entry_amount', amount);

            $.ajax({
                url: '<?php echo base_url("invoice/add_invoice_entry"); ?>', // Chemin correct pour la méthode de contrôleur
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#invoice_entry_temp').append(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script> 
 <script>
    $(function($) {
        $('#example').DataTable();
        $('#example-two').DataTable({
    dom: 'Bfrtip', // Ajoute la zone pour les boutons
    buttons: [
        {
            extend: 'print',
            text: 'Imprimer',
            title: '', // Désactiver le titre par défaut
            customize: function (win) {
                // Ajouter le logo et l'entête personnalisée
                $(win.document.body).prepend(`
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="<?php echo base_url(); ?>uploads/logo.jpg" alt="Logo" style="height: 50px; margin-bottom: 10px;">
                        <h3>Clinique Gamadadi</h3>
                        <p>Adresse : Niamey, Banifandou II rond-point Salou Djibo </p>
                        <p>Téléphone : +227 90480045/89050560 | Email : gamadadi@gmail.com | NIF: 28606/P</p>
                    </div>
                `);

                // Style des éléments
                $(win.document.body).css('font-size', '10pt');
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');

                 // Ajouter le total des montants au pied du tableau imprimé
                let footer = $('#example-two tfoot').html(); // Récupère le pied de table actuel
                $(win.document.body).find('table').append(`<tfoot>${footer}</tfoot>`);

                // Styliser la table
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');
            }
        }
    ],
    footerCallback: function (row, data, start, end, display) {
    // API DataTables
    let api = this.api();

    // Calculer la somme de la colonne index 5
    let total = api
        .column(5, { page: 'current' }) // 'current' pour la page visible uniquement
        .data()
        .reduce(function (a, b) {
            return parseFloat(a || 0) + parseFloat(b || 0); // Évite les NaN
        }, 0);

    // Mettre à jour la cellule correspondante dans le pied de table
    $(api.column(5).footer()).html(total.toFixed(0) + ' FCFA');
    },

        // Autres options DataTables ici
    });

        $('#example-three').DataTable({
    dom: 'Bfrtip', // Ajoute la zone pour les boutons
    buttons: [
        {
            extend: 'print',
            text: 'Imprimer',
            title: '', // Désactiver le titre par défaut
            customize: function (win) {
                // Ajouter le logo et l'entête personnalisée
                $(win.document.body).prepend(`
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="<?php echo base_url(); ?>uploads/logo.jpg" alt="Logo" style="height: 50px; margin-bottom: 10px;">
                        <h3>Clinique Gamadadi</h3>
                        <p>Adresse : Niamey, Banifandou II rond-point Salou Djibo </p>
                        <p>Téléphone : +227 90480045/89050560 | Email : gamadadi@gmail.com | NIF: 28606/P</p>
                    </div>
                `);

                // Style des éléments
                $(win.document.body).css('font-size', '10pt');
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');

                 // Ajouter le total des montants au pied du tableau imprimé
                let footer = $('#example-three tfoot').html(); // Récupère le pied de table actuel
                $(win.document.body).find('table').append(`<tfoot>${footer}</tfoot>`);

                // Styliser la table
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');
                }
            }
        ],
        
        footerCallback: function (row, data, start, end, display) {
        // API DataTables
        let api = this.api();

        // Calculer la somme de la colonne index 5
        let total = api
            .column(7, { page: 'current' }) // 'current' pour la page visible uniquement
            .data()
            .reduce(function (a, b) {
                return parseFloat(a || 0) + parseFloat(b || 0); // Évite les NaN
            }, 0);

        // Mettre à jour la cellule correspondante dans le pied de table
        $(api.column(7).footer()).html(total.toFixed(0) + ' FCFA');
        },

            // Autres options DataTables ici
        });

            $('#example-four').DataTable();
        });

        $(document).ready(function () {
        
    });

</script>
<?php if (isset($page_name) && $page_name === 'historique_paiement'): ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/paiements/historique_paiement.css">
<script src="<?php echo base_url(); ?>assets/js/paiements/historique_paiement.js"></script>
<script>
    HistoriquePaiements.init({
        printUrl: '<?php echo base_url('admin/payment_history_print'); ?>' ,
      ajaxUrl: '<?php echo base_url('admin/payment_history_ajax'); ?>',
        csrfToken: '<?php echo $this->security->get_csrf_hash(); ?>',
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>'
    });
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('message') != ""):?>
<script type="text/javascript">
     toastr.success("<?php echo $this->session->flashdata("message");?>");
</script>
<?php endif;?>

<?php if ($this->session->flashdata('error') != ""):?>
<script type="text/javascript">
     toastr.error("<?php echo $this->session->flashdata("error");?>");
</script>
<?php endif;?>