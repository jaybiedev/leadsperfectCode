<?php
namespace Library\Logic;

use LogicAbstract;

class Cache extends \Library\Logic\LogicAbstract
{
    static public function get($id=null)
    {
        $Repository = new \Library\Repository\Cache();

        return $Repository->get($id);
    }

    static public function update($meta=array())
    {
        if (empty($meta))
            return;

        $Cache = new \Model\Cache();
        $Cache->load($meta);

        $DataObject = new \Library\DataObject($Cache);
        $DataObject->save($meta);

        return $DataObject->getId();

    }
    
    static public function getByAccountId($identifier, $account_id) {
        $Repository = new \Library\Repository\Cache();
        return $Repository->getByAccountId($identifier, $account_id);        
    }

}
