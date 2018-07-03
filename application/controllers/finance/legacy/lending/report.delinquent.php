 <script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<?
if (!chkRights2('delinquent','mview',$ADMIN['admin_id']) )
{
	message("You have no permission in this Area [ Payment View/Reports ]...");
	exit;
}

if ($adue == '') $adue =2;

if ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	
	$q = "select * 
				from 
					releasing,
					account
				where
					releasing.account_id=account.account_id and
					releasing.balance>0";
	if ($account_group_id != '')
	{
		$q .= " and releasing.account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and branch_id='$branch_id'";
	}
	$q .= " order by account";
				

	$qr = @pg_query($q) or message(pg_errormessage());
	if ($p1 == 'Print Draft')
	{
		$header = "<small3>";
	}
	else
	{
		$header = '';
	}
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('DELIQENT LIST AS OF '.$from_date,80)."\n\n";

	$header .= "  #   Name of Account           Released   Last Pay    No.     Amount Due    \n";
	$header .= "---- ------------------------- ---------- ---------- -------- ------------- ---------------\n";

	$maccount_group_id='';
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	while ($r = @pg_fetch_assoc($qr))
	{
			$r['releasing_date'] = $r['date'];			
			$aAd = amountDue($r,$mfrom_date);
//if ($ADMIN[admin_id]==1 and $r[account_id]==2139) echo $aAd[account].'  '.$aAd[months_due];
 			
			if ($aAd['actual_due'] < $adue) continue;
			
	
		if ($maccount_group_id != $r['account_group_id'])
		{
			if ($p1 == 'Print Draft') $details .= "<bold>";
			$details .= "\nAccount Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id'])."\n";
			if ($p1 == 'Print Draft') $details .= "</bold>";
			$details .= str_repeat('-',30)."\n";
			$lc += 2;
			$maccount_group_id = $r['account_group_id'];
		}

		$ctr++;
		$details.=  adjustRight($ctr,3).'. '.
					adjustSize($r['account'],25).' '.
					adjustSize(ymd2mdy($r['date']),10).' '.
					adjustSize(ymd2mdy($aAd['lastpay']),10).' '.
					adjustRight($aAd['months_due'],5).'    '.
					adjustRight(number_format($aAd['amount_due']+$aAd['penalty'],2),12)."\n";
		$lc++;
		
		$total_amount += $aAd['amount_due'];			
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject><reset>";
			doPrint($header.$details);
			$details = '';
			$lc=6;
		}			
	}

	$details .= "---- ------------------------- ---------- ---------- -------- ------------- ---------------\n";
	$details .= space(40).adjustSize('TOTAL AMOUNT ->',20).'  '.
				adjustRight(number_format($total_amount,2),12)."\n";
	$details .= "---- ------------------------- ---------- ---------- -------- ------------- ---------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($details1);
	}	
}	

?>
<form action="" method="post" name="f1" id="f1">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  	<tr>
      <td height="25" background="../graphics/table0_horizontal.PNG"><font color="#FFFFFF" size="2"><strong>Deliquent 
        List</strong></font></td>
    </tr>
    <tr> 
      <td bgcolor="#FFFFFF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        As of 
        <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"></font> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Group 
        <select name = "account_group_id" id="account_group_id" >
          <option value=''>All Account Groups</option>
          <?
				$q = "select * from account_group where enable order by account_group";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($account_group_id == $r->account_group_id)
					{
						echo "<option value=$r->account_group_id selected>$r->account_group</option>";
					}
					else
					{	
						echo "<option value=$r->account_group_id>$r->account_group</option>";
					}	
				}
				
			?>
        </select><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font>
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
        Months Delinquent 
        <input name="adue" type="text" id="adue" value="<?= $adue;?>" size="3"/>
        <input name="p1" type="submit" id="p1" value="Go">
        </font> 
        <hr color="#CC3300"> </td>
    </tr>
  </table>
</form>
<?
if (in_array($p1,array('Go','Print','Print Draft')))
{
?>
  <div align="center">
    <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              
            <td width="38%"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <img src="../graphics/bluelist.gif" width="16" height="17">Delinquent 
              List Preview</strong></font></td>
              
            <td width="62%" align="right" nowrap><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              </strong></font> </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="100" rows="18" readonly wrap="OFF"><?= $details1;?></textarea>
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
 <?
 } //print preview
 elseif ($p1 == 'Search')
 {
 ?>
	<table width="50%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
	  <tr bgcolor="#330099"> 
		<td height="19" colspan="2"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Select 
		  Account Group For Report</strong></font></td>
	  </tr>
 <?
 	if ($xSearch == '')
	{
	 	$q = "select * from account_group where enable order by account_group";
	}
	else
	{
	 	$q = "select * from account_group where account_group ilike '$xSearch%' and enable order by account_group";
	}
	
	$qr = pg_query($q) or die (pg_errormessage());
	$ctr=0;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location='?p=report.delinquent&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>'"> 
    <td width="8%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$ctr;?>.&nbsp;</font></td>
    <td width="92%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<a href="?p=report.delinquent&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>">
	<?= $r->account_group;?></a></font></td>
  </tr>
		<?
	}
	?>
	</table>
	<?
 }
 ?> 
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
