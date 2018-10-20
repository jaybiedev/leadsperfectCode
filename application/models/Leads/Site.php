<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class Site extends \Model\AbstractModel {

    public $table = "site";

    public $id;
    public $name;
    public $slug;
    public $account_id;
    public $vanity_url;
    public $phone;
    public $email;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $template_id;
    public $guid;
    public $logo;
    public $is_cached;
    public $enabled;
    
    private $url;
    private $logo_url;
    private $Account;
    
    function __get($name) {
        if (isset($this->$name))
            return $this->$name;
        
        if ($name == 'url') {
            if (!empty($this->vanity_url))
                $this->url = $this->vanity_url;
            else
                $this->url = WEB_URL . '/' . $this->slug;
        }
     
        if ($name == 'logo_url') {
            
            $logo_url = WEB_URL;
            if (empty($this->Account)) {
                $Repository = new Logic\Repository\Account();
                $Account = $Repository->get($this->account_id);    
            }
            
            if (!empty($this->logo))
                $this->logo_url .= '/' . $this->Account->guid . '/' . $this->guid . '/' . $this->logo;
            elseif (isset($this->Account->logo))
                $this->logo_url .= '/' . $this->Account->guid  . '/' . $this->Account->logo;
                
            $this->logo_url = WEB_URL ;
        }
                
    }
    
    public function getAccount() {
        return $this->Account;    
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getUrl() {
        if (!empty($this->vanity_url))
            return $this->vanity_url;
        
         return WEB_URL . '/' . $this->slug;
    }
}