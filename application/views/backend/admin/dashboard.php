<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');
/*
 *  Tableau de bord Administration.
 *
 *  Reprend la présentation moderne des espaces Réception et Laboratoire
 *  (mêmes classes CSS reception-*), et récupère ses chiffres depuis
 *  Admin::_get_dashboard_data().
 *
 *  Corrections par rapport à l'ancienne version :
 *   - 4 requêtes "SELECT * FROM invoice/examen" (toutes les lignes, colonnes
 *     JSON comprises) remplacées par 2 requêtes d'agrégation ;
 *   - filtre de période : les dates passaient de $_GET directement dans le
 *     SQL (injection possible), elles sont maintenant liées en paramètres ;
 *   - comparaisons de dates corrigées : STR_TO_DATE() était appliqué à des
 *     colonnes devenues DATETIME, donc les filtres ne renvoyaient plus rien ;
 *   - ob_start() n'était jamais refermé, le contenu s'affichait hors du
 *     gabarit ;
 *   - jQuery était rechargé une seconde fois, ce qui casse les plugins
 *     initialisés sur la première instance.
 */
$d = isset($admin_dashboard) ? $admin_dashboard : array();
$escape = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$money  = function ($v) { return number_format((float) $v, 0, ',', ' '); };
$user = $this->db->get_where('admin', array('admin_id' => $this->session->userdata('login_user_id')))->row();
$user_name = $user && isset($user->name) ? $user->name : 'Administrateur';
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
<div class="reception-dashboard">

    <section class="reception-welcome">
        <div>
            <p class="reception-eyebrow">ADMINISTRATION</p>
            <h1>Bonjour, <?php echo $escape($user_name); ?>.</h1>
            <p>Vue d'ensemble de l'activité de l'établissement.</p>
        </div>
        <div class="reception-welcome-actions">
            <a class="btn reception-btn-light" href="<?php echo base_url('admin/patient'); ?>"><i class="fa fa-users"></i> Patients</a>
            <a class="btn reception-btn-primary" href="<?php echo base_url('admin/payment_history'); ?>"><i class="fa fa-history"></i> Historique paiements</a>
        </div>
    </section>

    <section class="reception-stats">
        <a class="reception-stat-card reception-stat-blue" href="<?php echo base_url('admin/patient'); ?>">
            <i class="fa fa-users"></i>
            <span><small>Patients</small><strong><?php echo $money($d['patients_total'] ?? 0); ?></strong><em>Dossiers enregistrés</em></span>
        </a>
        <a class="reception-stat-card reception-stat-violet" href="<?php echo base_url('admin/invoice_manage'); ?>">
            <i class="fa fa-stethoscope"></i>
            <span><small>Consultations</small><strong><?php echo $money($d['consultations_total'] ?? 0); ?></strong><em><?php echo $money($d['consultations_jour'] ?? 0); ?> aujourd'hui</em></span>
        </a>
        <a class="reception-stat-card reception-stat-amber" href="<?php echo base_url('admin/examen'); ?>">
            <i class="fa fa-flask"></i>
            <span><small>Examens</small><strong><?php echo $money($d['examens_total'] ?? 0); ?></strong><em><?php echo $money($d['examens_jour'] ?? 0); ?> aujourd'hui</em></span>
        </a>
        <a class="reception-stat-card reception-stat-green" href="<?php echo base_url('admin/payment_history'); ?>">
            <i class="fa fa-line-chart"></i>
            <span><small>Recette du jour</small><strong><?php echo $money($d['ca_jour'] ?? 0); ?></strong><em><?php echo $money($d['ca_mois'] ?? 0); ?> ce mois</em></span>
        </a>
    </section>

    <section class="reception-stats">
        <a class="reception-stat-card reception-stat-violet" href="<?php echo base_url('admin/traitement'); ?>">
            <i class="fa fa-medkit"></i>
            <span><small>Traitements</small><strong><?php echo $money($d['traitements_total'] ?? 0); ?></strong><em>Total enregistré</em></span>
        </a>
        <a class="reception-stat-card reception-stat-amber" href="<?php echo base_url('admin/examen'); ?>">
            <i class="fa fa-hourglass-half"></i>
            <span><small>Examens en attente</small><strong><?php echo $money($d['examens_attente'] ?? 0); ?></strong><em>Résultat à saisir</em></span>
        </a>
        <a class="reception-stat-card reception-stat-blue" href="<?php echo base_url('admin/invoice_manage'); ?>">
            <i class="fa fa-exclamation-circle"></i>
            <span><small>Consultations impayées</small><strong><?php echo $money($d['consultations_impayees'] ?? 0); ?></strong><em>À recouvrer</em></span>
        </a>
        <a class="reception-stat-card reception-stat-green" href="<?php echo base_url('admin/doctor'); ?>">
            <i class="fa fa-user-md"></i>
            <span><small>Personnel</small><strong><?php
                $p = $d['personnel'] ?? array();
                echo $money(array_sum($p));
            ?></strong><em><?php echo (int) ($p['doctor'] ?? 0); ?> médecin(s)</em></span>
        </a>
    </section>

    <section class="reception-panel">
        <div class="reception-panel-title">
            <div><h2>Rechercher par période</h2><p>Consultations et examens réalisés entre deux dates.</p></div>
        </div>
        <div class="panel-body p-20">
            <form method="GET" action="<?php echo base_url('admin/dashboard'); ?>" class="form-inline">
                <label for="start_date">Du&nbsp;</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="<?php echo $escape($this->input->get('start_date', TRUE)); ?>" required>
                <label for="end_date">&nbsp;au&nbsp;</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="<?php echo $escape($this->input->get('end_date', TRUE)); ?>" required>
                &nbsp;<button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Rechercher</button>
            </form>

            <?php if (!empty($d['periode'])): $p = $d['periode']; ?>
            <hr>
            <div class="table-responsive">
                <table class="table reception-table">
                    <thead><tr><th>Prestation</th><th>Nombre</th><th>Montant</th></tr></thead>
                    <tbody>
                        <tr><td>Consultations</td><td><?php echo $money($p['consultations_nb']); ?></td><td><?php echo $money($p['consultations_ca']); ?> F</td></tr>
                        <tr><td>Examens</td><td><?php echo $money($p['examens_nb']); ?></td><td><?php echo $money($p['examens_ca']); ?> F</td></tr>
                        <tr><td><strong>Total</strong></td>
                            <td><strong><?php echo $money($p['consultations_nb'] + $p['examens_nb']); ?></strong></td>
                            <td><strong><?php echo $money($p['total_ca']); ?> F</strong></td></tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="reception-panel">
        <div class="reception-panel-title">
            <div><h2>Activité des 14 derniers jours</h2><p>Consultations, examens et recette globale.</p></div>
        </div>
        <div class="panel-body p-20">
            <div id="admin-trend-chart" style="width:100%; height:300px;"></div>
        </div>
    </section>

    <div class="row reception-dashboard-grids">
        <div class="col-md-7">
            <section class="reception-panel">
                <div class="reception-panel-title">
                    <div><h2>Dernières consultations</h2><p>Les huit reçus les plus récents.</p></div>
                    <a href="<?php echo base_url('admin/invoice_manage'); ?>">Tout afficher <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table reception-table">
                        <thead><tr><th>Reçu</th><th>Patient</th><th>Montant</th><th>Statut</th></tr></thead>
                        <tbody>
                        <?php if (!empty($d['consultations_recentes'])): foreach ($d['consultations_recentes'] as $inv): ?>
                            <tr>
                                <td><strong><?php echo $escape($inv['invoice_number'] ?: '#'.$inv['invoice_id']); ?></strong><small><?php echo $escape($inv['creation_datetime']); ?></small></td>
                                <td><?php echo $escape(trim(($inv['name'] ?? '').' '.($inv['prenom'] ?? '')) ?: 'Patient non renseigné'); ?></td>
                                <td><?php echo $money($inv['total_amount']); ?> F</td>
                                <td><span class="reception-status"><?php echo $escape($inv['status'] ?: '—'); ?></span></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="reception-empty">Aucune consultation.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-md-5">
            <section class="reception-panel">
                <div class="reception-panel-title">
                    <div><h2>Derniers examens</h2><p>Activité du laboratoire.</p></div>
                    <a href="<?php echo base_url('admin/examen'); ?>">Voir tout <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="reception-list">
                <?php if (!empty($d['examens_recents'])): foreach ($d['examens_recents'] as $ex): ?>
                    <a href="<?php echo base_url('admin/examen_crud/edit/'.(int) $ex['id_examen']); ?>">
                        <i class="fa fa-flask"></i>
                        <span>
                            <strong><?php echo $escape(trim(($ex['name'] ?? '').' '.($ex['prenom'] ?? '')) ?: 'Patient non renseigné'); ?></strong>
                            <small>N° <?php echo $escape($ex['examen_number'] ?: $ex['id_examen']); ?> · <?php echo $money($ex['total_amount']); ?> F</small>
                        </span>
                        <em><?php echo $escape($ex['statut_examen'] ?: '—'); ?></em>
                    </a>
                <?php endforeach; else: ?>
                    <div class="reception-empty">Aucun examen.</div>
                <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    var trendData = <?php echo json_encode($d['tendance'] ?? array(), JSON_UNESCAPED_UNICODE); ?>;
    if (typeof AmCharts === 'undefined') { return; }
    AmCharts.makeChart("admin-trend-chart", {
        type: "serial", theme: "light",
        dataProvider: trendData, categoryField: "jour",
        categoryAxis: { gridPosition: "start", axisAlpha: 0, gridAlpha: 0.08 },
        valueAxes: [
            { id: "v1", axisAlpha: 0, gridAlpha: 0.08, title: "Nombre" },
            { id: "v2", axisAlpha: 0, gridAlpha: 0, position: "right", title: "Recette" }
        ],
        graphs: [
            { valueAxis: "v1", valueField: "consultations", title: "Consultations", type: "column",
              fillAlphas: 0.85, lineAlpha: 0, cornerRadiusTop: 3, lineColor: "#4178dc",
              balloonText: "<b>[[value]]</b> consultation(s)" },
            { valueAxis: "v1", valueField: "examens", title: "Examens", type: "column",
              fillAlphas: 0.85, lineAlpha: 0, cornerRadiusTop: 3, lineColor: "#f0ad4e",
              balloonText: "<b>[[value]]</b> examen(s)" },
            { valueAxis: "v2", valueField: "recette", title: "Recette", type: "smoothedLine",
              lineThickness: 2, bullet: "round", bulletSize: 7, lineColor: "#27ae60",
              balloonText: "<b>[[value]]</b> F" }
        ],
        chartCursor: { categoryBalloonEnabled: true, cursorAlpha: 0, zoomable: false },
        legend: { useGraphSettings: true, align: "center" },
        export: { enabled: false }
    });
})();
</script>
<?php
$output .= ob_get_clean();
echo $output;
