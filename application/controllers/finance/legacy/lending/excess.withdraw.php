<script>
/*
	periodic report summary of withdrawl per bank
   Bank     Total Active ATMs   Total ATMs Withdwn   Total Amount Withdrawn
   --------- ----------------   ------------------- -----------------------
   
   branch       ''                     ''                  ''
   ---------  -----------------
*/
function vMo()
{
	var aM =	new Array("January","February","March","April","May","June","July","August","September","October","November","December","13th Month")
	var starting_month = parseInt(document.getElementById('starting_month').value*1);
	var mo=0;
	var m='';
	
	for (c=0;c<13;c++)
	{
		mo = (starting_month+c-1)%13;
		m = c+1
		document.getElementById('m'+m).value=aM[mo];
	}
}
function vAmt()
{

	var aM =	new Array("January","February","March","April","May","June","July","August","September","October","November","December")

	refund_amount = parseFloat(document.getElementById('refund_amount').value*1);
	ps_amount = parseFloat(document.getElementById('ps_amount').value*1);
	charges = parseFloat(document.getElementById('charges').value*1);
	
	if (charges == 0 && ps_amount >0)
	{
		if (ps_amount > 5000)
			charges = 100;
		else charges = 50;	
	}

	month1 = parseFloat(document.getElementById('month1').value *1);
	month2 = parseFloat(document.getElementById('month2').value *1);
	month3 = parseFloat(document.getElementById('month3').value *1);
	month4 = parseFloat(document.getElementById('month4').value *1);
	month5 = parseFloat(document.getElementById('month5').value *1);
	month6 = parseFloat(document.getElementById('month6').value *1);
	month7 = parseFloat(document.getElementById('month7').value *1);
	month8 = parseFloat(document.getElementById('month8').value *1);
	month9 = parseFloat(document.getElementById('month9').value *1);
	month10 = parseFloat(document.getElementById('month10').value *1);
	month11 = parseFloat(document.getElementById('month11').value *1);
	month12 = parseFloat(document.getElementById('month12').value *1);
	month13 = parseFloat(document.getElementById('month13').value *1);

	gross_amount = refund_amount +ps_amount+ month1 + month2 + month3 + month4 + month5 + month6 + month7 + month8 + month9 + month10 + month11 + month12 + month13;
	rate  = parseFloat(document.getElementById('rate').value *1)/100;
	minterest = 0

//		interest += month1*rate + month2*rate*2 + month3*rate*3 + month4*rate*4 + month5*rate*5 + month6*rate*6 + month7*rate*7 + month8*rate*8;
	var cmonth;
	var c=0;
	var multiplier = 0
	for (c=1;c<=13;c++)
	{
		cmonth = 	document.getElementById('m'+c).value;
		monthfld = eval('month'+c)
		multiplier++;
		if (cmonth == "13th Month")
		{
			multiplier--;
			minterest += monthfld*rate *(multiplier)
		}
		else
		{
			minterest += monthfld*rate*multiplier
		}

	}

	minterest += month13*rate*multiplier	
	net_amount = gross_amount - minterest - charges;
//	alert('n '+net_amount+' g '+gross_amount+' i'+minterest+' c '+charges);
	document.getElementById('net_amount').value = twoDecimals(net_amount);
	document.getElementById('gross_amount').value = twoDecimals(gross_amount);
	document.getElementById('interest').value = twoDecimals(minterest);
	document.getElementById('charges').value = twoDecimals(charges);
}
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Transaction Record?"))
		{
			document.getElementById('f1').action="?p=excess.withdraw&p1=CancelConfirm"
		}	
		else
		{
			document.getElementById('f1').action="?p=excess.withdraw&p1=Cancel"
		}
	}
	else if (ul.name == 'Restore')
	{
		if (confirm("Are you sure to RESTORE Transaction Record?"))
		{
			document.getElementById('f1').action="?p=excess.withdraw&p1=RestoreConfirm"
		}	
	}	
	else
	{
		document.getElementById('f1').action="?p=excess.withdraw&p1="+ul.id;
	}	
	document.getElementByid('f1').submit();
}
</script>
<form action="" method="post" name="f1" id="f1">
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td background="../graphics/table0_horizontal.PNG" bgcolor="#003366"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        :: Excess Withdrawal/Advances ::</strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>"  onKeypress="if(event.keyCode == 13){document.getElementById('SearchButton').focus();return false;}">
        <?=lookUpAssoc('searchby',array('Name'=>'account','Account No.'=>'account_code','RecordId'=>'account_id'),$searchby);?>
        <select name = "account_group_id" id= "account_group_id" style="width:160px" >
          <option value=''>All Account Groups</option>
          <?
		  		if ($p1 == '') $account_group_id = 546;

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
      </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="p1" type="submit" id="p1" value="Search" />
        <input name="p12" type="button" id="p12" value="Browse" onClick="window.location='?p=wexcess.browse'">
        <input name="p1" type="button" id="p1" value="New" onClick="window.location='?p=wexcess.browse'">
        <input name="p122" type="button" id="p122" value="Close" onClick="window.location='?p='">
      </font></td>
    </tr>
    <tr> 
      <td><hr color="#BBBBEE" size="1"></td>
    </tr>
  </table>

