<?php

namespace Model;

class User extends \Model\AbstractModel {

    public $table = "user";

    public $id;
    public $admin_id;
    public $username;
    public $name;
    public $email;
    public $roles;
    public $enable;
    public $date_expire;

    public $first_name;
    public $last_name;
    
    // support
    private $UserAccountXrefs = null;
    private $UserSiteXrefs = null;
    
    public function __construct($meta=array()) {
        parent::__construct($meta);
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function isAdmin() 
    {
        if (empty($this->roles))
            return false;
        
        $roles = null;
        if (is_string($this->roles)) {
            $roles = json_decode($this->roles);
        }
        
        if (!is_object($roles) || !property_exists($roles, 'admin'))
            return false;
        
        return $roles->admin;        
    }

    public function getUserAccountXref($account_id) {
        $AccountXref = new \Model\UserAccountXref();
        $Xrefs = $this->getUserAccountXrefs();
        foreach ($Xrefs as $Xref) {
            if ($Xref->account_id == $account_id) {
                $AccountXref = $Xref;
                break;
            }                
        }
        return $AccountXref;
    }

    public function getUserSiteXref($site_id) {
        $SiteXref = new \Model\UserSiteXref();
        $Xrefs = $this->getUserSiteXrefs();
        foreach ($Xrefs as $Xref) {
            if ($Xref->site_id == $site_id) {
                $SiteXref = $Xref;
                break;
            }
        }
        return $SiteXref;
    }
    
    public function getUserAccountXrefs() {
        if (is_object($this->UserAccountXrefs))
            return $this->UserAccountXrefs;
        
        $Repository = new \Library\Repository\Generic();
        $Xref = $Repository->getUserAccountXrefs($this->id);
        $this->UserAccountXrefs = $Repository->getArray();
        
        return $this->UserAccountXrefs;
    }

    public function getUserSiteXrefs() {
        if (is_object($this->UserSiteXrefs))
            return $this->UserSiteXrefs;
 
        $Repository = new \Library\Repository\Generic();
        $Xref = $Repository->getUserSiteXrefs($this->id);
        $this->UserSiteXrefs = $Repository->getArray();
        
        return $this->UserSiteXrefs;        
    }
    
}