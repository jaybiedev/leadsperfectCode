<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class ContentTag extends \Model\AbstractModel {

    public $table = "content_tag";

    public $id;
    public $content_id;
    public $name;
    public $tag;
    public $tag_type_id;
    public $default_value;
    
    // tag info
    public $tag_system_name;
    public $tag_name;

    private $Parser;
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function isCustomField() {
        // quick and dirty
        return (strpos($this->tag, '[[w:') === 0);
    }
    
    public function getFieldName() {
        if (empty($this->Parser))
            $this->Parser =  new \Library\Logic\TagParser();
        
        $this->Parser->load($this);
        return $this->Parser->field;
    }
    
    public function isImage() {
        return ($this->tag_system_name == 'IMAGE');
    }
}