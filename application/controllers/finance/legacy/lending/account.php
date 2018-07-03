<script>
function vPix()
{
	f1.pix.src=f1.pixfile.value;
}

function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f1.action="?p=account&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=account&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=account&p1="+ul.id;
	}	
}
function switchPix(o)
{
	var folder = "../../graphics/";
	var n = o.name;
	var obj = new Array('patient_data','address','admission','account','diagnosis');

	for (c = 0;c<obj.length;c++)
	{
		eval("this.f1."+obj[c]).src=folder+obj[c]+"_lo.jpg";
		eval("this."+obj[c]+".style").visibility="hidden";
	}

	o.src=folder+o.name+"_hi.jpg"
	eval("this."+n+".style").visibility = "visible"
	
}
</script>
<?

$this->View->setPageTitle("Account Information");

function accountCode($branch_id)
{
	if ($branch_id == '' or $branch_id == '0')
	{
		message("No Branch Specified...");
		return '';
	}
	$q = "select * from branch where branch_id = '$branch_id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	
	$q = "select * from cache where type='account_code' and value1='$r->branch_id'";
	$qqr = @pg_query($q) or message(pg_errormessage());
	$rr = @pg_fetch_object($qqr);
	if (@pg_num_rows($qqr) == 0)
	{
		$value2=1;

		while (true)
		{
			$account_code = $r->branch_code.'-'.str_pad($value2,5,'0', STR_PAD_LEFT);
			$q = "select * from account where account_code= '$account_code'";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			if (@pg_num_rows($qr) == 0)
			{
				break;
			}
			$value2++;
		}


		$q = "insert into cache (type,value1,value2,description) values ('account_code','$rr->branch_id','$value2','$rr->branch_code')";
		@pg_query($q) or message(pg_errormessage().$q);
		

	}
	else
	{
		$value2 = $rr->value2+1;


		while (true)
		{
			$account_code = $r->branch_code.'-'.str_pad($value2,5,'0', str_pad_left);
			$q = "select * from account where account_code= '$account_code'";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			if (@pg_num_rows($qr) == 0)
			{
				break;
			}
			$value2++;
		}
		$q = "update cache set value2='$value2' where type='account_code' and value1='$branch_id'";
		@pg_query($q) or message(pg_errormessage().$q);

	}
	
	
	return $account_code;
}


if (!chkRights2('account','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aaccount'))
{
	session_register('aaccount');
	$aaccount = null;
	$aaccount = array();
}

$p1 = $this->input->get_post('p1', null);
$p3 = $this->input->get_post('p3', null);
$search = $this->input->get_post('search', null);
$searchby = $this->input->get_post('searchby', null);

$fields = array('account_code','account','address','telno', 'collection_type_id',
			'date_birth','age','gender','spouse','spouse_sss',
			'account_group_id','account_status','civil_status','sss',
			'ofc_address','office','ofc_telno','bank_account','salary',
			'comaker1','comaker1_address','comaker2','comaker2_address',
			'comaker1_relation','comaker2_relation',
			'remarks','clientbank_id','bank_pin','branch_id','withdraw_day','current_day',
			'bank_cardno','date_atm_in','date_atm_out','smartno',
			'mclass','date_child21','date_child21b','date_child21c','date_child21d',
			'child1','child2','child3','child4','firstname','lastname','middlename',
			'npension','nchangebank','ecamount','disability','member','enable');



