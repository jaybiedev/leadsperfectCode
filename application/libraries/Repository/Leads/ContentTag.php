<?php

namespace Library\Repository\Leads;

class ContentTag extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\ContentTag';

    public function getAll($content_id)
    {

        $this->sql = "SELECT content_tag.*, 
                    tag_type.system_name AS tag_system_name, 
                    tag_type.name AS tag_name
                FROM 
                  content_tag
                JOIN
                  tag_type ON tag_type.id=content_tag.tag_type_id                  
                WHERE 
                  tag_type.enabled
                  AND content_tag.content_id = '{$content_id}'";
        return $this;
    }
    
    public function getByTemplateId($template_id, $sortby='content_tag.sortby') {
        $this->sql = "SELECT content_tag.*, 
                tag_type.name AS tag_name, 
                tag_type.system_name AS tag_system_name
            FROM
              content_tag
            JOIN
              tag_type ON tag_type.id = content_tag.tag_type_id
            JOIN
              content ON content.id=content_tag.content_id
            WHERE
              content.enabled
              AND content.date_published IS NOT NULL ";
        
        if (!empty($sortby))
            $this->sql .= " ORDER by {$sortby}";
        
        return $this;
    }
}