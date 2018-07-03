<?php
namespace Library\Logic;

use LogicAbstract;

class Transaction extends \Library\Logic\LogicAbstract
{
    static public function get($id=null, $transaction_status=null, $transaction_type=null, $offset=0, $limit=null)
    {
        $Repository = new \Library\Repository\Transaction();

        $Repository = $Repository->get($id, $transaction_status, $transaction_type, $offset, $limit);

        return $Repository;
    }

}
