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
    
    /**
     * Strict enforce Id requiremetn
     */
    public function getById($id)
    {        
        $this->sql = "SELECT *
                FROM
                  account
                WHERE 
                    id=" . intval($id);
        return $this;
    }
    
    public function getAccountsByUserId($user_id) {
        
        $this->sql = "SELECT
                        account.*
                    FROM
                        account
                    JOIN
                        user_account_xref AS uax ON uax.account_id=account.id
                    WHERE 
                        account.enabled
                        AND uax.user_id=" . intval($user_id);
        return $this;
    }
}