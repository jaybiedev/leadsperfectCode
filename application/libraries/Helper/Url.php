<?php

namespace Library\Helper;

class Url {


    public function getBaseUrl() {
        return base_url();
    }

    public function getUrl($path) {
        return base_url() . (substr(base_url(), -1) == '/' ? '' : '/') . "{$path}/";
    }


    public function getLoginUrl() {
        return base_url() . (substr(base_url(), -1) == '/' ? '' : '/') . "login/";
    }


}