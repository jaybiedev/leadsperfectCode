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
if (!chkRights2('excessamount','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Excess/Change ]...");
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
			payment_detail.ddate,
			payment_detail.excess,
			payment_detail.withdrawn,
			payment_detail.amount,
			account.account,
			account.account_id,
			account.branch_id
		from 
			payment_header,
			payment_detail,
			account
		where 
			payment_header.payment_header_id = payment_detail.payment_header_id and 
			account.account_id=payment_detail.account_id and
			payment_header.date>='$mfrom_date' and 
			payment_header.date<='$mto_date' and 
			payment_header.status != 'C' ";
			
		if ($showall == 'N')
		{	
			$q .= " and payment_detail.excess > '0'";
		}
			
		if ($ADMIN['branch_id'] > '0')
		{
			$q .= " and account.branch_id ='$branch_id'";
		} 
		if ($branch_id != '' and $ADMIN['branch_id'] == '0')
		{
			$q .= " and account.branch_id = '$branch_id'";
		}
		if ($account_group_id != '')
		{
			$q .= " and account.account_group_id = '$account_group_id'";
		}

		$q .= " order by account.branch_id, account, date";
		//	payment_header.status!='C' 
	
	$qr = pg_query($q) or message(pg_errormessage());

	$header = "\n\n";
	$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
	if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	else $hdr = $SYSCONF['BUSINESS_NAME'];
	$header .= center($hdr,80)."\n";
	$header .= center('SUMMARY OF PENSION WITHDRAWAL EXCESS/CHANGE',80)."\n";
	$header .= center($from_date.' To '.$to_date,80)."\n\n";
//	$header .= "   Date         Rec.Id   Account                             Branch                   Refund   Advances   Interest   Gross    Net Amount\n";
//	$header .= "  -----------  -------- ---------------------------------- --------------------  ---------- ---------- ---------- ---------- ----------\n";
	$header .= "  WDate         Rec.Id   Account                        Branch            Withdrawn      Ammort       Excess   \n";
	$header .= "  -----------  -------- ----------------------------- -----------------  ------------ ------------ ------------\n";
	$details = $details1 = '';
	$total_amount = $total_psave = 0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	while ($r = pg_fetch_object($qr))	
	{

		$details.= '   '.adjustSize(ymd2mdy($r->ddate),10).'  '.
					adjustSize(str_pad($r->payment_header_id,8,'0',str_pad_left),8).'  '.
					adjustSize($r->account,28).' '.
					adjustSize(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id),15).' '.
					space(3).
					adjustRight(number_format($r->withdrawn,2),12).' '.
					adjustRight(number_format($r->amount,2),12).' '.
					adjustRight(number_format($r->excess,2),10).' '."\n";

		$lc++;
		$ctr++;
		$total_excess += $r->excess;
		$total_withdrawn += $r->withdrawn;
		$total_amount+= $r->amount;
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);

			$details = '';
			$lc=6;
		}			
	}

	$details .= "  -----------  -------- ----------------------------- -----------------  ------------ ------------ ------------\n";
	$details .= space(10).adjustSize($ctr.' Items ',38).adjustSize('TOTAL AMOUNT ->',25).
					adjustRight(number_format($total_withdrawn,2),12).' '.
					adjustRight(number_format($total_amount,2),12).' '.
					adjustRight(number_format($total_excess,2),10)."\n";
	$details .= "  -----------  -------- ----------------------------- -----------------  ------------ ------------ ------------\n";
	
	
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
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Summary 
          of Pension Excess/Change</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td width="724" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                To 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                </font> </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
                Branch 
                <select name = "branch_id">
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
                </select>
                <?= lookUpAssoc('showall', array('With Excess Only'=>'N', 'Show All'=>'Y'), $showall);?>
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="2"><textarea name="print_area" cols="130" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
