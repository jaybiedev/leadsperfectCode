<?php

namespace Library\Repository;

class User extends \Library\Repository\RepositoryAbstract {

    public $model = 'User';

    public function get($id=null)
    {

        $this->sql = "SELECT * 
                FROM 
                  user
                WHERE 1=1 ";

        if (!empty($id)) {
            $this->sql .= " AND id=" . intval($id);
        }
        else {
            $this->sql .= " ORDER BY LOWER(last_name), LOWER(first_name)";
        }

        return $this;
    }
    
    public function getByEmail($email)
    {
        $email = strtolower($email);
        $this->sql = "SELECT *
                FROM
                  user
                WHERE email=?";
        
        $this->bindings = array($email);
        return $this;
    }
    
    public function getAll($where=array(), $orderby=null)
    {        
        $this->sql = "SELECT *
                FROM
                  user
                WHERE enabled";
        
        if (empty($orderby)) {
            $orderby = " ORDER BY LOWER(last_name), LOWER(first_name)";
        }
        
        $this->sql .= $orderby;
        
        return $this;
    }
    
}