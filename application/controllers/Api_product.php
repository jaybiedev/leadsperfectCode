<?php

use Library\RESTController;

class Api_product extends Library\RESTController {

    public function index() {
        die('API');
        $this->load->helper('url');
        $this->load->view('api/manager');
    }

    /**
     * get product by Id
     */
    public function id_get() {
        echo " Id  is : "  . $this->get('id');
    }


    /**
     * get product by Barcode
     */
    public function barcode_get() {
        echo $this->get('barcode');
        die('barcode');
    }

}
