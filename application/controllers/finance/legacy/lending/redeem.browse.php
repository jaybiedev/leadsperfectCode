<form name="form1" method="post" action="">
  <table width="91%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td  colspan="9" background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/team_wksp.gif" width="16" height="17"> 
        </strong> <font color="#FFFFCC">Browse Redeemed/Gawad or Transferred Accounts</font></font></td>
    </tr>
    <tr> 
      <td colspan="9" >Search 
        <input name="search" type="text" id="search" value="<?= $search;?>"> <?= lookUpAssoc('searchby',array('Account'=>'account','Reference'=>'payment_header_id', 'Date'=>'date'),$searchby);?>
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
          <option value='99'>All Branches</option>
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
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        Dates: From <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
		<img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
		To 
		<input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
		<img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
 	    </font>		
		<input name="p1" type="submit" id="p1" value="Go"> 
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=redeem&p1=New'"> 
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
    <tr bgcolor="#DADADA"> 
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=account&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Account 
        Name</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=payment_header_id&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Reference</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=mcheck&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Type</a></font></strong></td>
       <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">GROUP</font></td>
     <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=date&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Date</a></font></strong></td>
      <td align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=amount&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Amount</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem.browse&p1=Go&sortby=status&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Status</a></font></strong></td>
      <td>&nbsp;</td>
    </tr>
    <?
  	$q = "select * 
				from 
					payment_header,
					payment_detail,
					account
				where
					payment_header.payment_header_id = payment_detail.payment_header_id and
					payment_header.mcheck in ('G','T') and 
					account.account_id = payment_detail.account_id  ";
	if ($ADMIN['branch_id'] > '0')
	{
		$q .= " and (account.branch_id ='".$ADMIN['branch_id']."'";
		if ($ADMIN['branch_id2'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id2']."'";
		if ($ADMIN['branch_id3'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id3']."'";
		if ($ADMIN['branch_id4'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id4']."'";
		if ($ADMIN['branch_id5'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id5']."'";
		if ($ADMIN['branch_id6'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id6']."'";
		if ($ADMIN['branch_id7'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id7']."'";
		if ($ADMIN['branch_id8'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id8']."'";
		if ($ADMIN['branch_id9'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id9']."'";
		if ($ADMIN['branch_id10'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id10']."'";
		if ($ADMIN['branch_id11'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id11']."'";
		if ($ADMIN['branch_id12'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id12']."'";
		if ($ADMIN['branch_id13'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id13']."'";
		if ($ADMIN['branch_id14'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id14']."'";
		if ($ADMIN['branch_id15'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id15']."'";
		if ($ADMIN['branch_id16'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id16']."'";
		if ($ADMIN['branch_id17'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id17']."'";
		if ($ADMIN['branch_id18'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id18']."'";
		if ($ADMIN['branch_id19'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id19']."'";
		if ($ADMIN['branch_id20'] > '0') $q .= " or account.branch_id ='".$ADMIN['branch_id20']."'";
		$q .= ") ";
	
	}
	$fdate = mdy2ymd($from_date);
	$edate = mdy2ymd($to_date);
	if ($from_date !='')
	{
		if ($to_date == '') $edate=date("Y-m-d");
		$q .= " and (date >= '$fdate' and date <= '$edate') ";
//		$from_date = $fdate;
//		$to_date = $edate;
	}
	if ($branch_id > 0 and $branch_id != 99) $q .= " and account.branch_id = '$branch_id' ";
	if ($searchby == '') $searchby='account';
	if ($search != '')
	{
		$q .= " and $searchby ilike '$search%' ";
		$q .= " order by date, account desc  ";
	}
	else
	{
		$q .= " order by date desc ";
	}

	$qr = @pg_query($q) or message(pg_errormessage().$q);
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
		
		$transferto='';
		$agid = lookUpTableReturnValue('x','account','account_id','account_group_id',$r->account_id);
		if ($r->mcheck != 'G')
		{
			$aid = $r->account_id;
			$qt="select account_group_id from payment_header where payment_header_id = '$r->payment_header_id'";
			$qrt = pg_query($qt) or message("Error querying account data...".pg_error().$qt);
			$rt = pg_fetch_object($qrt);
			if ($rg->branch_id == $rt->account_group_id) $branch_id=$r->branch_id;
			else $branch_id = $rt->account_group_id;
			$transferto = 'From '.lookUpTableReturnValue('x','branch','branch_id','branch_code',$r->branch_id);
			$transferto .= ' To '.lookUpTableReturnValue('x','branch','branch_id','branch_code',$branch_id);
		}
  ?>
    <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='<?=$bgColor;?>'" bgcolor="<?=$bgColor;?>" onClick="window.location='?p=redeem&p1=Load&id=<?= $r->account_id;?>'"> 
      <td width="3%" align="right" nowrap  bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $ctr;?>
        .</font></td>
      <td width="24%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=redeem&p1=Load&id=<?= $r->payment_header_id;?>"> 
        <?= $r->account;?>
        </a> </font></td>
      <td width="8%" align="center" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->payment_header_id;?>
        </font></td>
      <td width="14%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= ($r->mcheck=='G' ? 'Redeem/Gawad' :$transferto);?>
        </font></td>
      <td width="14%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$agid);?>
      </font></td>
      <td width="7%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem&id=<?= $r->payment_header_id;?>"> 
        <?= ymd2mdy($r->date);?>
        </a></font></td>
      <td width="11%" align="right" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($r->withdrawn,2);?>
        </font> </td>
      <td width="8%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=redeem&id=<?= $r->payment_header_id;?>"> 
        <?= status($r->status);?>
        </a></font></td>
      <td width="11%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id);?>
        </font></td>
    </tr>
    <?
  }
  ?>
    <tr> 
      <td colspan="8" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=redeem&p1=New'"> 
        <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
    </tr>
  </table>

  <div align="center"> <font size="2">Page 
    <?=intval(($start+15)/15) ." of ". intval($total_rows/15+1)." Displays  ".($start+15  > $total_rows ? $total_rows : $start+15)." of ".$total_rows;?>
    Records <a href="?p=redeem.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
    <a href="?p=redeem.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>"> 
    Previous</a> | <a href="?p=redeem.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>">Next</a> 
    <a href="?p=redeem.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
    </font></div>
</form>
