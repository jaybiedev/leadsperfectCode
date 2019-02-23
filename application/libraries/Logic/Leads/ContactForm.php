<?php
namespace Library\Logic\Leads;

use LogicAbstract;

/**
 * GA
 * user: jaybiedev@gmail.com
 * Tracking ID: UA-122186239-1
 */

class ContactForm extends \Library\Logic\LogicAbstract
{

    static function getBySiteId($site_id) {
        
        $ContactForm = new \Library\Repository\Leads\ContactForm();
        
        $ContactFormRepository = new \Library\Repository\Leads\ContactForm();
        $ContactForm = $ContactFormRepository->getBySiteId($slug);
            
        return $ContactForm->getAll();
    }
    
}