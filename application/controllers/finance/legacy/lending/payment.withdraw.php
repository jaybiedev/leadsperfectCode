<script>
function vAmt()
{
	if (1*f1.withdrawn.value != 0 && 1*f1.amount.value != 0 )
	{
		f1.excess.value = 1*f1.withdrawn.value - 1*f1.amount.value;
	}
	else
	{
		f1.excess.value = 1*f1.excess.value;
		f1.amount.value = 1*f1.amount.value;
	}	
}
function vSelectBank()
{
	if (f1.clientbank.value != '' && f1.withdraw_day.value!='')
	{
		f1.action='?p=payment.withdraw&p1=selectClientBank';
		f1.submit();
	}
}

function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Payment Entries?"))
		{
			document.f1.action="?p=payment.withdraw&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=payment.withdraw&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=payment.withdraw&p1="+ul.id;
		f1.submit()
	}	
}
</script>
<?
if (!session_is_registered('aPW'))
{
	session_register('aPW');
	$aPW = null;
	$aPW = array();
}
if (!session_is_registered('aPWD'))
{
	session_register('aPWD');
	$aPWD = null;
	$aPWD = array();
}
if (!session_is_registered('iPWD'))
{
	session_register('iPWD');
	$iPWD = null;
	$iPWD = array();
}

$fields_header = array('clientbank','date','date_withdrawn','aConfirm');
$fields_details = array('account_id','amount','withdrawn','excess','remark');

$p1 = $_REQUEST['p1'];


if (!in_array($p1,array(NULL,'Edit','New','Print','selectAccount')))
{
	for ($c=0;$c<count($fields_header);$c++)
	{
		$aPW[$fields_header[$c]] = $_REQUEST[$fields_header[$c]];
		if (substr($fields_header[$c],0,4) == 'date')
		{
			$aPW[$fields_header[$c]] = mdy2ymd($_REQUEST[$fields_header[$c]]);
		}
	}	

	for ($c=0;$c<count($fields_details);$c++)
	{
		$iPWD[$fields_details[$c]] = $_REQUEST[$fields_details[$c]];
		if ($iPWD[$fields_details[$c]] == '' && !in_array($fields_details[$c],array('remark')))
		{
			$iPWD[$fields_details[$c]] = 0;
		}
	}
	
	if (is_null($aPW['aConfirm'])) $aPW['aConfirm'] = array();
}

