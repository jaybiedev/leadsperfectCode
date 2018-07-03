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
if (!chkRights2('account','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Loan Releasing ]...");
	exit;
}

/*if ($ADMIN[admin_id] != 1)
{
	message('This report is still under construction...');
	exit;
}	
if (($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print') && $account_group_id=='')
{
	message('Specify Account and Click GO...');
}
*/
if ($year==0 or $year=='') $year=date("Y");

if ($date_to == '') $date_to = date('Y-m-d');
if ($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print')
{
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	if ($account_group_id == '') message("Report for ALL account groups...");
	if ($month <12) $date_to = $year.'-'.str_pad($month+1,2,'0',STR_PAD_LEFT).'-01';
	else 
	{
		$yr = $year+1;
		$date_to = $yr.'-12-01';
	}	
	$q = "select sum(debit) as debit, sum(credit) as credit, insurance.releasing_id, account.branch_id, account.account, account.account_group_id
			from
				insurance, account
			where
				account.account_id = insurance.account_id and
				insurance.status != 'C' and insurance.date < '$date_to'";
				
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id'";
		$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	}
	else
	{
		$branch = '';
	}
	$q .= " 	group by insurance.releasing_id, account.branch_id, account.account, account.account_group_id
			    order by branch_id, account";
	$qr = pg_query($q) or message(pg_errormessage());

	$header = "";
	$screen = $prtdraft = $prtframe = '';
	$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
	if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	else $hdr = $SYSCONF['BUSINESS_NAME'];
	$header .= center($hdr,100)."\n";
	$header .= center('PENSIONER LIST'.($account_group_id == ''?' - ALL ACCOUNT GROUPS':''),100)."\n";
	$header .= center('Report Date As of '.ymd2mdy($date_to),100)."\n";
	$header .= center("Printed: ".date('M. d Y g:ia')." ".$ADMIN['username'],100)."\n\n";

/*	if ($account_group_id > 0)
		$header .= space(5)."Account Group : ".lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id).' '.$branch."\n";
	else	
		$header .= space(5)."Branch :".$branch."\n";
*/
	$header .= "----- ------------------------------------------------------- ------------ ----------- ------ ---------- ----------\n";
	$header .= "  #    NAME OF ACCOUNT                                            LOAN      INSURANCE   TERM       DUE     BALANCE\n";
	$header .= "----- ------------------------------------------------------- ------------ ----------- ------ ---------- ----------\n";

	$maccount_group_id='';
	$maccount_id = '';
	$lc=8;
	$ctr=0;
	$bid = 0;
	while ($r = pg_fetch_object($qr))
	{
		if ($bid != $r->branch_id or $bid==0)
		{
			$bid = $r->branch_id;
			$details .= "\nBranch :".lookUpTableReturnValue('x','branch','branch_id','branch',$bid)."\n\n";
			$lc+=3;			
		}
		$cc++;
		$qu = "select * from releasing where releasing_id='$r->releasing_id'";
		$qur = pg_query($qu) or message(pg_errormessage());
		$ru = pg_fetch_object($qur);
		
		$due = $r->credit/$ru->term;
		$bal = $r->credit-$r->debit - $due;
		$details.=  adjustRight($cc,5).' '.
					adjustSize($r->account,55).
					adjustRight(number_format($ru->gross,2),13).' '.
					adjustRight(number_format($r->credit,2),10).' '.
					adjustRight($ru->term,6).' '.
					adjustRight(number_format($r->credit/$ru->term,2),10).' '.
					adjustRight(number_format($bal,2),10).' '.
					"\n";
			
		$lc++;			
		if ($lc>58 && $p1 == 'Print Draft')
		{
			$prtdraft = $header.$details."<eject>";
			$screen  .= $header.$details."\n\n";
			$prtframe.= "<p style='page-break-after:always;'>".$header.$details."</p>";
			doPrint($prtdraft);
			$details = '';
			$lc=8;
		}			
	}

	$details .= "----- ------------------------------------------------------- ------------ ----------- ------ ---------- ----------\n";
	
	
	$prtdraft = $header.$details."<eject>";
	$screen  .= $header.$details."\n\n";
	$prtframe.= "<p style='page-break-after:always;'>".$header.$details."</p>";
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
}	

?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Pensioner 
        Listing </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="15%" valign="top" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
              </font> </td>
            <td width="10%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
            <td width="75%" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <select name = "account_group_id">
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
              </select>
            </font></td>
          </tr>
          <tr> 
            <td valign="top" nowrap style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><?=lookUpMonth('month',$month);?>&nbsp;
			<input id="year" name="year" type="text" value="<?=$year;?>" size="4" maxlength="4"/></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
              <select name = "branch_id">
                <?
				$q = "select * from branch where enable ";
				if ($ADMIN['branch_id'] > '0')
				{
	                echo "<option value=''>Select Branch</option>";
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
                <option value=''>All Branches</option>
                <?
				}
				$q .= "order by branch";
				
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
              <input name="p1" type="submit" id="p13" value="Go">
              <input name="p1" type="submit" id="p1" value="Print Draft">
              </font></td>
          </tr>
          <tr bgcolor="#A4B9DB"> 
            <td height="24" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
              Preview</strong> &nbsp; </font></td>
          </tr>
          <tr align="left"> 
            <td height="24" colspan="4"><textarea name="textarea" cols="130" rows="20" readonly wrap="OFF"><?= $screen;?></textarea>
			<textarea name="print_area" style="display:none;" readonly wrap="OFF"><?= $prtframe;?></textarea></td>
          </tr>
        </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
	<div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
 <?
if ($p1 == 'Search')
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
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location='?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>&p1=Go'"> 
    <td width="8%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$ctr;?>.&nbsp;</font></td>
    <td width="92%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<a href="?p=report.receivable&account_group_id=<?=$r->account_group_id;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>&xSearch=<?=$r->account_group;?>&account_group_id=<?=$r->account_group_id;?>&p1=Go">
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
