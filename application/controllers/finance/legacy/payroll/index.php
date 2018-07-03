<?php
    chdir(__DIR__);

    // CI method
    $this->View->setPageTitle("");
    $p = $this->input->get_post('p', null);
    $p1 = $this->input->get_post('p1', null);

    require_once("../helpers/php4compat.php");

    require_once("../xajax.inc.php");
    $xajax = new xajax();
    $g = new stdClass();

    $g->objResponse = new xajaxResponse();

    require_once("../lib/library.php");
    require_once("../lib/dbconfig.php");
    require_once("../lib/connect.php");

    require_once("../lib/xajax__hope.lib.php");

	include_once('xajax.payroll.php');


	$xajax->registerFunction('viewmemo');
	$xajax->registerFunction('payroll_posting');
	$xajax->registerFunction('paymastLevel');
    $xajax->registerFunction('payroll_unpost');
    $xajax->registerFunction('payroll_recalc_account');


    $xajax->processRequests();

    include_once("../lib/library.js.php");
	if (!chkRights3("payrollmodule","mview",$ADMIN['admin_id']))
	{
		message("You have no permission in this area...");
		return;
	}

	$q = "set search_path to public,payroll";
	@pg_query($q);
	include_once('payroll.lib.php');
	include_once('config.payroll.php');


	if ($p == 'logout')
	{
    $date_out = date('Y-m-d g:ia');
    $q = "update userlog set 
    				date_out='$date_out' 
    			where 
    				userlog_id='".$ADMIN['userlog_id']."' and 
    				admin_id='".$ADMIN['admin_id']."'";

    $qr = @pg_query($q) or message(pg_errormessage());

		$ADMIN=null;
		session_unset();

		message("User has sucessfully logged Out.");
		require_once('login.php');
		exit;
	}

	if ($p != null) 
	{
		if (file_exists("$p.php"))
		{
			include_once("$p.php");	
		}
		else
		{
			message($p." file does not exists...");
			include_once("home.htm");
		}
	}
	else {	 
		include_once("home.php");
	}