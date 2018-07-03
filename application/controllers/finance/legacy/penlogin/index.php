<?php
chdir(__DIR__);


$p = $_REQUEST['p'];

require_once("../helpers/php4compat.php");

require_once("../xajax.inc.php");
$xajax = new xajax();
$g = new stdClass();

$g->objResponse = new xajaxResponse();

require_once("../lib/library.php");
require_once("../lib/dbconfig.php");
require_once("../lib/connect.php");

require_once("../lib/xajax__hope.lib.php");
include_once('xajax.lending.php');
//	$xajax->debugOn();
$xajax->processRequests();

include_once('../lib/library.js.php');

if ($ADMIN == null)
{
    legacy_redirect($this->Helper->getUrl()->getLoginUrl());
}

if ($p != null &&  file_exists("$p.php"))
{
    include_once("$p.php");
}
else
{
    include_once("penlogin.php");
}