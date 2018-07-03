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

	$aClass = null;
	$aClass = array();
	
	$aRep = null;
	$aRep = array();

	$q = "select * from account_class where enable ";
	$qr = @pg_query($q) or message(pg_errormessage());
	while ($r = @pg_fetch_assoc($qr))
	{
		$aClass[] = $r;
	}
	
	$q = "select 
				sum(payment_detail.amount) as amount,
				sum(payment_detail.withdrawn) as withdrawn,
				sum(payment_detail.excess) as excess,
				mcheck,
				account_group.account_class_id
			from 
				payment_detail, 
				payment_header,
				account,
				account_group
			where 
				payment_header.payment_header_id = payment_detail.payment_header_id and 
				payment_header.status!='C' and
				account_group.account_group_id=account.account_group_id and 
				account.account_id=payment_detail.account_id and 
				payment_detail.ddate>='$mfrom_date' and
				payment_detail.ddate<='$mto_date' ";
						
	if ($branch_id != '')
	{
		$q .= " and account.branch_id = '$branch_id'";
	}
	$q .= "	group by  account_group.account_class_id,mcheck";

	$qr = @pg_query($q) or message(pg_errormessage());

	if ($p1 == 'Print Draft')
	{
				doPrint('<small3>');
	}
	$page=1;
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$header .= center('SUMMARY FOR ACCOUNTING ENTRY',120)."\n";
	$header .= center($from_date.' To '.$to_date.'   Page '.$page,120)."\n\n";
	$header .= space(5).adjustSize("    Particulars ",25).' ';
	
	$line  = space(5).str_repeat('-',25).' ';
	foreach ($aClass as $temp)
	{
		$header .= center($temp['account_class'],15).' ';
		$line  .= str_repeat('-',15).' ';
	}
	$header .= " TOTAL \n";
	$line .= str_repeat('-',15)."\n";
	
	$header .= $line;
	$aRep['COLLECTION'][0] = ''; 	//0
	$aRep['WITHDRAWN'][0] = '';  //1
	$aRep['COLLECT'][0] = '' ;  //2
	$aRep['EXCESS'][0] = '';  //3
	$aRep['REDEEM_OUT'][0] = '';  //4
	$aRep['TRANSFER_OUT'][0] = '';  //5
	$aRep['REDEEM_OUT_CHARGE'][0] = '';  //6

	while ($r = @pg_fetch_object($qr))
	{
		if ($r->mcheck == 'G')
		{
			$aRep['REDEEM_OUT'][$r->account_class_id] = $r->amount; 
			$aRep['REDEEM_OUT_CHARGE'][$r->account_class_id] += $r->excess; 
		}
		elseif ($r->mcheck == 'T')
		{
			$aRep['TRANSFER_OUT'][$r->account_class_id] = $r->amount; 
			$aRep['REDEEM_OUT_CHARGE'][$r->account_class_id] += $r->excess; 
		}
		else
		{
			$aRep['WITHDRAWN'][$r->account_class_id] = $r->withdrawn; 
			$aRep['COLLECT'][$r->account_class_id] = $r->amount; 
			$aRep['EXCESS'][$r->account_class_id] = $r->excess; 
		}
	}	
	
	$q = "select 
					releasing.date as releasing_date,
					releasing.releasing_id,
					releasing.ammort,
					releasing.term,
					releasing.balance,
					account.account_group_id
				 from 
				 	releasing,
					account
			 	where 
					account.account_id = releasing.account_id and
					releasing.status!='C' and
					releasing.balance>0";
					
	$qr = @pg_query($q) or message(pg_errormessage());

	$aRep['NONEX'][0]='';  //7
	$aRep['RECEIVABLE'][0]= 0; //8

	while ($r = @pg_fetch_assoc($qr))
	{
		$qq = "select * from account_group where account_group_id = '".$r['account_group_id']."'";
		$qqr = @pg_query($qq);
		if (@pg_num_rows($qqr) == 0)
		{
			echo "NO ACCOUNT CLASSIFICATION ".$r->account."\n";
			$account_class_id = 4;
		}
		else
		{
			$rr = @pg_fetch_assoc($qqr);
			$account_class_id = $rr['account_class_id'];
		}
		$aAd = amountDue($r, $mto_date);
		$aRep['RECEIVABLE'][$account_class_id] += $aAd['amount_due'];   //8
	}
	
	//4
	$aRep['NONE0'][0]='';   //9
	$aRep['NEW LOANS'][0]='';   //10
	
	$aRep['GROSSLOAN'][0]=''; 	//11
	$aRep['PRINCIPAL'][0]=''; 	//12
	$aRep['INTEREST'][0]=''; 	//13
	$aRep['RELEASED'][0]=''; 	//14
	$aRep['REDEEM'][0]=''; 	//15
	$aRep['PREVIOUS_LOAN'][0]=''; 	//16
	$aRep['ADVANCE_CHANGE'][0]=''; 	//17
	$aRep['SERVICE'][0]=''; 	//18
	$aRep['INSURANCE'][0]=''; 	//19
	$aRep['COLLECTION_FEE'][0]=''; 	//20
	$aRep['ATM_CHARGE'][0]=''; 	//21
	$aRep['PHOTO'][0]=''; 	//22
	$aRep['PRINTOUT'][0]=''; 	//23
	$aRep['REFERRAL_FEE'][0]=''; 	//24
	$aRep['OTHER_CHARGES'][0]=''; 	//25

	$q = "select 
						sum(principal) as principal,
						sum(advance_change)  as advance_change,
						sum(printout) as printout,
						sum(photo) as photo,
						sum(atm_charge) as atm_charge,
						sum(service_charge) as service_charge,
						sum(advance_payment) as advance_payment,
						sum(collection_fee -  insurance) as collection_fee,
						sum(insurance) as insurance,
						sum(referral_fee) as referral_fee,
						sum(ca_balance) as ca_balance,
						sum(interest) as interest,
						sum(other_charges) as other_charges,
						sum(previous_balance) as previous_balance,
						sum(redeem) as redeem,
						sum(gross) as gross,
						sum(released) as released,
						 account_group.account_class_id
					from 
						releasing ,
						account,
						account_group
					where 
						account.account_id=releasing.account_id and 
						account_group.account_group_id = account.account_group_id and 
						releasing.status!='C' and
						releasing.date>='$mfrom_date' and
						releasing.date<='$mto_date'
					group by
						account_class_id";
	$qr = @pg_query($q) or message(pg_errormessage());
	
	while ($r = @pg_fetch_object($qr))
	{
			$aRep['GROSSLOAN'][$r->account_class_id] = $r->gross; 	//11
			$aRep['PRINCIPAL'][$r->account_class_id] = $r->principal; 	//12
			$aRep['INTEREST'][$r->account_class_id] = $r->interest; 	//13
			$aRep['RELEASED'][$r->account_class_id] = $r->released; 	//14
			$aRep['REDEEM'][$r->account_class_id] = $r->redeem; 	//15
			$aRep['PREVIOUS_LOAN'][$r->account_class_id] = $r->previous_balance; 	//16
			$aRep['ADVANCE_CHANGE'][$r->account_class_id] = $r->advance_change; 	//17
			$aRep['SERVICE'][$r->account_class_id] = $r->service_charge; 	//18
			$aRep['INSURANCE'][$r->account_class_id] = $r->insurance; 	//19
			$aRep['COLLECTION_FEE'][$r->account_class_id] = $r->collection_fee; 	//20
			$aRep['ATM_CHARGE'][$r->account_class_id] = $r->atm_charge; 	//21
			$aRep['PHOTO'][$r->account_class_id] = $r->photo; 	//22
			$aRep['PRINTOUT'][$r->account_class_id] = $r->printout; 	//23
			$aRep['REFERRAL_FEE'][$r->account_class_id] = $r->referral_fee; 	//24
			$aRep['OTHER_CHARGES'][$r->account_class_id] = $r->other_charges; 	//25
	}
	

	$aRep['NONE2'][0]=''; //26
	$aRep['ADVANCE CHANGE'][0]=''; //27

	$aRep['GROSSCHANGE'][0]=''; 	//28
	$aRep['INTERESTCHANGE'][0]=''; 	//29
	$aRep['NETCHANGE'][0]=''; 	//30

	$q = "select 
						sum(gross_amount) as gross_amount,
						sum(refund_amount)  as refund_amount,
						sum(interest) as interest,
						sum(net_amount) as net_amount,
						account_group.account_class_id
					from 
						wexcess ,
						account,
						account_group
					where 
						account.account_id=wexcess.account_id and 
						account_group.account_group_id = account.account_group_id and 
						wexcess.status!='C' and
						wexcess.type='C' and 
						wexcess.date>='$mfrom_date' and
						wexcess.date<='$mto_date'
					group by
						account_class_id";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	
	while ($r = @pg_fetch_object($qr))
	{
			$aRep['GROSSCHANGE'][$r->account_class_id] = $r->gross_amount; 	//28
			$aRep['INTERESTCHANGE'][$r->account_class_id] = $r->interest; 	//29
			$aRep['NETCHANGE'][$r->account_class_id] = $r->net_amount; 	//30
	}


	$xfrom_date  = substr($mfrom_date,0,7); //substr($from_date,0,3).substr($from_date,6,4);
	$xto_date  = substr($mto_date,0,7); //substr($to_date,0,3).substr($to_date,6,4);
	
	$aRep['NONE4'][0]=''; //31
	$aRep['PENALTY'][0]=''; 	//32

	$q = "select 
						sum(debit) as penalty,
						account_group.account_class_id
					from 
						ledger ,
						account,
						account_group
					where 
						account.account_id=ledger.account_id and 
						account_group.account_group_id = account.account_group_id and 
						ledger.status!='C' and
						ledger.type='P' and 
						(substring(ledger.remarks,4,4)||'-'||substring(ledger.remarks,1,2))>='$xfrom_date' and
						(substring(ledger.remarks,4,4)||'-'||substring(ledger.remarks,1,2))<='$xto_date'
					group by
						account_class_id";
	$qr = @pg_query($q) or message(pg_errormessage().$q);

	while ($r = @pg_fetch_object($qr))
	{
			$aRep['PENALTY'][$r->account_class_id] = $r->penalty; 	//32
	}
	
	
	//-- reporting...
	$aPart[0] = "COLLECTION";
	$aPart[1] = "  Amount Withdrawn";
	$aPart[2] = "  Collection Applied ";
	$aPart[3] = "  Excess Amount Collected";
	$aPart[4] = "  Redeem/Gawad Outgoing";
	$aPart[5] = "  Transfer Account";
	$aPart[6] = "  Redeem/Transfer Charges";
	$aPart[7] = "";
	$aPart[8] = "ACCOUNTS RECEIVABLES";
	$aPart[9] = "";
	$aPart[10] = "NEW LOANS";
	$aPart[11] = "  Gross Amount";
	$aPart[12] = "  Principal";
	$aPart[13] = "  Interest";
	$aPart[14] = "  Released";
	$aPart[15] = "  Redeem/Gawad";
	$aPart[16] = "  Previous Loan";
	$aPart[17] = "  Advance Change(CR)";
	$aPart[18] = "  Service Charge";
	$aPart[19] = "  Insurance";
	$aPart[20] = "  Collection Fee";
	$aPart[21] = "  ATM Charge";
	$aPart[22] = "  Photo";
	$aPart[23] = "  Print-out";
	$aPart[24] = "  Referral Fee";
	$aPart[25] = "  Other Charges";
	$aPart[26] = "";
	$aPart[27] = "EXCESS/CHANGE";
	$aPart[28] = "  Excess/Change Released";
	$aPart[29] = "  Interest Income";
	$aPart[30] = "  Net Excess/Change";
	$aPart[31] = "";
	$aPart[32] = "Penalties";

	reset($aPart);
	reset($aRep);
	$c=0;
	foreach ($aRep as $temp)
	{
		$details .= space(5);
		$particulars = $aPart[$c];
		$details .= str_pad($particulars,25,'.').' ';
		
		$total=0;
		foreach ($aClass as $temp1)
		{
			$details .= adjustRight(number_format2($temp[$temp1['account_class_id']],2),14).'  ';
			$total += $temp[$temp1['account_class_id']];
		}
		$details .= adjustRight(number_format2($total,2),14)."\n";
		
		$c++;		
	}	
	$details .= $line;
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	

	if ($branch_id != '')
	{
		
		$aip = explode('.',$_SERVER['REMOTE_ADDR']);

		$reportfile= './reports/COLLECT-'.$branch_code.'-'.$aip[3].'.txt';
		$fo = fopen($reportfile,'w+');
		if (@!fwrite($fo, $exportdata))
		{
			 message("Unable to create report file...");
		}
	}
}	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Summary 
          for Accounting Entries </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="74" height="24" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td width="745" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">To 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                </font></td>
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
                </select> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
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
              <td height="24" colspan="2"><textarea name="print_area" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
