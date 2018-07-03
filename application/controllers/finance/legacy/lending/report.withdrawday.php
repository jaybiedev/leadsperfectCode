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

if ($p1=='Bank Export')
{
	$bankdte = date('md');
	$bankdate= date('mdy');
	$batch   = date('Ymd').str_pad($batchno,2,'0',STR_PAD_LEFT);
	$export  = adjustSize('H',20);															// 	1
	$export .= adjustSize($bankdate,6);														//	2
	$export .= adjustSize($instcode,10);													//	3
	$export .= adjustSize($batch,10);														//	4

	$q = "select *
		from 
			account, clientbank
		where 
			clientbank.clientbank_id=account.clientbank_id and
			account.enable and 
			date_atm_out IS Null";

	if ($withdraw_day != '')
	{
		$q .= " and account.current_day='$withdraw_day'";
	}		
	if ($clientbank_id != '')
	{
		$q .= " and account.clientbank_id='$clientbank_id' ";
	}
	elseif ($clientbank != '')
	{
		$q .= " and clientbank ilike '%$clientbank%'";
	}
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id' ";
	}
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id' ";
	}
	if ($collection_type_id != '')
	{
		$q .= " and account.collection_type_id='$collection_type_id' ";
	}
	$q .= " order by account ";
	$qr = @pg_query($q) or message(pg_errormessage());

	while ($r = @pg_fetch_object($qr))
	{
		$export  .= chr(13).adjustSize('D',20);
		$accountno = str_replace('-','',$r->bank_account);											// 	1
		$export .= str_replace(' ','0',adjustRight($accountno,12));									//	2
		$name 	= explode('/',$r->account);
		$export .= adjustSize($name[0],30);															//	3
		$export .= adjustSize($r->account_code,20);													//	4
		$export .= str_replace(' ','0',adjustRight(number_format($r->salary,2,'.',''),15))." ";		//	5
	}
	$details1 = $export;
	$zfile = '../banks/'.$instcode.$bankdte.'DR'.'.txt';
	if (!$handle = fopen($zfile, 'w+')) 
	{
	         message("Cannot open file ($zfile)...");
	}
	else
	{
		if (fwrite($handle, $export) === FALSE) 
		{
			message("Cannot write to file ($zfile)");
		}
	}
}
if ($p1=='Go' || $p1=='Print Draft' || $p1=='Print')
{

/*	$q = "select *
		from 
			account
		where
			enable and
			date_atm_out IS Null";
*/			

	$q = "select *
		from 
			account, clientbank, branch
		where 
			clientbank.clientbank_id=account.clientbank_id and
			account.enable and account.branch_id = branch.branch_id and
			date_atm_out IS Null";

	if ($withdraw_day != '')
	{
		$q .= " and account.current_day='$withdraw_day'";
	}		
	if ($clientbank_id != '')
	{
		$q .= " and account.clientbank_id='$clientbank_id' ";
	}
	elseif ($clientbank != '')
	{
		$q .= " and clientbank ilike '%$clientbank%'";
	}
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id' ";
	}
	if ($province_id != '')
	{
		$q .= " and branch.province='$province_id' ";
	}
	if ($branch_id != '')
	{
		$q .= " and branch.branch_id='$branch_id' ";
	}
	if ($ADMIN[branch_id]!=0 and $branch_id=='')
	{
		$q .= " and (branch.branch_id ='".$ADMIN['branch_id']."'";
		if ($ADMIN['branch_id2'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id2']."'";
		if ($ADMIN['branch_id3'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id3']."'";
		if ($ADMIN['branch_id4'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id4']."'";
		if ($ADMIN['branch_id5'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id5']."'";
		if ($ADMIN['branch_id6'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id6']."'";
		if ($ADMIN['branch_id7'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id7']."'";
		if ($ADMIN['branch_id8'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id8']."'";
		if ($ADMIN['branch_id9'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id9']."'";
		if ($ADMIN['branch_id10'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id10']."'";
		if ($ADMIN['branch_id11'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id11']."'";
		if ($ADMIN['branch_id12'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id12']."'";
		if ($ADMIN['branch_id13'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id13']."'";
		if ($ADMIN['branch_id14'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id14']."'";
		if ($ADMIN['branch_id15'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id15']."'";
		if ($ADMIN['branch_id16'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id16']."'";
		if ($ADMIN['branch_id17'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id17']."'";
		if ($ADMIN['branch_id18'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id18']."'";
		if ($ADMIN['branch_id19'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id19']."'";
		if ($ADMIN['branch_id20'] > '0') $q .= " or branch.branch_id ='".$ADMIN['branch_id20']."'";
		$q .= ") ";
	}
	if ($collection_type_id != '')
	{
		$q .= " and account.collection_type_id='$collection_type_id' ";
		$collection_type = lookUpTableReturnValue('x','collection_type','collection_type_id','collection_type',$collection_type_id);
	}
	else
	{
		$collection_type = 'ATM/PASSBOOK ';
	}
	
	if ($show == 'S')
	{
		$q .= " order by account ";
	}
	elseif ($show == 'B')
	{
		$q .= " order by branch, clientbank, current_day, account_group_id, account ";
	}
	else
	{
		$q .= " order by clientbank,  account ";
	}	

	$qr = @pg_query($q) or message(pg_errormessage());
	if ($p1=='Print Draft') doPrint("<small3>");
//	$header = "\n\n";
	$lh=0;
	$header .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$header .= center($collection_type.' WITHDRAW SCHEDULE DAY: '.$withdraw_day,120)."\n";
	$header .= center('Printed '.date('m/d/Y g:ia'),120)."\n\n";
	$lh += 4;
	if ($branch_id !='')
	{
		$header .= center(lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id),120)."\n";
		$lh += 1;
	}
	else
	{
		if ($ADMIN[branch_id]==0)
			$header .= center('ALL BRANCHES',120)."\n";
		else
			$header .= center('ALL ASSIGNED BRANCHES',120)."\n";
		$lh += 1;
	}
	if ($account_group_id !='')
	{
		$header .= center(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id),120)."\n";
		$lh += 1;
	}
	else
	{
		$header .= center('ALL ACCOUNT GROUPS DATE WITHDRAWN : ______________________',120)."\n";
		$lh+=1;
	}
	if ($ADMIN['usergroup']=='A' and $showacct=='Y')
	{
		if ($p1=='Print Draft') doPrint("<small3>");
		$header .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
		$header .= "     Name of Account                 Bank            Account No.     Pension    Ammort     Account Group             Amount\n";
		$header .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
	} else
	{
		$header .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
		$header .= "     Name of Account                 Bank             Pension    Ammort     Account Group             Amount\n";
		$header .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
	}	
	$lh+=3;

	$details = $details2 = '';
	$details1 = "";
	//$details1 = $header;

	$lc = $lh;
	$total_amount = $total_month1 = $total_month2 = $total_month3= $total_month4= $total_month5 = $total_due=0;
	$ctr=0;
	$total_salary = 0;
	while ($r = @pg_fetch_object($qr))
	{
		//updateReleasing($r->releasing_id);
		$nextgap=0;
		if ($mbranch_id!=$r->branch_id && $show=='B' && $branch_id =='')
		{
			if ($lc > 58)
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
				$lc = $lh;
				$details2 .= $header.$details."\n\n";
				$details = '';
			}		
			if ($mbranch_id != '')
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->branch_id != '')
			{
				$details .= adjustSize($r->branch,30)."\n";
				$lc++;
			}
			else
			{
				$details .= "NO BRANCH\n";
				$lc++;
			}	
			$details .= str_repeat('-',20)."\n";
			$mbranch_id = $r->branch_id;
			$lc++;
			$nextgap=1;
		}
		if ($mclientbank!=$r->clientbank && ($show=='G' or $show=='B')  && $clientbank_id =='')
		{
			if ($show=='B') $padtxt='     ';
			else $padtxt = '';
			if ($lc > 58)
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
				$details2 .= $header.$details."\n\n";
				$lc = $lh;
				$details = '';
			}		
			if ($nextgap == 0)
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->clientbank != '')
			{
				$details .= $padtxt.adjustSize($r->clientbank,40)."\n";
				$lc++;
				//$details .= adjustSize(strtoupper(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id)),30)."\n";
			}
			else
			{
				$details .= "NO BANK\n";
				$lc++;
			}	
			$details .= $padtxt.str_repeat('-',20)."\n";
			$mclientbank = $r->clientbank;
			$lc++;
			$nextgap=1;
		}
		if ($mcurrent_day!=$r->current_day && $show=='B' && $withdraw_day=='')
		{
			if ($lc > 58)
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
				$details2 .= $header.$details."\n\n";
				$lc = $lh;
				$details = '';
			}		
			if ($nextgap == 0)
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->current_day != '')
			{
				$details .= '        Withdrawal Date : '.number_format($r->current_day,0)."\n";
				$lc++;
			}
			else
			{
				$details .= "        NO WITHDRAWAL DATE\n";
				$lc++;
			}	
			$details .= '        '.str_repeat('-',20)."\n";
			$mcurrent_day = $r->current_day;
			$lc++;
			$nextgap=1;
		}
		if ($maccount_group_id!=$r->account_group_id && $show=='B' && $account_group_id=='')  // account_group
		{
			if ($lc > 58)
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
				$details2 .= $header.$details."\n\n";
				$lc = $lh;
				$details = '';
			}		
			if ($nextgap == 0)
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->account_group_id != '')
			{
				$account_group = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);
				$details .= '            Account Group : '.$account_group."\n";
				$lc++;
			}
			else
			{
				$details .= "            NO ACCOUNT GROUP\n";
				$lc++;
			}	
			$details .=     '            '.str_repeat('-',40)."\n";
			$maccount_group_id = $r->account_group_id;
			$lc++;
		}
		
		$q = "select sum(ammort) as ammort from releasing where balance>'0' and status!='C' and account_id = '$r->account_id'";

		$qqr = @pg_query($q) or message(pg_errormessage());
		$rr = @pg_fetch_object($qqr);
		$ammort = $rr->ammort;

		$ctr++;	
		$details .= adjustRight($ctr,3).'. '.adjustSize($r->account,30).' '.
					adjustSize(substr($r->clientbank,0,15),15).' ';

		if ($ADMIN['usergroup']=='A' and $showacct=='Y')
		{
			$details .= adjustSize($r->bank_account,15).' ';
		}
		$details .=	adjustRight(number_format($r->salary,2),10).' '.
					adjustRight(number_format($ammort,2),10).' ';
					
		$total_salary += $r->salary;
		if ($r->account_group_id != '')
		{
			if ($include_pin == 'Y')
			{
				$details .= adjustSize($r->bank_pin,6).' ';
			}
			else
			{
				$details .= space(7);
			}
			$details .= adjustSize(strtoupper(substr(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id),0,25)),18).
						"____________\n";
			$lc++;
		}
		else
		{
			if ($include_pin == 'Y')
			{
				$details .= adjustSize($r->bank_pin,6).space(19)."__________ ____________\n";
				$lc++;
			}
			else
			{
				$details .= space(25)."__________ ____________\n";
				$lc++;
			}
		}			

		if ($lc > 58)
		{
			if ($p1=='Print Draft')
			{
				doPrint($header.$details."<eject>");
			}
			$details2 .= $header.$details."\n\n";
			$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
			$lc = $lh;
			$details = '';
		}		
	}
	if ($ADMIN['usergroup']=='A' and $showacct=='Y')
	{
		$details .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
		$lc++;
	} else
	{
		$details .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
		$lc++;
	}		
	$details .= space(8).adjustSize('TOTAL',58).
					adjustRight(number_format($total_salary,2),12).'  ';
	$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	
	$details2 .= $header.$details."\n\n";
	$details = '';

	//-- not widthdrawn
	if ($withdraw_day != ''   && $unwithdrawn == 'Y')
	{
		$q = "select *
			from 
				account, clientbank
			where 
				clientbank.clientbank_id=account.clientbank_id and
				account.enable and 
				date_atm_out IS Null";
	
		$q .= " and account.withdraw_day<'$current_day' and  account.current_day>0";
		if ($clientbank_id != '')
		{
			$q .= " and account.clientbank_id='$clientbank_id' ";
		}
		elseif ($clientbank != '')
		{
			$q .= " and clientbank ilike '%$clientbank%'";
		}
		if ($account_group_id != '')
		{
			$q .= " and account.account_group_id='$account_group_id' ";
		}
		if ($ADMIN[branch_id]!=0 and $branch_id=='')
		{
			$q .= " and (branch.branch_id ='".$ADMIN['branch_id']."'";
			if ($ADMIN['branch_id2'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id2']."'";
			if ($ADMIN['branch_id3'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id3']."'";
			if ($ADMIN['branch_id4'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id4']."'";
			if ($ADMIN['branch_id5'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id5']."'";
			if ($ADMIN['branch_id6'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id6']."'";
			if ($ADMIN['branch_id7'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id7']."'";
			if ($ADMIN['branch_id8'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id8']."'";
			if ($ADMIN['branch_id9'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id9']."'";
			if ($ADMIN['branch_id10'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id10']."'";
			if ($ADMIN['branch_id11'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id11']."'";
			if ($ADMIN['branch_id12'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id12']."'";
			if ($ADMIN['branch_id13'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id13']."'";
			if ($ADMIN['branch_id14'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id14']."'";
			if ($ADMIN['branch_id15'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id15']."'";
			if ($ADMIN['branch_id16'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id16']."'";
			if ($ADMIN['branch_id17'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id17']."'";
			if ($ADMIN['branch_id18'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id18']."'";
			if ($ADMIN['branch_id19'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id19']."'";
			if ($ADMIN['branch_id20'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id20']."'";
			$q .= ") ";
		}
		if ($branch_id != '')
		{
			$q .= " and account.branch_id='$branch_id' ";
		}
		if ($collection_type_id != '')
		{
			$q .= " and account.collection_type_id='$collection_type_id' ";
			$collection_type = lookUpTableReturnValue('x','collection_type','collection_type_id','collection_type',$collection_type_id);
		}
		else
		{
			$collection_type = 'ATM/PASSBOOK ';
		}
		
		if ($show == 'S')
		{
			$q .= " order by account ";
		}
		else
		{
			$q .= " order by account.clientbank_id,  account ";
		}	
		$qr = @pg_query($q) or message(pg_errormessage());

		//-- not withdrawn printing
		$header = "\n\n**** ACCOUNTS NOT WITHDRAWN ****\n";
		$lc += 3;
		if ($ADMIN['usergroup']=='A' and $showacct=='Y')
		{		
			$header .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
			$header .= "     Name of Account                 Bank            Account No.     Pension    Ammort     Account Group             Amount\n";
			$header .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
		} else
		{		
			$header .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
			$header .= "     Name of Account                 Bank             Pension    Ammort     Account Group             Amount\n";
			$header .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
		}			
		$details = '';
		//$details1 = $header;
		$lh = 6;
		$lc = 13;
		$total_amount = $total_month1 = $total_month2 = $total_month3= $total_month4= $total_month5 = $total_due=0;
		$ctr=0;
		$total_salary = 0;
		$yearmonth = date('Y-m');
		while ($r = @pg_fetch_object($qr))
		{
			$q = "select * from payment_header, payment_detail
						where
							payment_header.payment_header_id = payment_detail.payment_header_id and
							payment_header.status!='C' and
							payment_detail.account_id = '$r->account_id' and
							substr(payment_header.date,1,7) = '$yearmonth'";
							
			$qqr = @pg_query($q) or message(pg_errormessage());
			if (@pg_num_rows($qqr) > 0) continue;
			//updateReleasing($r->releasing_id);
			if ($mclientbank_id!=$r->clientbank_id && $show=='G')
			{
				if ($lc > 58)
				{
					if ($p1 == 'Print Draft')
					{
						doPrint($header.$details."<eject>");
					}
					$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
					$details2 .= $header.$details."\n\n";
					$lc = 6;
					$details = '';
				}		
				if ($mclientbank_id != '')
				{
					$details .= "\n";
					$lc++;
				}
				if ($r->clientbank_id != '')
				{
					$details .= adjustSize($r->clientbank.', '.$r->clientbank_address,60)."\n";
					$lc++;
					//$details .= adjustSize(strtoupper(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id)),30)."\n";
				}
				else
				{
					$details .= "NO BANK\n";
					$lc++;
				}	
				$details .= str_repeat('-',30)."\n";
				$lc++;
				$mclientbank_id = $r->clientbank_id;
			}
			
			$q = "select sum(ammort) as ammort from releasing where balance>'0' and status!='C' and account_id = '$r->account_id'";
	
			$qqr = @pg_query($q) or message(pg_errormessage());
			$rr = @pg_fetch_object($qqr);
			$ammort = $rr->ammort;
	
			$ctr++;	
			$details .= adjustRight($ctr,3).'. '.adjustSize($r->account,30).' '.
						adjustSize(substr($r->clientbank,0,15),15).' ';
			if ($ADMIN['usergroup']=='A' and $showacct=='Y')
			{		
				$details .= adjustSize($r->bank_account,15).' ';
			} 	
			$details .=	adjustRight(number_format($r->salary,2),10).' '.
						adjustRight(number_format($ammort,2),10).' ';
						
			$total_salary += $r->salary;
			if ($r->account_group_id != '')
			{
				if ($include_pin == 'Y')
				{
					$details .= adjustSize($r->bank_pin,6).' ';
				}
				else
				{
					$details .= space(7);
				}
				$details .= adjustSize(strtoupper(substr(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id),0,25)),18).
							"____________\n";
				$lc++;
			}
			else
			{
				if ($include_pin == 'Y')
				{
					$details .= adjustSize($r->bank_pin,6).space(19)."__________ ____________\n";
					$lc++;
				}
				else
				{
					$details .= space(25)."__________ ____________\n";
					$lc++;
				}
			}			
			if ($lc > 58)
			{
				if ($p1=='Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= "<p style='page-break-after:always;'>".$header.$details."</p>";
				$details2 .= $header.$details."\n\n";
				$lc = 6;
				$details = '';
			}		
		}
		if ($ADMIN['usergroup']=='A' and $showacct=='Y')
		{		
			$details .= "---- ------------------------------ --------------- --------------- ---------- ---------- ------------------------ ------------\n";
			$lc++;
		} else
		{
			$details .= "---- ------------------------------ --------------- ---------- ---------- ------------------------ ------------\n";
			$lc++;
		}	
		$details .= space(8).adjustSize('TOTAL',58).
						adjustRight(number_format($total_salary,2),12).'  ';
		$details1 .= "<p style='page-break-after:always;'>".$header.$details;
		$details2 .= $header.$details;
		if ($p1=='Print Draft')
		{
			doPrint($header.$details);
		}	
	}	
	
	$q = "select * from cache where type='WDAY_REPORT'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	if ($p1 == 'Print' or $p1 == 'Print Draft')
	{
		$reference = $r->value1 + 1;
		if ($r->value1 == '')
		{
			$q = "insert into cache (type, value1) values ('WDAY_REPORT' ,'1')";
		}
		else
		{
			$q = "update cache set value1 = '$reference'  where cache_id = '$r->cache_id'";
		}
		$qr = @pg_query($q) or message(pg_errormessage());
	}
	else
	{
		$reference = $r->value1;
	}
	$reference = str_pad($reference,8,'0',STR_PAD_LEFT);
	
	$details = "\n\n";
	$lc += 2;
	if ($ADMIN['usergroup']=='A' and $showacct=='Y')
	{		
		$details .= str_repeat('_',25).space(3).str_repeat('_',25).space(3).str_repeat('_',25).space(3).str_repeat('_',25).space(3).adjustSize('RefNo:'.$reference,15)."\n";
		$details .= adjustSize('Prepared By:',25).space(3).adjustSize('Collector:',25).space(3).adjustSize('Bookkeeper:',25).space(3).adjustSize('Received:',25)."\n\n";
		$lc += 3;
	}	
	else
	{
		$details .= str_repeat('_',25).space(3).str_repeat('_',25).space(3).str_repeat('_',25).space(3).str_repeat('_',25)."\n";
		$details .= adjustSize('Prepared By:',25).space(3).adjustSize('Collector:',25).space(3).adjustSize('Bookkeeper:',25).space(3).adjustSize('Received:',25)."\n".space(3).adjustSize('RefNo:'.$reference,15)."\n";
		$lc += 3;
	}	
	if ($lc > 58)
	{
		if ($p1=='Print Draft')
		{
			doPrint($header.$details."<eject>");
		}
		$details1 .= "</p>".$header.$details;
		$details2 .= $header.$details."\n\n";
		$lc = 6;
		$details = '';
	} else
	{		
		if ($p1=='Print Draft')
		{
			doPrint($details."<eject>");
		}	
		$details2 .= $details."\n\n";
		$details1 .= $details."</p>";
	}	
	$det2 = $details1;
	$details1 = "<font style='font-family:Andale Mono;line-height:125%;font-size:110%;'>".$det2."</font>";
}
if ($date == '') $date=date('m/d/Y');	
?>	
<form action="" method="post" name="f1" id="f1" style="margin:0">
    <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Schedule 
          of Withdrawal</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="104" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Client 
                Bank</font></td>
              <td colspan="2" valign="top"> <input name="clientbank" type="text" value="<?= $clientbank;?>" size="22"> 
                <select name="clientbank_id" id="clientbank_id">
                  <option value=''>All Banks</option>
                  <?
			  	$q = "select  clientbank,clientbank_id, clientbank_address
						from
							clientbank
						where
							enable
						order by
							clientbank";
				$qr = pg_query($q);
				while ($r = pg_fetch_object($qr))
				{
					if ($r->clientbank_id == $clientbank_id)
					{
						echo "<option value=$r->clientbank_id selected>$r->clientbank - $r->clientbank_address</option>";
					}
					else
					{
						echo "<option value=$r->clientbank_id>$r->clientbank - $r->clientbank_address</option>";
					}	
				}
			  ?>
                </select> <select name = "collection_type_id">
                  <option value=''>ATM/Passbook</option>
                  <?
				$q = "select * from collection_type where enable order by collection_type";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($collection_type_id == $r->collection_type_id)
					{
						echo "<option value=$r->collection_type_id selected>$r->collection_type</option>";
					}
					else
					{	
						echo "<option value=$r->collection_type_id>$r->collection_type</option>";
					}	
				}
				
			?>
                </select> </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdraw 
                Day</font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="withdraw_day" type="text" id="withdraw_day2"  value="<?=$withdraw_day;?>" size="2">
                Show 
                <?= lookUpAssoc('show',array('Group By Branch, Bank, day & account group'=>'B','Group By Bank Branch'=>'G','Summary'=>'S'),$show);?>
                PIN 
                <?= lookUpAssoc('include_pin',array('No'=>'N','Yes'=>'Y'),$include_pin);?>
                Not Withdrawn 
                <?= lookUpAssoc('unwithdrawn',array('No'=>'N','Yes'=>'Y'),$unwithdrawn);?>
                Include Account No. 
                <?= lookUpAssoc('showacct',array('No'=>'N','Yes'=>'Y'),$showacct);?>
              </font></td>
            </tr>
            <tr> 
              <td height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
              <td colspan="2" valign="top"> <select name = "account_group_id">
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
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp;Institution Code
                <input name="instcode" type="text" value="<?= $instcode;?>" size="12" maxlength="10" />
&nbsp;&nbsp;Batch #
<input name="batchno" type="text" value="<?= $batchno;?>" size="2" maxlength="2" />
                </font> </td>
            </tr>
            <tr> 
			<?
			if ((($ADMIN['usergroup'] == 'A' or $ADMIN['usergroup'] == 'X') and $ADMIN['branch_id'] == '0') or ($ADMIN['branch_id'] != '0')) 
			{
			?>
              <td height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Partner Group</font></td>	  
              <td width="382" valign="top">
			  <select name = "province_id">
			  <?
				if ($ADMIN['branch_id'] == '0')
				{
				    $q = "select * from bankcard where enable";
				}	
			  ?>
				  <option value=''>All Partners</option>
			  <?
				$q .= " order by bankcard";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($province_id == $r->bankcard_id)
					{
						echo "<option value=$r->bankcard_id selected>$r->bankcard</option>";
					}
					else
					{	
						echo "<option value=$r->bankcard_id>$r->bankcard</option>";
					}	
				}
			?>
              </select>
			 <?
			 }
			 else
			 {
			 ?>
			 	<td>&nbsp;</td>
                <td width="382" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">			 
			 <?
			 }
			 ?> 
			  Branch <select name = "branch_id">
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
				} 
				if ($ADMIN[branch_id]==0)
				{
				?>
				  <option value=''>All Branches</option>
				<?
				} else
				{
				?>
				  <option value=''>All Assigned Branches</option>
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
              <td width="278" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                <input name="p1" type="submit" id="p1" value="Bank Export" />
              </font></td>
            </tr>
            <tr background="../graphics/table0_horizontal.PNG"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Schedule 
                of Withdrawal</b><strong> Preview</strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                </font></td>
            </tr>
            <tr>
              <td height="24" colspan="3"><textarea name="print_scr" cols="125" rows="20" readonly="readonly" wrap="off"><?=$details2;?>
              </textarea><textarea name="print_area" style="display:none;" readonly="readonly" wrap="off"><?=$details1;?>
              </textarea></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
  <div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p1" type="button" id="p1" value="Print"  onclick="printIframe(print_area)">
  </div>
</form>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
