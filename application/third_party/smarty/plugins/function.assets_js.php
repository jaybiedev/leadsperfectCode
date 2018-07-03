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
function smarty_function_assets_js($params, &$smarty)
{
    $files = array();
    if (!empty($params['files']))
        $files = $params['files'];

    
    if (!empty($files))
        echo assets_js($files);

}