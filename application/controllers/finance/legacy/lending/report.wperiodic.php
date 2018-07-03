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
	message("You have no permission in this Area [ Payment View/Reports ]...");
	exit;
}


if ($from_date == '') $from_date = date('m/01/Y');
if ($to_date == '') $to_date = date('m/d/Y');
if ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	
	$q = "select count(payment_detail.account_id) as mcount, clientbank, payment_detail.remark, account.branch_id, sum(withdrawn) as withdrawn
		from 
			payment_header, payment_detail, account, clientbank
		where 
			payment_header.payment_header_id=payment_detail.payment_header_id  and
			account.account_id=payment_detail.account_id and
			clientbank.clientbank_id=account.clientbank_id and
			date_withdrawn >= '$mfrom_date' and
			date_withdrawn <= '$mto_date'";
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id' ";
	}
	$q .= " group by account.branch_id, clientbank, payment_detail.remark ";
	
	$qr = @pg_query($q) or message(pg_errormessage());
//	if ($p1 == 'Print Draft') $header = "\n\n<small3>";
//	else $header = '';
	$header = '';
	
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('ATM PERIODIC SUMMARY '.$date,80)."\n";
	$lc = 11;
	$header .= center('Printed '.date('m/d/Y g:ia'),80)."\n\n";

	$header .= "---- ------------------------- ------ ----------------------------------- -------------\n";
	$header .= "     Bank                      #ATMs          Remarks                        Amount \n";
	$header .= "---- ------------------------- ------ ----------------------------------- -------------\n";
	$details = $details1 = '';
	//$details1 = $header;

	$lc = 10;
	$ctr=0;
	while ($r = @pg_fetch_object($qr))
	{
		//updateReleasing($r->releasing_id);
		if ($mbranch_id!=$r->branch_id)
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
			if ($mbranch_id != '')
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->branch_id == '')
			{
				$details .= adjustSize('NO BRANCH',60)."\n";
			}
			else
			{
				$details .= strtoupper(adjustSize(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id),60))."\n";
			}	

			$mbranch_id = $r->branch_id;
			$lc++;
			$lc++;
		}
		$ctr++;	
		$details .= adjustRight($ctr,3).'. '.adjustSize($r->clientbank,25).' '.
					adjustRight($r->mcount,5).'  '.
					adjustSize($r->remark,34).' '.
					adjustRight(number_format($r->withdrawn,2),14)."\n";
					
		$total_count += $r->mcount;
		$total_amount += $r->withdrawn;
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
	$details .= "---- ------------------------- ------ ----------------------------------- -------------\n";
	$details .= space(15)."TOTAL ".space(10).adjustRight($total_count,5).space(35).adjustRight(number_format($total_amount,2),16)."\n";
	$details .= "---- ------------------------- ------ ----------------------------------- -------------\n";
	$details1 .= $header.$details."\n\n\n";
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
	

	if ($p1 == 'Print Draft') $header = "\n\n<small3>";
	else $header = '';
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('ATM PERIODIC SUMMARY '.$date,80)."\n";
	$lc = 11;
	$header .= center('Printed '.date('m/d/Y g:ia'),80)."\n\n";

	$header .= "---- ----------------------------------- ------------------------- --------------- -------------------- ------------- ---\n";
	$header .= "      Name                                Bank                      Account No      Card No.                Amount   \n";
	$header .= "---- ----------------------------------- ------------------------- --------------- -------------------- ------------- ---\n";
	$details = '';
	
	$q = "select account, clientbank, bank_account, bank_cardno, withdrawn, account.branch_id, mconfirm
		from 
			payment_header, payment_detail, account, clientbank
		where 
			payment_header.payment_header_id=payment_detail.payment_header_id  and
			account.account_id=payment_detail.account_id and
			clientbank.clientbank_id=account.clientbank_id and
			ddate >= '$mfrom_date' and
			ddate <= '$mto_date'";
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id' ";
	}
	$q .= "order by account.branch_id, clientbank ";

	$qr = @pg_query($q) or message(pg_errormessage().$q);
	
	$ctr = 0;
	$mclientbank = $mbranch_id = '';
	$total_amount = $sub_amount = 0;
	while ($r = @pg_fetch_object($qr))
	{
		if ($mclientbank != $r->clientbank || $mbranch_id != $r->branch_id)
		{
			if ($mclientbank != '')
			{
				$details .= "\n";
				$details .= space(100)."-----------------\n";
				$details .= space(90)."Sub Total     ".adjustRight(number_format($sub_amount,2),12)."\n\n";
				$lc += 4;
			}	
			
			if ($mbranch_id != $r->branch_id)
			{
				$details .= strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id))."\n";
				$lc++;			
			}
			if ($mclientbank != $r->clientbank)
			{
				$details .= strtoupper($r->clientbank)."\n";
				$lc++;
			}	
			$ctr=0;
			$sub_amount = 0;
			$mclientbank = $r->clientbank;
			$mbranch_id = $r->branch_id;
		}

		$ctr++;
		$details .= adjustRight($ctr,4).' '.
					adjustSize($r->account,35).' '.
					adjustSize($r->clientbank,25).' '.
					adjustSize($r->bank_account,15).' '.
					adjustSize($r->bank_cardno,20).' '.
					adjustRight(number_format($r->withdrawn,2),12).'  '.
					($r->mconfirm ? '[X]' : '[ ]')."\n";
					
		$lc++;			
		$sub_amount += $r->withdrawn;			
		$total_amount += $r->withdrawn;			
		
		if ($lc > 55 && $p1 == 'Print Draft')
		{
				$details1 .= $details;
				$details .= "<eject>";
				doPrint($header.$details);
				$header .= "\n\n\n";
				$header .= "---- ----------------------------------- ------------------------- --------------- -------------------- ------------- ---\n";
				$header .= "      Name                                Bank                      Account No      Card No.                Amount   \n";
				$header .= "---- ----------------------------------- ------------------------- --------------- -------------------- ------------- ---\n";
				$details = '';
				$lc=6;
		}
	}
	$details .= "\n";
	$details .= space(100)."-----------------\n";
	$details .= space(90)."Sub Total     ".adjustRight(number_format($sub_amount,2),12)."\n";
	$lc += 4;	

	$details .= space(100)."-----------------\n";
	$details .= space(88)."GRAND TOTAL     ".adjustRight(number_format($sub_amount,2),12)."\n";
	$details .= space(100)."=================\n";
	$lc += 5;	

	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
	
}
if ($date == '') $date=date('m/d/Y');	
?>	
<form name="f1" method="post" action="">
  <div align="center"> 
    <table width="70%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="95%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>ATM 
          Periodic Withdrawal </b></font></td>
        <td width="3%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="1%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="91" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td width="589" colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"> 
                </font> </td>
            </tr>
            <!--            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">As 
                of</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="date" type="text" id="date2"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.date, 'mm/dd/yyyy')"> 
                </font></td>
            </tr>
-->
            <tr> 
              <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">To</font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="to_date" type="text" id="to_date" value="<?= ($to_date);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
                </font></td>
            </tr>
            <tr> 
              <td height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td valign="top"> <select name = "branch_id">
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
                </select> </td>
              <td align="center" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr align="left" bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/bluelist.gif" width="16" height="17"></strong></font> 
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong></font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="textarea" cols="90" rows="18" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
