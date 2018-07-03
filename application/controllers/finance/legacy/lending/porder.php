<script>
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL Purchase Order?"))
		{
			document.f1.action="?p=porder&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=porder&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=porder&p1="+ul.id;
	}	
}
function vCompute()
{
	document.f1.amount.value=twoDecimals(document.f1.qty.value*document.f1.cost.value);
}
</script>
<form action="?p=porder.browse" method="post" name="form1" >
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?= lookUpAssoc('searchby',array('PO No.'=>'po_header_id','Reference'=>'reference','Supplier'=>'supplier','Stock description'=>'stock_description'), $searchby);?>
        <input name="p1" type="submit" id="p1" value="Go">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=porder&p1=New'">
        <input name="p1" type="submit" id="p1" value="Browse"> 
        <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>
<?
if (!chkRights2('transaction','madd',$ADMIN['admin_id']))
{
	message('You have [ NO ] permission to access this area...');
	exit;
}
if (!session_is_registered("aPO"))
{
	session_register("aPO");
	$aPo=null;
	$aPo=array();
}

if (!session_is_registered("aPOD"))
{
	session_register("aPOD");
	$aPOD=null;
	$aPOD=array();
}
if (!session_is_registered("iPOD"))
{
	session_register("iPOD");
	$iPOD=null;
	$iPOD=array();
}

$p1 = $_REQUEST['p1'];
$fields_header = array('supplier_id','reference','remarks','terms','transaction_type','date');
$fields_detail = array('qty','cost');


