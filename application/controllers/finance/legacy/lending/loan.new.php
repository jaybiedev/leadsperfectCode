<form action="" method="post" name="f1" id="f1" >
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search Loan Releases 
        <input name="xSearchAcct" type="text" id="xSearchAcct" value="<?= $xSearchAcct;?>"> 
        <?= lookUpAssoc('searchby',array('Account'=>'account','Reference'=>'reference','Date'=>'date','Amount'=>'amount'), $searchby);?>
        <input name="p1" type="button" id="p1" value="Go" onClick="window.location='?p=loan.releasing.browse&p1=Go&xSearch='+xSearchAcct.value+'&searchby='+searchby.value"> 
		<input type="button" name="Submit2" value="Browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'"> 
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>

  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td align="center"><font size="4" face="Times New Roman, Times, serif"> <strong>New 
        Loan Application</strong></font></td>
    </tr>
    <tr> 
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        Account 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" size="30">
        <input name="p1" type="submit" id="p1" value="Search">
        <input name="p12" type="button" id="p12" value="New Account" onClick="window.location='?p=account&p1=New'">
        </font></td>
    </tr>
  </table>
</form>
<script>document.getElementById('xSearch').focus()</script>
<?
if (!chkRights3('releasing','madd',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}
if ($p1 == 'Search')
{

?>
<table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCFF"> 
    <td colspan="5"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Select 
      Account</strong></font></td>
  </tr>
  <tr> 
    <td width="6%" height="19"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="46%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      Account Name</font></strong></td>
    <td width="23%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></strong></td>
    <td width="12%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
  </tr>
  <?
  $q = "select * from account where enable and account ilike '$xSearch%' order by account";
  $qr = pg_query($q) or message(pg_errormessage());
  $ctr=0;
  while ($r = pg_fetch_object($qr))
  {
  	$ctr++;
	$account_balance = accountBalance($r->account_id);
	if ($r->account_status == 'I')
	{
		$href = "javascript: alert('Account is In-Active');";
		$href1 = "javascript: alert('Account is In-Active');";
	}
	elseif ($account_balance > 0)
	{
		$href = "?p=loan.new&p1=addon&aid=$r->account_id";
		$href1 = "window.location='?p=loan.new&p1=addon&aid=$r->account_id'";
	}
	else
	{
		$href = "?p=loan&p1=New&aid=$r->account_id";
		$href1 = "window.location='?p=loan&p1=New&aid=$r->account_id'";
	}
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="<?=$href1;?>"> 
    <td align="right" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="<?=$href;?>"> 
      <?=$r->account;?>
      </a> </font></td>
    <td bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);?>
      </font></td>
    <td align="right" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $account_balance;?>&nbsp;</font></td>
    <td bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= status($r->account_status);?>
      </font></td>
  </tr>
  <?
  }
  ?>
</table>
<?
}
elseif ($p1 == 'addon' && $aid!='')
{
	$q = "select * from releasing where balance>0 and account_id='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
?>
<div align="center">
<form action="" method="post" name="f2" id="f2">
    <strong><font size="5"> <em> 
    <?=lookUpTableReturnValue('x','account','account_id','account',$aid);?>
    </em></font> <em><font color="#990000" size="5">has existing account balance 
    </font></em></strong><font color="#990000" size="5"> 
    <hr width="75%">
    <table width="65%" border="0" cellspacing="1" cellpadding="3">
      <tr bgcolor="#CCCCCC"> 
        <td width="10%">&nbsp;</td>
        <td colspan="2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Released</font></strong></td>
        <td width="19%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
      </tr>
      <?
	$ctr=0;
	$total_balance = 0;
	while ($r = pg_fetch_object($qr))
	{
		recalculate($r->releasing_id,'noneform');
		$q  = "select balance from releasing where releasing_id = '$r->releasing_id'";
		$qqr = @pg_query($q) or message(pg_errormessage().$q);
		$rr = @pg_fetch_object($qqr);
		
		$total_balance += $rr->balance;
		$ctr++;
	?>
      <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgColor='#FFFFFF'> 
        <td align="right" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= $ctr;?>
          . 
          <input name="restructure[]" type="checkbox" value="<?= $r->releasing_id;?>" checked>
          </font></td>
        <td width="17%" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= ymd2mdy($r->date);?>
          </font></td>
        <td width="33%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id);?>
          </font></td>
        <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input name="advancechange2" type="text" id="advancechange2" style="text-align:right; background:none; border:none" value ="<?= number_format($rr->balance,2);?>" size="15" readOnly>
          </font></td>
      </tr>
      <?
  }
  
  	$q = "select sum(excess)  as debit
				from
					payment_header,
					payment_detail
				where
					payment_header.payment_header_id = payment_detail.payment_header_id and
					payment_header.status!='C' and
					payment_detail.account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$debit = $r->debit;
						
  	$q = "select sum(gross_amount)  as credit from wexcess where account_id = '$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$credit = $r->credit;

	$advancechange = $credit - $debit;
	if ($advancechange > 0)
	{
		$total_balance += $advancechange;
		$ctr++;
  ?>
      <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgColor='#FFFFFF'> 
        <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= $ctr;?>
          . 
          <input name="restructure[]" type="checkbox" value="advancechange" checked>
          </font></td>
        <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advanced 
          Change</font></td>
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input name="advancechange" type="text" id="advancechange" style="text-align:right; background:none; border:none" value ="<?= number_format($advancechange,2);?>" size="15" readOnly>
          </font></td>
      </tr>
      <?
 }
 ?>
      <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgColor='#FFFFFF'> 
        <td colspan="3" align="center" nowrap>Total</td>
        <td align="right"> 
          <?= number_format($total_balance,2);?>
        </td>
      </tr>
    </table>
    </font><br>
  <br>
    <input name="Input" type="button" value="Restructure All Checked"  onClick="document.getElementById('f2').action='?p=loan&p1=RestructureChecked';document.getElementById('f2').submit()">
    <input name="Input" type="button" value="Add Another Account"  onClick="window.location='?p=loan&p1=New&aid=<?=$aid;?>&add=1'">
  </form>
  <?
}
?>
</div>
