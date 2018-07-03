<script>
function vMove()
{
		document.getElementById('excess').value = document.getElementById('withdrawn').value
		document.getElementById('amount').value = 0;
}
function vMove2()
{
		document.getElementById('amount').value = document.getElementById('withdrawn').value
		document.getElementById('excess').value = 0;
}
function vtoammort()
{
		document.getElementById('amount').value = document.getElementById('withdrawn').value
		document.getElementById('amount').value = 0;
		document.getElementById('excess').value = 0;
}
function vAmt()
{
	if (document.getElementById('balance').value*1 == '0') 
	{
		document.getElementById('amount').value = twoDecimals(document.getElementById('withdrawn').value);
		return;
	}
	
	if (document.getElementById('account_group_id').value != '546' && document.getElementById('selectAccountGroup').value !='SSS')
	{
		document.getElementById('amount').value = twoDecimals(document.getElementById('withdrawn').value);
	}
	$excess = 	document.getElementById('withdrawn').value  - 	document.getElementById('amount').value 
	document.getElementById('excess').value = twoDecimals($excess);
}
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Payment Entries?"))
		{
			document.f1.action="?p=payment.entry&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=payment.entry&p1=Cancel"
		}
	}
	else if (ul.name == 'Restore')
	{
		if (confirm("Are you sure to RESTORE Payment Entries?"))
		{
			document.f1.action="?p=payment.entry&p1=RestoreConfirm"
		}	
		else
		{
			document.f1.action="?p=payment.entry&p1=Restore"
		}
	}
	else
	{
		document.f1.action="?p=payment.entry&p1="+ul.id;
		f1.submit()
	}	
}
function vSubmitAccountId(t)
{
	f1.action="?p=payment.entry&p1=selectAccount&id="+t.value;
	f1.submit()
}
</script>
<?
if (!chkRights3('payment','mview',$ADMIN['admin_id']))
{
	message("You have no permission to entry payment/collection...");
	exit;
}
if (!session_is_registered('aPay'))
{
	session_register('aPay');
	$aPay = null;
	$aPay = array();
}
if (!session_is_registered('aPayD'))
{
	session_register('aPayD');
	$aPayD = null;
	$aPayD = array();
}
if (!session_is_registered('iPayD'))
{
	session_register('iPayD');
	$iPayD = null;
	$iPayD = array();
}
function aToolTip($aid)
{
	global  $aPay, $iPayD;
	
	$Q = "select * from account where account_id = '$aid'";
	$QR = @pg_query($Q) or message(pg_errormessage());
	$R = @pg_fetch_object($QR);

	$withdraw_day = $R->withdraw_day;
	$tooltip = 'Withdraw Day '.$R->withdraw_day.' Salary P '.number_format($R->salary,2);
	
	$Q = "select 
					releasing.releasing_id,
					releasing.date as releasing_date,
					releasing.ammort as ammort,
					releasing.balance,
					releasing.term
			 from 
			 		releasing where status!='C' and account_id = '$aid' and balance>0";
	$QR = @pg_query($Q) or message(pg_errormessage());
	$amount_due = 0;
	
	if ($iPayD['ddate'] != '' && $iPayD['ddate'] != '--' )
	{
		$d = $iPayD['ddate'];
	}
	elseif ($aPay['date'] && $aPay['date'] != '--')
	{
		$d = $aPay['date'];
	}
	else
	{
		$d = date('Y-m-d');
	}

	while ($R = @pg_fetch_assoc($QR))
	{
			$R['withdraw_day'] = $withdraw_day;

			$aDue = amountDue($R, $d);

			$amount_due += $aDue['amount_due'];
	}
	$tooltip .= ' Total Amount Due P '.number_format($amount_due);
	return $tooltip;
}

