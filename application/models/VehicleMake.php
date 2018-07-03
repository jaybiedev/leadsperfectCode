<?php

namespace Model;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class VehicleMake extends \Model\AbstractModel {


    public $id;
    public $name;
    public $enabled;
    public $year_model_count;


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}