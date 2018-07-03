<?php

function getRoutes()
{
    /** API ROUTES: Get Method/Verb
     *  mapping:
     *  api/class/method_get/variable_value
     *
     *  controller is mapped second segment of the URL : controller/api_{class}.php
     *  controller method is mapped as third segment of the URL : {method}_get()
     *  controller variable is also mapped as the third segment of the URL
     *  controller variable value is mapped as the fourth segment of the URL
     *
     *  api/product/id/123
     *  api/product/barcode/12345678
     */

    $route = array();
    
    $route['api/([a-z]+)/([a-z]+)/(:num)'] = 'api_$1/$2/$2/$3';
    $route['dashboard/([a-z]+)'] = 'dashboard/$1';
    $route['test'] = 'test';
    $route['test/file-list'] = 'test';
    $route['login'] = 'user/login';
    $route['logout'] = 'user/logout';


    return $route;
}