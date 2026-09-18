<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImpressionController extends CI_Controller {
    public function impression() {
        $this->load->view('show_prescription');
    }
}
