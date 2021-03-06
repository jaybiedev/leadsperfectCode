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
			document.f2.action="?p=report.payrollaccountledger&p1=CancelConfirm"
		}	
		else
		{
			document.f2.action="?p=report.payrollaccountledger&p1=Cancel"
		}
	}
	else if (ul.name=='Go')
	{
		document.f1.action="?p=report.payrollaccountledger&p1="+ul.id;
	}	
	else
	{
		document.f2.action="?p=report.payrollaccountledger&p1="+ul.id;
	}
}
</script>
<?
if (!chkRights2('payrollmodule','madd',$ADMIN['admin_id']))
{
	message("You have no permission to access this report");
	exit;
}

if (!session_is_registered('aLedger'))
{
	session_register('aLedger');
	$aLedger = null;
	$aLedger = array();
}
if (!session_is_registered('aLedgerDetail'))
{
	session_register('aLedgerDetail');
	$aLedgerDetail = null;
	$aLedgerDetail = array();
}
if ($ADMIN[admin_id]==1)
{
    $q = "select * from payroll_header as ph, payroll_detail as pd where ph.payroll_header_id=pd.payroll_header_id and paymast_id=1176 and type_id='6' and type='D'";
	$qr = @pg_query($q) or message(pg_errormessage());			
	while ($r = @pg_fetch_assoc($qr))
	{
		print_r($r);
		echo "<br><br>";
	}
}
if ($c_id!= ''&& $p1 == 'Selectaccount')
{
	$aLedger=null;
	$aLedger=array();
	$q = "select 
				paymast.idnum,
				paymast.paymast_id,
				paymast.elast,
				paymast.efirst,
				paymast.address
		 from 
		 		paymast
		where 
				paymast.paymast_id='$c_id'";
	$qr = @pg_query($q) or message(pg_errormessage());			
	$r = @pg_fetch_assoc($qr);
	$aLedger = $r;
	$p1='selectaccountId';
}	
if ($p1 != '')
{
	$aLedger['xSearch'] = $_REQUEST['xSearch'];
	$aLedger['sortby'] = $_REQUEST['sortby'];
	$aLedger['from_date'] = $_REQUEST['from_date'];
	$aLedger['deduction_type_id'] = $_REQUEST['deduction_type_id'];
}

