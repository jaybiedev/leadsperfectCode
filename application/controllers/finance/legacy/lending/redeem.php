<script>
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Transaction Record?"))
		{
			document.getElementById('f1').action="?p=redeem&p1=CancelConfirm"
		}	
		else
		{
			document.getElementById('f1').action="?p=redeem&p1=Cancel"
		}
	}
	else
	{
		document.getElementById('f1').action="?p=redeem&p1="+ul.id;
	}	
	document.getElementByid('f1').submit();
}
</script>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td background="../graphics/table_horizontal.PNG" bgcolor="#003366"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        :: Redeem/Gawad or Transfer Account ::</strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>"  onKeypress="if(event.keyCode == 13){document.getElementById('SearchButton').focus();return false;}">
        <?=lookUpAssoc('searchby',array('Name'=>'account','Account No.'=>'account_code','RecordId'=>'account_id'),$searchby);?>
        </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="SearchButton" value="Search">
        <input name="p12" type="button" id="p12" value="Browse" onClick="window.location='?p=redeem.browse'">
        <input name="p1" type="submit" id="p1" value="Add New" >
        <input name="p122" type="button" id="p122" value="Close" onClick="window.location='?p='">
        </font></td>
    </tr>
    <tr> 
      <td><hr color="#BBBBEE" size="1"></td>
    </tr>
  </table>