$fields_header = array('reference','date','name','total_amount','mcheck','account_group_id');
$fields_detail = array('account_id','name','amount', 'withdrawn','excess','remark');
if (!in_array($p1 ,array(null,'Print','New','Edit','selectAccountGroupId','selectAccountId','selectReleaseId')))
{
	for ($c=0;$c<count($fields_header);$c++)
	{
		if ($fields_header[$c] == 'date')
		{
			$aPay[$fields_header[$c]] = mdy2ymd($_REQUEST[$fields_header[$c]]);
		}
		else
		{
			$aPay[$fields_header[$c]] = $_REQUEST[$fields_header[$c]];
			if ($aPay[$fields_header[$c]] == '' )
			{
				$aPay[$fields_header[$c]] = '0';
			}
		}	
	}
	
	for ($c=0;$c<count($fields_detail);$c++)
	{
		if ($fields_detail[$c] == 'ddate')
		{
			$iPayD[$fields_detail[$c]] = mdy2ymd($_REQUEST[$fields_detail[$c]]);
		}
		else
		{
			$iPayD[$fields_detail[$c]] = $_REQUEST[$fields_detail[$c]];
			if ($iPayD[$fields_detail[$c]] == '' && $fields_detail[$c]!='remark' )
			{
				$iPayD[$fields_detail[$c]] = 0;
			}
		}	
	}
	$iPayD['ddate'] = mdy2ymd($_REQUEST['ddate']);

}
if ($p1 == 'New' or $p1=='Advance Payment')
{
	$aPay = null;
	$aPay = array();
	$aPayD = null;
	$aPayD = array();
	$iPayD = null;
	$iPayD = array();
	$aPay['date'] = date('Y-m-d');
	if ($p1 == 'Advance Payment')
	{
		$aPay['remarks'] = 'ADVANCE PAYMENT';
	}
}
elseif ($p1 == 'Load')
{
	$aPay = null;
	$aPay = array();
	$aPayD = null;
	$aPayD = array();
	$iPayD = null;
	$iPayD = array();
	if ($id =='')
	{
		message('Nothing to edit...');
	}
	else
	{
		$q = "select * 	from payment_header where payment_header_id='$id'";
		$r = fetch_assoc($q);
		$aPay=$r;

		$q = "select * from account_group where account_group_id='".$aPay['account_group_id']."'";
		$r = fetch_assoc($q);
		if ($r)
		{
			$aPay +=$r;
		}
		
		$q = "select * from payment_detail where payment_header_id='$id'";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		while ($r = pg_fetch_assoc($qr))
		{
			$remark = stripslashes($r[remark]);
			$r[remark] = $remark;
			$temp = $r;

			$q = "select * from account where account_id='".$temp['account_id']."'";
			$rr = fetch_assoc($q);
			if ($rr)
			{
				$temp +=$rr;
			}

			$q = "select ledger_id from ledger 
					where 
						reference='".$aPay['payment_header_id']."' and type='C' and
						releasing_id='".$temp['releasing_id']."' and 
						status != 'C' and
						account_id='".$temp['account_id']."'";
			
			$qqr = @pg_query($q) or message(pg_errrormessage());
			$cl = 0;
			while ($rl = @pg_fetch_assoc($qqr))
			{
				$cl++;
				if ($cl == 1)
				{
					$rr = $rl;
				}
				elseif ($cl > 1)
				{
					$q = "update ledger set status = 'C' where ledger_id='".$rl['ledger_id']."'";
					$qqqr = @pg_query($q) or message(pg_errormessage());
				}
			}
			if ($rr != '')
			{
				$temp +=$rr;
			}
			else
			{
				//added due to mis saved releasing_id in ledger table
				$q = "select ledger_id from ledger 
					where 
						reference='".$aPay['payment_header_id']."' and type='C' and
						credit='".$temp['amount']."' and 
						account_id='".$temp['account_id']."'";
				$rr = fetch_assoc($q);
				if ($rr != '')
				{
					$temp +=$rr;
				}
			}	

			if ($temp['releasing_id'] == '')
			{
				$q = "select 
							account.account_id, 
							account.account
						from 
							account	
						where
							account_id = '".$temp['account_id']."'"; 
			}
			else
			{
				$q = "select 
							account.account_id, 
							account.account, 
							account.account_group_id, 
							releasing.releasing_id,
							releasing.ammort as amount,
							releasing.balance
						from 
							account,
							releasing
						where
							account.account_id=releasing.account_id and
							releasing_id='".$temp['releasing_id']."'";
			}				
			$rr = fetch_assoc($q);
			if ($rr)
			{
				$temp +=$rr;
			}

			$aPayD[] = $temp;
		}
	}	
}
elseif ($p1 == 'selectReleaseId' && $id!='')
{
	$ddate = $iPayD['ddate'];
	$q = "select 
					account.account_id, 
					account.account, 
					account.account_group_id, 
					releasing.withdraw_day,
					releasing.releasing_id,
					releasing.date as releasing_date,
					releasing.ammort as ammort,
					releasing.balance,
					releasing.term
				from 
				 	account,
					releasing
				where
				 	account.account_id=releasing.account_id and
					releasing_id='$id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (pg_num_rows($qr)>0)
	{
		$iPayD = pg_fetch_assoc($qr);
		if ($iPayD[withdraw_day] == 0) 
			$iPayD[withdraw_day]=lookUpTableReturnValue('x','account','account_id','withdraw_day', $aPay[account_id]);
		if ($ddate != '' || $ddate =='//') $d = $aPay['date'];
		else $d = mdy2ymd($ddate);	
		$aDue = amountDue($iPayD, $d);
		
		$iPayD['amount'] = round($aDue['amount_due'],2);
		if ($iPayD['account_group_id'] != $aPay['account_group_id']  && $aPay['remarks'] != 'ADVANCE PAYMENT')
		{
			message("This account belongs to a different account group -- ".lookUpTableReturnValue('x','account_group','account_group_id','account_group', $iPayD['account_group_id']));
		}
		$focus = 'ddate';
	}
	else
	{
		message('No Data Found....');
		$focus = 'account';
	}	
	if ($ddate == '' or $ddate == '--') $ddate = date('Y-m-d');
	$iPayD['ddate'] = $ddate;

}
elseif ($p1 == 'selectAccountId' && $id!='')
{
	$ddate = $iPayD['ddate'];
	$q = "select 
					account.account_id, 
					account.account, 
					account.account_group_id, 
					account.withdraw_day
				from 
				 	account
				where
					account.account_id='$id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr)>0)
	{
		$iPayD = @pg_fetch_assoc($qr);
	
		$q = " select 
					releasing.releasing_id,
					releasing.date as releasing_date,
					releasing.ammort as ammort,
					releasing.balance,
					releasing.term,
					withdraw_day
				from 
					releasing
				where
					releasing.account_id='$id' and
					releasing.status!='C' and
					balance>0";
		$qr = @pg_query($q) or message(pg_errormessage().$q);

					
		if ($ddate != '' || $ddate !='//' || $ddate != '--') $d = $ddate;
		elseif ($date != '' || $date !='//' || $date != '--') $d = $date;
		else $d = '';	

		$amount_due = 0;
		
		while ($r = @pg_fetch_assoc($qr))
		{
			if ($r['withdraw_day'] == 0) $r['withdraw_day'] = $iPayD['withdraw_day'];
			else $iPayD['withdraw_day'] = $r['withdraw_day'];

			$aDue = amountDue($r, $d);
			$amount_due += $aDue['amount_due'];
		}
		$iPayD['amount'] = round($amount_due,2);	

		if ($iPayD['account_group_id'] != $aPay['account_group_id']  && $aPay['remarks'] != 'ADVANCE PAYMENT')
		{
			message("This account belongs to a different account group -- ".lookUpTableReturnValue('x','account_group','account_group_id','account_group', $iPayD['account_group_id']));
		}
		$focus = 'ddate';
	}
	else
	{
		message('No Data Found....');
		$focus = 'account';
	}	
	if ($ddate == '' or $ddate == '--') $ddate = date('Y-m-d');
	$tooltip = aToolTip($iPayD['account_id']);
	$iPayD['ddate'] = $ddate;

}
elseif ($p1 == 'selectAccountGroupId' && $id!='')
{
	$q = "select account_group_id, account_group
				 from account_group where account_group_id='$id'";
	$r = fetch_assoc($q);
	$aPay['account_group'] = $r['account_group'];
	$aPay['account_group_id'] = $r['account_group_id'];
}

