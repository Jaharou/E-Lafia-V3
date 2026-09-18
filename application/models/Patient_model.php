<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Patient_model
 *
 * Recherche et pagination côté serveur pour la table `patient`.
 * Suit le même pattern que Payment_model (voir Paiements.php /
 * historique_paiement.js) afin de rester cohérent avec le reste du projet :
 *   - count_all()           -> nombre total d'enregistrements
 *   - count_filtered($p)    -> nombre après recherche
 *   - get_datatables($p)    -> page de résultats (recherche + tri + pagination)
 *   - autocomplete($term)   -> résultats légers pour Select2 (sélection rapide
 *                              d'un patient dans les formulaires d'examen,
 *                              de facture, de vente, de traitement, etc.)
 *
 * @package Gamadadi
 */
class Patient_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // La base n'est pas autochargée dans ce projet.
        $this->load->database();
    }

    /** Colonnes triables, indexées comme les colonnes du tableau côté vue. */
    private $sortable_columns = array(
        0 => 'patient_id',
        1 => 'name',
        2 => 'prenom',
        3 => 'phone',
        4 => 'sex',
        5 => 'age',
    );

    /** Colonnes autorisées pour la recherche texte (LIKE). */
    private $searchable_columns = array('patient_id', 'name', 'prenom', 'phone', 'civilite', 'profession');

    // ------------------------------------------------------------------
    // Liste paginée (DataTables server-side)
    // ------------------------------------------------------------------

    public function count_all()
    {
        return (int) $this->db->count_all('patient');
    }

    public function count_filtered($params)
    {
        $this->_apply_search($params);
        return (int) $this->db->count_all_results('patient');
    }

    public function get_datatables($params)
    {
        $this->db->select('patient_id, civilite, name, prenom, phone, sex, age, blood_group, profession, created_at');
        $this->_apply_search($params);

        $order_col = $this->_safe_order_column($params);
        $dir = (isset($params['order'][0]['dir']) && strtolower($params['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
        $this->db->order_by($order_col, $dir);

        $start = isset($params['start']) ? max(0, (int) $params['start']) : 0;
        $length = isset($params['length']) ? (int) $params['length'] : 25;
        if ($length < 1 || $length > 100) {
            $length = 25;
        }
        $this->db->limit($length, $start);

        return $this->db->get('patient')->result_array();
    }

    /**
     * Applique la clause WHERE de recherche (recherche globale DataTables)
     * sur le query builder courant. Une seule requête LIKE combinée par
     * colonne recherchable, ce qui reste rapide même sur une grande table
     * grâce à l'index sur patient_id et à la limite de résultats.
     */
    private function _apply_search($params)
    {
        $search = isset($params['search']['value']) ? trim($params['search']['value']) : '';
        if ($search === '') {
            return;
        }

        $this->db->group_start();
        foreach ($this->searchable_columns as $i => $col) {
            if ($i === 0) {
                $this->db->like($col, $search);
            } else {
                $this->db->or_like($col, $search);
            }
        }
        // Recherche également sur "nom prénom" combinés (ex: "diallo amina").
        // La valeur est échappée manuellement (guillemets inclus) via
        // $this->db->escape(), car le 3e paramètre `false` de or_where()
        // désactive l'échappement automatique habituel.
        $like_full_name = $this->db->escape('%' . $this->db->escape_like_str($search) . '%');
        $this->db->or_where("CONCAT(IFNULL(name,''),' ',IFNULL(prenom,'')) LIKE " . $like_full_name, NULL, FALSE);
        $this->db->group_end();
    }

    private function _safe_order_column($params)
    {
        $idx = isset($params['order'][0]['column']) ? (int) $params['order'][0]['column'] : 0;
        return isset($this->sortable_columns[$idx]) ? $this->sortable_columns[$idx] : 'patient_id';
    }

    // ------------------------------------------------------------------
    // Autocomplétion (Select2 ajax) — utilisée par tous les formulaires qui
    // ont besoin de rattacher un patient (examen, facture, vente, traitement...)
    // ------------------------------------------------------------------

    /**
     * Retourne une petite page de patients correspondant au terme saisi.
     * Volontairement limité (25 par défaut) : l'utilisateur affine sa
     * recherche plutôt que de charger des milliers d'options d'un coup.
     *
     * @param string $term
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function autocomplete($term = '', $limit = 20, $offset = 0)
    {
        $term = trim((string) $term);
        $this->db->select('patient_id, civilite, name, prenom, phone, age, sex');
        $this->db->from('patient');

        if ($term !== '') {
            $this->db->group_start();
            $this->db->like('name', $term);
            $this->db->or_like('prenom', $term);
            $this->db->or_like('phone', $term);
            // Recherche sur "nom prénom" combinés (ex: "diallo amina"). La
            // valeur est échappée explicitement via $this->db->escape() (et
            // non par le 3e paramètre de or_where, qui désactiverait tout
            // échappement) avant d'être insérée dans la clause LIKE brute.
            $like_full_name = $this->db->escape('%' . $this->db->escape_like_str($term) . '%');
            $this->db->or_where("CONCAT(IFNULL(name,''),' ',IFNULL(prenom,'')) LIKE " . $like_full_name, NULL, FALSE);
            // Permet aussi de retrouver un patient par son numéro (ex: "19245")
            if (ctype_digit($term)) {
                $this->db->or_where('patient_id', (int) $term);
            }
            $this->db->group_end();
        }

        $this->db->order_by('patient_id', 'DESC');
        $this->db->limit($limit + 1, $offset); // +1 pour savoir s'il reste des résultats (pagination Select2)

        return $this->db->get()->result_array();
    }

    public function find_by_id($patient_id)
    {
        return $this->db->get_where('patient', array('patient_id' => $patient_id))->row_array();
    }
}
