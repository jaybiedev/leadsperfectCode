<?php

// Custom Controller for Dashboard Webservices
 
class WebserviceController extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
        $this->initSecurity();        
    }

    public function index()
    {
        die('Default method');
    }
    
    public function site() {
        $User = $this->UserSecurity->getUser();
        $guid = $this->inputRequest('guid');
        
        $data = array();
        $UserSites = $this->SessionManager->get('UserSites');        
        $Site = $UserSites[$guid];
        
        // $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $Template = \Library\Logic\Leads\Template::get($Site->template_id);
        
        $data['Site'] = $Site;
        $data['SiteData'] = \Library\Logic\Leads\SiteData::getAll($Site->id)->getArray('field');
         
        return $this->renderJson($data);
    }
    
    public function account() {
        $User = $this->UserSecurity->getUser();
        $guid = $this->inputRequest('guid');
        
        $data = array();
        
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $Template = \Library\Logic\Leads\Template::get($ActiveAccount->template_id);
        
        $data['Account'] = $ActiveAccount;
        
        return $this->renderJson($data);
        
    }
    
    public function user() {
        $data = array();
        if ($this->isPost()) {
            // save current user
            $Meta = $this->getInputStream(null, true, true);
            $UserModel = \Library\Logic\User::get($Meta->id);
            $UserModel->email = $Meta->email;
            $UserModel->first_name = $Meta->first_name;
            $UserModel->last_name = $Meta->last_name;
            
            if ($Meta->password  && $Meta->password_confirm) {
                $UserModel->password =  $this->Helper->getSecurity()->hashPassword($Meta->password);
            }
            
            $UserModel->saveModel();             
            // update session
            $User = $this->UserSecurity->setUser($UserModel);            
        }
        
        // get. always return User model
        $User = $this->UserSecurity->getUser();
        unset($User->table);
        unset($User->admin_id);
        unset($User->date_expire);
        unset($User->roles);
        
        $data['User'] = $User;
        return $this->renderJson($data);
        
    }
    
  
    
}
