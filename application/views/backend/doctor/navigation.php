<?php
/* 	
* 	Tamplate: Navigation
* 	@author : Raju Ahmed
* 	Date	: 20 August, 2021
*/
if ( ! defined( 'BASEPATH' ) ) {
exit( 'Direct script access denied.' );
}
?>
<?php $output = ''; ?>
<?php ob_start(); ?>

<div class="left-sidebar bg-black-300 box-shadow ">
	<!--<br>
	<div class="col-md-3">
				<img src="</?php echo base_url(); ?>uploads/doctor_image/13.jpg" alt="</?php echo $system_name; ?>" class="logo" style="height: 100px;width: 190px; white-space: normal;">
				<span>Nom d'utilisateur</span>
	</div>
	<br><br><br><br><br><br>-->
	<div class="sidebar-content">
		
		<div class="sidebar-nav">
			
			<ul class="side-nav color-gray">
				<div class="sui-normal"><br>
						<a href="#" class="user-link">
							<img src="<?php echo base_url(); ?>uploads/doctor_image/19.jpg" alt="2" class="img-circle" style="height:180px;width: 160px; white-space: normal; margin-bottom: 20px;">

							<span><?php echo get_phrase('Bienvenue'); ?>,</span>
							<strong><?php
								echo $this->db->get_where($this->session->userdata('login_type'), array($this->session->userdata('login_type') . '_id' =>
									$this->session->userdata('login_user_id')))->row()->name;
								?>
							</strong>
						</a>
					</div>
				
				<li class="<?php if ($page_name == 'dashboard') echo 'active'; ?>">
					<a href="<?php echo base_url(); ?>doctor">
					<i class="fa fa-list"></i>
					<span><?php echo get_phrase('tableau de bord'); ?></span>
					</a>                                       
				</li>
				<li class="<?php if ($page_name == 'manage_appointment') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>doctor/appointment"><i class="fa fa-dashboard"></i>
					<span><?php echo get_phrase('rendez-vous'); ?></span>					
					</a>				   
				</li>

                 <li class="<?php if ($page_name == 'manage_invoice') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>doctor/invoice_manage">
					<i class="fa fa-user"></i>
					<span><?php echo get_phrase('réception'); ?></span>	
					</a>	
				</li>
				<li class="<?php if ($page_name == 'manage_examen') echo 'active'; ?>">
					<a href="<?php echo base_url(); ?>doctor/examen">
						<i class="fa fa-flask"></i>
						<span><?php echo get_phrase('examen'); ?></span>
					</a>
				</li>
				<li class="has-children <?php if ($page_name == 'manage_appointment' || $page_name == 'manage_requested_appointment') 
            echo 'open active';?>">
					<a href="#"><i class="fa fa-file-text"></i> <span><?php echo get_phrase('ordonnances'); ?></span> <i class="fa fa-angle-right arrow"></i></a>
					<ul class="child-nav">
						<li class="<?php if ($page_name == 'manage_prescription' && $menu_check == 'from_prescription') echo 'active'; ?>">
							<a href="<?php echo base_url(); ?>doctor/prescription">
							<i class="fa fa-file-text"></i>
							<span><?php echo get_phrase('ordonnance-médicale'); ?></span>					
							</a>	   
						</li>
						<li class="<?php if ($page_name == 'manage_medicine') echo 'active'; ?> ">
							<a href="<?php echo base_url(); ?>doctor/medicine">
							<i class="fa fa-medkit"></i>
							<span><?php echo get_phrase('produit'); ?></span>
							</a>
				        </li>					
																	
						<!--<li class="</?php if ($page_name == 'manage_requested_appointment') echo 'active'; ?> ">
							<a href="</?php echo base_url(); ?>doctor/appointment_requested">
							<i class="fa fa-signal"></i>
							<span></?php echo get_phrase('liste-de-rendez-vous'); ?></span>					
							</a>				   
						</li>-->
					</ul>
				</li>

                <!--<li class="</?php if ($page_name == 'manage_type') echo 'active'; ?>">
					<a href="</?php echo base_url(); ?>doctor/type">
					<i class="fa fa-file-text"></i>
					<span></?php echo get_phrase('ordonnance_type'); ?></span>					
					</a>				   
				</li>

				<li class="</?php if ($page_name == 'manage_patient' ||
            ($page_name == 'manage_prescription' && $menu_check == 'from_patient')) echo 'active'; ?>">
					<a href="</?php echo base_url(); ?>doctor/bed_allotment">
					<i class="fa fa-file-text"></i>
					<span></?php echo get_phrase('attribution de lit'); ?></span>					
					</a>				   
				</li>								
				<li class="</?php if ($page_name == 'show_blood_bank') echo 'active'; ?>">
					<a href="</?php echo base_url(); ?>doctor/blood_bank">
					<i class="fa fa-file-text"></i>
					<span></?php echo get_phrase('banque du sang'); ?></span>					
					</a>				   
				</li>-->												
				<li class="<?php if ($page_name == 'manage_report') echo 'active'; ?>">
					<a href="<?php echo base_url(); ?>doctor/report">
					<i class="fa fa-file-text"></i>
					<span><?php echo get_phrase('rapport'); ?></span>					
					</a>				   
				</li>																			
				<li class="<?php if ($page_name == 'edit_profile') echo 'active'; ?>">
					<a href="<?php echo base_url(); ?>doctor/profile">
					<i class="fa fa-user-md"></i>
					<span><?php echo get_phrase('profil'); ?></span>					
					</a>				   
				</li>
			</ul>
		</div>
	</div>
</div>
<?php 
$output .= ob_get_clean();
echo $output;