<form name="form1" method="post" action="">
  <table width="70%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
        <input name="p1" type="submit" id="p1" value="Go">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=account&p1=New'">
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='">
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>
<table width="70%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCCC"> 
    <td height="20" colspan="5"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
      <img src="../graphics/team_wksp.gif" width="16" height="17"> Browse Account 
      Information</strong></font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=account&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Account 
      Name</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=type&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Type</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=status&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Status</a></font></strong></td>
    <td>&nbsp;</td>
  </tr>
  <?
  	$q = "select * from account ";
	if ($search != '')
	{
		$q .= " where $searchby like '%$search%' ";
	}
	$q .= " order by account ";
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
	
//	$q .= " limit $start,15 ";

	$qr = pg_query($q) or message("Error querying account data...".pg_error().$q);

	if (pg_num_rows($qr) == 0 && $p1!= '') message("account data [NOT] found...");
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF"> 
    <td width="6%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td width="57%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=account&id=<?= $r->account_id;?>"> 
      <?= $r->account;?>
      </a> </font></td>
    <td width="12%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account&id=<?= $r->account_id;?>">
      <?= $r->account_type;?>
      </a></font></td>
    <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account&id=<?= $r->account_id;?>">
      <?= $r->account_status;?>
      </a></font></td>
    <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=($r->enable==true ? 'Enabled' : 'Disabled' );?>
      </font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="5" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=account&p1=New'"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
  </tr>
</table>

<div align="center"> <a href="?p=account.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=account.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"> 
  Previous</a> | <a href="?p=account.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Next</a> 
  <a href="?p=account.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
