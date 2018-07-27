<?php


use Library\Logic\Menu;
use Library\Helper;

class Leads extends Library\MainController {

    public function index()
    {
        //$segments = $this->uri->segment_array();
        $data = array();
        $this->View->setPageTitle("Leads Perfect");
        $this->View->render( 'leads/frontend/index.tpl', $data);

    }

    /**
     * catch request and pass to leads/Dashboard
     */
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
    
    public function webservice() {
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
    
    /**
     * Website request using slug
     */
    public function slug() {

        $segments = $this->uri->segment_array();
        $slug = strtolower(implode('/', $segments));
        
        $SiteRepository = new \Library\Repository\Leads\Site();
        $Site = $SiteRepository->getBySlug($slug)->getOne();
        
        if (empty($Site->id) || get_boolean_value($Site->enabled) == false) {
            die ('404 - Site not found.');
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
