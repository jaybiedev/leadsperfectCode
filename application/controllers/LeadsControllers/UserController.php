<?php

// Custom Controller for Dashboard Webservices
 
class UserController extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
        $this->initSecurity();        
    }

    public function index()
    {
        $User = $this->UserSecurity->getUser();
        
        $data = array();
        $data['User'] = $User;
        $data['partial'] = '_profile.tpl';
        
        $this->View->setPageTitle('Profile - ' . COMPANY_NAME);
        $this->View->render( 'leads/dashboard/index.tpl', $data);}}

}