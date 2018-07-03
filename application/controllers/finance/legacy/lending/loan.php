<SCRIPT language=Javascript><!--
function vRequestOverride()
{
	var max_term = 1*(document.getElementById('max_term').value);
	var term = 1*(document.getElementById('term').value);
	
	value =prompt('Request OVerride Term To ('+max_term+')','');
	if (max_term <= 0)
	{
		max_term=0;
	}

	if (value > max_term)
	{
		xajax_loanoverride(xajax.getFormValues('f1'), value)
	}
	else
	{
		alert('Override is NOT required... Maximum Term is still '+max_term)
	}
	return false;
	
}
function checkTerm(rights)
{
	var max_term = parseFloat(document.getElementById('max_term').value);
	var term = parseFloat(document.getElementById('term').value); 
	
	if (rights!=1)
	{
		
		//-- if cannot override
		if (term>max_term)
		{
			alert("Term specified ("+term+") is above maximum ("+max_term+") term allowed...\n   Please ask for approval");
			document.getElementById('term').value = '';
		}
	}
}
function vNumber(id)
{
	var num = document.getElementById(id).value;
	var numname = document.getElementById(id).name;
	
	if (num == '') return;
	var anum=/(^\d+$)|(^\d+\.\d+$)/
	if (!anum.test(num))
	{
		if (num != '')
		{
	   		alert('Enter a Valid Number for '+numname);
	   	}	
		document.getElementById(id).value = 0;
		document.getElementById(id).focus();
	}
	return false;
}

function vUnPost()
{
	if (confirm('Are you sure to UnPost This Transaction ?')==true)
	{
		window.location='?p=loan&p1=UnPost'
	}
}
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Relasing Form?"))
		{
			document.getElementById('f1').action="?p=loan&p1=CancelConfirm"
		}	
		else
		{
			document.getElementById('f1').action="?p=loan&p1=Cancel"
			return false;
		}
	}
	else
	{
		document.getElementById('f1').action="?p=loan&p1="+ul.id;
	}	
	document.getElementById('f1').submit();
}
//-->	
</script>
<?
if (!chkRights3('releasing','mview',$ADMIN['admin_id']))
{
	message("You have no permission to View Loan Releasing...");
	exit;
}
/*
if (!session_is_registered('aLoan'))
{
	session_register('aLoan');
	$aLoan=null;
	$aLoan=array();
}
*/
if (!isset($_SESSION['aLoan'])) { 
   $_SESSION['aLoan'] = array(); 
} 

