<SCRIPT language=Javascript><!--
function vRequestOverride()
{
	var max_term = 1*(document.getElementById('max_term').value);
	var term = 1*(document.getElementById('term').value);
	
	value =prompt('Request Override Term To ('+max_term+')','');
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
	else 	if (term>max_term)
	{
			if (confirm("Term specified ("+term+") is above maximum ("+max_term+") term allowed...\n   Do you wish to Override?"))
			{
				document.getElementById('principal').focus();
			}
			else
			{
				document.getElementById('term').value = '';
				document.getElementById('term').focus();
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
		window.location='?p=loan.releasing&p1=UnPost'
	}
}
function PrintDeposit()
{
	document.getElementById('f1').action="?p=loan.releasing&p1=PrintDeposit"
}
function PrintPN()
{
	document.getElementById('f1').action="?p=loan.releasing&p1=PrintPN"
}
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Relasing Form?"))
		{
			document.getElementById('f1').action="?p=loan.releasing&p1=CancelConfirm"
		}	
		else
		{
			document.getElementById('f1').action="?p=loan.releasing&p1=Cancel"
			return false;
		}
	}
	else
	{
		document.getElementById('f1').action="?p=loan.releasing&p1="+ul.id;
	}	
	document.getElementById('f1').submit();
}
//-->	
</script>
<?
if (!chkRights2('releasing','mview',$ADMIN['admin_id']))
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
	$transdate = date('Y-m-d');
	echo "<script>window.location='?p=loan.releasing.new'</script>";
	exit;
}
elseif ($p1 == 'Renew' && $rid == '')
{	
	$aLoan=null;
	$aLoan=array();
	$transdate=date('Y-m-d');
	echo "<script>window.location='?p=loan.releasing.new'</script>";
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
				account.withdraw_day,
				account.current_day,
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
	
//	if ($r[current_day] != 0) $r[withdraw_day] = $r[current_day];
	
	$aLoan=$r;

	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
	
	$aLoan['date'] = date('Y-m-d');
	$transdate = date('Y-m-d');
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
	$transdate = date('Y-m-d');
	echo "<script>window.location='?p=loan.releasing.new'</script>";
	exit;
}
elseif ($p1 == 'RenewChecked'  )
{
	$renewadvancechange = $previous_balance = 0;
	$restructure = $_REQUEST['restructure'];
	$aid = $_REQUEST['aid'];
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
	if ($newewadvancechange == '0')
	{
			$retain_advancechange = str_replace(',','',$_REQUEST['advancechange']);
			if ($retain_advancechange > 0)
			{
				$message .= "Retain Advance Change: P ".number_format($retain_advancechange,2);
			}
	}

   if (count($arid)> 0 )
   {
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
         $aid = $r['account_id']; 
         $message .= " Restructure Previous Balance:P ".number_format($previous_balance,2);
   }

   $q = "select sum(balance) as all_balance from releasing where status!='C' and account_id = '$aid'";
   $qmr = @pg_query($q) or message(pg_errormessage().$q);
   $rm = @pg_fetch_object($qmr);
   if ($rm->all_balance > 0 && $rm->all_balance > $previous_balance)
   {
     	$message .= " Retain Previous Balance:P ".number_format($rm->all_balance - $previous_balance,2);
   }
   	
	$q = "select * from account where account_id = '$aid'";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$rr=@pg_fetch_assoc($qr);
	if ($rr['account_status'] != 'A')
	{
		message('Account of '.$r['account'].'<br>Account Status is not active [ '.status($r['account_status']).' ]. Refer to Finance/Supervisor...');
		exit;
	}
	$aLoan=null;
	$aLoan=array();
	$aLoan=$rr;
//	$wday = lookUpTableReturnValue('x','account','account_id','current_day',$aLoan['account_id']);
//	if ($wday == 0) $wday = lookUpTableReturnValue('x','account','account_id','withdraw_day',$aLoan['account_id']);
//	$aLoan[withdraw_day] = $wday;

	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
	
	$aLoan['date'] = date('Y-m-d');
	$aLoan['edate'] = date('Y-m-d');
	$aLoan['audit'] = '';
	$aLoan['addon'] = 'R';
	$transdate = date('Y-m-d');
	
	$aLoan['previous_balance'] = number_format($previous_balance,2);
	$aLoan['arid'] = $arid;	

	if ($renewadvancechange == 1)
	{
		$aLoan['advance_change'] = number_format($advancechange,2);
		$message.=' Credit Advance Change:P'.$aLoan['advance_change'];
	}
	$age = age($aLoan['date_birth']);
	$age = $aLoan['age'];

	$aLoan['audit'] = $message;
	message("[ $message ]");
}
elseif ($p1 == 'New')
{
	if ($aLoan[cashier_id]==0 and $ADMIN[usergroup]=='C') $aLoan[cashier_id] = $ADMIN[admin_id];
	$q = "select * from account where account_id='$aid'";
	$r=fetch_assoc($q);
	
	if ($r['account_status'] != 'A')
	{
		message('Account of '.$r['account'].'<br>Account Status is not active [ '.status($r['account_status']).' ]');
		exit;
	}
	$aLoan=null;
	$transdate=date('Y-m-d');
	$aLoan=array();
	$aLoan=$r;
	$wday = lookUpTableReturnValue('x','account','account_id','current_day',$aLoan['account_id']);
	if ($wday == 0) $wday = lookUpTableReturnValue('x','account','account_id','withdraw_day',$aLoan['account_id']);
	$aLoan[withdraw_day] = $wday;
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
		'comaker1_relation','comaker2_relation','comake1_id','comake2_id',
		'npension','nchangebank','max_term','age','status','deposit','printx','withdraw_day',
		'tpenalty','cashier_id','ded_insure','insure','insureamt');

