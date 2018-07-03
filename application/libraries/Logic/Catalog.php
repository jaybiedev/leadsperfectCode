<?php
namespace Library\Logic;

use LogicAbstract;

class Catalog extends \Library\Logic\LogicAbstract
{
    static public function get($id=null)
    {
        $Repository = new \Library\Repository\Catalog();

        return $Repository->get($id);
    }

}
