<style type="text/css">
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f1.action="?p=report.accountledger_oldledger&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=report.accountledger_oldledger&p1=Cancel"
		}
	}
	else if (ul.name=='Go')
	{
		document.f1.action="?p=report.accountledger_oldledger&p1="+ul.id;
	}	
	else
	{
		document.f1.action="?p=report.accountledger_oldledger&p1="+ul.id;
	}
}
</script>
<?
if (!chkRights2('accountledger','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aLedger'))
{
	session_register('aLedger');
	$aLedger = null;
	$aLedger = array();
}
if (!session_is_registered('aLedgerDetail'))
{
	session_register('aLedgerDetail');
	$aLedgerDetail = null;
	$aLedgerDetail = array();
}
$aLedger['remarks'] = $_REQUEST['remarks']; 
if ($p1 == 'Save Remarks' and $aLedger['account_id']!=0)
{
	$module = 'accountledger';
	$q = "select * from accountrems where account_id = '".$aLedger['account_id']."' and module='$module'";
	$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
	$r = pg_fetch_object($qr);

	if ($r->account_id == $aLedger['account_id'])
	{
		$q = "update accountrems set remark = '$remarks' where 
						account_id = '".$aLedger['account_id']."' and module='$module'";
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
	} else
	{
		$date = date('Y-m-d');
		$q = "insert into accountrems (account_id,module,remark,date) values 
									('".$aLedger[account_id]."','$module','$remarks','$date')";
		@pg_query($q) or message(pg_errormessage().$q);
	} 
}
if ($c_id!= '' && $aLedger['releasing_id'] == '' && $p1 == 'Selectreleasing')
{

	$aLedger=null;
	$aLedger=array();

	$module = 'accountledger';
	$q = "select * from accountrems where account_id = '$c_id' and module='$module'";
	$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
	$r = pg_fetch_object($qr);
	$remarks = $r->remark;

	$q = "select 
				account.account_id,
				account.account,
				account.account_group_id,				
				account.address,
				salary,
				clientbank_id,
				account.withdraw_day,
				account.wday,
				branch
		 from 
		 		account, branch
		where 
				branch.branch_id=account.branch_id and account.account_id='$c_id'";
	$r = fetch_assoc($q);
	$aLedger = $r;
	$aLedger[remarks] = $remarks;
	$aLedger['account_group']=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLedger['account_group_id']);
	if ($aLedger['account_id'] == 0) $aLedger['account_id'] = $c_id;

	$q = "select sum(gross) as gross, sum(ammort) as ammort
		 from releasing 
		 where account_id='".$aLedger['account_id']."' and balance>0";
		 
	$r = fetch_object($q);
	$aLedger['gross'] = $r->gross;
	$aLedger['ammort'] = $r->ammort;
	$p1='selectAccountId';
	
}	