if ($p1 == 'New' && $aid == '')
{	
	$aLoan=null;
	$aLoan=array();
	echo "<script>window.location='?p=loan.new'</script>";
	exit;
}
elseif ($p1 == 'Renew' && $rid == '')
{	
	$aLoan=null;
	$aLoan=array();
	echo "<script>window.location='?p=loan.new'</script>";
	exit;
}
elseif ($p1 == 'Renew' && $rid!='')
{
	$q = "select 
				releasing.account_id, 
				account, 
				account_group_id,
				account.salary,
				account_status, 
				releasing.balance, 
				releasing.releasing_id as renew_releasing_id
			from 
				account, 
				releasing 
			where 
				account.account_id=releasing.account_id and
				releasing.releasing_id='$rid'";
	$r=fetch_assoc($q);
	if ($r['account_status'] != 'A')
	{
		message('Account of '.$r['account'].'<br>Account Status is not active [ '.status($r['account_status']).' ]');
		exit;
	}
	$aLoan=null;
	$aLoan=array();
	$aLoan=$r;

	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
	
	$aLoan['date'] = date('Y-m-d');
	$aLoan['edate'] = date('Y-m-d');
	$aLoan['audit'] = '';
	$aLoan['addon'] = 'R';
	
	$aLoan['previous_balance'] = $aLoan['balance'];
	$aLoan['arid'] = $rid;
	message('Previous Balance P '.number_format($aLoan['balance'],2));
}
elseif ($p1 == 'RenewChecked'  && count($restructure) == 0)
{
	$aLoan=null;
	$aLoan=array();
	echo "<script>window.location='?p=loan.new'</script>";
	exit;
}
elseif ($p1 == 'RestructureChecked'  )
{
	$aLoan=null;
	$aLoan=array();

	$renewadvancechange = 0;
	$restructure = $_REQUEST['restructure'];
	$arid=null;
	for ($c=0;$c<count($restructure);$c++)
	{
		if ($restructure[$c] == 'advancechange')
		{
			$renewadvancechange = 1;
			$advancechange = str_replace(',','',$_REQUEST['advancechange']);
		}
		else
		{
			if (strlen($arid)>0) $arid .= ',';
			$arid .= "'".$restructure[$c]."'";
		}
	}

	$q = "select 
				sum(releasing.balance) as balance, account_id
			from 
				releasing 
			where 
				releasing.releasing_id in ($arid)
			group by 
				account_id";
				
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r=@pg_fetch_assoc($qr);
	$previous_balance = $r['balance'];
	
	$q = "select * from account where account_id = '".$r['account_id']."'";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$rr=@pg_fetch_assoc($qr);
	if ($rr['account_status'] != 'A')
	{
		message('Account of '.$r['account'].'<br>Account Status is not active [ '.status($r['account_status']).' ]');
		exit;
	}
	$aLoan=$rr;
	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
	
	$aLoan['date'] = date('Y-m-d');
	$aLoan['edate'] = date('Y-m-d');
	$aLoan['audit'] = '';
	$aLoan['addon'] = 'R';
	
	$aLoan['previous_balance'] = $previous_balance;
	$aLoan['arid'] = $arid;	
	if ($renewadvancechange == 1)
	{
		$aLoan['advance_change'] = $advancechange;
	}
	$age = age($aLoan['date_birth']);
	$age = $aLoan['age'];
	
	message('Previous Balance P '.number_format($aLoan['previous_balance'],2).'  Advance Change '.number_format($advancechange,2));
}
elseif ($p1 == 'New')
{
	$q = "select * from account where account_id='$aid'";
	$r=fetch_assoc($q);
	
	if ($r['account_status'] != 'A')
	{
		message('Account of '.$r['account'].'<br>Account Status is not active [ '.status($r['account_status']).' ]');
		exit;
	}
	$aLoan=null;
	$aLoan=array();
	$aLoan=$r;
	$aLoan['remarks']='';
	
	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
	
	$aLoan['date'] = date('Y-m-d');
	$aLoan['edate'] = date('Y-m-d');
	$aLoan['audit'] = '';
	
	$account_balance = accountBalance($aLoan['account_id']);
	if ($account_balance > 0 && $add!='1')
	{
		$aLoan['previous_balance'] = $account_balance;
		message('Previous Balance P '.number_format($account_balance,2));
	}
	elseif ($account_balance > 0 && $add=='1')
	{
		message('Old Account Maintained P '.number_format($account_balance,2));
	}	
	
	$age = age($aLoan['date_birth']);
	$age = $aLoan['age'];
}
elseif ($p1 == 'swaudit')
{
	if ($aLoan['show_audit'] == '1')
	{
		$aLoan['show_audit'] = 0;
	}
	else
	{
		$aLoan['show_audit'] = 1;
	}
}

$fields = array('mode','rate','term','loan_type_id','date','principal',
		'advance_payment','advance_applied','ca_balance','previous_balance','redeem',
		'service_charge','collection_fee','insurance','interest', 
		'printout', 'photo', 'atm_charge', 'referral_fee',
		'advance_change', 'other_charges', 'other_remarks',
		'vat','gross','released','ammort','mclass','date_child21',
		'comaker1','comaker2', 'comaker1_address','comaker2_address',
		'npension','nchangebank','max_term','age','withdraw_day');
		
