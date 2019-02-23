<?php

namespace Model\Leads;

/**
 * Entity-Model
 * Class Account
 * @package Model
 */

class ContactForm extends \Model\AbstractModel {
    public $table = "contact_form";

    public $id;
    public $date_added;
    public $site_id;
    public $email_address_from;
    public $email_address_to;
    public $name_from;
    public $subject;
    public $message;
    public $enabled=1;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}