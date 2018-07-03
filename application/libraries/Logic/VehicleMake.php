<?php
namespace Library\Logic;

use LogicAbstract;

class VehicleMake extends \Library\Logic\LogicAbstract
{
    static public function get($id=null, $offset=0, $limit=null)
    {
        $Repository = new \Library\Repository\VehicleMake();

        if ($offset || $limit)
            $Repository = $Repository->getWithCount($id, $offset, $limit);
        else
            $Repository = $Repository->get($id);

        return $Repository;
    }

}
