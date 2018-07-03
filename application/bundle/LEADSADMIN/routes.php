<?php

function getRoutes()
{
    $route = array();

    $route['api/([a-z]+)/([a-z]+)/(:num)'] = 'api_$1/$2/$2/$3';
    $route['signup'] = 'common/user/register';
    $route['dashboard/([a-z]+)'] = 'dashboard/$1';
    $route['(:any)'] = 'Leadsadmin';
    $route['(.+)'] = 'Leadsadmin/catchall';

    return $route;
}