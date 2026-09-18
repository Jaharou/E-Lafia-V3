<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');
/*
 *  Tableau de bord Laboratoire.
 *  Reprend la présentation moderne du tableau de bord Réception : mêmes
 *  classes CSS (reception-*), donc même feuille de style, pour garder une
 *  interface homogène entre les deux profils.
 *  Les données proviennent de Laboratorist::_get_dashboard_data().
 */
$dashboard = isset($lab_dashboard) ? $lab_dashboard : array();
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$money  = function ($value) { return number_format((float) $value, 0, ',', ' '); };
$user = $this->db->get_where('laboratorist', array('laboratorist_id' => $this->session->userdata('login_user_id')))->row();
$user_name = $user && isset($user->name) ? $user->name : 'Laboratoire';

/** Concatène un champ des entrées JSON d'un examen, en ignorant le JSON invalide. */
if (!function_exists('lab_entries_field')) {
    function lab_entries_field($json, $field) {
        $entries = json_decode($json, true);
        if (!is_array($entries)) { return ''; }
        $out = array();
        foreach ($entries as $entry) {
            if (is_array($entry) && isset($entry[$field]) && $entry[$field] !== '') {
                $out[] = $entry[$field];
            }
        }
        return implode(', ', $out);
    }
}
?>
<div class="reception-dashboard">

    <section class="reception-welcome">
        <div>
            <p class="reception-eyebrow">ESPACE LABORATOIRE</p>
            <h1>Bonjour, <?php echo $escape($user_name); ?>.</h1>
            <p>Analyses, résultats et suivi des patients depuis un seul espace.</p>
        </div>
        <div class="reception-welcome-actions">
            <a class="btn reception-btn-light" href="<?php echo base_url('laboratorist/patient_crud/add'); ?>"><i class="fa fa-user-plus"></i> Nouveau patient</a>
            <a class="btn reception-btn-primary" href="<?php echo base_url('laboratorist/examen_crud/add'); ?>"><i class="fa fa-plus-circle"></i> Nouvel examen</a>
        </div>
    </section>

    <section class="reception-stats">
        <a class="reception-stat-card reception-stat-blue" href="<?php echo base_url('laboratorist/patient'); ?>">
            <i class="fa fa-users"></i>
            <span><small>Patients enregistrés</small><strong><?php echo $money($dashboard['patients_total'] ?? 0); ?></strong><em>Gérer les dossiers</em></span>
        </a>
        <a class="reception-stat-card reception-stat-violet" href="<?php echo base_url('laboratorist/examen'); ?>">
            <i class="fa fa-flask"></i>
            <span><small>Examens du jour</small><strong><?php echo $money($dashboard['examens_jour'] ?? 0); ?></strong><em><?php echo $money($dashboard['analyses_jour'] ?? 0); ?> analyse(s)</em></span>
        </a>
        <a class="reception-stat-card reception-stat-amber" href="<?php echo base_url('laboratorist/examen'); ?>">
            <i class="fa fa-hourglass-half"></i>
            <span><small>En attente de résultat</small><strong><?php echo $money($dashboard['en_attente'] ?? 0); ?></strong><em>À traiter</em></span>
        </a>
        <a class="reception-stat-card reception-stat-green" href="<?php echo base_url('laboratorist/examen'); ?>">
            <i class="fa fa-line-chart"></i>
            <span><small>Recette du jour</small><strong><?php echo $money($dashboard['recette_jour'] ?? 0); ?></strong><em><?php echo $money($dashboard['recette_mois'] ?? 0); ?> ce mois</em></span>
        </a>
    </section>

    <section class="reception-quick-actions">
        <div class="reception-section-heading">
            <div><p class="reception-eyebrow">ACCÈS RAPIDE</p><h2>Actions fréquentes</h2></div>
            <span><?php echo date('d/m/Y'); ?></span>
        </div>
        <div class="reception-action-grid">
            <a href="<?php echo base_url('laboratorist/examen_crud/add'); ?>"><i class="fa fa-flask"></i><span>Ajouter un examen</span></a>
            <a href="<?php echo base_url('laboratorist/examen'); ?>"><i class="fa fa-list-alt"></i><span>Liste des examens</span></a>
            <a href="<?php echo base_url('laboratorist/patient_crud/add'); ?>"><i class="fa fa-user-plus"></i><span>Créer un patient</span></a>
            <a href="<?php echo base_url('laboratorist/patient'); ?>"><i class="fa fa-users"></i><span>Dossiers patients</span></a>
            <a href="<?php echo base_url('laboratorist/blood_bank'); ?>"><i class="fa fa-tint"></i><span>Banque de sang</span></a>
            <a href="<?php echo base_url('laboratorist/blood_donor'); ?>"><i class="fa fa-heartbeat"></i><span>Donneurs</span></a>
        </div>
    </section>

    <section class="reception-panel">
        <div class="reception-panel-title">
            <div><h2>Activité des 14 derniers jours</h2><p>Examens réalisés et recette associée.</p></div>
        </div>
        <div class="panel-body p-20">
            <div id="lab-trend-chart" style="width:100%; height:270px;"></div>
        </div>
    </section>

    <div class="row reception-dashboard-grids">
        <div class="col-md-7">
            <section class="reception-panel">
                <div class="reception-panel-title">
                    <div><h2>Derniers examens</h2><p>Les huit examens les plus récents.</p></div>
                    <a href="<?php echo base_url('laboratorist/examen'); ?>">Tout afficher <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table reception-table">
                        <thead><tr><th>Patient</th><th>Test</th><th>Résultat</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if (!empty($dashboard['examens_recents'])): foreach ($dashboard['examens_recents'] as $exam):
                            $patient = trim(($exam['name'] ?? '').' '.($exam['prenom'] ?? '')) ?: 'Patient non renseigné';
                            $resultat = lab_entries_field($exam['examen_entries'], 'resultat');
                        ?>
                            <tr>
                                <td><strong><?php echo $escape($patient); ?></strong><small>Examen n° <?php echo $escape($exam['examen_number'] ?: $exam['id_examen']); ?></small></td>
                                <td><?php echo $escape(lab_entries_field($exam['examen_entries'], 'description')); ?></td>
                                <td><?php echo $resultat === '' ? '<span class="reception-empty">—</span>' : $escape($resultat); ?></td>
                                <td><?php echo $escape($exam['creation_time']); ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="reception-empty">Aucun examen enregistré.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-md-5">
            <section class="reception-panel">
                <div class="reception-panel-title">
                    <div><h2>Examens à traiter</h2><p>En attente de résultat.</p></div>
                    <a href="<?php echo base_url('laboratorist/examen'); ?>">Voir tout <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="reception-list">
                <?php if (!empty($dashboard['examens_attente'])): foreach ($dashboard['examens_attente'] as $exam): ?>
                    <a href="<?php echo base_url('laboratorist/examen_crud/edit/'.(int) $exam['id_examen']); ?>">
                        <i class="fa fa-flask"></i>
                        <span>
                            <strong><?php echo $escape(trim(($exam['name'] ?? '').' '.($exam['prenom'] ?? '')) ?: 'Patient non renseigné'); ?></strong>
                            <small>Examen n° <?php echo $escape($exam['examen_number'] ?: $exam['id_examen']); ?></small>
                        </span>
                        <em><?php echo $escape($exam['statut_examen'] ?: 'En attente'); ?></em>
                    </a>
                <?php endforeach; else: ?>
                    <div class="reception-empty">Aucun examen en attente.</div>
                <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <section class="reception-panel reception-recent-consultations">
        <div class="reception-panel-title">
            <div><h2>Patients récemment enregistrés</h2><p>Les six derniers dossiers créés.</p></div>
            <a href="<?php echo base_url('laboratorist/patient'); ?>">Tout afficher <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table reception-table">
                <thead><tr><th>Patient</th><th>Téléphone</th><th>Sexe / âge</th><th></th></tr></thead>
                <tbody>
                <?php if (!empty($dashboard['patients_recents'])): foreach ($dashboard['patients_recents'] as $patient): ?>
                    <tr>
                        <td><strong><?php echo $escape(trim(($patient['name'] ?? '').' '.($patient['prenom'] ?? ''))); ?></strong><small>Dossier #<?php echo (int) $patient['patient_id']; ?></small></td>
                        <td><?php echo $escape($patient['phone'] ?: '—'); ?></td>
                        <td><?php echo $escape($patient['sex'] ?: '—'); ?><?php echo !empty($patient['age']) ? ' · '.(int) $patient['age'].' ans' : ''; ?></td>
                        <td><a class="reception-icon-link" href="<?php echo base_url('laboratorist/patient_crud/edit/'.(int) $patient['patient_id']); ?>"><i class="fa fa-arrow-right"></i></a></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="4" class="reception-empty">Aucun patient enregistré.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
