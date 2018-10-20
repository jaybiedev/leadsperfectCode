<?php

namespace Model;

/**
 * This will work as entity-model
 * Class AbstractModel
 * @package Model
 */

abstract class AbstractModel {

    public $table;

    abstract function setId($id);
    abstract function getId();

    function __construct($meta=array())
    {
        $this->load($meta);
    }

    function load($meta = array()) {

        // @todo:  possibly accept object too and map to the model
        if (!is_array($meta))
            return null;

        // load meta to defined attributes
        foreach ($meta as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public function isNew() {
        return empty($this->id);
    }
    
    public function getMeta() {
        return $this->get_object_public_vars();
    }

    
    public function save($meta) {
        $DataObject = new \Library\DataObject($this);
        $result = $DataObject->save($meta);

        return $this;
    }
    
    public function saveModel() {
        $meta = $this->getMeta();
        $DataObject = new \Library\DataObject($this);
        $result = $DataObject->save($meta);
        return $this;
    }
    
    private function get_object_public_vars() {
        return get_object_vars($this);
    }
}
