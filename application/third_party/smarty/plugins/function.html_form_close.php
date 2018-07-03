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
function smarty_function_html_form_close($params, &$smarty)
{
    echo "</form>";
}