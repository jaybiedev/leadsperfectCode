<?php

namespace Model;

class UserAccountXref extends \Model\AbstractModel {

    public $table = "user_account_xref";
    public $id;
    public $user_id;
    public $account_id;
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
}