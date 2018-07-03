<form name="form1" method="post" action="">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>Find 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>"><?= lookUpAssoc('searchby',array('Tracer #'=>'trace','Receipt'=>'reference'),$searchby);?>
        <input name="p1" type="submit" id="p1" value="Go">
        <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Show 
        Unpaid | Show Paid | Sort by Card No | Sort by Trace# </font></strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        </font> 
        <hr></td>
    </tr>
  </table>
  <?
	if ($p1 == 'Add New' && $id != '')
	{
		$q = "select * from bankcard_transaction, bankcard where bankcard.bankcard_id=bankcard_transaction.bankcard_id and
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
		$entry_screen =1;
	}
  if ($entry_screen == 1)
  {
  	include_once("creditcard.bankpayment.addnew.php");
  }
  ?>
  <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr bgcolor="#CCCCCC"> 
      <td width="5%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tracer 
        </font></strong></td>
      <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Receipt</font></strong></td>
      <td width="9%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
        No.</font></strong></td>
      <td width="30%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
      <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
      <td width="9%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
      <td width="9%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Paid</font></strong></td>
      <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
    </tr>
    <?
		$q = "select * from bankcard_transaction, bankcard, bankcard_type 
					where
						bankcard.bankcard_id=bankcard_transaction.bankcard_id and
						bankcard_type.bankcard_type_id=bankcard.bankcard_type_id and
						date='".$aCCT['browse_date']."'
					order by
						bankcard_transaction_id desc";
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
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=creditcard.bankpayment&p1=Add New&id=<?= $r->bankcard_transaction_id;?>"> 
        <?= $r->trace;?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.bankpayment&p1=Add New&id=<?= $r->bankcard_transaction_id;?>"> 
        <?= $r->reference;?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.bankpayment&p1=Add New&id=<?= $r->bankcard_transaction_id;?>"> 
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
        <?= number_format($r->paid,2);?>
        </font> </td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->status;?>
        </font></td>
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
</form>
