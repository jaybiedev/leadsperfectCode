<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class Site extends \Library\Logic\LogicAbstract
{

    static function getBySlug($slug) {
        $Repository = new \Library\Repository\Leads\Site();

        $Content = $Repository->getBySlug($slug);
        $Record = $Content->getOne();
            
        return $Record;
    }

    static function getByGuid($guid) {
        $Repository = new \Library\Repository\Leads\Site();
        
        $Content = $Repository->getByGuid($guid);
        $Record = $Content->getOne();
        
        return $Record;
    }
    
    
    /**
     * returns multiple
     */
    static function getByAccount($account_id) {
        $Repository = new \Library\Repository\Leads\Site();
        
        return $Repository->getByAccount($account_id);
    }
    
    /**
     * returns multiple
     */
    static public function getSitesByUserId($user_id) {
        $Repository = new \Library\Repository\Leads\Site();
        return $Repository->getSitesByUserId($user_id);        
    }
    
    // returns all sites based on account Id given user_id
    static public function getSitesByAccountUserId($user_id) {
        $Repository = new \Library\Repository\Leads\Site();
        return $Repository->getSitesByAccountUserId($user_id);
    }    
    
}