<?
if (!chkRights2('excessamount','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...!");
	return;
}

	if (!session_is_registered('aExcess'))
	{
		session_register('aExcess');
		$aExcess = null;
		$aExcess = array();
		$transdate=date('Y-m-d');
	}
	
	$fields = array('period_advance', 'rate', 'interest', 'period_excess', 'refund_remark', 'refund_amount',  'net_amount', 
					'charges', 'charges_remark','gross_amount', 'remarks','date','starting_month','month1','month2','month3',
					'month4','month5','month6','month7','month8','month9','month10','month11','month12','month13',
					'ps_amount','ps_remark','printx','cashier_id','account_group_id');
	if (!in_array($p1, array('', 'Edit', 'Load', 'selectAccount')))
	{
		for ($c = 0; $c<count($fields) ; $c++)
		{
		
			if (substr($fields[$c] ,0,4) == 'date')
			{
				$aExcess[$fields[$c]] = mdy2ymd($_REQUEST[$fields[$c]]);
			}
			else
			{
				if ( !in_array($fields[$c] , array('remarks')) && $_REQUEST[$fields[$c]] == '')
				{
					$aExcess[$fields[$c]] = 0;
				}
				else
				{
					$aExcess[$fields[$c]] = $_REQUEST[$fields[$c]];
				}
			}
		}
	}
	$qbranch_id = $_REQUEST['qbranch_id'];
/*	if ($p1 == 'Select')
	{
		include_once('wexcess.smarta.php');	
	}
	if ($pin != '' and $pin != null)
	{
		$q = "select * from account where smartno ='$pin'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qr) == '0')
		{
			message("Pensioner Record NOT Found...");
			exit;
		}
		$r = @pg_fetch_assoc($qr);
		$p1='selectAccount';
		$c_id=$r['account_id'];	
	}*/
	if ($p1 == 'New' or ($p1 == '' && count($aExcess) == 0))
	{
		$aExcess = null;
		$aExcess = array();
		$aExcess['date'] = date('Y-m-d');
		$aExcess['starting_month'] = date('m')+1;
		$transdate=date('Y-m-d');
		if ($aExcess['starting_month'] > 12) $aExcess['starting_month']-1;
		$focus = 'xSearch';
	}
	elseif ($p1 == '?')
	{
		$aExcess[month1] = $aExcess[month2] = $aExcess[month3] = $aExcess[month4] = $aExcess[month5] = $aExcess[month6] = 0;
		$aExcess[month7] = $aExcess[month8] = $aExcess[month9] = $aExcess[month10] = $aExcess[month11] = 0;
		$aExcess[month12] = $aExcess[month13] = $aExcess[gross_amount] = $aExcess[interest] = 0;
		$aExcess[net_amount] = $aExcess[gross_amount] - $aExcess[charges];
	}
	elseif ($p1 == 'Load' && $id != '')
	{
		$q = "select * from wexcess where wexcess_id = '$id'";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			return;
		}
		$r = @pg_fetch_assoc($qr);
		
		$aExcess = null;
		$aExcess = array();
		$aExcess = $r;
		
		$transdate=$aExcess['date'];
		
		$q = "select * 
					from 
						account
					where
						account_id ='".$aExcess['account_id']."'";
						
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
		$aExcess += $r;

		if ($aExcess['clientbank_id'] > '0')
		{
			$aExcess['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aExcess['clientbank_id']);
		}
		if ($aExcess['branch_id'] > '0')
		{
			$aExcess['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aExcess['branch_id']);
		}
		$aExcess['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aExcess['admin_id']);

		$focus = 'period_advance';		
	}
	elseif ($p1 == 'Save' && $aExcess['account_id'] == '')
	{
		message("No Account Specified...");
	}
	elseif ($p1 == 'Save' &&  $aExcess['wexcess_id'] == '' && !chkRights2('excessamount','madd',$ADMIN['admin_id']))
	{
		message("You have no permission to Add New Transaction in this area...");
	}
	elseif ($p1 == 'Save' &&  $aExcess['wexcess_id'] != '' && !chkRights2('excessamount','medit',$ADMIN['admin_id']))
	{
		message("You have no permission to Update/Modify Transaction in this area...");
	}
	elseif ($p1 == 'Save' && ($transdate!=date('Y-m-d') or $aExcess['date']!=date('Y-m-d')) && $ADMIN['usergroup'] != 'A')
	{
		message('transdate '.$transdate.'  Excess date : '.$aExcess['date']."  You have no permission to Update/Modify Transaction in this area...");
	}
	elseif ($p1 == 'Save' and $aExcess['status'] == 'P' && !chkRights2('excessadmin','mdelete',$ADMIN['admin_id']))
	{
		message("No modification is allowed after this has been printed...");
	}	
	elseif ($p1 == 'Save' )
	{
		$aExcess[account_group_id] = lookUpTableReturnValue('x', 'account','account_id', 'account_group_id', $aExcess['account_id']);
		if ($aExcess['wexcess_id'] == '')
		{
			$aExcess['audit'] = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';

			$q = "insert into wexcess ( audit, account_id, admin_id, tbranch_id ";
			$qq .= ") values ('".$aExcess['audit']."', '".$aExcess['account_id']."','".$ADMIN['admin_id']."','".$ADMIN['branch_id']."'";
			for ($c=0;$c<count($fields);$c++)
			{
				$q .= ",".$fields[$c];
				$qq .= ",'".$aExcess[$fields[$c]]."'";
			}
			$q .= $qq.")";
			$qr = @pg_query($q) or message(pg_errormessage());
			if ($qr)
			{
				$qr = query("select currval('wexcess_wexcess_id_seq'::text)");
				$r = pg_fetch_object($qr);
				$aExcess['wexcess_id'] = $r->currval;
				message("Transaction Saved...");
				$aExcess['status'] = 'S';
			}
		}
		else
		{
			$aExcess['audit'] .= 'Updated by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';

			$q = "update wexcess set account_id = '".$aExcess['account_id']."',audit = '".$aExcess['audit']."'";
			for ($c=0;$c<count($fields);$c++)
			{
				$q .= ", ".$fields[$c]." = '".$aExcess[$fields[$c]]."'";
			}
			$q .= " where wexcess_id = '".$aExcess['wexcess_id']."'";
			
			$qr = @pg_query($q) or message(pg_errormessage());
			if ($qr)
			{
				message("Transaction Updated...");
			}
		}
	}
	elseif ($p1 == 'RestoreConfirm')
	{
		$aExcess['audit'] .= 'Restored by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$q = "update wexcess set status='P',audit='".$aExcess['audit']."' where wexcess_id = '".$aExcess['wexcess_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr)
		{
			message("Transaction Restored...");
		}
	}
	elseif ($p1 == 'CancelConfirm' && chkRights2('excessamount','mdelete',$ADMIN['admin_id']))
	{
		$userg = lookUpTableReturnValue('x', 'admin','admin_id', 'usergroup', $ADMIN['admin_id']);
		if (($aExcess['status'] == 'P' and $aExcess['date']!=date('Y-m-d')) and $userg !='A')
		{
			message("Cancel not allowed");
		} else
		{ 
//if ($_SERVER['REMOTE_ADDR']=='192.168.1.13') exit;		
			$aExcess['audit'] .= 'Canceled by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
			$q = "update wexcess set status='C',audit='".$aExcess['audit']."' where wexcess_id = '".$aExcess['wexcess_id']."'";
			$qr = @pg_query($q) or message(pg_errormessage());
			if ($qr)
			{
				message("Transaction Cancelled...");
			}
		}
	}
	elseif ($p1 == 'selectAccount' && $c_id != '')
	{
		$q = "select * 
					from 
						account
					where
						account_id ='$c_id'";
						
		$qr = @pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qr) == '0')
		{
			message("Account NOT Found...");
		}
		$r = @pg_fetch_assoc($qr);
		if ($r['smartno'] != '' and $r['branch_id']=='' and $pin=='')
		{
			$aExcess = null;
			$aExcess = array();
//			message("Pensioner has a JGM ID please use SELECT ...");

            header("Location: /lending/?p=excess.withdraw&p1=Select");
            exit;

		}
		else
		{
			$aExcess = null;
			$aExcess = array();
			$aExcess = $r;
			$aExcess['date'] = date('Y-m-d');
			$transdate = date('Y-m-d');
			$aExcess['admin_id'] = $ADMIN['admin_id'];
			
			include_once('accountbalance.php');
		
			$aBal = excessBalance($aExcess['account_id']);
			$aExcess['balance'] = $aBal['balance'];
	
			if ($aExcess['clientbank_id'] > '0')
			{
				$aExcess['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aExcess['clientbank_id']);
			}
			if ($aExcess['branch_id'] > '0')
			{
				$aExcess['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aExcess['branch_id']);
			}
	
			$q = "select date from wexcess where account_id = '".$aExcess['account_id']."' and status!='C' order by date desc offset 0 limit 1";
			$qr = @pg_query($q) or message(pg_errormessage());
			$r = @pg_fetch_object($qr);
			$aExcess['last_takeout']  = $r->date;
	
			$q = "select excess as period_excess from payment_detail  where account_id = '".$aExcess['account_id']."'  order by payment_header_id desc offset 0 limit 1";
			$qr = @pg_query($q) or message(pg_errormessage());
			$r = @pg_fetch_object($qr);
			$aExcess['period_excess']  = $r->period_excess;
			
			$aExcess['net_amount'] = $aExcess['balance'];
			$aExcess['gross_amount'] = $aExcess['balance'];
			$aExcess['remarks'] = '';
			
			$aExcess['rate'] = 6;
			$aExcess['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aExcess['admin_id']);
	
			$focus = 'period_advance';
		
		/*
		if ($aExcess['withdraw_day'] <= date('d'))
		{
			$starting_month = date('m')+1;
			if ($starting_month > 12) $staring_month =1;
		}
		else
		{
			$starting_month = date('m');
		}
		$aExcess['starting_month'] =$starting_month;
		*/

			$q = "select * from wexcess where account_id = '".$aExcess['account_id']."'  and status!='C' order by date desc  offset 0 limit 1";
			$qr = @pg_query($q) or message(pg_errormessage());
			$r = @pg_fetch_assoc($qr);

			$myear = substr($r['date'],0,4);
			$year_flag = $months = 0;
			for ($cc = 1 ; $cc< 13; $cc++)
			{
				$mc = $r['starting_month'] + $cc ;
				
				if ($mc > 13)
				{
						$mc -= 13;

						if ($year_flag == '0')
						{
							$myear++;
							$year_flag = 1;
						}
				}
				$field = 'month'.$cc;
				if ($r[$field] == 0.00)
				{
					break;
				}

			}
			$starting_month = $mc-1;
			$aExcess['starting_month'] = $starting_month;
			
			$q = "select sum(ammort) as total_ammort from releasing where balance>0 and status!='C' and account_id = '".$aExcess['account_id']."'";
			$qre = @pg_query($q) or message(pg_errormessage().$q);
			$re = @pg_fetch_object($qre);
	
			if ($re->total_ammort >= $aExcess['salary'] && $aExcess['balance'] <=0)
			{
				message("[ Total Ammortization (P ".number_format($re->total_ammort,2).") is Greater than or Equal to Salary/Pension (P".number_format($aExcess['salary'],2).") ... Cannot Issue Advance Change...]");
				return;
			}
			$aExcess['total_ammort'] = $re->total_ammort;
		}	
	}
	elseif ($p1 == 'Print' && !in_array($aExcess['status'], array('P','S')))
	{
		message("Save Transaction First Before Printing...");
	}
	elseif ($p1 == 'Print' or $p1 == 'PrintForm' or $p1 == 'PrintForm2')
	{
		$q = "select * from wexcess where wexcess_id = '".$aExcess['wexcess_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			return;
		}
		$r = @pg_fetch_assoc($qr);
		
		$aExcess = null;
		$aExcess = array();
		$aExcess = $r;
		
		$q = "select * 
					from 
						account
					where
						account_id ='".$aExcess['account_id']."'";
						
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
		$aExcess += $r;

		if ($aExcess['clientbank_id'] > '0')
		{
			$aExcess['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aExcess['clientbank_id']);
		}
		if ($aExcess['branch_id'] > '0')
		{
			$aExcess['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aExcess['branch_id']);
		}
		$aExcess['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aExcess['admin_id']);
		
		$details = '<small3>';
		$detailsn ="\n\n\n\n\n";
		$details .= $SYSCONF['BUSINESS_NAME']."\n".	
				$SYSCONF['BUSINESS_ADDR'].'    Tel. No.: '.$SYSCONF['BUSINESS_TEL']."\n";
		$details .= space(50).'RELEASE No. '.str_pad($aExcess['wexcess_id'],8,'0',STR_PAD_LEFT)."\n";
		$detailsn .= 'RELEASE No. '.str_pad($aExcess['wexcess_id'],8,'0',STR_PAD_LEFT)."  ";
		$aExcess['printx']+= 1;
		$details .= 'Date & Time Printed : '.date('m/d/Y g:ia').'    Times Printed '.adjustRight($aExcess[printx],3)."\n";
		$detailsn .= 'Printed : '.date('m/d/Y g:ia').' xPrinted '.adjustRight($aExcess[printx],3)."\n";
		$details .= str_repeat('=',76)."\n";
		$detailsn .= str_repeat('=',75)."\n";
		$details .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aExcess['account'])),45).'   '.
				adjustSize('Excess Withdrawal/Advances',28)."\n";
		$detailsn .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aExcess['account'])),45).'   '.
				adjustSize('Excess Withdrawal/Advances',28)."\n";
		$details .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aExcess['account_group_id']),55).'   '.
				adjustSize("Date:".ymd2mdy($aExcess['date']),15)."\n";
		$detailsn .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aExcess['account_group_id']),55).'   '.
				adjustSize("Date:".ymd2mdy($aExcess['date']),15)."\n";
		$details .= str_repeat('=',76)."\n\n";
		$detailsn .= str_repeat('=',75)."\n";
		$as='';
		if ($aExcess['refund_amount'] != '0')
		{
			$details .= ' '.str_pad('For Refund('.$aExcess['refund_remark'].')',16,'.').adjustRight(number_format($aExcess['refund_amount'],2),10)."\n";
			$detailsn .= ' '.str_pad('For Refund('.$aExcess['refund_remark'].')',16,'.').adjustRight(number_format($aExcess['refund_amount'],2),10)."\n";
			$as = 'Change Refund';
		}
		if ($aExcess['ps_amount'] != '0')
		{
			if ($as != '') $as .= "\n and ";
			$as .= 'PS Withdrawal';			
			$details .= ' '.str_pad('P-Savings('.$aExcess['ps_remark'].')',16,'.').adjustRight(number_format($aExcess['ps_amount'],2),10)."\n";
			$detailsn .= ' '.str_pad('P-Savings('.$aExcess['ps_remark'].')',16,'.').adjustRight(number_format($aExcess['ps_amount'],2),10)."\n";
		}
		$details .= ' ';
		$detailsn .= ' ';
		$myear = substr($aExcess['date'],0,4);
		$curmo = substr($aExcess['date'],5,2)-4;
		if ($curmo > $aExcess['starting_month']) $myear++;
