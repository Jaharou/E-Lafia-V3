/**
 * historique_paiement.js
 * Chemin : assets/js/paiements/historique_paiement.js
 *
 * Module JavaScript pour la page Historique des paiements.
 * - DataTables Server-Side Processing
 * - Filtres de période + dates personnalisées
 * - Mise à jour des statistiques (totaux sur l'ensemble des résultats filtrés)
 * - Formatage FCFA (Intl.NumberFormat)
 *
 * Compatibilité : jQuery 1.12+ / jQuery 3+, DataTables 1.10+
 * Pas de fonctionnalités ES6+ incompatibles avec les navigateurs cibles.
 */
var HistoriquePaiements = (function ($) {
  'use strict';

  // -----------------------------------------------------------------------
  // État interne du module
  // -----------------------------------------------------------------------
  var _config = {
    ajaxUrl:   '',
    printUrl:  '',
    csrfToken: '',
    csrfName:  ''
  };

  var _table      = null;   // Instance DataTables
  var _isReady    = false;  // DataTables initialisé ?

  // Formatteur monétaire FCFA
  var _formatter = new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });

  // -----------------------------------------------------------------------
  // Initialisation publique
  // -----------------------------------------------------------------------

  /**
   * Point d'entrée — appeler depuis la vue après chargement du DOM.
   *
   * @param {Object} config  { ajaxUrl, csrfToken, csrfName }
   */
  function init(config) {
    $.extend(_config, config);
    $(document).ready(function () {
      _initDatePicker();
      _initFilters();
      _initDataTable();
      _initPrintButtons();
    });
  }

  // -----------------------------------------------------------------------
  // DataTables
  // -----------------------------------------------------------------------

  function _initDataTable() {
    _table = $('#table-paiements').DataTable({
      // --- Mode Server-Side ---
      serverSide:  true,
      processing:  true,
      searching:   true,
      ordering:    true,
      paging:      true,
      autoWidth:   false,
      responsive:  true,

      // --- Source AJAX ---
      ajax: {
        url:  _config.ajaxUrl,
        type: 'POST',
        data: function (d) {
          // Paramètres supplémentaires ajoutés à chaque requête
          d.periode     = $('#select-periode').val()     || '';
          d.date_debut  = $('#input-date-debut').val()   || '';
          d.date_fin    = $('#input-date-fin').val()     || '';

          // CSRF CodeIgniter
          if (_config.csrfName) {
            d[_config.csrfName] = _config.csrfToken;
          }

          return d;
        },
        dataSrc: function (json) {
          if (json && json.error) {
            _showError('Erreur serveur : ' + json.error);
            console.error('Historique paiements - erreur serveur:', json.error);
            return [];
          }
          // Mise à jour des statistiques dès réception de la réponse
          _updateStatistics(json.statistics, json.recordsFiltered);
          _updateCsrfToken(json);
          return json.data;
        },
        error: function (xhr, error, thrown) {
          var detail = '';
          if (xhr && xhr.responseText) { detail = xhr.responseText.substring(0, 1200); }
          console.error('Historique paiements AJAX:', xhr.status, detail);
          _showError('Erreur AJAX ' + xhr.status + ' : ' + (detail || thrown || error));
        }
      },

      // --- Colonnes ---
      // L'ordre correspond exactement au tableau retourné par _format_row() dans le Controller
      columns: [
        { data: 0, title: 'Source',          width: '90px', orderable: true  },
        { data: 1, title: 'Numéro',          width: '110px', orderable: true },
        { data: 2, title: 'Patient',          orderable: true  },
        { data: 3, title: 'Description',      orderable: false },
        { data: 4, title: 'Date',             width: '130px', orderable: true },
        { data: 5, title: 'Mode de paiement', orderable: true  },
        { data: 6, title: 'Montant',          width: '120px', orderable: true },
        { data: 7, title: 'Statut',           width: '90px',  orderable: true },
        { data: 8, title: '',                 width: '50px',  orderable: false, searchable: false }
      ],

      // Tri initial : par date décroissante (colonne index 4)
      order: [[4, 'desc']],

      // --- Pagination ---
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],

      // --- Langue française ---
      language: {
        url: '', // désactive le chargement distant; textes définis ci-dessous
        processing:     'Chargement en cours…',
        search:         'Rechercher :',
        lengthMenu:     'Afficher _MENU_ entrées',
        info:           'Affichage de _START_ à _END_ sur _TOTAL_ paiements',
        infoEmpty:      'Aucun paiement disponible',
        infoFiltered:   '(filtrés depuis _MAX_ paiements au total)',
        zeroRecords:    'Aucun paiement correspondant trouvé',
        emptyTable:     'Aucun paiement disponible dans cette table',
        paginate: {
          first:    'Premier',
          previous: 'Précédent',
          next:     'Suivant',
          last:     'Dernier'
        },
        aria: {
          sortAscending:  ': activer pour trier la colonne par ordre croissant',
          sortDescending: ': activer pour trier la colonne par ordre décroissant'
        }
      },

      // --- Callbacks ---
      drawCallback: function (settings) {
        _isReady = true;
        _updateResultLabel(settings.json);
        _toggleEmptyFooter(settings);
      },

      // Désactiver le tri sur la colonne Actions
      columnDefs: [
        { orderable: false, targets: [3, 8] }
      ],

      // --- DOM layout Bootstrap AdminLTE ---
      dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>"
         + "<'row'<'col-sm-12'tr>>"
         + "<'row'<'col-sm-5'i><'col-sm-7'p>>",

      // --- Styles Bootstrap ---
      pagingType: 'simple_numbers'
    });
  }

  // -----------------------------------------------------------------------
  // Filtres
  // -----------------------------------------------------------------------

  function _initFilters() {
    // Afficher/masquer les champs de dates selon la période
    $('#select-periode').on('change', function () {
      var val = $(this).val();
      if (val === 'custom') {
        $('#bloc-date-debut, #bloc-date-fin').show();
      } else {
        $('#bloc-date-debut, #bloc-date-fin').hide();
        $('#input-date-debut, #input-date-fin').val('');
      }
    });

    // Bouton Filtrer
    $('#btn-filtrer').on('click', function () {
      if (!_isReady) return;

      var periode    = $('#select-periode').val();
      var date_debut = $('#input-date-debut').val();
      var date_fin   = $('#input-date-fin').val();

      // Validation dates personnalisées
      if (periode === 'custom') {
        if (!date_debut || !date_fin) {
          alert('Veuillez saisir les deux dates pour une période personnalisée.');
          return;
        }
        if (date_debut > date_fin) {
          alert('La date de début doit être antérieure ou égale à la date de fin.');
          return;
        }
      }

      _table.ajax.reload(null, true);
    });

    // Bouton Réinitialiser
    $('#btn-reinitialiser').on('click', function () {
      if (!_isReady) return;
      $('#select-periode').val('');
      $('#input-date-debut, #input-date-fin').val('');
      $('#bloc-date-debut, #bloc-date-fin').hide();
      _table.search('').ajax.reload(null, true);
    });
  }

  // -----------------------------------------------------------------------
  // Sélecteur de dates natif HTML5
  // -----------------------------------------------------------------------

  function _initDatePicker() {
    // Définir la date du jour comme valeur max par défaut
    var today = _getTodayStr();
    $('#input-date-debut, #input-date-fin').attr('max', today);

    // Contraindre date_fin >= date_debut
    $('#input-date-debut').on('change', function () {
      var d = $(this).val();
      if (d) {
        $('#input-date-fin').attr('min', d);
      }
    });
  }

  // -----------------------------------------------------------------------
  // Impression
  // -----------------------------------------------------------------------

  function _printUrl(source, forceSearch) {
    var params = [];
    var periode = $('#select-periode').val() || '';
    var debut = $('#input-date-debut').val() || '';
    var fin = $('#input-date-fin').val() || '';
    var search = (_table && _table.search) ? _table.search() : '';

    params.push('source=' + encodeURIComponent(source || 'all'));
    params.push('periode=' + encodeURIComponent(periode));
    params.push('date_debut=' + encodeURIComponent(debut));
    params.push('date_fin=' + encodeURIComponent(fin));
    if (forceSearch || search !== '') params.push('search=' + encodeURIComponent(search));

    return _config.printUrl + '?' + params.join('&');
  }

  function _openPrint(source, forceSearch) {
    // Ouvre le PDF dans l'onglet courant plutôt que dans un nouvel onglet.
    window.location.assign(_printUrl(source, forceSearch));
  }

  function _initPrintButtons() {
    $('#btn-imprimer-tout').on('click', function () { _openPrint('all', false); });
    $('#btn-imprimer-factures').on('click', function () { _openPrint('invoice', false); });
    $('#btn-imprimer-examens').on('click', function () { _openPrint('examen', false); });
    $('#btn-imprimer-recherche').on('click', function () { _openPrint('all', true); });
  }

  // -----------------------------------------------------------------------
  // Mise à jour des statistiques
  // -----------------------------------------------------------------------

  /**
   * Met à jour les 4 cartes statistiques.
   *
   * @param {Object} stats         Objet statistics retourné par le serveur
   * @param {number} recordsFiltered
   */
  function _updateStatistics(stats, recordsFiltered) {
    if (!stats) return;

    var nb      = typeof stats.total_payments !== 'undefined'
      ? stats.total_payments : recordsFiltered;
    var inv     = typeof stats.total_invoice  !== 'undefined' ? stats.total_invoice  : 0;
    var exam    = typeof stats.total_examen   !== 'undefined' ? stats.total_examen   : 0;
    var total   = typeof stats.total_amount   !== 'undefined' ? stats.total_amount   : 0;
    var nbInv   = typeof stats.nb_invoice     !== 'undefined' ? stats.nb_invoice     : 0;
    var nbExam  = typeof stats.nb_examen      !== 'undefined' ? stats.nb_examen      : 0;

    $('#stat-nb-paiements').text(_formatNumber(nb));
    $('#stat-total-invoice').text(_formatFcfa(inv));
    $('#stat-total-examen').text(_formatFcfa(exam));
    $('#stat-total-global').text(_formatFcfa(total));
    // Cartes optionnelles (nombre de consultations / examens sur la
    // période) : mises à jour seulement si présentes dans le HTML, pour
    // ne rien casser sur une page qui ne les afficherait pas.
    $('#stat-nb-invoice').text(_formatNumber(nbInv));
    $('#stat-nb-examen').text(_formatNumber(nbExam));
  }

  /**
   * Met à jour le label du nombre de résultats en haut à droite du tableau.
   *
   * @param {Object} json  Réponse JSON DataTables
   */
  function _updateResultLabel(json) {
    if (!json) return;
    var n = json.recordsFiltered || 0;
    var label = n + ' paiement' + (n > 1 ? 's' : '') + ' trouvé' + (n > 1 ? 's' : '');
    $('#label-resultats').text(label);
  }

  /**
   * Affiche/masque le message "aucun résultat".
   *
   * @param {Object} settings  Objet settings DataTables
   */
  function _toggleEmptyFooter(settings) {
    var total = settings.json ? (settings.json.recordsFiltered || 0) : 0;
    if (total === 0) {
      $('#footer-vide').show();
    } else {
      $('#footer-vide').hide();
    }
  }

  // -----------------------------------------------------------------------
  // Gestion CSRF CodeIgniter (régénération après chaque requête POST)
  // -----------------------------------------------------------------------

  function _updateCsrfToken(json) {
    // CodeIgniter 3 régénère le token CSRF après chaque requête.
    // Si le serveur le retourne dans la réponse JSON, on le met à jour.
    if (json && json.csrf_token && _config.csrfName) {
      _config.csrfToken = json.csrf_token;
      // Mettre à jour aussi le champ hidden si présent
      $('input[name="' + _config.csrfName + '"]').val(json.csrf_token);
    }
  }

  // -----------------------------------------------------------------------
  // Utilitaires de formatage
  // -----------------------------------------------------------------------

  /**
   * Formate un montant en FCFA.
   * Exemple : 12500000 → "12 500 000 FCFA"
   *
   * @param  {number} amount
   * @return {string}
   */
  function _formatFcfa(amount) {
    return _formatter.format(amount) + ' FCFA';
  }

  /**
   * Formate un entier avec séparateurs.
   * Exemple : 1250 → "1 250"
   *
   * @param  {number} n
   * @return {string}
   */
  function _formatNumber(n) {
    return _formatter.format(n);
  }

  /**
   * Retourne la date du jour au format YYYY-MM-DD.
   *
   * @return {string}
   */
  function _getTodayStr() {
    var d   = new Date();
    var mm  = ('0' + (d.getMonth() + 1)).slice(-2);
    var dd  = ('0' + d.getDate()).slice(-2);
    return d.getFullYear() + '-' + mm + '-' + dd;
  }

  /**
   * Affiche un message d'erreur utilisateur.
   *
   * @param {string} msg
   */
  function _showError(msg) {
    // Adapter selon le système de notifications du projet (toastr, SweetAlert, etc.)
    if (typeof toastr !== 'undefined') {
      toastr.error(msg);
    } else {
      alert(msg);
    }
  }

  // -----------------------------------------------------------------------
  // API publique du module
  // -----------------------------------------------------------------------
  return {
    init:    init,
    // Méthode utilitaire exposée pour usage externe éventuel
    reload:  function () { if (_table) _table.ajax.reload(null, false); },
    getTable: function () { return _table; }
  };

}(jQuery));
