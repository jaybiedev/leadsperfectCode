<?php

namespace Library\Repository;

class Account extends \Library\Repository\RepositoryAbstract {

    public $model = 'Account';

    public function get($id=null)
    {

        $this->sql = "SELECT * 
                FROM 
                  account
                WHERE 1=1 ";

        if (!empty($id)) {
            $this->sql .= " AND id=" . intval($id);
        }
        else {
            $this->sql .= " ORDER BY LOWER(last_name), LOWER(first_name)";
        }

        return $this;
    }
}