elseif ($p1 == 'Ok' && $iPayD['account_id']=='')
{
	message('Specify Account Please...');
}
elseif ($p1 == 'Ok' && $iPayD['amount']=='' && $iPayD['withdrawn']*1 == '0')
{
	message('Specify Amount Please...');
}
elseif ($p1 == 'Ok')
{
	$q = "select account_id, account, account_group_id
				 from account where account_id='".$iPayD['account_id']."'";
	$ro = fetch_assoc($q);
	if ($ro)
	{
		$iPayD += $ro;
	}
	if ($iPayD['account_group_id'] != $aPay['account_group_id'] && $aPay['remarks']!='ADVANCE PAYMENT')
	{
		message("This account belongs to a different account group -- ".lookUpTableReturnValue('x','account_group','account_group_id','account_group', $iPayD['account_group_id']));
	}

	$fnd=0;
	$c=0;
	if ($aPay['remarks'] == 'ADVANCE PAYMENT')
	{
		$aPay['total_amount'] =0;
	}		
	foreach ($aPayD as $temp)
	{
		
		if ($temp['releasing_id'] == $iPayD['releasing_id'] && $temp['account_id'] == $iPayD['account_id'])
		{
			$dummy = $temp;
			$dummy['amount']=$iPayD['amount'];
			$dummy['withdrawn']=$iPayD['withdrawn'];
			$dummy['excess']=$iPayD['excess'];
			$dummy['ddate']=$iPayD['ddate'];
			$dummy['remark']=stripslashes($iPayD['remark']);
			$aPayD[$c] = $dummy;
			$fnd=1;
			break;
		}
		$c++;
	}
	if ($fnd==0)
	{
		if ($iPayD['ddate'] == '--')
		{
			$iPayD['ddate'] = $aPay['date'];
		}
		$aPayD[] = $iPayD;
		
	}
	foreach ($aPayD as $temp)
	{
		if ($aPay['remarks'] == 'ADVANCE PAYMENT')
		{
			$aPay['total_amount'] += $temp['amount'];
		}		
	}	

	$ddate = $iPayD['ddate'];
	$iPayD=null;
	$iPayD=array();
	$search='';
	$iPayD['ddate'] = $ddate;
	$focus = 'account';
}
elseif ($p1 == 'Edit')
{
	$c=0;
	foreach ($aPayD as $temp)
	{
		$c++;
		if ($ctr == $c)
		{
			$iPayD= $temp;
			break;
		}
			
	}
}

