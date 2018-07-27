<?php

use Library\Logic\Menu;
use Library\Security;

class Formcontroller extends Library\MainController {

    public function index_get() {
        die("Ajax get index");
    }
    
    public function index()
    {
        //
    }


    public function catchall() {
        $segments = $this->uri->segment_array();

        $slug = strtolower(implode('/', $segments));


        echo "404 inajax";
        die('Catch All');
    }

}
