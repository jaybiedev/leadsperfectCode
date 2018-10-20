<?php

namespace Library\Repository;

class Generic extends \Library\Repository\RepositoryAbstract {

    public $model = null;

    public function getUserAccountXrefs($user_id)
    {
        $this->model = 'UserAccountXref';
        $this->sql =  "SELECT * 
                FROM user_account_xref
                WHERE user_id=?";
        $this->bindings = array($user_id);
        return $this;
    }

    
    public function getUserSiteXrefs($user_id)
    {
        $this->model = 'UserSiteXref';
        $this->sql =  "SELECT *
                FROM user_site_xref
                WHERE user_id=?";
        $this->bindings = array($user_id);
        return $this;
    }
    
}