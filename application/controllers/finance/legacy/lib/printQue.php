<?
$serverPort	= $_SERVER['REMOTE_ADDR'];	

$Q = "select * from printque where source_ip='$serverPort'";
$QR = @pg_query($Q) or message("Error querying printer database...");
if (@pg_num_rows($QR) > 0)
{
	
	$R = @pg_fetch_object($QR);
	$SYSCONF['serverPort'] = $R->destination_ip;
	//$serverPort	= $_SERVER['REMOTE_ADDR'];	
	//$serverPort='192.168.1.2';
}	
else
{
	$SYSCONF['serverPort'] = $serverPort;
}

?>