if (!in_array($p1, array(null,'Edit','Delete','Print','Load','selectStock','Serve','New')))
{
	for ($c=0;$c<count($fields_header);$c++)
	{
		$aPO[$fields_header[$c]] = $_REQUEST[$fields_header[$c]];
		if ($fields_header[$c] == 'date')
		{
			$aPO['date'] = mdy2ymd($_REQUEST[$fields_header[$c]]);
		}
	}
	$aPo['admin_id']=$ADMIN['admin_id'];

	for ($c=0;$c<count($fields_detail);$c++)
	{
		$iPOD[$fields_detail[$c]] = $_REQUEST[$fields_detail[$c]];
	}
}
if ($p1 == 'Load' && $id == '')
{
	message("Nothing to Load...");
}
elseif ($p1 == 'Load')
{
	$aPO=null;
	$aPO=array();
	$aPOD= null;
	$aPOD = array();
	$q = "select * from po_header where po_header_id='$id'";
	$qr = pg_query($q) or message(pg_errormessage());
	if ($qr)
	{
		$qd = "select * from po_detail where po_header_id='$id'";
		$qdr = pg_query($qd) or message(pg_errormessage());
		while ($r = pg_fetch_assoc($qdr))
		{
			$temp = $r;
			$qs = "select stock, stock_code from stock where stock_id='".$r['stock_id']."'";
			$qrs = pg_query($qs);
			$rs = pg_fetch_object($qrs);
			$temp['stock']=$rs->stock;
			$temp['stock_code'] = $rs->stock_code;
			$aPOD[]=$temp;
		}
		
		$r = pg_fetch_assoc($qr);
		$aPO = $r;
	}
}
elseif ($p1 == 'New')
{
	$aPO=null;
	$aPO=array();
	$aPOD= null;
	$aPOD = array();
	$iPOD= null;
	$iPOD = array();
	$aPO['date'] = date('Y-m-d');
}
elseif ($p1 == 'selectStock' && $id != '')
{
	$q = "select stock_id, stock_code, stock, cost, unit from stock where stock_id = '$id'";
	$qr = pg_query($q) or message(pg_last_notice());
	$r = pg_fetch_assoc($qr);
	$iPOD = $r;
}
elseif ($p1 == 'Edit' && $id!='')
{
	$iPOD = null;
	$iPOD = array();
	$c=0;
	foreach ($aPOD as $temp)
	{
		$c++;
		if ($id == $c)
		{
			$iPOD = $temp;
			break;
		}
	}
}
elseif ($p1 == 'Ok' && ($iPOD['stock_id']=='' or intval($iPOD['qty']) ==0))
{
	$aPO['status'] = 'M';
}
elseif ($p1 == 'Ok' && $iPOD['stock_id']!='' && $iPOD['qty']>0)
{
	$aPO['status'] = 'M';
	$c=0;
	$fnd=0;
	foreach ($aPOD as $temp)
	{
		if ($temp['stock_id'] == $iPOD['stock_id'])
		{
			$dummy = $temp;
			$dummy['qty'] = $iPOD['qty'];
			$dummy['cost'] = $iPOD['cost'];
			$dummy['amount'] = $iPOD['qty'] * $iPOD['cost'];
			$aPOD[$c] = $dummy;
			$fnd=1;
			break;
		}
		$c++;
	}
	if ($fnd==0)
	{
			$iPOD['amount'] = $iPOD['qty']* $iPOD['cost'];
			$aPOD[] = $iPOD;
	}
	$iPOD = null;
	$iPOD = array();
}
elseif ($p1 == 'Delete Checked' && count($aChk)>0)
{
	$newArray=null;
	$newArray=array();
	$nctr=0;
	foreach ($aPOD as $temp)
	{
		$nctr++;
		if (in_array($nctr,$aChk))
		{
			if ($temp['po_detail_id'] != '')
			{
				$qr = pg_query("delete from po_detail where po_detail_id='".$temp['po_detail_id']."'");
				if (pg_affected_rows($qr) == 0)
				{
					message("Problem deleting in PO table for item ".$temp['stock']);
					$newArray[]=$temp;
				}
			}
		}
		else
		{
			$newArray[]=$temp;
		}
	}
	$aPOD = $newArray;	
}
elseif ($p1 == 'Save' && $aPO['supplier_id'] == '')
{
	message("Cannot save PO.  Please provide SUPPLIER...");
}
elseif ($p1 == 'Save' && count($aPOD) == 0)
{
	message("No items to save...");
}
elseif ($p1 == 'Save')
{
	if ($aPO['po_header_id'] == '')
	{
		$aPO['audit'] = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';

		pg_query("begin transaction");
		$q = "insert into po_header 
						(reference,date,supplier_id,terms,status,
						transaction_type, enable, admin_id, audit)
					values ('".$aPO['reference']."', '".$aPO['date']."','".$aPO['supplier_id']."',
						'".$aPO['terms']."', 'S','".$aPO['transaction_type']."', true, 
						'".$ADMIN['admin_id']."','".$aPO['audit']."')"; //'".$_SERVER['REMOTE_ADDR']."',
		$qr = pg_query($q);
		
		$ok=true;
		if ($qr)
		{
			$qid = query("select currval('po_header_po_header_id_seq'::text)");
			$rid = pg_fetch_object($qid);
			$aPO['po_header_id'] = $rid->currval;

			$c=0;
			foreach ($aPOD as $temp)
			{
				$q = "insert into po_detail
							(po_header_id,stock_id,qty,cost,amount)
						values
							('".$aPO['po_header_id']."','".$temp['stock_id']."', '".$temp['qty']."', 
							'".$temp['cost']."','".$temp['amount']."')";
				$qr = pg_query($q);
				if (!$qr)
				{
					$ok=false;
					break;
				}
				else
				{
					$dummy=$temp;
					$qr = query("select currval('po_detail_po_detail_id_seq'::text)") or message(pg_errormessage());
					$r = pg_fetch_object($qr);
					$dummy['po_detail_id'] = $r->currval;
					$aPOD[$c]=$dummy;
				}
				$c++;
			}	
			if ($ok)
			{
				pg_query("commit");
				$aPO['status']='S';
				message(" Purchase Order Saved...");
			}
			else
			{
				pg_query("rollback transaction");
				message("Problem Adding To PO Details...".pg_last_notice($qdr));
				$aPO['status']='S';
				$aPO['po_header_id']='';
			}
		}
		else
		{
			message("Cannot Add Record To PO Header File...".$q.pg_last_notice($qr));
			pg_query("rollback transaction");
		}
							
	}
	else
	{
		$ok=true;
		$aPO['audit'] = $aPO['audit'].'Updated by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
	
		pg_query("begin transaction");	
		$q = "update po_header set audit = '".$aPO['audit']."'";
		for ($c=0;$c<count($fields_header);$c++)
		{
			$q .= ",".$fields_header[$c]."='".$aPO[$fields_header[$c]]."'";
		}
		$q .= " where po_header_id = '".$aPO['po_header_id']."'";

		$qr = pg_query($q);
		if ($qr)
		{
			$c=0;
			foreach ($aPOD as $temp)
			{
				if ($temp['po_detail_id'] == '')
				{
					$q = "insert into po_detail
								(po_header_id,stock_id,qty,cost,amount)
							values
								('".$aPO['po_header_id']."','".$temp['stock_id']."', '".$temp['qty']."', 
								'".$temp['cost']."','".$temp['amount']."')";
					$qr = pg_query($q);
				}
				else
				{
					$q = "update po_detail set
								stock_id='".$temp['stock_id']."',
								qty='".$temp['qty']."',
								cost='".$temp['cost']."',
								amount='".$temp['amount']."'
							where po_detail_id='".$temp['po_detail_id']."'";
					$qr = pg_query($q) or message(pg_errormessage());
				}
				if (pg_affected_rows($qr)==0 or !$qr)
				{
					$ok=false;
					break;
				}
				else
				{
					if ($temp['po_detail_id'] == '')
					{
						$dummy=$temp;
						$qr = query("select currval('po_detail_po_detail_id_seq'::text)") or message(pg_errormessage());
						$r = pg_fetch_object($qr);
						$dummy['po_detail_id'] = $r->currval;
						$aPOD[$c]=$dummy;
					}
				}
				$c++;
			}	
			if ($ok)
			{
				pg_query("commit transaction");
				message(" Purchase Order Updated...");
			}
			else
			{
				pg_query("rollback transaction");
				message("Problem Updating To PO Details...".pg_errormessage());
				$aPO['status']='S';
			}
		}
		else
		{
			message("Cannot Modify Record To PO Header File...".$q.pg_last_notice($qr));
			pg_query("rollback transaction");
		}
					
	}
	
}
elseif ($p1 == 'Print' && !in_array($aPO['status'],array('S','P')))
{
	message("Cannot Print. Save Purchase Order Before Printing...");
}
elseif ($p1 == 'Print')
{
	$q = center(strtoupper($SYSCONF['BUSINESS_NAME']),80)."\n";
	$q .= center($SYSCONF['BUSINESS_ADDR'],80)."\n\n";
	$q .= center('PURCHASE ORDER',80)."\n";
	$q .= '  '. "Supplier : ".adjustSize(lookUpTableReturnValue('x','supplier','supplier_id','supplier',$aPO['supplier_id']),43).' '.
				"PO No.   : ".str_pad($aPO['po_header_id'],8,'0',str_pad_left)."\n";
	$q .= '  '. "Address  : ".adjustSize(lookUpTableReturnValue('x','supplier','supplier_id','address',$aPO['supplier_id']),43).' '.
				"Date     : ".ymd2mdy($aPO['date'])."\n";
	$q .= '  '. "Type     : ".adjustSize(transaction_type($aPO['transaction_type']),43).' '.
				"Terms    : ".$aPO['terms']."\n";
	
	$q .= "\n";
	$q .= "  ".str_repeat('-',76)."\n";
	$q .= "   Item Description                            Qty         Price     Amount   \n";
	$q .= "  ".str_repeat('-',76)."\n";
	$header = $q;
	$c=0;
	foreach ($aPOD as $temp)
	{
		$c++;
		$q .= '  '.adjustRight(number_format($c,0),3).'. '.
					adjustSize($temp['stock'],35).'  '.
					adjustRight(number_format($temp['qty'],3),10).'  '.
					adjustRight(number_format($temp['cost'],2),10).'  '.
					adjustRight(number_format($temp['amount'],2),10)."\n";
	}
	$q .= "  ".str_repeat('-',76)."\n";
	$q .= '    '.adjustSize($aPO['total_items'].' Item(s)',35).space(10).
				'TOTAL AMOUNT --> '.adjustRight(number_format($aPO['total_amount'],2),12)."\n";
	$q .= "  ".str_repeat('-',76)."\n";
	$q .= "\n\n";
	$q .= "  ".adjustSize($ADMIN['name'],30).space(15)."_____________________\n";
	$q .= "  Prepared by: ".space(35)."Received by:\n";
	$q .= "  ".date('m/d/Y g:ia');
	
	if ($SYSCONF['RECEIPT_PRINT'] == 'DRAFT')
	{
		doPrint($q);
	}
	else
	{
	   	echo "<textarea name='print_area' cols='90' rows='18' readonly wrap='OFF'>$q</textarea></script>";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>";
		echo "<script>printIframe(print_area)</script>";
	}
	$q = "update po_header set status='P' where po_header_id='".$aPO['po_header_id']."'";
	query($q);	
}
elseif ($p1=='CancelConfirm')
{
	$q = "select * from rr_header where po_header_id='".$aPO['po_header_id']."'";
	$qr = pg_query($q);
	if (pg_num_rows($qr)>0)
	{
		message("<img src='../graphics/achtung.gif'> Cannot Cancel Purchase Order.  Transactions already exists...");
	}
	else
	{
		$q = "update po_header set status='C' where po_header_id='".$aPO['po_header_id']."'";
		$qr = query($q);	
		if ($qr)
		{
			message(' Purhcase Order No. ['.str_pad($aPO['po_header_id'],8,'0',str_pad_left).'] Successfully CANCELLED');
		}
		$aPO['status'] = 'C';
	}	
}
?>
<form action="" method="post" name="f1" id="f1">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td height="20" colspan="2"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/waiting.gif" width="16" height="16"><strong>Purchase 
        Order Entry</strong></font></td>
      <td height="20" colspan="2" align="center"> <font face="Times New Roman, Times, serif"> 
        <em> 
        <?= status($aPO['status']);?>
        </em></font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="18%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></td>
      <td width="54" ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="reference" type="text" id="reference" value="<?= $aPO['reference'];?>" size="10">
        </font></td>
      <td width="10%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td width="18%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aPO['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Supplier</font></td>
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTable2('supplier_id','supplier','supplier_id','supplier',$aPO['supplier_id']);?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">PO No.</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= str_pad($aPO['po_header_id'],8,'0',str_pad_left);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transaction 
        Type</font></td>
      <td > 
        <?= lookUpAssoc('transaction_type',array('Cash'=>'C','Charge'=>'H'),$aPO['transaction_type']);?>
      </td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= status($aPO['status']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Terms</font></td>
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="terms" type="text" id="terms" value="<?= $aPO['terms'];?>" size="10">
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr bgcolor="#CCCCCC"> 
      <td colspan="4"><font size="2" face="Times New Roman"><strong><em>Details</em></strong><br>
        </font> <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF">
          <tr> 
            <td width="18%" nowrap><font size="2" face="Times New Roman"><font face="Verdana, Arial, Helvetica, sans-serif">Item</font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><br>
              <input name="searchkey" type="text" id="searchkey3" value="<?= $searchkey;?>" size="10">
              </font> <input name="p1" type="submit" id="p123" value="Search"></td>
            <td width="35%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description<br>
              <input name="stock"  readOnly  type="text" id="stock" value="<?= $iPOD['stock'];?>" size="40">
              </font></td>
            <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Qty</font><br> 
              <input name="qty" type="text" id="qty" value="<?= $iPOD['qty'];?>" size="7" onBlur="vCompute()"></td>
            <td width="1%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Price</font><br> 
              <input name="cost" type="text" id="cost" value="<?= $iPOD['cost'];?>" size="7" onBlur="vCompute()"></td>
            <td width="40%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font><br> 
              <input name="amount" type="text" id="amount" value="<?= $iPOD['amount'];?>" size="8"> 
              <input name="p1" type="submit" id="p1222" value="Ok"></td>
          </tr>
        </table></tr>
  </table>
  <?
	if ($p1 == 'Search')
  	{
  ?>
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F4EAFF">
    <tr bgcolor="#9999CC"> 
      <td width="3%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="15%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Stock 
        Code</font></strong></td>
      <td width="30%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="8%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Unit</font></strong></td>
      <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Category</font></strong></td>
      <td width="11%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Price</font></strong></td>
      <td width="11%" align="center">&nbsp;</td>
    </tr>
    <?
	  	$q = "select * from stock where stock_code ilike '$searchkey%' or stock ilike '%$searchkey%' and enable order by lower(stock)";
		$qr = pg_query($q) or message(pg_last_notice());
		$cs = 0;
		while ($r = pg_fetch_object($qr))
		{
			$cs++;
	?>
    <tr bgColor='#FFFFFF' onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" onClick="window.location=?p=porder&p1=selectStock&id=<?=$r->stock_id;?>'"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $cs;?>
        .</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  	<a href='?p=porder&p1=selectStock&id=<?=$r->stock_id;?>'>
        <?= $r->stock_code;?></a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  	<a href='?p=porder&p1=selectStock&id=<?=$r->stock_id;?>'>
        <?= $r->stock;?></a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r->unit;?>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTableReturnValue('x','category','category_id','category',$r->category_id);?>
        </font></td>
      <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($r->cost,2);?></font> 
      </td>
      <td><font size="1">
  	<a href='?p=porder&p1=selectStock&id=<?=$r->stock_id;?>'>
	  Select</a></font></td>
    </tr>
    <?
		}
	?>
  </table>
  <br>
  <?
  }
  ?>
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#D2DCDF"> 
      <td width="8%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Stock 
        Code</font></strong></td>
      <td width="37%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Unit</font></strong></td>
      <td width="9%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Qty</font></strong></td>
      <td width="10%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Price</font></strong></td>
      <td width="16%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
    </tr>
    <?
	$c=0;
	$total_amount=0;
	foreach ($aPOD as $temp)
	{
		$total_amount += $temp['amount'];
		$c++;
	?>
    <tr bgColor='#FFFFFF' onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $c;?>
        . 
        <input name="aChk[]" type="checkbox" id="aChk" value="<?= $c;?>">
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  	<a href='?p=porder&p1=Edit&id=<?=$c;?>'>
        <?= $temp['stock_code'];?></a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	  	<a href='?p=porder&p1=Edit&id=<?=$c;?>'>
        <?= $temp['stock'];?></a>
        </font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $temp['unit'];?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($temp['qty'],3);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($temp['cost'],2);?>
        </font></td>
      <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($temp['amount'],2);?>
        </font></td>
    </tr>
    <?
	}
	$aPO['total_amount']  = $total_amount;
	$aPO['total_items'] = $c;
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="5"><input name="p1" type="submit" id="p1" value="Delete Checked"></td>
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font></strong></td>
      <td align="right"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        <?= number_format($aPO['total_amount'],2);?>
        </font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="7"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/print.jpg" alt="Print This Claim Form"  onClick="vSubmit(this)" name="Print" id="Print"> 
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="vSubmit(this)"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20"> 
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            </td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
