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
if (!chkRights2('excessamount','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Excess/Change ]...");
	exit;
}

if ($from_date=='') $from_date=date('m/d/Y');
if ($to_date=='') $to_date=date('m/d/Y');
if ($year == '') $year = date("Y");

if (($p1=='Go' || $p1=='Print Draft') && $year=='')
{
	message('Please provide year...');
}
elseif ($p1=='Go' || $p1=='Print Draft')
{
//message("Under construction");
//exit;
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	$aM = array("January","February","March","April","May","June","July","August","September","October","November",
					"December","13th Month");


	if ($p1 == 'Print Draft') 
	{
		$header = "<small3>";
	}
	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],60)."\n";
	$header .= center('EXCESS RELEASE FOR SPECIFIC MONTH UNDER PERIOD COVERED' ,60)."\n";
	$header .= center($from_date.' To '.$to_date,60)."\n\n";
	$header .= "            -----------  --------  --------------\n";
	$header .= "             Month        Year          Amount   \n";
	$header .= "            -----------  --------  --------------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	$mbranch_id = $branch_id;
	if ($starting_month==0) $moend = 12;
	else $moend = $starting_month;
	$total = $ttotal =0;
	$atotal = array();

	$qb = "select * from branch where enable ";
	if ($branch_id != 0 and $branch_id !='') $qb .= " and branch_id = '$branch_id'";
	$qb .= "order by branch";
	
	$qbr = pg_query($qb) or message(pg_errormessage());

	while ($rb = pg_fetch_assoc($qbr))
	{
		$mbranch_id = $rb[branch_id];
		$branch = $rb[branch];
		
		$q = "select 
					*
			from 
				wexcess,
				account
			where 
				account.account_id=wexcess.account_id and 
				wexcess.status != 'C' ";
			if ($from_date != '')	
			{
				$q .= " and wexcess.date>='$mfrom_date' ";
				$cutdate = $from_date;
			}	
			else
			{
				$cyr = $year-1;
				$cutdate = $cyr.'-01-01';
				$q .= " and wexcess.date>='$cutdate' ";			
			}	 
			if ($to_date != '')	
				$q .= " and wexcess.date<='$mto_date' "; 
				 
			if ($mbranch_id != '')
			{
				$q .= " and account.branch_id = '$mbranch_id'";
			}
			$q .= " order by account.branch_id, account, date";
			//	payment_header.status!='C' 
		
		$qr = pg_query($q) or message(pg_errormessage());
		
		while ($r = pg_fetch_assoc($qr))
		{
			$myear = substr($r['date'],0,4);
			if ($r[starting_month] < substr($cutdate,0,2)) $myear++;
			$year_flag = $months = 0;
			if ($year > substr($cutdate,6,4))
				$smonth=1;
			else	
				$smonth = substr($cutdate,0,2);

			if ($starting_month=='0')
			{
				for ($i = 1; $i <= 13; $i++)
				{
					
					$mo = ($i - $r[starting_month])+1;
					if ($mo > 0)
					{
						$month = $mo; 
					}
					else
					{
						$month = 13 + $mo;
						$myear++;
					}
//exit;
					if ($r[starting_month] < $smonth and $year==$myear) continue;
					if ($r[starting_month] < substr($cutdate,0,2) and $year==$myear) continue;

					if ($year == $myear)
					{			
						$smon = 'month'.$month;
						$atotal[$i] += $r[$smon];
//echo 'starting month '.$r[starting_month].'  smonth '.$smonth.' myear '.$myear.'  year '.$year.'  smon '.$smon."<br>";
					}	
					$myear = substr($r['date'],0,4);
					if ($r[starting_month] < substr($cutdate,0,2)) $myear++;

				}
			} else
			{
				$mo = ($starting_month - $r[starting_month])+1;
				if ($mo > 0)
				{
					$month = $mo;
					$myear = $year;
				}
				else
				{
					$month = 13 - $mo;
					$myear++;
				}			
				
				$smon = 'month'.$month;
				$total += $r[$smon];
			}
		}
		if ($starting_month !='0')
		{ 	
			$details .= '             '.adjustSize($aM[$starting_month-1],10).'    '.$year.'   '.
						adjustRight(number_format($total,2),14)."\n";
						
		} else
		{
			$dets = '';
			for ($i = 1; $i <= 13; $i++)
			{
				if ($atotal[$i] == 0) continue;
				$dets .= '             '.adjustSize($aM[$i-1],10).'    '.$year.'   '.
							adjustRight(number_format($atotal[$i],2),14)."\n";
				$total += $atotal[$i];			
				$atotal[$i] = 0;
			}
			if ($total > 0)
			{
				$details .= '            BRANCH '.$branch."\n";
				$details .= $dets;
				$details .= space(34)."--------------\n";
				$details .= space(34).adjustRight(number_format($total,2),14)."\n";
				if ($branch_id!='' and $banch_id!='0')
					$details .= space(34)."==============\n";
				else	
					$details .= space(34)."--------------\n\n";
				$ttotal += $total;
				$total= 0;
			}
		}
	}
	if ($branch_id=='')
	{
		$details .= space(34)."--------------\n";
		$details .= space(34).adjustRight(number_format($ttotal,2),14)."\n";
		$details .= space(34)."==============\n";
	}
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	
}	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="65%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Summary 
          of Pension Excess/Change Refund and Advances</b></font></td>
        <td width="33%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="1%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="125" height="24" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month </font></td>
              <td width="679" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <select name="starting_month" id="starting_month" style="width:120px"  onkeypress="if(event.keyCode == 13){document.getElementById('month1').focus();return false;}" onblur="vMo()">
                  <option value="0" <?= ($starting_month == '0' ? 'selected' : '');?>>All</option>
                  <option value="1" <?= ($starting_month == '1' ? 'selected' : '');?>>January</option>
                  <option value="2" <?= ($starting_month == '2' ? 'selected' : '');?>>February</option>
                  <option value="3" <?= ($starting_month == '3' ? 'selected' : '');?>>March</option>
                  <option value="4" <?= ($starting_month == '4' ? 'selected' : '');?>>April</option>
                  <option value="5" <?= ($starting_month == '5' ? 'selected' : '');?>>May</option>
                  <option value="6" <?= ($starting_month == '6' ? 'selected' : '');?>>June</option>
                  <option value="7" <?= ($starting_month == '7' ? 'selected' : '');?>>July</option>
                  <option value="8" <?= ($starting_month == '8' ? 'selected' : '');?>>August</option>
                  <option value="9" <?= ($starting_month == '9' ? 'selected' : '');?>>September</option>
                  <option value="10" <?= ($starting_month == '10' ? 'selected' : '');?>>October</option>
                  <option value="11" <?= ($starting_month == '11' ? 'selected' : '');?>>November</option>
                  <option value="12" <?= ($starting_month == '12' ? 'selected' : '');?>>December</option>
                  <option value="13" <?= ($starting_month == '13' ? 'selected' : '');?>>13th Month</option>
                </select>
                Year 
                <input name="year" type="text" id="year"  onkeypress="if(event.keyCode ==13)
				{document.getElementById('from_date').focus();return false;}" value="<?= $year;?>" size="4" />
              </font></td>
			</tr>  
            <tr> 
              <td width="125" height="24" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date Released </font></td>
              <td width="679" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                Date 
                From
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"></font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                To 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onblur="IsValidDate(this,'MM/dd/yyyy')" onkeyup="setDate(this,'MM/dd/yyyy','en')" />
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')" /> </font></td>
            </tr>
            <tr> 
              <td height="22" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <select name = "branch_id">

                  <?
				$q = "select * from branch where enable ";
				
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
	                  <option value=''>Select Branch</option>
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
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
              </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="2"><textarea name="print_area" cols="120" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
