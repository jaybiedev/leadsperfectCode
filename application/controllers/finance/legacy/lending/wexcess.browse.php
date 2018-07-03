<form name="form1" method="post" action="">
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/team_wksp.gif" width="16" height="17"> 
        Browse Excess Amount </strong></font></td>
    </tr>
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>"> <?= lookUpAssoc('searchby',array('Account'=>'account','Reference'=>'wexcess_id', 'Date'=>'date'),$searchby);?>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		<select name = "branch_id">
          <?

				$q = "select * from branch where enable";
				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
					if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
					if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
					if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
					if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
					if ($ADMIN['branch_id11'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id11']."'";
					if ($ADMIN['branch_id12'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id12']."'";
					if ($ADMIN['branch_id13'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id13']."'";
					if ($ADMIN['branch_id14'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id14']."'";
					if ($ADMIN['branch_id15'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id15']."'";
					if ($ADMIN['branch_id16'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id16']."'";
					if ($ADMIN['branch_id17'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id17']."'";
					if ($ADMIN['branch_id18'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id18']."'";
					if ($ADMIN['branch_id19'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id19']."'";
					if ($ADMIN['branch_id20'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id20']."'";
					$q .= ") ";
				} else
				{
					?>
          <option value=''>Select a Branch</option>
          <?
				}
				$q .= " order by branch";
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
		</font>
		<input name="p1" type="submit" id="p1" value="Go"> 
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=excess.withdraw&p1=New'"> 
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#DADADA"> 
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw.browse&p1=Go&sortby=account&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Account 
      Name</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw.browse&p1=Go&sortby=account_group_id&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Date</a></font></strong></td>
    <td align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw.browse&p1=Go&sortby=account_group_id&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Amount</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw.browse&p1=Go&sortby=status&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Status</a></font></strong></td>
    <td>&nbsp;</td>
  </tr>
  <?
    if ($branch_id == 0) $branch_id = 99;

	$acc = 0;
	$bscan = array('1','2','3','7','10','11','18','19');
	$acid = array();
	$scdat = date('Y-m-d');
	$q="select * from schedule 
			where 
				date='$scdat' and branch_id = '".$ADMIN['branch_id']."' and
				active!='9' and status!='Finished'";
	$qs = pg_query($q) or message(pg_errormessage());
	
	while ($rs = pg_fetch_object($qs))
	{
		$acid[] = $rs->account_id;
	}
	$acc = count($acid);
//print_r($acid);
//echo '   '.$q;
  	$q = "select * 
				from 
					account,
					wexcess
				where
					account.account_id = wexcess.account_id  ";
/*	if ($ADMIN['branch_id'] > '0')
	{
//		$q .= " and branch_id ='".$ADMIN['branch_id']."'";
		$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
		if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
		if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
		if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
		if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
		if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
		if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
		if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
		if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
		if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
		if ($acc == 0)
			$q .= ") ";
		else
		{
			$cids = join(',',$acid);
			$q .= " or account.account_id IN ($cids)) ";
		}
	}*/
	if ($searchby == '') $searchby='account';
	if ($branch_id > 0)
	{ 
		if ($acc == 0)
			$q .= " and account.branch_id = '$branch_id' ";
		else
		{
			$cids = join(',',$acid);
			$q .= " and (account.branch_id = '$branch_id' or account.account_id IN ($cids)) ";
		}
	}
	if ($search != '')
	{
		if ($searchby == 'wexcess_id')
			$q .= " and $searchby = '$search' ";
		else
			$q .= " and $searchby ilike '$search%' ";
			
		$q .= " order by date desc  ";
	}
	else
	{
		$q .= " order by date desc ";
	}

	$qr = @pg_query($q);
	$total_rows = @pg_num_rows($qr);

	if ($p1 == 'Go' or $p1 == '')
	{
		$start = 0;
	}
	elseif ($p1 == 'Next')
	{
		$start += 15;
	}
	elseif ($p1 == 'Previous')
	{
		$start -= 15;
	}
	if ($start<0) $start=0;
	
	$q .= " offset $start limit 15 ";

	$qr = pg_query($q) or message("Error querying account data...".pg_error().$q);

	if (pg_num_rows($qr) == 0 && $p1!= '') message("account data [NOT] found...");
	$ctr=0;

	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		if ($r->status == 'C')
		{
			$bgColor ='#FFCCCC';
		}
		else
		{
			$bgColor= '#FFFFFF';
		}
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='<?=$bgColor;?>'" bgcolor="<?=$bgColor;?>" onClick="window.location='?p=excess.withdraw&p1=Load&id=<?= $r->wexcess_id;?>'"> 
    <td width="4%" align="right"  bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td width="41%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=excess.withdraw&p1=Load&id=<?= $r->wexcess_id;?>"> 
      <?= $r->account;?>
      </a> </font></td>
    <td width="9%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw&p1=Load&id=<?= $r->wexcess_id;?>"> 
      <?= ymd2mdy($r->date);?></a></font></td>
    <td width="11%" align="right" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= number_format($r->gross_amount,2);?>
      </font> </td>
    <td width="9%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=excess.withdraw&p1=Load&id=<?= $r->wexcess_id;?>"> 
      <?= status($r->status);?>
      </a></font></td>
    <td width="26%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=adjustSize(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id),20).' - ';?>
      <?=lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id);?>
      </font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="6" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=excess.withdraw&p1=New'"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
  </tr>
</table>

<div align="center"> 
Page <?=intval(($start+15)/15) ." of ". intval($total_rows/15+1)." Displays  ".($start+15  > $total_rows ? $total_rows : $start+15)." of ".$total_rows;?> Records <br>
<a href="?p=wexcess.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&branch_id=<?=$branch_id;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=wexcess.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&branch_id=<?=$branch_id;?>"> 
  Previous</a> | <a href="?p=wexcess.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&branch_id=<?=$branch_id;?>">Next</a> 
  <a href="?p=wexcess.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&branch_id=<?=$branch_id;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
</form>