$fieldc = array('comake1','comake2','comake3','comake4','comake5',
		'comake1_address','comake2_address','comake3_address','comake4_address','comake5_address',
		'comake1_relation','comake2_relation','comake3_relation','comake4_relation','comake5_relation',
		'comake1_id','comake2_id','comake3_id','comake4_id','comake5_id');
		
if (!in_array($p1,array(null,'New','Renew','Load','UnPost','RenewChecked')))
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
	for ($c=0;$c<count($fieldc);$c++)
	{
if ($ADMIN[admin_id]==1)
{
	echo $c.'. '.$fieldc[$c].' : '.$_REQUEST[$fieldc[$c]];
}	
		$aComaker[$fieldc[$c]] = $_REQUEST[$fieldc[$c]];	
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
				account.clientbank_id, 
				account.branch_id,
				account.account_code, 
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
				releasing.photo,
				releasing.advance_payment,
				releasing.previous_balance,
				releasing.advance_change, 
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
				releasing.comake1_id,
				releasing.comake2_id,
				releasing.comaker1,
				releasing.comaker1_address,
				releasing.comaker1_relation,
				releasing.comaker2,
				releasing.comaker2_address,
				releasing.comaker2_relation,
				releasing.mclass,
				releasing.date_child21,
				releasing.npension,
				releasing.nchangebank,
				releasing.status,
				releasing.renew_releasing_id,
				releasing.mode,
				releasing.atm_charge,
				releasing.other_charges,
				releasing.other_remarks,
				releasing.deposit,
				releasing.arid,
				releasing.printx, 
				releasing.tpenalty, 
				releasing.withdraw_day,
				releasing.cashier_id,
				releasing.insure
			from 
				releasing, account, loan_type 
			where
				account.account_id=releasing.account_id and
				loan_type.loan_type_id=releasing.loan_type_id and
				releasing_id='$id'";
	$r = fetch_assoc($q);
	if ($r[insure])
	{
		$q = "select * from insurance where releasing_id='".$r[releasing_id]."' and status='A'";
		$rr = fetch_assoc($q);
		$r[insureamt] = $rr[credit];
	}
//	$q = "select * from loandeposit where releasing_id='".$r[releasing_id]."'";
//	$rr = fetch_assoc($q);
//	$r[deposit] = $rr[credit];
	$aLoan=$r;
	$q = "select * from comaker where releasing_id='".$r[releasing_id]."'";
	$rc = fetch_assoc($q);
	$aComaker = $rc;
	if ($aComaker[comake1_id]=='')
	{
		$aComaker[comake1_id] = $aLoan[comake1_id];
		$aComaker[comake1] = $aLoan[comaker1];
		$aComaker[comake1_address] = $aLoan[comaker1_address];
		$aComaker[comake1_relation] = $aLoan[comaker1_relation];
	}
	if ($aComaker[comake2_id]=='')
	{
		$aComaker[comake2_id] = $aLoan[comake2_id];
		$aComaker[comake2] = $aLoan[comaker2];
		$aComaker[comake2_address] = $aLoan[comaker2_address];
		$aComaker[comake2_relation] = $aLoan[comaker2_relation];
	}
	if ($r[withdraw_day] == 0) 
	{
		$wday = lookUpTableReturnValue('x','account','account_id','current_day',$aLoan['account_id']);
		if ($wday == 0) $wday = lookUpTableReturnValue('x','account','account_id','withdraw_day',$aLoan['account_id']);

		$aLoan[withdraw_day] = $wday;
	}		
	$transdate=$aLoan['date'];

	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;
}
elseif ($p1 == 'Save' && !chkRights3('releasing','medit',$ADMIN['admin_id']) && $aLoan['releasing_id']!='')
{
	message("You have no permission to edit/update Loan Releasing...");
	exit;
}
elseif ($p1 == 'Save'  && (strlen($aLoan['comaker1'])<10 || strlen($aLoan['comaker2'])<10)) //&& !chkRights3('override','madd',$ADMIN['admin_id'])
{
	message("Lacking Co-Maker. Please complete information...");
}
elseif ($p1 == 'Save'  && ($aLoan['ammort']>$aLoan['salary'] )) 
{
	message("Ammortization Amount (P ".number_format($aLoan['ammort'],2).") is greater than Salary of (P ".number_format($aLoan['salary'],2).")");
}
//elseif ($p1 == 'Save' && ($transdate!=date('Y-m-d') or $aLoan['date']!=date('Y-m-d')) && $ADMIN['usergroup'] != 'A')
//{
//echo 'transdate '.$transdate.'  date : '.$aLoan['date'];
//	message("You have no permission to Update/Modify Transaction in this area...");
//exit;	
//}
elseif ($p1 == 'Save' && ($aLoan['principal']==0 ||
	  $aLoan['ammort']==0 ||  $aLoan['loan_type_id']==0))
{
	message("Cannot Save. Lacking Important Data, Please Check...(Loan Type, Rate, Principal, Ammortization..)");
}
elseif ($p1 == 'Save' && $aLoan['status']=='C')
{
	message("Cannot Save. Loan Release has already been cancelled ..)");
}
elseif ($p1 == 'Save' && $aLoan['status']=='P' and $ADMIN['usergroup'] !='A')
{
	message("Cannot Save. Loan Release has already been printed ..)");
}
elseif ($p1 == 'Save')
{
	$account_group_id = lookUpTableReturnValue('x','account','account_id','account_group_id',$aLoan['account_id']);

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
					comaker1_address, comaker2_address,redeem,printx,withdraw_day,tpenalty,
					audit, enable, comake1, comake2,tbranch_id,cashier_id, account_group_id)
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
					'".$aLoan['comaker2_address']."','".$aLoan['redeem']."', 
					'".$aLoan['printx']."','".$aLoan['withdraw_day']."',
					'".$aLoan['tpenalty']."','".$aLoan['audit']."','t',
					'".$aLoan['comake1_id']."','".$aLoan['comake2_id']."','".$ADMIN['branch_id']."','".$ADMIN['cashier_id']."','$account_group_id')";
		}
		else
		{
			$q = "insert into releasing (account_id, loan_type_id, mode, term, max_term, rate, edate, date,
					principal,  advance_payment , advance_applied, ca_balance, previous_balance, interest, service_charge,
					collection_fee,printout, photo, atm_charge, advance_change, other_charges, other_remarks,referral_fee
					insurance, vat, gross, released, ammort, admin_id, status, renew_releasing_id, 
					mclass, date_child21,npension, nchangebank, age, comaker1, comaker2,  comaker1_address, comaker2_address, 
					redeem,printx,withdraw_day,audit, enable, comake1_id, comake2_id,tpenalty,tbranch_id, cashier_id,account_group_id)
				values
					('".$aLoan['account_id']."','".$aLoan['loan_type_id']."','".$aLoan['mode']."',
					'".$aLoan['term']."','".$aLoan['max_term']."','".$aLoan['rate']."','".$aLoan['edate']."',
					'".$aLoan['date']."','".$aLoan['principal']."','".$aLoan['advance_payment']."',
					'".$aLoan['advance_applied']."','".$aLoan['ca_balance']."','".$aLoan['previous_balance']."',
					'".$aLoan['interest']."','".$aLoan['service_charge']."','".$aLoan['collection_fee']."',
					'".$aLoan['printout']."','".$aLoan['photo']."','".$aLoan['atm_charge']."','".$aLoan['advance_change']."',
					'".$aLoan['other_charges']."','".$aLoan['other_remarks']."','".$aLoan['referral_fee']."',
					'".$aLoan['insurance']."','".$aLoan['vat']."','".$aLoan['gross']."',
					'".$aLoan['released']."','".$aLoan['ammort']."',
					'".$ADMIN['admin_id']."','S', '".$aLoan['renew_releasing_id']."', 
					'".$aLoan['mclass']."','".$aLoan['date_child21']."', '".$aLoan['npension']."',
					'".$aLoan['nchangebank']."', '".$aLoan['age']."', 
					'".$aLoan['comaker1']."', '".$aLoan['comaker2']."', '".$aLoan['comaker1_address']."', 
					'".$aLoan['comaker2_address']."', 
					".$aLoan['redeem']."','".$aLoan['printx']."','".$aLoan['withdraw_day']."','".$aLoan['audit']."','t',
					'".$aLoan['comake1_id']."','".$aLoan['comake2_id']."','".$aLoan['tpenalty']."','".$ADMIN['branch_id']."','".$ADMIN['cashier_id']."',
					'$account_group_id')";
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
//if ($ADMIN[admin_id]==1) exit;	
		begin();
		$aLoan['audit'] .=';Updated by:'.$ADMIN['name'].' on '.date('m/d/Y g:ia');
		$q = "update releasing set audit = '".$aLoan['audit']."', ";
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
				$credit_applied = $aLoan['$previous_balance'];				
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
	$q = "select * from comaker where releasing_id='".$r[releasing_id]."'";
	$rc = fetch_assoc($q);
	if ($rc[comaker_id]!='')
	{
		$q = "update comaker set releasing_id='".$aLoan[releasing_id]."'";
		for ($c=0;$c<count($fieldc);$c++)
		{
			$q .= ",".$fieldc[$c]."='".$aComaker[$fieldc[$c]]."'";
		}
		$q .= " where releasing_id='".$aLoan['releasing_id']."'";
echo $q;
//		$qr = @pg_query($q) or message(pg_errormessage());
	}
	else
	{	
		$q = "insert into comaker (comake1, comake2, comake3, comake4, comake5,
				comake1_address, comake2_address, comake3_address, comake4_address, comake5_address,
				comake1_relation, comake2_relation, comake3_relation, comake4_relation, comake5_relation, 
				comake1_id, comake2_id, comake3_id, comake4_id,comake5_id, releasing_id)
			values
				('".$aComake['comake1']."', '".$aComake['comake2']."','".$aComake['comake3']."','".$aComake['comake4']."','".$aComake['comake5']."', 
				 '".$aComake['comake1_address']."','".$aComake['comake2_address']."','".$aComake['comake3_address']."',
				 '".$aComake['comake4_address']."','".$aComake['comake5_address']."',
				 '".$aComake['comake1_relation']."','".$aComake['comake2_relation']."','".$aComake['comake3_relation']."',
				 '".$aComake['comake4_relation']."','".$aComake['comake5_relation']."',
				 '".$aComake['comake1_id']."','".$aComake['comake2_id']."','".$aComake['comake3_id']."','".$aComake['comake4_id']."','".$aComake['comake5_id']."',
				 '".$aLoan['releasing_id']."')";
