<script>
function vChk(t)
{
	var id = t.id
	var n = id.substring(1)
	var mid = eval("this.form1.m"+n)
	mid.checked = true
}
</script>
<?

$href = '?p=payinsure';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2('releasing','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Pay Insurance...");
	exit;
}

if (!session_is_registered('apayinsure'))
{
	session_register('apayinsure');
	$apayinsure=array();
}
$provinces=array();

$q = "select * from bankcard where enable order by bankcard";
$qr = @pg_query($q) or message(pg_errormessage());
$provinces['NONE'] = '0';
while ($r = @pg_fetch_object($qr))
{
	$provinces[$r->bankcard] = $r->bankcard_id;
}

if ($year==0 or $year=='') $year=date("Y");
if ($p1=="Pay Checked" && !chkRights2("releasing","madd",$ADMIN['admin_id']))
{
	message("You have no permission to modify or add...");
}
if ($province_id==0 and $p1 != '')
{
	msgBox("Please specify partner");
}
elseif ($p1=="Print")
{
	if ($month <12) $dd = $year.'-'.str_pad($month+1,2,'0',STR_PAD_LEFT).'-01';
	else 
	{
		$yr = $year+1;
		$dd = $yr.'-01-01';
	}	
	$dated = date('Y-m-d',strtotime ('-1 day' , strtotime ( $dd)));
	$header = "DATE  : _____________________\n\nTO    : The Manufacturers Life Insurance Co. (Phils.), Inc.\n\n".
			  "FROM  : ".$SYSCONF['BUSINESS_NAME'].space(50).'    POLICY NUMBER : '.$SYSCONF['INSURE_POLICY']."\n".
			  "              Name of Assured\n\n".center("SUMMARY SHEET",138)."\n\n".
			  "Attached are the individual application for Creditors Group Life Insurance of the following debtors:\n\n";
	$header .= str_repeat('-',138)."\n";
	$header .= "    Loan                                                      Birth     Effective   Civil   Amount          Net Premium  Service  Gross\n";
	$header .= "No. Release#      Account Name                        Gender   Date       Date     Status   of Loan  Term ||    Paid       Fee    Premium\n";		
	$header .= str_repeat('-',138)."\n";
	$ctr=0;  
	$qa = "select
					ins.releasing_id,
					ins.account_id,
					ins.debit as due,
					ins.apterm,
					ins.apmonth,
					ins.termbal,
					rel.principal,  
					rel.term,
					acc.account,
					acc.branch_id,
					acc.date_birth,
					acc.civil_status,
					acc.gender
				from 
					insurance as ins, releasing as rel, account as acc 
				where 
					reference = '$reference' and
					rel.releasing_id = ins.releasing_id and
					acc.account_id = ins.account_id
				order by
					acc.branch_id, acc.account";
	$qra = pg_query($qa) or message("Error querying payroll master data...".pg_errormessage().$qa);
	$bid = $due_branch = $due_total = 0;
	while ($ra = pg_fetch_object($qra))
	{
		$ctr++;
		if ($bid != $ra->branch_id)
		{
			if ($due_branch != 0)
			{
				$detail .= space(120)."------------\n";
				$detail .= adjustRight(number_format($due_branch,2),12)."\n";
				$due_total += $due_branch;
				$due_branch = 0;
			}
			$detail .= ($bid!=0?"\n\n":'').lookUpTableReturnValue('x','branch','branch_id','branch',$ra->branch_id)."\n";
			$bid = $ra->branch_id;
		}
		$acct = explode('/',$ra->account);
		$term = $ra->apterm; //($ra->term > 12?12:$ra->term);
		$netdue = ((round(($ra->principal/1000) * $ra->term * .56,2) + round(($ra->principal/1000)*$ra->term*.1,2)) / $ra->term)*$term;
		$net = (round(($ra->principal/1000) * $ra->term * .56,2) / $ra->term)*$term;
		$scharge = (round(($ra->principal/1000)*$ra->term*.1,2) / $ra->term)*$term;
		
		$detail .= adjustRight($ctr,2).'. '.adjustSize($ra->releasing_id,8).' '.adjustSize($acct[0],40).' '.
					($ra->gender=='M'?'Male':'Female').' '.ymd2mdy($ra->date_birth).' '.ymd2mdy($dated).' '.
					($ra->civil_status=='S'?'Single':($ra->civil_status=='M'?'Maried':'Widow')).' '.
					adjustRight(number_format($ra->principal,2),11).' '.adjustRight($term,3).
					'  || '.adjustRight(number_format($net,2),8).'  '.
					adjustRight(number_format($scharge,2),8).' '.
					adjustRight(number_format($netdue,2),9)."\n";
		$netpay += $net;
		$serfee += $scharge;
		$grossp += $netdue;
		$net_branch += $netdue;
	} 
	$detail .= space(99)."------------ ------------ ------------\n";
	$detail .= space(125).adjustRight(number_format($due_branch,2),12)."\n";
	$detail .= str_repeat('-',138)."\n";
	$detail .= space(80)."<b> TOTAL ====>".space(17).adjustRight(number_format($netpay,2),8).'  '.
					adjustRight(number_format($serfee,2),8).' '.
					adjustRight(number_format($grossp,2),9)."</b>\n";
	$detail .= str_repeat('-',138)."\n";

	$prtform = "<font style='font-family:monospace;line-height:120%;font-size:100%;'>".$header.$detail."</font>";

	echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$prtform.'"'.">";
	echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
	echo "</iframe>";
	echo "<script>printIframe(print_area)</script>"; 

}
elseif ($p1=="Mark For Payment")
{
	$ctr=$lc=0;
	if ($month <12) $dd = $year.'-'.str_pad($month+1,2,'0',STR_PAD_LEFT).'-01';
	else 
	{
		$yr = $year+1;
		$dd = $yr.'-01-01';
	}	
	$payperiod = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT);
	$dated = date('Y-m-d',strtotime ('-1 day' , strtotime ( $dd)));
	$dt = date("Y-m-d");
	$payrefer = date("Ym");
	$q = "select reference, apmonth, apterm, termbal from insurance where substr(reference,1,6)='$payrefer' order by reference DESC";
	$qr = pg_query($q) or message(pg_errormessage());
	$r = pg_fetch_object($qr);
	if (substr($r->reference,0,6) == $payrefer)
	{
		$sub = substr($r->reference,6,2)*1;	
		$sub++;
		$payrefer = $payrefer.str_pad($sub,2,'0',STR_PAD_LEFT); 
	} else $payrefer = $payrefer.'00';

	$header = "DATE  : _____________________\n\nTO    : The Manufacturers Life Insurance Co. (Phils.), Inc.\n\n".
			  "FROM  : ".$SYSCONF['BUSINESS_NAME'].space(50).'    POLICY NUMBER : '.$SYSCONF['INSURE_POLICY']."\n".
			  "              Name of Assured\n\n".center("SUMMARY SHEET",138)."\n\n".
			  "Attached are the individual application for Creditors Group Life Insurance of the following debtors:\n\n";
	$header .= str_repeat('-',138)."\n";
	$header .= "    Loan                                                      Birth     Effective   Civil   Amount          Net Premium  Service  Gross\n";
	$header .= "No. Release#      Account Name                        Gender   Date       Date     Status   of Loan  Term ||    Paid       Fee    Premium\n";		
	$header .= str_repeat('-',138)."\n";
	$ctr=0;  
	$bid=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($insurance_id[$c]!='')
		{
			if ($bid != $branchid[$c])
			{
				if ($due_branch != 0)
				{
					$detail .= space(60)."------------\n";
					$detail .= adjustRight(number_format($due_branch,2),12);
					$due_total += $due_branch;
				}
				$bid = $branchid[$c];
				$detail .= ($bid!=0?"\n\n":'').lookUpTableReturnValue('x','branch','branch_id','branch',$bid)."\n";
			}
			$dues = str_replace(',','',$due[$c]);
			$netdue =  round(($loan[$c]/1000) * $term[$c] * .56,2) + round(($loan[$c]/1000)*$term[$c]*.1,2); //   round(($loan[$c]/1000) * $term[$c] * .4,2) / $term[$c];
			pg_exec("update insurance set
						reference='$payrefer', debit = '$dues'
				where
						insurance_id='".$insurance_id[$c]."'") or message (pg_errormessage());

			$insdata = fetch_assoc("select apmonth, apterm, termbal, credit, debit from insurance where insurance_id='".$insurance_id[$c]."'") or message (pg_errormessage());

			if ($insdata[termbal] > 0)
			{
				$aLoan = fetch_assoc("select * from releasing where releasing_id='".$releasing_id[$c]."'") or message (pg_errormessage());
	
				$tb = $insdata['termbal']-12;
				$insdata[crdbal] = $insdata[credit] - $insdata[debit];
				$termbal = ($tb >= 0?$tb:'0');
				$apterm  = ($tb >= 0?'12':$tb);
				$yr1 = substr($insdata[apmonth],0,4);
				$mo1 = substr($insdata[apmonth],5,2);
				$mo = $mo1;
				$yr = $yr1+1;
				$apmonth = $yr.'-'.str_pad($mo,2,'0',STR_PAD_LEFT);
				$insureamt = $insdata[credit] - $dues;
								 	
				pg_exec("insert into insurance (releasing_id, status, credit, account_id, date, apmonth, apterm, termbal, admin_id) 
						 values ('".$aLoan['releasing_id']."', 'A', '$insureamt', '".$aLoan['account_id']."','".$aLoan['date']."',
								 '$apmonth', '$apterm', '$termbal', '".$ADMIN['admin_id']."')") or message (pg_errormessage());
			}
/*echo "update insurance set
						reference='$payrefer', debit = '$dues'
				where
						insurance_id='".$insurance_id[$c]."'<br>";*/						

			$lc++;
			$qa = "select * from account where account_id = '".$account_id[$c]."'";
			$loanamt = str_replace(',','',$loan[$c]);
			$qra = pg_query($qa) or message("Error querying payroll master data...".pg_errormessage().$qa);
 			$ra = pg_fetch_object($qra);
			$acct = explode('/',$account[$c]);
			$detail .= adjustRight($ctr+1,2).'. '.adjustSize($releasing_id[$c],8).' '.adjustSize($acct[0],40).' '.
						($ra->gender=='M'?'Male':'Female').' '.ymd2mdy($ra->date_birth).' '.ymd2mdy($dated).' '.
						($ra->civil_status=='S'?'Single':($ra->civil_status=='M'?'Maried':'Widow')).' '.
						adjustRight(number_format($loanamt,2),11).' '.adjustRight($term[$c],3).
						'  || '.adjustRight(number_format($netdue,2),8).'  '.
						adjustRight(number_format($dues - $netdue,2),8).' '.
						adjustRight(number_format($dues,2),8)."\n";
		}
		$ctr++;
	} 
	$detail .= str_repeat('-',138)."\n";
	$prtform = "<font style='font-family:monospace;line-height:120%;font-size:100%;'>".$header.$detail."</font>";

	echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$prtform.'"'.">";
	echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
	echo "</iframe>";
	echo "<script>printIframe(print_area)</script>"; 
}
?>
<form name="form1" id="form1" method="post">
  <table width="81%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td nowrap><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
        <?=lookUpMonth('month',$month);?>
        &nbsp;
        <input id="year" name="year" type="text" value="<?=$year;?>" size="4" maxlength="4"/>
        <select name = "province_id" id = "province_id" onChange="document.getElementById('form1').action='?p=payinsure';document.getElementById('form1').submit();">
          <?
				$q = "select * from bankcard where enable ";
          		echo "<option value=''>".'Select Partner'."</option>";
	      
				$q .= "order by bankcard";
				
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
        <select name = "branch_id">
            <?
				$q = "select * from branch where enable ";
				if ($ADMIN['branch_id'] > '0')
				{
	                echo "<option value=''>Select Branch</option>";
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
				$q .= "order by branch";
				
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($province_id != '' and $province_id != $r->province) continue; 		
									
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
          <input name="p1" type="submit" id="p1" value="Go">
        <input name="p1" type="submit" id="p1" value="List">
        <input name="p1" type="submit" id="p1" value="Print" /><input name="reference" type="text" value="<?=$reference;?>" size="10" />
        <input name="p1" type="button" id="p1" onClick="window.location='?p='" value="Close">
        </font>
        <hr color="#CC3300">
      <font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; </font></td>
    </tr>
  </table>
  <table width="81%" border="0" cellspacing="1" cellpadding="1" bgcolor="#EFEFEF" align="center">
    <tr bgcolor="#C8D7E6" background="../graphics/table_horizontal.PNG"> 
      <td height="27" colspan="9"  background="../graphics/table_horizontal.PNG"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
        <img src="../graphics/mail.gif" width="16" height="17"> Insurance Payment
        <a name="top"></a></b></font></td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td width="9%"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">#</font></b></td>
      <td width="35%" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">NAME OF ACCOUNT</font></b></td>
      <td width="12%" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">ACCOUNT GROUP</font></b></td>
      <td width="9%" nowrap align="center"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">LOAN</font></b></td>
      <td width="10%" align="center" nowrap><b><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">INSURANCE</font></b></td>
      <td width="7%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">TERM</font></b></td>
      <td width="9%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">DUE</font></b></td>
      <td width="9%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">BALANCE</font></b></td>
      <td width="9%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">REFERENCE</font></b></td>
    </tr>
	<?
	$dt = date("Y-m-d");
	$payrefer = date("Ym");
	$q = "select reference from insurance where substr(reference,1,6)='$payrefer' order by reference DESC";
	$qr = pg_query($q) or message(pg_errormessage());
	$r = pg_fetch_object($qr);
	if (substr($r->reference,0,6) == $payrefer)
	{
		$sub = substr($r->reference,6,2)*1;	
		$sub++;
		$payrefer = $payrefer.str_pad($sub,2,'0',STR_PAD_LEFT); 
	} else $payrefer = $payrefer.'00';
	
	if ($p1=='List') 
	{
		$start=0;
		$xSearch='';
	}	
	if ($start == '') $start=0;
	if ($p1=='Next') $start = $start + 10;
	if ($p1=='Previous') $start = $start - 10;
	if ($start < 0) $start=0;	
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	$payperiod = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT);
	if ($month <12) $date_to = $year.'-'.str_pad($month+1,2,'0',STR_PAD_LEFT).'-01';
	else 
	{
		$yr = $year+1;
		$date_to = $yr.'-01-01';
	}	
	$q = "select sum(debit) as debit, sum(credit) as credit, insurance.releasing_id, account.branch_id, account.account, account.account_group_id, insurance.reference, 
					insurance.insurance_id, apmonth, apterm, termbal
			from
				insurance, account
			where
				account.account_id = insurance.account_id and
				insurance.status != 'C' and (insurance.apmonth = '$payperiod' or 
				(insurance.apmonth < '$payperiod' and (reference='' or reference ISNULL)))";
				
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id'";
		$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	}
	else
	{
		$branch = '';
	}
	$q .= " 	group by insurance.releasing_id, account.branch_id, account.account, account.account_group_id, insurance.reference, insurance.insurance_id
			    order by branch_id, account";
	$qr = pg_query($q) or message(pg_errormessage());
	$ctr = 0;
	
	while ($r = pg_fetch_assoc($qr))
	{
		if ($province_id != '')
		{
			$province=lookUpTableReturnValue('x','branch','branch_id','province',$r['branch_id']);
			if ($province_id != $province) 	continue;
		}
		$ctr++;
		$accountgroup=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r[account_group_id]);
		if (substr($accountgroup,0,8)=='Deceased')  continue;
				
		$qu = "select * from releasing where releasing_id='".$r['releasing_id']."'";
		$qur = pg_query($qu) or message(pg_errormessage());
		$ru = pg_fetch_object($qur);
		if ($r['reference'] !='' ) 
		{
			$due = $r['debit'];
			$bal = $r['credit']-$r['debit'];
		}	
		elseif ($r[termbal]==0) 
		{
			$due = $r['credit'];
			$bal = 0.00;
		}	
		else 
		{
			$due = ($r['credit']/($r['termbal']+$r['apterm'])) * $r['apterm'];
			$bal = $r['credit'] - $due;
		}	
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap><font size=1> 
        <input type="hidden" name="account_id[]" size="5" value="<?= $ru->account_id;?>">
        <input type="hidden" name="releasing_id[]" size="5" value="<?= $ru->releasing_id;?>">
        <input type="hidden" name="insurance_id[]" size="5" value="<?= $r['insurance_id'];?>">
        <input type="hidden" name="branchid[]" size="5" value="<?= $r['branch_id'];?>">
        <? 
 	    echo "$ctr."; 
		if ($r['reference'] == '') echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
 	    ?>
        </font> </td>
      <td> <input name="account[]" readonly type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r['account'];?>" size="60"> </td>
      <td> <input name="accountgroup[]" readonly type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $accountgroup;?>" size="30"> </td>
      <td align="center"><input name="loan[]" type="text" readonly id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=number_format($ru->principal,2);?>" size="13" style="text-align:right"> 
      </td>
      <td align="center"><input name="credit[]" type="text" readonly id="<?='k'.$ctr;?>"   value="<?=number_format($r['credit'],2);?>"  size="13" style="text-align:right"></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="term[]" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= $r['apterm'];?>" size="5" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="due[]" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= number_format($due,2);?>" size="12" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="bal" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= number_format($bal,2);?>" size="12" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="reference" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?=$r['reference'];?>" size="10" style="text-align:left">
        </font></td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="4"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="submit" name="p1" value="Mark For Payment">
        Reference# <?=$payrefer;?></font> </td>
      <td colspan="5"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
       </font> </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=clientbank&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=clientbank&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
