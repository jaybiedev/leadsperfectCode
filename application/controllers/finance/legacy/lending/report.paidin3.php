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
	if ($account_group_id == '') message("Report for ALL account groups...");
	$date_to = mdy2ymd($_REQUEST['date_to']);

	$q = "select * 
			from 
				account,
				releasing
			where
				account.account_id=releasing.account_id and
				releasing.status!='C' ";
	$q .= "and date <='$date_to' ";
	if ($account_group_id != '')
	{
		$q .= " and releasing.account_group_id='$account_group_id'";
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
	$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
	if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	else $hdr = $SYSCONF['BUSINESS_NAME'];
	$header .= center($hdr,130)."\n";
	$header .= center('FULLY PAID IN 5 MONTHS'.($account_group_id == ''?' - ALL ACCOUNT GROUPS':''),130)."\n";

	$header .= center('Report Date As of '.ymd2mdy($date_to),130)."\n\n";

	if ($p1 == 'Print Draft') $header .= "<bold>";
	$header .= space(5)."Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id).' '.$branch.
				space(35)."Printed: ".date('M. d Y g:ia')." ".$ADMIN['username']."\n";
	if ($p1 == 'Print Draft') $header .= "</bold>";
//	$header .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------- ------------ -------------\n";
//	$header .= "  #    NAME OF ACCOUNT                     RELEASED   DUE DATE  Term  INT. BALANCE   PRIN. BAL.     AMOUNT DUE   AMMORT     TOTAL DUE \n";
//	$header .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------- ------------ -------------\n";
	$header .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------ -------------\n";
	$header .= "  #    NAME OF ACCOUNT                     RELEASED   DUE DATE  Term   BALANCE       AMOUNT DUE       AMMORT     TOTAL DUE \n";
	$header .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------ -------------\n";

	$maccount_group_id='';
	$details = $details1 = '';
	$total_amount = $subtotal  = $total_balance = $total_amount_due = $total_ammort = $total_int = $total_prn = 0;
	$sub_due;
	$maccount_id = '';
	$lc=6;
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		if ($r->balance < 1) continue;

		$ldate = strtotime($r->date);
		if (substr($r->date,8,2) > $r->withdraw_day)
			$ds = date('Y-m-d',strtotime("+31 days",$ldate));		
		else $ds = $r->date;	
		
		$wday = ($r->withdraw_day>0?$r->withdraw_day:substr($r->date,8,2));
		$datestart =substr($ds,0,8).str_pad($wday,2,'0',STR_PAD_LEFT);
		$ldate = strtotime($datestart);
		$dd = $r->term*30;
		$dterm = '+'.$dd.' days';
		$ds = date('Y-m-d',strtotime($dterm,$ldate));
		$date1 =substr($ds,0,8).str_pad($wday,2,'0',STR_PAD_LEFT);

		$dd -= 90;
		$dterm = '+'.$dd.' days';
		$ds = date('Y-m-d',strtotime($dterm,$ldate));
		$date2 =substr($ds,0,8).str_pad($wday,2,'0',STR_PAD_LEFT);

//		if ($date1 < $date_to or $date2 > $date_to) continue;
		
		recalculate($r->releasing_id , 'noneform');
		$cutoff = ($r->ammort * 5);
		if ($r->balance > $cutoff) continue;
		
		if ($maccount_id != $r->account_id)
		{
			if ($maccount_id!='')
			{
//				if ($sub_due>0)
					$details .=	adjustRight(number_format($sub_due,2),13)."\n";
//				else
//					$details .= "\n";	
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
		$arr['withdraw_day'] = $r->withdraw_day;
		
		$aAd = amountDue($arr,$date_to);
		$amount_due = $aAd['amount_due'];
		
		$remaining_due = $aAd['remaining_due'];

		$balance = $r->gross;
		$ratio = $r->interest / $r->gross;
/*	if ($ADMIN[admin_id]==1 and $arr['account_id'] == '3446')
{
   echo 'balance '.$balance.'  ratio '.$ratio;
   exit;		
} */
// sum payments

		$q = "select date 
				from 
					ledger 
				where
					 status!='C' and type='C' and date <= '$date_to' and
					releasing_id ='".$arr['releasing_id']."'
				order by date desc";
		$qrd = @pg_query($q);
		$rd = @pg_fetch_object($qrd);
		$pendate = $rd->date;

		$q = "select debit, remarks, date
				from 
					ledger 
				where
					 status!='C' and type='P' and 
					releasing_id ='".$arr['releasing_id']."'
				order by date";
		$qrp = @pg_query($q);
		$penalty = 0;
		
		while ($rp = @pg_fetch_object($qrp))
		{
			$d1 = explode('/',$rp->remarks);
			$d2 = explode('-',$rp->date);
			if ($d1[1] < $d2[0] or ($d1[1] == $d2[0] and $d1[0]<=$d2[1]))
			$penalty += $rp->debit;
		}	
		$penq= $q;
//if ($arr['releasing_id']=='7144') echo $q;
		
//if ($penalty > 0) echo ' test point '.$penalty.'  ';

		$q = "select sum(credit) as credit
				from 
					ledger 
				where
					 status!='C' and (type='C' or type='D') and date <= '$date_to' and
					releasing_id ='".$arr['releasing_id']."'";
		$qrc = @pg_query($q);
		$rc = @pg_fetch_object($qrc);

	
		$payment = $rc->credit - $penalty;
			
//if ($arr['releasing_id']=='7144') echo $rc->credit.'-'.$penalty.'   :   '.$payment."<br>".$penq;	
//			if ($payment < 0 ) $payment = 0;
//			$balance -= $payment;
//			$penalty = 0;
		if ($payment < 0 ) $payment = 0;
		else
		{
			$balance -= $payment;
			$penalty = 0;
		}			
		
//if ($arr['account_id'] == '20924')
//  echo ' balance '.$balance.'  payment '.$payment;		

		$details.=  adjustRight($cc,5).' '.
					adjustSize($r->account,35).' '.
					adjustSize(ymd2mdy($r->date),10).' '.
					adjustSize(ymd2mdy($date1),10).' '.
					adjustRight($remaining_due,4).' '.
					adjustRight(number_format($r->balance,2),13).' '.
//					adjustRight(number_format($balance*$ratio,2),13).' '.
//					adjustRight(number_format($balance - ($balance*$ratio),2),13).' '.
					adjustRight(number_format($amount_due,2),13).' '.
					adjustRight(number_format($ammorts,2),12);
					
		$total_amount += $amount_due;
		$total_balance += $r->balance;
		$total_int += $balance*$ratio;
		$total_prn += $balance - ($balance*$ratio);
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

	$details .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------ -------------\n";
	$details .= space(51).adjustSize('TOTAL AMOUNT ->',15).'   '.
				adjustRight(number_format($total_balance,2),13)." ".
				adjustRight(number_format($total_amount_due,2),13)." ".
				adjustRight(number_format($total_ammort,2),12)." ".
				adjustRight(number_format($total_amount,2),12)."\n";
	$details .= "----- ----------------------------------- ---------- ---------- ---- ------------- ------------- ------------ -------------\n";
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
        
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Loan Fully Paid in 5 months based on balance </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="130" height="24" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td width="338" valign="top" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
              </font></td>
            <td width="108" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
            <td width="497" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
            <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cutoff Date</font></td>
            <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date_to" type="text" id="date_to" value="<?= ymd2mdy($date_to);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_to, 'mm/dd/yyyy')"></font></td>
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
              <input name="p1" type="submit" id="p13" value="Go">
              <input name="p1" type="submit" id="p1" value="Print Draft">
              </font></td>
          </tr>
          <tr bgcolor="#A4B9DB"> 
            <td height="24" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
              Preview</strong> &nbsp; </font></td>
          </tr>
          <tr align="left"> 
            <td height="24" colspan="4"><textarea name="textarea" cols="140" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
