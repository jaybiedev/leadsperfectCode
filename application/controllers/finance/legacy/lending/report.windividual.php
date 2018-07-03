<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f2.action="?p=report.windividual&p1=CancelConfirm"
		}	
		else
		{
			document.f2.action="?p=report.windividual&p1=Cancel"
		}
	}
	else if (ul.name=='Search')
	{
		document.f1.action="?p=report.windividual&p1="+ul.id;
	}	
	else
	{
		document.f2.action="?p=report.windividual&p1="+ul.id;
	}
}
</script>
<?
if (!chkRights2('payment','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Payment View/Reports ]...");
	exit;
}

if (!session_is_registered('aIATM'))
{
	session_register('aIATM');
	$aIATM = null;
	$aIATM = array();
}


if ($aIATM['from_date']=='')
{
	$aIATM['from_date'] = date('Y-01-01');
	$aIATM['to_date'] = date('Y-12-31');
}

if (!in_array($p1,array('')))
{
	  	$aIATM['searchby'] = $_REQUEST['searchby'];
  		$aIATM['xSearch'] = $_REQUEST['xSearch'];
  		$aIATM['from_date'] = mdy2ymd($_REQUEST['from_date']);
  		$aIATM['to_date'] = mdy2ymd($_REQUEST['to_date']);
}

if ($p1 == 'SelectAccount' && $c_id!='')
{
	$q = "Select * from account where account_id='$c_id'";

	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r = @pg_fetch_assoc($qr);

	$from_date = $aIATM['from_date'];
	$to_date = $aIATM['to_date'];

	$aIATM = null;
	$aIATM = array();
	$aIATM = $r;
	
	$aIATM['from_date'] = $from_date;
	$aIATM['to_date'] = $to_date;
	
	if ($aIATM['from_date'] != '' && $aIATM['from_date'] != '//')
	{
		$p1 = 'Go';
	}
}

if ($p1 == 'Print Draft' || $p1=='Print'  || $p1 == 'Go')
{
	$details = '';
	$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$details .= center($SYSCONF['BUSINESS_ADDR'],80)."\n";
	$details .= center('ATM INDIVIDUAL REPORT',80)."\n";
	$details .= center('From '.ymd2mdy($aIATM['from_date']).' To '.ymd2mdy($aIATM['from_date']),80)."\n";
	$details .= center('Date Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$details .= 'Account  : '.adjustSize($aIATM['account'],40).' '.'Branch   : '.($aIATM['branch_id'] != '' ? lookUpTableReturnValue('x','branch','branch_id','branch',$aIATM['branch_id']) : '')."\n";
	$details .= '---------------------------------  ---------- --------------------- ------------ --- '."\n";
	$details .= '  Bank                               Date         Month/Year           Amount   '."\n";
	$details .= '---------------------------------  ---------- --------------------- ------------ --- '."\n";

	$q = "select * from payment_detail, payment_header
			 where 	
			 	payment_header.payment_header_id=payment_detail.payment_header_id and 
			 	((date_withdrawn>='".$aIATM['from_date']."' and
			 	date_withdrawn<='".$aIATM['to_date']."') or
				(ddate>='".$aIATM['from_date']."' and
			 	ddate<='".$aIATM['to_date']."')) and
				account_id = '".$aIATM['account_id']."'";
				
	$qr = @pg_query($q) or message(pg_errormessage().$q);

	$ctr=0;
	$total_amount = 0;
	while ($r = @pg_fetch_object($qr))
	{
			$ctr++;
			$details .= adjustRight($ctr,3).'.';
/*			if ($ctr==1)
			{
				$details .= adjustSize($aIATM['account'],30).' ';
			}
			else
			{
				$details .= space(31);
			}
*/			
			if ($aIATM['clientbank_id'] != '')
			{
				$details .= adjustSize(lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$aIATM['clientbank_id']),30).' ';
			}
			else
			{
				$details .= adjustSize($aIATM['clientbank'],30).' ';
			}
			
			if (is_null($r->date_withdrawn))
			{
				$wdate = $r->ddate;
				$amount = $r->amount;
			}
			else
			{
				$wdate = $r->date_withdrawn;
				$amount = $r->withdrawn;
			}
			$wdate =
			
			$details .= adjustSize(ymd2mdy($wdate),10).' '.
						adjustSize(substr($wdate,0,4).' '.cMonth(substr($wdate,5,2)),23).' '.
						adjustRight(number_format($amount,2),10).' '.
						($r->mconfirm ? '[X]' : '[ ]')."\n";
				
			$total_amount += $amount;	
			$lc++;	
			if ($lc > 55)
			{			
				if ($SYSCONF['RECEIPT_PRINT'] == 'DRAFT'  && $p1=='Print Draft')
				{
					$details .= "<eject>";
					doPrint($details);
				}
				elseif ($p1 == 'Print')
				{
					echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
					echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
					echo "<script>printIframe(print_area)</script>";
				}
				$lc=8;
				$details1 .= $details;
				$details='';
			}		
	}
	$details .= '---------------------------------  ---------- --------------------- ------------ --- '."\n";
	$details .= space(20).'GRAND TOTAL  '.space(35).adjustRight(number_format($total_amount,2),12)."\n";
	$details .= '---------------------------------  ---------- --------------------- ------------ --- '."\n";
	$details1 .= $details;
	if ($SYSCONF['RECEIPT_PRINT'] == 'DRAFT'  && $p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($details);
	}
	elseif ($p1 == 'Print')
	{
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}

}
?> 
<br>
<form action="" method="post" name="f1" id="f1">
  <table width="70%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
    <tr> 
      <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Individual 
        Passbook Report</b></font></td>
      <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
      <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
    </tr>
    <tr bgcolor="#A4B9DB"> 
      <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="91" height="26" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account</font></td>
            <td width="589" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" size="30">
              </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <?=lookUpAssoc('searchby',array('Account Name'=>'account','Account No'=>'bank_account','Card No.'=>'bank_card'),$aIATM['searchby']);?>
              <input name="p1" type="submit" id="p1" value="Search">
              </font> </td>
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
            <td height="18" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
              From </font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="from_date" type="text" id="from_date" value="<?= ymd2mdy($aIATM['from_date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"> 
              </font> </td>
          </tr>
          <tr> 
            <td height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">To</font></td>
            <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="to_date" type="text" id="to_date" value="<?= ymd2mdy($aIATM['to_date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
              </font> </td>
          </tr>
          <tr align="center"> 
            <td height="24" colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
              </font></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
    </tr>
  </table>
  </form>
<?
  if ($p1 == 'Search')
  {

	  $q = "select * 
				from 
					account
				where 
					".$aIATM['searchby']."  ilike '".$aIATM['xSearch']."%'
				order by account";
					
		$qr = @pg_query($q)	or message("Error Querying account file...".pg_errormessage());
?>
  
<table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="38%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="11%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      No. </font></strong></td>
    <td width="10%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></strong></td>
    <td width="24%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      Group </font></strong></td>
    <td width="10%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		$account_balance=accountBalance($r['account_id']);
		$ctr++;
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		<a href="javascript: f1.action='?p=report.windividual&p1=SelectAccount&c_id=<?=$r['account_id'];?>';f1.submit()"> 
      <?= $r['account'];?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r['bank_account'];?>
      </font></td>
    <td>
	<font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= ($r['branch_id'] > 0 ? lookUpTableReturnValue('x','branch','branch_id','branch',$r['branch_id']) : '');?>
    </font>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      <?= status($r['account_status']);?>
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?	
  }
  elseif ($aIATM['account_id'] != '')
  {
?>
<form action="" method="post" name="f2" id="f2">
  <table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
    <tr> 
      <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/bluelist.gif" width="16" height="17"></strong><b> 
        Individual Withdrawal</b><strong> Preview</strong></font> </td>
      <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
      <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
    </tr>
    <tr bgcolor="#A4B9DB"> 
      <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <tr> 
            <td><textarea name="textarea" cols="90" rows="18" readonly wrap="OFF"><?= $details1;?></textarea></td>
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

<?
}
?>

