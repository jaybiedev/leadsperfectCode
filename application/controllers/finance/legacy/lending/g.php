<?
include_once('../lib/library.php');
include_once('../lib/dbconfig.php');
include_once('../lib/connect.php');

$qr = pg_query("select * from account_group");
while ($r = pg_fetch_object($qr))
{
	$q = "update account set account_group_id='$r->account_group_id'
			where trim(account_code)='".trim($r->account_group_code)."'";
	pg_query($q) or die (pg_errormessage());
}
?>