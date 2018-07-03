<?php

use Library\Logic\Menu;
use Library\Security;

class Pensioner extends Library\MainController {

    public function index()
    {
        //error_reporting(E_ALL);
        error_reporting(0);

        $menuArray = Library\Logic\Menu::getMenu('Top.Pensioner');

        $data = array('menu'=>$menuArray);

        ob_start();
        require_once(__DIR__ . "/legacy/penlogin/index.php");
        $data['contents'] = ob_get_clean();
        ob_start();

        $this->View->render( 'finance/legacy.tpl', $data);
    }

}
