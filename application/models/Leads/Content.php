<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Content extends \Model\AbstractModel {
    public $table = "content";

    public $id;
    public $content;
    public $date_published;
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