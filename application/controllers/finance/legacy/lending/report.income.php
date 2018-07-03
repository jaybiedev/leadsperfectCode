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

if ($payroll_period_id == '') $payroll_period_id =$PAYROLL_PERIOD_ID;

if (($p1=='Go' || $p1=='Print Draft') && $payroll_period_id!= '' && $income_type_id!='')
{
	$date = date('m/d/Y');
	$mdate = mdy2ymd($date);
	$payroll_period_id = $_REQUEST['payroll_period_id'];
	$income_type_id = $_REQUEST['income_type_id'];
	
	if ($income_type_id == 'NET')
	{
		$q = "select 
					idnum,
					elast,
					efirst,
					emiddle,
					net_income as amount
			from 
				payroll_header as ph,
				paymast
			where
				paymast.paymast_id = ph.paymast_id and
				ph.status!='C' and 
				ph.payroll_period_id = '$payroll_period_id' ";
		$income_type = 'NET INCOME ';
	}
	else
	{
		$q = "select * 
			from 
				payroll_detail as pd,
				payroll_header as ph,
				paymast
			where
				paymast.paymast_id = ph.paymast_id and
				ph.payroll_header_id = pd.payroll_header_id and
				ph.payroll_period_id = '$payroll_period_id' and
				type='I' and
				type_id = '$income_type_id'";
				
		$income_type = lookUpTableReturnValue('x','income_type','income_type_id','income_type',$income_type_id);
	}
	if ($branch_id != '')
	{
		$subtitle .= strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id)).'  BRANCH';
		$q .= " and branch_id = '$branch_id'";
	}
	else
	{
		$subtitle .= " ALL BRANCHES ";

	}
	if ($department_id != '')
	{
		$subtitle .= strtoupper(lookUpTableReturnValue('x','department','department_id','department',$department_id));
		$q .= " and department_id = '$department_id'";
	}
	else
	{
		$subtitle .= ' ALL DEPARTMENTS';
	}
	$q .= " order by elast, efirst ";

	$qr = @pg_query($q) or message(pg_errormessage().$q);
	if ($p1 == 'Print Draft') $details .= "<small3>";
	$header .= "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('INCOME LIST :: '.$income_type,80)."\n";
	$header .= center($subtitle,80)."\n";
	$header .= center('Payroll Period :'.lookUpPayPeriodReturnValue('x',$payroll_period_id).' Printed: '.$date,80)."\n\n";
	$header .= "---- ---------------------------------------- -------- ---------------\n";
	$header .= "      Empployee Name                             Qty        Amount    \n";
	$header .= "---- ---------------------------------------- --------- --------------\n";
	$details = $details1 = '';
	$details1 = $header;
	$ctr = $total_amount = 0;
	$lc=8;
	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;	
		$details .= adjustRight($ctr,3).'. '.
					adjustSize($r->elast.', '.$r->efirst,40).' '.
					adjustRight($r->qty,7).'  '.
					adjustRight(number_format($r->amount,2),14)."\n";
		$lc++;
		
		$total_amount += $r->amount;
		
		if ($lc>55 && $p1 == 'Print Draft')
		{
			doPrint($header.$details."<eject>");
			$lc=8;
			$details1 .= $header.$details;
			$details = '';
		}
	}
	$details .= "---- ---------------------------------------- --------- --------------\n";
	$details .= space(40).adjustSize('Total Amount :',15).adjustRight(number_format($total_amount,2),14)."\n";

	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
}
?>	
<br>
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="75%" border="0" align="center" cellpadding="1" cellspacing="1">
      <tr> 
        <td height="27" colspan="4" background="../graphics/table_horizontal.PNG">&nbsp; 
          <strong><font color="#F3F7F9" size="2" face="Verdana, Arial, Helvetica, sans-serif">:: 
          Income Listing </font></strong></td>
      </tr>
      <tr> 
        <td width="3%" height="27" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Payroll 
          Period<br>
          <strong> 
          <?= lookUpPayPeriod('payroll_period_id',$payroll_period_id);?>
          </strong> </font></td>
        <td width="3%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch 
          <br></font><font size="2" fa="fa" ce="Verdana, Arial, Helvetica, sans-serif">
          <select name = "branch_id" id = "branch_id" style="width:150">
            <?
				$q = "select * from branch where enable ";
				
				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					$q .= ") ";
				} else
				{
					?>
            <option value=''>All Branches</option>
            <?
				}
				$q .=  "order by branch";
				
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
          </font></td>
        <td width="6%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Department</font><br> 
          <select name="department_id" id="department_id" style="width:150">
            <option value=''>All Departments</option>
            <?
			$q = "select * from department order by department";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			while ($r = @pg_fetch_object($qr))
			{
				if ($department_id == $r->department_id)
				{
					echo "<option value=$r->department_id selected>$r->department</option>";
				}
				else
				{
					echo "<option value=$r->department_id>$r->department</option>";
				}	
			}
			?>
          </select></td>
        <td width="94%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Income 
          Type<br>
          <select name="income_type_id" id="income_type_id" style="width:150">
            <option value=''>Select Income</option>
            <option value='NET' <?= ($income_type_id == 'NET' ? 'selected' : '');?>>Net Income</option>
            <?
			$q = "select * from income_type order by income_type";
			$qr = @pg_query($q) or message(pg_errormessage().$q);
			while ($r = @pg_fetch_object($qr))
			{
				if ($income_type_id == $r->income_type_id)
				{
					echo "<option value=$r->income_type_id selected>$r->income_code $r->income_type</option>";
				}
				else
				{
					echo "<option value=$r->income_type_id >$r->income_code $r->income_type</option>";
				}	
			}
			?>
          </select>
          <input name="p1" type="submit" id="p1" value="Go">
          </font></td>
      </tr>
      <tr> 
        <td height="27" colspan="4"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td nowrap bgcolor="#DADADA"><font color="#666666" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17">Income 
                Listing Preview</strong></font></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" valign="top" bgcolor="#FFFFFF"> <textarea name="print_area" cols="110" rows="20"  wrap="off" readonly><?= $details1;?></textarea> 
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
