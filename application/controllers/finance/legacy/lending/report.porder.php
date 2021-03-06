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
if ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	if ($rtype == 'S')
	{
		$q = "select 
				po_header.po_header_id,
				po_header.reference,
				po_header.terms,
				po_header.date,
				supplier.supplier
			from 
				po_header,
				supplier
			where 
				supplier.supplier_id=po_header.supplier_id and
				date>='$mfrom_date' and 
				date<='$mto_date'";
	}
	else
	{
		$q = "select 
				po_header.po_header_id,
				po_header.reference,
				po_header.terms,
				po_header.date,
				supplier.supplier
			from 
				po_header,
				supplier
			where 
				supplier.supplier_id=po_header.supplier_id and
				date>='$mfrom_date' and 
				date<='$mto_date'";
	}
	
	$qr = pg_query($q) or message(pg_errormessage());
	$header = center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('PURCHASE ORDER SUMMARY',80)."\n";
	$header .= center($from_date.' To '.$to_date,80)."\n\n";
	$header .= "   Date      PO. No.  Reference  Supplier                  Term       Amount \n";
	$header .= "-----------  -------- --------- ------------------------- --------- -----------\n";
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	while ($r = pg_fetch_object($qr))
	{
		$lc++;
		if ($p1 == 'Print Draft'  && rtype=='D') $details .= "<bold>";
		$details.= ' '.adjustSize(ymd2mdy($r->date),10).'  '.
					adjustSize(str_pad($r->po_header_id,8,'0',str_pad_left),8).'  '.
					adjustSize($r->reference,8).' '.				
					adjustSize($r->supplier,25).' '.
					adjustRight($r->terms,9).' ';
		if ($rtype=='S')
		{
			$q = "select sum(amount) as amount from po_detail 
							where po_header_id='$r->po_header_id'";
			$rr = fetch_object($q);
			$details .=	adjustRight(number_format($rr->amount,2),11)."\n";
			$total_amount += $rr->amount;
		}	
		else
		{
			$q = "select stock, unit,  qty, po_detail.cost, amount from po_detail, stock
							where 
								stock.stock_id=po_detail.stock_id and 
								po_detail.po_header_id='$r->po_header_id'";
			$qqr = pg_query($q) or die (pg_errormessage());
			$subtotal = 0;
			$detailsx='';
			while ($rr = pg_fetch_object($qqr))
			{
				$lc++;
				$subtotal += $rr->amount;
				$detailsx .= space(5).
							adjustSize($rr->stock,30).'  '.
							adjustRight(number_format($rr->qty,3),7).' '.
							adjustSize($rr->unit,5).'@'.
							adjustRight(number_format($rr->cost,2),10).' '.
							adjustRight(number_format($rr->amount,2),10)."\n";
							
				if ($lc>55 && $p1 == 'Print Draft')
				{
					$details1 .= $header.$details;
					$details .= "<eject>";
					doPrint($header.$details);
					$details = '';
					$lc=6;
				}			
			}	
			$details .=	adjustRight(number_format($subtotal,2),11)."\n";
			if ($p1 == 'Print Draft'  && rtype=='D') $details .= "</bold>";
			$details .= $detailsx;
			$details .= "\n";
			$total_amount += $subtotal;
		}
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);
			$lc=6;
		}			
	}

	$details .= "-----------  -------- --------- ------------------------- --------- -----------\n";
	$details .= space(40).adjustSize('TOTAL AMOUNT ->',25).'  '.
				adjustRight(number_format($total_amount,2),12)."\n";
	$details .= "-----------  -------- --------- ------------------------- --------- -----------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($details1);
	}	
}	
?>	
<form name="form1" method="post" action="">
  <div align="center">
    <table width="1%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td width="38%"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                Purchase Order Print Preview</strong></font></td>
              <td width="62%" align="right" nowrap><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                From 
                <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $from_date;?>" size="8">
                </strong></font> <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                To 
                <input type="text" name="to_date" value="<?= $to_date;?>" size="8"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
                </strong></font> <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                <?= lookUpAssoc('rtype',array('S'=>'S','D'=>'D'), $rtype);?></font> 
                <input name="p1" type="submit" id="p1" value="Go">
              </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="90" rows="18" readonly wrap="OFF"><?= $details1;?></textarea>
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
