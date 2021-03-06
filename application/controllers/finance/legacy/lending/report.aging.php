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
if (!chkRights2('financereport','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Finance Reports ]...");
	exit;
}


if ($date == '') $date = date('m/d/Y');
if ($p1=='Go' || $p1=='Print Draft')
{
	$mdate = mdy2ymd($date);
	
	if ($p1 == 'Print Draft')
	{
		doPrint("<small3>");
	}
	$q = "select *
		from 
			releasing,
			account,
			account_group
		where 
			account.account_id=releasing.account_id and
			account.account_group_id = account_group.account_group_id and
			releasing.balance>0 and  
			releasing.status!='C' ";
	if ($account_group_id != '')
	{
		$q .= " and account_group.account_group_id='$account_group_id' ";
	}
	$q .= " order by account.account_group_id, account ";

	$qr = @pg_query($q) or message(pg_errormessage());
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('AGING OF ACCOUNTS AS OF '.$date,80)."\n";
	$header .= center('Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$header .= "---- ------------------------------ ------------ ------------ ---------- ------------ ------------ ------------ ------------ ------------ ------------\n";
	$header .= "     Name of Account                 Balance       Ammort.    Last Pay      1 Month      2 Months     3 Months     4 Months   Long Overdue   Total  \n";
	$header .= "---- ------------------------------ ------------ ------------ ---------- ------------ ------------ ------------ ------------ ------------ ------------\n";
	$details = $details1 = '';
	//$details1 = $header;

	$lc = 10;
	$total_amount = $total_month1 = $total_month2 = $total_month3= $total_month4= $total_month5 = $total_due=0;
	$ctr=0;
	while ($r = @pg_fetch_object($qr))
	{
		recalculate($r->releasing_id,'noneform');
		if ($r->balance <= 0) continue;
		if ($maccount_group_id!=$r->account_group_id)
		{
			if ($lc > 50)
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= $header.$details;
				$lc = 10;
				$details = '';
			}		
			if ($maccount_group_id != '')
			{
				$details .= "\n";
				$lc++;
			}
			$details .= adjustSize(strtoupper(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id)),30)."\n";
			$details .= str_repeat('-',30)."\n";
			$maccount_group_id = $r->account_group_id;
			$lc++;
			$lc++;
		}
		
		$months = $days = $month1 = $month2 = $month3 = $month4 = $month5 = $amount_due = 0;
		$maccount_id = $r->account_id;
		$q = "select * from ledger where releasing_id='$r->releasing_id' and credit>0
					order by date desc offset 0 limit 1";
		$qqr = @pg_query($q) or message(pg_errormessage());
		$lastpay = '';
		$lastdate = '';
		if (@pg_num_rows($qqr) > 0)		
		{
			$rr = @pg_fetch_object($qqr);
			$lastpay = ymd2mdy($rr->date);
			$lastdate = ymd2mdy($rr->date);
		}	
		else
		{
			$lastdate = ymd2mdy($r->releasing_date);
		}
		//$profile = releaseProfile($r->releasing_id,$from_date);
//		$period = monthDiff($lastdate,$date);
		
		$arr = null;
		$arr = array();
		$arr['releasing_id'] = $r->releasing_id;
		$arr['account_id'] = $r->account_id;
		$arr['balance'] = $r->balance;
		$arr['releasing_date']  = $r->date;
		$arr['ammort'] = $r->ammort;
		$arr['term'] = $r->term;
		
		$aAd = amountDue($arr,$mdate);
		$months = $aAd['months_due'];
/*
		if ($r->account == 'Yap, Annalou') 
		{
			print_r($aAd);
		}
*/		

		if ($aAd['amount_due'] <= 0) continue;
		$ctr++;	
		$details .= adjustRight($ctr,3).'. '.adjustSize($r->account,30).' '.
					adjustRight(number_format($r->balance,2),12).' '.
					adjustRight(number_format($r->ammort,2),12).' '.
					adjustSize($lastpay,10).' ';
					
//		$d=explode('-',$profile['releasing_date']);
//		$day_of_releasing=$d[2];
//		$date_month4 = date("m/d/Y",mktime(0, 0, 0, date("m")-4, date("d"),   date("Y")));
//		$months = monthDiff(ymd2mdy($profile['last_payment']),$date);
		
		//aging
//		$months = $period;
		if ($months > 4)
		{
			$month5 = $aAd['amount_due'] - 4*$r->ammort;
			$months = 4;
		}
		elseif ($aAd['amount_due'] > 0)
		{
			$month5 = $aAd['amount_due'] - $months*$r->ammort;
		}

		while ($months>0)
		{
			$fld='month'.$months;
			$$fld = $r->ammort;
			$months--;
		}
		// totals
		$amount_due += $month1 + $month2 + $month3 + $month4 + $month5;
		$total_amount+= $month1 + $month2 + $month3 + $month4 + $month5;
		$total_month1 += $month1;
		$total_month2 += $month2;
		$total_month3 += $month3;
		$total_month4 += $month4;
		$total_month5 += $month5;
		
		$details .= adjustRight(number_format2($month1,2),12).' '.
					adjustRight(number_format2($month2,2),12).' '.
					adjustRight(number_format2($month3,2),12).' '.
					adjustRight(number_format2($month4,2),12).' '.
					adjustRight(number_format2($month5,2),12).' '.
					adjustRight(number_format2($amount_due,2),12)."\n";
		$lc++;
		if ($lc > 55)
		{
			if ($p1=='Print Draft')
			{
				doPrint($header.$details."<eject>");
			}
			$details1 .= $header.$details;
			$lc = 10;
			$details = '';
		}		
	}
	$details .= "---- ------------------------------ ------------ ------------ ---------- ------------ ------------ ------------ ------------ ------------ ------------\n";
	$details .= space(8).adjustSize('***** TOTALS *****',35).space(30).
				adjustRight(number_format2($total_month1,2),12).' '.
				adjustRight(number_format2($total_month2,2),12).' '.
				adjustRight(number_format2($total_month3,2),12).' '.
				adjustRight(number_format2($total_month4,2),12).' '.
				adjustRight(number_format2($total_month5,2),12).' '.
				adjustRight(number_format2($total_amount,2),12)."\n";
	$details .= "---- ------------------------------ ------------ ------------ ---------- ------------ ------------ ------------ ------------ ------------ ------------\n";
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details);
	}	
}
if ($date == '') $date=date('m/d/Y');	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Aging 
          of Accounts</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
                Group </font></td>
              <td colspan="2" valign="top">
                <select name="account_group_id" id="account_group_id">
                  <option value=''>All</option>
                  <?
			  	$q = "select  account_group,account_group_id
						from
							account_group
						where
							enable
						order by
							account_group";
				$qr = pg_query($q);
				while ($r = pg_fetch_object($qr))
				{
					if ($r->account_group_id == $account_group_id)
					{
						echo "<option value=$r->account_group_id selected>$r->account_group</option>";
					}
					else
					{
						echo "<option value=$r->account_group_id>$r->account_group</option>";
					}	
				}
			  ?>
                </select>
              </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">As 
                of </font></td>
              <td width="501" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="date" type="text" id="date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.date, 'mm/dd/yyyy')"> 
                </font></td>
              <td width="223" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p132" type="submit" id="p132" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
