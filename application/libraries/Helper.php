<?php

namespace Library;


use \Library\Security;
use \Library\UserSecurity;
use \Library\Helper\Config;
use \Library\Helper\Url;

class Helper {

    public $Security;
    public $Url;
    public $Config;

    public function getSecurity() {

        // place holder for legacy using "admin" table
        if (false && false == is_object($this->Security))
            $this->Security = new Security();
        elseif (false == is_object($this->Security))
            $this->Security = new UserSecurity();
                
            
        return $this->Security;
    }

    public function getConfig() {
        if (false == is_object($this->Config))
            $this->Config = new Config();

        return $this->Config;

    }

    public function getUrl() {

        if (false == is_object($this->Url))
            $this->Url = new Url();

        return $this->Url;

    }

}