/**
 * patient_manage.js
 * Chemin : assets/js/receptionist/patient_manage.js
 *
 * Module JavaScript partagé pour le module Réceptionniste :
 *  - PatientManage.initDataTable()   : liste des patients avec recherche et
 *                                       pagination effectuées côté serveur
 *                                       (endpoint receptionist/patient_datatable)
 *  - PatientManage.initAutocomplete(): sélection d'un patient par
 *                                       autocomplétion (Select2 + AJAX) dans
 *                                       les formulaires (examen, facture,
 *                                       vente, traitement...)
 *  - PatientManage.bindAjaxForms()   : soumission des formulaires patient
 *                                       (création / modification) en AJAX
 *                                       pour éviter tout rechargement complet
 *
 * Compatibilité : jQuery 1.12+ / 3+, Select2 4.x, DataTables 1.10+
 */
var PatientManage = (function ($) {
    'use strict';

    var _config = {
        datatableUrl:   '',
        autocompleteUrl:'',
        ajaxFormUrl:    '', // base : .../receptionist/patient_ajax/
        editUrlBase:    '', // base : .../modal/popup/edit_patient/
    };

    var _table = null;

    function init(config) {
        $.extend(_config, config);
    }

    // -----------------------------------------------------------------
    // 1) Liste des patients : recherche + pagination côté serveur
    // -----------------------------------------------------------------

    function initDataTable(selector) {
        selector = selector || '#table-patients';
        var $table = $(selector);
        if (!$table.length || !$.fn.DataTable) {
            return null;
        }

        _table = $table.DataTable({
            serverSide: true,
            processing: true,
            searching:  true,
            ordering:   true,
            paging:     true,
            autoWidth:  false,

            ajax: {
                url:  _config.datatableUrl,
                type: 'POST',
                data: function (d) { return d; },
                dataSrc: function (json) {
                    if (json && json.error) {
                        console.error('Liste patients - erreur serveur :', json.error);
                        return [];
                    }
                    return json.data;
                },
                error: function (xhr) {
                    console.error('Liste patients - erreur AJAX :', xhr.status, xhr.responseText);
                }
            },

            columns: [
                { data: null, orderable: false, searchable: false,
                  render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                { data: 'name' },
                { data: 'prenom' },
                { data: 'phone' },
                {
                    data: null, orderable: false, searchable: false, className: 'text-center',
                    render: function (data, type, row) {
                        var editUrl = _config.editUrlBase + row.patient_id;
                        return '<a href="#" class="btn btn-success btn-rounded icon-only" title="Modifier" ' +
                               'onclick="showAjaxModal(\'' + editUrl + '\'); return false;">' +
                               '<i class="fa fa-pencil"></i></a>';
                    }
                }
            ],

            order: [[1, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],

            language: {
                url: '',
                processing:   'Chargement…',
                search:       'Rechercher :',
                searchPlaceholder: 'Nom, prénom, téléphone…',
                lengthMenu:   'Afficher _MENU_ patients',
                info:         'Affichage de _START_ à _END_ sur _TOTAL_ patients',
                infoEmpty:    'Aucun patient trouvé',
                infoFiltered: '(filtrés depuis _MAX_ patients au total)',
                zeroRecords:  'Aucun patient ne correspond à cette recherche',
                emptyTable:   'Aucun patient enregistré',
                paginate: { first: 'Premier', previous: 'Précédent', next: 'Suivant', last: 'Dernier' }
            }
        });

        return _table;
    }

    function reloadDataTable() {
        if (_table) {
            _table.ajax.reload(null, false);
        }
    }

    // -----------------------------------------------------------------
    // 2) Autocomplétion patient (Select2 + AJAX) réutilisable partout
    // -----------------------------------------------------------------

    /**
     * @param {string|jQuery} selector  sélecteur ou objet jQuery du <select>
     * @param {Object} [opts]
     *   opts.placeholder {string}
     */
    function initAutocomplete(selector, opts) {
        opts = opts || {};
        var $el = (selector instanceof $) ? selector : $(selector);
        if (!$el.length || !$.fn.select2) {
            return;
        }

        $el.select2({
            width: '100%',
            placeholder: opts.placeholder || 'Rechercher un patient (nom, prénom, téléphone)',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: function () { return 'Tapez au moins 1 caractère…'; },
                searching: function () { return 'Recherche…'; },
                noResults: function () { return 'Aucun patient trouvé'; }
            },
            ajax: {
                url: _config.autocompleteUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term || '', page: params.page || 1 };
                },
                processResults: function (data) {
                    return data; // déjà au format {results:[...], pagination:{more}}
                },
                cache: true
            }
        });
    }

    /** Initialise automatiquement tous les selects marqués data-patient-autocomplete="1" */
    function autoInitAutocomplete(context) {
        $(context || document).find('[data-patient-autocomplete="1"]').each(function () {
            initAutocomplete($(this), { placeholder: $(this).data('placeholder') });
        });
    }

    // -----------------------------------------------------------------
    // 3) Soumission AJAX des formulaires patient (ajout / modification)
    // -----------------------------------------------------------------

    /**
     * Intercepte les formulaires marqués data-ajax-target="patient" et les
     * soumet en AJAX (FormData, gère l'upload de photo) au lieu de faire un
     * POST classique suivi d'une redirection complète.
     */
    function bindAjaxForms(context) {
        $(context || document).off('submit.patientAjax', 'form[data-ajax-target="patient"]');
        $(context || document).on('submit.patientAjax', 'form[data-ajax-target="patient"]', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            var task = $form.data('task') || 'create';
            var patientId = $form.data('patient-id') || '';
            var url = _config.ajaxFormUrl + task + (patientId ? '/' + patientId : '');

            var originalBtnHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement…');

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function (resp) {
                if (resp && resp.success) {
                    if (window.toastr) {
                        toastr.success(resp.message || 'Enregistré avec succès.');
                    }

                    // Ferme le modal englobant (générique #modal_ajax ou #modal_patient)
                    $form.closest('.modal').modal('hide');

                    // Rafraîchit la liste des patients si elle est présente sur la page
                    reloadDataTable();

                    // Si le formulaire vient d'alimenter un select2 patient
                    // (ex : création rapide depuis un autre écran), on le
                    // signale via un évènement personnalisé.
                    $(document).trigger('patient:saved', [resp.patient]);
                } else {
                    if (window.toastr) {
                        toastr.error((resp && resp.message) || "Une erreur s'est produite.");
                    } else {
                        alert((resp && resp.message) || "Une erreur s'est produite.");
                    }
                }
            }).fail(function (xhr) {
                console.error('Enregistrement patient - erreur AJAX :', xhr.status, xhr.responseText);
                if (window.toastr) {
                    toastr.error('Erreur serveur, veuillez réessayer.');
                } else {
                    alert('Erreur serveur, veuillez réessayer.');
                }
            }).always(function () {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
            });
        });
    }

    return {
        init: init,
        initDataTable: initDataTable,
        reloadDataTable: reloadDataTable,
        initAutocomplete: initAutocomplete,
        autoInitAutocomplete: autoInitAutocomplete,
        bindAjaxForms: bindAjaxForms
    };
})(jQuery);
