<?php

namespace Model;

class Admin extends \Model\AbstractModel {

    public $admin_id;
    public $username;
    public $name;
    public $usergroup;
    public $enable;
    public $date_expire;
    public $branch_id;


    public function setId($id)
    {
        $this->admin_id = $id;
    }

    public function getId()
    {
        return $this->admin_id;
    }

}