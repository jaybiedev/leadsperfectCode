 <div id="payroll_ledger" style="position:relative; width:99%; height:330px; z-index:3;">
    
  <table width="70%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
    <tr bgcolor="#E2E2E2"> 
      <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account</font></strong></td>
      <td bgcolor="#E2E2E2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></td>
      <td bgcolor="#E2E2E2">&nbsp;</td>
    </tr>
    <?
	  $year = date('Y');
	  if ($paymast['paymast_id'] > 0)
	  {
	  	$q = "select 
						deduction_type,
						sum(credit) as credit,
						sum(debit) as debit
					from
						payrollcharge,
						deduction_type
					 where 
					 	payrollcharge.deduction_type_id = deduction_type.deduction_type_id and
						payrollcharge.enable='Y' and
						payrollcharge.paymast_id = '".$paymast['paymast_id']."'
					group by
						deduction_type";
		$qr = @pg_query($q) or message1(pg_errormessage());
		$total_balance = 0;
		while ($r = @pg_fetch_object($qr))
		{
			$balance = $r->credit - $r->debit;
			$total_balance += $balance;
	  ?>
    <tr bgcolor="#E2E2E2"> 
      <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?= $r->deduction_type;?>
        </font></td>
      <td bgcolor="#EFEFEF" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?= number_format($balance,2);?>
        </font></td>
      <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
	<?
	}
	?>
    <tr bgcolor="#E2E2E2">
      <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Total</strong></font></td>
      <td bgcolor="#EFEFEF" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
        <?= number_format($total_balance,2);?>
        </strong> </font></td>
      <td bgcolor="#EFEFEF">&nbsp;</td>
    </tr>
    <?
		}
		?>
  </table>
  </div>
