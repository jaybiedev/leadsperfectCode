<?php

namespace Model\Leads;

class UserSiteXref extends \Model\AbstractModel {

    public $table = "user_site_xref";
    public $id;
    public $user_id;
    public $site_id;
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
}