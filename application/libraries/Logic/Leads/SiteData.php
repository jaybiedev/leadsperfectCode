<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class SiteData extends \Library\Logic\LogicAbstract
{

    static function getAll($site_id) {
        $Repository = new \Library\Repository\Leads\SiteData();

        return $Repository->getAll($site_id);
    }

    static function getByField($site_id, $field) {
        $Repository = new \Library\Repository\Leads\SiteData();
        
        return $Repository->getByField($site_id, $field);
    }
    
}