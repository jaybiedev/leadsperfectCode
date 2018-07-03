			<div id="Layer1" style="position:virtual; width:100%; z-index:1; height: 300px; overflow: auto;">
          
  <table width="60%">
    <tr bgcolor="#DADADA"> 
      <td colspan="1"><font size='2'>Date</font></td>
      <td><font size='2'>Type</font></td>
      <td colspan="1"><font size='2'>Debit</font></td>
      <td colspan="1"><font size='2'>Credit</font></td>
      <td colspan="1"><font size='2'>Balance</font></td>
    </tr>
    <? 
			  	
			  	if ($aLoan['account_id'] != '' && $aLoan['releasing_id'] != '')
				{
					$l_balance = $l_ammort = 0;
					$q = "select * from ledger where account_id = '".$aLoan['account_id']."'   and  releasing_id = '".$aLoan['releasing_id']."'  order by date ";
					$qr = @pg_query($q) or message1(pg_errormessage());
					$l_balance=$l_credit = 0;
					while ($r = @pg_fetch_object($qr))
					{
						$l_balance += $r->debit - $r->credit;
						$l_credit += $r->credit;	
						echo "<tr><td><font size=2>".ymd2mdy($r->date)."</font></td>";
						echo "<td align='right'><font size=2>".($r->type)."</font></td>";
						echo "<td align='right'><font size=2>".number_format2($r->debit,2)."</font></td>";
						echo "<td align='right'><font size=2>".number_format2($r->credit,2)."</font></td>";
						echo "<td align='right'><font size=2>".number_format($l_balance,2)."</font></td>";
					}
						echo "<tr><td><font size=2></font></td>";
						echo "<td ><font size=2><b>Total</b></font></td>";
						echo "<td align='right'><font size=2><b>".number_format($l_credit,2)."</b></font></td>";
						echo "<td align='right'><font size=2><b>".number_format($l_balance,2)."</b></font></td>";

				}
			  ?>
  </table>
            </div>
