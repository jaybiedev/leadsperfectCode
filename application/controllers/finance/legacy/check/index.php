<title>Management Information System - Warehouse</title>
<?
	include_once("../lib/library.php");
	require_once("../lib/dbconfig.php");

	include_once("menu.php");
	require_once("../lib/connect.php");

	if ($ADMIN == null) // || $KURUKOKUK!=lango(1))
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
			message("<img src='../graphics/update.gif'> ".$p." file does not exists or undercontruction ...");
			include_once("home.htm");
		}
	}
	else {	 
		include_once("home.htm");
	}
?>
