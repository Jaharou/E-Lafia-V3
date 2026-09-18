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
	<div class="sidebar-content">
		<div class="sidebar-nav">		
			<ul class="side-nav color-gray">
				<li>
					<div class="sui-normal">
						<a href="#" class="user-link">
							<img src="<?php echo $this->crud_model->get_image_url($this->session->userdata('login_type'), $this->session->userdata('login_user_id'));?>" alt="" class="img-circle" style="height:44px;">

							<span><?php echo get_phrase('Bienvenue'); ?>,</span>
							<strong><?php
								echo $this->db->get_where($this->session->userdata('login_type'), array($this->session->userdata('login_type') . '_id' =>
									$this->session->userdata('login_user_id')))->row()->name;
								?>
							</strong>
						</a>
					</div>	
				</li>
				 <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>magasin/dashboard">
						<i class="fa fa-desktop"></i>
						<span><?php echo get_phrase('tableau-de-bord'); ?></span>
					</a>
				</li>
				<li class="<?php if ($page_name == 'manage_fournisseur') echo 'active'; ?> ">
				    <a href="<?php echo base_url(); ?>magasin/fournisseur">
						<i class="fa fa-user"></i>
						<span><?php echo get_phrase('fournisseur'); ?></span>
				    </a>
				</li>
				<li class="<?php if ($page_name == 'manage_medicine') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>magasin/medicine">
					<i class="fa fa-medkit"></i>
					<span><?php echo get_phrase('produit'); ?></span>
					</a>
		        </li>
		        <li class="<?php if ($page_name == 'manage_stock') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>magasin/stock">
						<i class="fa fa-light fa-cart-plus" aria-hidden="true" style="color: ; font-size: 20px;"></i>
						<span><?php echo get_phrase('stock'); ?></span>
					</a>
				</li>
				<li class="<?php if ($page_name == 'edit_profile') echo 'active'; ?> ">
					<a href="<?php echo base_url(); ?>magasin/profile">
						<i class="fa fa-user"></i>
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