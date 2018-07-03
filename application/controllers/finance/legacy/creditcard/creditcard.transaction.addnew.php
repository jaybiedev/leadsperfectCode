<script>
function vAmt()
{
	f2.gross.value = 1*f2.amount.value+(f2.amount.value*(1*f2.service_charge.value/100))
}

</script>

<form name="f2" method="post" action="">
  <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date" type="text" id="4" value="<?= ymd2mdy($aCCT['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f2.date, 'mm/dd/yyyy')"></font></td>
    </tr>
    <tr> 
      <td width="35%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
        Type</font></td>
      <td width="65%" colspan="2"> 
	  <select name="bankcard_type_id" onChange="f2.action='?p=creditcard.transaction&p1=selectBankCardType';f2.submit()">
	  <option value="">Select BankCard</option>
	  <?
	  	$q = "select * from bankcard_type where enable order by bankcard_type";
		$qr = @pg_query($q);
		while ($r = @pg_fetch_object($qr))
		{
			if ($aCCT['bankcard_type_id'] == $r->bankcard_type_id)
			{
				echo "<option value=$r->bankcard_type_id selected>$r->bankcard_type</option>";
			}
			else
			{	
				echo "<option value=$r->bankcard_type_id>$r->bankcard_type</option>";
			}	
		}
	  ?>
	  </select>
        <? //= lookUpTable2("bankcard_type_id","bankcard_type","bankcard_type_id","bankcard_type",$aCCT['bankcard_type_id']);?>
      </td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card No.</font></td>
      <td colspan="2"><input name="bankcard" type="text" id="bankcard" value="<?= $aCCT['bankcard'];?>" onBlur="getElementById('searchbankcard').click()"> 
        <input name="p1" type="submit" id="searchbankcard" value="..."></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name of 
        Account</font></td>
      <td><input name="name" type="text" id="name" value="<?= $aCCT['name'];?>" size="40"></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
      <td><textarea name="address" cols="30" id="address"><?= $aCCT['address'];?></textarea></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone 
        No.</font></td>
      <td colspan="2"><input name="telno" type="text" id="telno" value="<?= $aCCT['telno'];?>"></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Trace #</font></td>
      <td><input name="trace" type="text" id="trace" value="<?= $aCCT['trace'];?>"></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Receipt 
        # </font></td>
      <td><input name="reference" type="text" id="reference" value="<?= $aCCT['reference'];?>"></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></td>
      <td><input name="amount" type="text" id="amount" value="<?= $aCCT['amount'];?>" onBlur="vAmt()">
        <input name="service_charge" type="hidden" id="service_charge" value="<?= $aCCT['service_charge'];?>"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross (+<?=number_format($aCCT['service_charge'],1);?>%)</font></td>
      <td><input name="gross" type="text" id="gross" value="<?= $aCCT['gross'];?>"></td>
      <td><img src="../graphics/savedisc.gif" width="99" height="24" onClick="f2.action='?p=creditcard.transaction&p1=Save';f2.submit()"></td>
    </tr>
  </table>
</form>
