<?php


use Library\Logic\Menu;
use Library\Helper;

class Leads extends Library\MainController {

    public function index()
    {
        //$segments = $this->uri->segment_array();
        $states = array(''=>'Any');
        $states = array_merge($states, Helper\Utils::getUSStates());
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $where = array();
        $country_code = null;
        $region_code = null;

        $keyword = $this->getParam('keyword');
        $country_code = $this->getParam('country_code');
        $region_code = $this->getParam('region_code');

        if (!empty($country_code)) {
            $where[] = array('field'=>'country', 'operator'=>'=', 'value'=>trim($country_code));
        }
        
        if (!empty($region_code)) {
            $where[] = array('field'=>'state', 'operator'=>'=', 'value'=>trim($region_code));
        }
        
        if (!empty($keyword)) {
           $where[] = array('field'=>'name', 'operator'=>'like', 'value'=>'%' . trim($keyword) . '%');
        }
        
        
        $Sites = \Library\Logic\Leads\Site::getByAccount(2, $where, $orderby="country, state, city, zip");
        $UserGeolocation = Helper\Utils::getGeoLocation($ip);
        
        if (!isset($_REQUEST['country'])) {
            $country_code = $UserGeolocation->country_code;
        }
        if (!isset($_REQUEST['region_code'])) {
            $region_code = $UserGeolocation->region_code;
        }
        
        $data = array(
            'countries'=>array('US'=>'United States'),
            'states'=> $states,
            'UserGeolocation'=>$UserGeolocation,
            'Sites'=>$Sites->getArray(),
            'region_code' => $region_code,
            'country_code'=>$country_code,
            'keyword'=>$keyword,
        );
        
        $this->View->setPageTitle(COMPANY_NAME);

        $this->View->render( HOME_PAGE_TEMPLATE, $data);
    }

    /**
     * catch request and pass to leads/Dashboard
     */
    /*
    public function dashboard()
    {
        if ($this->Helper->getSecurity()->isLogged() == false) {
            redirect($this->Helper->getUrl()->getLoginUrl());
        }

        return new Controllers\Leads\DashboardController($this);
    }

    public function admin()
    {
        $data = array();
        $this->View->setPageTitle("Dashboard");
        $this->View->render( 'leads/dashboard/index.php', $data);
    }
    
    public function microservices() {
        if ($this->Helper->getSecurity()->isLogged() == false) {
            redirect($this->Helper->getUrl()->getLoginUrl());
        }
        
        return new Controllers\Leads\MicroservicesController($this);
        
        // factory
        
        $Email = new \Library\Logic\Email();
        
        $to = 'jaredsantibanez@gmail.com';
        $from = 'jaybiedev@gmail.com';
        $subject = 'Test Email ' . time();
        $message = 'Sending Test Email ' . time();
        
        //mail($to,"My subject",$message);
        //die;
        
        $Email->setTo($to);
        $Email->setFrom($from, 'Jay Saint');
        $Email->setSubject($subject);
        $Email->setMessage($message);
        if (false == $Email->send()) {
            echo "Failed to send..";
        }
        else {
            echo "Sent...";
        }
    }
    */
    /**
     * Website request using slug
     */
    public function slug() {

        $segments = $this->uri->segment_array();
        $slug = strtolower(implode('/', $segments));
        
        $SiteRepository = new \Library\Repository\Leads\Site();
        $Site = $SiteRepository->getBySlug($slug)->getOne();
        
        if (empty($Site->id) || get_boolean_value($Site->enabled) == false) {
            if (strpos($slug, 'church') ===  false) {
                $slug .= "/church";
                header("location: /" . $slug);
                exit;
            }
            
            if (empty($Site->id) || get_boolean_value($Site->enabled) == false) {
                header("location: ../");
            }
        }
        
        if (get_boolean_value($Site->is_external_site)) {
            header("location:" . $Site->vanity_url);
            exit;
        }
        
        $AccountRepository = new \Library\Repository\Account();
        $Account = $AccountRepository->getById($Site->account_id)->getOne();

        $Content = new \Model\Leads\Content();
        
        $cache_file = WEB_PATH . '/uploads/' . $Account->guid . '/' . $Site->guid . '/index.html';
        $filetime = @filemtime($cache_file);
        $nexttime = strtotime("+1 day", $filetime);
        
        if ($Site->is_cached && file_exists($cache_file) && time() < $nexttime) {
            $Content->content = @file_get_contents($cache_file);
        }
        
        if (empty($Content->content)) {
            $Content = Library\Logic\Leads\Content::getBySlug($slug, true);                
            @file_put_contents($cache_file, $Content->content);                
        }
        
        if (is_object($Content)) {
            echo $Content->content;
        }
        else {
            echo "404";
        }
    }

    public function catchall() {
        $segments = $this->uri->segment_array();

        $slug = strtolower(implode('/', $segments));


        $Content = Library\Logic\Leads\Content::getBySlug($slug);

        if (is_object($Content) && !empty($Content->content)) {
            echo $Content->content;
        }
        else {
            echo "404";
        }


        echo "<br >";
        echo $slug;
        die('Catch All');
    }

}
