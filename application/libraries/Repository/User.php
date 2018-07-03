<?php

namespace Library\Repository;

class User extends \Library\Repository\RepositoryAbstract {

    public $model = 'User';

    public function get($id=null)
    {

        $this->sql = "SELECT * 
                FROM 
                  users
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