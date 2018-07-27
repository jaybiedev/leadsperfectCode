<?php

namespace Controllers;

use \Library\SessionManager;

// abstract class for custom controller
abstract class ControllersAbstract extends \Library\MainController {
    public $Controller;
    
    protected $SessionManager;
    protected $UserSecurity;
    protected $resource = 'account';
    protected $resource_id;
    
    public function __construct($Controller) {
        
        $this->Controller = $Controller;
        $this->UserSecurity = $this->Controller->Helper->getSecurity();
        
        if ($this->UserSecurity->isLogged() == false) {
            redirect($this->Helper->getUrl()->getLoginUrl());
        }
        
        $this->SessionManager = new SessionManager(null, 'LEGACY');
        $this->SessionManager->start();
        
        $segments = $this->Controller->uri->segment_array();
        
        if (isset($segments[2]))
            $this->resource = $segments[2];
        if (isset($segments[3]))
            $this->resource_id = $segments[3];
                        
        $method = $this->resource . 'Action';
        if (method_exists($this, $method)) {
            $this->$method();
        }
        elseif (method_exists($this, 'index')) {
            $this->index();
        }
        else {
            die("Controller method " . $method . " not found.");
        }
    }
}