<?
if ($p1 == 'Clear Checked' && count($mark)==0)
{
	message1(" No Items Selected...");
}
elseif ($p1 == 'Clear Checked')
{
	$amark = implode("','",$mark);
	$udate = date('Y-m-d');
	
	$q = "update bankrecon set flag = 'R', udate='$udate' where bankrecon_id in ('$amark')";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	if ($qr)
	{	
		message1(" Cleared ".@pg_affected_rows($qr)." check(s)..");
	}
}
?>
<form name="form1" method="post" action="">
  <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
		<?= lookUpAssoc('searchby',array('Check No'=>'mcheck','Reference'=>'reference','Particulars'=>'descr','Bank'=>'bank','CheckDate'=>'checkdate'),$searchby);?>
        <input name="p1" type="submit" id="p1" value="Go">
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='">
        <hr color="#CC0000"></td>
    </tr>
  </table>
  <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td height="20" colspan="8"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        <img src="../graphics/team_wksp.gif" width="16" height="17"> Browse UnReleased 
        Check </strong></font></td>
    </tr>
    <tr bgcolor="#EFEFEF"> 
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=reference&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Reference</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=mcheck&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Check 
        No </a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=checkdate&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">CheckDate</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=bank&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Bank</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=descr&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Particulars</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=date&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Issued</a></font></strong></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=ucheck&p1=Go&sortby=credit&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Amount</a></font></strong></td>
    </tr>
    <?


  	$q = "select * 
						from 
							bankrecon,
							bank
						where 
							bank.bank_id = bankrecon.bank_id and 
							bankrecon.enable and 
							bankrecon.type='W' and 
							bankrecon.flag!='R' ";

//	if ($ADMIN['branch_id'] > '0')
//	{
//		$q .= " and bankrecon.branch_id = '".$ADMIN['branch_id']."'";
//	}
	if ($ADMIN['branch_id'] > '0')
	{
		$q .= " and (bankrecon.branch_id ='".$ADMIN['branch_id']."'";
		if ($ADMIN['branch_id2'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id2']."'";
		if ($ADMIN['branch_id3'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id3']."'";
		if ($ADMIN['branch_id4'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id4']."'";
		if ($ADMIN['branch_id5'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id5']."'";
		if ($ADMIN['branch_id6'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id6']."'";
		if ($ADMIN['branch_id7'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id7']."'";
		if ($ADMIN['branch_id8'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id8']."'";
		if ($ADMIN['branch_id9'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id9']."'";
		if ($ADMIN['branch_id10'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id10']."'";
		if ($ADMIN['branch_id11'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id11']."'";
		if ($ADMIN['branch_id12'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id12']."'";
		if ($ADMIN['branch_id13'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id13']."'";
		if ($ADMIN['branch_id14'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id14']."'";
		if ($ADMIN['branch_id15'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id15']."'";
		if ($ADMIN['branch_id16'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id16']."'";
		if ($ADMIN['branch_id17'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id17']."'";
		if ($ADMIN['branch_id18'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id18']."'";
		if ($ADMIN['branch_id19'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id19']."'";
		if ($ADMIN['branch_id20'] > '0') $q .= " or bankrecon.branch_id ='".$ADMIN['branch_id20']."'";
		$q .= ") ";
	}
	
	if ($search != '')
	{
		$q .= " and $searchby ilike '%$search%' ";
	}
	$q .= " order by checkdate ";
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
	$qr = @pg_query($q) or message("Error querying Bank Reconciliation data...".pg_errormessage().$q);

	if (@pg_num_rows($qr) == 0 && $p1!= '') message("Bank Reconciliation  data [NOT] found...");
	$ctr=0;
	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;
  ?>
    <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF"> 
      <td width="6%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $ctr;?>
        .
        <input name="mark[]" type="checkbox" id="mark[]" value="<?= $r->bankrecon_id;?>">
        </font></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=bankrecon&id=<?= $r->bankrecon_id;?>"> 
        <?= $r->reference;?>
        </a> </font></td>
      <td width="9%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=bankrecon&p1=LoadCheck&id=<?= $r->bankrecon_id;?>"> 
        <?= $r->mcheck;?>
        </a> </font></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=bankrecon&p1=LoadCheck&id=<?= $r->bankrecon_id;?>"> 
        <?= ($r->checkdate);?>
        </a></font></td>
      <td width="18%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankrecon&p1=LoadCheck&id=<?= $r->bankrecon_id;?>"> 
        <?= $r->bank .' '.($ADMIN['branch_id'] == '0' ? '('.lookUpTableReturnValue('x','branch','branch','branch_id',$r->branch_id).')' : '');?>
        </a></font></td>
      <td width="22%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=bankrecon&p1=LoadCheck&id=<?= $r->bankrecon_id;?>"> 
        <?= $r->descr;?>
        </a></font></td>
      <td width="10%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="?p=bankrecon&p1=LoadCheck&id=<?= $r->bankrecon_id;?>"> 
        <?= ymd2mdy($r->date);?>
        </a></font></td>
      <td width="13%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=number_format($r->credit,2 );?>
        </font></td>
    </tr>
    <?
		$total_credit += $r->credit;
  }
  ?>
    <tr> 
      <td colspan="6" align="center" bgcolor="#FFFFFF"><font size="2">TOTAL</font> 
      </td>
      <td bgcolor="#FFFFFF">&nbsp;</td>
      <td align="right" bgcolor="#FFFFFF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <?=number_format($total_credit,2 );?>
        </font></td>
    </tr>
    <tr> 
      <td colspan="8" bgcolor="#FFFFFF"><input name="p1" type="submit" id="p1"  value="Clear Checked">
        <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
    </tr>
  </table>

<div align="center"> <a href="?p=ucheck&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=ucheck&p1=Previous&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"> 
  Previous</a> | <a href="?p=ucheck&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>">Next</a> 
  <a href="?p=ucheck&p1=Next&start=<?=$start;?>&search=<?=$search;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
</form>
