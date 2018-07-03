<?
/*if (!chkRights2('releasing','mview',$ADMIN['admin_id']))
{
	message("You have no permission to View Loan Releasing...");
	exit;
}*/

if ($date == '') 
{
	$date=date('m/d/Y');
}
if ($p1=='Finish')
{
	$q = "select * from schedule where sched_id='$id'";
	$qs = pg_query($q) or message("Error Updating Queueing data...".pg_errormessage().$q);
	$rs = @pg_fetch_object($qs);
	if ($rs->status=='Queued')
	{
		$q = "update schedule set active='5', status='Called', admin_id ='".$ADMIN['admin_id']."' where sched_id='$id'";
		$qr = pg_query($q) or message("Error Updating Queueing data...".pg_errormessage().$q);
	} else	
	{
		$q = "update schedule set active='9', status='Finished', admin_id ='".$ADMIN['admin_id']."' where sched_id='$id'";
		$qr = pg_query($q) or message("Error Updating Queueing data...".pg_errormessage().$q);
	}	
}
?>
<form action="" method="post" name="f1" id="f1" >
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td><input type="button" name="Submit23222" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCCC" background="../graphics/table_horizontal.PNG"> 
      <td height="20" colspan="9"  background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        <img src="../graphics/storage.gif" width="16" height="17"> <font color="#DADADA">Browse 
        Accounts Scheduled for Transaction </font></strong></font></td>
  </tr>
  <tr> 
    <td align="center"><font size="2" face="Geneva, Arial, Helvetica, san-serif">#</font></td>
    <td nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif">Number</font></td>
    <td nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif"> 
	Time</font></td>
    <td width="24%" nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif">Account</font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Branch 
      </a></font></td>
    <td width="8%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      Status</font></td>
    <td width="7%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></td>
    <td width="9%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Encoder</font></td>
    <td width="6%" nowrap>&nbsp;</td>
  </tr>
  <?
$q = "select sc.branch_id as local_id,
			 sc.timeref,
			 sc.schednum,
			 sc.smartno,
			 sc.status,
			 sc.sched_id,
			 sc.admin_id,
			 ac.branch_id,
			 ac.account,
			 ac.account_id
		from 
			schedule as sc,
			account as ac
		where
			ac.smartno=sc.smartno";
if ($date == '') 
{
	$date=date('m/d/Y');
}
$mdate=mdy2ymd($date);
$q .= " and date = '$mdate' ";
$q .= " and sc.branch_id ='".$ADMIN['branch_id']."'";

/*if ($ADMIN['branch_id'] > '0')
{
	$q .= " and (location_id ='".$ADMIN['branch_id']."'";
	if ($ADMIN['branch_id2'] > '0') $q .= " or location_id ='".$ADMIN['branch_id2']."'";
	if ($ADMIN['branch_id3'] > '0') $q .= " or location_id ='".$ADMIN['branch_id3']."'";
	if ($ADMIN['branch_id4'] > '0') $q .= " or location_id ='".$ADMIN['branch_id4']."'";
	if ($ADMIN['branch_id5'] > '0') $q .= " or location_id ='".$ADMIN['branch_id5']."'";
	$q .= ") ";
}*/
$q .= " order by active, schednum ";

$qr = pg_query($q) or message("Error querying Queueing data...".pg_errormessage().$q);
	
$ctr=0;
while ($r = pg_fetch_object($qr))
{
/*	if ($ADMIN['branch_id'] > '0' && $r->location_id != $ADMIN['branch_id'] && $r->location_id != $ADMIN['branch_id2'] && $r->location_id != $ADMIN['branch_id3'] && $r->location_id != $ADMIN['branch_id4'] && $r->location_id != $ADMIN['branch_id5'])
	{
			$href = "javascript: alert('Account NOT on this branch...');";
			$href1='';
			$bgColor = '#DFDFDF';
		
	}
	else
	{*/
			$href = "?p=waiting.browse&p1=Finish&id=$r->sched_id";
			$bgColor = '#FFFFFF';
//	}
	$ctr++;
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='<?= $bgColor;?>'" bgcolor="<?= $bgColor;?>"> 
    <td width="4%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= '';?>
      </font></td>
    <td width="8%" nowrap" bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="<?=$href;?>"> 
      <?= $r->schednum;?>
      </a> </font></td>
    <td width="6%" nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->timeref;?>
      </font></td>
    <td  nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="<?=$href;?>"> 
      <?= $r->account;?></a>
      </font></td>
    <td width="28%""><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id);?>
      </font></td>
    <td nowrap bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->status;?>
      </font></td>
    <td align="right" nowrap>&nbsp;</td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= (lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id)=='No Record'?'':lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id));?>
    </font></td>
    <td nowrap>&nbsp;</td>
  </tr>
  <?
  }
  ?>
</table>

<div align="center"></div>
</form>

