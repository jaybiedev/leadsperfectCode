<?php
namespace Library\Logic;

use LogicAbstract;

class User extends \Library\Logic\LogicAbstract
{
    static public function get($id=null)
    {
        $Repository = new \Library\Repository\User();

        return $Repository->get($id);
    }

    static public function update($meta=array())
    {
        if (empty($meta))
            return;

        $User = new \Model\User();
        $User->load($meta);

        $DataObject = new \Library\DataObject($User);
        $DataObject->save($meta);

        return $DataObject->getId();

    }

}
