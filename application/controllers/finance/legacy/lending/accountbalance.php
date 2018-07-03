<?
function excessBalance($aid)
{

	$aPost = null;
	$aPost = array();
	$q = "select 
					sum(excess) as excess
				from
					payment_detail,
					payment_header
				where
					payment_header.payment_header_id = payment_detail.payment_header_id and
					payment_detail.account_id ='$aid' and status != 'C' and
					NOT (mcheck ='T')";  //in ('G','T'))";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r = @pg_fetch_object($qr);
	$debit = $r->excess;

	$q = "select 
					sum(gross_amount) as loan_debit
				from
					wexcess
				where
					wexcess.status != 'C' and
					(wexcess.type = 'D' or wexcess.type = 'T' or wexcess.type = 'G') and 
					wexcess.account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r = @pg_fetch_object($qr);
	$debit += $r->loan_debit;

	$q = "select 
					sum(gross_amount) as withdrawn
				from
					wexcess
				where
					wexcess.status != 'C' and
					wexcess.type = 'C'  and 
					wexcess.account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r = @pg_fetch_object($qr);
	$credit = $r->withdrawn;
	
	$balance = $debit - $credit;
	
	$aPost['debit'] = $debit;
	$aPost['credit'] = $credit;
	$aPost['balance'] = $balance;
	
	return $aPost;
	
}
?>