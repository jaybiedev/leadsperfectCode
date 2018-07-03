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
if (!chkRights2('payment','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Payment View Reports ]...");
	exit;
}

if ($date == '') $date = date('m/d/Y');
if ($p1=='Go' || $p1=='Print Draft')
{
	$mdate = mdy2ymd($date);
	
	$q = "select *
		from 
			account, clientbank
		where 
			clientbank.clientbank_id=account.clientbank_id and
			account.enable and
			date_atm_out IS Null ";
	if ($clientbank_id != '')
	{
		$q .= " and account.clientbank_id='$clientbank_id' ";
	}
	elseif ($clientbank != '')
	{
		$q .= " and clientbank ilike '%$clientbank%'";
	}
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id' ";
	}
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id' ";
	}
	if ($collection_type_id != '')
	{
		$q .= " and account.collection_type_id='$collection_type_id' ";
		$collection_type = lookUpTableReturnValue('x','collection_type','collection_type_id','collection_type',$collection_type_id);
	}
	else
	{
		$collection_type = 'ATM/PASSBOOK ';
	}
	
	if ($show == 'S')
	{
		$q .= " order by account ";
	}
	elseif ($show == 'B')
	{
		$q .= " order by clientbank, account ";
	}	
	elseif ($show == 'R')
	{
		$q .= " order by clientbank, branch_id, account ";
	}	

	
	$qr = @pg_query($q) or message(pg_errormessage());
	if ($p1 == 'Print Draft') $topheader = "\n\n<small3>";
	else $topheader .= "\n\n";
	
	$topheader .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$topheader .= center($collection_type.' INVENTORY AS OF '.$date,120)."\n";
	$lc = 11;
	$page=1;
	if ($branch_id !='')
	{
		$topheader .= center(lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id),120)."\n";
	}
	else
	{
		$topheader .= center('ALL BRANCHES',120)."\n";
	}
	if ($account_group_id !='')
	{
		$topheader .= center(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$account_group_id),120)."\n";
	}
	else
	{
		$topheader .= center('ALL ACCOUNT GROUPS',120)."\n";
	}
	$pageheader = center('Printed '.date('m/d/Y g:ia').' Page: '.$page,120)."\n\n";

	if ($show == 'S' && $clientbank != '' && $clientbank_id=='')
	{
		$header .= "\n".center('BANK : '.$clientbank,80)."\n";
		$lc +=2;
	}
	$lineheader .= "---- ------------------------------ --------------- --------------- ------------ ------------------------------------------\n";
	$lineheader .= "     Name of Account                 Bank            Account No.     Salary      Account Group\n";
	$lineheader .= "---- ------------------------------ --------------- --------------- ------------ ------------------------------------------\n";
	$details = $details1 = '';
	//$details1 = $header;

	$lc = 13;
	$total_amount = $total_month1 = $total_month2 = $total_month3= $total_month4= $total_month5 = $total_due=0;
	$total_salary = $sub_salary = 0;
	$ctr=$sctr=0;
	while ($r = @pg_fetch_object($qr))
	{
		//updateReleasing($r->releasing_id);
		if (($mclientbank!=trim($r->clientbank) && $show=='B') || ($mclientbank_id!=$r->clientbank_id && $show=='R'))
		{
			if ($sub_salary != '0')
			{
				$details .= space(10).adjustSize($sctr.' Item(s)',25).adjustSize('SubTotal',32).' '.
						adjustRight(number_format($sub_salary,2),12)."\n";
				$details .="\n";
				$lc = $lc + 2;
				$sub_salary = $sctr = 0;
			}
			if ($lc > 50)
			{
				$header = $topheader.$pageheader.$lineheader;
				if (($from_page!='' && $page>= $from_page && $page<= $to_page)||$from_page=='')
				{
					if ($p1 == 'Print Draft')
					{
						doPrint($header.$details."<eject>");	
					}
					$details1 .= $header.$details;
				}
				$lc = 13;
				$details = '';
				$page++;
				$pageheader = center('Printed '.date('m/d/Y g:ia').' Page: '.$page,120)."\n\n";
			}		
			if ($mclientbank != '')
			{
				$details .= "\n";
				$lc++;
			}
			if ($r->clientbank != '' && $show=='B')
			{
				$details .= adjustSize($r->clientbank,60)."\n";
				//$details .= adjustSize(strtoupper(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id)),30)."\n";
			}
			elseif ($r->clientbank_id != '' && $show=='R')
			{
				$details .= adjustSize($r->clientbank.', '.$r->clientbank_address,60)."\n";
				//$details .= adjustSize(strtoupper(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$r->clientbank_id)),30)."\n";
			}
			else
			{
				$details .= "NO BANK\n";
			}	
			$details .= str_repeat('-',30)."\n";
			$mclientbank_id = $r->clientbank_id;
			$mclientbank = trim($r->clientbank);
			$lc++;
			$lc++;
		}
		

		$ctr++;
		$sctr++;	
		$details .= adjustRight($ctr,3).'. '.adjustSize($r->account,30).' '.
					adjustSize(substr($r->clientbank,0,15),15).' '.
					adjustSize($r->bank_account,15).' '.
					adjustRight(number_format($r->salary,2),12).'  ';
		if ($r->account_group_id != '')
		{
			$details .= adjustSize(strtoupper(substr(lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id),0,25)),25)."\n";
		}
		else
		{
			$details .= "\n";
		}
		$lc++;
		if ($lc > 50)
		{
			$header = $topheader.$pageheader.$lineheader;
			if (($from_page!='' && $page>= $from_page && $page<= $to_page)||$from_page=='')
			{
				if ($p1 == 'Print Draft')
				{
					doPrint($header.$details."<eject>");
				}
				$details1 .= $header.$details;
			}
			$lc = 13;
			$details = '';
			$page++;
			$pageheader = center('Printed '.date('m/d/Y g:ia').' Page: '.$page,120)."\n\n";
		}		
		$total_salary += $r->salary;
		$sub_salary += $r->salary;
	}
	if ( $show=='B' || $show=='R')
	{
				$details .= space(10).adjustSize($sctr.' Item(s)',25).adjustSize('SubTotal',32).' '.
						adjustRight(number_format($sub_salary,2),12)."\n";
	}
	$details .= "---- ------------------------------ --------------- --------------- ------------ ------------------------------------------\n";
	$details .= space(15).adjustSize('TOTAL',52).' '.
					adjustRight(number_format($total_salary,2),12)."\n";
	$details .= "---- ------------------------------ --------------- --------------- ------------ ------------------------------------------\n";

	$header = $topheader.$pageheader.$lineheader;

	if (($from_page!='' && $page>= $from_page && $page<= $to_page)||$from_page=='')
	{
		if ($p1 == 'Print Draft')
		{
			doPrint($header.$details."<eject>");
		}
		$details1 .= $header.$details;
	}

}
if ($date == '') $date=date('m/d/Y');	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Passbook 
          Inventory </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="214" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Client 
                Bank</font></td>
              <td colspan="2" valign="top"> <input name="clientbank" type="text" value="<?= $clientbank;?>" size="22"> 
                &nbsp; <select name="clientbank_id" id="select">
                  <option value=''>All Banks</option>
                  <?
			  	$q = "select  clientbank,clientbank_id, clientbank_address
						from
							clientbank
						where
							enable
						order by
							clientbank";
				$qr = pg_query($q);
				while ($r = pg_fetch_object($qr))
				{
					if ($r->clientbank_id == $clientbank_id)
					{
						echo "<option value=$r->clientbank_id selected>$r->clientbank - $r->clientbank_address</option>";
					}
					else
					{
						echo "<option value=$r->clientbank_id>$r->clientbank - $r->clientbank_address</option>";
					}	
				}
			  ?>
                </select> <select name = "collection_type_id">
                  <option value=''>ATM/Passbook</option>
                  <?
				$q = "select * from collection_type where enable order by collection_type";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($collection_type_id == $r->collection_type_id)
					{
						echo "<option value=$r->collection_type_id selected>$r->collection_type</option>";
					}
					else
					{	
						echo "<option value=$r->collection_type_id>$r->collection_type</option>";
					}	
				}
				
			?>
                </select> </td>
            </tr>
            <!--            <tr> 
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">As 
                of</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="date" type="text" id="date2"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.date, 'mm/dd/yyyy')"> 
                </font></td>
            </tr>
-->
            <tr> 
              <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Show</font></td>
              <td colspan="2" valign="top"> 
                <?= lookUpAssoc('show',array('Group By Bank'=>'B','Group By Bank Branch'=>'R','Summary(Alphabetical)'=>'S'),$show);?>
                <? // = lookUpAssoc('include_zero',array('With Balances Only'=>'B','Show All Accounts'=>'A'),$include_zero);?>
              </td>
            </tr>
            <tr> 
              <td height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
              <td colspan="2" valign="top"> <select name = "account_group_id">
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
                </select> </td>
            </tr>
            <tr> 
              <td height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td width="356" valign="top"> <select name = "branch_id">
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
                </select> Pages <input size="3" type="text" name="from_page" value="<?=$from_page;?>">
                To <input size="3" type="text" name="to_page" value="<?=$to_page;?>"></td>
              <td width="248" align="center" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr align="left" bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="textarea" cols="110" rows="18" readonly wrap="OFF"><?= $details1;?></textarea></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
