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
if (!chkRights2('bankrecon','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Bank Recon ]...");
	exit;
}

if ($from_date == '') $from_date = date('m/d/Y');
if ($to_date == '') $to_date = date('m/d/Y');


if ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	
	$q = "select *
		from 
				bankrecon
		where 
			udate >= '$mfrom_date' and  
			udate <= '$mto_date' and  
			flag = 'R' and enable";
	if ($branch_id != '')
	{
		$q .= " and branch_id='$branch_id' ";
	}
	if ($bank_id != '')
	{
		$q .= " and bank_id='$bank_id' ";
	}
	$q .= " order by branch_id, bank_id, bankrecon_id ";

	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$header = "\n\n";
	$hdname = explode('-',$SYSCONF['BUSINESS_NAME']);
	$hdr = $hdname[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	$header .= center($hdr,80)."\n";
	$header .= center('BANK TRANSACTIONS FOR  '.$from_date.' To '.$to_date,80)."\n";
	$header .= center('Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$header .= "---------- ----------------------------------- - ---------- ------------ ---------- ---------- -------------\n";
	$header .= "Date Marked\n";  
	$header .= "As Cleared      Description                      Reference   Check       CheckDate  Trans.Date     Credit   \n";
	$header .= "---------- ----------------------------------- - ---------- ------------ ---------- ---------- -------------\n";
	$details = $details1 = '';
	//$details1 = $header;

	$lc = 10;
	$total_credit = $total_debit =0;
	$ctr=0;
	$stotal_credit=$stotal_debit=0;
	$m=0;
	$mbank_id ='';
	while ($r = @pg_fetch_object($qr))
	{
		if ($mbank_id != $r->bank_id)
		{
			if ($mbank_id != '') 
			{
				$m=1;
				$details .= space(21).adjustSize('***** SUB TOTALS *****',35).space(39).
							adjustRight(number_format($stotal_creditR,2),13)."\n";

				$details .= "\n";
				$lc++;
			}
			$details .= "*** BANK ".strtoupper(lookUpTableReturnValue('x','bank','bank_id','bank',$r->bank_id))."\n";
			$lc++;
			$mbank_id = $r->bank_id;
			$stotal_creditR = $stotal_creditU = $stotal_debit=0;
		}
		$details .= adjustSize(ymd2mdy($r->udate),10).' '.
					adjustSize($r->descr,35).' '.
					adjustSize($r->type,1).' '.
					adjustSize($r->reference,10).' '.
					adjustSize($r->mcheck,12).' '.
					adjustSize($r->checkdate,10).' '.
					adjustSize(ymd2mdy($r->date),10).' ';

			$stotal_creditR += $r->credit;
			$total_creditR += $r->credit;
			$details .= adjustRight(number_format2($r->credit,2),13)."\n";
		
		$lc++;
		if ($lc > 55)
		{
			if ($p1=='Print Draft')
			{
				doPrint($header.$details."<eject>");
			}
			$details1 .= $header.$details;
			$lc = 10;
			$details = '';
		}	
		$total_debit += $r->debit;
		$stotal_debit += $r->debit;
		$balance = $r->balance;	
	}
	if ($m == 1)
	{
				$m=1;
				$details .= space(21).adjustSize('***** SUB TOTALS *****',35).space(39).
							adjustRight(number_format($stotal_creditR,2),13)."\n";
//				$details .= space(8)."**** Bank Balance : ".number_format($balance,2)."\n";

				$details .= "\n";
	
	}
	$details .= "---------- ----------------------------------- - ---------- ------------ ---------- ---------- -------------\n";
	$details .= space(21).adjustSize('***** TOTALS *****',35).space(39).
				adjustRight(number_format($total_creditR,2),13)."\n";
				
	$details .= "---------- ----------------------------------- - ---------- ------------ ---------- ---------- -------------\n";

	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details);
	}	
	
}
if ($date == '') $date=date('m/d/Y');	
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Cleared Checks  
          for the Period </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <select name = "branch_id">
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
                  <option value=''>Select Branch</option>
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
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">From 
                Date</font></td>
              <td width="501" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$from_date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                To Date 
                <input name="to_date" type="text" id="to_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$to_date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                </font></td>
              <td width="223" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p132" type="submit" id="p132" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
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