if (!in_array($p1,array(null,'New','Renew','Load','UnPost','RenewChecked','RestructureChecked')))
{
	$aLoan['date_birth'] = mdy2ymd($_REQUEST['date_birth']);
	
	for ($c=0;$c<count($fields);$c++)
	{
		if ($fields[$c] == 'date' or $fields[$c] == 'advance_applied' or substr($fields[$c],0,4)=='date')
		{
			$aLoan[$fields[$c]] = mdy2ymd($_REQUEST[$fields[$c]]);
		}
		else
		{
			$aLoan[$fields[$c]] = $_REQUEST[$fields[$c]];
			if ($aLoan[$fields[$c]] == '' && !in_array($fields[$c],array('other_remarks','comaker1','comaker2','comaker1_address','comaker2_address')))
			{
				$aLoan[$fields[$c]] = 0;
			}
			else
			{
				$aLoan[$fields[$c]] = str_replace(',','',$aLoan[$fields[$c]]);
			}
		}	
	}
	
	$aLoan['salary'] = str_replace(',','',$aLoan['salary']);

	if ($aLoan['date'] == '--' or $aLoan['date'] == '')
	{
		$aLoan['date'] = date('Y-m-d');
	}
	$aLoan['rate_basis'] = $_REQUEST['rate_basis'];
	if ($aLoan['renew_releasing_id'] == '') $aLoan['renew_releasing_id'] = 0;
	if ($aLoan['advance_applied'] == '' || $aLoan['advance_applied'] == '//' || $aLoan['advance_applied'] == '--' || $aLoan['advance_applied']=='0000-00-00' || $aLoan['advance_applied']=='00/00/0000')
	{
		$aLoan['advance_applied'] = NULL;
	}
if ($ADMIN[admin_id]==1) print_r($aLoan);
}		

