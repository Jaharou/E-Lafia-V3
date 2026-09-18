<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paiements
 *
 * Contrôleur CodeIgniter 3 — Historique des paiements.
 *
 * Routes :
 *   GET  paiements/historique          → affiche la vue dans le layout backend
 *   POST paiements/historique_ajax     → endpoint DataTables Server-Side
 *
 * @package  Gamadadi
 * @author   Gamadadi Dev Team
 * @version  2.0
 */
class Paiements extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Gamadadi ne charge pas la base de données via autoload.php.
        // Ce contrôleur utilise $this->db directement (et le modèle aussi),
        // il faut donc charger explicitement la librairie Database.
        $this->load->database();

        // Le projet utilise les indicateurs admin_login / receptionist_login.
        if ($this->session->userdata('admin_login') != 1 && $this->session->userdata('receptionist_login') != 1) {
            redirect(base_url(), 'refresh');
        }

        $this->load->model('Payment_model');
        $this->load->helper(['url', 'date']);
    }

    // ------------------------------------------------------------------
    // Vue principale
    // ------------------------------------------------------------------

    /**
     * Affiche la page Historique des paiements.
     */
    public function historique()
    {
        $data['page_name'] = 'historique_paiement';
        $data['page_title'] = get_phrase('historique de paiement');
        $this->load->view('backend/index', $data);
    }

    // ------------------------------------------------------------------
    // Endpoint AJAX DataTables
    // ------------------------------------------------------------------

    /**
     * Endpoint POST — retourne un JSON DataTables Server-Side.
     *
     * Paramètres attendus (POST) :
     *   draw, start, length, search[value], order[0][column],
     *   order[0][dir], columns[], periode, date_debut, date_fin
     *
     * Retourne :
     * {
     *   "draw": int,
     *   "recordsTotal": int,
     *   "recordsFiltered": int,
     *   "data": [...],
     *   "statistics": {
     *     "total_payments": int,
     *     "total_invoice":  float,
     *     "total_examen":   float,
     *     "total_amount":   float,
     *     "nb_invoice":     int,
     *     "nb_examen":      int
     *   }
     * }
     */
    public function historique_ajax()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!$this->input->is_ajax_request()) {
                throw new Exception('La requête reçue n\'est pas AJAX.');
            }

            $params = $this->_get_sanitized_params();
            $records_total    = $this->Payment_model->count_all();
            $records_filtered = $this->Payment_model->count_filtered($params);
            $data             = $this->Payment_model->get_datatables($params);
            $statistics       = $this->Payment_model->get_statistics($params);

            $rows = array();
            foreach ($data as $row) {
                $rows[] = $this->_format_row($row);
            }

            echo json_encode(array(
                'draw' => (int)$params['draw'],
                'recordsTotal' => $records_total,
                'recordsFiltered' => $records_filtered,
                'data' => $rows,
                'statistics' => $statistics,
                'csrf_token' => $this->security->get_csrf_hash()
            ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(200);
            echo json_encode(array(
                'draw' => isset($params['draw']) ? (int)$params['draw'] : 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array(),
                'statistics' => array('total_payments'=>0,'total_invoice'=>0,'total_examen'=>0,'total_amount'=>0,'nb_invoice'=>0,'nb_examen'=>0),
                'error' => $e->getMessage(),
                'csrf_token' => $this->security->get_csrf_hash()
            ), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Impression complète de l'historique filtré.
     * source = all | invoice | examen
     */
    public function imprimer_historique()
    {
        if ($this->session->userdata('admin_login') != 1 && $this->session->userdata('receptionist_login') != 1) {
            redirect(base_url(), 'refresh');
            return;
        }

        $params = array(
            'draw' => 1, 'start' => 0, 'length' => 100,
            'search' => array('value' => $this->_get_query_string('search')),
            'order' => array(array('column' => 4, 'dir' => 'desc')),
            'periode' => $this->_get_query_string('periode'),
            'date_debut' => $this->_get_query_string('date_debut'),
            'date_fin' => $this->_get_query_string('date_fin')
        );
        if (!in_array($params['periode'], array('', 'today', 'week', 'month', 'year', 'custom'), true)) $params['periode'] = '';

        $source = $this->_get_query_string('source');
        if (!in_array($source, array('all', 'invoice', 'examen'), true)) $source = 'all';

        $rows = $this->Payment_model->get_print_rows($params, $source);
        $total_factures = 0; $total_examens = 0;
        foreach ($rows as $r) {
            $amount = isset($r['montant']) && is_numeric($r['montant']) ? (float)$r['montant'] : $this->_calculate_row_amount($r);
            if ($r['source'] === 'CONSULTATION') $total_factures += $amount;
            else $total_examens += $amount;
        }

        $this->load->library('fpdf');
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(8, 8, 8);
        $pdf->SetAutoPageBreak(true, 14);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $settings = array();
        foreach (array('logo','phone','address') as $type) {
            $q = $this->db->get_where('settings', array('type'=>$type))->row();
            $settings[$type] = $q ? $q->description : '';
        }

        $pdf->SetFont('Arial','B',14);
        $logo = trim($settings['logo']);

        if ($logo != '') {
            $logoPath = FCPATH . 'uploads/' . $logo;

            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 4, 3, 41, 27);
            }
        }
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,utf8_decode($settings['address'].' | '.$settings['phone']),0,1,'C');
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',12);
        $titre = ($source==='invoice') ? 'HISTORIQUE DES CONSULTATIONS' : (($source==='examen') ? 'HISTORIQUE DES EXAMENS' : 'HISTORIQUE DES PAIEMENTS');
        $pdf->Cell(0,7,utf8_decode($titre),0,1,'C');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,5,utf8_decode($this->_print_period_label($params)),0,1,'C');
        if ($params['search']['value'] !== '') {
            $pdf->Cell(0, 5, utf8_decode('Recherche : '.$params['search']['value']), 0, 1, 'C');
        }
        $pdf->Ln(3);

        $pdf->SetFillColor(230,230,230); $pdf->SetFont('Arial','B',8);
        $headers=array('Source','N°','Patient','Description','Date','Paiement','Montant','Statut');
        $widths=array(24,30,55,65,32,28,30,28);
        foreach($headers as $i=>$h) $pdf->Cell($widths[$i],7,utf8_decode($h),1,0,'C',true);
        $pdf->Ln(); $pdf->SetFont('Arial','',7);
        foreach($rows as $r){
            $amount=isset($r['montant'])&&is_numeric($r['montant'])?(float)$r['montant']:$this->_calculate_row_amount($r);
            $desc=$this->_extract_descriptions(isset($r['entries_json'])?$r['entries_json']:'',isset($r['description'])?$r['description']:'');
            $vals=array($r['source'], $r['numero'], trim($r['patient_nom']), $desc, !empty($r['date_paiement'])?date('d/m/Y H:i',strtotime($r['date_paiement'])):'', $r['mode_paiement'], number_format($amount,0,',',' ').' FCFA', $r['statut']);
            foreach($vals as $i=>$v) $pdf->Cell($widths[$i],6,utf8_decode(substr((string)$v,0,42)),1,0,$i===6?'R':'L');
            $pdf->Ln();
        }
        $pdf->Ln(3); $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,6,'Nombre de lignes : '.count($rows),0,1,'R');
        if($source!=='examen') $pdf->Cell(0,6,'Total consultations : '.number_format($total_factures,0,',',' ').' FCFA',0,1,'R');
        if($source!=='invoice') $pdf->Cell(0,6,'Total examens : '.number_format($total_examens,0,',',' ').' FCFA',0,1,'R');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(0,7,'TOTAL GENERAL : '.number_format($total_factures+$total_examens,0,',',' ').' FCFA',0,1,'R');
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,5,utf8_decode('Imprimé le '.date('d/m/Y H:i:s')),0,1,'L');
        // FPDF 1.x attend d'abord le nom du fichier, puis la destination.
        $pdf->Output('historique_paiements_'.date('Ymd_His').'.pdf', 'I');
        exit;
    }

    /**
     * Retourne un paramètre GET texte sans provoquer d'avertissement lorsqu'un
     * client envoie accidentellement une valeur sous forme de tableau.
     */
    private function _get_query_string($key)
    {
        $value = $this->input->get($key, TRUE);

        if (!is_scalar($value) && $value !== null) {
            return '';
        }

        return trim(strip_tags((string) $value));
    }

    private function _print_period_label($params)
    {
        switch ($params['periode']) {
            case 'today': return 'Période : Aujourd\'hui';
            case 'week': return 'Période : Cette semaine';
            case 'month': return 'Période : Ce mois';
            case 'year': return 'Période : Cette année';
            case 'custom': return 'Période : du '.$params['date_debut'].' au '.$params['date_fin'];
            default: return 'Période : Toutes les dates';
        }
    }

    // ------------------------------------------------------------------
    // Méthodes privées
    // ------------------------------------------------------------------

    /**
     * Récupère et assainit tous les paramètres POST.
     *
     * @return array
     */
    private function _get_sanitized_params()
    {
        $post = $this->input->post(null, true); // XSS filtering activé

        // Paramètres DataTables de base
        $params = [
            'draw'   => isset($post['draw'])   ? (int) $post['draw']   : 1,
            'start'  => isset($post['start'])  ? (int) $post['start']  : 0,
            'length' => isset($post['length']) ? (int) $post['length'] : 25,
        ];

        // Recherche
        $params['search'] = [
            'value' => isset($post['search']['value'])
                ? trim(strip_tags($post['search']['value'])) : '',
        ];

        // Tri
        $params['order'] = [];
        if (isset($post['order'][0])) {
            $col = (int) $post['order'][0]['column'];
            $dir = (isset($post['order'][0]['dir']) && $post['order'][0]['dir'] === 'desc')
                ? 'desc' : 'asc';
            $params['order'][0] = ['column' => $col, 'dir' => $dir];
        }

        // Filtres personnalisés — validation stricte
        $periode    = isset($post['periode']) ? trim($post['periode']) : '';
        $date_debut = isset($post['date_debut']) ? trim($post['date_debut']) : '';
        $date_fin   = isset($post['date_fin'])   ? trim($post['date_fin'])   : '';

        // Validation : période doit être dans la liste blanche
        $periodes_valides = ['today', 'week', 'month', 'year', 'custom', ''];
        if (!in_array($periode, $periodes_valides, true)) {
            $periode = '';
        }

        // Validation : format date YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = '';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = '';
        }

        $params['periode']    = $periode;
        $params['date_debut'] = $date_debut;
        $params['date_fin']   = $date_fin;

        return $params;
    }

    /**
     * Formate une ligne brute pour l'affichage DataTables.
     *
     * @param  array $row
     * @return array  Tableau indexé correspondant aux colonnes définies dans la vue
     */
    private function _format_row($row)
    {
        // Badge source
        $source_class = ($row['source'] === 'CONSULTATION') ? 'badge-facture' : 'badge-examen';
        $source_html  = '<span class="badge ' . $source_class . '">'
            . htmlspecialchars($row['source'], ENT_QUOTES, 'UTF-8')
            . '</span>';

        // Numéro
        $numero = htmlspecialchars($row['numero'] ?? '', ENT_QUOTES, 'UTF-8');

        // Patient
        $patient = htmlspecialchars(trim($row['patient_nom'] ?? ''), ENT_QUOTES, 'UTF-8');
        if ($patient === '') {
            $patient = '<em class="text-muted">—</em>';
        }

        // Description : on utilise les prestations réellement enregistrées dans le JSON.
        $description_raw = $this->_extract_descriptions($row['entries_json'] ?? '', $row['description'] ?? '');
        $description = htmlspecialchars($description_raw, ENT_QUOTES, 'UTF-8');

        // Date
        $date = '';
        if (!empty($row['date_paiement'])) {
            $ts   = strtotime($row['date_paiement']);
            $date = $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($row['date_paiement'], ENT_QUOTES, 'UTF-8');
        }

        // Mode de paiement
        $mode = htmlspecialchars($row['mode_paiement'] ?? '', ENT_QUOTES, 'UTF-8');

        // Montant : calculé à partir du JSON réel de la ligne.
        $montant_value = $this->_calculate_row_amount($row);
        $montant       = number_format($montant_value, 0, ',', ' ');
        $montant_html  = '<span class="montant-cell">' . $montant . ' FCFA</span>';

        // Statut
        $statut       = htmlspecialchars($row['statut'] ?? '', ENT_QUOTES, 'UTF-8');
        $statut_class = $this->_get_statut_class($row['statut'] ?? '');
        $statut_html  = $statut !== ''
            ? '<span class="badge ' . $statut_class . '">' . $statut . '</span>'
            : '<em class="text-muted">—</em>';

        // Lien de détail (adapter selon les routes du projet)
        if ($row['source'] === 'CONSULTATION') {
            $detail_url = base_url('Invoice/invoice_print/' . (int)$row['id']);
        } else {
            $detail_url = base_url('InvoiceExamenCaisse/examenCaisse_print/' . (int)$row['id']);
        }
        $action_html = '<a href="' . $detail_url . '" target="_blank" class="btn btn-xs btn-info" title="Voir / imprimer">'
            . '<i class="fa fa-print"></i>'
            . '</a>';

        return [
            $source_html,
            $numero,
            $patient,
            $description,
            $date,
            $mode,
            $montant_html,
            $statut_html,
            $action_html,
        ];
    }

    /**
     * Calcule le montant réel d'une facture ou d'un examen depuis son JSON.
     * invoice  : net_amount puis amount
     * examen   : amountT puis montant
     * La quantité par défaut est 1 lorsque le champ n'est pas exploitable.
     */
    private function _calculate_row_amount($row)
    {
        // Le modèle fournit déjà le montant numérique migré. On l'utilise en
        // priorité pour éviter de recalculer tous les JSON à chaque requête.
        if (isset($row['montant']) && is_numeric($row['montant']) && (float)$row['montant'] > 0) {
            return (float)$row['montant'];
        }

        // Fallback pour les anciennes lignes dont le montant migré est nul.
        $json = isset($row['entries_json']) ? $row['entries_json'] : '';
        if (!is_string($json) || trim($json) === '') {
            return 0;
        }

        $entries = json_decode($json, true);
        if (!is_array($entries)) {
            return 0;
        }

        $total = 0;
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            if ($row['source'] === 'CONSULTATION') {
                $value = isset($entry['net_amount']) && trim((string)$entry['net_amount']) !== ''
                    ? $entry['net_amount']
                    : (isset($entry['amount']) ? $entry['amount'] : 0);
                $qty = isset($entry['qte_consult']) && is_numeric($entry['qte_consult']) && (float)$entry['qte_consult'] > 0
                    ? (float)$entry['qte_consult'] : 1;
            } else {
                $value = isset($entry['amountT']) && trim((string)$entry['amountT']) !== ''
                    ? $entry['amountT']
                    : (isset($entry['montant']) ? $entry['montant'] : 0);
                $qty = isset($entry['qte_examen']) && is_numeric($entry['qte_examen']) && (float)$entry['qte_examen'] > 0
                    ? (float)$entry['qte_examen'] : 1;
            }

            $total += $this->_parse_money_value($value) * $qty;
        }

        return $total;
    }

    /** Normalise les montants historiques (espaces, virgules, points). */
    private function _parse_money_value($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $s = trim((string)$value);
        $s = str_replace(array("\xC2\xA0", ' '), '', $s);
        if ($s === '') {
            return 0;
        }

        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $lastComma = strrpos($s, ',');
            $lastDot = strrpos($s, '.');
            if ($lastComma > $lastDot) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
            }
        } elseif (strpos($s, ',') !== false) {
            $parts = explode(',', $s);
            if (count($parts) === 2 && strlen($parts[1]) !== 3) {
                $s = $parts[0] . '.' . $parts[1];
            } else {
                $s = str_replace(',', '', $s);
            }
        } elseif (strpos($s, '.') !== false) {
            $parts = explode('.', $s);
            if (count($parts) === 2 && strlen($parts[1]) === 3 && ctype_digit($parts[0]) && ctype_digit($parts[1])) {
                $s = $parts[0] . $parts[1];
            }
        }

        return is_numeric($s) ? (float)$s : 0;
    }

    /**
     * Extrait les descriptions des prestations depuis invoice_entries / examen_entries.
     * Les anciennes lignes peuvent ne pas contenir les champs de quantité.
     */
    private function _extract_descriptions($json, $fallback)
    {
        if (!is_string($json) || trim($json) === '') {
            return $fallback;
        }

        $entries = json_decode($json, true);
        if (!is_array($entries) || empty($entries)) {
            return $fallback;
        }

        $descriptions = [];
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $description = isset($entry['description']) ? trim((string)$entry['description']) : '';
            if ($description !== '') {
                $descriptions[] = $description;
            }
        }

        return !empty($descriptions) ? implode(' + ', array_unique($descriptions)) : $fallback;
    }

    /**
     * Retourne la classe Bootstrap pour un statut donné.
     *
     * @param  string $statut
     * @return string
     */
    private function _get_statut_class($statut)
    {
        $statut_lower = strtolower($statut);
        if (strpos($statut_lower, 'pay') !== false || strpos($statut_lower, 'valid') !== false) {
            return 'badge-success';
        }
        if (strpos($statut_lower, 'attent') !== false || strpos($statut_lower, 'pend') !== false) {
            return 'badge-warning';
        }
        if (strpos($statut_lower, 'annul') !== false || strpos($statut_lower, 'cancel') !== false) {
            return 'badge-danger';
        }
        return 'badge-secondary';
    }
}
