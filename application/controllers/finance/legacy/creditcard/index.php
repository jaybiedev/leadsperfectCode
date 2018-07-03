<title>Management Information System - Credit Card</title>
<?
	include_once("../lib/library.php");
	require_once("../lib/dbconfig.php");

	require_once("../lib/connect.php");
	include_once("../var/system.conf.php");
	include_once("menu.creditcard.php");

	if ($ADMIN == null) 
	{
		message("Security Check. Please Login Again");
		$p='login';
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
?>
<body bgcolor="#EFEFEF">