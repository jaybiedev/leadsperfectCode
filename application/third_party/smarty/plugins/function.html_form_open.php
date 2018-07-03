<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {ci_form} function plugin
 * @param Smarty
 */
function smarty_function_html_form_open($params, &$smarty)
{

    $action = $params['action'];
    $attributes = $params['attributes'] || '';
    $hidden = $params['hidden'] || array();

   echo form_open($action, $attributes , $hidden);

}