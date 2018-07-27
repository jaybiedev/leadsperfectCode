<?php
/**
 * Base class for Logic layer
 */

namespace Library\Logic;

abstract class LogicAbstract {

    function __construct() {
        //
    }
    
    
    /**
     *
     */
    static public function save($Model, $meta=array())
    {
        if (empty($meta))
            return;
            
        $DataObject = new \Library\DataObject($Model);
        
        return $DataObject->save($meta);
    }
    
}