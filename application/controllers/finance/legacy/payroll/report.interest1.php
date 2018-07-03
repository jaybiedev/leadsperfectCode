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
				account.branch_id,
				account.account_code, 
				account_group.account_group, 
				account.account_id,
				branch.branch,
				branch.branch_code,
				clientbank.clientbank
			from 
				account,
				account_group,
				branch,
				clientbank
			where 
				account_group.account_group_id=account.account_group_id and 
				clientbank.clientbank_id = account.clientbank_id and 
				branch.branch_id=account.branch_id";  // and account_id = '4780'";
				
	if ($branch_id != '')
	{
		$q .= " and account.branch_id = '$branch_id'";
	}
	if ($account_class_id != '')
	{
		$q .= " and account_group.account_class_id = '$account_class_id'";
	}
	$q .= "	order by branch, clientbank, account";

	$qr = pg_query($q) or message(pg_errormessage());

	if ($p1 == 'Print Draft')
	{
				doPrint('<small3>');
	}
	$page=1;
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],136)."\n";
	$header .= center('INTEREST INCOME PER BRANCH',136)."\n";		//  Straight line
	$header .= center($from_date.' To '.$to_date.'   Page '.$page,136)."\n\n"; //  Total Interest   Withdrawn     Penalty     Applied     Interest
	$header .= " #         Account                               Loan Id  Term   Total Int.    Withdrawn    Penalty   Principal    Interest    AP Excess   Date\n";
	$header .= "------ ----------------------------------------  -------- ----  ------------  ----------- ---------  ----------- ------------ ---------- --------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$mbranch_id = '';

	$subtotal = 0;
	$ctr = 0;
	$mclientbank_id = '';
	$exportdata = '';
	$collect = $interest = $released = $interest = 0;			
	$sub_collect = $sub_interest = $sub_released = $sub_interest = $sub_ammort = $sub_apexcess = 0;			
	$tot_collect = $tot_interest = $tot_released = $tot_interest = $tot_ammort = $tot_apexcess = 0;			
	$interest = $tpenalty = 0;
	$collect = 0; 
	$account_old = 0;
	$tpaydate='';
	
	while ($r = @pg_fetch_object($qr))
	{
		$account_id = $r->account_id;
		$account = $r->account;
		$penalty = 0;
	
		$q = "select * from payment_header as ph, payment_detail as pd
						    where 
							ph.payment_header_id = pd.payment_header_id and
							ph.date >='$mfrom_date' and ph.date<='$mto_date' and
							pd.account_id = '$account_id' and ph.status='S'";
		$qrs = pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qrs) == 0) continue;
		
		if ($r->branch_id != $mbranch_id)
		{
			if ($mbranch_id != '')
			{
				$details .= space(20).adjustSize('SUB TOTAL ->',42).
					adjustRight(number_format($sub_totalint,2),14).' '.
					adjustRight(number_format($sub_withdrawn,2),12).' '.
					adjustRight(number_format($sub_penalty,2),9).' '.
					adjustRight(number_format($sub_applied,2),12).' '.
					adjustRight(number_format($sub_interest,2),12).' '.
					adjustRight(number_format($sub_apexcess,2),10).' '.
					"\n";
				$sub_totalint = $sub_interest = $sub_withdrawn = $sub_penalty = $sub_applied = 0;			
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
				if ($sub_collect+$sub_applied+$sub_withdrawn+$sub_interest+$sub_penalty > 0)
				{
					$details .= space(20).adjustSize('SUB TOTAL ->',42).
						adjustRight(number_format($sub_totalint,2),14).' '.
						adjustRight(number_format($sub_withdrawn,2),12).' '.
						adjustRight(number_format($sub_penalty,2),9).' '.
						adjustRight(number_format($sub_applied,2),12).' '.
						adjustRight(number_format($sub_interest,2),12).' '.
						adjustRight(number_format($sub_apexcess,2),10).
						"\n";
					$sub_totalint = $sub_interest = $sub_withdrawn = $sub_penalty = $sub_applied = $sub_apexcess = 0;			
				}
				
				$lc++;
				$details .= "\n";
				$lc++;
			}
			$mclientbank_id = $r->clientbank_id;
			$mclientbank = $r->clientbank;
			if ($mclientbank_id == '') $mclientbank_id = '0';
			$details .= "   BANK : ".$mclientbank."\n";
			$lc++; 
		}
		$q = "select * from payment_header as ph, payment_detail as pd
						    where 
							ph.payment_header_id = pd.payment_header_id and
							ph.date >='$mfrom_date' and ph.date<='$mto_date' and
							pd.account_id = '$account_id' and ph.status='S'";
		$qrs = pg_query($q) or message(pg_errormessage());
		$flag = $flag1 = $redeemcrd = 0;
		$tpaydate = '';
		$tpenalty = 0;		
		while ($rw = @pg_fetch_object($qrs))
		{
///  Checking ending and starting date range for penalty
			$pedate	 = $rw->date;     // ending date range
			$q = "select * from payment_header as ph, payment_detail as pd
							where 
								ph.payment_header_id = pd.payment_header_id and
								ph.date<'$mfrom_date' and
								pd.account_id = '$account_id' and ph.status='S'
							order by
								date desc
							limit 2";
			$qrp = pg_query($q) or message(pg_errormessage());
			if ($mfrom_date == $mto_date)
				$rrp = @pg_fetch_object($qrp);
			$rrp = @pg_fetch_object($qrp);
			if ($rrp->date < $pedate) $psdate = $rrp->date;
			else $psdate = $pedate;
//////////////////
			
			$pendraw = $rw->withdrawn;
			$phid    = $rw->payment_header_id;

			if( $flag1 == 0)
			{				
				$q = "select * from wexcess where date >='$mfrom_date' and date<='$mto_date' and 
					account_id='$account_id' and remarks='REDEEMCREDIT'";
				$qra = pg_query($q) or message(pg_errormessage());
				$apexcess = 0;
				
				while ($ra = @pg_fetch_object($qra))
				{	 
					$redeemcrd += $ra->gross_amount;
				}
				if ($apexcess > 0) $flag1 = 1;
			}
						
			$q = "select pd.withdrawn,
						 pd.excess,
						 ld.date,
						 rl.releasing_id,
						 rl.gross,
						 rl.interest,
						 rl.advance_change,
						 rl.term,
						 ld.credit,
						 ph.date as phdate,
						 pd.mischarge,
						 ld.type 
						 from ledger as ld, releasing as rl, payment_detail as pd, payment_header as ph
								where 
								(pd.account_id = '$account_id' and pd.payment_header_id=ld.reference) and
								ph.payment_header_id = ld.reference and ld.reference = '$phid' and
								rl.releasing_id = ld.releasing_id and type='C' and
								ph.date >='$mfrom_date' and ph.date<='$mto_date' and
								ld.account_id = '$account_id' and ld.status='S'";
			$qrt = pg_query($q) or message(pg_errormessage());
			$term = $released = $interest = $ammort = $withdrawn = $collect = $applied = $penalty =0;
			if (@pg_num_rows($qrt) == 0) $withdrawn = $pendraw;
	
			while ($rt = @pg_fetch_object($qrt))
			{
				$withdrawn = $rt->withdrawn;
				$collect   = $rt->credit;
				$rid 	   = $rt->releasing_id;
				$releasing_id = $rid;
				$totalint  = $rt->interest;
				$principal = $rt->gross;
				$term 	= $rt->term;
				$apexcess = $rt->excess; // - $rt->mischarge;
				$paydate = $rt->date;
				$phdate = $rt->phdate;
				$curpen = 0;

//////// Acquire penalty amounts
//			if ($flag == 0)
//			{

				$q = "select *
						from 
							ledger as ld 
						where 
								releasing_id = '$rid' and 
								date<='$paydate' and
								account_id = '$account_id' and status='S'
						order by date DESC, type";
						
				$qrp = pg_query($q) or message(pg_errormessage());
//if ($account_id=='19153')
//	echo $q.'  '.$mfrom_date."<br>";
				$mfdate = $mfrom_date;
				while ($rp = @pg_fetch_object($qrp))
				{
					if ($rp->credit == 0 and $rp->type=='C') continue;

					if ($rp->type=='C')
					{
						if ($pay > 0 and $rp->date < $mfdate)
						{
							break;
						}	
						else	
							$pay = $rp->credit;
							$mfdate = $rp->date;
					} 
					elseif ($rp->type=='P')
					{
						if ($pay > 0 and $pay > $rp->debit) 
						{
//							$penalty += $rp->debit;
							$curpen += $rp->debit;
							$pay -= $rp->debit;
						}
					}
				}
//			}				
				if ($applied < 0 ) $applied = 0;

				if ($paydate == $tpaydate)
				{
					if ($tpenalty < $curpen)
					{
						$curpen = $curpen-$tpenalty;
						$tpenalty = 0;
					}	
					else
						$curpen = 0;	
				}	
if ($ADMIN[admin_id]==1 and $account_id=='29410')
{
 echo ' paydate '.$paydate.' tpaydate '.$tpaydate.' curpen '.$curpen."<br>";				
}

				if ($curpen > $collect) 
				{
					$int = 0;
					$applied = 0;
					$curpen = $collect;
				} 
				else
				{
					$int 	   = ($collect-$curpen) * ($totalint / $principal);
					$applied  += ($collect-$curpen) - $int ;	
				}
					
				$interest += $int;
				$penalty = $curpen;
				$curpen = 0;
			}
			
/*				
				$q = "select * from ledger 
									where 
									releasing_id = '$rid' and type='P' and
									account_id = '$account_id' and status='S'";
				$qrp = pg_query($q) or message(pg_errormessage());
				$penal = $prepenal = 0;
				$pcutd = $psdate;
				while ($rp = @pg_fetch_object($qrp))
				{
					$ps = $rp->date;  //substr($rp->remarks,3,4).'-'.substr($rp->remarks,0,2).'-'.substr($psdate,8,2);
					$pe = $rp->date;  //substr($rp->remarks,3,4).'-'.substr($rp->remarks,0,2).'-'.substr($pedate,8,2);

					if ($ps < $psdate)
					{
						$prepenal += $rp->debit;
						$opcut = $pcutd;
						if ($ps < $pcutd)
						{
							$pcutd = $ps;
						}
					}	
if ($account_id == 20559)
{
//	$rems = 'false';		
//	if ($ps >= $psdate and $pe <= $pedate) $rems = 'true ';
//	echo "ps : ".$ps.' psdate : '.$psdate.'  pe :'.$pe.'  pedate :'.$pedate.'  debit : '.$rp->debit.'  '.$rems."<br>";
}					
					if ($ps >= $psdate and $pe <= $pedate)
						$penal += $rp->debit;
				} 

/// Add up payment collection during the penalty period prior to cutoff
				if ($pcutd < $psdate)
				{
					$q = "select * from ledger 
									where 
									releasing_id = '$rid' and type='C' and date >='$pcutd' and date < '$mfrom_date' and 
									account_id = '$account_id' and status='S'";
					$qrpp = pg_query($q) or message(pg_errormessage());
					$penpay = 0;
					while ($rpp = @pg_fetch_object($qrpp))
					{
						$penpay += $rpp->credit;
					}
					if ($prepenal > $penpay)
						$penal += $prepenal - $penpay;
//if ($account_id == 4382)echo $prepenal.'   '.$penpay.'  '.$pcutd.'  '.$q;
				}
				if ($penal > 0) $flag = 1;
			}	

////// end

				$rid = '';
	if ($account_id ==17315)		
	{
	//echo $q."<br>";
	//exit;
	}		
					
				$int 	   = ($collect-$penalty) * ($totalint / $principal);
				$applied  += ($collect-$penalty) - $int ;	
				if ($applied < 0 ) $applied = 0;
				if ($penalty > $collect) 
				{
					$int = 0;
					$penal = $collect;
				}
				$interest += $int;
//				$penalty  += $penal;
			}	
*/
			$apexcess += $redeemcrd;
			$redeemcrd = 0;
			if ($collect > 0 or $withdrawn > 0)
			{
				$ctr++;			
				if ($term == 0)
				{
					$totalint = 0;
				}
				if ($apexcess == 0 and ($applied+$interest+$penalty ==0)) $apexcess = $withdrawn;
				if ($releasing_id=='0' or $releasing_id=='')  $apexcess = $withdrawn;
//				$apexcess = $withdrawn - ($applied+$interest+$penalty);
				$details .= adjustRight($ctr,5).'. '.
							adjustSize($account,40).'  '.
							adjustSize(str_pad($releasing_id,8,'0',STR_PAD_LEFT),8).' '.
//							adjustSize($type,1).'  '.
							adjustRight(number_format($term,0),4).'  '.
							adjustRight(number_format($totalint,2),12).' '.
							adjustRight(number_format($withdrawn,2),12).' '.
							adjustRight(number_format($penalty,2),9).' '.
							adjustRight(number_format($applied,2),12).' '.
							adjustRight(number_format($interest,2),12).' '.
							adjustRight(number_format($apexcess,2),10).' '.
							adjustRight($phdate,8).
							"\n";
					$sub_totalint += $totalint;
					$tot_totalint += $totalint;
					$sub_withdrawn += $withdrawn;
					$tot_withdrawn += $withdrawn;
					$sub_penalty += $penalty;
					$tot_penalty += $penalty;
					$sub_applied += $applied;
					$tot_applied += $applied;
					$sub_interest += $interest;
					$sub_apexcess += $apexcess;
					$tot_interest += $interest;
					$tot_apexcess += $apexcess;
					$collect = $withdrawn = $apexcess =0;
			}
	
	//		$branch_code += chop($r->branch_code);			
	/*		$exportdata .= 'branch_code=>'.$r->branch_code.'||'.
									'date=>'.$r->ddate.'||'.
									'account_code=>'.$r->account_code.'||'.
									'withdrawn=>'.$r->withdrawn.'||'.
									'amount=>'.$r->amount.'||'.
									'excess=>'.$r->excess."\n";*/
	
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
				$header = center($SYSCONF['BUSINESS_NAME'],136)."\n";
				$header .= center('INTEREST INCOME PER BRANCH',136)."\n";
				$header .= center($from_date.' To '.$to_date.'   Page '.$page,136)."\n\n";
				$header .= " #         Account                               Loan Id  Term   Total Int.    Withdrawn    Penalty   Principal    Interest     AP Excess   Date\n";
				$header .= "------ ----------------------------------------  -------- ----  ------------  ----------- ---------  ----------- ------------ ---------- --------\n";
				$lc=6;
			}			
			if ($tpaydate == '') 
			{
				$tpaydate=$paydate;
				$tpenalty=$penalty;
			}	
		}
	}	

	$details .= space(20).adjustSize('SUB TOTAL ->',42).
		adjustRight(number_format($sub_totalint,2),14).' '.
		adjustRight(number_format($sub_withdrawn,2),12).' '.
		adjustRight(number_format($sub_penalty,2),9).' '.
		adjustRight(number_format($sub_applied,2),12).' '.
		adjustRight(number_format($sub_interest,2),12).' '.
		adjustRight(number_format($sub_apexcess,2),10).' '.
		"\n";
	$details .= "------ ----------------------------------------  -------- ----  ------------  ----------- ---------  ----------- ------------ ---------- --------\n";
	$details .= space(20).adjustSize('SUB TOTAL ->',42).
		adjustRight(number_format($tot_totalint,2),14).' '.
		adjustRight(number_format($tot_withdrawn,2),12).' '.
		adjustRight(number_format($tot_penalty,2),9).' '.
		adjustRight(number_format($tot_applied,2),12).' '.
		adjustRight(number_format($tot_interest,2),12).' '.
		adjustRight(number_format($tot_apexcess,2),10).' '.
		"\n";
	$details .= "------ ----------------------------------------  -------- ----  ------------  ----------- ---------  ----------- ------------ ---------- --------\n";
	
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
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <?=lookUpAssoc('show',array('Show All'=>'A','Dont Show Negative'=>'D'),$show);?>
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
               
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Classification</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
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
                </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pages 
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
              <td height="24" colspan="3"><textarea name="textarea" cols="130" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
