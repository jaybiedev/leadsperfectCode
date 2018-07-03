<?php

namespace Model;

/**
 * Entity-Model
 * Class Menu
 * @package Model
 */

class Menu extends \Model\AbstractModel {

    public $table = "menu";

    public $id;
    public $menu;
    public $path;
    public $slug;
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