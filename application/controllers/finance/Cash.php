<?php

use Library\Logic\Menu;
use Library\Security;

class Cash extends Library\MainController {

    public function index()
    {
        //error_reporting(E_ALL);
        error_reporting(0);

        $menuArray = Library\Logic\Menu::getMenu('Top.Cash');

        $data = array('menu'=>$menuArray);

        ob_start();
        require_once(__DIR__ . "/legacy/cash/index.php");
        $data['contents'] = ob_get_clean();
        ob_start();

        $this->View->render( 'jgm/legacy.tpl', $data);
    }

}
