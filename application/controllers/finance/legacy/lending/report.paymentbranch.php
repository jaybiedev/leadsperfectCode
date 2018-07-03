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

	$q = "select account.account, 
				account.clientbank_id, 
				payment_header.reference, 
				payment_header.date,
				payment_detail.amount,
				payment_detail.withdrawn,
				payment_detail.excess,
				payment_detail.mischarge,
				payment_detail.discount,
				payment_detail.ddate,
				account.branch_id,
				account.account_code, 
				account_group.account_group, 
				branch.branch,
				branch.branch_code,
				clientbank.clientbank
			from 
				payment_detail, 
				payment_header,
				account,
				account_group,
				branch,
				clientbank
			where 
				payment_header.payment_header_id = payment_detail.payment_header_id and 
				account_group.account_group_id=account.account_group_id and 
				clientbank.clientbank_id = account.clientbank_id and 
				branch.branch_id=account.branch_id and 
				account.account_id=payment_detail.account_id and 
				payment_header.date>='$mfrom_date' and
				payment_header.date<='$mto_date' and
				payment_header.status != 'C'";


//				payment_detail.ddate>='$mfrom_date' and
//				payment_detail.ddate<='$mto_date' ";

				
	if ($branch_id != '')
	{
		$q .= " and account.branch_id = '$branch_id'";
	}
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id = '$account_group_id'";
	}
	$q .= "	order by branch, clientbank, account, date";
	$qr = pg_query($q) or message(pg_errormessage());

	if ($p1 == 'Print Draft')
	{
				doPrint('<small3>');
	}
	$page=1;
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],136)."\n";
	$header .= center('SUMMARY OF PAYMENTS/COLLECTION PER BRANCH',136)."\n";
	$header .= center($from_date.' To '.$to_date.'   Page '.$page,136)."\n\n";
	$header .= " #      WDate       Group            OR#          Account                 Withdrawn     Ammort       Excess       Charges       Discount\n";
	$header .= "------ ----------- --------------- ---------- ------------------------- ------------ ------------ ------------ ------------ ------------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$mbranch_id = '';

	$subtotal = 0;
	$ctr = 0;
	$mclientbank_id = '';
	$exportdata = '';
	$s_amount = 0;
	$s_excess = 0;
	$s_others = 0;
	$s_withdrawn = $s_others = $s_discount = 0;

	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;
		$lc++;
		
		if ($r->branch_id != $mbranch_id)
		{
			if ($mbranch_id != '')
			{
				$details .= "\n";
				$lc++;
			}
			$mbranch_id = $r->branch_id;
			$details .= space(3)."BRANCH : ".strtoupper($r->branch)."\n";
			$lc++;
		}
		if ($mclientbank != $r->clientbank)
		{
			if ($mclientbank_id != '')
			{
				$details .= space(50).adjustSize('SUB TOTAL ->',22).
					adjustRight(number_format($s_withdrawn,2),12)." ".
					adjustRight(number_format($s_amount,2),12)." ".
					adjustRight(number_format($s_excess,2),12)." ".
					adjustRight(number_format($s_others,2),12)." ".
					adjustRight(number_format($s_discount,2),12)."\n";
				$lc++;
				$s_amount = 0;
				$s_others = 0;
				$s_excess = 0;
				$s_withdrawn = 0;
				
				$details .= "\n";
				$lc++;
			}
			$mclientbank_id = $r->clientbank_id;
			$mclientbank = $r->clientbank;
			if ($mclientbank_id == '') $mclientbank_id = '0';
			$details .= "   BANK : ".$mclientbank."\n"; //ookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$mclientbank_id)."\n";
			$lc++; 
		}
		$subtotal += $rr->amount;
		$total_amount += $r->amount;
		$total_excess += ($r->excess=$r->mischarge?0:$r->excess);   //$r->excess;
		$total_withdrawn += $r->withdrawn;
		$total_others += $r->mischarge;
		$total_discount += $r->discount;		

		$s_amount += $r->amount;
		$s_excess += ($r->excess=$r->mischarge?0:$r->excess);  //$r->excess;
		$s_withdrawn += $r->withdrawn;
		$s_others += $r->mischarge;
		$s_discount += $r->discount;

		$details .= adjustRight($ctr,5).'. '.
					adjustSize(ymd2mdy($r->date),10). '  '.
					adjustSize($r->account_group,15).' '.
					adjustSize($r->reference,10).' '.
					adjustSize($r->account,25).' '.
					adjustRight(number_format($r->withdrawn,2),12).' '.
					adjustRight(number_format($r->amount,2),12).' '.
					adjustRight(number_format(($r->excess=$r->mischarge?0:$r->excess),2),12).' '.
					adjustRight(number_format($r->mischarge,2),12).' '.
					adjustRight(number_format($r->discount,2),12)."\n";
		$branch_code = chop($r->branch_code);			
		$exportdata .= 'branch_code=>'.$r->branch_code.'||'.
								'date=>'.$r->ddate.'||'.
								'account_code=>'.$r->account_code.'||'.
								'withdrawn=>'.$r->withdrawn.'||'.
								'amount=>'.$r->amount.'||'.
								'excess=>'.$r->excess."\n";

		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			if ($page_from == '' or ($page>= $page_from and $page<=$page_to))
			{
				doPrint($header.$details);
			}
			$details = $detailsx = '';

			$page++;
			$header = "\n\n";
			$header .= center($SYSCONF['BUSINESS_NAME'],136)."\n";
			$header .= center('SUMMARY OF PAYMENTS/COLLECTION PER BRANCH',136)."\n";
			$header .= center($from_date.' To '.$to_date.'   Page '.$page,136)."\n\n";
			$header .= " #      WDate       Group            OR#          Account                 Withdrawn     Ammort       Excess       Charges       Discount\n";
			$header .= "------ ----------- --------------- ---------- ------------------------- ------------ ------------ ------------ ------------ ------------\n";
			$lc=6;
		}			
	}	

	$details .= space(50).adjustSize('SUB TOTAL ->',22).
		adjustRight(number_format($s_withdrawn,2),12)." ".
		adjustRight(number_format($s_amount,2),12)." ".
		adjustRight(number_format($s_excess,2),12)." ".
		adjustRight(number_format($s_others,2),12)." ".
		adjustRight(number_format($s_discount,2),12)."\n";
	$details .= space(50).adjustSize('TOTAL AMOUNT ->',22).
				adjustRight(number_format($total_withdrawn,2),12)." ".
				adjustRight(number_format($total_amount,2),12)." ".
				adjustRight(number_format($total_excess,2),12)." ".
				adjustRight(number_format($total_others,2),12)." ".
				adjustRight(number_format($total_discount,2),12)."\n";
	$details .= "------ ----------- --------------- ---------- ------------------------- ------------ ------------ ------------ ------------ ------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	

	if ($branch_id != '')
	{
		
		$aip = explode('.',$_SERVER['REMOTE_ADDR']);

		$reportfile= './reports/COLLECT-'.$branch_code.'-'.$aip[3].'.txt';
//		$fo = fopen($reportfile,'w+');
//		if (@!fwrite($fo, $exportdata))
//		{
//			 message("Unable to create report file...");
//		}
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
              <td width="68" height="24" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">To 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                </font></td>
              <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td valign="top" nowrap><select name = "branch_id">
                  <?
				$q = "select * from branch where enable";
				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
					if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
					if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
					if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
					if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
					if ($ADMIN['branch_id11'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id11']."'";
					if ($ADMIN['branch_id12'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id12']."'";
					if ($ADMIN['branch_id13'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id13']."'";
					if ($ADMIN['branch_id14'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id14']."'";
					if ($ADMIN['branch_id15'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id15']."'";
					if ($ADMIN['branch_id16'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id16']."'";
					if ($ADMIN['branch_id17'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id17']."'";
					if ($ADMIN['branch_id18'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id18']."'";
					if ($ADMIN['branch_id19'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id19']."'";
					if ($ADMIN['branch_id20'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id20']."'";
					$q .= ") ";
				} else
				{
					?>
	                  <option value=''>All Branches</option>
					<?
				}
				$q .= " order by branch";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($branch_id == $r->branch_id)
					{
						echo "<option value=$r->branch_id selected>$r->branch</option>";
					}
					else
					{	
						echo "<option value=$r->branch_id>$r->branch</option>";
					}	
				}
				
			?>
                </select> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font> 
                <select name = "account_group_id">
                  <option value=''>All Account Groups</option>
                  <?
				$q = "select * from account_group where enable order by account_group";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
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
				
			?>
                </select>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pages 
                <input name="page_from" type="text" id="page_from" size="3" maxlength="3">
                To
<input name="page_to" type="text" id="page_to" size="3" maxlength="3">
                </font></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <?
		if (file_exists($reportfile))
		{
			echo "| <a href=$reportfile>Download</a>";
		}
		?>
                </font></td>
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
