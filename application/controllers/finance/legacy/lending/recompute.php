<?
$q = "select * from releasing";
$qr = pg_query($q) or die (pg_errormessage());

while ($r=pg_fetch_object($qr))
{
	updateReleasing($r->releasing_id);
}
?>