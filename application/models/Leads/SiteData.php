<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class SiteData
 * @package Model
 */

class SiteData extends \Model\AbstractModel {

    public $table = "site_data";

    public $id;
    public $site_id;
    public $field;
    public $field_value;
    public $enabled;
    
    // extra // supporting fields
    public $content_tag_name;
    public $content_tag_type_id;
    public $content_tag_system_name;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}