if ($p1 == 'Load')
{
	$aLoan=null;
	$aLoan=array();
	
	$q = "select 
				account.account,
				account.account_group_id,
				account.bank_account,
				account.salary,
				account.pix,
				account.date_birth,
				loan_type.basis as rate_basis, 
				releasing.account_id,
				releasing.releasing_id,
				releasing.date,
				releasing.edate,
				releasing.admin_id,
				releasing.postedby,
				releasing.loan_type_id,
				releasing.rate,
				releasing.term,
				releasing.principal,
				releasing.interest,
				releasing.redeem,
				releasing.printout,
				releasing.advance_payment,
				releasing.previous_balance,
				releasing.service_charge,
				releasing.collection_fee,
				releasing.insurance,
				releasing.gross,
				releasing.released,
				releasing.audit,
				releasing.ammort,
				releasing.referral_fee,
				releasing.ca_balance,
				releasing.age,
				releasing.comaker1,
				releasing.comaker1_address,
				releasing.comaker2,
				releasing.comaker2_address,
				releasing.mclass,
				releasing.date_child21,
				releasing.npension,
				releasing.nchangebank,
				releasing.status,
				releasing.renew_releasing_id,
				releasing.mode,
				releasing.atm_charge,
				releasing.withdraw_day
			from 
				releasing, account, loan_type 
			where
				account.account_id=releasing.account_id and
				loan_type.loan_type_id=releasing.loan_type_id and
				releasing_id='$id'";
	$r = fetch_assoc($q);
	$aLoan=$r;
	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;

}
elseif ($p1 == 'Save' && !chkRights3('releasing','medit',$ADMIN['admin_id']) && $aLoan['releasing_id']!='')
{
	message("You have no permission to edit/update Loan Releasing...");
	exit;
}
elseif ($p1 == 'Save'  && (strlen($aLoan['comaker1'])<1 || strlen($aLoan['comaker2'])<1)) //&& !chkRights3('override','madd',$ADMIN['admin_id'])
{
	message("Lacking Co-Maker. Please complete information...");
}
elseif ($p1 == 'Save'  && ($aLoan['ammort']>$aLoan['salary'] )) 
{
	message("Ammortization Amount (P ".number_format($aLoan['ammort'],2).") is greater than Salary of (P ".number_format($aLoan['salary'],2).")");
}
elseif ($p1 == 'Save' && ($aLoan['principal']==0 ||
	  $aLoan['ammort']==0 ||  $aLoan['loan_type_id']==0))
{
	message("Cannot Save. Lacking Important Data, Please Check...(Loan Type, Rate, Principal, Ammortization..)");
}
elseif ($p1 == 'Save')
{
	if ($aLoan['releasing_id']=='')
	{
		if ($aLoan['edate'] == '') $aLoan['edate'] = date('Y-m-d');
		begin();
		$aLoan['audit'] ='Created by:'.$ADMIN['name'].' on '.date('m/d/Y g:ia');
		if ($aLoan['advance_applied']=='')
		{
			$q = "insert into releasing (account_id, loan_type_id, mode, term, max_term, rate, edate, date,
					principal,  advance_payment , ca_balance, previous_balance, interest, service_charge, collection_fee,
					printout, photo, atm_charge, advance_change, other_charges, other_remarks, referral_fee,
					insurance, vat, gross, released, ammort, admin_id, status, renew_releasing_id, 
					mclass, date_child21,npension, nchangebank, age, comaker1, comaker2,  
					comaker1_address, comaker2_address,redeem,withdraw_day,
					audit, enable)
				values
					('".$aLoan['account_id']."','".$aLoan['loan_type_id']."','".$aLoan['mode']."',
					'".$aLoan['term']."','".$aLoan['max_term']."','".$aLoan['rate']."','".$aLoan['edate']."','".$aLoan['date']."',
					'".$aLoan['principal']."','".$aLoan['advance_payment']."',
					'".$aLoan['ca_balance']."','".$aLoan['previous_balance']."','".$aLoan['interest']."',
					'".$aLoan['service_charge']."','".$aLoan['collection_fee']."',
					'".$aLoan['printout']."','".$aLoan['photo']."','".$aLoan['atm_charge']."','".$aLoan['advance_change']."',
					'".$aLoan['other_charges']."','".$aLoan['other_remarks']."','".$aLoan['referral_fee']."',
					'".$aLoan['insurance']."','".$aLoan['vat']."','".$aLoan['gross']."',
					'".$aLoan['released']."','".$aLoan['ammort']."',
					'".$ADMIN['admin_id']."','S', '".$aLoan['renew_releasing_id']."', 
					'".$aLoan['mclass']."','".$aLoan['date_child21']."', '".$aLoan['npension']."',
					'".$aLoan['nchangebank']."', '".$aLoan['age']."', 
					'".$aLoan['comaker1']."', '".$aLoan['comaker2']."', '".$aLoan['comaker1_address']."', 
					'".$aLoan['comaker2_address']."','".$aLoan['redeem']."','".$aLoan['withdraw_day']."',
					'".$aLoan['audit']."','t')";
		}
		else
		{
			$q = "insert into releasing (account_id, loan_type_id, mode, term, max_term, rate, edate, date,
					principal,  advance_payment , advance_applied, ca_balance, previous_balance, interest, service_charge,
					collection_fee,	printout, photo, atm_charge, advance_change, other_charges, other_remarks,referral_fee
					insurance, vat, gross, released, ammort, admin_id, status, renew_releasing_id, 
					mclass, date_child21,npension, nchangebank, age, comaker1, comaker2,  comaker1_address, comaker2_address, 
					redeem,withdraw_day,audit, enable)
				values
					('".$aLoan['account_id']."','".$aLoan['loan_type_id']."','".$aLoan['mode']."',
					'".$aLoan['term']."','".$aLoan['max_term']."','".$aLoan['rate']."','".$aLoan['edate']."','".$aLoan['date']."',
					'".$aLoan['principal']."','".$aLoan['advance_payment']."','".$aLoan['advance_applied']."',
					'".$aLoan['ca_balance']."','".$aLoan['previous_balance']."','".$aLoan['interest']."',
					'".$aLoan['service_charge']."','".$aLoan['collection_fee']."',
					'".$aLoan['printout']."','".$aLoan['photo']."','".$aLoan['atm_charge']."','".$aLoan['advance_change']."',
					'".$aLoan['other_charges']."','".$aLoan['other_remarks']."','".$aLoan['referral_fee']."',
					'".$aLoan['insurance']."','".$aLoan['vat']."','".$aLoan['gross']."',
					'".$aLoan['released']."','".$aLoan['ammort']."',
					'".$ADMIN['admin_id']."','S', '".$aLoan['renew_releasing_id']."', 
					'".$aLoan['mclass']."','".$aLoan['date_child21']."', '".$aLoan['npension']."',
					'".$aLoan['nchangebank']."', '".$aLoan['age']."', 
					'".$aLoan['comaker1']."', '".$aLoan['comaker2']."', '".$aLoan['comaker1_address']."',
					'".$aLoan['comaker2_address']."',".$aLoan['redeem']."',".$aLoan['withdraw_day']."',
					'".$aLoan['audit']."','t')";
		}			
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		$credit = 0;
		$credit = $aLoan['advance_payment'];
		if ($qr && pg_affected_rows($qr)>0)
		{
			$q = "select currval('releasing_releasing_id_seq')" ;
			$r = fetch_object($q);
			$aLoan['releasing_id'] = $r->currval;


			$q = "update account set date_birth = '".$aLoan['date_birth']."', age='".$aLoan['age']."', 
							mclass='".$aLoan['mclass']."', date_child21='".$aLoan['date_child21']."',
							npension='".$aLoan['npension']."',  nchangebank ='".$aLoan['nchangebank']."'
						where
							account_id ='".$aLoan['account_id']."'";
			$qr = @pg_query($q) or message('Error Updating Account Info....'.pg_errormessage().$q);
			
			
			$q ="insert into ledger (account_id, releasing_id, reference, date,type,debit,credit,remarks)
					values ('".$aLoan['account_id']."','".$aLoan['releasing_id']."','".$aLoan['releasing_id']."',
							'".$aLoan['date']."','D','".$aLoan['gross']."','$credit',
							'".$aLoan['remarks']."')";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			
			if ($qr && pg_affected_rows($qr)>0)
			{
				recalculate($aLoan['releasing_id'],'noneform');
				commit();
				$maction = 'saved';
				$message = 'Loan Application Saved';
				$q = "select currval('ledger_ledger_id_seq')" ;
				$r = fetch_object($q);
				$aLoan['ledger_id'] = $r->currval;
				$aLoan['status']='S';
			}
			else
			{
				rollback();
				$aLoan['releasing_id'] = '';
				$maction = 'errorsave';
				$message = 'Cannot Save, Error Occured in Ledger Table '.pg_errormessage();
			}
		}	
		else
		{
			rollback();
			$maction = 'errorsave';
			$message = 'Cannot Save, Error Occured in Releasing Table '.pg_errormessage();
		}
	}
	else
	{
		begin();
		$aLoan['audit'] .=';Updated by:'.$ADMIN['name'].' on '.date('m/d/Y g:ia');
		$q = "update releasing set audit = '".$aLoan['audit']."', status='S'";
		for ($c=0;$c<count($fields);$c++)
		{
			if ($fields[$c] == 'advance_applied' && $aLoan[$fields[$c]]=='')
			{
				continue;
			}
			else
			{
				$q .= ",".$fields[$c]."='".$aLoan[$fields[$c]]."'";
			}	
		}
		$q .= " where releasing_id='".$aLoan['releasing_id']."'";
	
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		if ($qr && pg_affected_rows($qr))
		{
			$credit = $aLoan['advance_payment'];	
		
			$q = "update ledger set
						date='".$aLoan['date']."',
						account_id='".$aLoan['account_id']."',
						reference='".$aLoan['releasing_id']."',
						debit='".$aLoan['gross']."',
						credit='$credit',
						status='S'";
			$q .= " where reference='".$aLoan['releasing_id']."' and
						type='D'";
						
			$qr = @pg_query($q) or message (pg_errormessage().$q);

			if ($qr && pg_affected_rows($qr)>0)
			{
				commit();
				$aLoan['status']='S';
			}
			elseif (pg_affected_rows($qr) == 0)
			{
				$q ="insert into ledger (account_id, reference, date,type,debit,credit,remarks)
						values ('".$aLoan['account_id']."','".$aLoan['releasing_id']."',
								'".$aLoan['date']."','D','".$aLoan['gross']."','0',
								'".$aLoan['remarks']."')";
				$qr = @pg_query($q) or message(pg_errormessage());
			}
			if ($qr && pg_affected_rows($qr)>0)
			{
				commit();
				recalculate($aLoan['releasing_id'],'noneform');

				$maction = 'updated';
				$message = "Loan Application updated...";
				$aLoan['status']='S';
			}
			elseif (!$qr)
			{
				rollback();
				$maction='errorsave';
				$message = "NOT able to update Ledger. Error occured <br>".$q;
			}	
		}
		else
		{
			rollback();
			$maction='errorsave';
			$message = "NOT able to update transaction. Error occured <br>".$q;
		}
		
	}
	
	if ($aLoan['releasing_id'] != '' && $aLoan['arid'] != '')
	{
			$arestructure = explode (',',$aLoan['arid']);
			$previous_balance = $aLoan['previous_balance'];	
			for ($c=0;$c<0;$c++)
			{
				//update renewal
				
				$rid = $arestructure[$c];
				if ($rid == '') continue;
				if ($previous_balance <= 0) break;
				
				$q = "select  balance from releasing where releasing_id = '$rid'";
				$qr = @pg_query($q) or message(pg_errormessage().$q);
				$r = @pg_fetch_object($qr);
				
				if ($previous_balance>= $r->balance)
				{
					$previous_balance -= $r->balance;
					$credit_applied = $r->balance;
					$balance=0;
				}	
				else
				{
					$balance = $r->balance - $previous_balance;
					$credit_applied = $previous_balance;
					$previous_balance = 0;
				}
				$q = "select * 
							from 
								ledger 
							where 
								releasing_id='$rid' and
								reference='".$aLoan['releasing_id']."' and 
								type in ('R','C')
							order by type desc
							offset 0 limit 1";
				$qr = @pg_query($q) or message(pg_errormessage());
				if (pg_num_rows($qr) > 0)
				{
					$rr = @pg_fetch_object($qr);
					$q = "update ledger set credit='$credit_applied'
							 where 
							 		ledger_id ='$rr->ledger_id'";
									
					$qr = @pg_query($q) or message(pg_errormessage());				

					recalculate($rid,'noneform');
				}
				else
				{
					$q ="insert into ledger (account_id, releasing_id, reference, date,type,debit,credit)
							values ('".$aLoan['account_id']."','$rid',
									'".$aLoan['releasing_id']."',
									'".$aLoan['date']."','R','0','$credit_applied')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);
					recalculate($rid,'noneform');
				}	
			}

	}
}
elseif ($p1 == 'UnPost')
{	
	$audit = $aLoan['audit'].'UnPosted by:'.$ADMIN['username'].' '.date('m/d/Y g:ia').';';
	$q = "update releasing set status='U', audit='$audit' where releasing_id='".$aLoan['releasing_id']."'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if ($qr && pg_affected_rows($qr)>0)
	{
		$message = 'Transaction is UnPosted';
		$aLoan['audit'] = $audit;
		$aLoan['status']='U';
	}
	
}
elseif ($p1 == 'CancelConfirm')
{	
	$audit = $aLoan['audit'].'Cancelled by:'.$ADMIN['username'].' '.date('m/d/Y g:ia').';';
	$q = "update releasing set status='C', audit='$audit' where releasing_id='".$aLoan['releasing_id']."'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if ($qr && pg_affected_rows($qr)>0)
	{
		$q = "update ledger set status='C' where 
				releasing_id='".$aLoan['releasing_id']."' and
				account_id='".$aLoan['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());

/*			$q ="insert into ledger (account_id, releasing_id, reference, date,type,debit,credit,remarks)
					values ('".$aLoan['account_id']."','".$aLoan['releasing_id']."','".$aLoan['releasing_id']."',
							'".$aLoan['date']."','D','".$aLoan['gross']."','$credit',
							'".$aLoan['remarks']."')";
*/
		$message = 'Loan Releasing Form Cancelled';
		$aLoan['audit'] = $audit;
		$aLoan['status']='C';
	}
	
}
elseif ($p1 == 'Print' && $aLoan['releasing_id'] == '' ) //&& in_array($aLoan['status'],array('N','M','')))
{
	$message = "Please save transaction before printing...";
}
elseif ($p1 == 'Print')
{
	//make sure only saved info will be printed
	$q = "select 
				account.account,
				account.account_group_id,
				account.bank_account,
				account.salary,
				account.pix,
				account.date_birth,
				loan_type.basis as rate_basis, 
				releasing.account_id,
				releasing.releasing_id,
				releasing.date,
				releasing.edate,
				releasing.admin_id,
				releasing.postedby,
				releasing.loan_type_id,
				releasing.rate,
				releasing.term,
				releasing.principal,
				releasing.interest,
				releasing.advance_payment,
				releasing.previous_balance,
				releasing.service_charge,
				releasing.collection_fee,
				releasing.insurance,
				releasing.gross,
				releasing.released,
				releasing.ca_balance,
				releasing.referral_fee, 
				releasing.audit,
				releasing.photo,
				releasing.printout,
				releasing.atm_charge,
				releasing.advance_change,
				releasing.other_charges,
				releasing.other_remarks,
				releasing.ammort,
				releasing.redeem,
				releasing.status,
				releasing.renew_releasing_id,
				releasing.mode,
				releasing.comaker1,
				releasing.comaker2,
				releasing.comaker1_address,
				releasing.comaker2_address,
				loan_type.basis as rate_basis
				
			from 
				releasing, account, loan_type 
			where
				account.account_id=releasing.account_id and
				loan_type.loan_type_id=releasing.loan_type_id and
				releasing_id='".$aLoan['releasing_id']."'";
	$r = fetch_assoc($q);

	$aLoan=null;
	$aLoan=array();
	
	$aLoan=$r;
	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;


	$details .= $SYSCONF['BUSINESS_NAME']."\n".	
				$SYSCONF['BUSINESS_ADDR'].'    Tel. No.: '.$SYSCONF['BUSINESS_TEL']."\n";
	$details .= space(50).'LOAN RELEASE No. '.str_pad($aLoan['releasing_id'],8,'0',str_pad_left)."\n";
	$details .= str_repeat('=',76)."\n";
	$details .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aLoan['account'])),55).'   '.
				adjustSize(lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type_code',$aLoan['loan_type_id']),15)."\n";
	$details .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLoan['account_group_id']),55).'   '.
				adjustSize("Date:".ymd2mdy($aLoan['date']),15)."\n";
	$details .= str_repeat('=',76)."\n\n";
	
	$details .= adjustSize("Principal Loan",45).
				space(18).
				adjustRight(number_format($aLoan['principal'],2),12)."\n";
				
	$details .= adjustSize(space(10)."Less : Service Charge..........",45).'  '.
                                adjustRight(number_format($aLoan['service_charge']+$aLoan['collection_fee'],2),12)."\n";

	if ($aLoan['advance_change'] > 0)
	{
		$details .= adjustSize(space(10)."       Advance Change..............",45).'  '.
				adjustRight(number_format($aLoan['advance_change'],2),12)."\n";
	}
	if ($aLoan['previous_balance'] > 0)
	{
		$details .= adjustSize(space(10)."       Previous Balance............",45).'  '.
				adjustRight(number_format($aLoan['previous_balance'],2),12)."\n";
	}
	if ($aLoan['redeem'] > 0)
	{
		$details .= adjustSize(space(10)."       Redeem/Gawad................",45).'  '.
				adjustRight(number_format($aLoan['redeem'],2),12)."\n";
	}

	$details .= adjustSize(space(10)."       Other Charges............",45).'  '.
                                adjustRight(number_format($aLoan['other_charges']+$aLoan['printout']+$aLoan['photo']+$aLoan['atm_charge']+$aLoan['referral_fee'],2),12)."\n";
	if ($aLoan['other_remarks']!= '')
	{
		$details .= space(12).adjustSize(space(10).$aLoan['other_remarks'],60).'  '."\n";
	}

