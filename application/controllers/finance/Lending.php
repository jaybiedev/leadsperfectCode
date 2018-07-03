<?php

use Library\Logic\Menu;
use Library\Security;

class Lending extends Library\MainController {

    public function index()
    {
        // error_reporting(E_ALL);
        error_reporting(0);

        $menuArray = Library\Logic\Menu::getMenu('Top.Lending');

        $data = array('menu'=>$menuArray);

        ob_start();
        require_once(__DIR__ . "/legacy/lending/index.php");
        $data['contents'] = ob_get_clean();
        ob_start();

        $this->View->render( 'finance/legacy.tpl', $data);
    }

}