echo $q;
//		$qr = @pg_query($q) or message(pg_errormessage());
	}
}
elseif ($p1 == 'UnPost' && !chkRights2('releasing','mdelete',$ADMIN['admin_id']))
{
	message1("<br>[ You have NO Access Rights to UnPost Transaction.  ]");
}	
elseif ($p1 == 'UnPostxx')
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
elseif ($p1 == 'CancelConfirm' && !chkRights2('releasing','mdelete',$ADMIN['admin_id']))
{
	message1("<br>[ You have NO Access Rights to Cancel Transaction.  ]");
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

		$q = "update ledger set status='C' where 
				releasing_id='".$aLoan['releasing_id']."' and
				type='D' and 
				account_id='".$aLoan['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());

		$x=@recalculate($aLoan['releasing_id'],'noneform');
		
		$q = "update ledger set status='C' where 
				reference='".$aLoan['releasing_id']."' and
				type='R' and 
				account_id='".$aLoan['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());

		$q = "select * from ledger where 
				reference='".$aLoan['releasing_id']."' and
				type='R' and 
				account_id='".$aLoan['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());

		while ($r = @pg_fetch_object($qr))
		{
			$x=@recalculate($r->releasing_id,'noneform');
		}

		$q = " update wexcess set status='C' 
						where 
							account_id = '".$aLoan['account_id']."' and
							type='D' and  
							remarks = 'LOANCREDIT' and
							ps_remark = '".$aLoan['releasing_id']."'";
		$qr = @pg_query($q);

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
elseif (($p1 == 'Print' or $p1 == 'PrintInfo' or $p1 == 'PrintPN') && $aLoan['releasing_id'] == '' ) //&& in_array($aLoan['status'],array('N','M','')))
{
	$message = "Please save transaction before printing...";
}
elseif ($p1 == 'Print' or $p1 == 'PrintForm')
{
	//make sure only saved info will be printed
	$q = "select 
				account.account,
				account.account_group_id,
				account.bank_account,
				account.salary,
				account.pix,
				account.date_birth,
				account.clientbank_id,
				account.branch_id,
				loan_type.basis as rate_basis, 
				releasing.account_id,
				releasing.age,
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
				releasing.status,
				releasing.renew_releasing_id,
				releasing.mode,
				releasing.mclass,
				releasing.date_child21,
				releasing.npension,
				releasing.nchangebank,
				releasing.max_term,
				releasing.printx,
				releasing.comaker1,
				releasing.comaker1_relation,
				releasing.comaker1_address,
				releasing.comaker2,
				releasing.comaker2_relation,
				releasing.comaker2_address,
				releasing.cashier_id,
				releasing.insure,
				loan_type.basis as rate_basis
			from 
				releasing, account, loan_type 
			where
				account.account_id=releasing.account_id and
				loan_type.loan_type_id=releasing.loan_type_id and
				releasing_id='".$aLoan['releasing_id']."'";
        $qr = @pg_query($q) or message(pg_errormessage());
        $r = @pg_fetch_assoc($qr);

	if ($r[insure])
	{
		$q = "select * from insurance where releasing_id='".$r[releasing_id]."' and status='A'";
		$rr = fetch_assoc($q);
		$r[insureamt] = $rr[credit];
	}
	$aLoan=null;
	$aLoan=array();
	
	$aLoan=$r;

//if ($ADMIN[admin_id]==1) include_once("print.voucher.php");
	
	$detailsn ="\n\n\n\n\n\n\n\n";
	if ($aLoan['account_group_id']=='') $aLoan['account_group_id']=0;

	$details = '';
	$detailsh .= $SYSCONF['BUSINESS_NAME']."\n".	
				$SYSCONF['BUSINESS_ADDR'].'    Tel. No.: '.$SYSCONF['BUSINESS_TEL']."\n";
	$details  .= space(50).'LOAN RELEASE No. '.str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT)."\n";
	$detailsn  .= 'LOAN RELEASE No. '.str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT)."   ";
	$details  .= 'Date & Time Printed : '.date('m/d/Y g:ia').'    Times Printed '.adjustRight($aLoan[printx],3)."\n";
	$detailsn .= 'Printed: '.date('m/d/Y g:ia').' xPrinted '.adjustRight($aLoan[printx],2)."\n";

	$audit = $aLoan['audit'].'Printed by:'.$ADMIN['username'].' '.date('m/d/Y g:ia').';';
	$aLoan['status'] = 'P';
	if ($ADMIN[admin_id]!=1)
	{
		$aLoan['printx']+= 1;
		$qu = "update releasing set status='P', audit='$audit', printx='".$aLoan['printx']."' 
					where releasing_id='".$aLoan['releasing_id']."'";
		$qru = @pg_query($qu) or message(pg_errormessage());
	}
	$details .= str_repeat('=',76)."\n";
	$detailsn .= str_repeat('=',75)."\n";
	$details .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aLoan['account'])),55).'   '.
				 adjustSize(lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type_code',$aLoan['loan_type_id']),15)."\n";
	$detailsn .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aLoan['account'])),55).'   '.
				 adjustSize(lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type_code',$aLoan['loan_type_id']),15)."\n";
	$details .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLoan['account_group_id']),55).'   '.
				 adjustSize("Date:".ymd2mdy($aLoan['date']),15)."\n";
	$detailsn .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLoan['account_group_id']),55).'   '.
				 adjustSize("Date:".ymd2mdy($aLoan['date']),15)."\n";
	$details .= str_repeat('=',76)."\n\n";
	$detailsn .= str_repeat('=',75)."\n";
	
	$details .= adjustSize("Principal Loan",45).
				 space(18).
				 adjustRight(number_format($aLoan['principal'],2),12)."\n";
	$detailsn .= adjustSize("Principal Loan",45).
				 space(18).
				 adjustRight(number_format($aLoan['principal'],2),12)."\n";
				
	$details .= adjustSize(space(10)."Less : Service Charge..........",45).'  '.
				 adjustRight(number_format($aLoan['service_charge']+$aLoan['collection_fee'],2),12)."\n";
	$detailsn .= adjustSize(space(10)."Less : Service Charge..........",45).'  '.
				 adjustRight(number_format($aLoan['service_charge']+$aLoan['collection_fee'],2),12)."\n";

