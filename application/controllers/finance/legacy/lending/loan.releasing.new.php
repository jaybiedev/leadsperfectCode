<form action="" method="post" name="f1" id="f1" >
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search Loan Releases 
        <input name="xSearchAcct" type="text" id="xSearchAcct" value="<?= $xSearchAcct;?>"> 
        <?= lookUpAssoc('searchby',array('Account'=>'account','Reference'=>'reference','Date'=>'date','Amount'=>'amount'), $searchby);?>
        <input name="p1" type="button" id="p1" value="Go" onClick="window.location='?p=loan.releasing.browse&p1=Go&xSearch='+xSearchAcct.value+'&searchby='+searchby.value"> 
		<input type="button" name="Submit2" value="Browse" onClick="window.location='?p=loan.releasing.browse&p1=Browse'"> 
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="p1" type="submit" id="p1" value="Select" />
        </font>
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
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" size="30" onKeypress="if(event.keyCode==13) {document.getElementById('SearchButton').focus();return false;}">
        <input name="p1" type="submit" id="SearchButton" value="Search" class="altBtn">
        <input name="p12" type="button" id="p12" value="New Account" onClick="window.location='?p=account&p1=New'">
        </font></td>
    </tr>
  </table>
 <?
if (!chkRights2('releasing','madd',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}
if ($p1 == 'Select')
{
	include_once('wexcess.smarta.php');	
}
if ($pin != '' and $pin != null and !in_array($p1,array('addon','Search')))
{
	$q = "select * from account where smartno ='$pin'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr) == '0')
	{
		echo "<script>alert('Account NOT found...')</script>";
		$p1='';
	}
	if (pg_num_rows($qr) > 1)
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
	$q = "select * from account where smartno ='$pin'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$ctr=0;
	$aid = '';
	while ($r = pg_fetch_object($qr))
	{
		if ($r->account_status == 'I')
		{
			$href = "javascript: alert('Account is In-Active');";
			$href1 = "javascript: alert('Account is In-Active');";
		}
		elseif ($account_balance > 0)
		{
			$href = "?p=loan.releasing.new&p1=addon&aid=$r->account_id";
			$href1 = "window.location='?p=loan.releasing.new&p1=addon&aid=$r->account_id'";
		}
		else
		{
			include_once('accountbalance.php');
			
			$aBal = excessBalance($r->account_id);
		
			$advancechange = -1*$aBal['balance'];

			if ($advancechange > 0)
			{
				$href = "?p=loan.releasing.new&p1=addon&aid=$r->account_id";
				$href1 = "window.location='?p=loan.releasing.new&p1=addon&aid=$r->account_id'";
			} else
			{
				$href = "?p=loan.releasing&p1=New&aid=$r->account_id";
				$href1 = "window.location='?p=loan.releasing&p1=New&aid=$r->account_id'";
			}	
		}
		$bgColor = '#FFFFFF';
  ?>
  <tr bgcolor="<?= $bgColor;?>" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?= $bgColor;?>'" onClick="<?=$href1;?>"> 
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="<?=$href;?>"> 
      <?=$r->account;?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);?>
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= number_format($account_balance,2);?>&nbsp;</font></td>
    <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= status($r->account_status);?>
      </font></td>
  </tr>
  <?
  }
  ?>
</table>
<?
	} else
	{
		$r = @pg_fetch_object($qr);
		$account_balance = accountBalance($r->account_id);
		
		if ($ADMIN['branch_id'] > '0' && $r->branch_id != $ADMIN['branch_id'] && $r->branch_id != $ADMIN['branch_id2'] && $r->branch_id != $ADMIN['branch_id3'] && $r->branch_id != $ADMIN['branch_id4'] && $r->branch_id != $ADMIN['branch_id5'] && $r->branch_id != $ADMIN['branch_id6'] && $r->branch_id != $ADMIN['branch_id7'] && $r->branch_id != $ADMIN['branch_id8'] && $r->branch_id != $ADMIN['branch_id9'] && $r->branch_id != $ADMIN['branch_id10'] && $r->branch_id != $ADMIN['branch_id11'] && $r->branch_id != $ADMIN['branch_id12'] && $r->branch_id != $ADMIN['branch_id13'] && $r->branch_id != $ADMIN['branch_id14'] && $r->branch_id != $ADMIN['branch_id15'] && $r->branch_id != $ADMIN['branch_id16'] && $r->branch_id != $ADMIN['branch_id17'] && $r->branch_id != $ADMIN['branch_id18'] && $r->branch_id != $ADMIN['branch_id19'] && $r->branch_id != $ADMIN['branch_id20'])
		{		
			echo "<script>alert('Account NOT on this branch...')</script>";
			$p1='';
		}
		if ($r->account_status == 'I')
		{
			$message = "Account is In-Active";
			$p1='';
		}	
		elseif ($account_balance > 0)
		{
			$p1='addon';
			$aid=$r->account_id;	
			echo "<script>window.location = '?p=loan.releasing.new&p1=addon&aid=$r->account_id&pin=pin'</script>";
		} else
		{
			echo "<script>window.location = '?p=loan.releasing&p1=New&aid=$r->account_id'</script>";
		}
	}
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
//  $bscan = array('1','2','3','5','7','10','11','16','18','19','37');
  $bscan = array();
  $qb = "select * from branch where enable and swipe order by branch_id";
  $qrb = pg_query($qb) or message(pg_errormessage());
  while ($rb = pg_fetch_object($qrb))
  {
  	$bscan[] = $rb->branch_id;
  }
  $q = "select * from account where enable and account ilike '$xSearch%' order by account";
  $qr = pg_query($q) or message(pg_errormessage());
  $ctr=0;
  $aid = '';
  while ($r = pg_fetch_object($qr))
  {
//echo $aid.' = '.$r->account_id.'   ';	
//	if ($ADMIN['branch_id']=='3' or $ADMIN['branch_id']=='18' or $ADMIN['branch_id']=='10' or $ADMIN['branch_id']=='11' or $ADMIN['branch_id']=='16' or $ADMIN['branch_id']=='5' or $ADMIN['branch_id']=='37')
	if (in_array($ADMIN['branch_id'],$bscan) and $ADMIN[admin_id]!=1)
	{
//smartno branch lock
		if ($r->smartno !='')
		{
			$scdat = date('Y-m-d');
			$smartno=$r->smartno;
			$q="select * from schedule 
					where 
						smartno = '$smartno' and date='$scdat' and branch_id = '".$ADMIN['branch_id']."' and
						active!='9' and status!='Finished'";
			$qs = pg_query($q) or message(pg_errormessage());
			$rs = pg_fetch_object($qs);
//if ($r->account_id=='274')			
//	echo $q.'  '.$rs->smartno.' != '.$smartno;
			if ($rs->smartno != $smartno) continue; 		
		} 
		else
		{
			if ($ADMIN['branch_id'] > '0' && $r->branch_id != $ADMIN['branch_id'] && $r->branch_id != $ADMIN['branch_id2'] && $r->branch_id != $ADMIN['branch_id3'] && $r->branch_id != $ADMIN['branch_id4'] && $r->branch_id != $ADMIN['branch_id5'] && $r->branch_id != $ADMIN['branch_id6'] && $r->branch_id != $ADMIN['branch_id7'] && $r->branch_id != $ADMIN['branch_id8'] && $r->branch_id != $ADMIN['branch_id9'] && $r->branch_id != $ADMIN['branch_id10'] && $r->branch_id != $ADMIN['branch_id11'] && $r->branch_id != $ADMIN['branch_id12'] && $r->branch_id != $ADMIN['branch_id13'] && $r->branch_id != $ADMIN['branch_id14'] && $r->branch_id != $ADMIN['branch_id15'] && $r->branch_id != $ADMIN['branch_id16'] && $r->branch_id != $ADMIN['branch_id17'] && $r->branch_id != $ADMIN['branch_id18'] && $r->branch_id != $ADMIN['branch_id19'] && $r->branch_id != $ADMIN['branch_id20']) continue;
		}
	}
//echo $r->branch_id.'  ';		
  	$ctr++;
	$account_balance = accountBalance($r->account_id);
	
	if ($ADMIN['branch_id'] > '0' && $r->branch_id != $ADMIN['branch_id'] && $r->branch_id != $ADMIN['branch_id2'] && $r->branch_id != $ADMIN['branch_id3'] && $r->branch_id != $ADMIN['branch_id4'] && $r->branch_id != $ADMIN['branch_id5'] && $r->branch_id != $ADMIN['branch_id6'] && $r->branch_id != $ADMIN['branch_id7'] && $r->branch_id != $ADMIN['branch_id8'] && $r->branch_id != $ADMIN['branch_id9'] && $r->branch_id != $ADMIN['branch_id10'] && $r->branch_id != $ADMIN['branch_id11'] && $r->branch_id != $ADMIN['branch_id12'] && $r->branch_id != $ADMIN['branch_id13'] && $r->branch_id != $ADMIN['branch_id14'] && $r->branch_id != $ADMIN['branch_id15'] && $r->branch_id != $ADMIN['branch_id16'] && $r->branch_id != $ADMIN['branch_id17'] && $r->branch_id != $ADMIN['branch_id18'] && $r->branch_id != $ADMIN['branch_id19'] && $r->branch_id != $ADMIN['branch_id20'] and $ADMIN['branch_id'] !=3)
	{
			$href = "javascript: alert('Account NOT on this branch...');";
//			$href1= "javascript: alert('Account NOT on this branch...');";
			$href1='';
			$bgColor = '#DFDFDF';
		
	}
	else
	{
		if ($r->account_status == 'I')
		{
			$href = "javascript: alert('Account is In-Active');";
			$href1 = "javascript: alert('Account is In-Active');";
		}
/*		elseif ($r->branch_id == 3 and $r->smartno != '' and $pin=='')
		{
			$href = "?p=loan.releasing.new&p1=Select";
			$href1 = "window.location='?p=loan.releasing.new&p1=Select'";
		}*/
		elseif ($account_balance > 0)
		{
			$href = "?p=loan.releasing.new&p1=addon&aid=$r->account_id";
			$href1 = "window.location='?p=loan.releasing.new&p1=addon&aid=$r->account_id'";
		}
		else
		{
			include_once('accountbalance.php');
			
			$aBal = excessBalance($r->account_id);
		
			$advancechange = -1*$aBal['balance'];

			if ($advancechange > 0)
			{
				$href = "?p=loan.releasing.new&p1=addon&aid=$r->account_id";
				$href1 = "window.location='?p=loan.releasing.new&p1=addon&aid=$r->account_id'";
			} else
			{
				$href = "?p=loan.releasing&p1=New&aid=$r->account_id";
				$href1 = "window.location='?p=loan.releasing&p1=New&aid=$r->account_id'";
			}	
		}
		$bgColor = '#FFFFFF';
	}
  ?>
  <tr bgcolor="<?= $bgColor;?>" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?= $bgColor;?>'" onClick="<?=$href1;?>"> 
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="<?=$href;?>"> 
      <?=$r->account;?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);?>
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= number_format($account_balance,2);?>&nbsp;</font></td>
    <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
