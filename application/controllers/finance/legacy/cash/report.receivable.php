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
	message("You have no permission in this Area [ Loan Releasing ]...");
	exit;
}




/*if (($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print') && $account_group_id=='')
{
	message('Specify Account and Click GO...');
}
*/
if ($date_to == '') $date_to = date('Y-m-d');
if ($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print')
{
echo 'remark '.$qqq;
exit;
	if ($account_group_id == '') message("Report for ALL account groups...");
	$date_to = mdy2ymd($_REQUEST['date_to']);
	
	$q = "select * 
			from 
				account,
				releasing
			where
				account.account_id=releasing.account_id and
				releasing.status!='C'";
				
	if ($account_group_id != '')
	{
		$q .= " and account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and branch_id='$branch_id'";
		$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	}
	else
	{
		$branch = '';
	}
	$q .= " order by account, releasing.date";
				

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
	$header .= center('RECEIVABLE LIST'.($account_group_id == ''?' - ALL ACCOUNT GROUPS':''),130)."\n";
	$header .= center('Report Date As of '.ymd2mdy($date_to),130)."\n\n";

	if ($p1 == 'Print Draft') $header .= "<bold>";
	$header .= space(5)."Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id).' '.$branch.
				space(35)."Printed: ".date('M. d Y g:ia')." ".$ADMIN['username']."\n";
	if ($p1 == 'Print Draft') $header .= "</bold>";
	$header .= space(4)."----- ----------------------------------- ---------- ---------- --------- -------------- ------------- ---------- -------------\n";
	$header .= space(4)."  #    NAME OF ACCOUNT                     RELEASED   LAST PAY    Term         BALANCE      AMOUNT DUE   AMMORT     TOTAL DUE \n";
	$header .= space(4)."----- ----------------------------------- ---------- ---------- --------- -------------- ------------- ---------- -------------\n";

	$maccount_group_id='';
	$details = $details1 = '';
	$total_amount = $subtotal  = $total_balance = $total_amount_due = $total_ammort = 0;
	$sub_due;
	$maccount_id = '';
	$lc=6;
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		//if condition for zero balance
		//updateReleasing($r->releasing_id);
		recalculate($r->releasing_id , 'noneform');
		$qq = "select * from releasing where releasing_id = '$r->releasing_id'";
		$qqr = @pg_query($qq) or message(pg_errormessage().$qq);
		$rr = @pg_fetch_object($qqr);
		
		if ($rr->balance <= 0) continue;
		
		if ($maccount_id != $r->account_id)
		{
			if ($maccount_id!='')
			{
				$details .=	adjustRight(number_format($sub_due,2),13)."\n";
			}
			$sub_due = 0;
			$ctr++;
			$cc=$ctr.'.';
		}
		elseif ($maccount_id == $r->account_id)
		{
			$details .= "\n";
			$cc='';
		}
		$maccount_id = $r->account_id;

		$lastpay = '';
		if ($r->advance_payment > 0) 
		{
			if (!in_array($r->advance_applied, array('','//','--','0000-00-00','00/00/0000')))
			{
				$lastpay = ymd2mdy($r->advance_applied);
			}
			else
			{
				$lastpay = ymd2mdy($r->date);
			}
		}
		$q = "select * from ledger where releasing_id='$r->releasing_id' and type='C'
					order by date desc offset 0 limit 1";
		$qqr = @pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qqr)>0)
		{
			$rrr = pg_fetch_object($qqr);
			$lastpay = ymd2mdy($rrr->date);
		}	

		$arr = null;
		$arr = array();
		$arr['releasing_id'] = $r->releasing_id;
		$arr['account_id'] = $r->account_id;
		$arr['balance'] = $r->balance;
		$arr['releasing_date']  = $r->date;
		$arr['ammort'] = $r->ammort;
		$ammorts = $r->ammort;
		$arr['term'] = $r->term;
		
		$aAd = amountDue($arr,$date_to);
		$amount_due = $aAd['amount_due'];

		$remaining_due = $aAd['remaining_due'];		

		$details.=  space(3).adjustRight($cc,6).' '.
					adjustSize($r->account,35).' '.
					adjustSize(ymd2mdy($r->date),10).' '.
					adjustSize($lastpay,10).' '.
					space(3).adjustRight($remaining_due,5).'   '.
					adjustRight(number_format($r->balance,2),13).' '.
					adjustRight(number_format($amount_due,2),13).' '.
					adjustRight(number_format($ammorts,2),10);
					
		$total_amount += $amount_due;
		$total_balance += $r->balance;
		$total_amount_due += $amount_due;
		$total_ammort += $ammorts;
		$sub_due += $amount_due;

		$lc++;			
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details .= "\n";
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);
			$details = '';
			$lc=6;
		}			
	}
	$details .=	adjustRight(number_format($sub_due,2),13)."\n";

	$details .= space(4)."----- ----------------------------------- ---------- ---------- --------- -------------- ------------- ---------- -------------\n";
	$details .= space(60).adjustSize('TOTAL AMOUNT ->',15).'  '.
				adjustRight(number_format($total_balance,2),14)." ".
				adjustRight(number_format($total_amount_due,2),14)." ".
				adjustRight(number_format($total_amount,2),13)." ".
				adjustRight(number_format($total_ammort,2),10)."\n";
	$details .= space(4)."----- ----------------------------------- ---------- ---------- --------- -------------- ------------- ---------- -------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
}	

?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Receivable 
        Listing </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="85" height="24" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="77" valign="top" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
              </font> </td>
            <td width="60" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
            <td width="595" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
              </font></td>
          </tr>
          <tr> 
            <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">As 
              of Date </font></td>
            <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date_to" type="text" id="date_to" value="<?= ymd2mdy($date_to);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_to, 'mm/dd/yyyy')"> 
              </font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <select name = "branch_id">
                <?
				$q = "select * from branch where enable ";
				if ($ADMIN['branch_id'] > '0')
				{
	                echo "<option value=''>Select Branch</option>";
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					$q .= ") ";
				} else
				{
					?>
	                  <option value=''>All Branches</option>
					<?
				}
				$q .= "order by branch";
				
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
			  <input name="qqq" type="hidden" value="<?='$q'?>;" />
              <input name="p1" type="submit" id="p13" value="Go">
              <input name="p1" type="submit" id="p1" value="Print Draft">
              </font></td>
          </tr>
          <tr bgcolor="#A4B9DB"> 
            <td height="24" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
              Preview</strong> &nbsp; </font></td>
          </tr>
          <tr align="left"> 
            <td height="24" colspan="4"><textarea name="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
          </tr>
        </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
	<div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
 <?
if ($p1 == 'Search')
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
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location='?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>&p1=Go'"> 
    <td width="8%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$ctr;?>.&nbsp;</font></td>
    <td width="92%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<a href="?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>&p1=Go">
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
