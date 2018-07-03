<?php

namespace Model;

class User extends \Model\AbstractModel {

    public $table = "users";

    public $id;
    public $admin_id;
    public $username;
    public $name;
    public $email;
    public $roles;
    public $enable;
    public $date_expire;

    public $first_name;
    public $last_name;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

}