<?php

namespace Library\Repository\Leads;

class SiteData extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\SiteData';

    public function getAll($site_id)
    {

        $this->sql = "SELECT site_data.*, 
                    content_tag.name as content_tag_name,
                    content_tag.tag_type_id AS content_tag_type_id,
                    tag_type.system_name AS content_tag_system_name
                FROM 
                  site_data
                JOIN
                  content_tag ON content_tag.tag=site_data.field
                JOIN
                  tag_type ON tag_type.id=content_tag.tag_type_id
                WHERE 
                  site_data.enabled                  
                  AND site_data.site_id = '{$site_id}'";

        return $this;
    }
    
    public function getByField($site_id, $field)
    {
        
        $this->sql = "SELECT site_data.*, content_tag.name as content_tag_name
                FROM
                  site_data
                JOIN
                  content_tag ON content_tag.tag=site_data.field
                WHERE
                  site_data.enabled
                  AND site_data.site_id = '{$site_id}'
                  AND site_data.field='{$field}'";
        
        return $this;
    }
}