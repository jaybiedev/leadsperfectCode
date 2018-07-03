<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use \Library\MainController;


class Welcome extends Library\MainController {

    public function index()
    {
        // always require login
        if ($this->Helper->getSecurity()->IsLogged() == false) {
            redirect($this->Helper->getUrl()->getLoginUrl());
        }

        $data['admin'] = false;
        $this->View->setPageTitle("Home");
        $this->View->render( 'finance/home.tpl', $data);
    }


}
