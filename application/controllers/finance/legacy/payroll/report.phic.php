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
if (!chkRights2('payrollmodule','madd',$ADMIN['admin_id']))
{
	message("You have no permission to access this report");
	exit;
}

if ($from_date == '') 
{

	if ($SYSCONF['NUM']  == 1)
	{
		$from_date = substr($SYSCONF['PAYROLL_PERIOD'],0,10);
	}
	else
	{
		$month = $SYSCONF['MONTH'];
		$q = "select * from payroll_period where month = '$month' and year = '".$SYSCONF['YEAR']."' and enable='Y' order by period1 offset 0 limit 1";
		$qr = @pg_query($q);
		$r = @pg_fetch_object($qr);
		$from_date = ymd2mdy($r->period1);
	}
}
if ($to_date == '') $to_date = substr($SYSCONF['PAYROLL_PERIOD'],11,10);

if (($p1=='Go' || $p1=='Print Draft') )
{
	$date = date('m/d/Y');
	$from_date = $_REQUEST['from_date'];
	$to_date = $_REQUEST['to_date'];
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	
	if ($collate == '1')
	{
		$based = 'Whole Month';
	}
	else
	{
		$based = $from_date.' To '.$to_date;
	}
	//$pid = rangePayrollPeriod($mfrom_date, $mto_date);
	$q = "select 
			paymast_id,
			elast,
			efirst,
			phicid,
			department_id,
			fixed_sss,
			fixed_phic,
			fixed_wtax,
			fixed_pagibig,
			fixed_ssse,
			fixed_phice,
			fixed_wtaxe,
			fixed_pagibige
		from 
			paymast
		where
			phicw ='1' ";
	if ($branch_id == '')
	{
		$subtitle .= " ALL BRANCHES ";
	}
	else
	{
		$subtitle .= strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id)).'  BRANCH';
		$q .= " and paymast.branch_id = '$branch_id'";
	}
	if ($department_id != '')
	{
		$q .= " and department_id = '$department_id'";
	}
	
	if ($rank== '')
	{
		$subtitle .= ' ALL RANKS';
	}
	elseif ($rank == 'R')
	{
		$q .= " and rank = 'R'";
		$subtitle .= ' RANK  AND FILE';
	}
	elseif ($rank == 'S')
	{
		$q .= " and rank = 'S'";
		$subtitle .= ' SUPERVISORS';
	}

	if ($show != 'D') $subtitle .= ' - '.$show;			
	$q .= " order by department_id, upper(elast), efirst";
	
	$qr = @pg_query($q) or message(pg_errormessage().$q);

	if ($p1 == 'Print Draft') $header .= "<small3>";

	$header .= "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
	$header .= center('PHIC CONTRIBUTION SUMMARY',120)."\n";
	$header .=  center($subtitle,120)."\n";
	$header .= center('Payroll Period :'.$based,120)."\n";
	$header .= center('Printed: '.date('m/d/Y g:ia'),120)."\n\n";
	
	if ($show == 'V')
	{
/*		$header .= "---- ---------------------------------------- --------------- -------------- ------------ ------------ -------------- ---------- ----------\n";
		$header .= "      EMPLOYEE NAME                              PHIC NO.     GROSS INCOME    EMPLOYEE     EMPLOYER        TOTAL       Deducted   Variance\n";
		$header .= "---- ---------------------------------------- --------------- -------------- ------------ ------------ -------------- ---------- ----------\n";*/
		$header .= "---- ---------------------------------------- --------------- ------------ ------------ -------------- ---------- ----------\n";
		$header .= "      EMPLOYEE NAME                              PHIC NO.      EMPLOYEE     EMPLOYER        TOTAL       Deducted   Variance\n";
		$header .= "---- ---------------------------------------- --------------- ------------ ------------ -------------- ---------- ----------\n";
		
	}
	else
	{
/*		$header .= "---- ---------------------------------------- --------------- -------------- ------------ ------------  --------------\n";
		$header .= "      EMPLOYEE NAME                              PHIC NO.      GROSS INCOME    EMPLOYEE     EMPLOYER         TOTAL      \n";
		$header .= "---- ---------------------------------------- --------------- -------------- ------------ ------------  --------------\n";*/
		$header .= "---- ---------------------------------------- --------------- ------------ ------------  --------------\n";
		$header .= "      EMPLOYEE NAME                              PHIC NO.       EMPLOYEE     EMPLOYER         TOTAL      \n";
		$header .= "---- ---------------------------------------- --------------- ------------ ------------  --------------\n";
		
	}	
	$details = $details1 = '';
	$details1 = $header;
	$ctr = $total_amount = 0;
	$lc=8;
	$mbranch_id = '';
	$asum = sumPeriodic( $mfrom_date, $mto_date,'');

	while ($r = @pg_fetch_object($qr))
	{

		$fnd = 0;
		reset($asum);
		foreach ($asum as $temp)
		{
			if ($temp['paymast_id'] == $r->paymast_id)
			{
				$phic_basis = $temp['accu_phicbasis'];
				$total_phic = $temp['accu_phic'];
				$accu_phic = $temp['accu_phic'];
				$fnd =1;
				break;
			}

		}
		if ($fnd == 0 ) continue;

		if ($phic_basis == '') $basis=0;
		$q = "select * from phictable where income_from <= '$phic_basis' order by income_to desc offset 0 limit 1";
		$rr = fetch_object($q);
			
		$variance = $rr->employee - $total_phic;
		if ($show != 'D')
		{
			$total_phic = $rr->employee;
		}		
		if ($total_phic*1 == '0') continue;

		if ($r->fixed_phice*1 != '0')
		{
			$employer = $r->fixed_phice;
		}
		else
		{
			$employer = $rr->employer;
		}
		
		$ctr++;

		if ($mdepartment_id != $r->department_id )
		{
			$details .= "\nDEPARTMENT : ".strtoupper(@lookUpTableReturnValue('x','department','department_id','department',$r->department_id))."\n";
			$mdepartment_id = $r->department_id;
		}
		$details .= adjustRight($ctr,3).'. '.
					adjustSize($r->elast.', '.$r->efirst,40).' '.
					adjustSize($r->phicid,15).' '.
//					adjustRight(number_format($phic_basis,2),14).' '.
					adjustRight(number_format($total_phic,2),12).' '.
					adjustRight(number_format($employer,2),12).' '.
					adjustRight(number_format($total_phic+$employer,2),14).' ';
		if ($show == 'V')
		{
			$details .=	adjustRight(number_format($accu_phic,2),9).' '.
					adjustRight(number_format($variance,2),9)."\n";
		}		
		else
		{
			$details .= "\n";
		}
		$lc++;
		
		$total_total_phic += $total_phic;
		$total_income += $phic_basis;
		$total_employer += $employer;
		$total_ecc += $rr->ecc;
		
		if ($lc>55 && $p1 == 'Print Draft')
		{
			doPrint($header.$details."<eject>");
			$lc=8;
			$details1 .= $header.$details;
			$details = '';
		}
	}
	$details .= "---- ---------------------------------------- --------------- ------------ ------------ --------------\n";
	$details .= space(30).' '.
					adjustSize('TOTALS: ',15).' '.
					space(15).
					adjustRight(number_format($total_total_phic,2),12).' '.
					adjustRight(number_format($total_employer,2),12).' '.
					adjustRight(number_format($total_total_phic+$total_ecc+$total_employer,2),14)."\n";
	$details .= "---- ---------------------------------------- --------------- ------------ ------------ --------------\n";

	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
}
?>	
<br />
<form name="f1" id="f1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1">
      <tr> 
        <td height="27" colspan="6" background="../graphics/table_horizontal.PNG">&nbsp; 
          <font color="#EFEFEF" size="2" face="Verdana, Arial, Helvetica, sans-serif">:: <strong>
          PHIC Report </strong></font></td>
      </tr>
      <tr> 
        <td width="9%" height="46" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          From<br>
          <strong> </strong> 
          <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="10" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" >
          <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"> 
          <strong> </strong> </font></td>
        <td width="9%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">To<br>
          <strong> </strong> 
          <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="10" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" >
          <strong> </strong> <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
          </font></td>
        <td width="18%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch 
          <br>
          <select name = "branch_id" style="width:150">
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
        </font></td>
        <td width="17%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Department</font><br> 
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
          </select> </td>
        <td width="9%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Rank<br>
          <?= lookUpAssoc('rank',array('All Ranks'=>'','Rank & File'=>'R', 'Supervisor'=>'S'), $rank);?>
          </font></td>
        <td width="38%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          Show<br>
          <?= lookUpAssoc('show',array('Deducted'=>'D','As Per Table'=>'T', 'Variance'=>'V'), $show);?>
          <input name="p1" type="submit" id="p1" value="Go">
          </font></td>
      </tr>
      <tr> 
        <td height="27" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td nowrap bgcolor="#DADADA"><font color="#666666" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17"> PHIC 
                Report Preview</strong></font></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="6" valign="top" bgcolor="#FFFFFF"> <textarea name="print_area" cols="115" rows="20"  wrap="off" readonly><?= $details1;?></textarea> 
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
