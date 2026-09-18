<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Facture_model extends CI_Model
{
    public function getConsultations($facture_id)
    {
        return $this->db->select('description, qte_consult, amount, net_amount')
                        ->from('consultations')
                        ->where('facture_id', $facture_id)
                        ->get()
                        ->result();
    }

    public function getExamens($facture_id)
    {
        return $this->db->select('description, qte_examen, montant, amountT')
                        ->from('examens')
                        ->where('facture_id', $facture_id)
                        ->get()
                        ->result();
    }

    public function getTraitements($facture_id)
    {
        return $this->db->select('description, qte, amount, prixTrait')
                        ->from('traitements')
                        ->where('facture_id', $facture_id)
                        ->get()
                        ->result();
    }
}
