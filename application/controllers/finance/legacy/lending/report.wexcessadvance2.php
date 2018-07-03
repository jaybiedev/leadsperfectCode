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
if ($from_date=='') $from_date=date('m/d/Y');
if ($to_date=='') $to_date=date('m/d/Y');

if (($p1=='Go' || $p1=='Print Draft') && ($from_date == ''|| $to_date==''))
{
	message('Please provide date coverage...');
}
elseif ($p1=='Go' || $p1=='Print Draft')
{
	if ($from_month==1 or $from_month==13) $start_mo=12;
	else $start_mo=$from_month-1;
	
	$startdate=mdy2ymd('$start_mo'.'01'.'$from_year');
	$end_month = $from_month+7;
	
	if ($end_month > 12 ) $end_year = $from_year+1;
	else $end_year=$from_year;

//			account.account_id=wexcess.account_id and
	
	$q = "select 
				*
		from 
			wexcess,account
		where 
			account.account_id=wexcess.account_id and
			starting_month = '$from_month' and
			(substring(date from 1 for 4) >= '$from_year' and
			 substring(date from 1 for 4) <= '$end_year') and 
			wexcess.status != 'C'  and
			type='C'";
	
		if ($branch_id != '')
		{
			$q .= " and account.branch_id = '$branch_id'";
		}

	$qr = @pg_query($q) or message(pg_errormessage().$q);

	if ($p1 == 'Print Draft') 
	{
		$header = "<small3>";
	}
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	$mbranch_id = '';
	
	$aRep = null;
	$aRep = array();

	$aRep[0] = array('month'=>'Month');
	$aRep[1] = array('month'=>'January');
	$aRep[2] = array('month'=>'February');
	$aRep[3] = array('month'=>'March');
	$aRep[4] = array('month'=>'April');
	$aRep[5] = array('month'=>'May');
	$aRep[6] = array('month'=>'June');
	$aRep[7] = array('month'=>'July');
	$aRep[8] = array('month'=>'August');
	$aRep[9] = array('month'=>'September');
	$aRep[10] = array('month'=>'October');
	$aRep[11] = array('month'=>'November');
	$aRep[12] = array('month'=>'December');
	$aRep[13] = array('month'=>'13th Month');

	$aMon[0] = 'Month';
	$aMon[1] = 'January';
	$aMon[2] = 'February';
	$aMon[3] = 'March';
	$aMon[4] = 'April';
	$aMon[5] = 'May';
	$aMon[6] = 'June';
	$aMon[7] = 'July';
	$aMon[8] = 'August';
	$aMon[9] = 'September';
	$aMon[10] = 'October';
	$aMon[11] = 'November';
	$aMon[12] = 'December';
	$aMon[13] = '13th Month';

	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('EXCESS/CHANGE RELEASED BASED ON STARTING MONTH' ,80)."\n";
	$header .= center($aMon[$from_month].' '.$from_year,80)."\n\n";
	$header .= "  ---------------------------- ------------- ------------ ------------\n";
	$header .= "   Month                           Gross        Interest   NetAmount\n";
	$header .= "  ---------------------------- ------------- ------------ ------------\n";

	$details = $details1 ;

	while ($r = @pg_fetch_assoc($qr))
	{
		$cc=0;
		for ($c=1;$c<9;$c++)
		{

			$field = 'month'.$c;

			if (($c + $from_month - 1) !=13 ) $cc++;
			
			if ($r[$field] > 0)
			{
			
				$gross = $r[$field];
				$interest = ($r['rate']/100 ) * $cc * $r[$field];
				$net = $r[$field] - $interest;
				
				$temp = $aRep[$c];
				$temp['month'] = cmonth($month).' '.$year;
				$temp['gross'] += $gross;
				$temp['net'] += $net;
				$temp['interest'] += $interest;
				$aRep[$c] = $temp;
			}
		}
	}
	$mon = $from_month-1;
	$year = $from_year;
	foreach ($aRep as $temp)
	{
		if ($mon == 14)
		{
			 $mon =1;
			 $year++;
		}

		$temp['month'] = $aMon[$mon].' '.$year;
		$mon++;
		if ($temp['gross'] > 0)
		{
			$details .= space(5).
						adjustSize($temp['month'],25).' '.
						adjustRight(number_format($temp['gross'],2),12).' '.
						adjustRight(number_format($temp['interest'],2),12).' '.
						adjustRight(number_format($temp['net'],2),12)."\n";
						
			$total_gross += $temp['gross'];
			$total_interest += $temp['interest'];
			$total_net += $temp['net'];
		}
	}

	$details .= "  ---------------------------- ------------- ------------ ------------\n";
	$details .= space(6).adjustSize('TOTAL AMOUNT ->',25).
					adjustRight(number_format($total_gross,2),12).' '.
					adjustRight(number_format($total_interest,2),12).' '.
					adjustRight(number_format($total_net,2),12)."\n";
	$details .= "  ---------------------------- ------------- ------------ ------------\n";
	
	
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
          of Pension Excess/Change Advanced</b></font></td>
        <td width="33%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="1%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Month </font></td>
              <td width="724" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <select name="from_month" id="from_month"  tabindex="<?= array_search('from_month',$fields);?>" style="width:87px"  onkeypress="if(event.keyCode==13) {document.getElementById('year').focus();return false;}">
                  <option value="1" <?=($from_month=='1' ? 'selected' : '');?>>January</option>
                  <option value="2" <?=($from_month=='2' ? 'selected' : '');?>>February</option>
                  <option value="3" <?=($from_month=='3' ? 'selected' : '');?>>March</option>
                  <option value="4" <?=($from_month=='4' ? 'selected' : '');?>>April</option>
                  <option value="5" <?=($from_month=='5' ? 'selected' : '');?>>May</option>
                  <option value="6" <?=($from_month=='6' ? 'selected' : '');?>>June</option>
                  <option value="7" <?=($from_month=='7' ? 'selected' : '');?>>July</option>
                  <option value="8" <?=($from_month=='8' ? 'selected' : '');?>>August</option>
                  <option value="9" <?=($from_month=='9' ? 'selected' : '');?>>September</option>
                  <option value="10" <?=($from_month=='10' ? 'selected' : '');?>>October</option>
                  <option value="11" <?=($from_month=='11' ? 'selected' : '');?>>November</option>
                  <option value="12" <?=($from_month=='12' ? 'selected' : '');?>>December</option>
                  <option value="13" <?=($from_month=='13' ? 'selected' : '');?>>13th Mo</option>
                </select>
                Year: </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2">
                <input name="from_year" type="text" id="from_year" value="<?=$from_year?>" size="10" />
                </font></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">(Based on Starting Month) </font> </td>
            </tr>
            <tr> 
              <td height="22" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                 
                
                Branch 
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
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                <input name="p1" type="button" id="p1" value="Print"  onclick="printIframe(print_area)">
              </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="2"><textarea name="print_area" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
