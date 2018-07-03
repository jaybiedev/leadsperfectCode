<?php

namespace Library\Helper;


class Config {


    private $CI;

    function __construct()
    {
            $this->CI =& get_instance();
    }


    public function get($name) {
        return $this->CI->config->item($name);
    }

    public function getCompanyName() {
        $other = $this->get('other');
        return $other['company_name'];
    }


}