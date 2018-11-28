<?php

// Custom Controller for Dashboard Webservices
 
class DashboardController extends \Library\MainController {
    
    function __construct() {
        parent::__construct();
        $this->initSecurity();        
    }

    /**
     * entry point with /dashboard only no resource and Id
     */
    public function index()
    {
        // die('Default method');
        // validate user permissions
        // UserAccounts as Accounts this user owns
        $UserAccounts = $this->SessionManager->get('UserAccounts');
        
        if (empty($UserAccounts)) {
            $User = $this->UserSecurity->getUser();
            
            $Accounts = \Library\Logic\Account::getAccountsByUserId($User->id);
            $this->SessionManager->put('UserAccounts', $Accounts->getArray('guid'));
            $UserAccounts = $this->SessionManager->get('UserAccounts');
        }
        
        if (!empty($UserAccounts)) {
            // account admin
            $ActiveAccount = $this->SessionManager->get('ActiveAccount');
            if (!empty($ActiveAccount)) {
                $account_guid = $ActiveAccount->guid;
            }
            else {
                // should add option to choose account for multiple accounts
                // taking the first for now
                $account_guid = key($UserAccounts);
                $this->SessionManager->put('ActiveAccount', $UserAccounts[$account_guid]);
            }
            redirect(WEB_URL . "/dashboard/account/{$account_guid}");
            exit;
        }
         
         // redirect to site 
        $UserSites = $this->SessionManager->get('UserSites');
        if (empty($UserSites)) {
            $User = $this->UserSecurity->getUser();
            
            // get all sites administered by this account manager
            if ($User->isAdmin()) {
                $Sites = \Library\Logic\Leads\Site::getSitesByAccountUserId($User->id);
            }
            else {
                $Sites = \Library\Logic\Leads\Site::getSitesByUserId($User->id);
            }
            $this->SessionManager->put('UserSites', $Sites->getArray('guid'));
            $UserSites = $this->SessionManager->get('UserSites');
        }
        
        if (count($UserSites) > 0) {
            // fallback. probably went through here with no guid.  take the first site.
            $Site = reset($UserSites);
            redirect(WEB_URL . "/dashboard/site/{$Site->guid}");
        }
        else {
            die("No dashboard information found.");
        }
        exit;
    }

    private function settingsSaveAction() {
        // save
        // load Site with $site_guid = $this->_getResourceId();
        // grab values of images, resize, write and update site_data
        // Site->Save()
        
        // $User = $this->UserSecurity->getUser();        
        // $Account = \Library\Logic\Account::getAccountsByUserId($User->id);
        
        $UserSites = $this->SessionManager->get('UserSites');
        $site_guid = $this->_getResourceId();
        
        if (empty($UserSites[$site_guid])) {
            throw new \Exception("Unable to save.  Site not found.");
        }
        
        $Site = $UserSites[$site_guid];        

        if (isset($_FILES['logo']) && empty($_FILES['logo']['error']) && !empty($_FILES['logo']['tmp_name'])) {
            $File = \Library\Helper\Utils::uploadSiteAsset($Site, $_FILES['logo']);
            if (file_exists($File->fullpath)) {
                $Site->logo = $File->filename;
            }
        }
        
        $Site->name = $this->inputRequest('name');
        $Site->slug = $this->inputRequest('slug');
        $Site->vanity_url = $this->inputRequest('vanity_url');
        $Site->phone = $this->inputRequest('phone');
        $Site->email = $this->inputRequest('email');
        $Site->address1 = $this->inputRequest('address1');
        $Site->address2 = $this->inputRequest('address2');
        $Site->city = $this->inputRequest('city');
        $Site->zip = $this->inputRequest('zip');
        $Site->country = $this->inputRequest('country');
        $Site->state = $this->inputRequest('state');
        $Site->saveModel();
        
        // remove existing cached file
        \Library\Logic\Leads\Site::removeCachedContent($Site);
        
        // save customizations
        $SiteDataRepository = new \Library\Logic\Leads\SiteData();
        $customization = $this->inputRequest('customization');

        foreach ($customization as $field=>$value) {
            $SiteDataModel = $SiteDataRepository->getByField($Site->id, $field)->getOne();
            $SiteDataModel->field_value = $value;
            $SiteDataModel->saveModel();
        }
        
        // save images
        $files_uploaded = $_FILES['customization'];
       
        foreach ($files_uploaded['error'] AS $field=>$error) {
            if ($error != 0)
                continue;
            
            $file_uploaded_meta = array('name'=>$files_uploaded['name'][$field],
                'type'=>$files_uploaded['type'][$field],
                'tmp_name'=>$files_uploaded['tmp_name'][$field],
                'error'=>$files_uploaded['error'][$field],
                'size'=>$files_uploaded['size'][$field]
            );

            $File = \Library\Helper\Utils::uploadSiteAsset($Site, $file_uploaded_meta);
            if (file_exists($File->fullpath)) {
                $SiteDataModel = $SiteDataRepository->getByField($Site->id, $field)->getOne();
                $SiteDataModel->field_value = $File->filename;
                $SiteDataModel->saveModel();
            }            
        }
        
    }
    