// Graphique de tendance (amCharts est déjà chargé par includes_bottom.php).
(function () {
    var trendData = <?php echo json_encode($dashboard['tendance'] ?? array(), JSON_UNESCAPED_UNICODE); ?>;
    if (typeof AmCharts === 'undefined') { return; }
    AmCharts.makeChart("lab-trend-chart", {
        type: "serial",
        theme: "light",
        dataProvider: trendData,
        categoryField: "jour",
        categoryAxis: { gridPosition: "start", axisAlpha: 0, gridAlpha: 0.08 },
        valueAxes: [
            { id: "v1", axisAlpha: 0, gridAlpha: 0.08, title: "Examens" },
            { id: "v2", axisAlpha: 0, gridAlpha: 0, position: "right", title: "Recette" }
        ],
        graphs: [
            { id: "g1", valueAxis: "v1", valueField: "examens", title: "Examens",
              type: "column", fillAlphas: 0.85, lineAlpha: 0, cornerRadiusTop: 3,
              lineColor: "#4178dc", balloonText: "<b>[[value]]</b> examen(s)" },
            { id: "g2", valueAxis: "v2", valueField: "recette", title: "Recette",
              type: "smoothedLine", lineThickness: 2, bullet: "round", bulletSize: 7,
              lineColor: "#27ae60", balloonText: "<b>[[value]]</b> F" }
        ],
        chartCursor: { categoryBalloonEnabled: true, cursorAlpha: 0, zoomable: false },
        legend: { useGraphSettings: true, align: "center" },
        export: { enabled: false }
    });
})();
</script>