if (!in_array($p1,array(null,'showaudit','Load','disable','enable','Print')))
{
	for ($c=0;$c<count($fields);$c++)
	{
		if (in_array($fields[$c],array('account_group_id','collection_type_id','salary','clientbank_id','withdraw_day','age','npension','nchangebank','ecamount','disability')))
		{
			$aaccount[$fields[$c]] = 1*$_REQUEST[$fields[$c]];

 			if ($aaccount[$fields[$c]]=='')
 			{
				$aaccount[$fields[$c]]=0;
			}
		}
		elseif (substr($fields[$c],0,4) == 'date')
		{
			$aaccount[$fields[$c]] = mdy2ymd($_REQUEST[$fields[$c]]);
		}
		else
		{
			$aaccount[$fields[$c]] = $_REQUEST[$fields[$c]];
		}
	}
	if ($aaccount[bank_pin]!='') $bank_pin='*********';
	else $aaccount['bank_pin']=lookUpTableReturnValue('x','account','account_id','bank_pin',$aaccount['account_id']);
	
	if ($aaccount[bank_account]!='')$bank_account='*********';
	else $aaccount['bank_account']=lookUpTableReturnValue('x','account','account_id','bank_account',$aaccount['account_id']);

	$aaccount['pixfile_tmp']=$_FILES['pixfile']['tmp_name'];
	$aaccount['pixfile']=$_FILES['pixfile']['name'];
	$x = explode('.',$aaccount['pixfile']);
	$aaccount['pixfile_extension'] = $x[count($x)-1];
	
	if ($aaccount['withdraw_day'] != intval($aaccount['withdraw_day']))
	{
		message(' Withdraw day specified was '.$aaccount['withdraw_day'].' adjusted to... '.intval($aaccount['withdraw_day']).' Please re-check...');
	}
	$aaccount['withdraw_day'] = intval($aaccount['withdraw_day']);

	if (1*$aaccount['branch_id'] == '0')
	{
		if ($ADMIN['branch_id'] > '0')
		{
			$aaccount['branch_id'] = $ADMIN['branch_id'];
		}
	}
	if ($aaccount['enable']=='') $aaccount['enable']=true;
}	
if ($p3 == 'Clear Pin')
{
	$pin = $aaccount['smartno'];
	$qu = "update account set pensionpin = '' where smartno ='$pin'";
	$qru = @pg_query($qu) or message(pg_errormessage());
}

if ($p1 == 'New')
{
	$aaccount = null;
	$aaccount = array();
	$aacount['current_day'] = 0;
	
	if ($ADMIN['branch_id'] > '0')
	{
		$aaccount['branch_id'] = $ADMIN['branch_id'];
	}
}
elseif ($p1 == 'Print')
{
	include_once('print.accountinfo.php');
}
elseif ($p1 == '^' && $aaccount['account_code'] != '')
{
	message("Account Code (".$aaccount['account_code'].") has already been generated.<br> Remove Account Code To Generate New");
}
elseif ($p1 == '^')
{
	if ($ADMIN['branch_id'] > '0')
	{
		$aaccount['branch_id'] = $ADMIN['branch_id'];
	}

	$account_code = accountCode($aaccount['branch_id']);
	if ($aaccount['account_code'] == '')
	{
		$aaccount['account_code'] = $account_code;
	}
	else
	{
		message('Account Code Generated : '.$account_code);
	}

// -- generate account codes for all branches
/*
	$q = "select * from branch";
	$qqr = @pg_query($q) or message(pg_erroemessage());
	while ($rr = @pg_fetch_object($qqr))
	{
		if ($rr->branch_code == '') continue;
		$q = "select * from account where branch_id='$rr->branch_id' order by account_id";
		$qr = @pg_query($q) or message(pg_errormessage());
		$cc=0;
		while ($r = @pg_fetch_object($qr))
		{
			$cc++;
			$ac = $rr->branch_code.'-'.str_pad($cc,5,'0', str_pad_left);
			$q = "update account set account_code = '$ac' where account_id = '$r->account_id'";
			@pg_query($q) or message(pg_errormessage());
		
		}
		$q = "insert into cache (type, value1, value2, description) values ('account_code', '$rr->branch_id','$cc','$rr->branch_code')";;
		$qr = @pg_query($q) or message(pg_errormessage());
	}
*/
}
elseif ($p1=='Load' && $id=='')
{
	message("No account to edit...");
	exit;
}
elseif ($p1=='Load' && $id!='')
{
	$aaccount = null;
	$aaccount = array();
	$q = "select * from account where account_id = '$id'";
	$r = fetch_assoc($q);
	$r[pix] = $r[pixsign] = $r[account_code].".jpg";
	if ($r[firstname]=='' and $r[lastname]=='')
	{
		$name=explode('/',$r[account]);
		$name1=$name[0];
		$name2=explode(',',$name1);
		$nlen = strlen($name2[1])-1;
		if (substr($name2[1],$nlen)=='.')
		{
			$r[firstname] = substr($name2[1],1,$nlen-2);
			$r[middlename] = substr($name2[1],$nlen-1,2);
		}	
		else
			$r[firstname] = $name2[1];
		$r[lastname] = $name2[0];
	}
	$aaccount = $r;
	if ($aaccount[bank_pin]!='') $bank_pin='*********';
	if ($aaccount[bank_account]!='')$bank_account='*********';
	$branch_id = $aaccount['branch_id'];
}		