</form>
<div align="center">
<form action="" method="post" name="f2" id="f2">
    <strong><font size="5"> <em> 
    <?=lookUpTableReturnValue('x','account','account_id','account',$aid);?>
    </em></font> <em><font color="#990000" size="5">has existing account balance 
    </font></em></strong><font color="#990000" size="5"> 
    <hr width="75%">
    <table width="70%" border="0" cellspacing="1" cellpadding="3">
      <tr bgcolor="#CCCCCC"> 
        <td width="12%">&nbsp;</td>
        <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Released</font></strong></td>
        <td><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Loan 
          Type </font></strong></td>
        <td><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Principal</font></strong></td>
        <td><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Ammort</font></strong></td>
        <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
      </tr>
      <?
	$ctr=0;
	$total_balance = 0;

	while ($r = @pg_fetch_object($qr))
	{

		recalculate($r->releasing_id,'noneform');
		$q  = "select balance from releasing where releasing_id = '$r->releasing_id' and status!='C' order by date desc";
		$qqr = @pg_query($q) or message(pg_errormessage().$q);
		$rr = @pg_fetch_object($qqr);
		
		$total_balance += $rr->balance;
	  	$total_ammort += $r->ammort;
		$total_principal += $r->principal;
		$ctr++;
	?>
      <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgColor='#FFFFFF'> 
        <td align="right" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= $ctr;?>
          . 
          <input name="restructure[]" type="checkbox" value="<?= $r->releasing_id;?>" checked>
          </font></td>
        <td width="16%" align="center"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= ymd2mdy($r->date);?>
          </font></td>
        <td width="21%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id);?>
          </font></td>
        <td width="15%" align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($r->principal,2);?>
          </font></td>
        <td width="14%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($r->ammort,2);?>
          </font></td>
        <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input name="balance" type="text" id="balance" style="text-align:right; background:none; border:none" value ="<?= number_format($rr->balance,2);?>" size="15" readOnly>
          </font></td>
      </tr>
      <?
  }
  
	include_once('accountbalance.php');
	
	$aBal = excessBalance($aid);
