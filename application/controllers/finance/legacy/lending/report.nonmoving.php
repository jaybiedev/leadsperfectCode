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




/*if (($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print') && $account_group_id=='')
{
	message('Specify Account and Click GO...');
}
*/
if ($date_to == '') $date_to = date('Y-m-d');
if ($date_from == '') $date_from = date('Y-m-d');
if ($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print')
{
	if ($account_group_id == '') message("Report for ALL account groups...");
	$date_from = mdy2ymd($_REQUEST['date_from']);
	$date_to = mdy2ymd($_REQUEST['date_to']);

	$q = "select *
			from 
				account
			where
				enable";
	
	if ($account_group_id != '')
	{
		$q .= " and account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and branch_id='$branch_id'";
		$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	}
	else
	{
		$branch = '';
	}
	$q .= " order by account";
				
	$qr = pg_query($q) or message(pg_errormessage());
	$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
	if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	else $hdr = $SYSCONF['BUSINESS_NAME'];
	$header .= center($hdr,80)."\n";
	$header .= center('CLIENTS WITH NO EXCESS MOVEMENT ON THE GIVEN PERIOD '.($account_group_id == ''?' - ALL ACCOUNT GROUPS':''),80)."\n";
	$header .= center('Date Coverage :  From '.ymd2mdy($date_from).' To '.ymd2mdy($date_to),80)."\n\n";

	$header .= "BRANCH : ".$branch.space(15)."Printed: ".date('M. d Y g:ia')." ".$ADMIN['username']."\n";
	if ($p1 == 'Print Draft') $header .= "</bold>";
	$header .= "----- ---------------------------------------------------------------------------------------\n";
	$header .= "                                                                    Account  Last date of \n";
	$header .= "  #    NAME OF ACCOUNT                                                Code   Transaction\n";
	$header .= "----- ---------------------------------------------------------------------------------------\n";
	$ctr=0;
	while ($r = pg_fetch_assoc($qr))
	{
/*		$aid = $r[account_id];
		$qq = "select *
				from 
					payment_header, payment_detail
				where
					payment_detail.payment_header_id=payment_header.payment_header_id and
					account_id = '$aid' and
					payment_header.status!='C' 
					and date >= '$date_from' and date <= '$date_to'";
		$qqr = pg_query($qq) or message(pg_errormessage());
		if (pg_num_rows($qqr) > 0) 
		{
			continue;
		}
		
		$q = "select *
						from 
							wexcess 
						where 
							account_id = '".$r['account_id']."' and status !='C' and
							date >= '$date_from' and date <= '$date_to' 
						order by date";
		$qrw = @pg_query($q) or message(pg_errormessage());
		if (pg_num_rows($qrw) > 0) 
		{
			continue;
		}	
		$q = "select *
						from 
							releasing 
						where 
							account_id = '".$r['account_id']."' and status !='C' and
							date >= '$date_from' and date <= '$date_to' 
						order by date";
		$qrx = @pg_query($q) or message(pg_errormessage());
		if (pg_num_rows($qrx) > 0) 
		{
			continue;
		}	*/

		$q = "select *
						from 
							wexcess 
						where 
							account_id = '".$r['account_id']."' and status !='C'
						order by date DESC";
		$qrw = @pg_query($q) or message(pg_errormessage());
		$rs = pg_fetch_object($qrw);
		
		if (pg_num_rows($qrw)==0) continue;
		if ($rs->date >= $date_from and $rs->date <= $date_to) continue; 
		$q = "select * from releasing	where account_id = '".$r['account_id']."' and status !='C' and balance > 1";
		$qrw = @pg_query($q) or message(pg_errormessage());
		$rs = pg_fetch_object($qrw);
		if ($rs->balance <=0) 
		{
			continue;
		} 
			
		$ctr++;
		$cc=$ctr.'.';

		$details.=  adjustRight($cc,5).' '.
					adjustSize($r[account],60).' '.$r[account_code].'  '.ymd2mdy($rs->date)."\n";
							
		$lc++;			
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details .= "\n";
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);
			$details = '';
			$lc=6;
		}			
	}
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
	elseif ($p1 == 'Print')
	{
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}
}	

?>
<form action="" method="post" name="f1" id="f1" style="margin:10px">
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Clients with no Transaction on the Given Period</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="218" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Start Date of Report Coverage </font></td>
            <td width="154" valign="top" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
              <input name="date_from" type="text" id="date_from" value="<?= ymd2mdy($date_from);?>" size="10" onblur="IsValidDate(this,'MM/dd/yyyy')" onkeyup="setDate(this,'MM/dd/yyyy','en')" />
              <img src="../graphics/dwn-arrow-grn.gif" alt="date_from" onclick="popUpCalendar(this, f1.date_from, 'mm/dd/yyyy')" /> 
              </font></td>
            <td width="86" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
            <td width="394" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
            <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">End Date of Report Coverage </font></td>
            <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date_to" type="text" id="date_to" value="<?= ymd2mdy($date_to);?>" size="10" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_to, 'mm/dd/yyyy')"></font></td>
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
              <input name="p1" type="button" id="p1" value="Print"  onclick="printIframe(print_area)">
            </font></td>
          </tr>
          <tr bgcolor="#A4B9DB"> 
            <td height="24" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
              Preview</strong> &nbsp; </font></td>
          </tr>
          <tr align="left"> 
            <td height="24" colspan="4"><textarea name="print_area" cols="100" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
          </tr>
        </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
	<div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
