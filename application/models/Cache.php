<?php

namespace Model;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Cache extends \Model\AbstractModel {

    public $table = "cache";

    public $id;
    public $account_id;
    public $identifier;
    public $date;
    public $value;
    public $enabled=true;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}