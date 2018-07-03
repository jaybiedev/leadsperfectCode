<form name="form1" method="post" action="">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
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
  <table width="90%" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr bgcolor="#CCCCCC"> 
      <td width="5%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></strong></td>
      <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tracer</font></strong></td>
      <td width="9%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
        No.</font></strong></td>
      <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
      <td width="15%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
      <td width="11%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
      <td width="11%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross</font></strong></td>
      <td width="8%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Paid</font></strong></td>
      <td width="6%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
    </tr>
    <?
		$q = "select 
						bankcard_transaction.date,
						bankcard_transaction.trace,
						bankcard_transaction.reference,
						bankcard_transaction.status,
						bankcard_transaction.bankcard_transaction_id,
						bankcard_transaction.amount,
						bankcard_transaction.gross,
						bankcard_transaction.paid,
						bankcard.name,
						bankcard_type.bankcard_type,
						bankcard_type.bankcard_type_id
					from 
						bankcard_transaction, bankcard, bankcard_type 
					where
						bankcard.bankcard_id=bankcard_transaction.bankcard_id and
						bankcard_type.bankcard_type_id=bankcard.bankcard_type_id 
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
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=creditcard.payment&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
        <?= ymd2mdy($r->date);?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.payment&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
        <?= $r->trace;?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.payment&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
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
      <td colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
  </table>
</form>