//if ($ADMIN[admin_id]==1) echo	$curmo.'  '.$aExcess['starting_month'].'  year '.$myear.'  excess date'.$aExcess['date'];	
		$year_flag = $months = 0;
		for ($cc = 0 ; $cc< 13; $cc++)
		{
			$mc = $aM[($aExcess['starting_month']+$cc-1)%13];
			$mc = $aExcess['starting_month'] + $cc ;
			if ($mc > 13)
			{
				$mc -= 13;
				if ($year_flag == '0')
				{
					$myear++;
					$year_flag = 1;
				}
			}
			
			if ($mc <13)
			{
				$cmonth = cmonth($mc);
			}
			else
			{
				$cmonth = '13th Month ';
			}
			$mi = 'month'.($cc+1);
			
			if ($aExcess[$mi] > '0')
			{
				$months++;
				$details .= str_pad(adjustSize($cmonth,9).', '.$myear,20,'.').adjustRight(number_format($aExcess[$mi],2),10).'    ';
				$detailsn .= str_pad(adjustSize($cmonth,9).', '.$myear,20,'.').adjustRight(number_format($aExcess[$mi],2),10).'    ';
				if ($cc%2 == 1)
				{
					$details .= "\n ";
					$detailsn .= "\n ";
				}
			}
		}	
		$details .= "\n";
		$detailsn .= "\n";
		$details .= adjustSize("Gross Amount.............................",45).
				space(18).
				adjustRight(number_format($aExcess['gross_amount'],2),12)."\n";
		$detailsn .= adjustSize("Gross Amount.............................",45).
				space(18).
				adjustRight(number_format($aExcess['gross_amount'],2),12)."\n";
				
		$details .= adjustSize("Less : Interest..........................",50).'  '.
				adjustRight(number_format($aExcess['interest'],2),12)."\n";
		$detailsn .= adjustSize("Less : Interest..........................",50).'  '.
				adjustRight(number_format($aExcess['interest'],2),12)."\n";
		if ($aExcess['interest'] > 0 or $aExcess['charges'] > 0)
		{
			if ($as != '') $as .= "\n and ";
			$as .= ' Advance Change';			
		
		$details .= adjustSize("       Other Charges (".$aExcess['charges_remark'].").......",50).'  '.
				adjustRight(number_format($aExcess['charges'],2),12)."\n";
		$detailsn .= adjustSize("       Other Charges (".$aExcess['charges_remark'].").......",50).'  '.
				adjustRight(number_format($aExcess['charges'],2),12)."\n";

		$details .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aExcess['net_amount'],2),12)."\n";
		$detailsn .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aExcess['net_amount'],2),12)."\n";

		$details .= str_repeat('=',76)."\n";
		$detailsn .= str_repeat('=',75)."\n";
		$oblige = $aExcess['gross_amount'] - $aExcess['refund_amount'];
		$details .= "Obligation: ".number_format($oblige,2).'  '.
				" for ".$months." Month/s \n";
		$detailsn .= "Obligation: ".number_format($oblige,2).'  '.
				" for ".$months." Month/s \n";
		$details .= str_repeat('=',76)."\n";
		$detailsn .= str_repeat('=',75)."\n";
		}
		if (strlen($aExcess[remarks]) > 216)
		{
			$details .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$detailsn .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$details .= substr($aExcess[remarks],66,75)."\n";
			$detailsn .= substr($aExcess[remarks],66,75)."\n";
			$details .= substr($aExcess[remarks],141,75)."\n";
			$detailsn .= substr($aExcess[remarks],141,75)."\n";
			$details .= substr($aExcess[remarks],216,75);
			$detailsn .= substr($aExcess[remarks],216,75);
		}	
		elseif (strlen($aExcess[remarks]) > 141)
		{
			$details .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$detailsn .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$details .= substr($aExcess[remarks],66,75)."\n";
			$detailsn .= substr($aExcess[remarks],66,75)."\n";
			$details .= substr($aExcess[remarks],141,75);
			$detailsn .= substr($aExcess[remarks],141,75);
		}	
		elseif (strlen($aExcess[remarks]) > 66)
		{
			$details .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$detailsn .= "Remarks: ".substr($aExcess[remarks],0,66)."\n";
			$details .= substr($aExcess[remarks],66,75);
			$detailsn .= substr($aExcess[remarks],66,75);
		}	
		else 
		{
			$details .= "Remarks: ".$aExcess['remarks'];
			$detailsn .= "Remarks: ".$aExcess['remarks'];
		}
		if ($aExcess['remarks'] != '') 
		{
			$details .= "\n";
			$detailsn .= "\n";
		}
		
		$details .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aExcess['net_amount'])." (".number_format($aExcess['net_amount'],2).")  as $as.\n\n\n";
		$detailsn .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aExcess['net_amount'])." (".number_format($aExcess['net_amount'],2).")  as $as.\n\n";
		if ($aExcess['username'] == '') $prepared = $ADMIN['username'];
		else $prepared = $aExcess['username'];		

		if ($aExcess[cashier_id]=='' or $aExcess[cashier_id]==0) $cashier = $ADMIN['username'];
		else $cashier = lookUpTableReturnValue('x','admin','admin_id','username',$aExcess['cashier_id']);

		$details .= "Received by: ".adjustSize(strtoupper($aExcess['account']),40)." Prepared by: ".strtoupper($prepared)."\n\n\n";
		$detailsn .= "Received by: ".adjustSize(strtoupper($aExcess['account']),40)." Prepared by: ".strtoupper($prepared)."\n\n\n";
		$details .= "Reviewed by: ".adjustSize($ADMIN['username'],40)." Released by: ".$cashier."\n\n";
		$detailsn .= "Approved by: ".adjustSize($ADMIN['username'],40)." Released by: ".$cashier."\n";
		$details .= str_repeat('=',76)."\n";
		$detailsn .= str_repeat('=',75)."\n";

