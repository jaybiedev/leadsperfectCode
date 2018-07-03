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
if (!chkRights2('payment','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Payment View Reports ]...");
	exit;
}

if ($from_date=='') $from_date=date('m/d/Y');
if ($to_date=='') $to_date=date('m/d/Y');

if (($p1=='Go' || $p1=='Print Draft') && ($from_date == ''|| $to_date==''))
{
	message('Please provide date coverage...');
}
elseif ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);

	$q = "select 
			payment_header.payment_header_id,
			payment_header.reference,
			payment_header.total_amount,
			payment_header.date,
			account_group.account_group_id,
			account_group.account_group
		from 
			payment_header,
			account_group
		where 
			account_group.account_group_id=payment_header.account_group_id and
			date>='$mfrom_date' and 
			date<='$mto_date'";
	
	if ($account_group_id != '')
	{
		$q .= " and account_group.account_group_id = '$account_group_id'";
	}
	$q .= " order by date";

	$qr = @pg_query($q) or message(pg_errormessage());

	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('SUMMARY OF COLLECTION/PAYMENTS',80)."\n";
	$header .= center($from_date.' To '.$to_date,80)."\n\n";
	$header .= "   Date         Ref No.   OR No.    Account Group               Amount \n";
	$header .= "  -----------  -------- --------- ------------------------- ---------------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	while ($r = pg_fetch_object($qr))
	{

		if ($p1 == 'Print Draft'  && rtype=='D') $details .= "<bold>";
		$details.= '   '.adjustSize(ymd2mdy($r->date),10).'  '.
					adjustSize(str_pad($r->payment_header_id,8,'0',str_pad_left),8).'  '.
					adjustSize($r->reference,8).' '.
					adjustSize(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id),25).' '.
					space(3).
					adjustRight(number_format($r->total_amount,2),12)."\n";

		$lc++;
		if ($rtype=='D')
		{
			$q = "select account.account, 
						payment_detail.amount 
					from 
						payment_detail, account
					where 
						account.account_id=payment_detail.account_id and 
						payment_detail.payment_header_id='$r->payment_header_id'";
			$qqr = pg_query($q) or die (pg_errormessage());
			$subtotal = 0;
			$detailsx='';
			while ($rr = pg_fetch_object($qqr))
			{
				$lc++;
				$subtotal += $rr->amount;
				$detailsx .= space(5).
							adjustSize($rr->account,30).'  '.
							space(10).
							adjustRight(number_format($rr->amount,2),10)."\n";
							
				if ($lc>55 && $p1 == 'Print Draft')
				{
					$details .= $detailsx;
					$details1 .= $header.$details;
					$details .= "<eject>";
					doPrint($header.$details);
					$details = $detailsx = '';
					$lc=6;
				}			
			}	
			if ($p1 == 'Print Draft'  && rtype=='D') $details .= "</bold>";
			$details .= $detailsx;
			$details .= "\n";
			$lc++;
		}
		$total_amount += $r->total_amount;
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);

			$details = '';
			$lc=6;
		}			
	}

	$details .= "  -----------  -------- --------- ------------------------- -----------------\n";
	$details .= space(40).adjustSize('TOTAL AMOUNT ->',25).
				adjustRight(number_format($total_amount,2),12)."\n";
	$details .= "  -----------  -------- --------- ------------------------- -----------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	
}	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Payment/Collection</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td colspan="2" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                </font> </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                To </font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                </font></td>
            </tr>
            <tr> 
              <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></td>
              <td width="501" valign="top"> 
                <?= lookUpAssoc('rtype',array('Summary'=>'S','Detailed'=>'D'), $rtype);?>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
                Group </font> 
                <?
		$q = "select * from account_group where enable order by account_group ";
		$qr = pg_query($q) or message(pg_errormessage());
		echo "<select name='account_group_id'>";
		echo "<option value=''>All Accounts</option>";
		while ($r= pg_fetch_object($qr))
		{
			if ($account_group_id == $r->account_group_id)
			{
				echo "<option value=$r->account_group_id selected>$r->account_group</option>";
			}
			else
			{
				echo "<option value=$r->account_group_id>$r->account_group</option>";
			}

		}
		echo "</select>";
	   ?>
              </td>
              <td width="223" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="print_area" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
