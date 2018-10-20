<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class SiteData extends \Model\AbstractModel {

    public $table = "site_data";

    public $id;
    public $site_id;
    public $field;
    public $field_value;
    public $enabled;
    
    public $content_tag_name;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}