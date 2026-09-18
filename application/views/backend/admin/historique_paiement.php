<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Vue : Historique des paiements
 * Fichier : application/views/paiements/historique_paiement.php
 *
 * Cette vue est conçue pour s'intégrer dans un layout existant CI3.
 * Adapter $this->load->view('layouts/header') selon la structure du projet.
 */
?>


<div class="content-wrapper">
  <section class="content-header">
    <h1>
      <i class="fa fa-history"></i>
      Historique des paiements
      <small>Consultations &amp; Examens</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard'); ?>"><i class="fa fa-home"></i> Accueil</a></li>
      <li class="active">Historique des paiements</li>
    </ol>
  </section>

  <section class="content">

    <!-- ================================================================
         FILTRES
    ================================================================ -->
    <div class="box box-default collapsed-box" id="box-filtres">
      <div class="box-header with-border" style="cursor:pointer;" data-widget="collapse">
        <h3 class="box-title">
          <i class="fa fa-filter"></i> Filtres
        </h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-plus"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <form id="form-filtres">
          <div class="row">

            <!-- Période -->
            <div class="col-md-3">
              <div class="form-group">
                <label for="select-periode">
                  <i class="fa fa-calendar"></i> Période
                </label>
                <select id="select-periode" name="periode" class="form-control">
                  <option value="">-- Toutes les périodes --</option>
                  <option value="today">Aujourd'hui</option>
                  <option value="week">Cette semaine</option>
                  <option value="month">Ce mois</option>
                  <option value="year">Cette année</option>
                  <option value="custom">Période personnalisée</option>
                </select>
              </div>
            </div>

            <!-- Date début -->
            <div class="col-md-2" id="bloc-date-debut" style="display:none;">
              <div class="form-group">
                <label for="input-date-debut">Du</label>
                <input type="date"
                       id="input-date-debut"
                       name="date_debut"
                       class="form-control"
                       placeholder="AAAA-MM-JJ">
              </div>
            </div>

            <!-- Date fin -->
            <div class="col-md-2" id="bloc-date-fin" style="display:none;">
              <div class="form-group">
                <label for="input-date-fin">Au</label>
                <input type="date"
                       id="input-date-fin"
                       name="date_fin"
                       class="form-control"
                       placeholder="AAAA-MM-JJ">
              </div>
            </div>

            <!-- Boutons -->
            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="button" id="btn-filtrer" class="btn btn-primary">
                    <i class="fa fa-search"></i> Filtrer
                  </button>
                  <button type="button" id="btn-reinitialiser" class="btn btn-default">
                    <i class="fa fa-times"></i> Réinitialiser
                  </button>
                </div>
              </div>
            </div>

          </div><!-- /.row -->
        </form>
      </div><!-- /.box-body -->
    </div><!-- /#box-filtres -->

    <!-- Impression -->
    <div class="box box-success">
      <div class="box-body">
        <strong><i class="fa fa-print"></i> Impression</strong>
        <span class="text-muted" style="margin-left:8px;">Les boutons respectent la période, les dates et la recherche du tableau.</span>
        <div class="pull-right">
          <button type="button" id="btn-imprimer-tout" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Tout l'historique</button>
          <button type="button" id="btn-imprimer-factures" class="btn btn-info btn-sm"><i class="fa fa-file-text"></i> Consultations</button>
          <button type="button" id="btn-imprimer-examens" class="btn btn-warning btn-sm"><i class="fa fa-flask"></i> Examens</button>
          <button type="button" id="btn-imprimer-recherche" class="btn btn-default btn-sm"><i class="fa fa-search"></i> Résultat de la recherche</button>
        </div>
      </div>
    </div>

    <!-- ================================================================
         STATISTIQUES
    ================================================================ -->
    <div class="row" id="bloc-stats">

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat">
          <span class="info-box-icon bg-stat-payments">
            <i class="fa fa-list-alt"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Nombre de paiements</span>
            <span class="info-box-number" id="stat-nb-paiements">—</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat">
          <span class="info-box-icon bg-stat-invoice">
            <i class="fa fa-file-text"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Total consultations</span>
            <span class="info-box-number" id="stat-total-invoice">—</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat">
          <span class="info-box-icon bg-stat-examen">
            <i class="fa fa-flask"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Total examens</span>
            <span class="info-box-number" id="stat-total-examen">—</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat info-box-total">
          <span class="info-box-icon bg-stat-total">
            <i class="fa fa-money"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Total global</span>
            <span class="info-box-number" id="stat-total-global">—</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat">
          <span class="info-box-icon bg-stat-invoice">
            <i class="fa fa-stethoscope"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Nombre de consultations</span>
            <span class="info-box-number" id="stat-nb-invoice">—</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6">
        <div class="info-box info-box-stat">
          <span class="info-box-icon bg-stat-examen">
            <i class="fa fa-flask"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Nombre d'examens</span>
            <span class="info-box-number" id="stat-nb-examen">—</span>
          </div>
        </div>
      </div>

    </div><!-- /#bloc-stats -->

    <!-- ================================================================
         TABLEAU DataTables
    ================================================================ -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-table"></i> Liste des paiements
        </h3>
        <div class="box-tools pull-right">
          <span id="label-resultats" class="label label-default" style="font-size:13px;padding:5px 10px;">
            Chargement…
          </span>
        </div>
      </div>
      <div class="box-body table-responsive">
        <table id="table-paiements"
               class="table table-bordered table-striped table-hover table-condensed"
               style="width:100%">
          <thead>
            <tr>
              <th>Source</th>
              <th>Numéro</th>
              <th>Patient</th>
              <th>Description</th>
              <th>Date</th>
              <th>Mode de paiement</th>
              <th>Montant</th>
              <th>Statut</th>
              <th class="no-sort">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Données chargées via AJAX -->
          </tbody>
          <tfoot>
            <tr>
              <th>Source</th>
              <th>Numéro</th>
              <th>Patient</th>
              <th>Description</th>
              <th>Date</th>
              <th>Mode de paiement</th>
              <th>Montant</th>
              <th>Statut</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div><!-- /.box-body -->

      <!-- Message état vide -->
      <div class="box-footer" id="footer-vide" style="display:none;">
        <div class="callout callout-info">
          <i class="fa fa-info-circle"></i>
          Aucun paiement trouvé pour les critères sélectionnés.
        </div>
      </div>

    </div><!-- /.box -->

  </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- ================================================================
     CSS inline (à déplacer dans un fichier CSS dédié en production)
================================================================ -->


<!-- ================================================================
     Scripts JavaScript
     (adapter les chemins selon la structure asset du projet)
================================================================ -->
