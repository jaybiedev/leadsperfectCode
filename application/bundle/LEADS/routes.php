<?php

function getRoutes()
{
    $route = array();

    $route[ 'default_controller' ]  = 'leads';
    
    $route['api/([a-z]+)/([a-z]+)/(:num)'] = 'api_$1/$2/$2/$3';
    // such as admin/user -> admin/usercontroller
    
    $route['dashboard'] = 'LeadsControllers/DashboardController';
    $route['dashboard/(:any)'] = 'LeadsControllers/DashboardController/$1';
    $route['dashboard/(:any)/(:any)'] = 'LeadsControllers/DashboardController/$1/$2';
    $route['dashboard/(:any)/(:any)/(:any)'] = 'LeadsControllers/DashboardController/$1/$2/$3';
    
    $route['microservices/(:any)'] = 'LeadsControllers/microservices/WebserviceController/$1';
    $route['microservices/(:any)/(:any)'] = 'LeadsControllers/microservices/WebserviceController/$1/$2';
    
    $route['admin'] = 'leads/admin';
    $route['admin/(:any)'] = 'leads/admin';
    
    $route['login'] = 'security';
    $route['logout'] = 'security/logout';
    
    // $route['(:any)'] = 'leads/admin/index/$1';
    $route['(:any)/(:any)'] = 'leads/slug';
    
    return $route;
}