<?
if (!chkRights3('gawad','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

	if (!session_is_registered('aRedeem'))
	{
		session_register('aRedeem');
		$aRedeem = null;
		$aRedeem = array();
	}
	
	$fields = array('date','amount','excess','withdrawn','remark','mcheck','reference','account_group_id','discount','discrem');
	if (!in_array($p1, array('', 'Edit', 'Load', 'selectAccount')))
	{

		for ($c=0;$c<count($fields);$c++)
		{
			if (substr($fields[$c],0,4) == 'date' or $fields[$c] == 'advance_applied')
			{
				if ($_REQUEST[$fields[$c]] == ''or $_REQUEST[$fields[$c]]=='--')
				{
					$aRedeem[$fields[$c]] = '';
				}
				else
				{
					$aRedeem[$fields[$c]] = mdy2ymd($_REQUEST[$fields[$c]]);
				}
			}
			else
			{
				$aRedeem[$fields[$c]] = $_REQUEST[$fields[$c]];
				if ($aRedeem[$fields[$c]] == '' && !in_array($fields[$c],array('remarks','remark','reference')))
				{
					$aRedeem[$fields[$c]] = 0;
				}
				else
				{
					$aRedeem[$fields[$c]] = str_replace(',','',$aRedeem[$fields[$c]]);
				}
			}	

		}
	}
	if ($p1 == 'New'  or $p1 == 'Add New' )
	{
		$aRedeem = null;
		$aRedeem = array();
		$aRedeem['date'] = date('Y-m-d');
		$focus = 'xSearch';
	}
	elseif ($p1 == 'Load' && $id != '')
	{
		$q = "select * from payment_header, payment_detail 
					where
						payment_header.payment_header_id = payment_detail.payment_header_id and
						payment_header.payment_header_id = '$id'";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		
		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			exit;
		}
		$r = @pg_fetch_assoc($qr);
		
		$aRedeem = null;
		$aRedeem = array();
		$aRedeem = $r;
		if ($r['mischarge'] != 0) $aRedeem['excess'] = $r['mischarge'];		
		
		$q = "select * 
					from 
						account
					where
						account_id ='".$aRedeem['account_id']."'";
						
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
		$aRedeem += $r;
		if ($r['branch_id'] !=0) $aRedeem['branch_id'] = $r['branch_id'];
		
		if ($aRedeem['clientbank_id'] > '0')
		{
			$aRedeem['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aRedeem['clientbank_id']);
		}
		if ($aRedeem['branch_id'] > '0')
		{
			$aRedeem['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aRedeem['branch_id']);
		}
		$aRedeem['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aRedeem['admin_id']);

		$focus = 'reference';
		
	}
	elseif ($p1 == 'CancelConfirm' and chkRights2('excessamount','mdelete',$ADMIN['admin_id']))
	{
		$q = "update payment_header set status='C' where payment_header_id = '".$aRedeem['payment_header_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr)
		{
			$q = "update ledger set status='C' where reference= '".$aRedeem['payment_header_id']."' and type='C' and account_id ='".$aRedeem['account_id']."'";
			$qr = @pg_query($q) or message(pg_errormessage());

			$q = "select * from ledger where account_id ='".$aRedeem['account_id']."'";
			$qr =@pg_query($q) or message(pg_errormessage().$q);
			while ($r =@pg_fetch_object($qr))
			{
				recalculate($r->releasing_id,'noneform');
			}			
			$aRedeem['status'] = 'C';	
			message("Transaction Cancelled...");
		}
	}
	elseif ($p1 == 'selectAccount' && !chkRights2('gawad','madd',$ADMIN['admin_id']) && !chkRights2('excessamount','madd',$ADMIN['admin_id']))
	{
		message("[ You have no permission to make transaction entry in this area... ] ");
		exit;
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
		$aRedeem = null;
		$aRedeem = array();
		$aRedeem = $r;
		$aRedeem['date'] = date('Y-m-d');
		$aRedeem['admin_id'] = $ADMIN['admin_id'];
		

		if ($aRedeem['clientbank_id'] > '0')
		{
			$aRedeem['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aRedeem['clientbank_id']);
		}
		if ($aRedeem['branch_id'] > '0')
		{
			$aRedeem['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aRedeem['branch_id']);
		}

		$aRedeem['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aRedeem['admin_id']);

		$focus = 'mcheck';
		
		$q = "select releasing_id from releasing  where account_id = '".$aRedeem['account_id']."'  and status!='C' ";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		while ($r = @pg_fetch_object($qr))
		{
			recalculate($r->releasing_id,'noneform');
		}
		
		$q = "select sum(balance) as loanbal  from releasing  where account_id = '".$aRedeem['account_id']."'  and status!='C' and balance>0 ";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
//		$aRedeem['amount'] = $r['loanbal'];
		
		include_once('accountbalance.php');
	
		$aBal = excessBalance($aRedeem['account_id']);
		
		if ($aBal['balance'] < 0)
		{
			$aRedeem['amount'] = $r['loanbal'] + abs($aBal['balance']);
		} else $aRedeem['amount'] = $r['loanbal'];	
		$aRedeem['excessbal'] = abs($aBal['balance']);
		$aRedeem['excess'] = $SYSCONF['REDEEM_CHARGE'];
		$aRedeem['withdrawn'] = $aRedeem['amount']+ $aRedeem['excess'] - $aRedeem['discount'];
		$aRedeem['remark'] = $aRedeem['remarks'] = '';

	}
	elseif ($p1 == 'Print' && !in_array($aRedeem['status'], array('P','S')))
	{
		message("Save Transaction First Before Printing...");
	}
	elseif ($p1 == 'Print')
	{

		$q = "select * from payment_header, payment_detail 
					where
						payment_header.payment_header_id = payment_detail.payment_header_id and
						payment_header.payment_header_id = '".$aRedeem['payment_header_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage().$q);

		if (@pg_num_rows($qr) == '0')
		{
			message("Transaction Record NOT Found...");
			exit;
		}
		$r = @pg_fetch_assoc($qr);
		
		$aRedeem = null;
		$aRedeem = array();
		$r['excess'] = $r['mischarge'];
		$aRedeem = $r;
		$account_group_id=$aRedeem['account_group_id'];

		$q = "select * 
					from 
						account
					where
						account_id ='".$aRedeem['account_id']."'";
						
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
		$aRedeem += $r;
		$account_group = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);
		$aRedeem['account_group_id']=$account_group_id;

		if ($aRedeem['clientbank_id'] > '0')
		{
			$aRedeem['clientbank'] = lookUpTableReturnValue('x', 'clientbank','clientbank_id', 'clientbank', $aRedeem['clientbank_id']);
		}
		if ($aRedeem['branch_id'] > '0')
		{
			$aRedeem['branch'] = lookUpTableReturnValue('x', 'branch','branch_id', 'branch', $aRedeem['branch_id']);
		}
		$aRedeem['username'] = lookUpTableReturnValue('x','admin','admin_id','username', $aRedeem['admin_id']);
		
		$details = '<small3>';
		
		if ($aRedeem['mcheck'] == 'G') $type = 'REDEEM/GAWAD OUTGOING';
		else 
		{
			$type = 'TRANSFER ACCOUNT ';
		}
		
		$details .= $SYSCONF['BUSINESS_NAME']."\n".	
				$SYSCONF['BUSINESS_ADDR'].'    Tel. No.: '.$SYSCONF['BUSINESS_TEL']."\n";
		$details .= space(50).'RELEASE No. '.str_pad($aRedeem['payment_header_id'],8,'0',str_pad_left)."\n";
		$details .= str_repeat('=',76)."\n";
		$details .= adjustSize("Pay To Client : ".strtoupper(htmlspecialchars($aRedeem['account'])),45).'   '.
				adjustSize($type,28)."\n";
		$details .= adjustSize("Account Group : ".$account_group,55).'   '.
				adjustSize("Date:".ymd2mdy($aRedeem['date']),15)."\n";
		$details .= str_repeat('=',76)."\n\n";
		$as='';

		if ($aRedeem['mcheck'] =='G')
		{
			$details .= ' '.str_pad('Redeem/Gawad',16,'.').adjustRight(number_format($aRedeem['amount'],2),10)."\n";
			$as = 'Redeem/Gawad';
		}
		else
		{
			$details .= ' '.str_pad('Transfer Account',16,'.').adjustRight(number_format($aRedeem['amount'],2),10)." ";
			$as = 'Transfer Account ';
			if ($aRedeem['account_group_id'] > '0')
			{
				$details .= ' To: '.lookUpTableReturnValue('x','branch','branch_id','branch',$aRedeem['account_group_id']);
			}
			$details .= "\n";
		}
		$details .= ' '.str_pad('Charges',16,'.').adjustRight(number_format($aRedeem['excess'],2),10)."\n";
		$details .= ' '.str_pad('Discount',16,'.').adjustRight(number_format($aRedeem['discount'],2),10).
		            '  '.$aRedeem[discrem]."\n";
		$details .= "\n";
		$details .= ' '.str_pad('TOTAL',16,'.').adjustRight(number_format($aRedeem['withdrawn'],2),10)."\n";

		$details .= str_repeat('=',76)."\n";
		$details .= "Total: ".number_format($aRedeem['withdrawn'],2)."\n";
		$details .= "\n";
		$details .= "Remarks: ".$aRedeem['remark'];
		$details .= "\n";

		$details .= str_repeat('=',76)."\n";

if ($ADMIN[admin_id] == 1) echo "<pre>$details</pre>";
	
       if ($SYSCONF['PRINTER_TYPE'] == 'GRAPHIC')
       {
		   echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$details.'"'.">";
		   echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		   echo "<script>printIframe(print_area)</script>";
       }
       else
       {
			$details .= "<eject>";
			doPrint($details);
       }
  }
	
$aM = array("January","February","March","April","May","June","July","August","September","October","November","December","13th Month");
if ($aRedeem['starting_month'] == '') $aRedeem['starting_month'] =date('m');

for ($c=0;$c<8;$c++)
{
	$d=$c+1;
	$fld = 'Month'.$d;
	$$fld = $aM[($aRedeem['starting_month']+$c-1)%13];
}	

  if ($p1 == 'Search')
  {
  	if ($searchby == '') $searchby = 'account';
	  $q = "select * 
				from 
					account
				where 
					$searchby ilike '$xSearch%' ";

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
		$q .= "order by
					account
				offset 0 limit 30";
					
					
		$qr = @pg_query($q)	or message("Error Querying account file...".db_error());
		if (@pg_num_rows($qr) && $searchby == 'account_code')
		{
			$r = @pg_fetch_assoc($qr);
			$id = $r['account_id'];
			echo "<script>window.location = '?p=redeem&p1=selectAccount&c_id=$id'</script>";
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
      <td width="15%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
  </tr>
  <?
  		include_once('accountbalance.php');
	

  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		$ctr++;
		if ($r['branch_id'] == '') $branch_id = '0';
		else $branch_id = $r['branch_id'];

		$balance=0;
		$qq = "select sum(balance) as balance from releasing where account_id = '".$r['account_id']."' and status!='C'";
		$qqr = @pg_query($qq);
		$rr = @pg_fetch_object($qqr);
		$balance = $rr->balance;

		$aBal = @excessBalance($r['account_id']);
		$excess_balance = $aBal['balance'];
	
		if ($balance > 0 || $excess_balance<0)
		{
			$bgColor = '#FFFFFF';
			$href = "\"javascript: document.getElementById('f1').action='?p=redeem&p1=selectAccount&c_id=".$r['account_id']."&xSearch=$xSearch&rtype=$rtype';document.getElementById('f1').submit()\"";			
		}
		else
		{
			$bgColor = '#DADADA';
			$href = "'#' onmouseover=\"showToolTip(event,'No current loan balance...');return false\" onmouseout=\"hideToolTip()\"";
		}
  ?>
  <tr bgcolor="<?=$bgColor;?>" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?=$bgColor;?>'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href=<?= $href;?>> 
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
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= number_format($balance +  abs($excess_balance),2);?>
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
      <td height="25" colspan="2"  background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Payment 
        Entry Details </strong></font></td>
      <td height="25" colspan="2" align="center" background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Record_Id: 
        </font><font size="2">
        <input type='text' size='8' value="<?= $aRedeem['payment_header_id'];?>" name="ph_id" id="ph_id" readOnly style="text-align:center; border:0; background-color:#3399CC;font-color:#FFCCCC; padding:0;" >
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp;&nbsp;(<b> 
        <?= status($aRedeem['status']);?>
        ) </b></font> </td>
    </tr>
    <tr> 
      <td width="16%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        ( 
        <?=$aRedeem['account_code'];?>
        )</font></td>
      <td width="29%"> <input name="textfield" type="text" value="<?= $aRedeem['account'];?>" size="40" readOnly></td>
      <td width="12%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td width="43%"><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="date" type="text" id="date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= ymd2mdy($aRedeem['date']);?>" size="8">
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" color="#000000"><img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="current_month" type="hidden" id="current_month"  readOnly value="<?= substr($aRedeem['date'],5,2);?>" size="8">
        </font></strong></font></font></strong></font></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
      <td><input name="textfield2" type="text" value="<?= $aRedeem['branch'];?>" size="40" readOnly></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Encoded</font></td>
      <td> <font size="2"> 
        <?= $aRedeem['username'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank</font></td>
      <td><input name="textfield3" type="text" value="<?= $aRedeem['clientbank'];?>" size="40" readOnly></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Withdaw 
        Day </font></td>
      <td><font size="2"> 
        <?= $aRedeem['withdraw_day'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance(s) 
        Due </font></td>
      <td colspan="3"><input name="amount" type="text" id="amount" style="text-align:right" value="<?= number_format( $aRedeem['amount'],2);?>" size="12" readOnly> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Salary 
        <input name="textfield322" type="text" style="text-align:right" value="<?= number_format( $aRedeem['salary'],2);?>" size="12" readOnly>
        Last Transaction 
        <input name="textfield5" type="text" value="<?= ymd2mdy($aRedeem['last_takeout']);?>" size="12" readOnly>
        </font></td>
    </tr>
    <tr> 
      <td colspan="4"><hr size="1"></td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></td>
      <td><input name="reference" type="text" id="reference" style="text-align:right;font-size:20" onBlur="xajax_computeRedeem(xajax.getFormValues('f1'));document.getElementById('excess').focus();return false;"    onKeypress="if(event.keyCode == 13){document.getElementById('mcheck').focus();return false;}" value="<?= $aRedeem['reference'];?>" size="12"></td>
      <td>&nbsp;</td>
      <td nowrap>&nbsp;</td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transaction 
        Type</font></td>
      <td> <select  name="mcheck" id="mcheck" style="font-size:20"  onBlur="xajax_selectRedeemType(xajax.getFormValues('f1'));document.getElementById('excess').focus();return false;"  onKeypress="if(event.keyCode==13) {document.getElementById('excess').focus();return false;}">
          <option value='G' <?=($aRedeem['mcheck'] == 'G' ?'selected':'');?>>Gawad/Redeem</option>
          <option value='T' <?=($aRedeem['mcheck'] == 'T' ?'selected':'');?>>Transfer to Other Branch</option>
        </select></td>
      <td>&nbsp;</td>
      <td nowrap>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transfer 
        To </font></td>
      <td><select name="account_group_id" id="account_group_id" style="font-size:15;width:270" onBlur="xajax_selectRedeemType(xajax.getFormValues('f1'));document.getElementById('excess').focus();return false;">
	  <option value="99999">- Select Branch To Transfer - </option>
          <?
			  	$q = "select  *
						from
							branch
						where
							enable";
				$q .= " order by
							branch";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($r->branch_id == $aRedeem['account_group_id'] )
					{
						echo "<option value=$r->branch_id selected>$r->branch</option>";
					}
					else
					{
						echo "<option value=$r->branch_id>$r->branch</option>";
					}	
				}
			  ?>
        </select></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Charges</font></td>
      <td><input name="excess" type="text" readonly id="excess" style="text-align:right;font-size:20" onBlur="xajax_computeRedeem(xajax.getFormValues('f1'));document.getElementById('excess').focus();return false;"    onKeypress="if(event.keyCode == 13){document.getElementById('discount').focus();return false;}" value="<?= $aRedeem['excess'];?>" size="12"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Discount</font></td>
      <td><input name="discount" type="text" id="discount" style="text-align:right;font-size:16" onblur="xajax_computeRedeem(xajax.getFormValues('f1'));document.getElementById('discount').focus();return false;"    onkeypress="if(event.keyCode == 13){document.getElementById('remark').focus();return false;}" value="<?= $aRedeem['discount'];?>" size="8" /> 
	  <input name="discrem" id="discrem" type="text" value="<?= $aRedeem['discrem'];?>" size="40"/></td>
    </tr>
    <tr> 
      <td colspan="4"><hr></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total Due  </font></td>
      <td colspan="1"><input name="withdrawn" type="text" id="withdrawn"  readOnly style="text-align:right;font-size:20"   onKeypress="if(event.keyCode == 13){document.getElementById('save').focus();return false;}" value="<?= number_format($aRedeem['withdrawn'],2);?>" size="12"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Loan Bal : </font></td>
      <td colspan="1"><input name="loanbal" type="text" id="loanbal"  readOnly style="text-align:right;font-size:16"   onKeypress="if(event.keyCode == 13){document.getElementById('save').focus();return false;}" value="<?= number_format($aRedeem['amount']-$aRedeem['excessbal'],2);?>" size="12">
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;</font>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Excess Bal : 
      <input name="excessbal" type="text" id="excessbal"  readonly="readOnly" style="text-align:right;font-size:16"   onkeypress="if(event.keyCode == 13){document.getElementById('save').focus();return false;}" value="<?= number_format($aRedeem['excessbal'],2);?>" size="10" />
      </font></td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
      <td colspan="3"><textarea name="remark" cols="60" rows="2" id="remark"><?= $aRedeem['remark'];?></textarea></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="4"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="if(confirm('Are you sure to Save GAWAD/TRANSFER?')){wait('Please wait. Saving Payment Entry...');xajax_saveRedeem(xajax.getFormValues('f1'));return false;}else{return false;}" name="Save"  accesskey="S">
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
if ($p1 == 'viewhistory' && $aRedeem['account_id'] != '')
{
	include_once('excess.history.php');
}
if ($focus != '')
{
	echo "<script>document.getElementById('$focus').focus()</script>";
}

?>
