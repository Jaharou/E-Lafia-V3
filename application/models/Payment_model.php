<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // La base n'est pas autochargée dans ce projet.
        $this->load->database();
    }

    private $sortable_columns = array(
        0 => 'source', 1 => 'numero', 2 => 'patient_nom', 3 => 'description',
        4 => 'date_paiement', 5 => 'mode_paiement', 6 => 'montant', 7 => 'statut'
    );

    public function count_all()
    {
        return (int)$this->db->count_all('invoice') + (int)$this->db->count_all('examen');
    }

    public function count_filtered($params)
    {
        return $this->_count_source('invoice', $params) + $this->_count_source('examen', $params);
    }

    public function get_datatables($params)
    {
        $start = isset($params['start']) ? max(0, (int)$params['start']) : 0;
        $length = isset($params['length']) ? (int)$params['length'] : 25;
        if ($length < 1 || $length > 100) $length = 25;
        $needed = max(25, $start + $length);

        $rows = array_merge(
            $this->_fetch_source('invoice', $params, $needed),
            $this->_fetch_source('examen', $params, $needed)
        );

        $order_col = $this->_safe_order_column($params);
        $dir = (isset($params['order'][0]['dir']) && strtolower($params['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

        usort($rows, function($a, $b) use ($order_col, $dir) {
            $av = isset($a[$order_col]) ? $a[$order_col] : '';
            $bv = isset($b[$order_col]) ? $b[$order_col] : '';
            if ($order_col === 'montant') {
                $cmp = ((float)$av == (float)$bv) ? 0 : (((float)$av < (float)$bv) ? -1 : 1);
            } elseif ($order_col === 'date_paiement') {
                $at = $av ? strtotime($av) : 0;
                $bt = $bv ? strtotime($bv) : 0;
                $cmp = ($at == $bt) ? 0 : (($at < $bt) ? -1 : 1);
            } else {
                $cmp = strcasecmp((string)$av, (string)$bv);
            }
            if ($cmp === 0) {
                $ai = isset($a['id']) ? (int)$a['id'] : 0;
                $bi = isset($b['id']) ? (int)$b['id'] : 0;
                $cmp = ($ai == $bi) ? 0 : (($ai < $bi) ? -1 : 1);
            }
            return ($dir === 'ASC') ? $cmp : -$cmp;
        });

        return array_slice($rows, $start, $length);
    }

    /**
     * Données complètes pour impression selon période/recherche/source.
     * Contrairement à DataTables, cette méthode ne limite pas à 25 lignes.
     */
    public function get_print_rows($params, $source = 'all')
    {
        $rows = array();
        if ($source === 'all' || $source === 'invoice') {
            $rows = array_merge($rows, $this->_fetch_source('invoice', $params, 1000000));
        }
        if ($source === 'all' || $source === 'examen') {
            $rows = array_merge($rows, $this->_fetch_source('examen', $params, 1000000));
        }

        usort($rows, function($a, $b) {
            $at = !empty($a['date_paiement']) ? strtotime($a['date_paiement']) : 0;
            $bt = !empty($b['date_paiement']) ? strtotime($b['date_paiement']) : 0;
            if ($at === $bt) return ((int)$b['id'] <=> (int)$a['id']);
            return ($at < $bt) ? 1 : -1;
        });
        return $rows;
    }

    public function get_statistics($params)
    {
        $inv = $this->_sum_source('invoice', $params);
        $exa = $this->_sum_source('examen', $params);
        return array(
            'total_payments' => $inv['count'] + $exa['count'],
            'total_invoice' => $inv['amount'],
            'total_examen' => $exa['amount'],
            'total_amount' => $inv['amount'] + $exa['amount'],
            // Nombre de consultations / examens sur la période filtrée
            // (distinct des montants ci-dessus, qui sont des totaux en FCFA).
            'nb_invoice' => $inv['count'],
            'nb_examen' => $exa['count']
        );
    }

    private function _query($sql, $bindings)
    {
        $old = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $q = $this->db->query($sql, $bindings);
        $err = $this->db->error();
        $this->db->db_debug = $old;
        if ($q === FALSE) {
            throw new Exception('SQL '.$err['code'].' : '.$err['message'].' | '.$sql);
        }
        return $q;
    }

    private function _count_source($source, $params)
    {
        $c = $this->_build_conditions($params, $source);
        if ($source === 'invoice') {
            $sql = "SELECT COUNT(*) total FROM invoice i LEFT JOIN patient p ON p.patient_id=i.patient_id WHERE 1=1 {$c['where']}";
        } else {
            $sql = "SELECT COUNT(*) total FROM examen e LEFT JOIN patient p ON p.patient_id=e.patient_id WHERE 1=1 {$c['where']}";
        }
        $row = $this->_query($sql, $c['bindings'])->row();
        return $row ? (int)$row->total : 0;
    }

    private function _sum_source($source, $params)
    {
        $c = $this->_build_conditions($params, $source);
        if ($source === 'invoice') {
            $sql = "SELECT COUNT(*) total_count, COALESCE(SUM(CASE WHEN i.total_amount>0 THEN i.total_amount ELSE i.net_amount END),0) total_amount FROM invoice i LEFT JOIN patient p ON p.patient_id=i.patient_id WHERE 1=1 {$c['where']}";
        } else {
            $sql = "SELECT COUNT(*) total_count, COALESCE(SUM(e.total_amount),0) total_amount FROM examen e LEFT JOIN patient p ON p.patient_id=e.patient_id WHERE 1=1 {$c['where']}";
        }
        $row = $this->_query($sql, $c['bindings'])->row();
        return array('count'=>$row?(int)$row->total_count:0, 'amount'=>$row?(float)$row->total_amount:0);
    }

    private function _fetch_source($source, $params, $limit)
    {
        $c = $this->_build_conditions($params, $source);
        $limit = max(1, min((int)$limit, 1000));
        if ($source === 'invoice') {
            $sql = "SELECT 'CONSULTATION' source, i.invoice_id id, i.invoice_number numero,
                    TRIM(CONCAT(IFNULL(p.prenom,''),' ',IFNULL(p.name,''))) patient_nom,
                    i.invoice_entries entries_json, IFNULL(i.title,'Facture') description,
                    i.creation_datetime date_paiement, IFNULL(i.mode_paiement,'') mode_paiement,
                    CASE WHEN i.total_amount>0 THEN i.total_amount ELSE i.net_amount END montant,
                    IFNULL(i.status,'') statut
                    FROM invoice i LEFT JOIN patient p ON p.patient_id=i.patient_id
                    WHERE 1=1 {$c['where']}
                    ORDER BY i.creation_datetime DESC, i.invoice_id DESC LIMIT {$limit}";
        } else {
            $sql = "SELECT 'EXAMEN' source, e.id_examen id, e.examen_number numero,
                    TRIM(CONCAT(IFNULL(p.prenom,''),' ',IFNULL(p.name,''))) patient_nom,
                    e.examen_entries entries_json, 'Examen' description,
                    e.creation_time date_paiement, IFNULL(e.mode_paiement,'') mode_paiement,
                    IFNULL(e.total_amount,0) montant,
                    IFNULL(e.status,IFNULL(e.statut_examen,'')) statut
                    FROM examen e LEFT JOIN patient p ON p.patient_id=e.patient_id
                    WHERE 1=1 {$c['where']}
                    ORDER BY e.creation_time DESC, e.id_examen DESC LIMIT {$limit}";
        }
        return $this->_query($sql, $c['bindings'])->result_array();
    }

    private function _build_conditions($params, $source)
    {
        $where = '';
        $bindings = array();
        $date_col = ($source === 'invoice') ? 'i.creation_datetime' : 'e.creation_time';
        $range = $this->_resolve_date_range($params);
        if ($range) {
            $where .= " AND {$date_col} >= ? AND {$date_col} < ?";
            $bindings[] = $range['debut'];
            $bindings[] = $range['fin'];
        }
        $search = isset($params['search']['value']) ? trim($params['search']['value']) : '';
        if ($search !== '') {
            $like = '%'.$this->db->escape_like_str($search).'%';
            if ($source === 'invoice') {
                $where .= " AND (i.invoice_number LIKE ? OR p.name LIKE ? OR p.prenom LIKE ? OR i.mode_paiement LIKE ? OR i.status LIKE ? OR CONCAT(p.prenom,' ',p.name) LIKE ? OR i.invoice_entries LIKE ?)";
                for ($i=0;$i<7;$i++) $bindings[]=$like;
            } else {
                $where .= " AND (e.examen_number LIKE ? OR p.name LIKE ? OR p.prenom LIKE ? OR e.mode_paiement LIKE ? OR e.status LIKE ? OR e.statut_examen LIKE ? OR CONCAT(p.prenom,' ',p.name) LIKE ? OR e.examen_entries LIKE ?)";
                for ($i=0;$i<8;$i++) $bindings[]=$like;
            }
        }
        return array('where'=>$where,'bindings'=>$bindings);
    }

    private function _resolve_date_range($params)
    {
        $periode = isset($params['periode']) ? trim($params['periode']) : '';
        $debut = isset($params['date_debut']) ? trim($params['date_debut']) : '';
        $fin = isset($params['date_fin']) ? trim($params['date_fin']) : '';
        if ($periode==='today') return array('debut'=>date('Y-m-d').' 00:00:00','fin'=>date('Y-m-d',strtotime('+1 day')).' 00:00:00');
        if ($periode==='week') { $m=date('Y-m-d',strtotime('monday this week')); return array('debut'=>$m.' 00:00:00','fin'=>date('Y-m-d',strtotime($m.' +7 days')).' 00:00:00'); }
        if ($periode==='month') return array('debut'=>date('Y-m-01').' 00:00:00','fin'=>date('Y-m-d',strtotime('first day of next month')).' 00:00:00');
        if ($periode==='year') return array('debut'=>date('Y-01-01').' 00:00:00','fin'=>(date('Y')+1).'-01-01 00:00:00');
        if ($periode==='custom' && $this->_is_valid_date($debut) && $this->_is_valid_date($fin)) return array('debut'=>$debut.' 00:00:00','fin'=>date('Y-m-d',strtotime($fin.' +1 day')).' 00:00:00');
        return NULL;
    }

    private function _is_valid_date($date)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)) return FALSE;
        $p=explode('-',$date); return checkdate((int)$p[1],(int)$p[2],(int)$p[0]);
    }

    private function _safe_order_column($params)
    {
        $idx=isset($params['order'][0]['column'])?(int)$params['order'][0]['column']:4;
        return isset($this->sortable_columns[$idx])?$this->sortable_columns[$idx]:'date_paiement';
    }
}
