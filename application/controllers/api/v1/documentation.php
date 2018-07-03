<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Documentation extends CI_Controller {
    public function index()
    {
        $this->load->helper('url');
        $this->load->view('api_v1_documentation');
    }
}