if ($ADMIN[admin_id] == 1)
	echo "<pre>$details</pre>";


		if ($SYSCONF['PRINTER_TYPE'] == 'GRAPHIC' or $p1=='PrintForm' or $p1=='PrintForm2')
		{
			if ($p1=='PrintForm') include("print.voucher.excess.php");   //$printdetails = "<font size='5'>".$detailsn."</font>";
			elseif ($p1=='PrintForm2') include("print.voucher.excess1.php");   //$printdetails = "<font size='5'>".$detailsn."</font>";
			else
			{
				$printdetails = "<font size='3'>".$details."</font>";
				
				echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$printdetails.'"'.">";
				echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
				echo "<script>printIframe(print_area)</script>";
			}	
		}
		else
		{
			$details .= "<eject>";
			doPrint($details);
			if ($aExcess[account_id]==24286)
			{ 
				echo "<pre>$details</pre>";
			}	
			else
				doPrint($details);
		}
		$q = "update wexcess set status='P' where wexcess_id = '".$aExcess['wexcess_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		$aExcess['status'] = 'P';		
 }	
$aM = array("January","February","March","April","May","June","July","August","September","October","November","December","13th Month");
if ($aExcess['starting_month'] == '') $aExcess['starting_month'] =date('m');

for ($c=0;$c<13;$c++)
{
	$d=$c+1;
	$fld = 'Month'.$d;
	$$fld = $aM[($aExcess['starting_month']+$c-1)%13];
}	
if ($p1 == 'Search')
{
//	$bscan = array('1','2','3','5','7','10','11','16','18','19');
	  $bscan = array();
	  $qb = "select * from branch where enable and swipe order by branch_id";
	  $qrb = pg_query($qb) or message(pg_errormessage());
	  while ($rb = pg_fetch_object($qrb))
	  {
		$bscan[] = $rb->branch_id;
	  }
	$acid = array();
	$scid = array();
	$scdat = date('Y-m-d');

	$user_branch_id = $ADMIN['branch_id'];
	if (empty($user_branch_id))
	    $user_branch_id = 0;

	$q="select * from schedule 
			where 
				date='$scdat' and branch_id = '{$user_branch_id}' and
				active!='9' and status!='Finished'";
	$qs = pg_query($q) or message(pg_errormessage());
	while ($rs = pg_fetch_object($qs))
	{
		$scid[] = $rs->smartno;
		$acid[] = $rs->account_id;
	}
	
//if ($ADMIN[admin_id]==1)
//{
//	$scid[] = "33FDB5A2";
	$scc = count($scid);
	if ($scc > 0)
	{
		$cids = implode(',',$scid);
		$q = "select account_id from account where smartno in ('$cids')";
		$qs = pg_query($q) or message(pg_errormessage());
		while ($rs = pg_fetch_object($qs))
		{
			$acid[] = $rs->account_id;
		}
	}
//}
	$acc = count($acid);
	
  	if ($searchby == '') $searchby = 'account';
	  $q = "select * 
				from 
					account
				where 
					$searchby ilike '$xSearch%' and enable";

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
			if ($acc == 0)
				$q .= ") ";
			else
			{
				$cids = join(',',$acid);
				$q .= " or account_id IN ($cids)) ";
			}
		}
