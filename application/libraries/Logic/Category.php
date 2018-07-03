<?php
namespace Library\Logic;

use LogicAbstract;

class Category extends \Library\Logic\LogicAbstract
{
    static public function getCategories($top='Top')
    {
        $Repository = new \Library\Repository\Category();

        return $Repository->getCategories($top);
    }


    static public function getCategory($id)
    {
        //
    }
}
