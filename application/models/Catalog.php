<?php

namespace Model;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Account extends \Model\AbstractModel {


    public $id;
    public $name;
    public $desciption;
    public $price;
    public $is_inventory;
    public $balance;


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}