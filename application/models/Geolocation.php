<?php

namespace Model;

class Geolocation extends \Model\AbstractModel {

    public $id;
    public $ip;
    public $type;
    public $continent_code;
    public $continent_name;
    public $country_code;
    public $country_name;
    public $region_code;
    public $region_name;
    public $city;
    public $zip;
    public $latitude;
    public $longitude;
    
    public $location; // object


    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

}