//	$details .= adjustSize(space(10)."       Collection Fee..........",45).'  '.
//				adjustRight(number_format($aLoan['collection_fee']+$aLoan['referral_fee'],2),12)."\n";

	if ($aLoan['previous_balance'] > 0)
	{
		$details .= adjustSize(space(10)."               Advances........",45).'  '.
				adjustRight(number_format($aLoan['previous_balance']+$aLoan['ca_balance'],2),12)."\n";
		$detailsn .= adjustSize(space(10)."               Advances........",45).'  '.
				adjustRight(number_format($aLoan['previous_balance']+$aLoan['ca_balance'],2),12)."\n";
	}

	if ($aLoan['redeem'] > 0)
	{
		$details .= adjustSize(space(10)."       Redeem/Gawad/Yubos/Buy-out...",45).'  '.
				adjustRight(number_format($aLoan['redeem'],2),12)."\n";
		$detailsn .= adjustSize(space(10)."       Redeem/Gawad/Yubos/Buy-out...",45).'  '.
				adjustRight(number_format($aLoan['redeem'],2),12)."\n";
	}
	if ($aLoan['advance_change'] > 0)
	{
		$details .= adjustSize(space(10)."       Advance Change..............",45).'  '.
				adjustRight(number_format($aLoan['advance_change'],2),12)."\n";
		$detailsn .= adjustSize(space(10)."       Advance Change..............",45).'  '.
				adjustRight(number_format($aLoan['advance_change'],2),12)."\n";
	}
	if ($aLoan['insure'])
	{
		$details .= adjustSize(space(10)."       Insurance...................",45).'  '.
				adjustRight(number_format($aLoan['insureamt'],2),12)."\n";
		$detailsn .= adjustSize(space(10)."       Insurance...................",45).'  '.
				adjustRight(number_format($aLoan['insureamt'],2),12)."\n";
	}


	$details .= adjustSize(space(10)."       Other Charges............",45).'  '.
				adjustRight(number_format($aLoan['other_charges']+$aLoan['printout']+$aLoan['photo']+$aLoan['atm_charge']+$aLoan['referral_fee'],2),12)."\n";
	$detailsn .= adjustSize(space(10)."       Other Charges............",45).'  '.
				adjustRight(number_format($aLoan['other_charges']+$aLoan['printout']+$aLoan['photo']+$aLoan['atm_charge']+$aLoan['referral_fee'],2),12)."\n";
	if ($aLoan['other_remarks']!= '')
	{
		$details .= space(5).substr($aLoan['other_remarks'],0,70).'  '."\n";
		$detailsn .= space(5).substr($aLoan['other_remarks'],0,70).'  '."\n";
		if (strlen($aLoan['other_remarks'])>70)
		{
			$details .= space(5).substr($aLoan['other_remarks'],70,70).'  '."\n";
			$detailsn .= space(5).substr($aLoan['other_remarks'],70,70)." \n";
		}
	}

