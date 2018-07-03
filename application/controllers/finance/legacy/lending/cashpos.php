<?
if (!chkRights2('cashpos','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Cash Position ]...");
	exit;
}
	
/*if ($ADMIN['branch_id'] > '0')
{
	$branch_id = $ADMIN['branch_id'];
}*/

if ($branch_id == '')  	$branch_id = lookUpTableReturnValue('x','branch','local','branch_id','Y');

if ($date == '') $date=date('Y-m-d');

if ($p1 == 'Go')
{
	$date = mdy2ymd($_REQUEST['date']);
}
$revolving_begin = $revolving_in = $revolving_out = $revolving_balance = 0;
$petty_begin = $petty_in = $petty_out = $petty_balance = 0;
$collect_begin = $collect_in = $collect_out = $collect_balance = 0;

$q = "select * from cashpos 
			where 
				date='$date' and enable ";
$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
$q .= ") ";
$q .= "		order by cashpos_id";
$qr = @pg_query($q) or message('Unable to query database...');
while ($r=@pg_fetch_object($qr))
{
	if ($r->type == 'P')
	{
		if ($petty_begin == 0 && $r->descr == 'Beginning Balance')
		{
			$petty_begin = $r->debit;
		}
		else
		{
			$petty_in += $r->debit;
			$petty_out += $r->credit;
		}
		$petty_balance = $r->balance;
	}
	elseif ($r->type == 'C')
	{
		if ($collect_begin == 0 && $r->descr == 'Beginning Balance')
		{
			$collect_begin = $r->debit;
		}
		else
		{
			$collect_in += $r->debit;
			$collect_out += $r->credit;
		}
		$collect_balance = $r->balance;
	}
	else
	{
		if ($revolving_begin == 0 && $r->descr == 'Beginning Balance')
		{
			$revolving_begin = $r->debit;
		}
		else
		{
			$revolving_in += $r->debit;
			$revolving_out += $r->credit;
		}
		$revolving_balance = $r->balance;
	}
}
$total_in = $revolving_in + $collect_in + $petty_in;
$total_out = $revolving_out + $collect_out + $petty_out;
$total_balance = $revolving_balance + $collect_balance + $petty_balance;

?>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>Cash Position:</strong> Branch 
        <select name = "branch_id">
          <?
				$q = "select * from branch where enable ";
				
				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					$q .= ") ";
				} else
				{
					?>
          <option value=''>Select Branch</option>
          <?
				}
				$q .=  "order by branch";
				
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($branch_id == $r->branch_id)
					{
						echo "<option value=$r->branch_id selected>$r->branch</option>";
					}
					else
					{	
						echo "<option value=$r->branch_id>$r->branch</option>";
					}	
				}
				
			?>
        </select>
        For Date </font> 
        <input name="date" type="text" id="cashpos_date" value="<?= ymd2mdy($date);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
      <input type="submit" value="Go" name="p1"> </td>
    </tr>
    <tr> 
      <td><hr color="red"></td>
    </tr>
  </table>
  <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr> 
      <td height="70" colspan="6"><input name="p1" type="button" id="p1" value="Revolving" style="font-size:25" onClick="window.location='?p=cashpos.revolving'">
        <input name="p1" type="button" id="p1" value="Petty Cash"  onClick="window.location='?p=cashpos.petty'" style="font-size:25">
        <input name="p12" type="button" id="p12" value="Collection"  onClick="window.location='?p=cashpos.collection'"  style="font-size:25"></td>
    </tr>
    <tr background="../graphics/table0_horizontal.PNG"> 
      <td height="30" colspan="6"><font color="#EFEFEF" size="3" face="Verdana, Arial, Helvetica, sans-serif"><b>Transaction 
        Summary for the Day</b></font></td>
    </tr>
    <tr> 
      <td width="8%" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="32%" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong><strong></strong></td>
      <td width="15%" align="center" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Beginning</font></strong></td>
      <td width="14%" align="center" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Toal-In</font></strong></td>
      <td width="14%" align="center" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total-Out</font></strong></td>
      <td width="17%" align="center" bgcolor="#C2D6C0"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
    </tr>
    <tr> 
      <td height="25">&nbsp;</td>
      <td>Revolving Fund</td>
      <td align="right"><?= number_format($revolving_begin,2);?></td>
      <td align="right">
        <?= number_format($revolving_in,2);?>
      </td>
      <td align="right">
        <?= number_format($revolving_out,2);?>
      </td>
      <td align="right">
        <?= number_format($revolving_balance,2);?>
      </td>
    </tr>
    <tr> 
      <td height="25" bgcolor="#FFFFFF">&nbsp;</td>
      <td bgcolor="#FFFFFF">Petty Cash</td>
      <td align="right" bgcolor="#FFFFFF">
        <?= number_format($petty_begin,2);?>
      </td>
      <td align="right" bgcolor="#FFFFFF"> 
        <?= number_format($petty_in,2);?>
      </td>
      <td align="right" bgcolor="#FFFFFF">
        <?= number_format($petty_out,2);?>
      </td>
      <td align="right" bgcolor="#FFFFFF">
        <?= number_format($petty_balance,2);?>
      </td>
    </tr>
    <tr> 
      <td height="29">&nbsp;</td>
      <td>Collection</td>
      <td align="right">
        <?= number_format($collect_begin,2);?>
      </td>
      <td align="right"> 
        <?= number_format($collect_in,2);?>
      </td>
      <td align="right"> 
        <?= number_format($collect_out,2);?>
      </td>
      <td align="right">
        <?= number_format($collect_balance,2);?>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#FFFFFF">&nbsp;</td>
      <td bgcolor="#FFFFFF">&nbsp;</td>
      <td bgcolor="#FFFFFF">&nbsp;</td>
      <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
      <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
      <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
    </tr>
    <tr> 
      <td bgcolor="#DADADA">&nbsp;</td>
      <td bgcolor="#DADADA">Total</td>
      <td align="right" bgcolor="#DADADA">
        <?= number_format($total_begin,2);?>
      </td>
      <td align="right" bgcolor="#DADADA">
        <?= number_format($total_in,2);?>
      </td>
      <td align="right" bgcolor="#DADADA">
        <?= number_format($total_out,2);?>
      </td>
      <td align="right" bgcolor="#DADADA">
        <?= number_format($total_balance,2);?>
      </td>
    </tr>
  </table>
  </form>
  <script>
  if (f1.branch_id.value != '')
  {
  	f1.descr.focus()
  }
  else
  {
  	f1.branch_id.focus()
  }
  </script>
