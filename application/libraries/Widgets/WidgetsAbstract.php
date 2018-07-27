<?php

namespace Library\Widgets;

abstract class WidgetsAbstract  {

    protected $CI;
    protected $db;
    
    public $Site;
    public $Account;
    
    public $elementID;

    public abstract function render();
    
    public abstract function getContent();

    
    public function setAccount($Account) {
        $this->Account = $Account;
    }

    public function setSite($Site) {
        $this->Site = $Site;
    }
    
}