if ($p1 == 'Print Draft' || $p1=='Print' || $p1 == 'selectaccountId' || $aLedger['paymast_id']!='')
{
	$details = '';
	$details .= center('EMPLOYEE ACCOUNT LEDGER',80)."\n";
	$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$details .= center($SYSCONF['BUSINESS_ADDR'],80)."\n";
	$details .= center('Date Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$details .= 'Account  : ['.adjustSize($aLedger['idnum'],10).']'.adjustSize($aLedger['elast'].', '.$aLedger['efirst'],30).'   Tel No.'.$aLedger['telno']."\n";
	$details .= 'Address  : '.adjustSize($aLedger['address'],45)."\n";
	$details .= '---------- ---------- ----------- - ------------ ------------+------------ ------------'."\n";
	$details .= 'Date       Reference   Type           Credit         Debit   |   Deducted     Balance'."\n";
	$details .= '---------- ---------- ----------- - ------------ ------------+------------ ------------'."\n";
	$q = "select 
			*			 
		from 
			payrollcharge 
		where 
			enable='Y' and 
			paymast_id='".$aLedger['paymast_id']."'";
			
	if ($aLedger['deduction_type_id'] != '')
	{
		$q .= " and deduction_type_id = '".$aLedger['deduction_type_id']."'";
	}
	$q .= " order by	date";

	$qr = @pg_query($q) or message(pg_errormessage());
	
	$balance = 0;
		while ($temp = @pg_fetch_assoc($qr))
	   {

			$deduction_type = lookUpTableReturnValue('x','deduction_type','deduction_type_id','deduction_type',$temp['deduction_type_id']);
	   		$balance += $temp['credit']-($temp['debit']+$temp['deduct']) ;  //$temp['grocery_debit'] + $temp['drty_goods_debit'] - $temp['grocery_credit']-$temp['dry_goods_credit'];
			if (($temp['date'] < mdy2ymd($aLedger['from_date']))   && ($aLedger['from_date']!=''))
			{

				$beginning_balance = $balance;
				continue;
			}
			elseif ($beginning_balance > 0 )
			{
				$details .= adjustSize(ymd2mdy($mfrom_date),10).' '.
						adjustSize('Beginning Balance',55).
						adjustRight(number_format($beginning_balance,2),12)."\n";
			}
			
			$beginning_balance = 0;
			$details .= adjustSize(ymd2mdy($temp['date']),10).' '.
					adjustSize($temp['invoice'],10).' '.
					adjustSize($deduction_type,12).'  '.
					adjustRight(number_format2($temp['credit'],2),12).' '.
					adjustRight(number_format2($temp['debit'],2),12).'|'.
					adjustRight(number_format2($temp['deduct'],2),12).' '.
					adjustRight(number_format($balance,2),12)."\n";
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'] ;
			$total_deduct +=  $temp['deduct'];
  }
  
	//--unposted
	$q = "select 
					period2 as date,
					date_entry,
					pd.amount as deduct,
					pd.type_id as deduction_type_id 
				from
					payroll_header as ph,
					payroll_detail as pd,
					payroll_period as pp,
					deduction_type
				where
					ph.payroll_header_id = pd.payroll_header_id and
					pp.payroll_period_id = ph.payroll_period_id and
					deduction_type.deduction_type_id = pd.type_id and
					(basis = 'L' or basis = 'P') and date_entry > '2012-07-16' and
					pp.post='N' and 
					ph.status!='C' and
					pd.type='D' and 
					pd.enable!='N' and 
					paymast_id='".$aLedger['paymast_id']."'";
			
		if ($aLedger['deduction_type_id'] != '')
		{
			$q .= " and pd.type_id = '".$aLedger['deduction_type_id']."'";
		}
		$q .= "order by date_entry";
		
		$qr = @pg_query($q) or message1(pg_errormessage());
		while ($temp = @pg_fetch_assoc($qr))
	   {

			$deduction_type = lookUpTableReturnValue('x','deduction_type','deduction_type_id','deduction_type',$temp['deduction_type_id']);
	   		$balance += $temp['credit']-$temp['debit']-$temp['deduct'] ; 
			if (($temp['date'] < mdy2ymd($aLedger['from_date']))   && ($aLedger['from_date']!=''))
			{

				$beginning_balance = $balance;
				continue;
			}
			$details .= adjustSize(ymd2mdy($temp['date']),10).' '.
					adjustSize('*UNPOSTED',10).' '.
					adjustSize($deduction_type,12).'  '.
					adjustRight(number_format2($temp['credit'],2),12).' '.
					adjustRight(number_format2($temp['debit'],2),12).'|'.
					adjustRight(number_format2($temp['deduct'],2),12).' '.
					adjustRight(number_format($balance,2),12)."\n";
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'] ;
			$total_deduct +=  $temp['deduct'];
  }
 	$details .= '---------- ---------- ----------- - ------------ ------------+------------ ------------'."\n";
	
				
					
	$details .= space(10).' Account Balance:'.space(9).
				adjustRight(number_format($total_credit,2),12).' '.
				adjustRight(number_format($total_debit,2),12).'|'.
				adjustRight(number_format($total_deduct,2),12).' '.
				adjustRight(number_format($balance,2),12)."\n";
	$details .= '---------- ---------- ----------- - ------------ ------------+------------ ------------'."\n";
	$details .= 'Remarks :'."\n";
	$details .= $remarks."\n";
	$details1 = $details;
	if ($p1=='Print Draft')
	{
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		nPrinter($header.$details, $SYSCONF['REPORT_PRINTER_TYPE'], $SYSCONF['REPORT_PRINTER_DEST']);
	}	
	elseif ($p1 == 'Print')
	{
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$details</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}
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
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td height="26" background="../graphics/table_horizontal.PNG" bgcolor="#003366"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        :: Account Ledger ::</strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $aLedger['xSearch'];?>">
        <?=lookUpAssoc('sortby',array('Name'=>'elast','Id No.'=>'idnum'),$aLedger['sortby']);?>
        <?=lookUpAssoc('rtype',array('Summary'=>'S','Detailed'=>'D'),$rtype);?>
        <select name="deduction_type_id"  id="deduction_type_id"  style="width:150">
          <option value=''>All Charges</option>
          <?
			$q = "select * from deduction_type where enable='Y' order by deduction_type";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			while ($r = @pg_fetch_object($qr))
			{
				if ($r->deduction_type_id == $aLedger['deduction_type_id'])
				{
					echo "<option value=$r->deduction_type_id selected>$r->deduction_type</option>";
				}
				else
				{
					echo "<option value=$r->deduction_type_id>$r->deduction_type</option>";
				}	
			}
			?>
        </select>
        As of </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $aLedger['from_date'];?>" size="8">
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" color="#000000"><img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"></font></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p12" type="button" id="p12" value="Close" onClick="window.location='?p='">
        </font></td>
    </tr>
    <tr> 
      <td><hr color="#BBBBEE" size="1"></td>
    </tr>
  </table>

<?
  if ($p1 == 'Go')
  {
	  $q = "select * 
				from 
					paymast
				where 
					$sortby ilike '$xSearch%'
				order by
					$sortby";
					
					
		$qr = @pg_query($q)	or message("Error Querying Employee  file...".pg_errormessage());
?>
  
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="34%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Id 
      No. </font></strong></td>
    <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      No. </font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
    <td width="15%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
  </tr>
  <?
  include_once('accountbalance.php');
  	$ctr=0;
  	while ($r = @pg_fetch_assoc($qr))
	{
		$ctr++;
		$aBal = employeeTotalBalance($r['paymast_id'], '');

  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="javascript: document.getElementById('f1').action='?p=report.payrollaccountledger&p1=Selectaccount&c_id=<?=$r['paymast_id'];?>&xSearch=<?=$xSearch;?>&rtype=<?=$rtype;?>';document.getElementById('f1').submit()"> 
      <?= $r['elast'].', '.$r['efirst'];?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r['idnum'] ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= status($r['account_status']);?>
      </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($aBal['balance'],2);?>
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?
	echo "</form>";	
  }
  elseif ($aLedger['paymast_id'] != '')
  {
?>

<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr> 
      <td colspan="4" bgcolor="DADADA"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Account 
        Ledger </strong></font></td>
  </tr>
  <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="97" rows="18" readonly wrap="OFF"><?= $details1;?></textarea>
        <br>
        Remarks<br> 
        <textarea name="remarks" cols="97" id="remarks"><?= $remarks;?>
</textarea> </td>
  </tr>
</table>
  </form>
<div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>

<?
}
?>

