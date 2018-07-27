<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Template
 * @package Model
 */

class Template extends \Model\AbstractModel {
    public $table = "template";

    public $id;
    public $name;
    public $date_added;
    public $date_modified;
    public $addedby_user_id;
    public $modifiedby_user_id;
    public $public;
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