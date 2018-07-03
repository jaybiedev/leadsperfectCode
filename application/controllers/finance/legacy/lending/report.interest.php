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
	$header .= center('INTEREST INCOME PER BRANCH',136)."\n";
	$header .= center($from_date.' To '.$to_date.'   Page '.$page,136)."\n\n";
	$header .= " #         Account                               Loan Id     Term    Withdrawn     Applied       Released    EIRR%     Interest \n";
	$header .= "------ ----------------------------------------  -------- -  ----  ------------  -----------  -------------  -----  ------------\n";
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
	$sub_collect = $sub_interest = $sub_released = $sub_interest = $sub_ammort = 0;			
	$tot_collect = $tot_interest = $tot_released = $tot_interest = $tot_ammort = 0;			
	$interest = 0;
	$collect = 0; 
	$account_old = 0;
	
	while ($r = @pg_fetch_object($qr))
	{
		$account_id = $r->account_id;

		$q = "select * from payment_header as ph, payment_detail as pd
				 where 
				 	pd.payment_header_id = ph.payment_header_id and 
					ph.status ='S' and pd.account_id ='$account_id' and 
					ph.date >='$mfrom_date' and ph.date<='$mto_date' ";

/*		$q = "select * from payment_header as ph, ledger as ld 
				 where 
					ph.payment_header_id = ld.reference and
					ld.status ='S' and ld.type !='D' and ld.account_id ='$account_id' and 
					ph.date >='$mfrom_date' and ph.date<='$mto_date' ";*/

		$qrs = pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qrs) == 0) continue;
		
		$ctr++;
		$lc++;
		$account = $r->account;
		
		if ($r->branch_id != $mbranch_id)
		{
			if ($mbranch_id != '')
			{
				$details .= space(20).adjustSize('SUB TOTAL ->',47).
					adjustRight(number_format($sub_collect,2),12).' '.
					adjustRight(number_format($sub_ammort,2),12).'  '.
					adjustRight(number_format($sub_released,2),13).'         '.
					adjustRight(number_format($sub_interest,2),12)."\n";
				$sub_collect = $sub_interest = $sub_released = $sub_interest = $sub_ammort = 0;			
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
				if ($sub_collect+$sub_ammort+$sub_released+$sub_interest > 0)
				{
					$details .= space(20).adjustSize('SUB TOTAL ->',47).
						adjustRight(number_format($sub_collect,2),12).' '.
						adjustRight(number_format($sub_ammort,2),12).'  '.
						adjustRight(number_format($sub_released,2),13).'         '.
						adjustRight(number_format($sub_interest,2),12)."\n";
					$sub_collect = $sub_interest = $sub_released = $sub_interest = $sub_ammort = 0;			
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
							pd.amount = 0 and pd.account_id = '$account_id' and ph.status='S'";
		$qrt = pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qrs) != 0)
		{
			$rt = @pg_fetch_object($qrt);
			$term = $released = $interest = $ammort = $irr = 0;
			$collect = $rt->withdrawn;
			$releasing_id = $rt->releasing_id;
			
			if ($collect > 0)
			{	
				$details .= adjustRight($ctr,5).'. '.
							adjustSize($account,40).'  '.
							adjustSize(str_pad($releasing_id,8,'0',str_pad_left),8).' '.
							adjustSize($type,1).'  '.
							adjustRight(number_format($term,0),4).'  '.
							adjustRight(number_format($collect,2),12).'  '.
							adjustRight(number_format($ammort,2),11).'  '.
							adjustRight(number_format($released,2),13).' '.
							adjustRight(number_format($irr*100,2),6).'% '.
							adjustRight(number_format($interest,2),12)."\n";
					$sub_collect += $collect;
					$tot_collect += $collect;
			}
		}

		$q = "select releasing_id 	from  releasing where account_id = '$account_id' and status='S'";
		$qrd = pg_query($q) or message(pg_errormessage());

		while ($rd = @pg_fetch_object($qrd))
		{
			$releasing_id = $rd->releasing_id;

/*			$q = "select * from ledger where status ='S' and type !='D' and account_id ='$account_id' and 
									releasing_id = '$releasing_id' and date >='$mfrom_date' and date<='$mto_date'";
			$qrs = pg_query($q) or message(pg_errormessage());
			if (@pg_num_rows($qrs) == 0) continue; */
/*						rel.principal-(rel.service_charge+rel.collection_fee*.30+rel.insurance*.30+
							rel.atm_charge+rel.printout+rel.other_charges) as released,*/
			
			$q = "select 
						ledger.reference,
						ledger.type,
						ledger.debit,
						ledger.credit,
						ledger.releasing_id,
						rel.gross,
						rel.principal,
						rel.term,
						rel.mode,
						rel.interest,
						rel.date as reldate,
						rel.principal as released,
						rel.ammort,
						rel.rate,
						ph.date
					from 
						ledger,
						releasing as rel,
						payment_header as ph
					where 
						ph.payment_header_id = ledger.reference and
						rel.releasing_id = ledger.releasing_id and
						ledger.status='S' and 
						ledger.account_id='$account_id' and
						ledger.releasing_id = '$releasing_id'";
//						 and
//						ledger.date >='$mfrom_date' ledger.and date<='$mto_date'";
			$q .= "	order by
						ph.date";	
			$qdx = pg_query($q) or message(pg_errormessage());

			$flg = 1;
			$interest = $collect = $balance = $intcomp = $xterm = $pays = $intnow = $tcollect = $tammort = 0;
			
			while ($rx = @pg_fetch_object($qdx))
			{
				if ($rx->type=='D') 
				{
//					$balance = $rx->debit;
					continue;
				}
				if ($flg)
				{
					$irr    = number_format(($rx->rate/12)/100,4);
					$iro	= 0;
					$intcomp = 0;
					$cf=1;
					$reldate=$rx->reldate;
					$term = $rx->term;
					$ammort = $rx->ammort;
					$released = $rx->released;
					$balance = $released;
					$gross = $rx->principal + $rx->interest;
					$tinterest = $gross - $released;    //$rx->gross - $released;
					$flg=0;
					$cf0=$gross;
					while ($irr > $iro and $irr < 1)
					{
						$cf = 0;
						for ($i = 1; $i <= $term; $i++) 
						{
						   $cf += $ammort/pow((1+$irr),$i);
						}
//echo 'term  '.$term.'   cf0 '.$cf0.'  irr '.$irr.'   '.$cf.'  '.$released."<br>";						
						$cf -= $released;
						$cd = abs($cf);
						if ($cd == 0 or $cf0 < $cd)
						{
							$irr = $iro;
						} else 
						{
							$cf0 = $cd;
							$iro = $irr;
							if ($irr > 1)
							{
								$cf=-1;
								continue;
							}
							$irr += .0001;
							$cfo = $cf;
							$cf = 1;
						}
					}							
//echo 'cd '.$cd.'   cf0 '.$cf0.'  irr '.$irr."<br>";						
//exit;
				}  //$flg
				$xterm = $term - round((($gross - ($tcollect + $rx->credit))/$ammort),0);
				if ($pays  < $xterm)
				{
					while ($pays < $xterm)
					{
//							$intcomp += $balance * $irr;
						$intnow  += round($balance * $irr,2);
						$begbal  = $balance;
						$balance += round($balance * $irr,2);				
						$balance -= $ammort;
						$tammort += $ammort;
						$pays ++;
					}
				}
				
				if ($rx->type == 'C')
				{
					if ($rx->date >= $mfrom_date and $rx->date <= $mto_date)
					{
						$interest += $intnow;
						$collect   += $rx->credit;
						$type = $rx->type;
					}
//echo ' no of pays '.$pays.'  interest '.$intnow.'  begbal '.$begbal.'  end bal '.$balance."<br>";						
					$intcomp += $intnow;
					$tcollect += $rx->credit;
					$intnow = 0;
				}  // payment
				else
				{
					if ($rx->date >= $mfrom_date and $rx->date <= $mto_date)
					{
						$type = $rx->type;
						$intnow = $tinterest - $intcomp;
						$interest += $intnow;
						$collect   += $rx->credit;
						$irr = $intnow / $begbal;
//echo ' no of pays '.$pays.'total interest '.$tinterest.'  interest comp '.$intcomp.'  interest '.$interest."<br>";						
					} 
				} // restructure
			}	// same releasing
			if (($show == 'A' and $collect!=0) or ($show=='D' and $interest > 0))
			{
				$ammort = $collect;
				$q = "select sum(withdrawn) as withdrawn from payment_header as ph, payment_detail as pd
									where 
									ph.payment_header_id = pd.payment_header_id and
									ph.date >='$mfrom_date' and ph.date<='$mto_date' and
									pd.account_id = '$account_id' and ph.status='S'";
				$qrs = pg_query($q) or message(pg_errormessage());
				$rs = @pg_fetch_object($qrs);
				$collect = $rs->withdrawn;
								
				$details .= adjustRight($ctr,5).'. '.
							adjustSize($account,40).'  '.
							adjustSize(str_pad($releasing_id,8,'0',str_pad_left),8).' '.
							adjustSize($type,1).'  '.
							adjustRight(number_format($term,0),4).'  '.
							adjustRight(number_format($collect,2),12).'  '.
							adjustRight(number_format($ammort,2),11).'  '.
							adjustRight(number_format($released,2),13).' '.
							adjustRight(number_format($irr*100,2),6).'% '.
							adjustRight(number_format($interest,2),12)."\n";

				if ($account_old != $account_id)
				{
					$sub_collect += $collect;
					$tot_collect += $collect;
					$account_old = $account_id;
				}
				$sub_interest+= $interest;
				$sub_ammort  += $ammort;
				$sub_released+= $released;
				$tot_ammort  += $ammort;
				$tot_interest+= $interest;
				$tot_released+= $released;
			}
		}	// same account


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
			$header .= " #         Account                               Loan Id     Term    Withdrawn     Applied       Released   EIRR%     Interest \n";
			$header .= "------ ----------------------------------------  -------- -  ----  ------------  -----------  -------------  -----  ------------\n";
			$lc=6;
		}			
	}	

	$details .= space(20).adjustSize('SUB TOTAL ->',47).
		adjustRight(number_format($sub_collect,2),12).' '.
		adjustRight(number_format($sub_ammort,2),12).'  '.
		adjustRight(number_format($sub_released,2),13).'        '.
		adjustRight(number_format($sub_interest,2),13)."\n";
	$details .= "------ ----------------------------------------  -------- -  ----  ------------  -----------  -------------  -----  ------------\n";
	$details .= space(20).adjustSize('GRAND TOTAL ->',47).
		adjustRight(number_format($tot_collect,2),12).' '.
		adjustRight(number_format($tot_ammort,2),12).'  '.
		adjustRight(number_format($tot_released,2),13).'        '.
		adjustRight(number_format($tot_interest,2),13)."\n";
	$details .= "------ ----------------------------------------  -------- -  ----  ------------  -----------  -------------  -----  ------------\n";
	
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
