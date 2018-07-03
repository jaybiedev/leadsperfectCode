<?
if ($year == '') $year = date('Y');

if ($p1 == 'cancelchecked' && !chkRights2('penalty','mdelete',$ADMIN['admin_id']))
{
		message("You have no permission to Cancel Transaction in this area...");
}
elseif ($p1 == 'unpostchecked' && !chkRights2('penalty','medit',$ADMIN['admin_id']))
{
		message("You have no permission to Unpost/Restore Transaction in this area...");
}
elseif ($p1 == 'restorechecked' && !chkRights2('penalty','medit',$ADMIN['admin_id']))
{
		message("You have no permission to Restore Transaction in this area...");
}
elseif ($p1 == 'cancelchecked')
{
	$admin_id = $ADMIN['admin_id'];
	$arem = implode ("','",$mark);
	$q = "update ledger set status='C', admin_id='$admin_id' where type='P' and remarks  in ('$arem')";
	$qr = @pg_query($q);
	if ($qr)
	{

		$q = "select * from ledger where status='C' and type='P' and remarks  in ('$arem')";
		$qr  =@pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			recalculate($r['releasing_id'],'noneform');
		}

		message("Successfully Cancelled ".pg_affected_rows($qr)." Lines");
	}
	else
	{
		message(pg_errormessage().$q);
	}
}
elseif ($p1 == 'unpostchecked')
{

	$aledger_id = implode ("','",$alid);
	$q = "update ledger set status='C' where type='P' and ledger_id  in ('$aledger_id')";

	$qr = @pg_query($q);
	if ($qr)
	{
		$q = "select * from ledger where status='C' and type='P'  and ledger_id  in ('$aledger_id')";
		$qr  =@pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			recalculate($r['releasing_id'],'noneform');
		}

		message("Successfully Cancelled ".pg_affected_rows($qr)." Line(s)");
	}
	else
	{
		message(pg_errormessage().$q);
	}
}
elseif ($p1 == 'restorechecked')
{

	$aledger_id = implode ("','",$alid);
	$q = "update ledger set status='S' where type='P' and ledger_id  in ('$aledger_id')";

	$qr = @pg_query($q);
	if ($qr)
	{

		$q = "select * from ledger where status='S' and type='P'  and ledger_id  in ('$aledger_id')";
		$qr  =@pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			recalculate($r['releasing_id'],'noneform');
		}

		message("Successfully Restored ".pg_affected_rows($qr)." Line(s)");
	}
	else
	{
		message(pg_errormessage().$q);
	}
}
?>
<form name="f1" id="f1"  method="post" action="" style="margin:10px">
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td height="21" colspan="5" background="../graphics/table_horizontal.PNG"><font color="#CCCCCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Browse 
        Posted Penalties</strong></font> </td>
    </tr>
    <tr> 
      <td colspan="5"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Year</font> 
        <input name="year" type="text" id="year" value="<?= $year;?>" size="4" maxlength="4"> 
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
        <input name="p1" type="submit" id="p1" value="Show"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="button" name="Button2" value="Close" onClick="window.location='?p='";>
        </font></td>
    </tr>
    <tr bgcolor="#CCCCCC"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month/Year</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Processed</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">User</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
    </tr>
    <?
	
		$q = "select  distinct (ledger.remarks) as remarks,  date, admin_id, status, count(*)  as mcount, branch_id
								from 
									ledger, account  
								where 
									substring(ledger.remarks,4,4)='$year'  and
									type='P' and account.account_id = ledger.account_id";
		if ($branch_id != 0 and $branch_id != 99)
			$q .= " and branch_id = '$branch_id' ";							
		$q .= "						group by
									account.branch_id,
									ledger.remarks,
									date,
									admin_id,
									status
								order by ledger.remarks";

		$qr = @pg_query($q) or message(pg_errormessage().$q);
		while ($r = @pg_fetch_object($qr))
		{
			$ctr++;
				if ($r->status=='C')
				{
					$bgColor= '#FFCCCC';
				}
				else
				{
					$bgColor = '#EFEFEF';
				}

	?>
    <tr bgColor="<?= $bgColor;?>"> 
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">[ 
        <a href="?p=penalty.browse&show=<?=$r->remarks;?>&year=<?=$year;?>&branch_id=<?=$branch_id;?>&p1=Show"  onmouseover="showToolTip(event,'Show All <?= $r->mcount;?> Account(s)...');return false" onmouseout="hideToolTip()">+</a> ] 
        <?= $ctr;?>
        . 
        <input name="mark[]" type="checkbox" id="mark[]" value="<?=$r->remarks;?>">
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->remarks;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= ymd2mdy($r->date);?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id);?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->status;?>
        </font></td>
    </tr>
    <?

		if ($show == $r->remarks)
		{
	?>
					<tr><td></td><td colspan='4'>
				<table width="100%" cellpadding="1" cellspacing="1">
          <tr bgcolor="#E1E7F1" > 
            <td width="9%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></td>
            <td width="63%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account</font></td>
            <td width="28%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></td>
          </tr>
	<?	
			$q = "select 
						ledger_id,
						ledger.account_id,
						debit,
						status,
						branch_id	
					 from ledger, account where account.account_id = ledger.account_id and
								type='P' and ledger.remarks = '$show'";
			if ($branch_id != 0 and $branch_id != 99)
				$q .= " and branch_id = '$branch_id' ";
											
			$qqr = @pg_query($q) or message(pg_errormessage());
			$cc=0;

			while ($rr = @pg_fetch_object($qqr))
			{
				$cc++;
				if ($rr->status=='C')
				{
					$bgColor= '#FFCCCC';
				}
				else
				{
					$bgColor = '#FFFFFF';
				}
				?>

          <tr  bgColor="<?= $bgColor;?>"> 
            <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?= $cc;?>
              . 
              <input name="alid[]" type="checkbox" id="alid[]" value="<?=$rr->ledger_id;?>">
              </font></td>
            <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?= lookUpTableReturnValue('x','account','account_id','account',$rr->account_id);?>
              </font></td>
            <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?= number_format($rr->debit,2);?>
              </font></td>
          </tr>
          <?
		  	$show = '';
		  }
		  ?>
          <tr> 
            <td colspan="3" nowrap><input name="p12" type="button" id="p13" value="Unpost Checked" onClick="if(confirm('Are you sure to CANCEL  this entry?')){f1.action='?p=penalty.browse&show=<?=$show;?>&p1=unpostchecked';f1.submit();return false;}">
              <input name="p1" type="button" id="p1" value="Restore Checked" onClick="if(confirm('Are you sure to RESTORE all Unposted penalties for this Period?')){f1.action='?p=penalty.browse&show=<?=$show;?>&p1=restorechecked';f1.submit();return false;}"></td>
          </tr>
        </table>
				</td>
				</tr>
				<?
			
		}
	}
	?>
    <tr> 
      <td colspan="5"><input name="p1" type="button" id="p1" value="Cancel Checked" onClick="if(confirm('Are you sure to CANCEL all posted penalties for this Period?')){f1.action='?p=penalty.browse&p1=cancelchecked';f1.submit();return false;}">
      </td>
    </tr>
  </table>
</form>
