<?php
namespace Controllers\Leads;
require(APPPATH.'/libraries/RESTController.php');

// Custom Controller for Dashboard

class DashboardController extends \Controllers\ControllersAbstract{
    
    private $UserLeads;
    
    function __construct($Controller) {
        parent::__construct($Controller);
    }
        
    public function index()
    {
        die('Default method');
    }
    
    protected function uploadSitesAction() {
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        
        $file = WEB_PATH . "/uploads/gcimicro.zip"; 
        $result = \Library\Logic\Leads\UploadSite::uploadZip($ActiveAccount, $file);
        var_dump($result);
    }
    
    protected function passwordAction() {
        var_dump($this->resource_id);
        echo $this->Controller->Helper->getSecurity()->hashPassword($this->resource_id);
        exit;
    }
    
    protected function siteAction() {
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
        
        $site_guid = $this->resource_id;
        
        if (!in_array($site_guid, array_keys($UserSites))) {
            die("Site not found or user has not access to this site.");
        }

        $data = array();
        $data['partial'] = '_siteadmin.tpl';
        
        $this->Controller->View->setPageTitle("Leads Perfect");
        $this->Controller->View->render( 'leads/dashboard/index.tpl', $data);
    }

    protected function accountAction() {
        
        // validate user permissions
        // UserAccounts as Accounts this user owns 
        $UserAccounts = $this->SessionManager->get('UserAccounts');
        
        if (empty($UserAccounts)) {
            $User = $this->UserSecurity->getUser();
            
            $Accounts = \Library\Logic\Account::getAccountsByUserId($User->id);            
            $this->SessionManager->put('UserAccounts', $Accounts->getArray('guid'));
            $UserAccounts = $this->SessionManager->get('UserAccounts');
        }
        
        $account_guid = $this->resource_id;
        
        if (empty($account_guid) && !empty($UserAccounts)) {
            
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
        }
        
        if (!in_array($account_guid, array_keys($UserAccounts))) {
            die("Account not found or user has not access to this account.");
        }        
        
        $data = array();
        $data['partial'] = '_accountadmin.tpl';
        
        $this->Controller->View->setPageTitle("Leads Perfect");
        $this->Controller->View->render( 'leads/dashboard/index.tpl', $data);
    }
    
    // slow but ok since there's not a lot of sites
    protected function downloadsiteAction() {
        
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