//	$details .= adjustSize(space(10)."       Printout/Photo............",45).'  '.
//				adjustRight(number_format($aLoan['printout']+$aLoan['photo'],2),12)."\n";
//	$details .= adjustSize(space(10)."       ATM Charges...............",45).'  '.
//				adjustRight(number_format($aLoan['atm_charge'],2),12)."\n";

	if ($aLoan['advance_payment'] > 0)
	{
		$details .= adjustSize(space(10)."       Advance Payment.........",45).'  '.
				adjustRight(number_format($aLoan['advance_payment'],2),12)."\n";
	}


	$details .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aLoan['released'],2),12)."\n";

	$details .= str_repeat('=',76)."\n";
	$details .= "Obligation: ".number_format($aLoan['gross'],2).'  '.
				"Ammortization ".number_format($aLoan['ammort'],2).' '.
				mode($aLoan['mode'])." for ".$aLoan['term']." Months \n";
	$details .= str_repeat('=',76)."\n";
	
	$details .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aLoan['released'])." (".number_format($aLoan['released'],2).")\n".
				"as payment for the above loan\n\n\n";
				
	$details .= "Received by: ".adjustSize(strtoupper($aLoan['account']),40)." Prepared by: ".strtoupper($ADMIN['username'])."\n\n\n";
	$details .= "Approved by: ".adjustSize("Operation Manager",40)." Approved by: Cashier\n\n";
	$details .= str_repeat('=',76)."\n";

