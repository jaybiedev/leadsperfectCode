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
if (!chkRights2('releasing','mview',$ADMIN['admin_id']))
{
	message("You have no permission to View Loan Releasing...");
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
			releasing.releasing_id,
			releasing.deposit,
			releasing.date,
			account.account,
			account.account_id,
			account.branch_id
		from 
			releasing, account
		where 
			account.account_id = releasing.account_id and deposit > 0 and
			date>='$mfrom_date' and date<='$mto_date' and releasing.status != 'C'";
	
	if ($branch_id != '')
	{
		$q .= " and branch_id = '$branch_id'";
	}
	$q .= " order by branch_id,account,date";

	$qr = @pg_query($q) or message(pg_errormessage());

	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('SUMMARY OF LOAN DEPOSIT',80)."\n";
	$header .= center($from_date.' To '.$to_date,80)."\n\n";
	$header .= " Date           Name of Account                          Releasing No       Amount \n";
	$header .= "---------- --------------------------------------------- ------------ -------------\n";
	$details = $details1 = '';
	$total_amount =0;  
	$subtotal  = 0;
	$lc=6;
	$obranch_id = 0;
	while ($r = pg_fetch_object($qr))
	{
		if ($obranch_id != $r->branch_id)
		{
			if ($obranch_id !=0)
			{
				$details .= space(40).adjustSize('SUB TOTAL',28).
							adjustRight(number_format($sub_amount,2),15)."\n";
				$sub_amount = 0;		
			}	 
			$details .= "\nBRANCH : ".lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id)."\n";
			$obranch_id = $r->branch_id;
		}
		$astatus = lookUpTableReturnValue('x','account','account_id','account_status',$r->account_id);
		if ($astatus=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;
		
		$details.= adjustSize(ymd2mdy($r->date),10).' '.
					adjustSize($r->account,45).'  '.
					adjustSize(str_pad($r->releasing_id,8,'0',STR_PAD_LEFT),8).'  '.
					adjustRight(number_format($r->deposit,2),15)."\n";
		$lc++;
		$total_amount += $r->deposit;
		$sub_amount += $r->deposit;

		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);

			$details = '';
			$lc=6;
		}			
	}

	if ($obranch_id !=0)
	{
		$details .= space(40).adjustSize('SUB TOTAL',28).
					adjustRight(number_format($sub_amount,2),15)."\n\n";
		$sub_amount = 0;		
	}	 
	$details .= "---------- --------------------------------------------- ------------ -------------\n";
	$details .= space(40).adjustSize('TOTAL AMOUNT ->',28).
				adjustRight(number_format($total_amount,2),15)."\n";
	$details .= "---------- --------------------------------------------- ------------ -------------\n";
	
	
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
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Loan Deposit Summary </b></font></td>
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
              <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td width="501" valign="top"><select name = "branch_id">
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
              </select></td>
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
