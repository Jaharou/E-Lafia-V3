<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImpressionControllerVente extends CI_Controller {
    public function impression() {
        $this->load->view('show_vente');
    }
}