/*	if ($aLoan['insurance']+$aLoan['vat'] > 0)
	{
		$details .= adjustSize(space(10)."       Insurance/VAT.............",45).'  '.
				adjustRight(number_format($aLoan['insurance']+$aLoan['vat'],2),12)."\n";
	}
*/
	if ($aLoan['advance_payment'] > 0)
	{
		$details .= adjustSize(space(10)."       Advance Payment.........",45).'  '.
				adjustRight(number_format($aLoan['advance_payment'],2),12)."\n";
		$detailsn .= adjustSize(space(10)."       Advance Payment.........",45).'  '.
				adjustRight(number_format($aLoan['advance_payment'],2),12)."\n";
	}


	$details .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aLoan['released'],2),12)."\n";
	$detailsn .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aLoan['released'],2),12)."\n";

	$details .= str_repeat('=',76)."\n";
	$detailsn .= str_repeat('=',75)."\n";
	$details .= "Obligation: ".number_format($aLoan['gross'],2).'  '.
				"Ammortization ".number_format($aLoan['ammort'],2).' '.
				mode($aLoan['mode'])." for ".$aLoan['term']." Months \n";
	$detailsn .= "Obligation: ".number_format($aLoan['gross'],2).'  '.
				"Ammortization ".number_format($aLoan['ammort'],2).' '.
				mode($aLoan['mode'])." for ".$aLoan['term']." Months \n";
	$details .= str_repeat('=',76)."\n";
	$detailsn .= str_repeat('=',75)."\n";
	
	$details .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aLoan['released'])." (".number_format($aLoan['released'],2).")\n".
				"as payment for the above loan\n\n\n";
	$detailsn .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aLoan['released'])." (".number_format($aLoan['released'],2).")\n".
				"as payment for the above loan\n\n";
				
	$details .= "Received by: ".adjustSize(strtoupper($aLoan['account']),76)."\n\n\n";
	$detailsn .= "Received by: ".adjustSize(strtoupper($aLoan['account']),80)."\n\n\n";
	
	$user = lookUpTableReturnValue('x','admin','admin_id','username',$aLoan['admin_id']);			
	if ($aLoan[cashier_id]=='' or $aLoan[cashier_id]==0) $cashier = $ADMIN['username'];
	else $cashier = lookUpTableReturnValue('x','admin','admin_id','username',$aLoan['cashier_id']);
	
	$details .= adjustSize("Reviewed by: ".$ADMIN['username'],25).adjustSize(" Prepared by: ".strtoupper($user),25).
				adjustSize(" Released by: ".$cashier,25)."\n\n";
	$detailsn .= adjustSize("Reviewed by: ".$ADMIN['username'],25).adjustSize(" Prepared by: ".strtoupper($user),25).
				adjustSize(" Released by: ".$cashier,25)."\n";
	$details .= str_repeat('=',76)."\n";
	$detailsn .= str_repeat('=',75)."\n";

