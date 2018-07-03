<?

if ($from_date == '')
{
	$from_date = date('Y-m-d');
	$to_date = date('Y-m-d');
}
else
{
	$from_date = mdy2ymd($from_date);
	$to_date = mdy2ymd($to_date);
}
?>
<form action="" method="post" name="f1" id="f1">
  <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>From Date <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="from_date" type="text" id="from_date" value="<?=ymd2mdy($from_date);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        To 
        <input name="to_date" type="text" id="to_date" value="<?=ymd2mdy($to_date);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
        </font> 
        <input name="p1" type="submit" id="p1" value="Select Date">
        <input name="p12" type="button" id="p12" value="Close" onClick="window.location='?p='"> 
        <hr> </td>
    </tr>
  </table>
</form>

<table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr bgcolor="#CCCCCC"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tracer 
      </font></strong></td>
    <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Receipt</font></strong></td>
    <td width="10%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
      No.</font></strong></td>
    <td width="21%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
    <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
    <td width="3%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross</font></strong></td>
    <td width="2%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Paid</font></strong></td>
    <td width="2%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Income</font></strong></td>
    <td width="8%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
  </tr>
  <?
		$q = "select * from bankcard_transaction, bankcard, bankcard_type 
					where
						bankcard.bankcard_id=bankcard_transaction.bankcard_id and
						bankcard_type.bankcard_type_id=bankcard.bankcard_type_id and
						date>= '$from_date' and 
						date<='$to_date' ";
						
		$q .= "	order by bankcard_transaction_id desc";

		$qr = @pg_query($q) or message(pg_errormessage());
		$ctr=0;
		$total_amount = $total_paid = $total_gross;
		while ($r = @pg_fetch_object($qr))
		{
			$total_amount += $r->amount;
			$total_paid += $r->paid;
			$total_gross += $r->total_gross;
			$ctr++;
	?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= ymd2mdy($r->date);?>
      </a></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= $r->reference;?>
      </a></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=creditcard.transaction&p1=Load&id=<?=$r->bankcard_transaction_id;?>"> 
      <?= $r->bankcard;?>
      </a></font></td>
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
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($r->gross - $r->paid,2);?>
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
    <td><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>&nbsp;Total</strong></font></td>
    <td align="right" nowrap><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($total_amount,2);?>
      </font></strong></td>
    <td align="right" nowrap><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_gross,2);?>
      </font></strong></td>
    <td align="right" nowrap><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_paid,2);?>
      </font></strong></td>
    <td align="right" nowrap><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_gross - $total_paid,2);?>
      </font></strong></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?
if ($focus != '')
{
	echo "<script>$focus.focus()</script>";
}
?>

