<?php
/* 	
 * 	Tamplate: Manage Apointment
 * 	@author : Raju Ahmed
 * 	Date	: 20 August, 2021
 */
if ( ! defined( 'BASEPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>
	<div class="row">
		<div>
		<table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
		   <thead>
		       <tr>                                  
		           <th><?php echo get_phrase('numéro de reçu'); ?></th>
		           <th><?php echo get_phrase('patient'); ?></th>
		           <th><?php echo get_phrase('date_de_creation'); ?></th>
		           <th><?php echo get_phrase('type'); ?></th>
		           <th><?php echo get_phrase('statut'); ?></th>
		           <th><?php echo get_phrase('montant'); ?></th>
		           
		       </tr>
		   </thead>
		   <tbody>
		       <?php

           $this->db->order_by('invoice_id', 'DESC');
           $this->db->limit(5);
           $invoice_info   = $this->db->get('invoice')->result_array();  
           foreach ($invoice_info as $row):
           ?>   
           <tr>
               <td><?php echo $row['invoice_number'] ?></td>
               <td>
                   <?php $name = $this->db->get_where('patient' , array('patient_id' => $row['patient_id'] ))->row()->name;
                       echo $name;?>
               </td>
               <td><?php echo $row['creation_datetime'] ?></td>
               <td><?php echo $row['title'] ?></td>
               
               
               <td><?php echo $row['status'] ?></td>
               <td>
                   <?php $net_amount = $this->db->get_where('invoice' , array('invoice_id' => $row['invoice_id'] ))->row()->net_amount;
                       echo $net_amount;?>
               </td>
               <!--<td>
                   <a  href="</?php echo base_url(); ?>Invoice/invoice_print/</?php echo $row['invoice_id']; ?>" 
                       class="btn btn-default btn-sm btn-icon icon-left">
                       <!-<i class="fa fa-print"></i>
                       Voir le reçu
                   </a>
               </td>-->
           </tr>                           
           <?php endforeach; ?>
     </tbody>
   </table>
</div>				
</div>
<?php 
$output .= ob_get_clean();
echo $output;