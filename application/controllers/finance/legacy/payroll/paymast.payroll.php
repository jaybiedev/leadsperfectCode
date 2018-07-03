 <div id="payroll_payroll" style="position:relative; width:99%; height:330px; z-index:3; overflow: auto;">
     <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
  <tr bgcolor="#E2E2E2"> 
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Period</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Basic</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Net 
      Income</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">SSS</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">PHIC</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tax</font></strong></td>
    <td bgcolor="#E2E2E2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      PagIbig</font></strong></td>
  </tr>
  <?
	  $year = date('Y');
	  if ($paymast['paymast_id'] > 0)
	  {
	  	$q = "select * from payroll_header, payroll_period
					 where 
					 	payroll_header.payroll_period_id = payroll_period.payroll_period_id and
						payroll_header.status != 'C' and
						payroll_header.paymast_id = '".$paymast['paymast_id']."' and
						payroll_period.year = '$year'
					order by
						payroll_period.period1";
		$qr = @pg_query($q);
		while ($r = @pg_fetch_object($qr))
		{
	  ?>
  <tr bgcolor="#E2E2E2"> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= ymd2mdy($r->period1).' - '.ymd2mdy($r->period2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($r->total_basic,2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($r->net_income,2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($r->total_sss,2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($r->total_phic,2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($r->total_tax,2);?>
      </font></td>
    <td align="right" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= number_format($r->total_pagibig,2);?>
      </font></td>
  </tr>
  <?
				$net_income += $r->net_income;
				$total_basic += $r->total_basic;
				$total_sss += $r->total_sss;
				$total_tax += $r->total_tax;
				$total_phic += $r->total_phic;
				$total_pagibig += $r->total_pagibig;
			}
	?>
  <tr bgcolor="#E2E2E2"> 
    <td bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_basic,2);?>
      </font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($net_income,2);?>
      </font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_sss,2);?>
      </font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_phic,2);?>
      </font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_tax,2);?>
      </font></strong></td>
    <td align="right" bgcolor="#EFEFEF"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($total_pagibig,2);?>
      </font></strong></td>
  </tr>
  <?		}
		?>
</table>
</div>