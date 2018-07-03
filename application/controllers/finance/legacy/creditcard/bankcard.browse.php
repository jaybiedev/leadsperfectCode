<form name="form1" method="post" action="">
  <table width="70%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
		<?=lookUpAssoc('searchby',array('Card No.'=>'bankcard','Name of Holder'=>'name','Bank Card'=>'bankcard_type'),$searchby);?>
        <input name="p1" type="submit" id="p1" value="Go" accesskey="G">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=bankcard&p1=New'" accesskey="N">
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='" accesskey="C">
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>
<table width="70%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCCC"> 
    <td height="20" colspan="5"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
      <img src="../graphics/team_wksp.gif" width="16" height="17"> Browse bankcard 
      Information</strong></font></td>
  </tr>
  <?
  	$q = "select bankcard, name, bankcard_type , bankcard.bankcard_type_id
				from 
					bankcard, bankcard_type 
				where 
					bankcard.bankcard_type_id=bankcard_type.bankcard_type_id ";
	if ($search != '')
	{
		if ($searchby == 'All')
		{
			$q .= " and (bankcard ilike '%$search%' OR name ilike '%$search%' OR bankcard_type ilike '%$search%')";
		}
		else
		{
			$q .= " and $searchby ilike '%$search%' ";
		}	
	}
	$q .= " order by name ";
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

	$qr = pg_query($q) or message("Error querying bankcard data...".pg_error().$q);

	if (pg_numrows($qr) == 0 && $p1!= '') message("bankcard data [NOT] found...");
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF"> 
    <td width="6%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td width="20%""><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=bankcard&p1=Load&id=<?= $r->bankcard_id;?>"> 
      <?= $r->bankcard;?>
      </a> </font></td>
    <td width="20%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=bankcard&p1=Load&id=<?= $r->bankcard_id;?>"> 
	<?= $r->bankcard_type;?></a></font></td>
    <td width="40%""><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankcard&p1=Load&id=<?= $r->bankcard_id;?>"> 
      <?= $r->name;?>
      </a></font></td>
    <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=($r->enable=='t' ? 'Enabled' : 'Disabled' );?>
      </font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="5" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=bankcard&p1=New'"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
  </tr>
</table>

<div align="center"> <a href="?p=bankcard.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=bankcard.browse&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"> 
  Previous</a> | <a href="?p=bankcard.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Next</a> 
  <a href="?p=bankcard.browse&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
