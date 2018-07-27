<?php
namespace Library\Logic;

use LogicAbstract;

class TagParser extends \Library\Logic\LogicAbstract
{
    public $tag;
    public $resource;
    public $field;
    
    public $Tag;
    public $Account;
    public $Site;
    public $SiteData;
    
    function __construct($tag=null) {
        if (!empty($tag))
            $this->load($tag);
    }
    
    public function init() {
        $this->resource = null;
        $this->field = null;
        $this->Tag = null;
    }
    
    public function load($Tag) {
        $this->init();        
        $this->Tag = $Tag;
        preg_match('#\[\[(.*?)\]\]#', $Tag->tag, $match);
        
        $tag_info = explode(":", $match[1]);
        if (count($tag_info) > 1) {
            $this->resource = trim($tag_info[0]);
            $this->field =  trim($tag_info[1]);        

        }
    }
    
    public function getContent() {
        
        if ($this->resource == 'w') {
            return $this->getWebSiteContent();
        }
        elseif ($this->resource == 'widget') {
            return $this->getWidgetContent();
        }
    }
    
    public function getWebSiteContent() {
        $field = trim($this->field);
        $value = null;
        $is_default = false;
        
        if (isset($this->SiteData[$field]) && !empty($this->SiteData[$field]->field_value)) {
            $value = $this->SiteData[$field]->field_value;
        }
        elseif (property_exists($this->Site, $field) && !empty($this->Site->$field)) {
            $value = $this->Site->$field;
        }
        else {
            $is_default = true;
            $value = $this->Tag->default_value;
        }
        
        // if image
        if ($this->Tag->tag_system_name == 'IMAGE') {
            if ($is_default) {
                $value = "/uploads/" . $this->Account->guid . "/" . $value;
            }
            else {
                $value = "/uploads/" . $this->Account->guid . "/" .  $this->Site->guid . "/" . $value;
            }
        }
        return $value;
    }
    
    public function render() {
        //
    }
    
    public function getWidgetContent() {
        // @todo: factory of widgets
        
        $classfile = APPPATH . 'libraries/Widgets/Leads/' . $this->field . '.php';
        
        if (file_exists($classfile)) {
            $class = "\Library\Widgets\Leads\\$this->field";
            $Widget = new $class();
            $Widget->setAccount($this->Account);
            $Widget->setSite($this->Site);            
            
            return $Widget->getContent();
        }
    }
    
}