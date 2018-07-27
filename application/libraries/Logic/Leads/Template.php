<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class Template extends \Library\Logic\LogicAbstract
{

    static function get($id) {
        $Repository = new \Library\Repository\Leads\Template();

        return $Repository->get($id)->getOne();
    }
    
    static function getByAccount($account_id) {
        $Repository = new \Library\Repository\Leads\Template();
        
        return $Repository->getByAccount($account_id);
    }
    
}