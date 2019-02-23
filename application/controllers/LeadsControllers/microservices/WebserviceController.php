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
        
        $ContentTags = \Library\Logic\Leads\ContentTag::getByTemplateId($Site->template_id);
        
        $data['Site'] = $Site;
        $data['SiteData'] = \Library\Logic\Leads\SiteData::getAll($Site->id)->getArray('field');
         
        while ($ContentTag = $ContentTags->getNext()) {
            if (isset($data['SiteData'][$ContentTag->tag])) {
                continue;
            }
            
            if (property_exists($Site, $ContentTag->tag)) {
                continue;
            }
            
            $Data = new \Model\Leads\SiteData();
            $Data->site_id = $Site->id;
            $Data->field = $ContentTag->tag;
            $Data->content_tag_name = $ContentTag->name;
            $Data->content_tag_type_id = $ContentTag->tag_type_id;
            $Data->content_tag_system_name = $ContentTag->tag_system_name;
            
            $data['SiteData'][$ContentTag->tag] = $Data;
            unset($Data);
            
        }
        unset($data['SiteData']['address']);
        unset($data['SiteData']['address1']);
        
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
