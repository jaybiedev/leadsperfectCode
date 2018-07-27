<?php

namespace Library\Helper;


class Utils {


    private $CI;

    function __construct()
    {
            $this->CI =& get_instance();
    }

    static public function generateToken($Site, $timestamp, $seed='Jaybskie', $method='') {
        $token = md5($Site->guid . $Site->slug . $seed . $timestamp);
        return $token;
    }
    
    /**
     * simple validation method using md5
     */
    static public function isValidToken($token, $Site, $timestamp, $seed='Jaybskie', $method='') {
        $_token = md5($Site->guid . $Site->slug . $seed . $timestamp);
        return ($_token === $token);
    }
    


}