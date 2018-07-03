<?
if (!session_is_registered('aPay'))
{
	session_register('aPay');
	$aPay = null;
	$aPay = array();
}
if (!session_is_registered('aPayD'))
{
	session_register('aPayD');
	$aPayD = null;
	$aPayD = array();
}
if (!session_is_registered('iPayD'))
{
	session_register('iPayD');
	$iPayD = null;
	$iPayD = array();
}

if ($aPay != null && !in_array($p1,array('Browse','Go','Next','Previous')))
{
	echo "<script>window.location='?p=payment.entry'</script>";
	exit;
}
if ($date == '') $date = date('m/d/Y');
?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        Payment : Date 
        <input name="date" type="text" id="date" value="<?= $date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p1" type="button" id="p1" value="Search By Client" onClick="window.location='?p=payment.browse.client'";>
        <input name="p1" type="button" id="p1" value="New Group" onClick="window.location='?p=payment.entry&p1=New'">
        </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <!-- <input name="p12" type="button" id="p12" value="New Withdrawal" onClick="window.location='?p=payment.withdraw&p1=New'"> -->
        </font></td>
    </tr>
    <tr> 
      <td><hr color="#993300"></td>
    </tr>
  </table>
  <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#C2D6C0" background="../graphics/table_horizontal.PNG"> 
      <td height="23" colspan="8"  background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        &nbsp;<img src="../graphics/blue_bullet.gif" width="11" height="10"><strong><font color="#E1E9F1">Payment 
        /Collection </font></strong></font></td>
    </tr>
    <tr> 
      <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="15%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Encoded</font></strong></td>
      <td width="25%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></strong></td>
      <td width="20%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Collector</font></strong></td>
      <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
      <td width="5%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></strong></td>
      <td width="5%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
      <td width="16%">&nbsp;</td>
    </tr>
    <?
	if ($date == '') $date=date('m/d/Y');
	$mdate = mdy2ymd($date);
	$q = "select * from payment_header where  (date='$mdate' or date_withdrawn='$mdate') and not mcheck in ('G','T')";
	$qr = @pg_query($q) or message (pg_errormessage().$q);
	if (@pg_num_rows($qr) == 0)
	{
		message('No Transaction Found for '.$date);
	}
	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;
		      
		if ($r->account_group_id != '')
		{
		 	$particulars = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);
			$href = '?p=payment.entry&p1=Load&id='.$r->payment_header_id;
		}
		elseif ($r->entry_type=='W')
		{
			$particulars = "Passbook/ATM Withdrawal Entry";
			$href = '?p=payment.withdraw&p1=Load&id='.$r->payment_header_id;
		}	
		elseif ($r->entry_type=='I')
		{
			$particulars = "Passbook/ATM Withdrawal Entry";
			$href = '?p=payment.individual&p1=Load&id='.$r->payment_header_id;
		}
		
		if ($r->clientbank_id == '')
		{
			$clientbank = '';
		}
		else
		{
			$clientbank = lookupTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id);
		}	
		if ($r->status == 'C')
		{
			$bgColor = '#FFCCCC';
		}
		else
		{
			$bgColor = '#FFFFFF';
		}
	?>
    <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?=$bgColor;?>'" bgcolor="<?=$bgColor;?>"> 
      <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=$ctr;?>
        .</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="<?=$href;?>"> 
        <img src="../graphics/b_edit.png" alt="Click To Edit Payment Entry" width="11" height="12" border="0"></a> 
        <a href="<?=$href;?>"> 
        <?= ymd2mdy($r->date);?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $particulars;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->name;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="<?=$href;?>"> 
        <?= status($r->status);?>
        </a></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->reference;?></font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?= number_format($r->total_amount,2);?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id);?>
        </font></td>
    </tr>
    <?
	}
	?>
  </table>
</form>
