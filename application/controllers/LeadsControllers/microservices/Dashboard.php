<?php

// Custom Controller for Dashboard Webservices
 
class Dashboard extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
        $this->initSecurity();        
    }

    public function index()
    {
        die('Default method');
    }
    
    public function getDashboardSiteInitAjax() {
        
        $User = $this->UserSecurity->getUser();
        $guid = $this->inputRequest('guid');
        
        $data = array();
        $UserSites = $this->SessionManager->get('UserSites');        
        $Site = $UserSites[$guid];
        
        // $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $Template = \Library\Logic\Leads\Template::get($Site->template_id);
        
        $data['Site'] = $Site;
        $data['SiteData'] = \Library\Logic\Leads\SiteData::getAll($Site->id)->getArray();
         
        return $this->renderJson($data);
    }
    
    protected function getDashboardInitAjaxAction() {
        $User = $this->UserSecurity->getUser();
        
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        
        $Template = \Library\Logic\Leads\Template::get($ActiveAccount->template_id);
        
        $ContentTags = \Library\Logic\Leads\ContentTag::getByTemplateId ($ActiveAccount->template_id);
        
        $data = array('User'=>$User,
            'Account'=>$ActiveAccount,
            'Template'=>$Template,
            'ContentTags' => $ContentTags->getArray('id'),
        );
        
        return $this->renderJson($data);
    }
    
}
