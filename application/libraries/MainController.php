<?php

namespace Library;

use \Library\SessionManager;
use \Library\View;
use \Library\Helper;

class MainController extends  \CI_Controller {

    protected $SessionManager;
    protected $UserSecurity;
    protected $resource = 'account';
    protected $resource_id;
    
    public $Helper = null;
    public $View = null;
    public $Storage = null;
    
    function __construct()
    {
        parent::__construct();

        $SessionManager = new SessionManager(null, 'LEGACY');
        $SessionManager->Start();

        $this->Helper = new \Library\Helper();
        $this->View = new \Library\View();
        $this->Storage = new \Library\Storage();

    }

    public function isPost() {
        return ($this->input->method(TRUE) == 'POST');
    }
    
    public function inputPost($name) {
        return $this->input->post($name);
    }

    public function inputRequest($name) {
        return $this->input->post_get($name, true);
    }
    
    public function getInputStream($key=null, $xss_clean=true, $json_decode=false, $default=null) {
        if (!empty($key))
            return $this->input->raw_input_stream($key, $xss_clean);
        
        $data = $this->input->raw_input_stream;
        if ($json_decode) {
            $data = json_decode($data);
        }
        return $data;
    }
    
    public function renderJson($data, $success=true, $message=null) {
        $data = array('data'=>$data, 'success'=>$success, 'message'=>$message);
        echo json_encode($data);
        exit;
    }
    
    public function initSecurity() {
        $this->UserSecurity = $this->Helper->getSecurity();
        
        if ($this->UserSecurity->isLogged() == false) {
            redirect($this->Helper->getUrl()->getLoginUrl());
        }
        
        $this->initSession();
        return $this->UserSecurity;
    }
    
    public function initSession() {
        $this->SessionManager = new SessionManager(null, 'LEGACY');
        $this->SessionManager->start();
        return $this->SessionManager;
    }

}