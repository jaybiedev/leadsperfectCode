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
if (!chkRights2('releasing','mview',$ADMIN['admin_id']) and !chkRights2('financereport','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Loan Releasing ]...");
	exit;
}

$from_date = $_REQUEST['from_date'];
$to_date = $_REQUEST['to_date'];
if ($from_date == '')
{
 	$from_date = date('m/d/Y');
	$to_date =  date('m/d/Y');

}
if ($p1=='Go' || $p1=='Print Draft')
{
	if ($filterby == 'date')
	{
		$mfrom_date = mdy2ymd($from_date);
		$mto_date = mdy2ymd($to_date);
	}
	else
	{
		$from_date = $from_date*1;
		$to_date = $to_date*1;
	}
	$qu = "update releasing set account_group_id = account.account_group_id 
	              from account 
				  where account.account_id = releasing.account_id and (releasing.account_group_id=0 or releasing.account_group_id IS NULL)";
	$qr = pg_query($qu) or message(pg_errormessage());
				  
	$q = "select 
			*
		from 
			account,
			releasing,
			account_group,
			account_class
		where 
			account.account_id=releasing.account_id and
			account_group.account_group_id=releasing.account_group_id and
			account_class.account_class_id=account_group.account_class_id";
			
	if ($filterby == 'date')
	{
			$q .= " and date>='$mfrom_date' and  date<='$mto_date' ";
	}
	else
	{
			if ($from_date != '')
			{
				$q .= " and releasing_id>='$from_date' ";
			}
			if ($to_date != '')
			{
				$q .= " and  releasing_id<='$to_date' ";
			}
	}
	if ($account_class_id!='')
	{
		$q .= " and account_class.account_class_id='$account_class_id'";
	}		
	if ($branch_id != '')
	{
		$q .= " and branch_id='$branch_id'";
	}
	
	if ($show == 'T')
	{
		$q .= " and status != 'U' ";
	}
	elseif ($show == 'U')
	{
		$q .= " and status= 'U'";
	}
	$q .= "	order by date, account";
	
	$qr = pg_query($q) or message(pg_errormessage());
	if ($p1 == 'Print Draft')
		$header = "<small3>";
	else
		$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
		if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
		else $hdr = $SYSCONF['BUSINESS_NAME'];
		$header = '';	
		
	$header .= center(rtrim($hdr),136)."\n";
	$header .= center('SUMMARY OF LOAN RELEASES',136)."\n";
	$header .= center($from_date.' To '.$to_date,136)."\n";
	$header .= center('Printed '.date('m/d/Y g:ia'),136)."\n\n";
	$header .= "---- -------- -------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";
	$header .= "      DATE     ACCOUNT              GROUP           PRINCIPAL    SERVICE    CFEE/INS   AdvChg/OTH   PREVLOAN       OBLIG       RELEASED \n";
	$header .= "---- -------- -------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";
	$details = $details1 = '';
	$total_amount = $total_principal = $total_service_charge = $total_adv_interest = $total_insurance = 0;
	$total_collection_fee = $total_ocharge = $total_previous_balance = $total_gross = $total_released = 0;
	$total_advance_change = $total_printout = $total_atm_charge = $total_other_charges= $total_photo = 0;
	$total_referral_fee = $total_ca_balance = $total_insurance = 0;
	$total_deduction = $total_interest = $total_redeem = 0;
	$total_prevprin = $total_prevint = 0; 
	$lc=6;
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		$astatus = lookUpTableReturnValue('x','account','account_id','account_status',$r->account_id);
		if ($astatus=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;
	
		$ctr++;
		$lc++;
		if ($p1 == 'Print Draft'  && rtype=='D') $details .= "<bold>";
		$details.= adjustRight($ctr,3).'. '.
				adjustSize(udate($r->date),8).' '.
				adjustSize($r->account,20).' ';
		if ($r->account_group_id != 0)
		{
			$details .= adjustSize(substr(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id),0,15),15).' ';
		}
		else
		{
			$details .= space(16);
		}
		
		$ocharge = $r->vat + $r->advance_payment + $r->advance_change + $r->photo + $r->printout + $r->atm_charge + $r->other_charges;
		if ($r->status == 'C')
		{
			$details .= "*** CANCELLED ****\n";
		}
		else
		{
			$details .=	adjustRight(number_format2($r->principal,2),12).' '.
					adjustRight(number_format2($r->service_charge,2),10).' '.
					adjustRight(number_format2($r->collection_fee,2),11).' '.
					adjustRight(number_format2($ocharge,2),10).' '.
					adjustRight(number_format2($r->previous_balance,2),12).' '.
					adjustRight(number_format2($r->gross,2),12).' '.
					adjustRight(number_format2($r->released,2),12)."\n";

			$total_principal += $r->principal;
			$total_advance_change += $r->advance_change;
			$total_printout += $r->printout;
			$total_photo += $r->photo;
			$total_atm_charge  += $r->atm_charge;
			$total_service_charge += $r->service_charge;
			$total_adv_interest += $r->advance_payment;
			$total_adv_intcomp  += $r->advance_payment * ($r->interest / $r->gross);
			$total_collection_fee += $r->collection_fee;
			$total_insurance += $r->principal*($SYSCONF['INSURANCEFEE']/100);
			$total_referral_fee += $r->referral_fee;
			$total_ca_balance += $r->ca_balance;
			$total_interest += $r->interest;
			$total_ocharge += $ocharge;
			$total_other_charges += $r->other_charges;
			$total_previous_balance += $r->previous_balance;
			$total_redeem += $r->redeem;
			$total_gross += $r->gross;
			$total_released += $r->released;
			$total_deduction += $r->service_charge + $r->collection_fee + $r->advance_change + 
						$r->ca_advance + $r->redeem + $r->printout + $r->photo + 
						$r->referral_fee + $r->previous_balance + $r->vat + 
						$r->other_charges + $r->atm_charge + $r->advance_payment;

			$intapp = $prnapp = $monthint = $balterm = 0;
			if ($r->interest !=0 and $r->term !=0) $monthint = $r->interest/$r->term;
			if ($r->previous_balance !=0 and $r->ammort != 0) $balterm  = $r->previous_balance/$r->ammort;
			$intapp = $balterm * $monthint;
			$prnapp = $r->previous_balance - $intapp;
			$total_prevprin += $prnapp;
			$total_prevint += $intapp;
				
		}

					
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			
			doPrint($header.$details);
			$lc=6;
			$details = '';
		}			
	}
		if ($lc>50 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			
			doPrint($header.$details);
			$lc=6;
			$details = '';
		}			

	$details .= "---------- ----------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";
	
	
	$details .= space(40).adjustSize('TOTAL :',10).' '.
					adjustRight(number_format($total_principal,2),12).' '.
					adjustRight(number_format($total_service_charge,2),10).' '.
					adjustRight(number_format($total_collection_fee,2),11).' '.
					adjustRight(number_format($total_ocharge,2),10).' '.
					adjustRight($total_previous_balance,12).' '.
					adjustRight($total_gross,12).' '.
					adjustRight($total_released,12)."\n";
	$details .= "---------- ----------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";

		if ($lc>50 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			
			doPrint($header.$details);
			$lc=6;
			$details = '';
		}			

