<?php
namespace Library\Logic\Leads;

use LogicAbstract;

class ContentTag extends \Library\Logic\LogicAbstract
{
    static function getByTemplateId($template_id) {
        $Repository = new \Library\Repository\Leads\ContentTag();
        
        return $Repository->getByTemplateId($template_id);
    }
}