<?php
/*  
 *  Class for INVOICE
 *  @author : Raju Ahmed
 *  Date    : 20 August, 2017
 */
if ( ! defined( 'BASEPATH' ) ) {
    exit( 'Direct script access denied.' );
}
class Payment extends CI_Controller {

    public function insert_payments($task = "", $param2 = "") {
    if ($this->input->server('REQUEST_METHOD') == 'POST') {
        $this->load->database();

        // Extract data from the invoice table
        $invoice_query = $this->db->query("
            SELECT 
                i.invoice_id,
                p.name,
                p.prenom,
                i.creation_datetime AS payment_date,
                JSON_UNQUOTE(JSON_EXTRACT(i.invoice_entries, '$[0].description')) AS description,
                JSON_UNQUOTE(JSON_EXTRACT(i.invoice_entries, '$[0].amount')) AS amount,
                i.status AS payment_status
            FROM 
                invoice i
            JOIN
                patient p ON i.patient_id = p.patient_id
        ");
        $invoices = $invoice_query->result_array();

        // Extract data from the examen table
        $examen_query = $this->db->query("
            SELECT 
                e.id_examen,
                p.name,
                p.prenom,
                e.creation_time AS payment_date,
                e.status AS payment_status,
                JSON_UNQUOTE(JSON_EXTRACT(e.examen_entries, '$[0].description')) AS description,
                JSON_UNQUOTE(JSON_EXTRACT(e.examen_entries, '$[0].montant')) AS amount
            FROM 
                examen e
            JOIN
                patient p ON e.patient_id = p.patient_id
        ");
        $examens = $examen_query->result_array();

        // Insert data into the payment table
        foreach ($invoices as $invoice) {
            $existing_payment_query = $this->db->query("
                SELECT * FROM payment
                WHERE  name = ? 
                AND prenom = ?
                AND payment_date = ?
                AND description = ?
                AND amount = ?
                AND payment_status = ?", array(
                    $invoice['name'],
                    $invoice['prenom'],
                    $invoice['payment_date'],
                    $invoice['description'],
                    $invoice['amount'],
                    $invoice['payment_status']
                )
            );

            if ($existing_payment_query->num_rows() == 0) {
                $data = array(
                    'name' => $invoice['name'],
                    'prenom' => $invoice['prenom'],
                    'payment_date' => $invoice['payment_date'],
                    'description' => $invoice['description'],
                    'amount' => $invoice['amount'],
                    'payment_status' => $invoice['payment_status']
                );
                $this->db->insert('payment', $data);
            }
        }

        foreach ($examens as $examen) {
            $existing_payment_query = $this->db->query("
                SELECT * FROM payment 
                WHERE name = ? 
                AND prenom = ?
                AND payment_date = ?
                AND description = ?
                AND amount = ?
                AND payment_status = ?", array(
                    $examen['name'],
                    $examen['prenom'],
                    $examen['payment_date'],
                    $examen['description'],
                    $examen['amount'],
                    $examen['payment_status']
                )
            );

            if ($existing_payment_query->num_rows() == 0) {
                $data = array(
                    'name' => $examen['name'],
                    'prenom' => $examen['prenom'],
                    'payment_date' => $examen['payment_date'],
                    'description' => $examen['description'],
                    'amount' => $examen['amount'],
                    'payment_status' => $examen['payment_status']
                );
                $this->db->insert('payment', $data);
            }
        }
    }

    if ($task == 'add') {
        $page_data['page_name'] = 'add_payment';
        $page_data['page_title'] = get_phrase('paiement');
        $this->load->view('backend/index', $page_data);
    } elseif ($task == 'edit') {
        $page_data['page_name'] = 'edit_payment';
        $page_data['param2'] = $param2;
        $page_data['page_title'] = get_phrase('paiement');
        $this->load->view('backend/index', $page_data);
    } else {
        $data['invoice_info'] = $this->crud_model->select_invoice_info();
        $data['page_name'] = 'manage_journal_consult';
        $data['page_title'] = get_phrase('journal');
        $this->load->view('backend/index', $data);
    }

    if ($task == "create") {
            //$this->crud_model->create_insert_payments();
            $this->session->set_flashdata('message', get_phrase("l'informations a été enregistrées avec succès"));
            redirect(base_url() . 'payment/manage_journal_consult');
        }

    if ($this->session->userdata('receptionist_login') != 1) {
        $this->session->set_userdata('last_page', current_url());
        redirect(base_url(), 'refresh');
    }
}


}