if ($ADMIN[admin_id]==1)  echo "<pre>$detailsn</pre>"; 
 	
	if ($SYSCONF['PRINTER_TYPE'] == 'GRAPHIC' or $p1=='PrintForm')
	{
		if ($p1=='PrintForm') $printdetails = "<font size='5'>".$detailsn."</font>";
		else
			$printdetails = "<font size='3'>".$detailsh.$details."</font>";
			
	 	echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$printdetails.'"'.">";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}
	else
	{
		$details .= "<eject>";
        doPrint($detailsh.$details);
	}
}
elseif (($p1 == 'PrintDeposit' or $p1 == 'NewPrintDeposit') and $aLoan['deposit']==0)
{
	$message = "Please reload transaction ....";
}
elseif ($p1 == 'PrintInfo')
{
	include_once("print.loaninfo.php");
}
elseif ($p1 == 'PrintPN')
{
	include_once("print.pmnote.php");
}
elseif ($p1 == 'PrintDeposit' or $p1 == 'NewPrintDeposit' )
{
	$q = "select * from loandeposit where releasing_id='".$aLoan[releasing_id]."'";
	$qr = @pg_query($q);
	
	if (@pg_num_rows($qr) > 0)
	{
		$rd = @pg_fetch_object($qr);
		$detailsn ="\n\n\n\n\n";
		$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n".	 
					center($SYSCONF['BUSINESS_ADDR'],80)."\n".
					center('Tel. No.: '.$SYSCONF['BUSINESS_TEL'],80)."\n\n".
					center('ACKNOWLEDGEMENT RECEIPT # '.str_pad($rd->loandeposit_id,8,'0',STR_PAD_LEFT),80)."\n".
					center('Date : '.ymd2mdy($aLoan['date']),80)."\n".
					center('Cross Refrence Loan Releasing No. '.str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT),80)."\n\n";
		$detailsn .= center('ACKNOWLEDGEMENT RECEIPT # '.str_pad($rd->loandeposit_id,8,'0',STR_PAD_LEFT),80)."\n".
					center('Date : '.ymd2mdy($aLoan['date']),80)."\n".
					center('Cross Refrence Loan Releasing No. '.str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT),80)."\n\n";

		$details .= '    Received from '.adjustSize(strtoupper(htmlspecialchars($aLoan['account'])),55)."\n";
		$detailsn .= '    Received from '.adjustSize(strtoupper(htmlspecialchars($aLoan['account'])),55)."\n";
		$details .= 'the amount of '.numWord($aLoan['deposit'])." (P ".number_format($aLoan['deposit'],2).")";
		$detailsn .= 'the amount of '.numWord($aLoan['deposit'])." (P ".number_format($aLoan['deposit'],2).")";
		$det= 'the amount of '.numWord($aLoan['deposit'])." (P ".number_format($aLoan['deposit'],2).")";
		if (strlen($det) > 50) $details .= "\n";
		if (strlen($det) > 50) $detailsn .= "\n";
		$details .=	"  as loan deposit.\n\n\n\n";
		$detailsn .=	"  as loan deposit.\n\n\n\n";

		$user = lookUpTableReturnValue('x','admin','admin_id','username',$aLoan['admin_id']);			
		$details .= "Acknowledged by: ".adjustSize(strtoupper($aLoan['account']),80)."\n\n\n";
		$detailsn .= "Acknowledged by: ".adjustSize(strtoupper($aLoan['account']),80)."\n\n\n";
	
		$details .= adjustSize("Approved by: ",25).adjustSize(" Prepared by: ".strtoupper($user),25).
				adjustSize(" Received by: ".$ADMIN['username'],25)."\n\n";
		$detailsn .= adjustSize("Approved by: ",25).adjustSize(" Prepared by: ".strtoupper($user),25).
				adjustSize(" Received by: ".$ADMIN['username'],25)."\n\n";
		$details .= str_repeat('=',76)."\n\n\n\n";