/*
	$details .= space(20).'Service Charge : '.adjustRight(number_format($total_service_charge,2),14).space(10).'Total Obligation : '.adjustRight(number_format($total_gross,2),14).space(10).'Total Released  : '.adjustRight(number_format($total_released,2),14)."\n";
	$details .= space(20).'Adv. Interest  : '.adjustRight(number_format($total_adv_interest,2),14).space(10)  .'Total Deduction  : '.adjustRight(number_format($total_deduction,2),14)."\n";
	$details .= space(20).'CollFee        : '.adjustRight(number_format($total_collection_fee,2),14).space(10).'Total Principal  : '.adjustRight(number_format($total_principal,2),14)."\n";
	$details .= space(20).'Insurance      : '.adjustRight(number_format($total_insurance,2),14)."\n";

	$details .= "---------- ----------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";
	$details .=	space(37).adjustRight(number_format($total_service_charge+$total_adv_interest+$total_collection_fee +$total_insurance ,2),14).' '.space(28).
		adjustRight(number_format($total_deduction+$total_gross+$total_principal,2),14).' '.space(27).
		adjustRight(number_format($total_deduction+$total_gross+$total_principal+$total_service_charge+$total_collection_fee+$total_insurance+$total_adv_interest,2),14)."\n";
*/
	$details .= space(20).'Total Obligation : '.adjustRight(number_format($total_gross,2),14).space(10).
					'Total Deduction : '.adjustRight(number_format($total_deduction,2),14).space(10)."\n";
	$details .= space(20).'Total Principal  : '.adjustRight(number_format($total_principal,2),14).space(10).
					'Total Released  : '.adjustRight(number_format($total_released,2),14)."\n";
	$details .= "---------- ----------------------- --------------- ------------ ---------- ----------- ---------- ------------ ------------ ------------\n";

	$total_collection_fee -= $total_insurance;
