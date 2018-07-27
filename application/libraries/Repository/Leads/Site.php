<?php

namespace Library\Repository\Leads;

class Site extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\Site';

    public function getBySlug($slug)
    {
        $this->sql = "SELECT site.*
                FROM 
                  site
                WHERE 
                  site.enabled
                  AND site.slug = '{$slug}'";
        return $this;
    }

    public function getByGuid($guid)
    {
        $this->sql = "SELECT site.*
                FROM
                  site
                WHERE
                  site.enabled
                  AND site.guid = '{$guid}'";
        return $this;
    }
    
    
    public function getByAccount($account_id, $orderby="") {
        $this->sql = "SELECT site.*
                FROM
                  site
                WHERE
                  site.enabled
                  AND site.account_id = " . intval($account_id);
        
        if (empty($orderby))
            $this->sql .= " ORDER BY name ";
        else
            $this->sql .= " ORDER BY {$orderby} ";
            
        return $this;
    }
    
    public function getSitesByUserId($user_id) {
        
        $this->sql = "SELECT
                        site.*
                    FROM
                        site
                    JOIN
                        user_site_xref AS usx ON usx.site_id=site.id
                    WHERE
                        usx.user_id=" . intval($user_id);
        return $this;
    }

    public function getSitesByAccountUserId($user_id) {
        
        $this->sql = "SELECT
                        site.*
                    FROM
                        site
                    JOIN
                        account ON account.id=site.account_id
                   JOIN
                        user_account_xref AS aux ON aux.account_id=account.id
                    WHERE
                        aux.user_id=" . intval($user_id);
        return $this;
    }
    
}