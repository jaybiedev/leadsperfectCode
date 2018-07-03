<form name="form1" method="post" action="" style="margin:10px">
  <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
        <?=lookUpAssoc('searchby',array('Name'=>'account','Account No.'=>'account_code','RecordId'=>'account_id',
							'Bank Account#'=>'bank_account'),$searchby);?>
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Show</font> 
        <?= lookUpAssoc('show',array('All'=>'A', 'This Branch Only'=>'B', 'With ID# Only'=>'I'),$show);?>
        <input name="p1" type="submit" id="p1" value="Go">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=account&p1=New'">
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='">
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>
<table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCCC" background="../graphics/table_horizontal.PNG"> 
    <td height="20" colspan="7" background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
      <img src="../graphics/team_wksp.gif" width="16" height="17"> <font color="#CCCCCC">Browse 
      Account Information - <?= myBranch();?></font></strong></font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=account&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Account 
      Name</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account#</font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=account_group_id&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Group</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=account_group_id&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Branch</a></font></strong></td>
    <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=account.browse&p1=Go&sortby=status&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Status</a></font></strong></td>
    <td>&nbsp;</td>
  </tr>
  <?
  	$q = "select * from account where 1=1";
	
	if ($show == 'B' && $ADMIN['branch_id'] >'0')
	{
		$q .= " and branch_id ='".$ADMIN['branch_id']."'";
	}
	elseif ($show == 'I') $q .= " and smartno !=''";
	
	if ($searchby == '')
	    $searchby='account';


	if ($search != '')
	{
		if ($searchby=='bank_account')
			$q .= " and $searchby ilike '%$search%' ";
        else if ($searchby == 'account_id')
            $q .= " and $searchby = " . (int)$search;
		else
			$q .= " and $searchby ilike '$search%' ";
	}
	$q .= " order by account ";
	
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
		if ($r->account_status=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;
	
		$ctr++;
		if ($ADMIN['branch_id'] == '0'  || $ADMIN['branch_id'] =='' || $ADMIN['branch_id'] ==   $r->branch_id || $ADMIN['branch_id2'] ==   $r->branch_id || $ADMIN['branch_id3'] ==   $r->branch_id || $ADMIN['branch_id4'] ==   $r->branch_id || $ADMIN['branch_id5'] ==   $r->branch_id || $ADMIN['branch_id6'] ==   $r->branch_id || $ADMIN['branch_id7'] ==   $r->branch_id || $ADMIN['branch_id8'] ==   $r->branch_id || $ADMIN['branch_id9'] == $r->branch_id || $ADMIN['branch_id10'] ==   $r->branch_id|| $ADMIN['branch_id11'] ==   $r->branch_id|| $ADMIN['branch_id11'] ==   $r->branch_id || $ADMIN['branch_id12'] ==   $r->branch_id || $ADMIN['branch_id13'] ==   $r->branch_id || $ADMIN['branch_id14'] ==   $r->branch_id || $ADMIN['branch_id15'] ==   $r->branch_id || $ADMIN['branch_id16'] ==   $r->branch_id || $ADMIN['branch_id17'] ==   $r->branch_id || $ADMIN['branch_id18'] ==   $r->branch_id || $ADMIN['branch_id19'] ==   $r->branch_id || $ADMIN['branch_id20'] ==   $r->branch_id)
		{
			$href = "<a href=\"?p=account&p1=Load&id=$r->account_id\" >";
			$bgColor = '#FFFFFF';
		}
		else
		{
			$href = "<a href='#'  onClick=\"alert('Account NOT on this branch...')\">";
			$bgColor = '#DFDFDF';
		}
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='<?= $bgColor;?>'" bgcolor="<?= $bgColor;?>" > 
    <td width="6%" align="right"  bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td width="35%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $href;?> 
      <?= $r->account;?>
      </a> </font></td>
    <td width="11%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= $href;?> 
      <?= $r->account_code;?>
      </a></font></td>
    <td width="19%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= $href;?> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);?>
      </a></font></td>
    <td width="14%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= $href;?> 
      <?= lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id);?>
      </a></font></td>
    <td width="5%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= $href;?> 
      <?= $r->account_status;?>
      </a></font></td>
    <td width="10%" bgColor="<?=($r->account_status=='I' ? '#ECDDE1' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=($r->enable=='t' ? 'Enabled' : 'Disabled' );?>
      </font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="7" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=account&p1=New'"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
  </tr>
</table>

<div align="center"> 
Page <?=intval(($start+15)/15) ." of ". intval($total_rows/15+1)." Displays  ".($start+15  > $total_rows ? $total_rows : $start+15)." of ".$total_rows;?> Records <br>
<a href="?p=account.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&show=<?=$show;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=account.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&show=<?=$show;?>"> 
  Previous</a> | <a href="?p=account.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&show=<?=$show;?>">Next</a> 
  <a href="?p=account.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>&show=<?=$show;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
