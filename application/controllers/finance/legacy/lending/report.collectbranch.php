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

if (!chkRights2('payment','mview',$ADMIN['admin_id']) and !chkRights2('financereport','mview',$ADMIN['admin_id']))
{
	message(" You have no permission in this Area [ Payment View Reports ]...");
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
				sum(payment_detail.amount) as payamount,
				sum(payment_detail.withdrawn) as withdrawn,
				sum(payment_detail.excess) as excess,
				sum(payment_detail.mischarge) as mischarge,
				sum(payment_detail.discount) as discount,
				branch.branch
			from 
				payment_detail, 
				payment_header,
				account,
				branch,
				account_group
			where 
				payment_header.payment_header_id = payment_detail.payment_header_id and 
				branch.branch_id=account.branch_id and 
				account.account_id=payment_detail.account_id and 
				account_group.account_group_id = account.account_group_id and
				payment_header.date>='$mfrom_date' and
				payment_header.date<='$mto_date'
				and payment_header.status = 'S'";
		if ($account_class_id != 0)
		{
			$q .= " and account_group.account_class_id = '$account_class_id'";
		}
		if ($province == '' and $branch_id!='')
		{
			$q .= " and account.branch_id = '$branch_id'";
		}
				
		$q .= " group by 
				branch	
			order by 
				branch";
	$qr = pg_query($q) or message(pg_errormessage());
//if ($ADMIN[admin_id]==1)
//	echo $q;
	if ($p1 == 'Print Draft')
	{
				doPrint('<small3>');
	}
	$page=1;
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$header .= center('SUMMARY OF COLLECTION PER BRANCH',120)."\n";
	$header .= center($from_date.' To '.$to_date,120)."\n\n";                           
	$header .= space(10)." #     Branch Name         Withdrawn       Ammort        Excess       Charges      Discount\n";
	$header .= space(10)."---- ------------------ ------------- ------------- ------------- ------------- -------------\n";
	$details = $details1 = '';
	$total_amount = $total_payamount = $total_payamount = $total_excess = $ctr = 0;
	$total_mischarge = $total_discount = 0;
	$lc=6;
	$mbranch_id = '';

	while ($r = @pg_fetch_object($qr))
	{
		$bid = lookUpTableReturnValue('x','branch','branch','branch_id',$r->branch);
		$partner = lookUpTableReturnValue('x','branch','branch','province',$r->branch);
		if ($ADMIN['branch_id'] > '0')
		{
			if ($ADMIN['branch_id'] != $bid and $ADMIN['branch_id'] != $bid and 
				$ADMIN['branch_id2'] != $bid and 
				$ADMIN['branch_id3'] != $bid and $ADMIN['branch_id4'] != $bid and
				$ADMIN['branch_id5'] != $bid and $ADMIN['branch_id6'] != $bid and 
				$ADMIN['branch_id7'] != $bid and $ADMIN['branch_id8'] != $bid and 
				$ADMIN['branch_id9'] != $bid and $ADMIN['branch_id10'] != $bid and
				$ADMIN['branch_id11'] != $bid and $ADMIN['branch_id12'] != $bid and 
				$ADMIN['branch_id13'] != $bid and $ADMIN['branch_id14'] != $bid and
				$ADMIN['branch_id15'] != $bid and $ADMIN['branch_id16'] != $bid and
				$ADMIN['branch_id17'] != $bid and $ADMIN['branch_id18'] != $bid and
				$ADMIN['branch_id19'] != $bid and $ADMIN['branch_id20'] != $bid) 
			continue;
		}
		if ($province != '' and $province != $partner) continue;

		$ctr++;
//		$excess = ($r->withdrawn - $r->payamount) ;
		$excess = ($r->withdrawn + $r->discount) - ($r->payamount + $r->mischarge) ;
		if ($excess < 0) $excess = 0;
		$total_amount += $r->payamount;
		$total_excess += $excess;
		$total_withdrawn += $r->withdrawn;
		$total_mischarge += $r->mischarge;
		$total_discount += $r->discount;
		$details .= space(10).adjustRight($ctr,3).'. '.
					adjustSize($r->branch,18).' '.
					adjustRight(number_format($r->withdrawn,2),13).' '.
					adjustRight(number_format($r->payamount,2),13).' '.
					adjustRight(number_format($excess,2),13).' '.
					adjustRight(number_format($r->mischarge,2),13).' '.
					adjustRight(number_format($r->discount,2),13).' '."\n";
//					adjustRight(number_format($r->excess+$r->payamount,2),13)."\n";
//		$branch_code = chop($r->branch_code);			
		$exportdata .= 'branch_code=>'.$r->branch_code.'||'.
								'date=>'.$r->ddate.'||'.
								'account_code=>'.$r->account_code.'||'.
								'withdrawn=>'.$r->withdrawn.'||'.
								'amount=>'.$r->amount.'||'.
								'excess=>'.$r->excess."\n";

	}	

	$details .= space(10)."---- ------------------ ------------- ------------- ------------- ------------- -------------\n";
	$details .= space(10).adjustSize('TOTAL AMOUNT ->',24).
				adjustRight(number_format($total_withdrawn,2),13)." ".
				adjustRight(number_format($total_amount,2),13)." ".
				adjustRight(number_format($total_excess,2),13)." ".
				adjustRight(number_format($total_mischarge,2),13)." ".
				adjustRight(number_format($total_discount,2),13)."\n";
//				adjustRight(number_format($total_amount+$total_excess,2),13)."\n";
	$details .= space(10)."=============================================================================================\n";
	
	
	$details1 .= $header.$details;
//echo $details1;	
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
              <td width="76" height="24" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td width="505" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">To 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
              </font></td>
              <td width="222" valign="top"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><i>** Partners overides branch</i></font></td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td valign="top" nowrap><div align="left"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                 
                <select name = "branch_id">
                  <?
				$q = "select * from branch where enable";
				if ($ADMIN['branch_id'] > '0')
				{
					?>
				    <option value=''>Select Branch</option>
                  <?
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
                Classification
                <select name = "account_class_id">
                  <?
				$q = "select * from account_class where enable";
				?>
			    <option value=''>All Classification</option>
  			    <?
				$qrc = @pg_query($q);
				while ($rc = @pg_fetch_object($qrc))
				{
					if ($account_class_id == $rc->account_class_id)
					{
						echo "<option value=$rc->account_class_id selected>$rc->account_class</option>";
					}
					else
					{	
						echo "<option value=$rc->account_class_id>$rc->account_class</option>";
					}	
				}
				
			?>
                </select>
                Partners
                <select name = "province">
                  <?
				$q = "select * from bankcard where enable";
				?>
                  <option value=''>All Partners</option>
                  <option value='0'>NONE</option>
                  <?
				$qrc = @pg_query($q);
				while ($rc = @pg_fetch_object($qrc))
				{
					if ($province == $rc->bankcard_id)
					{
						echo "<option value=$rc->bankcard_id selected>$rc->bankcard</option>";
					}
					else
					{	
						echo "<option value=$rc->bankcard_id>$rc->bankcard</option>";
					}	
				}
				
			?>
                </select>
                </font></div></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <input name="p1" type="submit" id="p1" value="Go" />
                <input name="p1" type="submit" id="p1" value="Print Draft" />
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
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
