<?php

namespace Model;

/**
 * Entity-Model
 * Class Category
 * @package Model
 */

class Category extends \Model\AbstractModel {

    public $table = "category";

    public $id;
    public $category;
    public $path;
    public $enabled;
    public $children;


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}