if ($p1 == 'Print Draft' || $p1=='Print' || $p1 == 'selectAccountId')
{
	$clientbank='';
	if ($aLedger['clientbank_id'] > '0')
	{
		$clientbank = lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$aLedger['clientbank_id']);
	}
	$details = '';
	$details .= center('A C C O U N T   L E D G E R',80)."\n";
	$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$details .= center($SYSCONF['BUSINESS_ADDR'],80)."\n";
	$details .= center('Date Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$details .= 'Customer: '.adjustSize($aLedger['account'],45).'  '.' Pension: '. number_format($aLedger['salary'],2)."\n";
	$details .= 'Group   : '.adjustSize($aLedger['account_group'],20).' Bank:'.adjustSize($clientbank,25).' ';
	$details .= ' OWD: '.$aLedger['withdraw_day']."\n";
	$details .= 'Branch   : '.adjustSize($aLedger['branch'],30).space(20).'  LRWD: '.$aLedger['wday']."\n";
	
	if ($show_withdrawn == 'S')
	{
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
		$details .= '    Date       Reference       Debit      Withdrawn     Credit    Excess      Balance '."\n";
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	}
	else
	{
		$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
		$details .= '    Date       Reference      Debit        Credit       Balance   '."\n";
		$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
	}
	$q = "select 
				ledger.date,
				ledger.type,
				ledger.debit,
				ledger.credit,
				ledger.account_id,
				ledger.releasing_id,
				ledger.reference,
				ledger.remarks,
				ledger.ledger_id,
				ledger.admin_id,
				releasing.principal,
				releasing.ammort,
				releasing.gross,
				releasing.balance,
				releasing.term,
				releasing.withdraw_day as withday_loan,
				releasing.admin_id as admin_id_releasing			

			 from 
			 	ledger, 
				releasing
			 where 
			 	ledger.releasing_id=releasing.releasing_id and
			 	ledger.account_id='".$aLedger['account_id']."' and 
				ledger.status!='C'";

	if ($r_id != '')
	{
		$q .= " and releasing.releasing_id ='$r_id'";
	}
	elseif ($show == 'B' || $show == 'F')
	{
		$q .= " and releasing.balance > 1 ";
	}
	elseif ($show == 'P')
	{
		$q .= " and releasing.balance <= 1 ";
	}
	$q .= "	order by ledger.releasing_id,ledger.date,ledger.remarks";

//	$q .= "	order by ledger.releasing_id,substring(ledger.remarks,4,4), 
//				substring(ledger.remarks,1,2),ledger.date";
	$qr = @pg_query($q) or message("Error querying ledger...".pg_errormessage());
	$aledger = NULL;
	$aledger = array();
	$atemp = NULL;
	$atemp = array();
	while ($temp = @pg_fetch_assoc($qr))
	{
		$rid=str_pad($temp['releasing_id'],12,'0', STR_PAD_LEFT);
		if ($temp[type]=='D') $temp[sortype] = 'A';
		else $temp[sortype] = $temp[type];
//		$rid=$temp['releasing_id'];\
		if ($temp['type']=='P')
		{
			$tt=explode("/",$temp['remarks']);
			$d2 = $tt[0].'/01/'.$tt[1];
			if ($tt[0]==12) 
			{
				$tt[1]++;
				$tt[0]=1;
			} else $tt[0]++;	
			$dd = $tt[0].'/01/'.$tt[1];
			$d1 = strtotime(mdy2ymd($dd));
			$ds = date('Y-m-d',strtotime("-1 days",$d1));
			$relid = $temp[releasing_id];
			$qq = "select date from ledger where releasing_id = '$relid' and type='R'";
			$qqr = @pg_query($qq) or message(pg_errormessage());
			$qrr = @pg_fetch_object($qqr); 
			if ($ds > $qrr->date) 
			{
				$ds = mdy2ymd($d2);
//				echo $ds;
			}	
				
			$sort = $rid.$ds.$temp[sortype];
		} 
		elseif ($temp['type']=='C')
		{
			$colldate = '';
			$q = "select 	payment_header.admin_id,
								payment_header.date as colldate
							from 
								payment_header
							where 
								payment_header.payment_header_id='".$temp['reference']."' and
								payment_header.status!='C'";
			$qrp = @pg_query($q) or message(pg_errormessage());
			$rp = @pg_fetch_object($qrp);
			$colldate = $rp->colldate;
			$temp[colldate] = $colldate;
			$sort = $rid.$colldate.$temp[sortype];
			$sort = $rid.$temp['date'].$temp[sortype];
		} 
		else $sort = $rid.$temp['date'].$temp[sortype];
		$atemp[]=$sort;
		$aledger[] = $temp;
	}
	asort($atemp);
	reset($atemp);
	
	$ctr=0;
	$total_credit = $total_debit = 0;
	$sub_credit = $sub_debit = 0;
	$total_obligation = $total_ammort = 0;
	$mreleasing_id='';
	$multiple = $cc = $penalty = $tremaining_ammort_count = 0;
	
//	while ($temp = @pg_fetch_assoc($qr))
//	{
//print_r($atemp);

	while (list ($key, $val) = each ($atemp))
	{
			$temp=$aledger[$key];
			$withdrawn = $excess = 0;
			$reference = '';
			$date = ymd2mdy($temp['date']);
			if ($mreleasing_id != $temp['releasing_id'] && $mreleasing_id != '')
			{
				//-- UPDATE RELEASING TABLE BALANCE FIELD
				$sub_balance = round($sub_balance,2);
				$qur = "update releasing set balance = '$sub_balance' where releasing_id = '$mreleasing_id'";
				@pg_query($qur);

				if ($show_withdrawn == 'S')
				{
					$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
					$details .= space(29).
					adjustRight(number_format($sub_debit,2),12).' '.
					adjustRight(number_format($sub_withdrawn,2),10).' '.
					adjustRight(number_format($sub_credit,2),10).' '.
					adjustRight(number_format($sub_excess,2),10).' '.
					adjustRight(number_format($sub_balance,2),12)."\n\n";
				}
				else
				{
					$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
				
					$details .= space(29).
					adjustRight(number_format($sub_debit,2),12).' '.
					adjustRight(number_format($sub_credit,2),12).' '.
					adjustRight(number_format($sub_balance,2),12)."\n\n";
				}
				
				$lc++;
				$lc++;
				$lc++;

				
				if ($show == 'F') break;
				$multiple =1;
				$sub_credit = $sub_debit = $sub_balance = $sub_withdrawn = $sub_excess = 0;
			}
			$mreleasing_id = $temp['releasing_id'];
			$reference = '';
			if ($temp['type'] == 'D')
			{
				$q = "select username, loan_type_id, term, advance_applied, advance_payment, mode from releasing, admin where admin.admin_id=releasing.admin_id and releasing.releasing_id='".$temp['reference']."'";
				$r = fetch_object($q);
			
				$reference = strtoupper($r->username);

				$details .= "Loan Type : ".lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id).
						"   Mode :".($r->mode=='S' ? 'Semi-Monthly' : ($r->mode=='W' ? 'Weekly' : 'Monthly')).
						"   Terms :".$r->term.'   Voucher '.str_pad($temp['releasing_id'],8,'0',STR_PAD_LEFT)."\n";
				$details .= "Principal :".adjustRight(number_format($temp['principal'],2),12)." ".
						"   Ammortization : ".number_format($temp['ammort'],2).'     OWD: '.$aLedger['withdraw_day'].($temp['withday_loan']!=0?'     LRWD: '.$temp['withday_loan']:'')."\n";
//						($temp['withday_loan']!=0?$temp['withday_loan']:$aLedger['withdraw_day'])."\n";
				$lc++;
				
				if ($temp['balance']>0 )
				{
					if ($temp['balance']>$temp['ammort'])
					{
						$total_ammort += $temp['ammort'];
					}
					else
					{
						$total_ammort += $temp['balance'];
					}
				}
				$total_obligation += $temp['gross'];
				
				$cc = 0;
			}
			elseif ($temp['type'] == 'P')
			{
				//penalty
//				$tt=explode("/",$temp['remarks']);
//				$date = $tt[0].'/28/'.$tt[1];
				$date = $temp['remarks'];
				$reference = lookUpTableReturnValue('x','admin','admin_id','username',$temp['admin_id']);
				$penalty += $temp['debit'];
			}
			elseif ($temp['type'] == 'R' || $temp['remarks'] == 'RENEW')
			{
				$q = "select username,previous_balance from releasing, admin 
							where 
								admin.admin_id=releasing.admin_id and 
								releasing.releasing_id='".$temp['reference']."'";
				$r = fetch_object($q);
				$reference = strtoupper($r->username);
//if ($ADMIN[admin_id]==1)
//  echo 'credit '.$temp[credit].' bal '.$sub_balance.' prev '.$r->previous_balance."<br>";				
				if ($sub_balance != $r->previous_balance)
				{
					if ($sub_balance > $r->previous_balance)
						$temp[credit] = $r->previous_balance;
					else
						$temp[credit] = $sub_balance;
						
					$qur = "update ledger set credit = '".$temp[credit]."' where ledger_id = '".$temp[ledger_id]."'";
					@pg_query($qur);
				} 
				elseif ($sub_balance == $r->previous_balance and $temp[credit] != $sub_balance)
				{
						$temp[credit] = $sub_balance;
						
					$qur = "update ledger set credit = '".$temp[credit]."' where ledger_id = '".$temp[ledger_id]."'";
					@pg_query($qur);
				} 
				
			}
			elseif ($temp['type'] == 'C')
			{
				$colldate = '';
				$q = "select 	payment_header.admin_id,
									payment_header.date as colldate,
									payment_detail.withdrawn,
									payment_detail.excess,
									payment_detail.amount,
									payment_detail.payment_detail_id,
									payment_detail.payment_header_id
								from 
									payment_header, 
									payment_detail
								where 
									payment_detail.payment_header_id = payment_header.payment_header_id and 
									payment_detail.account_id = '".$aLedger['account_id']."' and 
									payment_header.payment_header_id='".$temp['reference']."' and
									payment_header.status!='C'";
//									payment_detail.releasing_id = '".$temp['releasing_id']."' and

				$qrp = @pg_query($q) or message(pg_errormessage());

				if (@pg_num_rows($qrp) > 0)
				{
					$rp = @pg_fetch_object($qrp);

					$reference = strtoupper(lookUpTableReturnValue('x','admin','admin_id','username',$rp->admin_id));
					$withdrawn = $rp->withdrawn;
					$excess = $rp->excess;
					$colldate = ymd2mdy($rp->colldate);
//					$temp['credit'] = $rp->amount;

				}
			}
			$accountbalance += $temp['debit'] - $temp['credit'];
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'];

			$sub_credit += $temp['credit'];
			$sub_debit += $temp['debit'];
			$sub_withdrawn += $withdrawn;
			$sub_excess += $excess;
			if ($withdrawn < $temp['credit'])
			{
				$sub_balance += $temp['debit'] - $temp['credit'] - $excess;
			}
			else
			{
				$sub_balance += $temp['debit'] - $temp['credit'] ;
			}
			if ($temp['type'] == 'D' && $r->advance_applied != '')
			{

				$details .= space(4).adjustSize($date,10).' ';				
				$details .= adjustSize($reference,10).' '.
							adjustSize($temp['type'],2).' '.
							adjustRight(number_format($temp['debit'],2),12).' '.
							adjustRight(number_format(0,2),12).' '.
							adjustRight(number_format($sub_balance,2),12)."\n";
				$lc++;
				$cc++;
				$details .= adjustRight($cc,2).'. '.
							adjustSize(ymd2mdy($r->advance_applied),10).' '.
							adjustSize($reference,10).' '.
							adjustSize($temp['type'],2).' '.
							adjustRight(number_format(0,2),12).' ';

				if ($show_withdrawn == 'S')
				{
				
					$details .= adjustRight(number_format($withdrawn,2),10).' '.
									adjustRight(number_format($temp['credit'],2),10).' '.
									adjustRight(number_format($excess,2),10).' '.
									adjustRight(number_format($sub_balance,2),12).' ';
//									adjustRight(number_format($temp['credit'],2),10).' '.

				}
				else
				{
					$details .= adjustRight(number_format($temp['credit'],2),12).' '.
									adjustRight(number_format($sub_balance,2),12).' ';

				}
				$details .= "\n";

				$total_withdrawn += $withdrawn;
				$total_excess += $excess;
			}
			else
			{
				if ($temp['type']!='D') //($temp['credit'] > 0)
				{
					$cc++;
				}
				$details .= adjustRight(number_format2($cc,0),2).'. ';
				$details .=	adjustSize($date,10).' ';                           //application date
				$details .= adjustSize($reference,10).' '.
							adjustSize($temp['type'],2).' ';
				if ($temp['debit'] == 0 and $colldate != '')
				{
					$details .=	adjustSize($colldate,12).' ';				    //collection date
					$colldate = '';
				} else
				{			
					$details .=	adjustRight(number_format($temp['debit'],2),12).' ';
				}
//							adjustRight(number_format($temp['credit'],2),12).' '.
//							adjustRight(number_format($sub_balance,2),12).' ';

				if ($show_withdrawn == 'S')
				{
					$details .= adjustRight(number_format($withdrawn,2),10).' '.
									adjustRight(number_format($temp['credit'],2),10).' '.
									adjustRight(number_format($excess,2),10).' '.
									adjustRight(number_format($sub_balance,2),12).' ';
//									adjustRight(number_format($temp['credit'],2),10).' '.
				}
				else
				{
					$details .= adjustRight(number_format($temp['credit'],2),12).' '.
									adjustRight(number_format($sub_balance,2),12).' ';

				}
				
				$total_withdrawn += $withdrawn;
				$total_excess += $excess;
				
				//if ($rp->payment_detail_id == '24632') print_r($temp);
				$details .= "\n";
			}				
			$lc++;	
			if ($lc > 55)
			{			
				if ($p1=='Print Draft')
				{
					$details .= "<eject>";
					doPrint($details);
				}
				elseif ($p1 == 'Print')
				{
					echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
					echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
					echo "<script>printIframe(print_area)</script>";
				}
				$lc=8;
				$details1 .= $details;
				$details='';
			}

		if ($temp['ammort'] > 0)
		{
			$remaining_ammort_count = round(($sub_balance-$penalty)/$temp['ammort'],2);

			if ($remaining_ammort_count > intval($remaining_ammort_count))
			{
				$remaining_ammort_count=intval($remaining_ammort_count)+1;
			}
			$remaining_ammort_count = intval($remaining_ammort_count);
		}
		else
		{
			$remaining_ammort_count = '';
		}
		
		$penalty = 0;
	}
		//-- UPDATE RELEASING TABLE BALANCE FIELD

		$sub_balance = round($sub_balance,2);
		$qur = "update releasing set balance = '$sub_balance' where releasing_id = '$mreleasing_id'";
		@pg_query($qur);

	if ($multiple >0 )
	{
		
		if ($show_withdrawn == 'S')
		{
					$details .= '--- ---------- ---------- -- ------------ ---------- ----------- ----------- ----------'."\n";
					$details .= space(5).adjustSize('Remaining Ammort: '.$remaining_ammort_count,24).
						adjustRight(number_format($sub_debit,2),12).' '.
						adjustRight(number_format($sub_withdrawn,2),10).' '.
						adjustRight(number_format($sub_credit,2),10).' '.
						adjustRight(number_format($sub_excess,2),10).' '.
						adjustRight(number_format($sub_balance,2),12)."\n\n";
		}
		else
		{
			$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
			$details .= space(5).adjustSize('Remaining Ammort: '.$remaining_ammort_count,24).
				adjustRight(number_format($sub_debit,2),12).' '.
				adjustRight(number_format($sub_credit,2),12).' '.
				adjustRight(number_format($sub_balance,2),12)."\n\n";
		}
		$lc++;
		$lc++;
	}
	if ($accountbalance !=0 and $total_ammort !=0)
		$remaining_ammort_count = round(($accountbalance)/$total_ammort,2);
	else
		$remaining_ammort_count = 0;	
	if ($remaining_ammort_count > intval($remaining_ammort_count))
	{
		$remaining_ammort_count=intval($remaining_ammort_count)+1;
	}
	$remaining_ammort_count = intval($remaining_ammort_count);

	if ($show_withdrawn == 'S')
	{
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
		$details .= space(5).adjustSize('Remaining Ammort: '.$remaining_ammort_count,24).
				adjustRight(number_format($total_debit,2),12).' '.
				adjustRight(number_format($total_withdrawn,2),10).' '.
				adjustRight(number_format($total_credit,2),10).' '.
				adjustRight(number_format($total_excess,2),10).' '.
				adjustRight(number_format($accountbalance,2),12)."\n";
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	}
	else
	{
		$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
		$details .= space(5).adjustSize('Remaining Ammort: '.$remaining_ammort_count,24).
				adjustRight(number_format($total_debit,2),12).' '.
				adjustRight(number_format($total_credit,2),12).' '.
				adjustRight(number_format($accountbalance,2),12)."\n";
		$details .= '--- ---------- ---------- -- ------------ ------------ ------------ '."\n";
	}

	$details .= space(10).'Total Obligation......'.adjustRight(number_format($total_obligation,2),12)."\n";
	$details .= space(10).'Total Ammortization...'.adjustRight(number_format($total_ammort,2),12)."\n";
	$details .= space(10).'Total Balance.........'.adjustRight(number_format($accountbalance,2),12)."\n\n";
	$details .= 'Remarks :'."\n";
	$details .= $remarks."\n";
	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($details);
	}
	elseif ($p1 == 'Print')
	{
	  	$detprint = "<font style='font-family:monospace; font-size:14px; letter-spacing:1px;'>".$details1."</font>";
	
		echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$detprint.'"'.">";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
		echo "</iframe>";
		echo "<script>printIframe(print_area)</script>";

/*
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";*/
	}

}
?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr> 
      <td background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/bluelist.gif" width="16" height="17"> 
        <font color="#CCCCCC">Account Ledger</font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Account 
        Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?=lookUpAssoc('sortby',array('Name'=>'account','Account No.'=>'account_code','Releasing No'=>'releasing_id'),$sortby);?>
        <?=lookUpAssoc('show',array('Show All'=>'A','Show Balance'=>'B','Show First'=>'F', 'Show Paid'=>'P'),$show);?>
		<?= lookUpAssoc('show_withdrawn',array('Show Withdrawn'=>'S','No Withdrawal'=>'N'),$show_withdrawn);?>
        <!-- <input name="Go" type="image" id="Go" onClick="vSubmit(this)" src="../graphics/go.gif" alt="Search account" align="middle" width="23" height="19"> -->
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p1" type="submit" value="Save Remarks" id="p1"/>
      </font></td>
    </tr>
    <tr> 
      <td><hr color="red" height="1"></td>
    </tr>
  </table>

