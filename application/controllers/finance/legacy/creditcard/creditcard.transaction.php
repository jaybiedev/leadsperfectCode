<?
if (!session_is_registered('aCCT'))
{
	session_register('aCCT');
	$aCCT = null;
	$aCCT = array();
}


$fields = array('bankcard_type_id','service_charge','bankcard','name','address','telno','gross','amount','date','trace','reference');
if (!in_array($p1,array(NULL,'Load','Edit','Select Date')))
{
	for($c=0;$c<count($fields);$c++)
	{
		if ($fields[$c] == 'date')
		{
			$aCCT[$fields[$c]] = stripslashes(mdy2ymd($_REQUEST[$fields[$c]]));
		}
		else
		{
			$aCCT[$fields[$c]] = stripslashes($_REQUEST[$fields[$c]]);
		}	
	}
}

if ($p1 == 'Select Date')
{
	$aCCT['browse_date']= mdy2ymd($_REQUEST['browse_date']);
}
elseif ($p1 == 'selectBankCardType')
{
	 if ($aCCT['bankcard_type_id'] == '')
	 {
	 	message("Specify BankCard...");
		
	 }
	 else
	 {
		$q = "select service_charge from bankcard_type where bankcard_type_id='".$aCCT['bankcard_type_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$aCCT['service_charge'] = $r->service_charge;
	}
	$entry_screen = 1;
}
elseif ($p1 == '...')
{
	if ($aCCT['bankcard_type_id'] == '')
	{
		message("Select Bankcard Type...");
		$focus = 'f2.bankcard_type_id';
	}
	elseif ($aCCT['bankcard'] == '')
	{
		$focus = 'f2.bankcard';
		message("Specify Card No...");
	}
	else
	{
		$q = "select * from bankcard where bankcard='".$aCCT['bankcard']."' and bankcard_type_id='".$aCCT['bankcard_type_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if (@pg_numrows($qr)>0)
		{
			$r = @pg_fetch_object($qr);
			$aCCT['name'] = $r->name;
			$aCCT['address'] = $r->address;
			$aCCT['telno'] = $r->telno;
			$aCCT['bankcard_id'] = $r->bankcard_id;
		}
		else
		{
			message("Bankcard has NO Record. Creating New.");
		}
		$focus = 'f2.name';
	}	
	$entry_screen =1;
}
elseif ($p1 == 'Save' && $aCCT['bankcard_type_id'] == '')
{
	message("Specify Bankcard...");
}
elseif ($p1 == 'Save' && $aCCT['bankcard'] == '')
{
	message("Specify Bankcard Card Number...");
}
elseif ($p1 == 'Save' && $aCCT['amount'] > 0)
{
	$ok=true;
	begin();
	if ($aCCT['bankcard_id'] == '')
	{
		$q = "insert into bankcard (bankcard_type_id,bankcard,name,address,telno,status)
				values ('".$aCCT['bankcard_type_id']."','".$aCCT['bankcard']."','".$aCCT['name']."',
						'".$aCCT['address']."','".$aCCT['telno']."','S')";
		$qr = @pg_query($q) or message(pg_errormessage().$q);				
		if (!$qr)
		{
			$ok=false;
		}
		else
		{
				$q = "select currval('bankcard_bankcard_id_seq')" ;
				$r = fetch_object($q);
				$aCCT['bankcard_id'] = $r->currval;
		}		
	}
	else
	{
		$q = "update bankcard set name='".$aCCT['name']."', address='".$aCCT['address']."',
								telno='".$aCCT['telno']."'
						where
								bankcard_id='".$aCCT['bankcard_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());				
		if (!$qr)
		{
			$ok=false;
		}

	}
	if ($ok)
	{
		if ($aCCT['bankcard_transaction_id'] == '')
		{
			$q = "insert into bankcard_transaction (bankcard_id,date,gross,amount,trace, reference, admin_id)
					values ('".$aCCT['bankcard_id']."','".$aCCT['date']."','".$aCCT['gross']."','".$aCCT['amount']."',
							'".$aCCT['trace']."','".$aCCT['reference']."','".$ADMIN['admin_id']."')";
			$qr = @pg_query($q) or message(pg_errormessage().$q);				
			if (!$qr)
			{
				$ok=false;
			}
			else
			{
				$q = "select currval('bankcard_transaction_bankcard_transaction_id_seq')" ;
				$r = fetch_object($q);
				$aCCT['bankcard_transaction_id'] = $r->currval;
			}
		}
		else
		{
			$q = "update bankcard_transaction set bankcard_id='".$aCCT['bankcard_id']."', date='".$aCCT['date']."', 
									admin_id='".$ADMIN['admin_id']."',
									trace='".$aCCT['trace']."',
									reference='".$aCCT['reference']."',
									gross='".$aCCT['gross']."',
									amount='".$aCCT['amount']."'
							where
									bankcard_transaction_id='".$aCCT['bankcard_transaction_id']."'";
			$qr = @pg_query($q) or message(pg_errormessage());				
			if (!$qr)
			{
				$ok=false;
			}
		}
	}
	if ($ok)
	{
		commit();
		message("Transaction Saved...");
	}
	else
	{
		rollback();
	}
	$entry_screen=1;
	
}
elseif (($p1 == 'Edit' || $p1 == 'Load') && $id != '')
{
	$q = "select * from bankcard_transaction, bankcard, bankcard_type where bankcard.bankcard_id=bankcard_transaction.bankcard_id and
				bankcard_type.bankcard_type_id=bankcard.bankcard_type_id and
				bankcard_transaction_id='$id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_numrows($qr) >0)
	{
		$r = @pg_fetch_assoc($qr);
		$browse_date= $aCCT['browse_date'];
		$aCCT=null;
		$aCCT=array();
		$aCCT = $r;
		$entry_screen=1;
	}	
	else
	{
		message("Record nor found...");
	}
}
elseif ($p1 == 'Add New')
{
	$browse_date= $aCCT['browse_date'];
	$aCCT=null;
	$aCCT=array();
	if ($aCCT['date'] == '' || $aCCT['date'] == '--' || $aCCT['date'] == '//')
	{
		$aCCT['date'] = date('Y-m-d');
	}
	$aCCT['browse_date']= $browse_date;
	$entry_screen=1;
	
}
if ($aCCT['browse_date'] == '')
{
	$aCCT['browse_date'] = date('Y-m-d');
}
?>
<form action="" method="post" name="f1" id="f1">
  <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>Date <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="browse_date" type="text" id="4" value="<?= ymd2mdy($aCCT['browse_date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.browse_date, 'mm/dd/yyyy')"></font> 
        <input name="p1" type="submit" id="p1" value="Select Date">
        Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
        <?=lookUpAssoc('searchby',array('Tracer#'=>'trace','Reference'=>'reference','BankCard'=>'bankcard','Holder Name'=>'name'),$searchby);?>
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p1" type="submit" id="p1" value="Add New">
        <input name="p12" type="button" id="p12" value="Close" onClick="window.location='?p='"> 
        <hr> </td>
    </tr>
  </table>
</form>
<?

	
if ($entry_screen==1)
{
	include_once("creditcard.transaction.addnew.php");
}
?>
<table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr bgcolor="#CCCCCC"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tracer 
      </font></strong></td>
    <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Receipt</font></strong></td>
    <td width="10%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
      No.</font></strong></td>
    <td width="31%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="21%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
    <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
    <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross</font></strong></td>
    <td width="8%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
  </tr>
  <?
		$q = "select * from bankcard_transaction, bankcard, bankcard_type 
					where
						bankcard.bankcard_id=bankcard_transaction.bankcard_id and
						bankcard_type.bankcard_type_id=bankcard.bankcard_type_id ";
						
		if ($search != '')
		{
			$q .= " and $searchby ilike '$search%'";
		}
		else
		{
			$q .= " and	date='".$aCCT['browse_date']."'";
		}				
		$q .= "	order by bankcard_transaction_id desc";

		$qr = @pg_query($q) or message(pg_errormessage());
		$ctr=0;
		while ($r = @pg_fetch_object($qr))
		{
			$ctr++;
	?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= $r->trace;?>
      </a></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= $r->reference;?>
      </a></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= $r->bankcard;?>
      </a></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->name;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->bankcard_type;?>
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($r->amount,2);?>
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= number_format($r->gross,2);?>
      </font></td>
    <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->status;?>
      </font> </td>
  </tr>
  <?
  }
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
  </tr>
</table>
<?
if ($focus != '')
{
	echo "<script>$focus.focus()</script>";
}
?>

