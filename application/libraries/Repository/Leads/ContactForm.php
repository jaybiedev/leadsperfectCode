<?php

namespace Library\Repository\Leads;

class ContactForm extends \Library\Repository\RepositoryAbstract {

    public $model = 'Leads\ContactForm';

    public function getBySiteId($site_id, $order_by="date_added desc", $limit=50)
    {
        $site_id = (int)$site_id;
        $this->sql = "SELECT contact_form.*
                FROM 
                  contact_form                                
                WHERE 
                  site_id={$site_id}
               ORDER by contact_form.{$order_by} limit {$limit}";

        return $this;
    }
}