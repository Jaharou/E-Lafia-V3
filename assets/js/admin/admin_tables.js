/**
 * Listes de l'administration en mode "server-side DataTables".
 *
 * Une seule implémentation pour les quatre grandes listes (patients,
 * examens, consultations, traitements) : la vue déclare simplement
 *   <table data-admin-list="patient"> ... </table>
 * et ce script se charge du reste.
 *
 * La recherche, le tri et la pagination sont envoyés au serveur
 * (admin/list_datatable/<entite>) : le navigateur ne reçoit qu'une page de
 * résultats à la fois, au lieu de la table entière.
 */
var AdminTables = (function ($) {
    'use strict';

    var _base = '';

    /** Échappe le texte injecté dans le HTML des cellules. */
    function esc(value) {
        if (value === null || typeof value === 'undefined') { return ''; }
        return String(value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function btn(url, cls, icon, title, confirmDelete) {
        if (!url) { return ''; }
        var onclick = confirmDelete
            ? ' onclick="confirm_modal(\'' + url + '\'); return false;" href="#"'
            : ' href="' + url + '"';
        return '<a class="btn ' + cls + ' btn-rounded icon-only" title="' + title + '"' + onclick + '>'
             + '<i class="fa ' + icon + '"></i></a> ';
    }

    /** Colonne "options" commune. */
    function actions(row, opts) {
        var html = '';
        if (opts.view && row.view_url) {
            html += '<a class="btn btn-primary btn-rounded icon-only" title="Aperçu" href="#"'
                 +  ' onclick="showAjaxModal(\'' + row.view_url + '\'); return false;">'
                 +  '<i class="fa fa-bars"></i></a> ';
        }
        html += btn(row.edit_url, 'btn-success', 'fa-pencil', 'Modifier');
        html += btn(row.print_url, 'btn-warning', 'fa-print', 'Impression');
        html += btn(row.delete_url, 'btn-danger', 'fa-trash-o', 'Supprimer', true);
        return html;
    }

    /** Définition des colonnes par entité. */
    var columns = {
        patient: [
            { data: 'id' },
            { data: 'photo', orderable: false, render: function (d) {
                return '<img src="' + esc(d) + '" class="img-circle" width="40" height="40">'; } },
            { data: 'name', render: esc },
            { data: 'phone', render: esc },
            { data: 'email', render: esc },
            { data: null, orderable: false, render: function (d, t, row) {
                return actions(row, { view: true }); } }
        ],
        examen: [
            { data: 'id' },
            { data: 'number', render: esc },
            { data: 'patient', render: esc },
            { data: 'date', render: esc },
            { data: 'amount', className: 'text-right', render: esc },
            { data: 'statut', render: function (d) {
                return '<span class="label ' + statutClass(d) + '">' + esc(d) + '</span>'; } },
            { data: null, orderable: false, render: function (d, t, row) { return actions(row, {}); } }
        ],
        invoice: [
            { data: 'id' },
            { data: 'number', render: esc },
            { data: 'patient', render: esc },
            { data: 'date', render: esc },
            { data: 'amount', className: 'text-right', render: esc },
            { data: 'statut', render: function (d) {
                var cls = (d === 'payé') ? 'label-success' : 'label-warning';
                return '<span class="label ' + cls + '">' + esc(d) + '</span>'; } },
            { data: null, orderable: false, render: function (d, t, row) { return actions(row, {}); } }
        ],
        traitement: [
            { data: 'id' },
            { data: 'number', render: esc },
            { data: 'patient', render: esc },
            { data: 'date', render: esc },
            { data: 'statut', render: esc },
            { data: null, orderable: false, render: function (d, t, row) { return actions(row, {}); } }
        ]
    };

    function statutClass(statut) {
        switch (statut) {
            case 'Validé':
            case 'Terminé':  return 'label-success';
            case 'En cours': return 'label-info';
            case 'Annulé':   return 'label-danger';
            default:         return 'label-warning';
        }
    }

    var french = {
        sProcessing: "Chargement...",
        sLengthMenu: "Afficher _MENU_ entrées",
        sZeroRecords: "Aucun résultat",
        sInfo: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
        sInfoEmpty: "Aucune entrée",
        sInfoFiltered: "(filtré sur _MAX_ entrées au total)",
        sSearch: "Recherche :",
        oPaginate: { sFirst: "Premier", sPrevious: "Précédent", sNext: "Suivant", sLast: "Dernier" }
    };

    return {
        init: function (config) {
            _base = (config && config.baseUrl) ? config.baseUrl : '';
        },

        /** Initialise toutes les tables marquées data-admin-list de la page. */
        initAll: function () {
            $('table[data-admin-list]').each(function () {
                var $table = $(this);
                var entity = $table.data('admin-list');
                if (!columns[entity] || $.fn.DataTable.isDataTable($table)) { return; }

                $table.DataTable({
                    processing: true,
                    serverSide: true,
                    searchDelay: 400,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [[0, 'desc']],
                    language: french,
                    ajax: {
                        url: _base + 'admin/list_datatable/' + entity,
                        type: 'POST',
                        error: function () {
                            if (typeof toastr !== 'undefined') {
                                toastr.error("Impossible de charger la liste.");
                            }
                        }
                    },
                    columns: columns[entity]
                });
            });
        }
    };
})(jQuery);

$(function () { AdminTables.initAll(); });