if (!chkRights3('payment','madd',$ADMIN['admin_id']) && $aPay['payment_header_id'] == '')
{
	message("You have no permission to add new entry for payment/collection...");
	exit;
}
if (!chkRights2('payment','medit',$ADMIN['admin_id']) && $aPay['payment_header_id'] != '')
{
	message("You have no permission to edit/update entry of payment/collection...");
	exit;
}
elseif ($p1 == 'CancelConfirm' && $aPay['payment_header_id'] != '')
{
	$aPay['status'] ='C';
	$q = "update payment_header set status='C' where payment_header_id = '".$aPay['payment_header_id']."'";
	$qr  =@pg_query($q) or message(pg_errormessage());
	
	if ($qr)
	{
		$q = "update ledger set status='C' where type='C' and reference= '".$aPay['payment_header_id']."'";
		$qr  =@pg_query($q) or message(pg_errormessage());

		$q = "select * from ledger where status='C' and type='C' and reference= '".$aPay['payment_header_id']."'";
		$qr  =@pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			recalculate($r['releasing_id'],'noneform');
		}
		message("Payment Transaction  CANCELLED !");

	}
}
elseif ($p1 == 'RestoreConfirm' && $aPay['payment_header_id'] != '')
{
	$aPay['status'] ='S';
	$q = "update payment_header set status='S' where payment_header_id = '".$aPay['payment_header_id']."'";
	$qr  =@pg_query($q) or message(pg_errormessage());
	
	if ($qr)
	{
		$q = "update ledger set status='S' where type='C' and reference= '".$aPay['payment_header_id']."'";
		$qr  =@pg_query($q) or message(pg_errormessage());

		$q = "select * from ledger where status='S' and type='C' and reference= '".$aPay['payment_header_id']."'";
		$qr  =@pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			recalculate($r['releasing_id'],'noneform');
		}
		message("Payment Transaction  RESTORED !");

	}
}
elseif ($p1 == 'Delete Checked' && count($aChk)>0)
{
	$newArray=null;
	$newArray=array();
	$nctr=0;
	foreach ($aPayD as $temp)
	{
		$nctr++;
		if (in_array($nctr,$aChk))
		{
			if ($temp['payment_detail_id'] != '')
			{
				$dummy = $temp;
				$dummy['record_status'] = 'deleted';
				$newArray[]=$dummy;
			}
		}
		else
		{
			$newArray[]=$temp;
		}
	}
	$aPayD = $newArray;	
}
elseif ($p1 == 'Restore' && count($aChk)>0)
{
	$newArray=null;
	$newArray=array();
	$nctr=0;
	foreach ($aPayD as $temp)
	{
		if (in_array($nctr,$aChk))
		{
			if ($temp['payment_detail_id'] != '')
			{
				$dummy = $temp;
				$dummy['record_status'] = '';
				$newArray[]=$dummy;
			}
		}
		else
		{
			$newArray[]=$temp;
		}
		$nctr++;
	}
	$aPayD = $newArray;	
}
elseif ($p1 == 'Print')
{
	$q = "select * 
				from
					payment_header,
					payment_detail,
					account
				where
					payment_header.payment_header_id=payment_detail.payment_header_id and
					account.account_id=payment_detail.account_id and
					payment_header.payment_header_id='".$aPay['payment_header_id']."'
				order by
					account.branch_id,
					account.clientbank_id";

	$header = center("PAYMENT/COLLECTION REPORT",80)."\n";					
	$header .= center("Control No.".$aPay['payment_header_id'],80)."\n";
	
	$header .= "     Account Name                      Withdrawn      Ammort     Excess\n";
	$header .= "---- -------------------------------- ------------ ----------- ------------\n";
	
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$total_withdrawn = $total_excess = $total_amount = $ctr = 0;	
	$branch_id = $client_bank_id = '';
	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;
		if ($branch_id != $r->branch_id)
		{
			$details .= "\nBRANCH : ".lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id)."\n";
			$branch_id = $r->branch_id;
		}
		
		if ($clientbank_id != $r->clientbank_id)
		{
			$details .= "BRANK : ".lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id)."\n";
			$clientbank_id = $r->clientbank_id;
		}
		$details .= adjustRight($ctr,3).'. '.
						adjustSize($r->account,30).' '.
						adjustRight(number_format($r->withdrawn,2),12).' '.		
						adjustRight(number_format($r->amount,2),12).' '.		
						adjustRight(number_format($r->excess,2),12)."\n";
		$total_withdrawn += $r->withdrawn;
		$total_excess += $r->excess;
		$total_amount += $r->amount;		
	
	}
	$details .= "---- -------------------------------- ------------ ----------- ------------\n";
	$details .= space(6).adjustSize('TOTAL',30).' '.
						adjustRight(number_format($total_withdrawn,2),12).' '.		
						adjustRight(number_format($total_amount,2),12).' '.		
						adjustRight(number_format($total_excess,2),12)."\n";
	$details .= "---- -------------------------------- ------------ ----------- ------------\n";
	
	doPrint($header.$details);
	//echo "<pre>$header$details</pre>	";			
}

