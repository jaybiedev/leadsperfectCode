<?
$this->View->setPageTitle("Loan Releases");


if (!chkRights2('releasing','mview',$ADMIN['admin_id']))
{
	message("You have no permission to View Loan Releasing...");
	exit;
}

if ($date == '') 
{
	$date=date('m/d/Y');
}
if ($aLoan != '' && $aLoan['account_id']!='' && !in_array($p1, array('Go','Next','Previous','Sort','Browse')))
{
	legacy_redirect("?p=loan.releasing");
}
if ($branch_id == 0)
{
	if ($ADMIN['branch_id'] == '0') $branch_id = 3;
	else $branch_id = $ADMIN['branch_id'];
}

?>
<form action="" method="post" name="f1" id="f1" class="form-horizontal">
<div class="well">
    <div class="col-lg-3 pad-left-0">
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" class="form-control" placeholder="Search">
    </div>
    <div class="col-lg-2">
        <?= lookUpAssoc('searchby',array('Account'=>'account','Account No'=>'account_code','Reference'=>'releasing_id','Date'=>'date','Amount'=>'amount'), $searchby, null, "form-control");?>
    </div>
    <div class="col-lg-2">
    <select name = "branch_id" class="form-control">
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
          <option value=''>All Branches</option>
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
    </div>
    <div class="col-lg-3">
        <input class="form-control" name="date" type="date" id="date" value="<?= $date;?>">
        <!--<img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> -->
    </div>
    <div>
        <input name="p1" class="btn btn-primary" type="submit" id="p1" value="Go">
        <input type="button" class="btn btn-primary" value="Add New" onClick="window.location='?p=loan.releasing.new'">
    </div>
</div>

<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="table table-responsive table-striped table-hover">
    <thead>
  <tr> 
    <td align="center">#</td>
    <td nowrap> <a href="?p=loan.releasing.browse&p1=Go&sortby=releasing_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Reference 
      </a></td>
    <td nowrap> 
	<a href="?p=loan.releasing.browse&p1=Go&sortby=date&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Date</a></td>
    <td width="24%" nowrap> 
	<a href="?p=loan.releasing.browse&p1=Go&sortby=loan_type_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Type</a></td>
    <td nowrap> <a href="?p=loan.releasing.browse&p1=Go&sortby=account&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Account</a></td>
    <td width="8%" nowrap> 
      <a href="?p=loan.releasing.browse&p1=Go&sortby=status&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Status</a></td>
    <td width="7%" nowrap><a href="?p=loan.releasing.browse&p1=Go&sortby=principal&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Principal</a></td>
    <td width="9%" nowrap><a href="?p=loan.releasing.browse&p1=Go&sortby=admin_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Encoder</a></td>
    <td width="6%" nowrap>&nbsp;</td>
  </tr>
    </thead>
  <?
$q = "select 
			releasing.releasing_id,
			releasing.principal,
			releasing.date,
			releasing.account_id,
			releasing.status,
			releasing.loan_type_id,
			releasing.admin_id,
			account.account,
			account.account_status,
			account.branch_id
		from 
			releasing,
			account
		where
			account.account_id=releasing.account_id ";

if ($ADMIN['branch_id'] > '0')
{
//	$q .= " and account.branch_id = '".$ADMIN['branch_id']."'";
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
	$q .= ") ";

}
if ($date == '') 
{
	$date=date('m/d/Y');
}
if ($branch_id > 0)
    $q .= " and account.branch_id = '$branch_id' ";

if ($xSearch != '')
{
	$q .= " and $searchby ilike '$xSearch%' ";
}
else
{
	$mdate = convertDate($_REQUEST['date']);
	$q .= " and date = '$mdate' ";
}
if ($sortby == '')
{
	$sortby = 'releasing_id desc';
}
$q .= " order by $sortby ";

		
if ($p1 == 'Go' or $p1 == '' or $start=='')
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

$qr = @pg_query($q);
$total_rows = @pg_num_rows($qr);

