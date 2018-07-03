<?php

namespace Library\Repository\Leads;

class Content extends \Library\Repository\RepositoryAbstract {

    public $model = 'Content';

    public function getBySlug($slug)
    {

        $this->sql = "SELECT content.*
                FROM 
                  content
                JOIN
                  slug ON slug.content_id=content.id                  
                WHERE 
                  content.enabled
                  AND slug.enabled
                  AND slug.slug = '{$slug}'";

        return $this;
    }
}