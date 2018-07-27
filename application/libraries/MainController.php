<?php

namespace Library;

use \Library\SessionManager;
use \Library\View;
use \Library\Helper;

class MainController extends  \CI_Controller {


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

    public function renderJson($data, $success=true, $message=null) {
        $data = array('data'=>$data, 'success'=>$success, 'message'=>$message);
        echo json_encode($data);
        exit;
    }

}