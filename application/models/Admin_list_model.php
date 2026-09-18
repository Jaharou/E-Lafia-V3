<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modèle générique pour les listes de l'administration en mode
 * "server-side DataTables" : la recherche, le tri et la pagination sont
 * effectués par MySQL (LIMIT/WHERE/ORDER BY) au lieu de charger toute la
 * table en PHP puis de la filtrer dans le navigateur.
 *
 * Un seul modèle sert les quatre grandes listes (patients, examens,
 * consultations, traitements) via la configuration $entities ci-dessous,
 * pour éviter quatre copies de la même mécanique.
 */
class Admin_list_model extends CI_Model
{
    /**
     * Configuration par entité.
     *  - table    : table principale
     *  - pk       : clé primaire (aussi le tri par défaut, décroissant)
     *  - select   : colonnes renvoyées
     *  - joins    : jointures éventuelles (LEFT JOIN pour ne jamais masquer
     *               une ligne dont le patient a été supprimé)
     *  - search   : colonnes sur lesquelles porte la recherche globale
     *  - order    : colonnes triables, dans l'ordre des colonnes DataTables
     */
    private $entities = array(

        'patient' => array(
            'table'  => 'patient',
            'pk'     => 'patient.patient_id',
            'select' => 'patient.patient_id, patient.name, patient.prenom, patient.phone, patient.email',
            'joins'  => array(),
            'search' => array('patient.name', 'patient.prenom', 'patient.phone', 'patient.email'),
            'order'  => array('patient.patient_id', null, 'patient.name', 'patient.phone', 'patient.email', null),
        ),

        'examen' => array(
            'table'  => 'examen',
            'pk'     => 'examen.id_examen',
            'select' => 'examen.id_examen, examen.examen_number, examen.creation_time, examen.statut_examen,
                         examen.status, examen.total_amount, examen.examen_entries,
                         patient.name, patient.prenom',
            'joins'  => array(
                array('patient', 'patient.patient_id = examen.patient_id', 'left'),
            ),
            'search' => array('examen.examen_number', 'patient.name', 'patient.prenom', 'examen.statut_examen'),
            'order'  => array('examen.id_examen', 'examen.examen_number', 'patient.name',
                              'examen.creation_time', 'examen.total_amount', 'examen.statut_examen', null),
        ),

        'invoice' => array(
            'table'  => 'invoice',
            'pk'     => 'invoice.invoice_id',
            'select' => 'invoice.invoice_id, invoice.invoice_number, invoice.creation_datetime,
                         invoice.status, invoice.net_amount, invoice.total_amount, invoice.invoice_entries,
                         patient.name, patient.prenom',
            'joins'  => array(
                array('patient', 'patient.patient_id = invoice.patient_id', 'left'),
            ),
            'search' => array('invoice.invoice_number', 'patient.name', 'patient.prenom', 'invoice.status'),
            'order'  => array('invoice.invoice_id', 'invoice.invoice_number', 'patient.name',
                              'invoice.creation_datetime', 'invoice.total_amount', 'invoice.status', null),
        ),

        'traitement' => array(
            'table'  => 'traitement',
            'pk'     => 'traitement.traitement_id',
            'select' => 'traitement.traitement_id, traitement.traitement_number, traitement.date_traitement,
                         traitement.status, traitement.mode_paiement, traitement.traitement_entries,
                         patient.name, patient.prenom',
            'joins'  => array(
                array('patient', 'patient.patient_id = traitement.patient_id', 'left'),
            ),
            'search' => array('traitement.traitement_number', 'patient.name', 'patient.prenom', 'traitement.status'),
            'order'  => array('traitement.traitement_id', 'traitement.traitement_number', 'patient.name',
                              'traitement.date_traitement', 'traitement.status', null),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /** Renvoie la configuration d'une entité, ou FALSE si elle est inconnue. */
    public function config($entity)
    {
        return isset($this->entities[$entity]) ? $this->entities[$entity] : FALSE;
    }

    /** Applique FROM + JOIN. */
    private function _base($cfg)
    {
        $this->db->from($cfg['table']);
        foreach ($cfg['joins'] as $join) {
            $this->db->join($join[0], $join[1], $join[2]);
        }
    }

    /**
     * Recherche globale DataTables. La valeur passe par like() de CodeIgniter,
     * qui échappe la chaîne : pas de concaténation SQL manuelle ici.
     */
    private function _search($cfg, $search)
    {
        $search = trim((string) $search);
        if ($search === '') {
            return;
        }
        $this->db->group_start();
        foreach ($cfg['search'] as $i => $column) {
            if ($i === 0) {
                $this->db->like($column, $search);
            } else {
                $this->db->or_like($column, $search);
            }
        }
        $this->db->group_end();
    }

    /** Nombre total de lignes, sans filtre. */
    public function count_all($entity)
    {
        $cfg = $this->config($entity);
        if (!$cfg) return 0;
        return (int) $this->db->count_all($cfg['table']);
    }

    /** Nombre de lignes correspondant à la recherche en cours. */
    public function count_filtered($entity, $params)
    {
        $cfg = $this->config($entity);
        if (!$cfg) return 0;
        $this->_base($cfg);
        $this->_search($cfg, isset($params['search']['value']) ? $params['search']['value'] : '');
        return (int) $this->db->count_all_results();
    }

    /** Page de résultats demandée par DataTables. */
    public function get_datatables($entity, $params)
    {
        $cfg = $this->config($entity);
        if (!$cfg) return array();

        $this->db->select($cfg['select'], FALSE);
        $this->_base($cfg);
        $this->_search($cfg, isset($params['search']['value']) ? $params['search']['value'] : '');

        // Tri : on n'accepte QUE les colonnes listées dans la configuration.
        // Un index envoyé par le client ne peut donc jamais injecter de SQL.
        $ordered = FALSE;
        if (!empty($params['order'][0])) {
            $idx = (int) $params['order'][0]['column'];
            $dir = (isset($params['order'][0]['dir']) && strtolower($params['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
            if (isset($cfg['order'][$idx]) && $cfg['order'][$idx] !== null) {
                $this->db->order_by($cfg['order'][$idx], $dir);
                $ordered = TRUE;
            }
        }
        if (!$ordered) {
            $this->db->order_by($cfg['pk'], 'DESC');
        }

        $length = isset($params['length']) ? (int) $params['length'] : 25;
        $start  = isset($params['start']) ? (int) $params['start'] : 0;
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        return $this->db->get()->result_array();
    }
}
