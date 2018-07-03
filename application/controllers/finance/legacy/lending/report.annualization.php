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

	$pid = rangePayrollPeriod($mfrom_date, $mto_date);
	$q = "select 
			paymast_id,
			elast,
			efirst,
			sssid,
			idnum,
			tin,
			taxcode,
			ratem,
			adwr,
			date_employ,
			department_id
			
		from 
			paymast
		where
			enable='Y'";
	if ($branch_id == '')
	{
		$subtitle .= " ALL BRANCHES ";
	}
	else
	{
		$subtitle .= strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id)).'  BRANCH';
		$q .= " and paymast.branch_id = '$branch_id'";
	}

	if ($show != 'D') $subtitle .= ' - '.$show;			
	$q .= " order by  upper(elast), efirst";
	
	$qr = @pg_query($q) or message(pg_errormessage().$q);

	if ($p1 == 'Print Draft') $header .= "<reset>";

	$header .= "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],200)."\n";
	$header .= center('ANNUALIZATION REPORT SUMMARY',200)."\n";
	$header .=  center($subtitle,200)."\n";
	$header .= center('Payroll Period :'.$based,200)."\n";
	$header .= center('Printed: '.date('m/d/Y g:ia'),200)."\n\n";
	$header .= "---- ---------------------------------------- ----------- ------ --- ------------ ------ ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ----------\n";
	$header .= "                                                           TAX           Monthly   Daily    Date       Basic   ";
	$header .= "   Add'l.    Commis-     Night    Sal. Inc.   Regular   Rest Day   Rest Day    Regular     Legal   ";
	$header .= "Legal Hly     Special  Spcl.H OT   RD & LH  RD & LH OT   RD & SH  RD & SH OT  Separat'n            ";
	$header .= "              Under                 Discre-    Gross                                       S S S   ";
	$header .= "   PHIC    Pag-Ibig   Ttl Non-Tx     13th     Mid-Year                        Maternity  Paternity ";
	$header .= "  Early                Transpo     Ttl 13th      Rice    Vacation     Sick      Uniform    Medical ";
	$header .= " Med.Allow   Laundry   Anvrsary  Christmas                Meal    De Minimis  Management Ttl NonTax";
	$header .= "    Cash     Company  Emergency                                      Metro        PI               ";
	$header .= "  Ttl Othr     Tax      N E T   \n";
	
	$header .= "      EMPLOYEE NAME                              T I N #  Status DEP      Rate      Rate    Hired       Pay    ";
	$header .= "   Basic      sion       Diff'l    Diff'l    Overtime   Overtime  OT aftr 8h   Holiday    Holiday  ";
	$header .= "Aftr 8hrs  Holiday OT   aftr 8h   Overtime  Over 8hrs  Overtime  After 8hrs     Pay      Absences  ";
	$header .= "Tardiness     Time    Adjustment    pancy       Pay      S S S    Philhealth  Pag-Ibig   Variance  ";
	$header .= " Variance   Variance  Contribtn     Month      Bonus    Incentive   C O L A     Leave      Leave   ";
	$header .= "   Bird     Allowance    E R A    & Others   Allowance    Leave      Leave    Allowance  Allowance ";
	$header .= "for Dep's.  Allowance    Gift        Gift      Award     Benefit     Total    Allowance   Income   ";
	$header .= "  Advance     Loan      Loan       Others   Discrepancy  Jesters     Click    Variance  Adjustment ";
	$header .= " Deduction  Withheld     P A Y   \n";

	$header .= "---- ---------------------------------------- ----------- ------ --- ------------ ------ ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$header .= "---------- ---------- ----------\n";
	$details = $details1 = '';
	$details1 = $header;
	$ctr = $total_amount = 0;
	$lc=8;
	$asum = sumAnnual( $mfrom_date, $mto_date,'');

	while ($r = @pg_fetch_object($qr))
	{

		$fnd = 0;
		reset($asum);
		foreach ($asum as $temp)
		{
			if ($temp['paymast_id'] == $r->paymast_id)
			{
				$sss_basis = $temp['accu_sssbasis'];
				$total_sss = $temp['accu_sss'];
				$accu_sss = $temp['accu_sss'];
				$fnd =1;
				
				break;
			}

		}
		if ($fnd == 0) continue;

		$ctr++;
//		$total_basic = $sss_basis;
		$thirteen = $total_basic / 12;
		$grosspay = $temp['accu_basic']+$temp['IA2']+$temp['IA3']+$temp['IA4']+
					$temp['IB']+$temp['ID1']+$temp['ID2']+$temp['ID3']+
					$temp['ID13']+$temp['ID14']+$temp['ID12']+$temp['ID8']+
					$temp['ID9']+$temp['ID4']+$temp['ID5']+$temp['ID10']+
					$temp['ID11']+$temp['ID6']+$temp['ID7']+$temp['IS1']+
					$temp['DA1']+$temp['DA2']+$temp['DA3']+$temp['IE4']+
					$temp['IG1'];
		$nontaxcont = $temp['DE1']+$temp['DE2']+$temp['DE3']-($temp['accu_sss']+$temp['accu_phic']+$temp['accu_pagibig']);
		$th13other  = $temp['IF1']+$temp['IF2']+$temp['IB1']+$temp['IC6']+$temp['IC3']+$temp['IC5']+$temp['IA5']+$temp['IC4']+$temp['IA16'];
		$benefits	= $temp['IA6']+$temp['IC2']+$temp['IC1']+$temp['IA7']+$temp['IA8']+$temp['IA9']+$temp['IA10']+
						+$temp['IA11']+$temp['IA12']+$temp['IA13']+$temp['IA15'];
		$dedother	= $temp['DL1']+$temp['DL4']+$temp['DL2']+$temp['DL3']+$temp['DL5']+$temp['DL6']+$temp['DL7']
						+$temp['DZ1']+$temp['DZ2'];

					
		if ($r->ratem==0) $ratem= $r->adwr * 30;
		else $ratem=$r->ratem;			
		$details .= adjustRight($ctr,3).'. '.
					adjustSize($r->elast.', '.$r->efirst,40).' '.
					adjustSize($r->tin,11).' '.
					adjustSize($r->taxcode,6).'     '.
					adjustRight(number_format($ratem,2),10).' '.
					adjustRight(number_format($r->adwr,2),8).' '.
					adjustSize(ymd2mdy($r->date_employ),10).' '.
					adjustRight(number_format($temp['accu_basic'],2),10).' '.
					adjustRight(number_format($temp['IA2'],2),10).' '.
					adjustRight(number_format($temp['IA3'],2),10).' '.
					adjustRight(number_format($temp['IA4'],2),10).' '.
					adjustRight(number_format($temp['IB'],2),10).' '.
					adjustRight(number_format($temp['ID1'],2),10).' '.
					adjustRight(number_format($temp['ID2'],2),10).' '.
					adjustRight(number_format($temp['ID3'],2),10).' '.
					adjustRight(number_format($temp['ID13']+$temp['ID14'],2),10).' '.
					adjustRight(number_format($temp['ID12']+$temp['ID8'],2),10).' '.
					adjustRight(number_format($temp['ID9'],2),10).' '.
					adjustRight(number_format($temp['ID4'],2),10).' '.
					adjustRight(number_format($temp['ID5'],2),10).' '.
					adjustRight(number_format($temp['ID10'],2),10).' '.
					adjustRight(number_format($temp['ID11'],2),10).' '.
					adjustRight(number_format($temp['ID6'],2),10).' '.
					adjustRight(number_format($temp['ID7'],2),10).' '.
					adjustRight(number_format($temp['IS1'],2),10).' '.
					adjustRight(number_format($temp['DA1']*-1,2),10).' '.
					adjustRight(number_format($temp['DA2']*-1,2),10).' '.
					adjustRight(number_format($temp['DA3']*-1,2),10).' '.
					adjustRight(number_format($temp['IE4'],2),10).' '.
					adjustRight(number_format($temp['IG1'],2),10).' '.
					adjustRight(number_format($grosspay,2),10).' '.
					adjustRight(number_format($temp['accu_sss'],2),10).' '.
					adjustRight(number_format($temp['accu_phic'],2),10).' '.
					adjustRight(number_format($temp['accu_pagibig'],2),10).' '.
					adjustRight(number_format($temp['DE1']*-1,2),10).' '.
					adjustRight(number_format($temp['DE3']*-1,2),10).' '.
					adjustRight(number_format($temp['DE2']*-1,2),10).' '.
					adjustRight(number_format($nontaxcont*-1,2),10).' '.
					adjustRight(number_format($temp['IF1'],2),10).' '.
					adjustRight(number_format($temp['IF2'],2),10).' '.										
					adjustRight(number_format($temp['IB1'],2),10).' '.
					adjustRight(number_format($temp['IC6'],2),10).' '.
					adjustRight(number_format($temp['IC3'],2),10).' '.
					adjustRight(number_format($temp['IC5'],2),10).' '.
					adjustRight(number_format($temp['IA5'],2),10).' '.
					adjustRight(number_format($temp['IC4'],2),10).' '.
					adjustRight(number_format($temp['IA16'],2),10).' '.
					adjustRight(number_format($th13other,2),10).' '.
					adjustRight(number_format($temp['IA6'],2),10).' '.
					adjustRight(number_format($temp['IC2'],2),10).' '.
					adjustRight(number_format($temp['IC1'],2),10).' '.
					adjustRight(number_format($temp['IA7'],2),10).' '.
					adjustRight(number_format($temp['IA8'],2),10).' '.
					adjustRight(number_format($temp['IA9'],2),10).' '.
					adjustRight(number_format($temp['IA10'],2),10).' '.
					adjustRight(number_format($temp['IA11'],2),10).' '.
					adjustRight(number_format($temp['IA12'],2),10).' '.
					adjustRight(number_format($temp['IA15'],2),10).' '.
					adjustRight(number_format($temp['IA13'],2),10).' '.
					adjustRight(number_format($benefits,2),10).' '.
					adjustRight(number_format($temp['IA14'],2),10).' '.
					adjustRight(number_format($th13other+$benefits+$temp['IA14'],2),10).' '.
					adjustRight(number_format($temp['DL1']*-1,2),10).' '.
					adjustRight(number_format($temp['DL4']*-1,2),10).' '.
					adjustRight(number_format($temp['DL2']*-1,2),10).' '.
					adjustRight(number_format($temp['DL3']*-1,2),10).' '.
					adjustRight(number_format($temp['DL5']*-1,2),10).' '.
					adjustRight(number_format($temp['DL6']*-1,2),10).' '.
					adjustRight(number_format($temp['DL7']*-1,2),10).' '.
					adjustRight(number_format($temp['DZ1']*-1,2),10).' '.
					adjustRight(number_format($temp['DZ2']*-1,2),10).' '.
					adjustRight(number_format($dedother*-1,2),10).' '.
					adjustRight(number_format($temp['accu_tax'],2),10).' '.
					adjustRight(number_format($grosspay+$th13other+$benefits+$nontaxcont+$dedother+$temp['IA14']
							-$temp['accu_tax'],2),10).' ';
										
			$details .= "\n";
		$lc++;
		
		$total_thirteen += $thirteen;
		$total_total_basic += $total_basic;
		
		if ($lc>55 && $p1 == 'Print Draft')
		{
			doPrint($header.$details."<eject>");
			$lc=8;
			$details1 .= $header.$details;
			$details = '';
		}
	}
	$details .= "---- ---------------------------------------- ----------- ------ --- ------------ ------ ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ---------- ";
	$details .= "---------- ---------- \n";

	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
		
	}	
}
?>	
<br>
<form name="f1" id="f1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1">
      <tr> 
        <td height="27" colspan="6" background="../graphics/table_horizontal.PNG">&nbsp; 
          <font color="#F3F7F9" size="2" face="Verdana, Arial, Helvetica, sans-serif">.:: 
          <strong>Annualization Report </strong></font></td>
      </tr>
      <tr> 
        <td width="9%" height="27" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          From<br>
          <strong> </strong> 
          <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="10">
          <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"> 
          <strong> </strong> </font></td>
        <td width="9%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">To<br>
          <strong> </strong> 
          <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="10">
          <strong> </strong> <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
          </font></td>
        <td width="17%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch 
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
        <td width="46%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <br>
          <input name="p1" type="submit" id="p14" value="Go">
          </font></td>
      </tr>
      <tr> 
        <td height="27" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td nowrap bgcolor="#DADADA"><font color="#666666" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17"> 13TH 
                Month Report Preview</strong></font></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="6" valign="top" bgcolor="#FFFFFF"> <textarea name="print_area" cols="120" rows="20"  wrap="off" readonly><?= $details1;?></textarea> 
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
