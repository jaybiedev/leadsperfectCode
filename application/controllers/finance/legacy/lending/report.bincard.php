<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL customer Record?"))
		{
			document.f1.action="?p=report.bincard&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=report.bincard&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=report.bincard&p1="+ul.id;
	}	
}
</script>
<?
if ($from_date == '') $from_date=date('m/d/Y');
if ($to_date == '') $to_date=date('m/d/Y');

if (!session_is_registered('aBIN'))
{
	session_register('aBIN');
	$aBIN=null;
	$aBIN=array();
}
if ($c_id!= ''  && $p1 == 'selectStock')
{
	$aBIN=null;
	$aBIN=array();
	$q = "select 
				*
		 from 
		 		stock
		where 
				stock_id='$c_id'";
	$r = fetch_assoc($q);
	$aBIN = $r;
}
?> 

<form action="" method="post" name="f1" id="f1">
  <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search Item 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?=lookUpAssoc('sortby',array('Item Name'=>'stock','Stock Code'=>'stock_code','Stock Id'=>'stock_id'),$sortby);?>
        </font><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>
        <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $from_date;?>" size="9">
        </strong></font><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        To </font><font  color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        <input name="to_date" type="text" id="to_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $to_date?>" size="9">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.to_date, 'mm/dd/yyyy')"> 
        </strong></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p1" type="submit" id="p1" value="Refresh">
        </font></td>
    </tr>
    <tr>
      <td><hr color="red"></td>
    </tr>
  </table>
