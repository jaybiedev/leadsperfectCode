<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class Content extends \Library\Logic\LogicAbstract
{

    static function getBySlug($slug) {
        $Repository = new \Library\Repository\Leads\Content();

        $Content = $Repository->getBySlug($slug);
        return $Content->getOne();
    }
}