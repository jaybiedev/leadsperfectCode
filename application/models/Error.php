<?php

namespace Model;

class Error extends \Model\AbstractModel {

    public $id;
    public $code;
    public $message;


    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

}