/*if ($ADMIN[admin_id]==1)
{					
				$cids = join(',',$acid);
				$q .= " and account_id IN ($cids) ";
}*/						
					
		if ($account_group_id != '')
		{
			$q .=  " and account.account_group_id = '$account_group_id'";
		}
		if ($branch_id != '')
		{
			$q .=  " and account.branch_id = '$branch_id'";
		}
		$q .= " order by
					account
				offset 0 limit 30";
if ($ADMIN[admin_id]==1)
{
//	echo $q;
}					

		$qr = @pg_query($q)	or message("Error Querying account file...".db_error());
		if (@pg_num_rows($qr) && $searchby == 'account_code')
		{
			$r = @pg_fetch_assoc($qr);
			$id = $r['account_id'];
			header("Location:/lending/?p=excess.withdraw&p1=selectAccount&c_id={$id}");
			exit;
		} 
?>
  
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="34%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
      <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        No. </font></strong></td>
    <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account Group</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></strong></td>
    <td width="15%" align="center"><strong></strong></td>
  </tr>
  <?
  	$ctr=0;
	include_once('accountbalance.php');
  	while ($r = @pg_fetch_assoc($qr))
	{
//		if ($ADMIN['branch_id']=='3' or $ADMIN['branch_id']=='18' or $ADMIN['branch_id']=='10' or $ADMIN['branch_id']=='11')
//echo $ADMIN['branch_id'].'  ';
//print_r($bscan);
		if (in_array($ADMIN['branch_id'],$bscan))
		{
			if ($r['smartno'] !='')
			{
				$scdat = date('Y-m-d');
				$smartno=$r['smartno'];
				$q="select * from schedule 
						where 
							smartno = '$smartno' and date='$scdat' and branch_id = '".$ADMIN['branch_id']."' and
							active!='9' and status!='Finished'";
				$qs = pg_query($q) or message(pg_errormessage());
				$rs = pg_fetch_object($qs);
				if ($rs->smartno != $smartno) continue; 		
			} 
		}

		$ctr++;
		if ($r['branch_id'] == '') $branch_id = '0';
		else $branch_id = $r['branch_id'];
 
 		$qq = "select sum(ammort) as total_ammort from releasing where balance>0 and account_id = '".$r['account_id']."' and status!='C'";
		$qqr = @pg_query($qq);
		$rr = @pg_fetch_object($qqr);

		$cbalance = '';
		$balance = 0;
		
//		echo "sal ".$r['salary']."  rr ".$rr->total_ammort;
		if ($rr->total_ammort < $r['salary'])
		{
	
			$aBal = excessBalance($r['account_id']);
			$balance = $aBal['balance'];
	
			if ($balance > 0)
			{
				$cbalance = number_format($balance,2);
				$tooltip = " Refundable Pension Change/Excess : P $cbalance";
			}
			else
			{
				$cbalance = "(".number_format(abs($balance),2).")";
				$tooltip = " Advance Change/Excess Made : P $cbalance ";;
			}		
			$bgColor = '#FFFFFF';
			$href = "\"javascript: document.getElementById('f1').action='?p=excess.withdraw&p1=selectAccount&c_id=".$r['account_id']."&xSearch=$xSearch&rtype=$rtype';document.getElementById('f1').submit()\" onmouseover=\"showToolTip(event,'$tooltip...');return false\" onmouseout=\"hideToolTip()\"";			
		}
		else
		{
			$aBal = excessBalance($r['account_id']);
			$balance = $aBal['balance'];
	
			if ($balance > 0)
			{
				$cbalance = number_format($balance,2);
				$tooltip = " Refundable Pension Change/Excess : P $cbalance";
				$bgColor = '#FFFFFF';
				$href = "\"javascript: document.getElementById('f1').action='?p=excess.withdraw&p1=selectAccount&c_id=".$r['account_id']."&xSearch=$xSearch&rtype=$rtype';document.getElementById('f1').submit()\" onmouseover=\"showToolTip(event,'$tooltip...');return false\" onmouseout=\"hideToolTip()\"";			

			}
			else
			{
				$bgColor = '#DADADA';
				$href = "'#' onmouseover=\"showToolTip(event,'No Pension Excess/Change available...');return false\" onmouseout=\"hideToolTip()\"";
			}
		}
 
  ?>
  <tr bgcolor="<?=$bgColor;?>" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?=$bgColor;?>'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href=<?=$href;?>> 
      <?= $r['account'];?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r['account_code'] ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']) ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		<?= lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id) ;?>
       
    </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $cbalance;?>
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?
	return;
}
?>
  <table width="86%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="25" colspan="3"  background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Excess 
        Withdrawal / Cash Advance <font color="#0000FF"> :: <a href="javascript: document.getElementById('f1').action='?p=excess.withdraw&p1=viewhistory';document.getElementById('f1').submit()">View 
        History </a>::</font></strong></font></td>
      <td height="25" colspan="3" align="center" background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Record_Id: 
        <?= $aExcess['wexcess_id'];?>
        &nbsp;&nbsp;<b>( 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><em><font face="Times New Roman, Times, serif"><b>
        <?=($aExcess[printx] >0?$aExcess[printx].' X ':'').status($aExcess['status']);?>
        </b></font></em></font>        ) </b></font> </td>
    </tr>
    <tr> 
      <td width="15%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account (<?=$aExcess['account_code'];?>)</font></td>
      <td width="28%"> <input name="textfield" type="text" value="<?= $aExcess['account'];?>" size="40" readOnly></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td colspan="2">
	  <font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="date" type="text" id="date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= ymd2mdy($aExcess['date']);?>" size="10"><img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"></font></strong></font></font>
