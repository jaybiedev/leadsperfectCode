<?php

namespace Model;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Account extends \Model\AbstractModel {

    public $table = "account";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $address1;
    public $address2;
    public $city;
    public $zip;
    public $state;
    public $phone1;
    public $phone2;
    public $country;
    public $notes;
    public $date_added;
    public $date_modified;
    public $user_id_modified;
    public $user_id_added;
    public $enabled;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}