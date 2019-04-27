<?php

namespace Library\Repository;

class Cache extends \Library\Repository\RepositoryAbstract {

    public $model = 'Cache';

    public function getByAccountId($identifier, $account_id) {
        
        $this->sql = "SELECT
                        cache.*
                    FROM
                        cache
                    WHERE 
                        cache.identifier='{$identifier}'
                        AND cache.account_id=" . intval($account_id);
        return $this;
    }
}