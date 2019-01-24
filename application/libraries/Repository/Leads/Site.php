<?php

namespace Library\Repository\Leads;

class Site extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\Site';

    public function getBySlug($slug, $is_enabled_only=true)
    {
        $this->sql = "SELECT site.*
                FROM 
                  site
                WHERE 
                  1=1
                  AND site.slug = '{$slug}'";
        
        if ($is_enabled_only) {
            $this->sql .= " AND site.enabled ";
        }

        return $this;
    }

    /**
     * @return object Repository
     */
    public function getByGuid($guid, $is_enabled_only=true)
    {
        $this->sql = "SELECT site.*
                FROM
                  site
                WHERE
                  1=1
                  AND site.guid = '{$guid}'";

        if ($is_enabled_only) {
            $this->sql .= " AND site.enabled ";
        }
        return $this;
    }
    
    /**
     * @return object Repository
     */
    public function getByName($name, $operator='LOWER')
    {
        
        $name = trim($name);
        $this->sql = "SELECT site.*
                FROM
                  site
                WHERE
                  site.enabled";
        
        if ($operator == 'LOWER') {
            $name = trim(strtolower($name));       
            $this->sql .= "  AND LOWER(site.name)='{$name}'";
        }
        
        return $this;
    }
    
    public function getByAccount($account_id,  $where=null, $orderby="") {
        $this->sql = "SELECT site.*
                FROM
                  site
                WHERE
                  site.enabled
                  AND site.account_id = " . intval($account_id);
        
        if (!empty($where)) {
            $where_clause = $this->getWhereClause($where);
            $this->sql .= $where_clause;
        }
        
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

    public function getSitesByAccountUserId($user_id, $sortby='name', $all=false) {
        
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
        
        if (!$all) {
            $this->sql .= " AND site.enabled=1";
        }
        $this->sql .= " ORDER BY {$sortby}";
        
        return $this;
    }
    
}