//	echo "<pre>$details</pre>";
		
		if ($SYSCONF['PRINTER_TYPE'] == 'GRAPHIC' or $p1 == 'NewPrintDeposit')
		{
			$printdetails = "<font size='3'>".$detailsn."</font>";
			echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$printdetails.'"'.">";
			echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
			echo "<script>printIframe(print_area)</script>";
		}
		else
		{
			$details .= "<eject>";
			doPrint($details);
		}
	}
}
elseif (substr($p1,0,14) == 'ReleaseDeposit')
{
	$q	= "select * from loandeposit where releasing_id ='".$aLoan['releasing_id']."' order by loandeposit_id";
	$qr = pg_query($q);
	$cc=0;

	while ($rr = pg_fetch_object($qr))
	{
		if ($cc == 1)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197) 
				$oldrel = $rr->debit;
			else 
			{
				$out1 = $rr->debit;
				$dte1 = $rr->date;
			}	
			$lid1 = $rr->loandeposit_id;
		}	
		elseif ($cc == 2)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197) $oldrel=$rr->debit;
			else 
			{
				$out2 = $rr->debit;
				$dte2 = $rr->date;
			}	
			$lid2 = $rr->loandeposit_id;
		}	
		elseif ($cc == 3)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197) $oldrel=$rr->debit;
			else 
			{
				$out3 = $rr->debit;
				$dte3 = $rr->date;
			}	
			$lid3 = $rr->loandeposit_id;
		}	
		elseif ($cc == 4)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197) $oldrel=$rr->debit;
			else 
			{
				$out4 = $rr->debit;
				$dte4 = $rr->date;
			}	
			$lid4 = $rr->loandeposit_id;
		}	
		$cc++;
	}
	$rlno = substr($p1,14,1);
	$ldid = 0;

	if ($rlno == '1')
	{
		if ($out1 > $aLoan[deposit])
		{
			$dte1 = $reldate = '';
			$out1 = $relamt	 = 0;
			$ldid	 = 0;
		} else
		{
			$reldate = $dte1;
			$relamt	 = $out1;
			$ldid	 = $lid1;
		}
	}
	elseif ($rlno == '2')
	{
		if ($out1+$out2 > $aLoan[deposit]+.0001)
		{
			$dte2 = $reldate = '';
			$out2 = $relamt	 = 0;
			$ldid	 = 0;
		} else
		{
			$reldate = $dte2;
			$relamt	 = $out2;
			$ldid	 = $lid2;
		}
	}
	elseif ($rlno == '3')
	{
		if ($out1+$out2+$out3 > $aLoan[deposit]+.0001)
		{
			$dte3 = $reldate = '';
			$out3 = $relamt	 = 0;
			$ldid	 = 0;
		} else
		{
			$reldate = $dte3;
			$relamt	 = $out3;
			$ldid	 = $lid3;
		}
	}		
	elseif ($rlno == '4')
	{
		if ($out1+$out2+$out3+$out4 > $aLoan[deposit]+.0001)
		{
			$dte4 = $reldate = '';
			$out4 = $relamt	 = 0;
			$ldid	 = 0;
		} else
		{
			$reldate = $dte4;
			$relamt	 = $out4;
			$ldid	 = $lid4;
		}
	}
	if ($relamt + $oldrel > 0)
	{
		if ($ldid == 0)
		{
			$rdate = mdy2ymd($reldate);
			if (substr($rdate,0,1)=='-') $rdate = substr($rdate,1,10);
			if (substr($rdate,0,4) < '2014') 
				message("Encountered an ERROR releasing deposit, please inform programmer");
			else
			{
				$q = "insert into loandeposit (releasing_id,date,account_id, debit, admin_id, status)
						values ('".$aLoan['releasing_id']."','$rdate','".$aLoan['account_id']."', 
						'$relamt','".$ADMIN['admin_id']."','R')";
				$qr = @pg_query($q);	
				$q = "select currval('loandeposit_loandeposit_id_seq')" ;
				$rp = fetch_object($q);
				$ldid = $rp->currval;
			}	
		} else
		{	
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197)
			{
				$rdate = mdy2ymd($reldate);
				if (substr($rdate,0,1)=='-') $rdate = substr($rdate,1,10);
				if (substr($rdate,0,4) < '2014') 
					message("Encountered an ERROR releasing deposit, please inform programmer");
				else
				{
					if ($relamt=='')
					{ 
						$relamt=0.00;
						$q = "update loandeposit set debit = '$relamt', date='' where loandeposit_id='$ldid'";
					} else	
						$q = "update loandeposit set debit = '$relamt', date='$rdate' where loandeposit_id='$ldid'";
				
					$qr = @pg_query($q);	
				}
			}	
		}
