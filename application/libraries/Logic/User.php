<?php
namespace Library\Logic;

use LogicAbstract;

class User extends \Library\Logic\LogicAbstract
{
    static public function get($id)
    {
        $Repository = new \Library\Repository\User();

        return $Repository->get($id)->getOne();
    }

    static public function getByEmail($email)
    {
        $Repository = new \Library\Repository\User();
        
        return $Repository->getByEmail($email)->getOne();;
    }
    

    static public function update($meta=array())
    {
        if (empty($meta))
            return;

        $User = new \Model\User();
        $User->load($meta);

        $DataObject = new \Library\DataObject($User);
        $DataObject->save($meta);

        $User->load($DataObject->getMeta());
        
        return $User;
    }

}