<!--        <input name="current_month" type="hidden" id="current_month"  readOnly value="<?= substr($aExcess['date'],5,2);?>" size="8"> -->
        <input name="transdate" type="hidden" id="transdate"  readonly="readOnly" value="<?=$transdate;?>" size="8" />
        <input name="wexcess_id" type="hidden" id="wexcess_id"  readonly="readOnly" value="<?=$aExcess['wexcess_id'];?>" size="8" />
        <input name="audit" type="hidden" id="audit"  readonly="readOnly" value="<?=$aExcess['audit'];?>" size="8" /></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
      <td><input name="textfield2" type="text" value="<?= $aExcess['branch'];?>" size="40" readOnly></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Encoded</font></td>
      <td colspan="2"> <font size="2"> 
        <?= $aExcess['username'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank</font></td>
      <td><input name="textfield3" type="text" value="<?= $aExcess['clientbank'];?>" size="40" readOnly></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdaw 
        Day </font></td>
      <td colspan="2"><font size="2">
        <?= $aExcess['withdraw_day'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Current 
        Balance</font></td>
      <td colspan="3"><input name="textfield32" type="text" style="text-align:right" value="<?= number_format( $aExcess['balance'],2);?>" size="10" readOnly>
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Salary 
        <input name="textfield322" type="text" style="text-align:right" value="<?= number_format( $aExcess['salary'],2);?>" size="10" readOnly>
        Ammortization
        <input name="textfield52" type="text" style="text-align:right" value="<?= number_format($aExcess['total_ammort'],2);?>" size="10" readOnly>
        Last Transaction		</font>
	  <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="textfield5" type="text" value="<?= ymd2mdy($aExcess['last_takeout']);?>" size="10" readOnly>
        </font></td>
    </tr>
    <tr> 
      <td colspan="4"><hr size="1"></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">For 
        Refund(Current)</font></td>
      <td><input name="refund_amount" type="text" id="refund_amount"  style="text-align:right" onBlur="vAmt()"  onKeypress="if(event.keyCode == 13){document.getElementById('refund_remark').focus();return false;}"  value="<?= $aExcess['refund_amount'];?>" size="12"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Refund Remarks        </font></td>
      <td colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="refund_remark" type="text" id="refund_remark" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('ps_amount').focus();return false;}" value="<?= $aExcess['refund_remark'];?>" size="35" maxlength="40">
        </font></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Personal 
        Savings </font></td>
      <td><input name="ps_amount" type="text" id="ps_amount"  style="text-align:right" onBlur="vAmt()"  onKeypress="if(event.keyCode == 13){document.getElementById('ps_remark').focus();return false;}"  value="<?= $aExcess['ps_amount'];?>" size="12"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdrawal 
        Remarks </font></td>
      <td colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="ps_remark" type="text" id="ps_remark" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('rate').focus();return false;}" value="<?= $aExcess['ps_remark'];?>" size="35" maxlength="40">
        </font></td>
    </tr>
    <tr bgcolor="#E3F6F1"> 
      <td colspan="6"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advances</font></strong></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Interest/Month</font></td>
      <td><input name="rate" type="text" id="rate" style="text-align:right" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('starting_month').focus();return false;}" value="<?= $aExcess['rate'];?>" size="12"></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m4" type="text" id="m4"  size="15" readonly value="<?= $Month4;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month4" type="text" id="month4"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month5').focus();return false;}" value="<?= $aExcess['month4'];?>" size="12" /></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m9" type="text" id="m9"  size="15" readonly="readonly" value="<?= $Month9;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td width="14%">
	  <input name="month9" type="text" id="month9"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month10').focus();return false;}" value="<?= $aExcess['month9'];?>" size="12" /></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Starting 
        Month</font></td>
      <td nowrap> <select name="starting_month" id="starting_month" style="width:120px"  onKeypress="if(event.keyCode == 13){document.getElementById('month1').focus();return false;}" onBlur="vMo()">
          <option value="1" <?= ($aExcess['starting_month'] == '1' ? 'selected' : '');?>>January</option>
          <option value="2" <?= ($aExcess['starting_month'] == '2' ? 'selected' : '');?>>February</option>
          <option value="3" <?= ($aExcess['starting_month'] == '3' ? 'selected' : '');?>>March</option>
          <option value="4" <?= ($aExcess['starting_month'] == '4' ? 'selected' : '');?>>April</option>
          <option value="5" <?= ($aExcess['starting_month'] == '5' ? 'selected' : '');?>>May</option>
          <option value="6" <?= ($aExcess['starting_month'] == '6' ? 'selected' : '');?>>June</option>
          <option value="7" <?= ($aExcess['starting_month'] == '7' ? 'selected' : '');?>>July</option>
          <option value="8" <?= ($aExcess['starting_month'] == '8' ? 'selected' : '');?>>August</option>
          <option value="9" <?= ($aExcess['starting_month'] == '9' ? 'selected' : '');?>>September</option>
          <option value="10" <?= ($aExcess['starting_month'] == '10' ? 'selected' : '');?>>October</option>
          <option value="11" <?= ($aExcess['starting_month'] == '11' ? 'selected' : '');?>>November</option>
          <option value="12" <?= ($aExcess['starting_month'] == '12' ? 'selected' : '');?>>December</option>
        </select>
        <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="p1" type="submit" id="p1" value="?" />
        </font></strong></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m5" type="text" id="m5"  size="15" readonly value="<?= $Month5;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td nowrap><input name="month5" type="text" id="month5"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month6').focus();return false;}" value="<?= $aExcess['month5'];?>" size="12" /></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m10" type="text" id="m10"  size="15" readonly="readonly" value="<?= $Month10;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td width="14%"><input name="month10" <?=($Month10!="13th Month"?'readonly':'');?> type="text" id="month10"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month11').focus();return false;}" value="<?= $aExcess['month10'];?>" size="12" /></td>
    </tr>
    <tr> 
      <td height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m1" type="text" id="m1"  size="15" readonly value="<?= $Month1;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month1" type="text" id="month1"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month2').focus();return false;}" value="<?= $aExcess['month1'];?>" size="12" /></td>
      <td><p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m6" type="text" id="m6"  size="15" readonly value="<?= $Month6;?>" style="background:#EFEFEF;border:none" />
      </font></p></td>
      <td><input name="month6" type="text" id="month6"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month7').focus();return false;}" value="<?= $aExcess['month6'];?>" size="12" /></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="m11" type="text" id="m11"  size="15" readonly="readonly" value="<?= $Month11;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td width="14%"><input name="month11" <?=($Month11!="13th Month"?'readonly':'');?> type="text" id="month11"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month12').focus();return false;}" value="<?= $aExcess['month11'];?>" size="12" /></td>
    </tr>
    <tr> 
      <td height="23"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m2" type="text" id="m2"  size="15" readonly value="<?= $Month2;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month2" type="text" id="month2"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month3').focus();return false;}" value="<?= $aExcess['month2'];?>" size="12" /></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m7" type="text" id="m7"  size="15" readonly value="<?= $Month7;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month7" type="text" id="month7"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month8').focus();return false;}" value="<?= $aExcess['month7'];?>" size="12" /></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="m12" type="text" id="m12"  size="15" readonly="readonly" value="<?= $Month12;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td width="14%"><input name="month12" <?=($Month12!="13th Month"?'readonly':'');?> type="text" id="month12"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month13').focus();return false;}" value="<?= $aExcess['month12'];?>" size="12" /></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m3" type="text" id="m3"  size="15" readonly value="<?= $Month3;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month3" type="text" id="month3"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('month4').focus();return false;}" value="<?= $aExcess['month3'];?>" size="12" /></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="m8" type="text" id="m8"  size="15" readonly value="<?= $Month8;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td><input name="month8" type="text" id="month8"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('gross_amount').focus();return false;}" value="<?= $aExcess['month8'];?>" size="12" /></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="m13" type="text" id="m13"  size="15" readonly="readonly" value="<?= $Month13;?>" style="background:#EFEFEF;border:none" />
      </font></td>
      <td width="14%"><input name="month13" <?=($starting_month!=1?'readonly':'');?> type="text" id="month13"  style="text-align:right"  onblur="vAmt()"   onkeypress="if(event.keyCode == 13){document.getElementById('gross_amount').focus();return false;}" value="<?= $aExcess['month13'];?>" size="12" /></td>
    </tr>
    <tr> 
      <td colspan="6"><hr></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross Amount</font></td>
      <td colspan="5"><input name="gross_amount" type="text" id="gross_amount" style="text-align:right"  readOnly onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('interest').focus();return false;}" value="<?= $aExcess['gross_amount'];?>" size="12"></td>
    </tr>
    <tr> 
      <td height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Interest 
        Amount</font></td>
      <td colspan="5"><input name="interest" type="text" id="interest" style="text-align:right" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('charges').focus();return false;}" value="<?= $aExcess['interest'];?>" size="12"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Other Charges</font></td>
      <td colspan="5"><input name="charges" type="text" id="charges" style="text-align:right" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('charges_remark').focus();return false;}" value="<?= $aExcess['charges'];?>" size="12"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charge Remarks 
        <input name="charges_remark" type="text" id="charges_remark" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('net_amount').focus();return false;}" value="<?= $aExcess['charges_remark'];?>" size="35" maxlength="40">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Net Amount</font></td>
      <td colspan="3"><input name="net_amount" type="text" id="net_amount" style="text-align:right" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('remarks').focus();return false;}" value="<?= $aExcess['net_amount'];?>" size="12"></td>
	  <td>
		  <?
		  if ($ADMIN[usergroup]=='R' or $ADMIN[usergroup]=='A')
		  {
			  ?>
			  <select name="cashier_id" id="cashier_id"  style="width:180"   onkeypress="if(event.keyCode==13) {document.getElementById('term').focus();return false;}">
			  <option value=''>Select Cashier</option>
			  <?
			  $qr = pg_query("select * from admin where enable='Y' and (usergroup='C' or usergroup='U') and 
						  (branch_id=0 or branch_id='".$aExcess[branch_id]."' or branch_id2='".$aExcess[branch_id]."' or branch_id3='".$aExcess[branch_id]."' or
						   branch_id4='".$aExcess[branch_id]."' or branch_id5='".$aExcess[branch_id]."' or branch_id6='".$aExcess[branch_id]."' or 
						   branch_id7='".$aExcess[branch_id]."' or branch_id8='".$aExcess[branch_id]."' or branch_id9='".$aExcess[branch_id]."' or 
						   branch_id10='".$aExcess[branch_id]."' or branch_id11='".$aExcess[branch_id]."' or branch_id12='".$aExcess[branch_id]."' or 
						   branch_id13='".$aExcess[branch_id]."' or branch_id14='".$aExcess[branch_id]."' or branch_id15='".$aExcess[branch_id]."' or 
						   branch_id16='".$aExcess[branch_id]."' or branch_id17='".$aExcess[branch_id]."' or branch_id18='".$aExcess[branch_id]."' or 
						   branch_id19='".$aExcess[branch_id]."' or branch_id20='".$aExcess[branch_id]."')");
		  while ($r=pg_fetch_object($qr))
		  {
			if ($aExcess['cashier_id'] == $r->admin_id)
			{
				echo "<option value=$r->admin_id selected>$r->name</option>";
			}
			else
			{
				echo "<option value=$r->admin_id>$r->name</option>";
			}	
		  }  
		  ?>
		</select>
		<?
		}
		?>
	  </td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
      <td colspan="5"><textarea name="remarks" cols="60" rows="2" id="remarks"><?= $aExcess['remarks'];?></textarea></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="4"><table width="58%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save"  accesskey="S">
            </strong></font></td>
            <td nowrap width="16%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
              <input name="Print" type="image" id="Print" onclick="vSubmit(this)" src="../graphics/print.jpg" alt="Print This Claim Form" width="65" height="18"   accesskey="P" />
            </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
			<?
			if ($aExcess['status']=='C' and ($ADMIN[admin_id]==54 or $ADMIN[admin_id]==197 or $ADMIN[admin_id]==128 or 
					$ADMIN[admin_id]==1))
			{
			?>
            <td nowrap width="12%"> <input type='image' name="Restore" id="Restore" onClick="vSubmit(this)"  src="../graphics/trash-restore.jpg" alt="Restore Form" width="50" height="25"> </td>
			<?
			}
			else
			{
			?>
            <td nowrap width="18%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> </td> 
			<?
			}
			?>
            <td nowrap width="15%"><input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20" /></td>
			<td width="13%" align="center"><input type='image' name="PrintForm" id="PrintForm" onClick="vSubmit(this)"  src="../graphics/PrintOk.png" alt="Print Form" width="40" height="20" /></td>
			<td width="12%" align="center"><input type='image' name="PrintForm2" id="PrintForm2" onClick="vSubmit(this)"  src="../graphics/dotmatrix.png" alt="Print Form2" width="40" height="20" /></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<?
if ($p1 == 'viewhistory' && $aExcess['account_id'] != '')
{
	include_once('excess.history.php');
}
if ($focus != '')
{
	echo "<script>document.getElementById('$focus').focus()</script>";
}

?>
