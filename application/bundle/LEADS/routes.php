<?php

function getRoutes()
{
    $route = array();

    $route[ 'default_controller' ]  = 'leads';


    $route['api/([a-z]+)/([a-z]+)/(:num)'] = 'api_$1/$2/$2/$3';
    $route['signup'] = 'common/user/register';
    $route['dashboard/([a-z]+)'] = 'leads/dashboard/dashboardcontroller/$1';

    $route['admin'] = 'leads/admin/admincontroller';

    // such as admin/user -> admin/usercontroller
    $route['admin/([a-z]+)'] = 'leads/admin/$1controller';
    $route['admin/([a-z]+)/(:num)'] = 'leads/admin/$1controller/index/$2';

    // $route['(:any)'] = 'leads/admin/index/$1';
    $route['(.+)'] = 'leads/slug';
    // $route['(:any)'] = 'leads/front/frontcontroller/home';

    return $route;
}