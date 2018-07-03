<?php

namespace Model;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Transaction extends \Model\AbstractModel {


    public $id;
    public $date_added;
    public $date_modified;
    public $account_id;
    public $addedby_user_id;
    public $modifiedby_user_id;
    public $transaction_type_id;
    public $transaction_status_id;
    public $vehicle_model_id;
    public $total_amount;
    public $enabled;


    // others fkeyed
    public $account_first_name;
    public $account_last_name;
    public $transaction_status_name;
    public $transaction_type_name;
    public $user_first_name;
    public $user_last_name;
    public $vehicle_model;
    public $vehicle_year;
    public $vehicle_make;


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}