<?
if ($p1 != 'Go')
{
  	$header = center($SYSCONF['BUSINESS_NAME'],80)."\n";
  	$header .= center('B-I-N-C-A-R-D',80)."\n\n";
	$header .= adjustSize("Item : ".$aBIN['stock'],45).' '.
  				'Dates '.$from_date.' To '.$to_date."\n";
	$header .= "Unit       : ".$aBIN['unit']."\n";
	$header .= "Last Cost  : ".number_format($aBIN['cost'],2)."\n";
	$header .= "\n";
	$header .= str_repeat('-',78)."\n";
	$header .= " Date    Reference Type    Particulars              IN       OUT     BALANCE  \n";
	$header .= str_repeat('-',78)."\n";
	
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);
	$beginning_balance = 0;
	
	$aRep = array();
	$q = "select 
				rr_header.date, 
				rr_header.rr_header_id,
				rr_header.supplier_id,
				rr_header.status,
				rr_detail.stock_id, 
				rr_detail.qty,
				rr_detail.cost,
				rr_detail.amount
		 from 
		 		rr_header, 
				rr_detail
		where
				rr_detail.rr_header_id = rr_header.rr_header_id and
				date<='$mto_date' and
				stock_id='".$aBIN['stock_id']."'";
				
	$qr = pg_query($q) or die (pg_errormessage());
	while ($r=pg_fetch_assoc($qr))
	{
		if ($r['date'] < $mfrom_date)
		{
			$beginning_balance += $r['qty'];
		}
		else
		{
			$temp = $r;
			$temp['particulars'] = lookUpTableReturnValue('x','supplier','supplier_id','supplier',$r['supplier_id']);
			$temp['type']='RR';
			$temp['qty_in']=$r['qty'];
			$temp['reference'] = str_pad($r['rr_header_id'],8,'0',str_pad_left);
			$aRep[] = $temp;
		}
	}

	// stock returns	
	$q = "select 
				resturnstock_header.date, 
				resturnstock_header.rr_header_id,
				resturnstock_header.supplier_id,
				resturnstock_header.type,
				resturnstock_header.status,
				resturnstock_detail.stock_id, 
				resturnstock_detail.qty,
				resturnstock_detail.cost,
				resturnstock_detail.amount
		 from 
		 		resturnstock_header, 
				resturnstock_detail
		where
				resturnstock_detail.resturnstock_header_id = resturnstock_header.resturnstock_header_id and
				date<='$mto_date' and
				stock_id='".$aBIN['stock_id']."'";
				
	$qr = pg_query($q) or die (pg_errormessage());
	while ($r=pg_fetch_assoc($qr))
	{
		if ($r['date'] < $mfrom_date)
		{
			$beginning_balance += $r['qty'];
		}
		else
		{
			$temp = $r;
			$temp['particulars'] = lookUpTableReturnValue('x','supplier','supplier_id','supplier',$r['supplier_id']);
			if ($r['type'] == 'DR')
			{
				$temp['particulars'] = lookUpTableReturnValue('x','account','account_id','account',$r['account_id']);
			}
			else
			{
				$temp['particulars'] = lookUpTableReturnValue('x','section','section_id','section',$r['section_id']);
			}	
			$temp['qty_in']=$r['qty'];
			$temp['reference'] = str_pad($r['returnstock_header_id'],8,'0',str_pad_left);
			$aRep[] = $temp;
		}
	}
	
	// stocks issuance
	$q = "select 
				si_header.date, 
				si_header.si_header_id,
				si_header.section_id,
				si_header.type,
				si_header.status,
				si_header.account_id,
				si_detail.stock_id, 
				si_detail.qty_out,
				si_detail.cost,
				si_detail.amount
		 from 
		 		si_header, 
				si_detail
		where
				si_detail.si_header_id = si_header.si_header_id and
				date<='$mto_date'and
				stock_id='".$aBIN['stock_id']."'";
				
	$qr = pg_query($q) or die (pg_errormessage());
	while ($r=pg_fetch_assoc($qr))
	{
		if ($r['date'] < $mfrom_date)
		{
			$beginning_balance -= $r['qty_out'];
		}
		else
		{
			$temp = $r;
			if ($r['type'] == 'DR')
			{
				$temp['particulars'] = lookUpTableReturnValue('x','account','account_id','account',$r['account_id']);
			}
			else
			{
				$temp['particulars'] = lookUpTableReturnValue('x','section','section_id','section',$r['section_id']);
			}	
			$temp['reference'] = str_pad($r['si_header_id'],8,'0',str_pad_left);
			$aRep[] = $temp;
		}
	}


	// reporting
	$balance = $beginning_balance;
	$details .= adjustSize('Balance Forwarded',67).
				adjustRight(number_format($balance,3),8)."\n";

	foreach ($aRep as $temp)
	{
		$details .= adjustSize(ymd2mdy($temp['date']),10).' '.
					adjustSize($temp['reference'],8).' '.
					adjustSize($temp['type'],2).' '.
					adjustSize($temp['particulars'],25).' ';
		if ($temp['status'] == 'C')
		{
			$details .= "Cancelled Transaction \n";
		}
		else
		{
			$balance += $temp['qty_in'] - $temp['qty_out'];
			$details .=	adjustRight(number_format2($temp['qty_in'],3),8).' '.
						adjustRight(number_format2($temp['qty_out'],3),8).' '.
						adjustRight(number_format($balance,3),8)."\n";
		}		
	}
	$details .= str_repeat('-',78)."\n\n";
	$details1 = $header.$details;
	if ($p1 = 'Print Draft')
	{
		doPrint($header.$details);
	}	
?>
  <div align="center">
    <table width="1%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td width="34%" nowrap><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17">BinCard 
                Preview</strong></font></td>
              <td width="66%" align="right" nowrap>&nbsp; </td>
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

<?
}
elseif ($p1 == 'Go')
{
	echo "</form>";
  	$qr = pg_query("select * 
				from 
					stock
				where 
					enable and
					stock like '$xSearch%'
				order by
					stock")
			or message("Error Querying Stock file...".pg_errormessage());
?>
  
<table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="38%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Item 
      Name</font></strong></td>
    <td width="10%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Unit</font></strong></td>
    <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Classification</font></strong></td>
    <td width="23%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Category</font></strong></td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location='?p=report.bincard&p1=selectStock&c_id=<?=$r->stock_id;?>&xSearch=<?=$xSearch;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<a href="?p=report.bincard&p1=selectStock&c_id=<?=$r->stock_id;?>&xSearch=<?=$xSearch;?>&from_date=<?=$from_date;?>&to_date=<?=$to_date;?>"> 
      <?= $r->stock;?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->unit;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','classification','classification_id','classification',$r->classification_id);?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','category','category_id','category',$r->category_id);?>
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?	
  }
?>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
