<?php

function getRoutes()
{
    $route = array();

    $route[ 'default_controller' ]  = 'leads';
    
    $route['api/([a-z]+)/([a-z]+)/(:num)'] = 'api_$1/$2/$2/$3';
    // such as admin/user -> admin/usercontroller
    // $route['dashboard/site/(:any)'] = 'leads/dashboard/site/$1';
    
    $route['dashboard'] = 'leads/dashboard';
    $route['dashboard/(:any)'] = 'leads/dashboard';
    $route['dashboard/(:any)/(:any)'] = 'leads/dashboard';
    
    $route['webservice/(:any)/(:any)'] = 'leads/dashboard';
    
    $route['admin'] = 'leads/admin';
    $route['admin/(:any)'] = 'leads/admin';
    
    $route['login'] = 'security';
    $route['logout'] = 'security/logout';
    
    // $route['(:any)'] = 'leads/admin/index/$1';
    $route['(:any)/(:any)'] = 'leads/slug';
    
    return $route;
}