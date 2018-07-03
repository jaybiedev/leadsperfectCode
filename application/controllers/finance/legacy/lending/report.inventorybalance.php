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
	$mdate = mdy2ymd($date);
	
	$q = "select *
			
		from 
			stock
		where enable ";
	if ($classification_id != '')
	{
		$q .= " and classification_id='$classification_id'";
	}
	$q .= "order by classification_id, category_id, stock";
	$qr = pg_query($q) or message(pg_errormessage());
	$header = center($BUSINESS_NAME,80)."\n";
	$header .= center('INVENTORY BALANCE REPORT',80)."\n";
	$header .= center('As of Date '.$date,80)."\n\n";
	$header .= "      Item Description          Unit  Cat.      Cost      Balance    Total Cost\n";
	$header .= "---- -------------------------- ----- ----- ------------ --------- -------------\n";
	$details = $details1 = '';
	$details1 = $header;
	$ctr=$total_cost = 0;
	$mclassification_id = $category_id = '';
	$lc=5;
	while ($r = pg_fetch_object($qr))
	{
		if ($mclassification_id != $r->classification_id)
		{
			$details .= adjustSize(lookUpTableReturnValue('x','classification','classification_id','classification',$r->classification_id),25)."\n";
			$mclassification_id=$r->classification_id;
		}
		if ($category_id != $r->category_id)
		{
			$details .= "  ".adjustSize(lookUpTableReturnValue('x','category','category_id','category',$r->category_id),25)."\n";
			$category_id=$r->category_id;
		}
		
		$q = "select sum(qty_balance) as qty_balance, sum(qty_balance*cost) as item_cost 
					from 
						stockledger
					where
						stock_id='$r->stock_id' and
						enable ";
		$qlr = pg_query($q) or die (pg_errormessage());
		$rl = pg_fetch_object($qlr);
		$ctr++;	
		$details .= adjustRight($ctr,3).'. '.adjustSize($r->stock,25).' '.
					adjustRight($r->unit,5).' '.
					adjustRight(lookUpTableReturnValue('x','category','category_id','category_code',$r->category_id),5).'  '.
					adjustRight(number_format($r->cost,2),11).' '.
					adjustRight(number_format($rl->qty_balance,3),10).' '.
					adjustRight(number_format($rl->item_cost,2),12)."\n";
		$total_cost += $rl->item_cost;
		$lc++;
		if ($lc>55 && $p1 == 'Print Draft')
		{
			doPrint($header.$details);
			$lc=5;
			$details1 .= $header.$details;
			$details = '';
		}
	}
	$details .= "---- -------------------------- ----- ----- ------------ --------- -------------\n";
	$details .= adjustSize('***** TOTALS *****',35).space(32).
				adjustRight(number_format($total_cost,2),12)."\n";
	$details .= "---- -------------------------- ----- ----- ------------ --------- -------------\n";
	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details);
	}	
}
if ($date == '') $date=date('m/d/Y');	
?>	
<form name="form1" method="post" action="">
  <div align="center">
    <table width="1%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td width="34%" nowrap><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17">Stock 
                Balance Preview</strong></font></td>
              <td width="66%" align="right" nowrap><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Classification 
                <input name="date" type="hidden" id="date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $date;?>" size="9">
                </strong></font> <font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <?= lookUpTable2('classification_id','classification','classification_id','classification',$classification_id);?>
                </strong></font> </font> <font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                </strong></font> 
                <input name="p1" type="submit" id="p1" value="Go">
              </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="90" rows="20"  wrap="off" readonly><?= $details1;?></textarea>
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