<?
  if ($p1 == 'Go')
  {
		$acc = 0;
		$bscan = array('1','2','3','7','10','11','18','19');
		$acid = array();
		$scdat = date('Y-m-d');
		$q="select * from schedule 
				where 
					date='$scdat' and branch_id = '".$ADMIN['branch_id']."' and
					active!='9' and status!='Finished'";
		$qs = pg_query($q) or message(pg_errormessage());
		while ($rs = pg_fetch_object($qs))
		{
			$acid[] = $rs->account_id;
		}
		$acc = count($acid);
  
	  $q = "select * 
				from 
					account
				where 
					account ilike '$xSearch%'";

			if ($ADMIN['branch_id'] > '0')
			{
				$q .= " and  (branch_id  = '".$ADMIN['branch_id']."'";

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
				if ($acc == 0)
					$q .= ") ";
				else
				{
					$cids = join(',',$acid);
					$q .= " or account_id IN ($cids)) ";
				}
			}
					
			$q .= "	order by
					account";
					
					
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
?>
  
<table width="75%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="38%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="21%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      Group </font></strong></td>
    <td width="11%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
    <td width="12%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
    <td width="11%" align="center">&nbsp;</td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = @pg_fetch_assoc($qr))
	{
		$account_balance=accountBalance($r['account_id']);
	
		if ($r['account_status']=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;
		
		if ($r['enable']=='t') $accstatus=status($r['account_status']);
		else $accstatus='Closed';
		
		$ctr++;
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap width="7%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td width="38%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="javascript:document.getElementById('f1').action='?p=report.accountledger_oldledger&p1=Selectreleasing&c_id=<?= $r['account_id'];?>';document.getElementById('f1').submit();"> 
      <?= $r['account'];?>
      </a> </font></td>
    <td width="21%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
      </font></td>
    <td width="11%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $accstatus;?>
      </font></td>
    <td align="right" width="12%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($account_balance,2);?>
      </font></td>
    <td width="11%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp; 
      <a href="?p=report.accountledger_oldledger&p1=Selectreleasing&show=<?=$show;?>&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>"> 
	All</a></font></td>
  </tr>
  <?
  		$q = "select * from releasing where status!='C' and account_id='".$r['account_id']."' ";
		if ($show == 'B')
		{
			$q .= " and releasing.balance > 0 ";
		}
		if ($show == 'P')
		{
			$q .= " and releasing.balance <= 0 ";
		}
		$q .= " order by releasing.date desc" ;

		$qqr = @pg_query($q) or message(pg_errormessage());
		if (@pg_numrows($qqr)>1)
		{
			while ($rr= @pg_fetch_object($qqr))
			{
		?>
  <tr  bgcolor="#EFEFEF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#EFEFEF'"> 
    <td width="7%"></td>
    <td width="38%"></td>
    <td width="21%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="javascript:document.getElementById('f1').action='?p=report.accountledger_oldledger&p1=Selectreleasing&c_id=<?= $r['account_id'];?>&r_id=<?= $rr->releasing_id;?>';document.getElementById('f1').submit();"> 
      <?= ymd2mdy($rr->date);?></a>
      </font></td>
    <td align="right" width="11%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($rr->principal,2);?>
      </font></td>
    <td align="right" width="12%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($rr->balance,2);?>
      </font></td>
    <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp; 
      <a href="?p=report.accountledger_oldledger&p1=Selectreleasing&show=1&r_id=<?=$rr->releasing_id;?>&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>"> 
	Select</a></font></td>
  </tr>
  <?
			}
		}
  }
  ?>
</table>

<?
	echo "</form>";	
  }
  elseif ($aLedger['account_id'] != '')
  {
  	$details2 = '<font style="font-family:monospace; font-size:14px; letter-spacing:2px;">'.$details1.'</font>';

?>
<table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr> 
    <td colspan="4" bgcolor="#DADADA"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Account 
      Ledger</strong></font></td>
  </tr>
  <tr> 
        <td valign="top">
	        <textarea name="la2" cols="1" rows="1" readonly><?= $details2;?></textarea>
	        <textarea name="la1" cols="97" rows="18" readonly><?= $details1;?></textarea>
	      <br><font size="2">Remarks</font><br> 
	          <textarea name="remarks" cols="97" rows="2" id="remarks"><?= $aLedger[remarks];?></textarea>
        </td>
  </tr>
</table>
  <div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
<!--    <input name="p1" type="submit" id="p1" value="Print" >-->
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(la2)" > 
  </div>
</form>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>

<?
}
?>