elseif ($p1=='showaudit')
{
	$aaccount['showaudit'] =1;
}
elseif ($p1 == 'Save' && !chkRights3('account','medit',$ADMIN['admin_id']) && $aaccount['account_id']!='')
{
	message("You have no permission to edit/update Account Information...");
	exit;
}
elseif ($p1 == 'Save' && ($aaccount['comaker1']=='' || $aaccount['comaker2']=='' || $aaccount['comaker1_address']==''  || $aaccount['comaker2_address']=='' ))
{
	message("Lacking Data for Comaker...");
}
elseif ($p1 == 'Save' && ($aaccount['sss']==''))
{
	message("Lacking Data for SSS Number...");
}
elseif ($p1 == 'Save' && ($aaccount['comaker1_relation']=='' || $aaccount['comaker2_relation']=='' ))
{
	message("Lacking Relationship Data for Comaker...");
}
elseif ($p1 == 'Save' && $aaccount['account_group_id']*1 == '0')
{
	message("Lacking Data for Account Group...");	
}
elseif ($p1 == 'Save' && $aaccount['salary'] == '0')
{
	message("Lacking Data for Salary...");	
}
elseif ($p1 == 'Save' && $aaccount['age'] == '0')
{
	message("Lacking Information on Age...");	
}
elseif ($p1 == 'Save' && $aaccount['bank_account'] == '')
{
	message("Lacking Information on Bank Account...");	
}
elseif ($p1 == 'Save' && $aaccount['collection_type_id']*1 == '0')
{
	message("Lacking Information on Collection Type...");	
}
elseif ($p1 == 'Save' && $aaccount['clientbank_id']*1 == '0')
{
	message("Lacking Information on Client Bank Account...");	
}
elseif ($p1 == 'Save' && $aaccount['date_birth'] == '--')
{
	message("Lacking Information on Date of Birth...");	
}
elseif ($p1 == 'Save' && $aaccount['salary'] == '')
{
	message("Lacking Data for Salary...");	
}
elseif ($p1 == 'Save' && $aaccount['account']!='')
{
	if ($aaccount[bank_pin]=='*********')
		$aaccount['bank_pin']=lookUpTableReturnValue('x','account','account_id','bank_pin',$aaccount['account_id']);
	
	if ($aaccount[bank_account]=='*********')
		$aaccount['bank_account']=lookUpTableReturnValue('x','account','account_id','bank_account',$aaccount['account_id']);

	if ($ADMIN['branch_id'] > '0')
	{
		if ($ADMIN['branch_id'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id'];
		elseif ($ADMIN['branch_id2'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id2'];
		elseif ($ADMIN['branch_id3'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id3'];
		elseif ($ADMIN['branch_id4'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id4'];
		elseif ($ADMIN['branch_id5'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id5'];
		elseif ($ADMIN['branch_id6'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id6'];
		elseif ($ADMIN['branch_id7'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id7'];
		elseif ($ADMIN['branch_id8'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id8'];
		elseif ($ADMIN['branch_id9'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id9'];
		elseif ($ADMIN['branch_id10'] == $aaccount['branch_id']) $aaccount['branch_id'] = $ADMIN['branch_id10'];
	}


    $aaccount['salary'] = str_replace(',','',$aaccount['salary']);
	if ($aaccount['account_code'] == '')
	{
		$aaccount['account_code'] = accountCode($aaccount['branch_id']);
	}
	if (($aaccount['account_status']=='' or $aaccount['account_status']==' ') and $aacount[account_id]!='') 
	{
		$aaccount['account_status']=lookUpTableReturnValue('x','account','account_id','account_status',$aacount['account_id']);
	}
	if ($aaccount[account_status]=='1')
	{
		$aaccount['account_status'] = 'A';
	}
	if ($account[account_status]=='t') 
	{
		$account[account_status] = 'A';
	}	

	if ($aaccount['account_id'] == '')
	{
		$aaccount['account_status'] = 'A';
		$aaccount['enable']=true;
		if ($aacount['current_day'] == null or $aaccount['current_day'] =='') $aaccount['current_day'] = 0;
		$audit = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$aaccount['audit'] = $audit;
		$q = "insert into account 
						(account_code, account, address, ofc_telno, telno, 
						 collection_type_id, account_group_id,
						 civil_status, account_status, ofc_address, office, 
						 clientbank_id, bank_pin, 
						 bank_account, bank_cardno, salary,sss,
						 comaker1,comaker1_address, 
						 comaker2,comaker2_address,
						 comaker1_relation,  comaker2_relation,
						 branch_id, remarks,
						 date_birth, age, 
						 spouse, spouse_sss, 
						 gender, mclass, 
						 date_child21, date_child21b, date_child21c, date_child21d,
						 child1,child2,child3,child4,
						 npension,nchangebank,
						 withdraw_day, current_day, smartno, member, 
						 ecamount, disability, enable)
					values 
						('".$aaccount['account_code']."','".$aaccount['account']."','".$aaccount['address']."',
						'".$aaccount['ofc_telno']."','".$aaccount['telno']."','".$aaccount['collection_type_id']."',
						'".$aaccount['account_group_id']."',
						'".$aaccount['civil_status']."',
						'".$aaccount['account_status']."',
						'".$aaccount['ofc_address']."','".$aaccount['office']."',
						'".$aaccount['clientbank_id']."','".$aaccount['bank_pin']."',
						'".$aaccount['bank_account']."','".$aaccount['bank_cardno']."',
						'".$aaccount['salary']."','".$aaccount['sss']."',
						'".$aaccount['comaker1']."','".$aaccount['comaker1_address']."',
						'".$aaccount['comaker2']."','".$aaccount['comaker2_address']."',
						'".$aaccount['comaker1_relation']."','".$aaccount['comaker2_relation']."',
						'".$aaccount['branch_id']."','".$aaccount['remarks']."',
						'".$aaccount['date_birth']."','".$aaccount['age']."',
						'".$aaccount['spouse']."','".$aaccount['spouse_sss']."',
						'".$aaccount['gender']."', '".$aaccount['mclass']."', 
						'".$aaccount['date_child21']."','".$aaccount['date_child21b']."', 
						'".$aaccount['date_child21c']."','".$aaccount['date_child21d']."', 
						'".$aaccount['child1']."','".$aaccount['child2']."', 
						'".$aaccount['child3']."','".$aaccount['child4']."', 
						'".$aaccount['npension']."','".$aaccount['nchangebank']."',
						'".$aaccount['withdraw_day']."','".$aaccount['current_day']."',
						'".$aaccount['smartno']."','".$aaccount['member']."',
						'".$aaccount['ecamount']."','".$aaccount['disability']."','".$aaccount['enable']."')";
	}
	else
	{
		$audit = $aaccount['audit'].'Updated by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$aaccount['audit'] = $audit;
		$q = "update account set audit='$audit'  ";
	
		for($c=0;$c<count($fields);$c++)
		{
			if (substr($fields[$c],0,4) == 'date' && $aaccount[$fields[$c]]=='--')
			{
				$q .= ", ".$fields[$c] ."= NULL";
				continue;
			}
			elseif ($fields[$c] == 'remarks')
			{
				$remarks = $aaccount[remarks];
				$q .= ", ".$fields[$c] ."='$remarks'";
			} 
			else
				$q .= ", ".$fields[$c] ."='".$aaccount[$fields[$c]]."'";
		}
		$q .= " where account_id='".$aaccount['account_id']."'";

	}

    /*if ($ADMIN[admin_id]==1)
    {
        if ($account[account_status]=='t')
        {
            echo "   check point   ";
            $account[account_status] = 'A';
        }
        echo $account[account_status].'  '.$q;
        exit;
    }*/

	$qr = @pg_query($q) or message("Error saving account data...".pg_errormessage().$q);
	if ($qr)
	{
		if ($aaccount['account_id'] == '')
		{
			$q = "select currval('account_account_id_seq'::text)";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			$r = @pg_fetch_object($qr);
			$aaccount['account_id'] = $r->currval;

			// update date fields after insert
			$q = "update account set account='".$aaccount['account']."'";
			for($c=0;$c<count($fields);$c++)
			{
				if (substr($fields[$c],0,4) == 'date' && $aaccount[$fields[$c]]!='--')
				{
					$q .= ", ".$fields[$c] ."='".$aaccount[$fields[$c]]."'";
				}	
			}
			$q .= " where account_id='".$aaccount['account_id']."'";
			@pg_query($q);
		}
		/*
		if ($aaccount['account_code'] != '')
		{
			$cc = intval(substr($aaccount['account_code'],5,5));
			$q = "select * from cache where type='account_code' and value1='".$aaccount['branch_id']."'";
			$qqr = @pg_query($q) or message(pg_errormessage());
			$rr=@pg_fetch_object($qqr);
			if ($rr->value2 > $cc)
			{
				$q = "update cache set value2='$cc' where cache_id = '$rr->cache_id'";
				$qqr = @pg_query($q) or message(pg_errormessage());
			} 
		}
		*/
		message("Account Data Saved...");
	}
	if ($aaccount['pixfile_tmp'] != '')
	{
		$extension = $aaccount['pixfile_extension'];
		$picture_source = $aaccount['pixfile_tmp'];
		$picture_file = "images\account_".strtolower($aaccount['account_id']).".".strtolower($extension);
		$pix = "account_".strtolower($aaccount['account_id']).".".strtolower($extension);

		if (!copy($picture_source,$picture_file))
		{
			message("Unable to upload picture....".$picture_source." To ".$picture_file);
		}
		else
		{
			$aaccount['pix'] = $pix;
			$q = "update stock set pix='".$aaccount['pix']."' where account_id='".$aaccount['account_id']."'";
			$qr = @mysql_query($q) or message("Unable to update picture filename to database...");
			if ($qr) message("Picture file name updated...");
		}
	}
}
elseif ($p1 == 'disable')
{
		$audit = $aaccount['audit'].';Account Disabled by '.$ADMIN['username'].' '.date('m/d/Y g:ia');
		$q = "update account set enable='false', audit='$audit' where account_id='".$aaccount['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr && pg_affected_rows($qr)>0)
		{
			message('Account Disabled');
			$aaccount['enable'] = false;
			$aaccount['audit'] = $audit;
		}
}
elseif ($p1 == 'enable')
{
		$audit = $aaccount['audit'].';Account Enabled by '.$ADMIN['username'].' '.date('m/d/Y g:ia');
		$q = "update account set enable='true', audit='$audit' where account_id='".$aaccount['account_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr && pg_affected_rows($qr)>0)
		{
			message('Account Enabled');
			$aaccount['enable'] = true;
			$aaccount['audit'] = $audit;
		}
}
elseif ($p1 == 'Next')
{
	$q = "select * from account where account > '".$aaccount['account']."' order by account offset 0 limit 1";
	
	$qr = @pg_query($q);
	if (@pg_num_rows($qr) == 0)
	{
		message("End of file...");
	}	
	else
	{
		$aaccount = null;
		$aaccount = array();
		$r = fetch_assoc($q);
		$aaccount = $r;
	}	

}
elseif ($p1 == 'Previous')
{
	$q = "select * from account where account < '".$aaccount['account']."' order by account desc offset 0 limit 1";
	
	$qr = @pg_query($q);
	if (@pg_num_rows($qr) == 0)
	{
		message("Beginning of file...");
	}	
	else
	{
		$aaccount = null;
		$aaccount = array();
		$r = fetch_assoc($q);
		$aaccount = $r;
	}	

}

?>

<link rel="stylesheet" href="/tab-print.css" TYPE="text/css" MEDIA="print">

<form action="?p=account" method="post" enctype="multipart/form-data" name="f1" id="f1">
    <div class="row">
        <div class="col-lg-3" style="padding-left:7px">
            <input name="search" type="text" id="search" class="form-control form-control-sm" value="<?= $search;?>"  onKeypress="if(event.keyCode==13) {document.getElementById('go').click();return false;}" placeholder="Search">
        </div>
        <div class="col-lg-3">
            <?=lookUpAssoc('searchby',array('Name'=>'account','Account No.'=>'account_code','RecordId'=>'account_id',
							'Bank Account#'=>'bank_account'), $searchby, null, "form-control form-control-sm");?>
        </div>
        <div class="col-lg-3">
            <input name="p1" type="button" class="btn  btn-primary btn-sm" id="go" value="Go" onClick="window.location='?p=account.browse&p1=Go&search='+search.value+'&searchby='+searchby.value">
            <input type="button" name="Submit2" value="Add New" class="btn  btn-secondary btn-sm" onClick="window.location='?p=account&p1=New'">
            <input type="button" name="Submit23" value="Browse" class="btn  btn-secondary btn-sm" onClick="window.location='?p=account.browse'">
        </div>
        <div class="col-lg-3">
            <a href="javascript: f1.action='?p=account&p1=Previous';f1.submit()"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a>
            <a href="javascript: f1.action='?p=account&p1=Previous'; f1.submit()">Previous</a> | &nbsp;<a href="javascript: f1.action='?p=account&p1=Next';f1.submit()">Next</a>
            <a href="javascript: f1.action='?p=account&p1=Next';f1.submit()"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a>
        </div>
    </div>

    <div class="row">
        <table class="table">
          <tr valign="top"> 
            <td width="35%">Name<br>
              <input name="account" type="text" id="account" class="form-control form-control-sm" value="<?= stripslashes($aaccount['account']);?>" size="50" maxlength="50" onFocus="nextfield ='account_code'" onKeypress="if(event.keyCode==13) {document.getElementById('date_birth').focus();return false;}" onBlur="wait('Checking Account name...');xajax_uniqueAccount(xajax.getFormValues('f1'));" style="font-size:20">
              </td>
            <td width="15%">Account
              # <br>
              <input name="account_code"  type="text" id="account_code" class="form-control form-control-sm" value="<?= $aaccount['account_code'];?>" size="10" maxlength="10" onKeypress="if(event.keyCode==13) {document.getElementById('cardno').focus();return false;}" style="font-size:20"  onFocus="showToolTip(event,'Leave blank to auto-generate account number upon saving...');return false" onBlur="hideToolTip()">
            </td>
            <td width="1%">
                <br />
              <input name="p1" type="submit" id="p1" value="^" class="form-control form-control-sm" title="Click To Generate Account Number, OR  A New Account Number Will Be Generated If you leave it blank..." toogle="tooltip">
            </td>
            <td width="20%">Branch
              <br>
              <select name="branch_id" id="branch_id" class="form-control form-control-sm">
                <?
                    $q = "select * from branch where enable ";
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
                    if ($ADMIN['branch_id'] > '0') $q .= " order by branch";
                    if ($ADMIN['branch_id'] == '0') $q .= " order by branch";

                    $qr = @pg_query($q) or die(pg_errormessage());
                    while ($r = @pg_fetch_object($qr))
                    {
                        if ($aaccount['branch_id'] == $r->branch_id)
                        {
                            echo "<option value=$r->branch_id selected>$r->branch</option>";
                        }
                        elseif (($aaccount['branch_id'] == '' || $aaccount['branch_id'] == 0)  && $ADMIN['branch_id'] == $r->branch_id)
                        {
                            echo "<option value=$r->branch_id selected>$r->branch</option>";
                        }
                        elseif (($aaccount['branch_id'] == '' || $aaccount['branch_id'] == 0)  && $r->local== 't')
                        {
                            echo "<option value=$r->branch_id selected>$r->branch</option>";
                        }
                        else
                        {
                            echo "<option value=$r->branch_id>$r->branch </option>";
                        }
                    }

		            ?>
              </select>
              </td>
			<?

            if (chkRights2('account','mdelete',$ADMIN['admin_id']))
			{
                ?>
				<td width="10%">Status<br>
				  <?= lookUpAssoc('account_status',array('Active'=>'A','InActive'=>'I','Legal'=>'L'),$aaccount['account_status'],  null, "form-control form-control-sm");?>
				  </td>
				<td width="32%">Enable<br>
              	<?= lookUpAssoc('enable',array('Yes'=>'t','No'=>'f'),$aaccount['enable'], null, "form-control form-control-sm");?>
            	</td>
			<?
			} 
			elseif ($aaccount['account_status']=='A')
			{
			?>
				<td width="2%"><b>Status<br>
				  <?= lookUpAssoc('account_status',array('Active'=>'A','InActive'=>'I','Legal'=>'L'),$aaccount['account_status'], null, "form-control form-control-sm");?>
				  </b></td>
				<td width="32%">Enable<br>
				<input name="enable" type="hidden" id="enable" value="<?= $aaccount[enable];?>">
				<?	
				if ($aaccount[enable]=='t') echo " Yes";
				else echo "  No";
				?>
				</td>
				<?
			}
			else
			{
			?>
				<td width="2%"><b>Status<br>
				<input name="account_status" type="hidden" id="account_status" class="form-control form-control-sm" value="<?= $aaccount[account_status];?>">
			<?	
				if ($aaccount['account_status']=='L') echo " Legal ";
				elseif ($aaccount['account_status']=='I') echo " InActive ";
				elseif ($aaccount['account_status']=='A') echo " Active ";
			?>	
				</b></td>
				<td width="2%"><b>Enable<br>
				<input name="enable2" type="hidden" id="enable2" value="<?= $aaccount['enable'];?>">
			<?
				if ($aaccount[enable]=='t') echo " Yes ";
				else echo "  No";
			?>				
				</b></td>
			<?		
			}
            ?>
              <input name="account_id" type="hidden" id="account_id" value="<?= str_pad($aaccount['account_id'],8,'0',STR_PAD_LEFT);?>" size="12" maxlength="12" style="text-align:center; border:0; background-color:#EFEFEF; padding:0;" readOnly >
          </tr>
        </table>
    </div>

    <div style="padding-left:9px;">
        <div class="tab-container">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#personal-info" data-toggle="tab">Personal Info</a></li>
                <li><a href="#other-info" data-toggle="tab" >Other Info</a></li>
                <li><a href="#account-info" data-toggle="tab" >Account</a></li>
                <li><a href="#account-loan" data-toggle="tab" >Loans</a></li>
                <li><a href="#account-images" data-toggle="tab" >Images</a></li>
                <li><a href="#account-audit" data-toggle="tab" >Audit</a></li>
            </ul>

            <div class="tab-content" style="padding:10px 5px;min-height:400px;">
                <div class="tab-pane active" id="personal-info">
                    <?include_once('account.personal.php');?>
                </div>
                <div class="tab-pane fade" id="other-info">
                    <?include_once('account.other.php');?>
                </div>
                <div class="tab-pane fade" id="account-info">
                    <?include_once('account.account.php');?>
                </div>
                <div class="tab-pane fade" id="account-loan">
                    <?include_once('account.loan.php');?>
                </div>
                <div class="tab-pane fade" id="account-images">
                    <?include_once('account.images.php');?>
                </div>
                <div class="tab-pane fade" id="account-audit">
                    <?=$aaccount['audit'];?>
                </div>

            </div>
        </div>
    </div>
    <div>
      <a accesskey="S" href="javascript: f1.action='?p=account&p1=Save';f1.submit();">
          <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" name="Save" width="57"  border="0" id="Save" onClick="f1.action='?p=account&p1=Save';f1.submit();" tabIndex="99">
      </a>
      <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print"   accesskey="P">
      <input type='image' name="New" id="New" onClick="f1.action='?p=account&p1=New';f1.submit();"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20" accesskey="N">
    </div>