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

if ($payroll_period_id == '') $payroll_period_id =$SYSCONF['PAYROLL_PERIOD_ID'];

if (($p1=='Go' || $p1=='Print Draft') && $payroll_period_id!= '')
{
	$payroll_period_id = $_REQUEST['payroll_period_id'];
	if ($payroll_period_id == '') $payroll_period_id =$SYSCONF['PAYROLL_PERIOD_ID'];
	
	$header = "";
	$branch = lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	$address = lookUpTableReturnValue('x','branch','branch_id','branch_address',$branch_id);
	$busname=explode('-',$SYSCONF['BUSINESS_NAME']);
	$header .= $busname[0].'-'.$branch."\n";
	$header .= $address."\n";
	
	$header .= "PAYROLL SUMMARY REPORT \n";
	$header .= "PAYROLL PERIOD: ".lookUpPayPeriodReturnValue('x',$payroll_period_id)."\n";
	$header .= "Printed : ".date('m/d/Y g:ia')."\n";
	$title = "";
	$titleln="";
	$titleln .= "---- ------- ------------------------- ---- ";
	$title .= "  #   Id No. Employee  Name            Days ";
	
	$aCols = null;
	$aCols = array();
	$mseqn = '';
	$c = 'I';
	$q = "select * from reportform where reportform='PAYSUM'  and enable='Y' order by type desc, seqn";
	$qr = @pg_query($q) or message(pg_errormessage());

	$r = null;
	$r = array();
	$r['column'] = 'days';
	$r['long'] = 4;
	$aCols[] = $r;
	while ($r = @pg_fetch_assoc($qr))
	{
		if ($r['type'] == 'D' && $c=='I')
		{
			$s = array();
			$s['title1'] = 'total_income';
			$s['column'] = 'total_income';
			$s['long'] = 12;
			$s['type'] = "I";
			$aCols[] = $s;
			$c='D';
			$titleln .= str_repeat('-',12).'|';
			$title .= center(substr('Gross Income',0,12),12).'|';
		}
		if ($mseqn != $r['seqn'])
		{
			if ($r['title1'] != '') 
				$r['column'] = $r['title1'];
			elseif ($r['title1'] != '') 
				$r['column'] = $r['title2'];
			else
				$r['column'] = count($aCols);
				
			$aCols[] = $r;
		}
		
		$mlong = $r['long'];
		if ($mlong == '') $mlong=10;
		$titleln .= str_repeat('-',$mlong).' ';
		$title .= center(substr($r['title1'],0,$mlong),$mlong).' ';
	}
	if ($c='D')
	{
		$s = array();
		$s['title1'] = 'total_deduction';
		$s['column'] = 'total_deduction';
		$s['long'] = 12;
		$s['type'] = "D";
		$aCols[] = $s;
		$titleln .= str_repeat('-',12).'|';
		$title .= center(substr('Total Deduct',0,12),12).'|';
	}
	$s = array();
	$s['title1'] = 'net_income';
	$s['column'] = 'net_income';
	$s['long'] = 12;
	$s['type'] = "N";
	$aCols[] = $s;
	$titleln .= str_repeat('-',12).' ';
	$title .= center(substr('NET INCOME',0,12),12).' ';
	
	$titleln .= "\n";
	$title .= "\n";
	$header .= $titleln;
	$header .= $title;
	$header .= $titleln;
	$q = "select * 
				from 
					payroll_header, 
					paymast
				where
					paymast.paymast_id = payroll_header.paymast_id and
					payroll_header.status!='C' and 
					payroll_header.payroll_period_id='$payroll_period_id'";
					
	if ($adwr != '')
	{
		$q .= " and adwr >= '$adwr'";
	}
	if ($department_id != '')
	{
		$q .= " and payroll_header.department_id='$department_id'";
	}
	if ($section_id != '')
	{
		$q .= " and section_id='$section_id'";
	}
	if ($paymast_id != '')
	{
		$q .= " and paymast_id='$paymast_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and payroll_header.branch_id='$branch_id'";
	}

	if ($sort == 'A' || $sort == '')
	{
		$q .= " order by paymast.elast, paymast.efirst ";
	}
	elseif ($sort == 'G')
	{
		$q .= " order by payroll_header.branch_id,payroll_header.department_id, paymast.elast, paymast.efirst ";
	}
	
	$qr = @pg_query($q) or message(pg_errormessage().$q);

	$aName = null;
	$aName = array();
	while ($r = @pg_fetch_assoc($qr))
	{
		$aName[] = $r;
	}
	
	$c=0;
	
	
	$q = "select 
					ph.paymast_id, 
					ph.total_income,
					ph.total_deduction,
					ph.total_sss,
					ph.total_tax, 
					ph.total_pagibig,
					ph.total_phic,
					ph.total_basic,
					ph.actual_days,
					pd.type,
					type_id as type_id,
					pd.payroll_detail_id,
					pd.qty  as qty,
					pd.amount as amount
					
				from 
					payroll_header as ph,  
					payroll_detail as pd
				where
					ph.payroll_header_id = pd.payroll_header_id and 
					ph.payroll_period_id='$payroll_period_id' and
					ph.status!='C' and
					pd.enable='Y'
				order by
					ph.paymast_id";

	$qr = @pg_query($q) or message1(pg_errormessage().$q);
	//echo "n ".@pg_num_rows($qr).$q;
	$mpaymast_id = '';
	$key = '';
	$dummy = null;
	$dummy = array();

	while ($r = @pg_fetch_assoc($qr))
	{
		//$c = array_search($aName, $r['paymast_id']);
		if ($mpaymast_id != $r['paymast_id'] )
		{
			if ($dummy != '')
			{
				$aName[$key] = $dummy;
			}
			$dummy = null;
			$dummy = array();
			$key=0;
			foreach ($aName as $temp)
			{
				if ($temp['paymast_id'] == $r['paymast_id'])
				{
					$dummy=$temp;
					break;
				}
				$key++;
			}
			$mpaymast_id = $r['paymast_id'];
		}
		$type = $r['type'];
		if ($r['type'] == 'I')
		{
			$qq  = "select * from income_type where income_type_id = '".$r['type_id']."'";
			$qqr = @pg_query($qq) or message1(pg_errormessage());
			$rr = @pg_fetch_assoc($qqr);
			$code_fld = 'income_code';
		}
		else
		{
			$qq  = "select * from deduction_type where deduction_type_id = '".$r['type_id']."'";
			$qqr = @pg_query($qq) or message1(pg_errormessage());
			$rr = @pg_fetch_assoc($qqr);
			$code_fld = 'deduction_code';
		}	

		foreach ($aCols as $temp)
		{
			if ($temp['type'] != $type) continue;
			$acode = explode(';', $temp['reportform_code']);	
			if (in_array($rr[$code_fld], $acode))
			{
				$dummy[$temp['column']] += $r['amount'];
			}
		}
	}

	$aName[$key] = $dummy;
	

	$mbranch_id = $mdepartment_id = '';	

	$details1 = $header;
	if ($p1 == 'Print Draft')
	{
		doPrint("<small3>");
		doPrint($header);
	}
	$lc=9;
	$pln='';

	foreach ($aName as $temp)
	{
		if ($temp['paymast_id'] == '') continue;
		
		$c++;
		if ($sort == 'G')
		{

			if ($mbranch_id != $temp['branch_id'])
			{
				if ($mbranch_id != '')
				{
					$pln.="\n";
					$lc++;
				}
				$pln .= "BRANCH : ".strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$temp['branch_id']));
				$mbranch_id = $temp['branch_id'];
				$mdepartment_id = '';
			}

			if ($mdepartment_id != $temp['department_id'])
			{
				$pln .= "\n".lookUpTableReturnValue('x','department','department_id','department',$temp['department_id'])."\n";
				$mdepartment_id = $temp['department_id'];
				$lc++;
				$lc++;
			}
		}
		$pln .= adjustRight($c,3).'. '.
						adjustSize($temp['idnum'],7).' '.
						adjustSize($temp['elast'].', '.$temp['efirst'],25).' ';

		foreach ($aCols as $temp1)
		{
		
			if ($temp1['column'] == '') continue;
			if ($temp1['long'] == '0') $long = 10;else $long=$temp1['long'];

			$amt = 0;
			if (!in_array($temp1['column'], array('days')))
			{
				if ($temp1['column'] == 'Basic')
				{
					$amt = $temp[$temp1['column']] + $temp['basic'];
				}
				elseif ($temp1['reportform_code'] == 'SSS')
				{
					$amt = $temp[$temp1['column']] + $temp['total_sss'] + $temp['total_phic'];
				}
				elseif ($temp1['reportform_code'] == 'WTAX')
				{
					$amt = $temp[$temp1['column']] + $temp['total_tax'];
				}
				elseif ($temp1['reportform_code'] == 'PAGIBIG')
				{
					$amt = $temp[$temp1['column']] + $temp['total_pagibig'];
				}
				else
				{
					$amt = $temp[$temp1['column']];
				}
				
				$pln .= adjustRight(number_format($amt,2),$long).' ';
			}
			else
			{
				$pln .= adjustRight($temp['actual_days'],$long).' ';
			}
			$aTotal[$temp1['column']] += $amt; //$temp[$temp1['column']];
		}
		$pln .= "\n";
		if (chkRights2('netpay','mview',$ADMIN['admin_id']))
		{
			$lc++;
		}
		if ($c%20 == 0 )
		{
			if ($p1 == 'Print Draft')
			{
				if (chkRights2('netpay','mview',$ADMIN['admin_id']))
				{
					doPrint($pln);
				}
			}
				//echo " c=$c $pln ";

			if (chkRights2('netpay','mview',$ADMIN['admin_id']))
			{
				$details1 .= $pln;
			}
			$pln='';
		}	

		if ($lc>55)
		{
			 if ($p1 == 'Print Draft')
			 {
				if (chkRights2('netpay','mview',$ADMIN['admin_id']))
				{
					doPrint($pln);
				}
				doPrint("<eject>");
				doPrint($header);

			}
			//			echo " cc=$c $pln ";
			if (chkRights2('netpay','mview',$ADMIN['admin_id']))
			{			
				$details1 .= "\n\n".$header.$pln;
			}
			$pln='';

			$lc=9;
		}

	}
	if (chkRights2('netpay','mview',$ADMIN['admin_id']))
	{
		$details1 .= $pln;
	}
	if ($p1 == 'Print Draft')
	{
		if (chkRights2('netpay','mview',$ADMIN['admin_id']))
		{
			doPrint($pln);
		}
		$pln='';
	}	


	$details = '';	
	if ($lc>60)
	{
		 if ($p1 == 'Print Draft')
		 {
			doPrint("<eject>");
			doPrint($header);
		}
		$details1 .= "\n\n".$header;
		$details .= $header;
		$lc=9;
	}

	$details .= $titleln;
	$details .= '  '.adjustSize($c.' Item/s      GRAND TOTAL -- >' ,40).'  ';

	foreach ($aCols as $temp1)
	{
		if ($temp1['column'] == '') continue;
		if ($temp1['long'] == '0') $long = 10;else $long=$temp1['long'];
		
		if (in_array($temp1['column'], array('days')))
		{
			continue;
		}
		elseif (in_array($temp1['column'], array('')))
		{
			$details .= adjustRight($aTotal[$temp1['column']],$long).' ';
		}
		else
		{
			$details .= adjustRight(number_format($aTotal[$temp1['column']],2),$long).' ';
		}
	}
	$details .= "\n\n";
	$details .= $titleln;
	$details1 .=  $details;

	if ($p1 == 'Print Draft')
	{
		doPrint($details."<eject>");
		$lc=8;
	}

}
?>	
<form name="form1" method="post" action="">
  <div align="center">
    <table width="90%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td><font  size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font> 
          <table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
            <tr bgcolor="#CCCCCC" background="../graphics/table_horizontal.PNG"> 
              <td colspan="4"> <font color="#F3F7F9"  size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                .::  <strong> Payroll Summary Report  </strong></font></td>
            </tr>
            <tr> 
              <td width="16%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Payroll 
                Period<br>
                </font></td>
              <td width="19%" nowrap><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <?= lookUpPayPeriod('payroll_period_id',$payroll_period_id);?>
                </strong></font> </strong></td>
              <td width="8%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Sorting</font></td>
              <td width="57%"> 
                <?= lookUpAssoc('sort',array('Alphabetical'=>'A','Grouped by Department'=>'G'),$sort);?>
              </td>
            </tr>
            <tr> 
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Department</font></td>
              <td nowrap><select name="department_id" id="department_id" style="width:150">
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
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Rank</font></td>
              <td nowrap> 
                <?= lookUpAssoc('rank',array('All Ranks'=>'','Rank & File'=>'R', 'Supervisor'=>'S'), $rank);?>
              </td>
            </tr>
            <tr> 
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
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
              <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Adwr(&gt;=) 
                </font></td>
              <td nowrap><input type="text" size="5" name="adwr" value="<?=$adwr;?>"> 
                <input name="p1" type="submit" id="p1" value="Go"></td>
            </tr>
            <tr > 
              <td colspan="4" nowrap  bgcolor="#DADADA"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Report 
                Preview | <a href="?p=setup.payrollsummary">Edit Report Format</a></font></td>
            </tr>
            <tr> 
              <td colspan="4" nowrap bgcolor="#EFEFEF"><textarea name="print_area" cols="120" rows="20"  wrap="off" readonly><?= $details1;?></textarea></td>
            </tr>
          </table>
          
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