//echo "<pre>$details</pre>";

	if ($SYSCONF['RECEIPT_PRINT'] != 'GRAPHICS')
	{
		$details .= "<eject>";
		doPrint($details);
	}
	else
	{
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}
}
?>
<script type="text/javascript" src="tabber.js"></script>
<link rel="stylesheet" href="tab.css" TYPE="text/css" MEDIA="screen">
<link rel="stylesheet" href="tab-print.css" TYPE="text/css" MEDIA="print">

<script type="text/javascript">

/* Optional: Temporarily hide the "tabber" class so it does not "flash"
   on the page as plain HTML. After tabber runs, the class is changed
   to "tabberlive" and it will appear. */

document.write('<style type="text/css">.tabber{display:none;}<\/style>');
</script>

<body bgcolor="#EFEFEF"> 
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
   <tr> 
      <td colspan="2">Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?= lookUpAssoc('searchby',array('Account'=>'account','Reference'=>'releasing_id','Date'=>'date','Amount'=>'amount'), $searchby);?>
        <input name="p1" type="button" id="p12" value="Go" onClick="window.location='?p=loan.releasing.browse&p1=Go&xSearch='+xSearch.value+'&searchby='+searchby.value"> 
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=loan.new'">
        <input name="browse" type="button" id="browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'" value="Browse"> 
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font> 
        <hr color="#CC0000"></td>
    </tr>
     <td colspan="2"> <table width="100%" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
          <tr> 
            <td height="18" width="19%"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name<br>
              <input name="account" type="text" id="account" value="<?= stripslashes($aLoan['account']);?>" readOnly size="30" maxlength="40" onFocus="nextfield ='account_code'" style="font-size:18">
              </font></b></td>
            <td width="22%" height="18" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Branch 
              </b></font> <br> <input name="branch_id" type="text" id="branch_id" value="<?=lookUpTableReturnValue('x','branch','branch_id','branch',$aLoan['branch_id']);?>" readOnly size="12" maxlength="12" style="font-size:18"> 
            </td>
            <td width="22%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>MaxTerm<br>
              <input name="max_term" type="text" id="max_term" value="<?=$aLoan['max_term'];?>" readOnly size="5" maxlength="5" style="font-size:18; text-align:right">
              </b></font></td>
            <td width="10%" height="18" nowrap><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date<br>
              <input name="date" type="text" id="date" value="<?= ymd2mdy($aLoan['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"></font></b></td>
            <td width="17%" align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status<br>
              <em><font face="Times New Roman, Times, serif"><b> 
              <?= status($aLoan['status']);?>
              </b></font></em> </font></b></td>
            <td height="18" width="10%" align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference<br>
              <input name="account_id" type="text" id="account_id" value="<?= str_pad($aLoan['releasing_id'],8,'0',str_pad_left);?>" size="12" maxlength="12" style="text-align:center; border:0; background-color:#EFEFEF; padding:0;" readOnly >
              </font></b></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2" bgcolor="#EFEFEF" height="350px" valign="top"> <div class="tabber" style="width:95%; left:20px"> 
          <div class="tabbertab" style="margin:0px"> 
            <h2>Account Info</h2>
			<p>
              <? include_once('loan.releasing.info.php');?>
			  </p>
          </div>
          <div class="tabbertab"> 
            <h2>Loan</h2>
			<p>
              <?  include_once('loan.releasing.loan.php');?>
			</p>
          </div>
          <div class="tabbertab"> 
            <h2>Ledger</h2>
			<? include_once('loan.releasing.ledger.php');?>
          </div>
          <div class="tabbertab"> 
            <h2>Log</h2>
              <?= $aLoan['audit'];?>
	      </div>
        </div></td>
    </tr>
   <tr bgcolor="#FFFFFF"> 
      <td ><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <img src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="xajax_loansave(xajax.getFormValues('f1'))" name="Save"   accesskey="S">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print"   accesskey="P">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> 
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            <td nowrap width="25%"> <img name="New" id="Browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'"  src="../graphics/browse.GIF" alt="New Claim Form" width="67" height="17"> 
            </td>
          </tr>
        </table></td>
      <td bgcolor="#EFEFEF" align="center"><a href="javascript: vUnPost()"><font size="2">UnPost 
        This Transaction</font></a></td>
    </tr>
  </table>
  </form>
</body>
<?

if ($maction == 'saved')
{
	?>
	<script>
	if (confirm("Loan Application Saved.\n Do you wish to Print Voucher?"))
	{
		document.getElementById("f1").action = '?p=loan&p1=Print';
		document.getElementById("f1").submit();
	}
	</script>
	<?
	
}
elseif ($message != '')
{
	echo "<script>alert('$message')</script>";
}

if ($aLoan['max_term'] == '' && $aLoan['date_birth']!='' && $aLoan['date_birth']!='--')
{
	echo "<script>xajax_max_term(xajax.getFormValues('f1'))</script>";
}
?>
