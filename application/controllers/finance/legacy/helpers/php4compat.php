<?php
/**
 *  This is bootstrap for legacy php4.0
 */

use Library\SessionManager;

define('LEGACY_SESSION_PRIVATIZER', 'LEGACY');

// definition of legacy gobal session variables
$GLOBALS['global_legacy_variables'] = array();


// require_once("classes/SessionManager.php");
$SessionManagerLegacy = new Library\SessionManager(null, LEGACY_SESSION_PRIVATIZER);

// workaround globals for now
if (! isset($PXM_REG_GLOB)) {

    $PXM_REG_GLOB = 1;

    if (! ini_get('register_globals')) {
        foreach (array_merge($_GET, $_POST) as $key => $val) {
            global $$key;
            $$key = (get_magic_quotes_gpc()) ? $val : addslashes($val);
        }
    }
    if (! get_magic_quotes_gpc()) {
        foreach ($_POST as $key => $val) $_POST[$key] = addslashes($val);
        foreach ($_GET as $key => $val)  $_GET[$key]  = addslashes($val);
    }
}
//


// retrieve the list of legacy session variables from session
if (empty($global_legacy_variables)) {

    if ($SessionManagerLegacy->Has('global_legacy_variables')) {
        $GLOBALS['global_legacy_variables'] = $SessionManagerLegacy->Get('global_legacy_variables');
    }
    else {
        // default session variables
        $GLOBALS['global_legacy_variables'] = array('ADMIN',
            'SYSCONF',
        );
    }

}

// retrieve legacy session variables and declare as globals
foreach ($GLOBALS['global_legacy_variables'] as $variable) {

    global $$variable;

    if ($SessionManagerLegacy->Has($variable)) {
        $$variable = $SessionManagerLegacy->Get($variable);
    }
}

if (false == function_exists('pprint_r')) {
    function pprint_r($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        return;
    }
}

if (false == function_exists('pg_error')) {
    function pg_error()
    {
        return pg_last_error();
    }
}

if (false == function_exists('fetch_assoc')) {

    function fetch_assoc($q)
    {
        $qR = @pg_exec($q) or message("Error query. " . pg_errormessage());
        $R = @pg_fetch_assoc($qR);
        return $R;
    }
}


if (false == function_exists('fetch_object')) {
    function fetch_object($Q)
    {
        $qR = @pg_exec($Q) or message("Error query. ".pg_errormessage()." ".$Q);
        $R = @pg_fetch_object($qR);
        return $R;
    }
}



if (false == function_exists('session_is_registered')) {
    function session_is_registered($name) {
        $SessionManagerLegacy = new Library\SessionManager(null, LEGACY_SESSION_PRIVATIZER);
        return $SessionManagerLegacy->Has($name);
    }
}


if (false == function_exists('session_register')) {

    function session_register($name) {

        // add to the list of legacy session variables
        if (!in_array($name, $GLOBALS['global_legacy_variables'])) {
            $GLOBALS['global_legacy_variables'][] = $name;
        }

        $SessionManagerLegacy = new Library\SessionManager(null, LEGACY_SESSION_PRIVATIZER);
        return $SessionManagerLegacy->Put($name, null);
    }
}


function save_legacy_session_variables() {

    $SessionManagerLegacy = new Library\SessionManager(null, LEGACY_SESSION_PRIVATIZER);
    $SessionManagerLegacy->Put('global_legacy_variables', $GLOBALS['global_legacy_variables']);


    // flush legacy session variables to session
    foreach ($GLOBALS['global_legacy_variables'] as $variable) {

        global $$variable;

        $SessionManagerLegacy->Put($variable, $$variable);
    }
}

register_shutdown_function('save_legacy_session_variables');
