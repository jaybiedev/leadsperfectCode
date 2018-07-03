<?
	if ($aBankRecon['branch_id'] == '' && $ADMIN['branch_id']>'0')
	{
		$aBankRecon['branch_id'] =$ADMIN['branch_id'];
	}

	//--recompute
	if ($aBankRecon['branch_id'] > 0)
	{
		$q = "select * from deposit 
			where 
				branch_id= '".$aBankRecon['branch_id']."' 
			order by 
				date offset 0 limit 1";
		$qr = @pg_query($q) or message1(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$balance = $r->add - $r->minus;
		$date = $r->date;
		while (true)
		{
			$beginning = $balance;
			if ($beginning == '') $beginning=0;

			$q = "select * from deposit 
				where 
					branch_id= '".$aBankRecon['branch_id']."' and
					date>'$date'
				order by date offset 0 limit 1";
			$qqr = @pg_query($q) or message1(pg_errormessage());

			if (@pg_num_rows($qqr) == 0) break;
			$rr = @pg_fetch_object($qqr);
			$balance = $beginning + $rr->add - $rr->minus;
			$date = $rr->date;
			$deposit_id = $rr->deposit_id;

			$q = "update deposit set beginning = '$beginning',balance = '$balance'
					 where 
						deposit_id='$deposit_id'";
			$qqqr = @pg_query($q) or message1(pg_errormessage().$q);
		}
	}
	//-- end of recompute

	$q = "select * from deposit where branch_id ='".$aBankRecon['branch_id']."' and  date ='".$aBankRecon['date']."'";
	$qr = @pg_query($q) or message(pg_errormessage());
	
	$r = @pg_fetch_object($qr);
	
	$beginning = $r->beginning;
	$add = $r->add;
	$total_deposits = $r->minus;
	$balance = $r->balance;
	
	$q = "select sum(debit - credit) as thisbank from bankrecon where
				branch_id ='".$aBankRecon['branch_id']."' and
				bank_id = '".$aBankRecon['bank_id']."' and
				date = '".$aBankRecon['date']."' and
				type='D' and
				enable=TRUE";

	$qr = @pg_query($q) or message(pg_errormessage());
	
	$r = @pg_fetch_object($qr);

	$deposit_thisbank = $r->thisbank;
	$deposit_otherbank = $add - $deposit_thisbank;			
?>
<div id="browsePLULayer" name="browsePLULayer"  style="position:absolute; width:400px; height:100px; z-index:1; left: 5%; top:20%; "> 
  <table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
    <tr> 
      <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="6" height="31"></td>
      <td width="48%"  height="31"align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <b> View Undeposited</b></font></td>
      <td width="49%" height="31" align="right" background="../graphics/table0_horizontal.PNG">
	  <img src="../graphics/table_close.PNG" onClick="document.getElementById('browsePLULayer').style.display='none'; document.getElementById('type').focus()" ></td>
      <td width="2%" align="right"><img src="../graphics/table0_upper_right.PNG" width="6" height="30"></td>
    </tr>
    <tr valign="top" bgcolor="#A4B9DB"> 
      <td colspan="4" height="100px"> 
          <table width="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
            <tr> 
              <td width="42%">&nbsp;</td>
              <td width="58%">&nbsp;</td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
              <td><input name="textfield" type="text" readOnly style="font-weight:bold; font-size:18px;background:#99DDFF;" value="<?=ymd2mdy( $aBankRecon['date']);?>" size="15"></td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Beginning</font></td>
              
            <td><input name="textfield2" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($beginning,2);?>" size="15"></td>
            </tr>
            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Additional 
                Undeposited</font></td>
              
            <td><input name="textfield3" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($add,2);?>" size="15"></td>
            </tr>
            <tr> 
              
            <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Deposits 
              Made (This Bank)</font></td>
              
            <td><input name="textfield4" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($deposit_thisbank,2);?>" size="15"></td>
            </tr>
            <tr> 
              
            <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Deposits 
              Made (Other Bank)</font></td>
              
            <td><input name="textfield5" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($deposit_otherbank,2);?>" size="15"></td>
            </tr>
            <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total 
              Deposits Made</font></td>
              
            <td><input name="textfield6" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($total_deposits,2);?>" size="15"></td>
            </tr>
            <tr> 
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Undeposited 
              Balance</font></td>
              
            <td><input name="textfield7" type="text"  readOnly style="text-align:right; font-weight:bold; font-size:18px;background:#99DDFF;" value="<?= number_format($balance,2);?>" size="15"></td>
            </tr>
            <tr> 
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr align="center"> 
              <td colspan="2"><input type="submit"  readOnly name="Submit" id="closewindow" value="Close" onClick="document.getElementById('browsePLULayer').style.display='none'; document.getElementById('type').focus()" ></td>
            </tr>
          </table>
		</td>
    </tr>
    <tr> 
      <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG">
      </td>
    </tr>
  </table>
  </div>
  <script>document.getElementById('closewindow').focus()</script>
