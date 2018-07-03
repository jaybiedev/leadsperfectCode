 <script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<?
if (($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print') && $account_group_id=='')
{
	message('Specify Account and Click GO...');
}
elseif ($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print')
{
	$date=date('m/d/Y');
	$mdate = mdy2ymd($date);
	
	$q = "select * 	from account, releasing
				where
					releasing.account_id=account.account_id ";
	if ($account_group_id != '')
	{
		$q .= " account.account_group_id='$account_group_id'";
	}
	$q .= " order by releasing.date, account";
				

	$qr = pg_query($q) or message(pg_errormessage());
	if ($p1 == 'Print Draft')
	{
		$header = "<small3>";
	}
	else
	{
		$header = '';
	}
	$header .= center($SYSCONF['BUSINESS_NAME'],130)."\n";
	$header .= center('RECEIVABLE LIST',130)."\n";
	$header .= center('Report Date '.$from_date.' To '.$to_date,130)."\n\n";

	if ($p1 == 'Print Draft') $header .= "<bold>";
	$header .= space(5)."Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id)."\n";
	if ($p1 == 'Print Draft') $header .= "</bold>";
	$header .= space(5)."---- ----------------------------------- ---------- ---------- ---------- ------------ ----------- ------------\n";
	$header .= space(5)."  #   NAME OF ACCOUNT                     RELEASED   LAST PAY   APPLIED     BALANCE    AMOUNT DUE   TOTAL DUE  \n";
	$header .= space(5)."---- ----------------------------------- ---------- ---------- ---------- ------------ ----------- ------------\n";

	$maccount_group_id='';
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		//if condition for zero balance
		/*
		$q = "select * from releasing 
							where 
								account_id='$r->account_id'
							order by
								releasing.date desc";
		$qqr = pg_query($q) or message(pg_errormessage());
		if (pg_num_rows($qqr) == 0) continue;
		else	$rr = pg_fetch_object($qqr);
		*/
		$q = "select * from ledger where account_id='$r->account_id'
					order by date";
		$qqr = pg_query($q) or message(pg_errormessage());

		$balance = 0;
		$lastpay = '';
		if (pg_num_rows($qqr) == 0) continue;
		else
		{
			while ($rrr = pg_fetch_object($qqr))
			{
				$balance += $rrr->debit-$rrr->credit;
				if ($rrr->type=='C' or $rr->advance_payment >0)
				{
					$lastpay = ymd2mdy($rrr->date);
				}
			}
		}	
							

/*		if ($maccount_group_id != $r->account_group_id)
		{
			$details .= space(5).str_repeat('-',40)."\n";
			$lc += 2;
			$maccount_group_id = $r->account_group_id;
		}
*/		
		$ctr++;
		$details.=  space(5).adjustRight($ctr,3).'. '.
					adjustSize($r->account,35).' '.
					adjustSize(ymd2mdy($r->date),10).' '.
					adjustSize($lastpay,10).' '.
					space(10).' '.
					adjustRight(number_format($balance,2),12).' '.
					adjustRight(number_format($r->ammort,2),11).' '.
					adjustRight(number_format($r->ammort,2),11).' '.
					"\n";
		$total_amount += $rr->ammort;			
		$lc++;			
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);
			$details = '';
			$lc=6;
		}			
	}

	$details .= space(5)."---- ----------------------------------- ---------- ---------- ---------- ------------ ----------- ------------\n";
	$details .= space(65).adjustSize('TOTAL AMOUNT ->',35).'   '.
				adjustRight(number_format($total_amount,2),12)."\n";
	$details .= space(5)."---- ----------------------------------- ---------- ---------- ---------- ------------ ----------- ------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "\n\n<eject>\n\n";
		doPrint($header.$details);
	}	
}	

?>
<form action="" method="post" name="f1" id="f1">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr> 
      <td bgcolor="#FFFFFF"><font size="4" face="Times New Roman, Times, serif"><strong>Receivable 
        List</strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">: 
        Date From 
        <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"></font> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> To 
        <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
        </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Group 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" size="22">
        <input name="account_group_id" type="hidden" id="account_group_id" value="<?=$account_group_id;?>" size="5">
        <input name="p1" type="submit" id="p1" value="Search">
        <input name="p1" type="submit" id="p1" value="Go">
        </font> 
        <hr color="#CC3300"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        </font></td>
    </tr>
  </table>
<?
if (in_array($p1,array('Go','Print','Print Draft')))
{
?>
  <div align="center">
    <table width="1%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td width="38%"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17">Receivale 
                List Preview</strong></font></td>
              
            <td width="62%" align="right" nowrap><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              </strong></font> </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="90" rows="18" readonly wrap="OFF"><?= $details1;?></textarea>
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
 <?
 } //print preview
 elseif ($p1 == 'Search')
 {
 ?>
	<table width="50%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
	  <tr bgcolor="#330099"> 
		<td height="19" colspan="2"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Select 
		  Account Group For Report</strong></font></td>
	  </tr>
 <?
 	if ($xSearch == '')
	{
	 	$q = "select * from account_group where enable order by account_group";
	}
	else
	{
	 	$q = "select * from account_group where account_group ilike '$xSearch%' and enable order by account_group";
	}
	
	$qr = pg_query($q) or die (pg_errormessage());
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location='?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>'"> 
    <td width="8%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$ctr;?>.&nbsp;</font></td>
    <td width="92%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<a href="?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>">
	<?= $r->account_group;?></a></font></td>
  </tr>
		<?
	}
	?>
	</table>
	<?
 }
 ?> 
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
