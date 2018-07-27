<?php

namespace Library\Repository\Leads;

class Template extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\Template';

    public function get($id)
    {
        $this->sql = "SELECT template.*
                FROM 
                  template
                WHERE 
                  template.enabled
                  AND template.id = '{$id}'";
        return $this;
    }

}