<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class User extends \Library\Logic\LogicAbstract
{

    static function getAccounts($user_id) {
        $Repository = new \Library\Repository\Leads\User();

        return $Repository->getAccounts($user_id);            
    }
    
    static function getSites($account_id) {
        $Repository = new \Library\Repository\Leads\User();
        
        return $Repository->getSites($account_id);
    }
    
}