//	$total_service_charge -= $total_insurance;
	
	$details .= adjustSize("Breakdown: ",12).adjustSize("Total Advance Payment",25).adjustRight(number_format($total_adv_interest,2),12).space(5). 	
					adjustSize("Total Collection Fee",25).adjustRight(number_format($total_collection_fee,2),12).space(5).
					adjustSize("Total Other Charges",25).adjustRight(number_format($total_other_charges,2),12).space(5)."\n";

	$details .= space(12).adjustSize("Advance Change",25).adjustRight(number_format($total_advance_change,2),12).space(5). 	
					adjustSize("Total Service Charge",25).adjustRight(number_format($total_service_charge,2),12).space(5). 	
					adjustSize("ATM Charge",25).adjustRight(number_format($total_atm_charge,2),12).space(5)."\n";

	$details .= space(12).adjustSize("Cash Advance",25).adjustRight(number_format($total_ca_balance,2),12).space(5).	
					adjustSize("Total Insurance Fee",25).adjustRight(number_format($total_insurance,2),12).space(5).
					adjustSize("Photo Charge",25).adjustRight(number_format($total_photo,2),12)."\n"; 	
				
	$details .= space(12).adjustSize("Interest",25).adjustRight(number_format($total_interest,2),12).space(5). 	
					adjustSize("Referral Fee",25).adjustRight(number_format($total_referral_fee,2),12).space(5). 	
					adjustSize("Printout",25).adjustRight(number_format($total_printout,2),12)."\n";

	$details .= space(12).adjustSize("Gawad",25).adjustRight(number_format($total_redeem,2),12).space(5). 	
					adjustSize("Previous Loan",25).adjustRight(number_format($total_previous_balance,2),12).space(5). 	
					adjustSize(" ",25).adjustRight(number_format2(0,2),12)."\n\n";

	$details .= space(12).adjustSize("Total Advance Paymnt Principal",30).
				adjustRight(number_format($total_adv_interest-$total_adv_intcomp,2),12).
				space(14).adjustSize("  Prev. Principal",23).adjustRight(number_format($total_prevprin,2),10).space(5)."\n";

	$details .= space(12).adjustSize("Total Advance Paymnt Interest",30).adjustRight(number_format($total_adv_intcomp,2),12).
				space(14).adjustSize("  Prev. Interest ",23).adjustRight(number_format($total_prevint,2),10).space(5)."\n";

	//$details .= space(12).adjustSize("Photo Charge",25).adjustRight(number_format($total_photo,2),10).space(5). 	
	//				adjustSize("Printout",25).adjustRight(number_format($total_printout,2),10)."\n"; 	

	$details .= "\n\n";
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject><reset>";
		doPrint($header.$details);
	}	
}	
?>	
<form name="form1" id="form1" method="post" action="" style="margin:10px">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Loan 
          Releases</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="220" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                From </font></td>
              <td colspan="2" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" >
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                </font> </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">To 
                </font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> By</font> 
                <?= lookUpAssoc('filterby',array('Date (mm/dd/yyyy)'=>'date','Control No.'=>'releasing_id'),$filterby);?>
                Show </font><font color="#000000"> 
                <?= lookUpAssoc('show',array('All'=>'A','Posted'=>'T','UnPosted'=>'U'),$show);?>
                </font></td>
            </tr>
            <tr> 
              <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Classification</font></td>
              <td width="375" valign="top"> 
                <?
		$q = "select * from account_class where enable order by account_class";
		$qr = pg_query($q) or message(pg_errormessage());
		echo "<select name='account_class_id'>";
		echo "<option value=''>All Accounts</option>";
		while ($r= pg_fetch_object($qr))
		{
			if ($account_class_id == $r->account_class_id)
			{
				echo "<option value=$r->account_class_id selected>$r->account_class</option>";
			}
			else
			{
				echo "<option value=$r->account_class_id>$r->account_class</option>";
			}

		}
		echo "</select>";
	   ?>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch 
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
                </font></td>
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
              <td height="24" colspan="3"><textarea name="print_area" cols="110" rows="22" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
