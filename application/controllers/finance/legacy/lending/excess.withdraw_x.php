<script>
/*
	periodic report summary of withdrawl per bank
   Bank     Total Active ATMs   Total ATMs Withdwn   Total Amount Withdrawn
   --------- ----------------   ------------------- -----------------------
   
   branch       ''                     ''                  ''
   ---------  -----------------
*/
function vAmt()
{
	refund_amount = parseFloat(document.getElementById('refund_amount').value*1);
	charges = parseFloat(document.getElementById('charges').value*1);

	month1 = parseFloat(document.getElementById('month1').value *1);
	month2 = parseFloat(document.getElementById('month2').value *1);
	month3 = parseFloat(document.getElementById('month3').value *1);
	month4 = parseFloat(document.getElementById('month4').value *1);
	month5 = parseFloat(document.getElementById('month5').value *1);
	month6 = parseFloat(document.getElementById('month6').value *1);
	month7 = parseFloat(document.getElementById('month7').value *1);
	month8 = parseFloat(document.getElementById('month8').value *1);

	gross_amount = refund_amount + month1 + month2 + month3 + month4 + month5 + month6 + month7 + month8;
	rate  = parseFloat(document.getElementById('rate').value *1)/100;
	interest = 0
	interest += month1*rate + month2*rate*2 + month3*rate*3 + month4*rate*4 + month5*rate*5 + month6*rate*6 + month7*rate*7 + month8*rate*8;

	net_amount = gross_amount - interest - charges;
	document.getElementById('net_amount').value = twoDecimals(net_amount);
	document.getElementById('gross_amount').value = twoDecimals(gross_amount);
	document.getElementById('interest').value = twoDecimals(interest);
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
        </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="SearchButton" value="Search">
        <input name="p12" type="button" id="p12" value="Browse" onClick="window.location='?p=wexcess.browse'">
        <input name="p122" type="button" id="p122" value="Close" onClick="window.location='?p='">
        </font></td>
    </tr>
    <tr> 
      <td><hr color="#BBBBEE" size="1"></td>
    </tr>
  </table>

<?
	if (!session_is_registered('aExcess'))
	{
		session_register('aExcess');
		$aExcess = null;
		$aExcess = array();
	}
	
	$fields = array('period_advance', 'rate', 'interest', 'period_excess', 'refund_remark', 'refund_amount',  'net_amount', 'charges', 'charges_remark',
						'gross_amount', 'remarks','date','starting_month','month1','month2','month3','month4','month5','month6','month7','month8',);
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
	if ($p1 == 'New' or ($p1 == '' && count($aExcess) == 0))
	{
		$aExcess = null;
		$aExcess = array();
		$aExcess['date'] = date('Y-m-d');
		$aExcess['starting_month'] = date('m')+1;
		if ($aExcess['starting_month'] > 12) $aExcess['starting_month']-1;
		$focus = 'xSearch';
	}
	elseif ($p1 == 'Load' && $id != '')
	{
		$q = "select * from wexcess where wexcess_id = '$id'";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			exit;
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

		$focus = 'period_advance';
		
	}
	elseif ($p1 == 'Save' && $aExcess['account_id'] == '')
	{
		message("No Account Specified...");
	}
	elseif ($p1 == 'Save')
	{
		if ($aExcess['wexcess_id'] == '')
		{
			$aExcess['audit'] = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';

			$q = "insert into wexcess ( audit, account_id, admin_id ";
			$qq .= ") values ('".$aExcess['audit']."', '".$aExcess['account_id']."','".$ADMIN['admin_id']."'";
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

			$q = "update wexcess set account_id = '".$aExcess['account_id']."'";
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
	elseif ($p1 == 'CancelConfirm')
	{
		$q = "update wexcess set status='C' where wexcess_id = '".$aExcess['wexcess_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr)
		{
			message("Transaction Cancelled...");
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
		$aExcess = null;
		$aExcess = array();
		$aExcess = $r;
		$aExcess['date'] = date('Y-m-d');
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
	}
	elseif ($p1 == 'Print' && !in_array($aExcess['status'], array('P','S')))
	{
		message("Save Transaction First Before Printing...");
	}
	elseif ($p1 == 'Print')
	{
		$q = "select * from wexcess where wexcess_id = '".$aExcess['wexcess_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			exit;
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
		
		$details .= $SYSCONF['BUSINESS_NAME']."\n".	
				$SYSCONF['BUSINESS_ADDR'].'    Tel. No.: '.$SYSCONF['BUSINESS_TEL']."\n";
		$details .= space(50).'RELEASE No. '.str_pad($aExcess['wexcess_id'],8,'0',str_pad_left)."\n";
		$details .= str_repeat('=',76)."\n";
		$details .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aExcess['account'])),45).'   '.
				adjustSize('Excess Withdrawal/Advances',28)."\n";
		$details .= adjustSize("Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aExcess['account_group_id']),55).'   '.
				adjustSize("Date:".ymd2mdy($aExcess['date']),15)."\n";
		$details .= str_repeat('=',76)."\n\n";
		
		if ($aExcess['refund_amount'] > '0')
		{
			$details .= ' '.str_pad('For Refund('.$aExcess['refund_remark'].')',16,'.').adjustRight(number_format($aExcess['refund_amount'],2),10)."\n";
		}
		$details .= ' ';
		$myear = substr($aExcess['date'],0,4);
		$year_flag = $months = 0;
		for ($cc = 0 ; $cc< 8; $cc++)
		{
			$mc = $aExcess['starting_month'] + $cc ;
			if ($mc > 12)
			{
				$mc -= 12;
				if ($year_flag == '0')
				{
					$myear++;
					$year_flag = 1;
				}
			}
			$cmonth = cmonth($mc);
			$mi = 'month'.($cc+1);
			
			if ($aExcess[$mi] > '0')
			{
				$months++;
				$details .= str_pad($cmonth.', '.$myear,16,'.').adjustRight(number_format($aExcess[$mi],2),10).'    ';
				if ($cc%2 == 1)
				{
					$details .= "\n ";
				}
			}
		}	
		$details .= "\n";
		$details .= adjustSize("Gross Amount.............................",45).
				space(18).
				adjustRight(number_format($aExcess['gross_amount'],2),12)."\n";
				
		$details .= adjustSize("Less : Interest..........................",50).'  '.
				adjustRight(number_format($aExcess['interest'],2),12)."\n";

		$details .= adjustSize("       Other Charges (".$aExcess['charges_remark'].").......",50).'  '.
				adjustRight(number_format($aExcess['charges'],2),12)."\n";


		$details .= adjustSize("Net Amount Released",45,'.').
				space(18).
				adjustRight(number_format($aExcess['net_amount'],2),12)."\n";

		$details .= str_repeat('=',76)."\n";
		$details .= "Obligation: ".number_format($aExcess['gross_amount'],2).'  '.
				" for ".$months." Month/s \n";
		$details .= str_repeat('=',76)."\n";
		$details .= "Remarks: ".$aExcess['remarks'];
		if ($aExcess['remarks'] != '') $details .= "\n";
		$details .= "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aExcess['net_amount'])." (".number_format($aExcess['net_amount'],2).")  as change.\n\n\n";
				
		$details .= "Received by: ".adjustSize(strtoupper($aExcess['account']),40)." Prepared by: ".strtoupper($ADMIN['username'])."\n\n\n";
		$details .= "Approved by: ".adjustSize("Operation Manager",40)." Approved by: Cashier\n\n";
		$details .= str_repeat('=',76)."\n";

		//echo "<pre>$details</pre>";	
		if ($SYSCONF['RECEIPT_PRINT'] == 'DRAFT')
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
	
  if ($p1 == 'Search')
  {
	  $q = "select * 
				from 
					account
				where 
					account ilike '$xSearch%'
				order by
					account
				offset 0 limit 30";
					
					
		$qr = @pg_query($q)	or message("Error Querying account file...".db_error());
?>
  
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="34%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
      No. </font></strong></td>
    <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account Group</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></strong></td>
    <td width="15%" align="center"><strong></strong></td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		$ctr++;
		if ($r['branch_id'] == '') $branch_id = '0';
		else $branch_id = $r['branch_id'];
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="javascript: document.getElementById('f1').action='?p=excess.withdraw&p1=selectAccount&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>&rtype=<?=$rtype;?>';document.getElementById('f1').submit()"> 
      <?= $r['account'];?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r['bank_cardno'] ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']) ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		<?= lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id) ;?>
       
    </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?
	exit;
}
?>
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td height="25" colspan="2"  background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Excess 
        Withdrawal / Cash Advance <font color="#0000FF"> :: <a href="javascript: document.getElementById('f1').action='?p=excess.withdraw&p1=viewhistory';document.getElementById('f1').submit()">View 
        History </a>::</font></strong></font></td>
      <td height="25" colspan="3" align="center" background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Record_Id: 
        <?= $aExcess['wexcess_id'];?>
        &nbsp;&nbsp;(<b> 
        <?= status($aExcess['status']);?>
        ) </b></font> </td>
    </tr>
    <tr> 
      <td width="16%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account</font></td>
      <td width="30%"> <input name="textfield" type="text" value="<?= $aExcess['account'];?>" size="40" readOnly></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td width="43%" colspan="2"><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="date" type="text" id="date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= ymd2mdy($aExcess['date']);?>" size="8">
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" color="#000000"><img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="current_month" type="hidden" id="current_month"  readOnly value="<?= substr($aExcess['date'],5,2);?>" size="8">
        </font></strong></font></font></strong></font></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
      <td><input name="textfield2" type="text" value="<?= $aExcess['branch'];?>" size="40" readOnly></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Encoded</font></td>
      <td colspan="2"> <font size="2"> 
        <?= $aExcess['username'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank</font></td>
      <td colspan="4"><input name="textfield3" type="text" value="<?= $aExcess['clientbank'];?>" size="40" readOnly></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Current 
        Balance</font></td>
      <td colspan="4"><input name="textfield32" type="text" style="text-align:right" value="<?= number_format( $aExcess['balance'],2);?>" size="12" readOnly> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Salary 
        <input name="textfield322" type="text" style="text-align:right" value="<?= number_format( $aExcess['salary'],2);?>" size="12" readOnly>
        Last Transaction 
        <input name="textfield5" type="text" value="<?= ymd2mdy($aExcess['last_takeout']);?>" size="12" readOnly>
        </font></td>
    </tr>
    <tr> 
      <td colspan="5"><hr size="1"></td>
    </tr>
    <tr bgcolor="#E3F6F1"> 
      <td colspan="5" nowrap><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transaction 
        Entry </font></strong></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type<br>
        </font> 
        <?= lookUpAssoc('type',array('Refund'=>'R','Advances'=>'A','Personal Saving'=>'P'),$type);?>
      </td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Year</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">I-Rate</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">For 
        Refund(Current)</font></td>
      <td colspan="4"><input name="refund_amount" type="text" id="refund_amount"  style="text-align:right"  value="<?= $aExcess['refund_amount'];?>"  onKeypress="if(event.keyCode == 13){document.getElementById('refund_remark').focus();return false;}" onBlur="vAmt()"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Refund Remarks 
        <input name="refund_remark" type="text" id="refund_remark" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('rate').focus();return false;}" value="<?= $aExcess['refund_remark'];?>" size="35" maxlength="40">
        </font></td>
    </tr>
    <tr bgcolor="#EFEFEF"> 
      <td colspan="5"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advances</font></strong></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Interest/Month</font></td>
      <td><input name="rate" type="text" id="rate" value="<?= $aExcess['rate'];?>"   onKeypress="if(event.keyCode == 13){document.getElementById('starting_month').focus();return false;}" onBlur="vAmt()" style="text-align:right"></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Starting 
        Month </font></td>
      <td colspan="2"> 
        <?=lookUpMonth('starting_month',$aExcess['starting_month']);?>
      </td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month1</font></td>
      <td nowrap> <input name="month1" type="text" id="month1" value="<?= $aExcess['month1'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month2').focus();return false;}"  onBlur="vAmt()"> 
      </td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month5</font></td>
      <td colspan="2" nowrap><input name="month5" type="text" id="month5" value="<?= $aExcess['month5'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month6').focus();return false;}"  onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month2</font></td>
      <td> <input name="month2" type="text" id="month2" value="<?= $aExcess['month2'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month3').focus();return false;}"  onBlur="vAmt()"> 
      </td>
      <td><p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month6</font></p></td>
      <td colspan="2"><input name="month6" type="text" id="month6" value="<?= $aExcess['month6'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month7').focus();return false;}"  onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td height="23"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month3</font></td>
      <td><input name="month3" type="text" id="month3" value="<?= $aExcess['month3'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month4').focus();return false;}"  onBlur="vAmt()"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month7</font></td>
      <td colspan="2"><input name="month7" type="text" id="month7" value="<?= $aExcess['month7'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month8').focus();return false;}"  onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month4</font></td>
      <td><input name="month4" type="text" id="month4" value="<?= $aExcess['month4'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('month5').focus();return false;}"  onBlur="vAmt()"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month8</font></td>
      <td colspan="2"><input name="month8" type="text" id="month8" value="<?= $aExcess['month8'];?>"  style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('gross_amount').focus();return false;}"  onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td colspan="5"><hr></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross Amount</font></td>
      <td colspan="4"><input name="gross_amount" type="text" id="gross_amount" value="<?= $aExcess['gross_amount'];?>" style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('interest').focus();return false;}" onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Interest 
        Amount</font></td>
      <td colspan="4"><input name="interest" type="text" id="interest" value="<?= $aExcess['interest'];?>" style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('charges').focus();return false;}" onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Other Charges</font></td>
      <td colspan="4"><input name="charges" type="text" id="charges" value="<?= $aExcess['charges'];?>" style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('net_amount').focus();return false;}" onBlur="vAmt()"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charge Remarks 
        <input name="charges_remark" type="text" id="charges_remark" onBlur="vAmt()"   onKeypress="if(event.keyCode == 13){document.getElementById('rate').focus();return false;}" value="<?= $aExcess['charges_remark'];?>" size="35" maxlength="40">
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Net Amount</font></td>
      <td colspan="4"><input name="net_amount" type="text" id="net_amount" value="<?= $aExcess['net_amount'];?>" style="text-align:right"   onKeypress="if(event.keyCode == 13){document.getElementById('remarks').focus();return false;}" onBlur="vAmt()"></td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
      <td colspan="4"><textarea name="remarks" cols="60" rows="2" id="remarks"><?= $aExcess['remarks'];?></textarea></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td colspan="4">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="5"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save"  accesskey="S">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input name="Print" type="image" id="Print" onClick="vSubmit(this)" src="../graphics/print.jpg" alt="Print This Claim Form" width="65" height="18"   accesskey="P">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"  accesskey="C"> 
            </td>
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            </td>
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