    public function site()
    {
        if ($this->isPost()) {
            $action = $this->_getSubResource() . 'SaveAction';
            if (method_exists($this, $action)) {
                $this->$action();
            }
            
            // die('HERE');
        }
        
        // must be site admin
        $UserSites = $this->SessionManager->get('UserSites');
        if (empty($UserSites)) {
            $User = $this->UserSecurity->getUser();
            
            // get all sites administered by this account manager
            if ($User->isAdmin()) {
                $Sites = \Library\Logic\Leads\Site::getSitesByAccountUserId($User->id);
            }
            else {
                $Sites = \Library\Logic\Leads\Site::getSitesByUserId($User->id);
            }
            $this->SessionManager->put('UserSites', $Sites->getArray('guid'));
            $UserSites = $this->SessionManager->get('UserSites');
        }
        
        $site_guid = $this->_getResourceId();
        if (empty($site_guid)) {
            // fallback. probably went through here with no guid.  take the first site.
            $Site = reset($UserSites);
            redirect(WEB_URL . "/dashboard/site/{$Site->guid}");
            exit;
        }
        
        if (!in_array($site_guid, array_keys($UserSites))) {
            die("Site not found or user has no access to this site.");
        }
        
        $Site = $UserSites[$site_guid];
        
        $data = array();
        $data['Site'] = $Site;
        $data['dashboard'] = 'site';
        $data['partial'] = 'site/index.tpl';
        $data['site_guid'] = $site_guid;
        $data['sub_resource'] = $this->_getSubResource();
        $data['page_heading'] = $this->_getPageHeading();        
        
        $this->View->setPageTitle('Site Dashboard - ' . $data['page_heading']);
        $this->View->render( 'leads/dashboard/index.tpl', $data);
    }

    public function account()
    {
        $UserAccounts = $this->SessionManager->get('UserAccounts');
        
        if (empty($UserAccounts)) {
            $User = $this->UserSecurity->getUser();
            
            $Accounts = \Library\Logic\Account::getAccountsByUserId($User->id);
            $this->SessionManager->put('UserAccounts', $Accounts->getArray('guid'));
            $UserAccounts = $this->SessionManager->get('UserAccounts');
        }
        
        // user has not access to main accounts
        if (empty($UserAccounts)) {
            return $this->index();
        }
        
        $account_guid = $this->_getResourceId();
        if (empty($account_guid)) {
            $ActiveAccount = $this->SessionManager->get('ActiveAccount');
            
            // no active account in session; get the first
            if (empty($ActiveAccount))
                $ActiveAccount = reset($UserAccounts);
            
            $account_guid = $ActiveAccount->guid;
        
            $this->SessionManager->put('ActiveAccount', $ActiveAccount);
            redirect(WEB_URL . "/dashboard/account/{$account_guid}");
            exit;
        }
        
        if (!in_array($account_guid, array_keys($UserAccounts))) {
            die("Account not found or user has no access to this account.");
        }
        
        $Account = $UserAccounts[$account_guid];
        
        $data = array();
        
        $data['Account'] = $Account;
        $data['dashboard'] = 'account';
        $data['partial'] = 'account/index.tpl';
        $data['account_guid'] = $account_guid;
        $data['sub_resource'] = $this->_getSubResource();
        
        $action = $data['sub_resource'] . 'Action';
        if (method_exists($this, $action)) {
            return $this->$action();
        }
        
        $this->View->setPageTitle('Account Dashboard');
        $this->View->render( 'leads/dashboard/index.tpl', $data);
    
    }
    
