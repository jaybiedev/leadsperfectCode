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
/*elseif (($p1=='Go' || $p1=='Print Draft') && $branch_id=='')
{
	message('Select Branch of Origin...');
}*/
elseif ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);

	$q = "select 
			tbranch_id,
			cp.branch_id,
			date,
			cashpos_id,
			debit,
			credit,
			br.branch
		from 
			cashpos as cp,
			branch as br
		where 
			date>='$mfrom_date' and 
			date<='$mto_date' and 
			cp.enable and type='P' and ";
		if ($sortby == 'origin')
		{
			if ($branch_id != '')
			{
				$q .= " br.branch_id=cp.branch_id and ";
				$q .= " cp.tbranch_id = '$branch_id'";
			}
			if ($branchto_id != '')
			{
				if ($branch_id != '') 
					$q .= " and cp.branch_id = '$branchto_id'";
				else	
				{
					$q .= " br.branch_id=cp.tbranch_id";
					$q .= " and cp.branch_id = '$branchto_id'";
				}	
			}
		}	
		else
		{	
			if ($branchto_id != '')
			{
				$q .= " cp.branch_id = '$branchto_id'";
				$q .= " and br.branch_id=cp.tbranch_id";
			}
			if ($branch_id != '')
			{
				if ($branchto_id != '')
					$q .= " and cp.tbranch_id = '$branch_id'";
				else 
				{
					$q .= " br.branch_id=cp.branch_id";
					$q .= " and cp.tbranch_id = '$branch_id'";
				}	
			}
		}	
			
		$q .= " order by branch, date";
//echo $q;	
	$qr = pg_query($q) or message(pg_errormessage());

	$header = "\n\n";
	$header .= center($SYSCONF['BUSINESS_NAME'],96)."\n";
	$header .= center('PETTY CASH INTERBRANCH TRANSFERS' ,96)."\n";
	$header .= center('Grouped by : '.($sortby=='origin'?'BRANCH OF ORIGIN':'Branch of Transaction'),96)."\n";
	$header .= center($from_date.' To '.$to_date,96)."\n\n";
	$header .= "  ---- ---------------- ----------- ---------- ----------------------------------- -------------\n";
	$header .= "       ".($sortby=='origin'?'  Transfered To ':'Branch of Origin').
	           "Date       Reference     Account                            Net Amount \n";
	$header .= "  ---- ---------------- ----------- ---------- ----------------------------------- -------------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$ctr=0;
	$mbranch_id = '';
	while ($r = pg_fetch_object($qr))
	{
		if ($r->branch_id == $r->tbranch_id or $r->credit==0 or $r->tbranch_id==0 or $r->tbranch_id==NULL ) continue;
		
		if ($mtbranch_id != $r->branch_id and $sortby=='transfer')
		{
			if ($subtotal !=0)
			{
				$details .= space(83)."------------\n";
				$details .= space(83).adjustRight(number_format($subtotal,2),12)."\n";
				$lc++;			
				$lc++;			
				$subtotal=$ctr=0;
			}
			$details .= "\n   BRANCH OF TRANSACTION : ".strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id))."\n";
			$mtbranch_id = $r->branch_id;
			$lc++;			
			$lc++;			
		} elseif ($mbranch_id != $r->tbranch_id and $sortby=='origin')
		{
			if ($subtotal !=0)
			{
				$details .= space(83)."------------\n";
				$details .= space(83).adjustRight(number_format($subtotal,2),12)."\n";
				$lc++;			
				$lc++;			
				$subtotal=$ctr=0;
			}
			$details .= "\n   BRANCH OF ORIGIN : ".strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$r->tbranch_id))."\n";
			$mbranch_id = $r->tbranch_id;
			$lc++;			
			$lc++;			
		}				
		$ctr++;
		$details.= '  '.adjustRight($ctr,3).'. ';
		if ($sortby=='origin') $details .= adjustSize(strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$r->branch_id)),16);
		else $details .= adjustSize(strtoupper(lookUpTableReturnValue('x','branch','branch_id','branch',$r->tbranch_id)),16);
		$details.=  ' '.adjustSize(ymd2mdy($r->date),10).'  '.
					adjustSize(str_pad($r->cashpos_id,8,'0',STR_PAD_LEFT),9).'  '.
					adjustSize($r->descr,35).' '.
					adjustRight(number_format($r->credit,2),12)."\n";
		$lc++;			
		$total_amount+= $r->credit;
		$subtotal += $r->credit;
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);

			$details = '';
			$lc=6;
		}			
	}
	if ($subtotal !=0)
	{
		$details .= space(83)."------------\n";
		$details .= space(83).adjustRight(number_format($subtotal,2),12)."\n";
		$lc++;			
		$lc++;			
		$subtotal=$ctr=0;
	}

	$details .= "  ---- ---------------- ----------- ---------- ----------------------------------- -------------\n";

	$details .= space(83).adjustRight(number_format($total_amount,2),12)."\n";
	$details .= "  ---- ---------------- ----------- ---------- ----------------------------------- -------------\n";
	
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
        <td width="79%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Interbranch Petty Cash Transactions </b></font></td>
        <td width="19%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="1%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                From </font></td>
              <td width="724" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date" value="<?= $from_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Branch of Origin 
                 
                <select name = "branch_id">
                  <?
				$q = "select * from branch where enable ";
				
				if ($ADMIN['branch_id'] > '999')
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
                Grouped by : 
              <?=lookUpAssoc('sortby',array('Branch of Origin'=>'origin','Branch of Transaction'=>'transfer'),$sortby);?>
              </font></td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
                To </font></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="to_date" type="text" id="to_date" value="<?= $to_date;?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')">
Branch of Transaction
<select name = "branchto_id">
  <?
				$q = "select * from branch where enable ";
				
				if ($ADMIN['branch_id'] > '99')
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
					if ($branchto_id == $r->branch_id)
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