if ($ADMIN[admin_id]==1) print_r($aBal);
	$advancechange = -1*$aBal['balance'];

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
        <td colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advanced 
          Change</font></td>
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <input name="advancechange" type="text" id="advancechange" style="text-align:right; background:none; border:none" value ="<?= number_format($advancechange,2);?>" size="15" readOnly>
          </font></td>
      </tr>
      <?
 }
 ?>
      <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgColor='#FFFFFF'> 
        <td colspan="3" align="right" nowrap>Total</td>
        <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <?= number_format($total_principal,2);?>
          </font></td>
        <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
          <?= number_format($total_ammort,2);?>
          </font></td>
        <td align="right"> 
          <?= number_format($total_balance,2);?>
        </td>
      </tr>
	  
    </table>
    </font><br>
  <br>
    <input name="Input" type="button" value="Restructure All Checked"  onClick="document.getElementById('f2').action='?p=loan.releasing&p1=RenewChecked&aid=<?=$aid;?>';document.getElementById('f2').submit()">
    <input name="Input" type="button" value="Add Another Account"  onClick="window.location='?p=loan.releasing&p1=New&aid=<?=$aid;?>&add=1'">
  </form>
  <?
}
?>
</div>
<?
if ($p1 == 'Select' )
{?>
	<script>document.getElementById('pin').focus()</script>
<?
}
else
{
	message($message);
?>
	<script>document.getElementById('xSearch').focus()</script>
<?
}?>