if ($p1 == 'New')
{
	$aPW = NULL;
	$aPW = array();
	$aPWD = NULL;
	$aPWD = array();
	$iPWD = NULL;
	$iPWD = array();

	$aPW['date'] = date('Y-m-d');
	$aPW['date_withdrawn'] = date('Y-m-d');
}
elseif ($p1 == 'selectReleaseId' && $id!='')
{
	$ddate = $iPWD['ddate'];
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
					releasing_id='$id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (pg_num_rows($qr)>0)
	{
		$iPWD = pg_fetch_assoc($qr);
		if ($iPWD['account_group_id'] != $aPW['account_group_id']  && $aPW['remarks'] != 'ADVANCE PAYMENT')
		{
			message("This account belongs to a different account group -- ".lookUpTableReturnValue('x','account_group','account_group_id','account_group', $iPWD['account_group_id']));
		}
	}
	else
	{
		message('No Data Found....');
	}	
	$iPWD['ddate'] = $ddate;

}
elseif ($p1 == 'selectAccount' && $id!='')
{
	$q = "select account_id, account, account_group_id, salary as withdrawn
				 from account where account_id='$id'";
	$iPWD = fetch_assoc($q);

	//sum(ammort) for multiple outstanding account
	$q = "select ammort as amount,  releasing_id  from releasing where account_id='$id' and enable order by date desc ";
	$r = fetch_assoc($q);

	if ($r != '')
	{
		$iPWD += $r;
	}
	else
	{
		$iPWD['releasing_id'] = 0;
		$iPWD['amount'] = 0;
	}	
	$iPWD['excess'] = $iPWD['withdrawn'] - $iPWD['amount'];


}
elseif ($p1 == 'Ok' && $iPWD['account_id'] == '')
{
	message("Specify Account, please...");
}
/*elseif ($p1 == 'Ok' && ($iPWD['amount'] == '' && $iPWD['remark'] == ''))
{
	message("Specify Amount, please...");
}
*/
elseif ($p1 == 'Ok')
{

	$c=0;
	foreach ($aPWD as $temp)
	{
		if ($temp['account_id'] ==  $iPWD['account_id'])
		{
			$dummy = $temp;
			$dummy['amount'] = $iPWD['amount'];
			$dummy['withdrawn'] = $iPWD['withdrawn'];
			$dummy['excess'] = $iPWD['excess'];
			$dummy['mconfirm'] = $iPWD['mconfirm'];
			$dummy['remark'] = $iPWD['remark'];
			$aPWD[$c] = $dummy;
			$fnd = 1;
			break;
		}
		
		$c++;
	}
	if ($fnd == 0)
	{
		$iPWD['account'] = lookUpTableReturnValue('x','account','account_id','account',$iPWD['account_id']);
		$aPWD[] = $iPWD;
	}
	$iPWD=null;
	$iPWD=array();
}
elseif ($p1 == 'Edit' && $ctr != '')
{
	$c=0;
	foreach ($aPWD as $temp)
	{
		$c++;
		if ($ctr == $c)
		{
			$iPWD= $temp;
			break;
		}
			
	}
	
}
elseif ($p1 == 'Load')
{
	$aPW = null;
	$aPW = array();
	$aPWD = null;
	$aPWD = array();
	$iPWD = null;
	$iPWD = array();
	
	$confirm_array = null;
	$confirm_array = array();
	if ($id =='')
	{
		message('Nothing to edit...');
	}
	else
	{
		$q = "select * 	from payment_header where payment_header_id='$id'";
		$r = fetch_assoc($q);
		$aPW=$r;
		$q = "select * from payment_detail where payment_header_id='$id'";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		while ($r = pg_fetch_assoc($qr))
		{
			$temp = $r;

			if ($temp['releasing_id'] == '' or $temp['releasing_id'] == 0)
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
			$temp +=$rr;
			$aPWD[] = $temp;
			if ($r['mconfirm'] == 't')
			{
				$confirm_array[] = count($aPWD);
			}

		}
		$aPW['aConfirm'] = $confirm_array;
	}	

}
elseif ($p1 == 'Save' && ($aPW['date'] == '--'))
{
	message('Cannot Save.  No Transaction Date Specified');
}
elseif ($p1 == 'Save' && ($aPW['date_withdrawn']=='--' ))
{
	message('Cannot Save. No Withdrawal Date Specified');
}
elseif ($p1 == 'Save')
{
	if ($aPW['payment_header_id'] == '')
	{
		begin();
		$ok=true;

		$q = "insert into payment_header (entry_type, date, date_withdrawn,total_amount, admin_id)
				values ('W','".$aPW['date']."', 
						'".$aPW['date_withdrawn']."', '".$aPW['total_amount']."','".$ADMIN['admin_id']."')";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		if (pg_affected_rows($qr) == 0 or !$qr)
		{
			rollback();
			message('Unable to save to collection header...');
		}
		else
		{
			$qr = query("select currval('payment_header_payment_header_id_seq'::text)");
			$r = pg_fetch_object($qr);
			$aPW['payment_header_id']=$r->currval;
			$aPW['status']='S';
			
			$c=0;
			foreach ($aPWD as $temp)
			{
				if (in_array($c+1,$aPW['aConfirm']))
				{
					$mconfirm = 'true';
				}
				else
				{
					$mconfirm = 'false';
				}
				$q = "insert into payment_detail (payment_header_id, account_id, releasing_id, amount, withdrawn, excess, mconfirm, remark)
							values ('".$aPW['payment_header_id']."', '".$temp['account_id']."', 
									'".$temp['releasing_id']."', '".$temp['amount']."','".$temp['withdrawn']."',
									'".$temp['excess']."', '$mconfirm','".$temp['remark']."')";


				$qr = @pg_query($q) or message(pg_errormessage().$q);
				if (@pg_affected_rows($qr) == 0 or !$qr)
				{
					rollback();
					$aPW['payment_header_id'] = '';
					message('Unable to save to collection detail...');
					break;
					$ok = false;
				}
				else
				{
					$qr = query("select currval('payment_detail_payment_detail_id_seq'::text)");
					$r = pg_fetch_object($qr);
					$dummy = $temp;
					$dummy['payment_detail_id']=$r->currval;

			//		$q = "insert into ledger (account_id, releasing_id,  date, reference, type, credit)
			//				values
			//					('".$temp['account_id']."','".$temp['releasing_id']."','".$aPW['date_withdrawn']."',
			//						'".$aPW['payment_header_id']."','C','".$temp['amount']."')";
			//		$qr = @pg_query($q) or message(pg_errormessage().$q);
					if (!$qr && pg_affected_rows($qr)<=0)
					{
						rollback();
						message('Unable to save to collection detail...ledger');
						$ok=false;
						break;
					}
					else
					{
			//			$qr = query("select currval('ledger_id_seq'::text)");
			//			$r = pg_fetch_object($qr);
			//			$dummy['ledger_id']=$r->currval;
						$dummy['payment_detail_id']=$r->currval;
						$aPWD[$c] = $dummy;
					}
				}
				$c++;
			}
			if (!$ok)
			{	
				rollback();
				$aPW['payment_header_id'] = '';
			}
			else
			{
				commit();
			}
		}
	}
	else
	{
		$ok=true;
		$q = "update payment_header set  
						total_amount='".$aPW['total_amount']."', 
						date='".$aPW['date']."', 
						date_withdrawn='".$aPW['date_withdrawn']."' 
					where
						payment_header_id='".$aPW['payment_header_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage().$q);				
		

			$c=0;
			foreach ($aPWD as $temp)
			{
				if (in_array($c+1,$aPW['aConfirm']))
				{
					$mconfirm = 'true';
				}
				else
				{
					$mconfirm = 'false';
				}
				
				if ($temp['payment_detail_id'] == '')
				{
					$q = "insert into payment_detail (payment_header_id, account_id,releasing_id, amount, withdrawn, excess, remark, mconfirm)
								values ('".$aPW['payment_header_id']."', '".$temp['account_id']."', 
										'".$temp['releasing_id']."', '".$temp['amount']."','".$temp['withdrawn']."',
										'".$temp['excess']."', '".$temp['remark']."', '$mconfirm')";
										
					$qr = @pg_query($q) or message(pg_errormessage().$q);
					if (@pg_affected_rows($qr) == 0 or !$qr)
					{
						rollback();
						message('Unable to save to collection detail...'.$q);
						break;
						$ok = false;
					}
					else
					{
						$qr = query("select currval('payment_detail_payment_detail_id_seq'::text)");
						$r = pg_fetch_object($qr);
						$dummy['payment_detail_id']=$r->currval;
					
				//		$q = "insert into ledger (account_id, releasing_id,  date, reference, type, credit)
				//				values
				//					('".$temp['account_id']."','".$temp['releasing_id']."','".$aPW['date_withdrawn']."',
				//						'".$aPW['payment_header_id']."','C','".$temp['amount']."')";
				//		$qr = @pg_query($q) or message(pg_errormessage());
						if (!$qr && @pg_affected_rows($qr)<=0)
						{
							rollback();
							message('Unable to save to collection detail...ledger update');
							$ok=false;
							break;
						}
						else
						{
					//		$qr = query("select currval('ledger_id_seq'::text)");
					//		$r = pg_fetch_object($qr);
					//		$dummy['ledger_id']=$r->currval;
							$dummy['payment_detail_id']=$r->currval;
							$aPWD[$c] = $dummy;
						}
	
					}
				}
				else
				{
							
					if ($temp['record_status'] == 'deleted' && $temp['payment_detail_id'] != '')
					{
						$q = "delete from payment_detail where payment_detail_id='".$temp['payment_detail_id']."'";
						@pg_query($q) or message(pg_errormessage().$q);
	
						$q = "delete from ledger 
								where 
									account_id ='".$temp['account_id']."' and 
									releasing_id = '".$temp['releasing_id']."' and  
									reference = '".$aPW['payment_header_id']."' and
									type = 'C' and
									credit = '".$temp['amount']."'";
						@pg_query($q) or message(pg_errormessage().$q);
						continue;
					}
					$q = "update payment_detail set
								account_id = '".$temp['account_id']."',
								amount = '".$temp['amount']."',
								withdrawn = '".$temp['withdrawn']."',
								excess = '".$temp['excess']."',
								mconfirm = '$mconfirm'
							where
								payment_detail_id='".$temp['payment_detail_id']."'";
					$qr = @pg_query($q) or message(pg_errormessage().$q);
								
				}
			/*	if ($temp['ledger_id'] == '')
				{
					$q = "insert into ledger (account_id,  releasing_id, date, reference, type, credit)
							values
								('".$temp['account_id']."','".$temp['releasing_id']."','".$aPW['date_withdrawn']."',
									'".$aPW['payment_header_id']."','C','".$temp['amount']."')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);
					if (!$qr && @pg_affected_rows($qr)<=0)
					{
						$ok=false;
						break;
					}
					else
					{
						$qr = query("select currval('ledger_id_seq'::text)");
						$r = pg_fetch_object($qr);
						$dummy['ledger_id']=$r->currval;
					}
				}
				else
				{
					$q = "update ledger set 
								account_id='".$temp['account_id']."',
								date = '".$aPW['date_withdrawn']."',
								releasing_id='".$temp['releasing_id']."',
								credit = '".$temp['amount']."'
							where
								ledger_id='".$temp['ledger_id']."'";
					$qr = @pg_query($q) or message(pg_errormessage());

					if (!$qr && @pg_affected_rows($qr)<=0)
					{
						$ok=false;
						break;
					}
				}
				*/
				if ($temp['releasing_id'] > 0)
				{
					updateReleasing($temp['releasing_id']);
				}	

				$c++;
			}
	}
	if ($ok)
	{
		message("Withdrawal Entry Saved...");
		$aPW['status'] = 'S';
	}
}
elseif ($p1 == 'Delete Checked' && count($aChk)>0)
{
	$newArray=null;
	$newArray=array();
	$nctr=0;
	foreach ($aPWD as $temp)
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
	$aPWD = $newArray;	
}
elseif ($p1 == 'Restore' && count($aChk)>0)
{
	$newArray=null;
	$newArray=array();
	$nctr=0;
	foreach ($aPWD as $temp)
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
	$aPWD = $newArray;	
}
elseif ($p1 == 'Print' && !in_array($aPW['status'], array('S','P')))
{
	message("Cannot Print.  Please Save Transaction First...");
}
elseif ($p1 == 'Print')
{
	$header = "<small3>";
	$header .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$header .= center($SYSCONF['BUSINESS_ADDR'],120)."\n";
	$headers .= center('DAILY WITHDRAWAL REPORT',120)."\n";
	$header .= center('Withdrawal Date '.ymd2mdy($aPW['date_withdrawn']).' Encoded '.ymd2mdy($aPW['date']),120)."\n";
	$header .= center('Date Printed '.date('m/d/Y g:ia'),120)."\n\n";
	$header .= str_repeat('-',120)."\n";
	$header .= "      Name                          Bank                 Account No.        Card No.        Amount  Date \n";
	$header .= str_repeat('-',120)."\n";
	$lc = 10;
	
	$details ='';
	
	$q = "select * from payment_detail, account, clientbank 
			where 
				account.account_id=payment_detail.account_id and
				clientbank.clientbank_id=account.clientbank_id and 
				payment_header_id='".$aPW['payment_header_id']."'
			order by
				account.branch_id, trim(clientbank), account";

	$qr = @pg_query($q) or message(pg_errormessage());
	$cc=0;
	$branch_id = $clientbank = '';
	
	while ($r = @pg_fetch_object($qr))
	{
		if ($branch_id != $r->branch_id)
		{
			if ($branch_id != '')
			{
				//echo "<pre>$header$details</pre>";
				$details .= "<eject>";
				doPrint($header.$details);
				$details ='';
				$lc = 10;
			}
			$details .= strtoupper(lookupTableReturnValue('x','branch','branch_id','branch',$r->branch_id))."\n";
			$details .= str_repeat('-',20)."\n";
			$lc += 2;
			$branch_id = $r->branch_id;
		}
		
		if (trim($clientbank) != trim($r->clientbank))
		{
			$details .= strtoupper($r->clientbank)."\n";
			$lc++;
			$clientbank = $r->clientbank;
		}
		$cc++;
		$details .= adjustRight($cc,3).'. '.
					adjustSize($r->account,30).' '.
					adjustSize(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id),20).' '.
					adjustSize($r->bank_account,15).' '.
					adjustSize($r->cardno,15).' '.
					adjustRight(ymd2mdy($aPW['date_withdrawn']),10).' '.
					adjustRight(number_format($r->withdrawn,2),10).' '.
					($r->mconfirm ? '[X]' : '[ ]')."\n";
					
					
		$total_amount += $r->withdrawn;			
					
	}	
	
	$details .= str_repeat('-',120)."\n";
	$details .= space(30)." TOTAL AMOUNT ".space(45).adjustRight(number_format($total_amount,2),10)."\n";
	$details .= str_repeat('-',120)."\n";
	$details .= "<eject>";
	
	$details .= "<eject>";
	doPrint($header.$details);
	//echo "<pre>$header$details</pre>";

}
?>
<form name="f1" method="post" action="">
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr> 
      <td height="21" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>&nbsp;<img src="../graphics/list3.gif" width="16" height="16">Collection 
        Entry by Passbook Withdrawal</strong></font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="7%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        Encoded </font></td>
      <td width="21%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aPW['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        </font></td>
      <td width="11%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        Withdrawn</font></td>
      <td width="61%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date_withdrawn" type="text" id="date_withdrawn" value="<?= ymd2mdy($aPW['date_withdrawn']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_withdrawn, 'mm/dd/yyyy')"> 
        </font></td>
    </tr>
  </table>  
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td colspan="7" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Account 
        Details</strong></font></td>
    </tr>
    <tr> 
      <td width="11%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        <br></font>
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" size="10">
        <input name="p1" type="submit" id="p1" value="Search"> </td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Account</font>
        <input name="account_id" type="hidden" id="account_id" value="<?= $iPWD['account_id'];?>" size="4">
        <br>
        <input name="account" type="text" id="account" value="<?= ($iPWD['account_id'] != ''  ? lookUpTableReturnValue('x','account','account_id','account',$iPWD['account_id']): '');?>" size="25" readOnly>
        </td>
      <td width="8%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Withdrawn<br>
        <input name="withdrawn" type="text" id="withdrawn2" value="<?= $iPWD['withdrawn'];?>" size="9" onBlur="vAmt()">
        </font></td>
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ammort 
        <br>
        <input name="amount" type="text" id="amount" value="<?= $iPWD['amount'];?>" size="9" onBlur="vAmt()">
        </font></td>
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Excess 
        <br>
        <input name="excess" type="text" id="excess4" value="<?= $iPWD['excess'];?>" size="9" onBlur="vAmt()">
        </font></td>
      <td width="4%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remark<br>
        <input name="remark" type="text" id="remark" value="<?= $iPWD['remark'];?>" size="25" onBlur="vAmt()">
        </font></td>
      <td width="65%" valign="bottom"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="p1" type="submit" id="p1" value="Ok">
        </font></td>
    </tr>
  </table>
  <?
  if ($p1 == 'Search')
  {
  ?>
  <br>
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#FFCCFF"> 
      <td width="3%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="44%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Select 
        Account </font></strong></td>
      <td width="13%" align="center" bgcolor="#FFCCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></strong></td>
      <td width="14%" align="center" bgcolor="#FFCCFF"><strong></strong></td>
      <td width="14%" align="center" bgcolor="#FFCCFF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pension</font></strong></td>
    </tr>
    <?
	
		$q = "select 
					*
				from
					account
				where 
					account.enable and 
					account ilike '$xSearch%'
				order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		$ctr=0;
		$maccount_id=0;
		while ($r = @pg_fetch_object($qr))
		{
				$branch = '';
				$clientbank = '';
				if ($r->branch_id > 0)
				{
					$branch = lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id);
				}
				if ($r->clientbank_id > 0)
				{
					$clientbank = lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id);
				}
				
				$account = $r->account;
				$ctr++;
				$cc= $ctr.'.';
				?>
    <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" onClick="window.location='?p=payment.entry&p1=selectReleaseId&id=<?= $r->releasing_id;?>'"> 
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=$cc;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  <a href='?p=payment.withdraw&p1=selectAccount&id=<?= $r->account_id;?>'> 
        <?= $account;?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  <a href='?p=payment.withdraw&p1=selectAccount&id=<?= $r->account_id;?>'> 
        <?= $branch;?>
		</a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=$clientbank;?>
        </font></td>
      <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?=number_format($r->salary,2);?>
        &nbsp; </font></td>
    </tr>
    <?
		}
		?>
  </table>
  <br><br>
  <?
  }
  ?>
  
  <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr> 
      <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Accounts</font></strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remark</font></strong></td>
      <td width="12%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Withdrawn</strong></font></td>
      <td width="12%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Ammort</strong></font></td>
      <td width="12%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Excess</strong></font></td>
      <td width="6%" align="center">&nbsp;</td>
    </tr>
    <?
	$ctr=0;
	$aPW['total_amount'] = $aPW['total_withdrawn'] = $aPW['total_excess'] = 0;
	foreach ($aPWD as $temp)
	{
		if ($temp['record_status'] == 'deleted') continue;
		$aPW['total_withdrawn'] += $temp['withdrawn'];
		$aPW['total_amount'] += $temp['amount'];
		$aPW['total_excess'] += $temp['excess'];
		$ctr++;
	?>
    <tr bgcolor="#FFFFFF"> 
      <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $ctr;?>
        . 
        <input name="aChk[]" type="checkbox" id="aChk[]" value="<?= $ctr;?>">
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href='?p=payment.withdraw&p1=Edit&ctr=<?=$ctr;?>'> 
        <?= $temp['account'];?>
        </a> </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?=$temp['remark'];?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($temp['withdrawn'],2);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        <?=number_format($temp['amount'],2);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($temp['excess'],2);?>
        </font></td>
      <td><input type="checkbox" name="aConfirm[]" value="<?= $ctr;?>"  <?=(in_array($ctr,$aPW['aConfirm']) ? 'checked' : '');?> <?=(!chkRights2('confirmpayment','madd',$ADMIN['admin_id']) ? 'disabled' : '');?>></td>
    </tr>
    <?
	}
	
	?>
    <tr> 
      <td colspan="2"><strong> 
        <input name="p1" type="submit" id="p1" value="Delete Checked">
        </strong></td>
      <td align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPW['total_withdrawn'],2);?>
        </font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPW['total_amount'],2);?>
        </font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aPW['total_excess'],2);?>
        </font></strong></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="7" bgcolor="#FFFFFF"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> 
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            <td nowrap width="25%"> <img name="New" id="Browse" onClick="window.location='?p=payment.browse&p1=Browse'"  src="../graphics/browse.GIF" alt="Browse Payments Made" width="67" height="17"> 
            </td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
