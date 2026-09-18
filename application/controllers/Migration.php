<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Migration extends CI_Controller
{


public function invoice_items()
{


// Sécurité temporaire
// À supprimer après utilisation

if($this->session->userdata('admin_login') != 1)
{
    exit('Accès interdit');
}



$this->load->database();



$invoices = $this->db
->select('invoice_id, invoice_entries')
->where('invoice_entries IS NOT NULL')
->get('invoice')
->result_array();



$count = 0;



foreach($invoices as $invoice)
{


$items = json_decode(
    $invoice['invoice_entries'],
    true
);



if(is_array($items))
{


foreach($items as $item)
{


$data=array(

'invoice_id'=>
$invoice['invoice_id'],


'description'=>
isset($item['description'])
?
$item['description']
:
'',


'amount'=>
isset($item['amount'])
?
$item['amount']
:
0


);



$this->db->insert(
'invoice_items',
$data
);


$count++;


}



}



}



echo "Migration terminée : ".$count." prestations transférées";


}



}