$q .= " offset $start limit 15 ";

$qr = pg_query($q) or message("Error querying Loan Releasing data...".pg_errormessage().$q);

if (pg_num_rows($qr) == 0)
{
	$start -= 15;
	if ($start<0) $start=0;
	if ($p1== 'Go') 
	{
	 	message1("Loan Releasing [NOT] found...");
	}	
	else
	{
	 	message1("End of File...");
	}
}


$ctr=0;
while ($r = pg_fetch_object($qr))
{
	if ($r->account_status=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;

	if ($ADMIN['branch_id'] > '0' && $r->branch_id != $ADMIN['branch_id'] && $r->branch_id != $ADMIN['branch_id2'] && $r->branch_id != $ADMIN['branch_id3'] && $r->branch_id != $ADMIN['branch_id4'] && $r->branch_id != $ADMIN['branch_id5'] && $r->branch_id != $ADMIN['branch_id6'] && $r->branch_id != $ADMIN['branch_id7'] && $r->branch_id != $ADMIN['branch_id8'] && $r->branch_id != $ADMIN['branch_id9'] && $r->branch_id != $ADMIN['branch_id10'] && $r->branch_id != $ADMIN['branch_id11'] && $r->branch_id != $ADMIN['branch_id12'] && $r->branch_id != $ADMIN['branch_id13'] && $r->branch_id != $ADMIN['branch_id14'] && $r->branch_id != $ADMIN['branch_id15'] && $r->branch_id != $ADMIN['branch_id16'] && $r->branch_id != $ADMIN['branch_id17'] && $r->branch_id != $ADMIN['branch_id18'] && $r->branch_id != $ADMIN['branch_id19'] && $r->branch_id != $ADMIN['branch_id20' ])
	{
			$href = "javascript: alert('Account NOT on this branch...');";
//			$href1= "javascript: alert('Account NOT on this branch...');";
			$href1='';
			$bgColor = '#DFDFDF';
		
	}
	else
	{
			$href = "?p=loan.releasing&p1=Load&id=$r->releasing_id";
			$bgColor = '#FFFFFF';
	}
	$ctr++;
  ?>
  <tr>
    <td width="4%" align="right" nowrap> 
      <?= $ctr;?>
      .</td>
    <td width="8%" nowrap" bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"> 
	<a href="<?=$href;?>"> 
      <?= str_pad($r->releasing_id,8,'0',STR_PAD_LEFT);?>
      </a> </td>
    <td width="6%" nowrap"> 
      <?= ymd2mdy($r->date);?>
      </td>
    <td  nowrap"> 
      <?= lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id);?>
      </td>
    <td width="28%""> 
	<a href="<?=$href;?>"> 
      <?= $r->account;?></a>
      </td>
    <td nowrap bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"> 
      <?= status($r->status);?>
      </td>
    <td align="right" nowrap>
      <?= number_format($r->principal);?>
      </td>
    <td nowrap>
      <?= lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id);?>
    </td>
    <td nowrap>
      <?= lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id);?>
    </td>
  </tr>
  <?
  }
  ?>

</table>
<div class="col-lg-12">
    <button class="btn btn-primary" onClick="window.location='?p=loan.releasing.new'">Add New</button>
</div>
<div align="center"> 
Page <?=intval(($start+15)/15) ." of ". intval($total_rows/15+1)." Displays  ".($start+15  > $total_rows ? $total_rows : $start+15)." of ".$total_rows;?> Records <br>
<a href="javascript: f1.action='?p=loan.releasing.browse&p1=Previous&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>';f1.submit()"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="javascript: f1.action='?p=loan.releasing.browse&p1=Previous&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>';f1.submit()"> 
  Previous</a> | <a href="javascript: f1.action='?p=loan.releasing.browse&p1=Next&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>';f1.submit()">Next</a> 
  <a href="javascript: f1.action='?p=loan.releasing.browse&p1=Next&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>';f1.submit()"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
</form>

