<?php

namespace Library\Repository;

class Category extends \Library\Repository\RepositoryAbstract {

    public $model = 'Category';

    public function getCategories($path)
    {

        $this->sql = "SELECT * 
                FROM 
                  category
                WHERE
                  \"path\" ~ '{$path}.*'
                  AND enabled
                ORDER BY path";

        return $this;
    }
}