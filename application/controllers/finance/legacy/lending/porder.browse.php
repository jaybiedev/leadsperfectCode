<?
if ($aPO != '' && !in_array($p1, array('Go','Next','Previous','Sort','Browse')))
{
	echo "<script> window.location='?p=porder' </script>";
	exit;
}
?>
<form action="" method="post" name="form1" >
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?= lookUpAssoc('searchby',array('PO No.'=>'po_header_id','Reference'=>'reference','Supplier'=>'supplier','Date'=>'date','Stock description'=>'stock_description'), $searchby);?>
        <input name="p1" type="submit" id="p1" value="Go"> 
		<input type="button" name="Submit2" value="Add New" onClick="window.location='?p=porder&p1=New'">
        <input type="button" name="Submit23222" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>

<table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CCCCCC"> 
    <td height="20" colspan="8"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
      <img src="../graphics/storage.gif" width="16" height="17"> Browse Purchase 
      Orders</strong></font></td>
  </tr>
  <tr> 
    <td align="center"><font size="2" face="Geneva, Arial, Helvetica, san-serif">#</font></td>
    <td nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif"> <a href="?p=porder.browse&p1=Go&sortby=porder&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">PO 
      No. </a></font></td>
    <td nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif"> <a href="?p=porder.browse&p1=Go&sortby=porder&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Date</a></font></td>
    <td nowrap><font size="2" face="Geneva, Arial, Helvetica, san-serif"> <a href="?p=porder.browse&p1=Go&sortby=porder&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Reference</a></font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="?p=porder.browse&p1=Go&sortby=unit&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Supplier</a></font></td>
    <td width="7%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=porder.browse&p1=Go&sortby=category_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
      Status</a></font></td>
    <td width="7%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=porder.browse&p1=Go&sortby=category_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Type</a></font></td>
    <td width="14%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=porder.browse&p1=Go&sortby=category_id&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Encoder</a></font></td>
  </tr>
  <?
$q = "select 
			po_header.po_header_id,
			po_header.date,
			po_header.reference,
			po_header.supplier_id,
			supplier.supplier,
			po_header.status,
			po_header.admin_id,
			po_header.transaction_type
		from 
			po_header,
			supplier
		where
			supplier.supplier_id=po_header.supplier_id ";

if ($xSearch != '')
{
	$q .= " and $searchby like '$xSearch%' ";
}
if ($sortby == '')
{
	$sortby = 'po_header_id';
}
$q .= " order by $sortby ";

		
if ($p1 == 'Go' or $p1 == '' or $start=='')
{
	$start = 0;
}
elseif ($p1 == 'Next')
{
	$start += 15;
}
elseif ($p1 == 'Previous')
{
	$start -= 15;
}
if ($start<0) $start=0;
	
$q .= " offset $start limit 15 ";

$qr = pg_query($q) or message("Error querying Purchase Order data...".pg_errormessage().$q);

if (pg_num_rows($qr) == 0)
{
	if ($p1== 'Go') 
	{
	 	message("Purchase Order data [NOT] found...");
	}	
	else
	{
	 	message("End of File...");
	}
}
	
$ctr=0;
while ($r = pg_fetch_object($qr))
{
	$ctr++;
  ?>
  <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" onClick="window.location='?p=porder&p1=Load&id=<?= $r->po_header_id;?>'"> 
    <td width="4%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $ctr;?>
      .</font></td>
    <td width="8%" nowrap" bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=porder&p1=Load&id=<?= $r->po_header_id;?>"> 
      <?= str_pad($r->po_header_id,8,'0',str_pad_left);?>
      </a> </font></td>
    <td width="7%" nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= ymd2mdy($r->date);?>
      </font></td>
    <td width="9%" nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->reference;?>
      </font></td>
    <td width="44%" nowrap"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r->supplier;?>
      </font></td>
    <td nowrap bgColor="<?=($r->status=='C' ? '#FFCCCC' : '');?>"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= status($r->status);?>
      </font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= transaction_type($r->transaction_type);?>
      </font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= lookUpTableReturnValue('x','admin','admin_id','name',$r->admin_id);?>
      </font></td>
  </tr>
  <?
  }
  ?>
  <tr> 
    <td colspan="8" bgcolor="#FFFFFF"><input type="button" name="Submit22" value="Add New" onClick="window.location='?p=porder&p1=New'"> 
      <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"></td>
  </tr>
</table>

<div align="center"> <a href="?p=porder.browse&p1=Previous&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_left.gif" width="4" height="7" border="0"></a> 
  <a href="?p=porder.browse&p1=Previous&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"> 
  Previous</a> | <a href="?p=porder.browse&p1=Next&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>">Next</a> 
  <a href="?p=porder.browse&p1=Next&sortby=<?=$sortby;?>&start=<?=$start;?>&xSearch=<?=$xSearch;?>&searchby=<?=$searchby;?>"><img src="../graphics/redarrow_right.gif" width="4" height="7" border="0"></a> 
</div>
