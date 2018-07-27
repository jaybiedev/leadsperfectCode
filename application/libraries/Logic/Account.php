<?php
namespace Library\Logic;

use LogicAbstract;

class Account extends \Library\Logic\LogicAbstract
{
    static public function get($id=null)
    {
        $Repository = new \Library\Repository\Account();

        return $Repository->get($id);
    }

    static public function update($meta=array())
    {
        if (empty($meta))
            return;

        $Account = new \Model\Account();
        $Account->load($meta);

        $DataObject = new \Library\DataObject($Account);
        $DataObject->save($meta);

        return $DataObject->getId();

    }
    
    static public function getAccountsByUserId($user_id) {
        $Repository = new \Library\Repository\Account();
        return $Repository->getAccountsByUserId($user_id);        
    }

}
