<?php

namespace Library\Repository\Leads;

class Content extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\Content';

    public function getBySlug($slug)
    {

        $this->sql = "SELECT content.*
                FROM 
                  content
                JOIN
                  site ON site.template_id=content.template_id                  
                WHERE 
                  content.enabled
                  AND content.date_published IS NOT NULL
                  AND site.enabled
                  AND site.slug = '{$slug}'
               ORDER by content.date_published DESC
               LIMIT 1";

        return $this;
    }
}