///print
		$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n".	 
					center($SYSCONF['BUSINESS_ADDR'],80)."\n".
					center('Tel. No.: '.$SYSCONF['BUSINESS_TEL'],80)."\n\n".
					center('RELEASE NO : '.$rlno,80)."\n".
					center('Release Date : '.$reldate,80)."\n".
					center('Cross Refrence Loan Releasing No. '.str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT),80)."\n\n";
	
		$details .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
					numWord($relamt)." (".number_format($relamt,2).") ".
					"as release of loan deposit.\n\n\n";
	
		$user = lookUpTableReturnValue('x','admin','admin_id','username',$aLoan['admin_id']);			
	
		$details .= adjustSize(" Prepared by: ".$ADMIN['username'],25).space(25).adjustSize(" Acknowledged by: ".$user,25)."\n\n\n\n".
					center("Received by: ".$aLoan['account'],76)."\n\n";
		$details .= str_repeat('=',76)."\n\n\n\n";
	
//		echo "<pre>$details</pre>";
	
//		if ($SYSCONF['PRINTER_TYPE'] == 'GRAPHIC')
//		{
			$printdetails = "<font size='3'>".$details."</font>";
			echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$printdetails.'"'.">";
			echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
			echo "<script>printIframe(print_area)</script>";
/*		}
		else
		{
			$details .= "<eject>";
			doPrint($details);
		}*/
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
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=loan.releasing.new'">
        <input name="browse" type="button" id="browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'" value="Browse"> 
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='">
        <hr color="#CC0000"></td>
    </tr>
     <td colspan="2"> <table width="100%" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
          <tr> 
            <td height="18" width="27%"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name<font size="1"> 
              [RecId: 
              <?= $aLoan['account_id'];?>
              ]</font><br>
              <input name="account" type="text" id="account" value="<?= stripslashes($aLoan['account']);?>" readOnly size="50" maxlength="50" onFocus="nextfield ='account_code'" style="font-size:18">
            </font></b></td>
            <td width="17%" height="18" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Branch 
              </b></font> <br> 
              <input name="branch_id" type="text" id="branch_id" value="<?=lookUpTableReturnValue('x','branch','branch_id','branch',$aLoan['branch_id']);?>" readOnly size="12" maxlength="12" style="font-size:18"> 
            </td>
            <td width="20%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>MaxTerm<br>
              <input name="max_term" type="text" id="max_term" value="<?=$aLoan['max_term'];?>" readOnly size="5" maxlength="5" style="font-size:18; text-align:right">
            </b></font></td>
            <td width="11%" height="18" nowrap><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date<br>
              <input name="date" type="text" id="date" value="<?= ymd2mdy($aLoan['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2">
              <input name="transdate" type="hidden" id="transdate"  readonly="readOnly" value="<?=$transdate;?>" size="8" />
            </font></strong></font></font></strong></font></font></strong></font></font></strong></font></font></b></td>
            <td width="15%" align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status<br>
              <em><font face="Times New Roman, Times, serif"><b> 
              <?=($aLoan[printx] >0?$aLoan[printx].' X ':'').status($aLoan['status']);?>
            </b></font></em> </font></b></td>
            <td height="18" width="10%" align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference<br>
              <input name="reference_id" type="text" id="reference_id" value="<?= str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT);?>" size="12" maxlength="12" style="text-align:center; border:0; background-color:#EFEFEF; padding:0;" readOnly >
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
            <h2>Additional CoMakers</h2>
			<p>
              <? include_once('loan.releasing.comaker.php');?>
			</p>
          </div>
          <div class="tabbertab"> 
            <h2>Loan</h2>
			<p>
              <? include_once('loan.releasing.loan.php');?>
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
          <div class="tabbertab"> 
            <h2>Release Deposit</h2>
			<? include_once('loan.releasing.deposit.php');?>
	      </div>
        </div></td>
    </tr>
   <tr bgcolor="#FFFFFF"> 
      <td ><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
			<?
			if (($aLoan['status']!='P' and $aLoan['status']!='C') or $ADMIN['usergroup'] =='A' )
			{
			?>
              <img src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="if(confirm('Are you sure to Save Loan Release Form?')){wait('Please wait. Saving Loan Release Form...');xajax_loansave(xajax.getFormValues('f1'));return false;}else{return false;}" name="Save"   accesskey="S">
			<?
			} else 
			    echo "<td>View Only</td>";
			?>  
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print"   accesskey="P">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> 
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            <td nowrap width="25%"> <img name="Browse" id="Browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'"  src="../graphics/browse.gif" alt="Browse" width="67" height="17"> 
            </td>
			<td><input type="button" value="PrintForm" onClick="vSubmit(this)" name="PrintForm" id="PrintForm"></td>
			<td><input type="button" value="PrintDeposit" onClick="vSubmit(this)" name="PrintDeposit" id="PrintDeposit"></td>
			<td><input type="button" value="NewPrintDeposit" onClick="vSubmit(this)" name="NewPrintDeposit" id="NewPrintDeposit"></td>
			<td><input type="button" value="PrintInfo" onClick="vSubmit(this)" name="PrintInfo" id="PrintInfo"></td>
			<td><input type="button" value="PrintPN" onClick="vSubmit(this)" name="PrintPN" id="PrintPN"></td>
          </tr>
        </table></td>
      <td bgcolor="#EFEFEF" align="center"></font></a></td> 
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
		document.getElementById("f1").action = '?p=loan.releasing&p1=Print';
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
	echo "<script>xajax_computeMaxTerm(xajax.getFormValues('f1'))</script>";
}
?>
