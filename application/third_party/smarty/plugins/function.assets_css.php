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
function smarty_function_assets_css($params, &$smarty)
{
    $attr = array();
    $files = array();
    if (!empty($params['files']))
        $files = $params['files'];

    if (!empty($params['attr']))
        $attr = params['attr'];

    if (!empty($files)) {
        echo assets_css($files, $attr);
    }
}