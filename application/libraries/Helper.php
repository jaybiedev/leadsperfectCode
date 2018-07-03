<?php

namespace Library;


use \Library\Security;
use \Library\Helper\Config;
use \Library\Helper\Url;

class Helper {

    public $Security;
    public $Url;
    public $Config;

    public function getSecurity() {

        if (false == is_object($this->Security))
            $this->Security = new Security();

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