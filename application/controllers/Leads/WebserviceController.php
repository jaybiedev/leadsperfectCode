<?php
namespace Controllers\Leads;

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
    
    
    protected function templateAction($id) {
        
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
        $this->Controller->View->setPageTitle("Leads Perfect");
        $this->Controller->View->render( 'leads/dashboard/index.php', $data);
    }
    
    protected function accountAction() {
        
        // validate user permissions
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
        
        $this->Controller->View->setPageTitle("Leads Perfect");
        $this->Controller->View->render( 'leads/dashboard/index.php', $data);
    }
    
    // slow but ok since there's not a lot of sites
    protected function downloadsiteAction() {
        
        $ActiveAccount = $this->SessionManager->get('ActiveAccount');
        $account_id = $ActiveAccount->id;
        $Site = new \Library\Logic\Leads\Site();
        $SiteData = new \Library\Logic\Leads\SiteData();
        
        $firstSite = $Site->getByAccount($account_id)->getOne();
        // default columns
        $columns = array('user_email',);
        
        // get table field columns
        $meta = get_object_vars($firstSite);
        unset($meta['table']);
        unset($meta['account_id']);
        unset($meta['id']);
        
        $columns = array_merge(array_keys($meta), $columns);
        // distinct customizable fields. get first entry
        $firstSiteData = $SiteData->getAll($firstSite->id);
        foreach  ($firstSiteData->getArray() as $Field) {
            array_push($columns, $Field->field);
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
    
}
