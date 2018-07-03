<?php

/**
 * Class Pgsales
 * Dashboard for PG version of Perfect Sales
 */
class Sales extends Library\MainController
{

    public function index()
    {
        $data = array();
        $this->View->render('dashboard/sales.tpl', $data);
    }
}