    public function uploadSites() {
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        
        $file = WEB_PATH . "/uploads/gcimicro.zip";
        
        $file = "sites1113.csv";
        $result = \Library\Logic\Leads\UploadSite::uploadSites($ActiveAccount, $file);
        var_dump($result);
    }
    
    private function downloadsitesAction() {
        
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $account_id = $ActiveAccount->id;
        $Site = new \Library\Logic\Leads\Site();
        $SiteData = new \Library\Logic\Leads\SiteData();
        
        $ContentTagRepository = new \Library\Logic\Leads\ContentTag();
        $ContentTags = $ContentTagRepository->getByTemplateId($ActiveAccount->template_id)->getArray();
        
        // default columns
        $site_properties = get_object_vars(new \Model\Leads\Site());
        unset($site_properties['table']);
        unset($site_properties['account_id']);
        unset($site_properties['id']);
        unset($site_properties['is_cached']);
        
        $columns = array('guid', 'slug', 'user_email');
        $columns = array_unique(array_merge($columns, array_keys($site_properties)));
        
        foreach ($ContentTags as $Tag) {
            // if ($Tag->isCustomField() == false)
            //    continue;
            // $field = $Tag->getFieldName();
            
            if (in_array($Tag->tag, $columns))
                continue;
                
                array_push($columns, $Tag->tag);
        }
        
        # Start the ouput
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=sites.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        $output = fopen("php://output", "w");
        
        fputcsv($output, $columns);
        
        # Then loop through the rows
        $Sites = $Site->getByAccount($account_id);
        
        while (false !== $SiteItem = $Sites->getNext()) {
            
            $data = array();
            $meta = (array)$SiteItem;
            
            $SiteDataArray = $SiteData->getAll($SiteItem->id)->getArray();
            foreach ($SiteDataArray as $Dat) {
                if (in_array($Dat->field, array('id', 'account_id')))
                    continue;
                    
                    $meta[$Dat->field] = $Dat->field_value;
            }
            
            foreach ($columns as $column) {
                if (in_array($column, array_keys($meta)))
                    $data[] = $meta[$column];
                    else
                        $data[] = '';
            }
            fputcsv($output, $data); // here you can change delimiter/enclosure
            
        }
        # Close the stream off
        fclose($output);
    }
    
    private function _getResourceId() {
        
        $resource_id = null;
        $segments = $this->uri->segment_array();
    
        if (isset($segments[2]))
            $resource = $segments[2];
        
        if (isset($segments[3]))
            $resource_id = $segments[3];
        
        return $resource_id;
    }
    
    private function _getSubResource() {
        
        $resource = null;
        $segments = $this->uri->segment_array();
        
        if (isset($segments[4])) // && in_array($segments[4], array('charts', 'settings', 'template', 'profile', 'sites', 'downloadsites')))
            $resource = $segments[4];
        
        return $resource;
    }
    
    private function _getPageHeading() {
        $sub_resource = $this->_getSubResource();
        
        switch ($sub_resource) {
            case  'profile':
                $page_heading = 'Update User Profile';
                break;
            case  'template':
                $page_heading = 'Site Template';
                break;
            case  'settings':
                $page_heading = 'Site Settings';
                break;
            case  'charts':
                $page_heading = 'Statistics and Charts';
                break;
            default:
                $page_heading = '';
                break;
        }
        return $page_heading;
    }
}