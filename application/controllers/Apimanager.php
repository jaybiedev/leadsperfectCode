<?php

class Apimanager extends Library\MainController {

    public function index()
    {
        $this->load->helper('url');

        $this->render('api/manager');
    }
}
