	<script>
	function vOverride(id, value)
	{
		value = prompt("Grant Override with value ("+value+")", value);
		value = parseFloat(value)
		if (value > 0)
		{
			xajax_grantloanoverride(id, value);
		}
		else
		{
			alert("Override Cancelled");
		}
		return false;
	}
	</script>
    <?
if (!chkRights2('loanoverride','madd',$ADMIN['admin_id']))
{
	message("You have no permission to access Loan Override...");
	exit;
}
	
$q = "select * from override where status in ('S') ";
if ($date == '') 
{
	$date=date('m/d/Y');
}
if ($xSearch != '')
{
	$q .= " and $searchby ilike '$xSearch%' ";
}
else
{
	$mdate=mdy2ymd($date);
	$q .= " and date_request = '$mdate' ";
}
if ($sortby == '')
{
	$sortby = 'override_id ';
}
$q .= " order by $sortby ";

		
if ($p1 == 'Go' or $p1 == '' or $start=='')
{
	$start = 0;
}

$qr = pg_query($q) or message("Error querying Loan Releasing data...".pg_errormessage().$q);

if (@pg_num_rows($qr) == 0)
{
	$start -= 15;
	if ($start<0) $start=0;
	if ($p1== 'Go') 
	{
	 	message("Override Info [NOT] found...");
	}	
}
?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
        <input name="date" type="text" id="date" value="<?= $date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        </font> 
        <input name="p1" type="submit" id="p1" value="Go">
      <input type="button" name="Submit232" value="Refresh" onClick="window.location='?p=override'">
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC" background="../graphics/table0_horizontal.PNG"> 
      <td height="20" colspan="7"><font color="#EFEFEF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        <img src="../graphics/storage.gif" width="16" height="17"> Browse Override 
        Request </strong></font></td>
    </tr>
    <tr> 
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Requested</font></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Module</font></td>
      <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Account</font></td>
      <td width="30%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Remarks</font></td>
      <td width="12%" align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Override</font></td>
      <td width="12%" align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></td>
    </tr>

<?	
$ctr=0;
while ($r = @pg_fetch_object($qr))
{
	$ctr++;
	
	if ($r->value > 0) $value = $r->value;
	else $value=0;
	if ($r->status == 'S')
	{
		$override= "<a href='#'  onClick= \"vOverride('$r->override_id', '$value')\" onmouseover=\"showToolTip(event,'Grant Request for Term='+$value);return false\" onmouseout=\"hideToolTip()\"> Grant</a> " .
				" | "." <a href='#'  onClick= \"xajax_denyloanoverride('$r->override_id')\" onmouseover=\"showToolTip(event,'Deny Request for Term='+$value);return false\" onmouseout=\"hideToolTip()\"> Deny ";
	}
	else
	{
		$override = @lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id_override);
	}
	$remarks = $r->remarks.'; Request '.$r->field.': '.$r->value;
	if ($r->mtable == 'account')
	{
		$qq = "select * from account, account_group 
					where
					 account_group.account_group_id = account.account_group_id and 
					 account_id = '$r->mtable_id'";
		$qqr = @pg_query($qq) or message(pg_errormessage().$qq);
		$rr = @pg_fetch_object($qqr);

		$maccount = ' Group '.$rr->account_group;
		
	}
	elseif ($r->mtable == 'releasing')
	{
		$qq = "select * 
						from account, account_group, releasing 
					where 
						account_group.account_group_id=account.account_group_id and 
						releasing.account_id =account.account_id and 
						releasing_id = '$r->mtable_id'";
		$qqr = @pg_query($qq);
		$rr = @pg_fetch_object($qqr) or message(pg_errormessage().$qq);

		$maccount = ' Group '.$rr->account_group;

	}
	
	$qq = "select sum(balance) as balance, count(*)  as mcount 
				from 
					releasing 
				where 
					status!='C' and 
					account_id ='$rr->account_id'";
	$qqr = @pg_query($qq) or message(pg_errormessage().$qq);
	$rr = @pg_fetch_object($qqr);

	$maccount = ' Previous Loans Made '.$rr->mcount.' ; Current Loan Balance: '.number_format($rr->balance,2);

  ?>

    <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" > 
      <td width="4%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $ctr;?>
        .</font></td>
      <td width="15%" nowrap" bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id_request);?>
        </font></td>
      <td width="9%" nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->module;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  <a href="#" onmouseover="showToolTip(event,'<?= $maccount;?>');return false" onmouseout="hideToolTip()"> 
        <?= $r->account;?>
        </a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	  <a href="#" onmouseover="showToolTip(event,'<?= $remarks;?>');return false" onmouseout="hideToolTip()"> 
        <?= $remarks;?>
        </a> </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        </font></td>
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $override;?>
        </font></td>
      <td align="center" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->status;?>
        </font></td>
    </tr>
    <?
  }
  ?>
    <tr> 
      <td colspan="7" bgcolor="#FFFFFF"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='">
      <input type="button" name="Submit232" value="Refresh" onClick="window.location='?p=override'">
      </td>
    </tr>
  </table>

</form>