if ($aPay['date'] == '' or $aPay['date']=='//')
{
	$aPay['date'] = date('Y-m-d');
}


if ($iPayD['account_id'] != '' && $tooltip == '')
{

	$tooltip = aToolTip($iPayD['account_id']);

}


?>
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
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="93%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
  <tr>
      <td colspan="3" bgcolor="#DFEAED"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> .:: 
	  <a href='#'  onClick="document.getElementById('Save').click()">Save</a> | 
	  <a href='#'  onClick="document.getElementById('Print').click()">Print </a> | 
	  <a href='#'  onClick="document.getElementById('Cancel').click()">Cancel</a> | 
	  <a href='#'  onClick="document.getElementById('New').click()"> New</a> | 
	  <a href='#'  onClick="window.location='?p=payment.browse&p1=Browse'">Browse</a></font></td>
    </tr>
    <tr bgcolor="#3366FF"  background="../graphics/table0_horizontal.PNG"> 
      <td height="21" colspan="3" background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>&nbsp;<img src="../graphics/list3.gif" width="16" height="16"><font color="#EFEFEF">Account 
        Payment/Collection Entry</font></strong></font></td>
      <td height="21" align="center" background="../graphics/table_horizontal.PNG"> <font size="2"># 
        <input type='text' size='8' value="<?= $aPay['payment_header_id'];?>" name="ph_id" id="ph_id" readOnly style="text-align:center; border:0; background-color:#3399CC;font-color:#FFCCCC; padding:0;" >
		&nbsp;<?= status($aPay['status']);?>
        </font> </td>
    </tr>
    <tr <?= ($aPay['status'] == 'C' ? "bgColor='#FF9999'": "bgcolor='#FFFFFF'");?>> 
      <td width="16%" height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></td>
      <td width="33%"><input name="reference" type="text" id="reference" value="<?= $aPay['reference'];?>" size="10" maxlength="10"    style="border: #CCCCCC 1px solid;"  onKeypress="if(event.keyCode == 13){document.getElementById('name').focus();return false;}"></td>
      <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td width="37%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aPay['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')"   style="border: #CCCCCC 1px solid;"  onKeyUp="setDate(this,'MM/dd/yyyy','en')"  onKeypress="if(event.keyCode == 13){document.getElementById('total_amount').focus();return false;}">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Collector</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="name" type="text" id="name" value="<?= $aPay['name'];?>" size="30" maxlength="30"   style="border: #CCCCCC 1px solid;"  onKeypress="if(event.keyCode == 13){document.getElementById('account_group').focus();return false;}">
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></td>
      <td><input name="total_amount" type="text" id="total_amount" value="<?= $aPay['total_amount'];?>"  style="border: #CCCCCC 1px solid;text-align:right" size="10" maxlength="10"   onKeypress="if(event.keyCode == 13){document.getElementById('mcheck').focus();return false;}"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        Group</font></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="account_group" type="text" id="selectAccountGroup" value="<?= $aPay['account_group'];?>"   style="border: #CCCCCC 1px solid;"  size="30" maxlength="30" onChange="vSubmit(this)">
        <input type="button" name="selectAccountGroup" value="..."  onClick="vSubmit(this)" id="selectAccountGroup"  style="font-size:10px; margin:1px; padding:0px">
        <input name="account_group_id" type="hidden" id="account_group_id" value="<?= $aPay['account_group_id'];?>" size="10">
        </font></td>
      <td>&nbsp;</td>
      <td><!-- <input name="mcheck" type="text" id="mcheck" value="<?= $aPay['mcheck'];?>" size="20" maxlength="20"   style="border: #CCCCCC 1px solid;"   onKeypress="if(event.keyCode == 13){document.getElementById('account').focus();return false;}">--></td>
    </tr>
  </table>
  <table width="93%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr > 
      <td colspan="6" bgcolor="#EFEFEF"  background="../graphics/table0_horizontal.PNG"> 
        <font color="#EFEFEF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        &nbsp; </strong><font color="#DADADA">
		<a href="#"  onmouseover="showToolTip(event,'<?= $tooltip;?>');return false" onmouseout="hideToolTip()">
		Account Details</a></font></font></td>
    </tr>
    <tr bgcolor="#E4EEEE"> 
      <td width="20%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        Account<br>
        <input name="account" type="text" id="account" value="<?= $iPayD['account'];?>" size="25" maxlength="30"  style="border: #CCCCCC 1px solid; " onKeyPress="if(event.keyCode==13) {document.getElementById('ddate').focus();return false;}" onBlur="if (this.value != ''){document.getElementById('selectAccount').click();}">
        <input type="button" name="selectAccount" value="..."  onClick="vSubmit(this)" id="selectAccount" style="font-size:10px; margin:1px; padding:0px">
        </font></td>
      <td width="6%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.ddate, 'mm/dd/yyyy')"> 
        <br>
        <input name="ddate" type="text" id="ddate" value="<?= ymd2mdy($iPayD['ddate']);?>" size="8"  onChange="wait('Computing...');xajax_paymentapplication(xajax.getFormValues('f1'))" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"  style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('withdrawn').focus();return false;}">
        </font></td>
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdrawn</font><br> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="withdrawn" type="text" id="withdrawn" value="<?= $iPayD['withdrawn'];?>" size="9" onBlur="wait('Computing...');xajax_paymentapplication(xajax.getFormValues('f1'))"   style="border: #CCCCCC 1px solid; text-align:right"  onKeypress="if(event.keyCode == 13){document.getElementById('remark').focus();return false;}" >
        </font> </td>
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">AmtApplied<br>
        <input name="amount" type="text" id="amount" value="<?= $iPayD['amount'];?>" size="9" readOnly style="border: #CCCCCC 1px solid; text-align:right"  onKeypress="if(event.keyCode == 13){document.getElementById('excess').focus();return false;}">
        </font></td>
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Excess</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <br>
        <input name="excess" type="text" id="excess" value="<?= $iPayD['excess'];?>" size="9"  readOnly  style="border: #CCCCCC 1px solid; text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('remark').focus();return false;}">
        </font></td>
      <td width="55%">
	  <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font>
	  	<font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="#" onClick="vMove()" onmouseover="showToolTip(event,'Move Withdrawn Amount To Excess');return false" onmouseout="hideToolTip()">[&gt;&gt;Move to Excess&gt;&gt;]</a></font>
		<?
		if (chkRights4('payment','mdelete',$ADMIN['admin_id']))
		{ 
		?>		
		<font size="1" face="Verdana, Arial, Helvetica, sans-serif"> ||
        <a href="#" onClick="vMove2()" onmouseover="showToolTip(event,'Move Withdrawn Amount To Ammortization');return false" onmouseout="hideToolTip()">[&gt;&gt;Move to Ammort.&gt;&gt;]</a></font>
		<?
		}
		?>		
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
        <input name="remark" type="text" id="remark" value="<?= $iPayD['remark'];?>" size="20"    style="border: #CCCCCC 1px solid; "  onKeypress="if(event.keyCode == 13){document.getElementById('Ok').focus();return false;}">
        <input name="p1" type="submit" id="Ok" value="Ok">
        <input name="account_id" type="hidden" id="account_id" value="<?= $iPayD['account_id'];?>" size="10">
        <input name="balance" type="hidden" id="balance" value="<?= $iPayD['balance'];?>" size="10">
        </font></td>
    </tr>
  </table>
  <table width="93%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#DADADA"> 
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></td>
      <td colspan="2" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        </font></td>
      <td width="9%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td width="10%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdrawn</font></td>
      <td width="10%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ammort</font></td>
      <td width="10%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Excess</font></td>
      <td width="26%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
    </tr>
    <?
	$ctr=0;
	$aPay['sub_total'] = $aPay['sub_withdrawn'] = $aPay['sub_excess'] =0;
	foreach ($aPayD as $temp)
	{

		if ($temp['record_status'] == 'deleted') continue;
		$aPay['sub_total'] += $temp['amount'];
		$aPay['sub_withdrawn'] += $temp['withdrawn'];
		$aPay['sub_excess'] += $temp['excess'];

		$tooltip = aToolTip($temp['account_id']);
		
		$ctr++;
	?>
    <tr bgcolor="#FFFFFF"> 
      <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $ctr;?>. <input name="aChk[]" type="checkbox" id="aChk[]" value="<?= $ctr;?>">
        </font></td>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href='?p=payment.entry&p1=Edit&ctr=<?=$ctr;?>'   onmouseover="showToolTip(event,'<?= $tooltip;?>');return false" onmouseout="hideToolTip()"> 
        <?= $temp['account'];?>
        </a> </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= ymd2mdy($temp['ddate']);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($temp['withdrawn'],2);?>
        &nbsp;</font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($temp['amount'],2);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($temp['excess'],2);?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $temp['remark'];?>
        </font></td>
    </tr>
    <?
	}
	
	?>
    <tr> 
      <td colspan="3"><strong> 
        <input name="p1" type="submit" id="p1" value="Delete Checked">
        </strong></td>
      <td width="9%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPay['sub_withdrawn'],2);?>
        </font></strong>&nbsp;&nbsp; </td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPay['sub_total'],2);?>
        </font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPay['sub_excess'],2);?>
        </font></strong><em> </em></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="8" align="center" bgcolor="#FFFFFF"><em><font face="Times New Roman, Times, serif">Diff 
        : 
        <?=number_format($aPay['total_amount']-$aPay['sub_withdrawn'],2);?>
        </font></em></td>
    </tr>
    <tr> 
      <td colspan="8" bgcolor="#FFFFFF"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="document.getElementById('topMark').scrollIntoView(false);if(confirm('Are you sure to Save Payment Entry?')){wait('Please wait. Saving Payment Entry...');xajax_savePayment(xajax.getFormValues('f1'));return false;}else{return false;}" name="Save"  accesskey="S">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
			<?
			if ($aPay[status]=='C' and ($ADMIN[admin_id]==54 or $ADMIN[admin_id]==197 or $ADMIN[admin_id]==128 or 
					$ADMIN[admin_id]==1))
			{
			?>
            <td nowrap width="25%"> <input type='image' name="Restore" id="Restore" onClick="vSubmit(this)"  src="../graphics/trash-restore.jpg" alt="Restore Form" width="50" height="25"> 
			<?
			}
			else
			{
			?>
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> 
			<?
			}
			?>
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            <td nowrap width="25%"> <img name="Browse" id="Browse" onClick="window.location='?p=payment.browse&p1=Browse'"  src="../graphics/browse.GIF" alt="Browse Payments Made" width="67" height="17"> 
            </td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<div align="center"><font color="#000000" size="2"> <em> 
  <?= $aPay['remarks'];?>
  </em></font></div>
<?
if ($focus != '')
{
	echo "<script>document.getElementById('$focus').focus()</script>";
}

  if ($p1 == 'selectAccount')
  {
  	include_once('payment.searchaccount.php');
  }
  elseif ($p1 == 'selectAccountGroup')
  {
  	include_once('payment.searchaccountgroup.php');

  }

  ?>
