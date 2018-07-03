<?php

namespace Model;

/**
 * Entity-Model for userlog
 * Class Menu
 * @package Model
 */

class Userlog extends \Model\AbstractModel {

    public $table = "userlog";

    public $userlog_id;
    public $admin_id;
    public $date_in;
    public $date_out;
    public $remarks;
    public $ip;


    public function setId($id)
    {
        $this->userlog_id = $id;
    }

    public function getId()
    {
        return $this->userlog_id;
    }
}