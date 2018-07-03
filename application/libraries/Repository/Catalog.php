<?php

namespace Library\Repository;

class Catalog extends \Library\Repository\RepositoryAbstract {

    public $model = 'Catalog';

    public function get($id=null)
    {

        $this->sql = "SELECT * 
                FROM 
                  catalog
                WHERE 1=1 ";

        if (!empty($id)) {
            $this->sql .= " id=" . intval($id);
        }
        else {
            $this->sql .= " ORDER BY LOWER(name)";
        }

        return $this;
    }
}