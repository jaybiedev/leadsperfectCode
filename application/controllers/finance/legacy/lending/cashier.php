<title>Point of	Sale</title>
<script	language="JavaScript"	type="text/JavaScript">
<!--
function vItemReturn()
{
	x=prompt("Please input Receipt#","");
	alert(x);
	return false;
}
function MM_reloadPage(init) {	//reloads	the	window if	Nav4 resized
	if (init==true)	with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4))	{
		document.MM_pgW=innerWidth;	document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
	else if	(innerWidth!=document.MM_pgW ||	innerHeight!=document.MM_pgH)	location.reload();
}
MM_reloadPage(true);

var	isNS = (navigator.appName	== "Netscape") ? 1 : 0;
var	EnableRightClick = 0;

if(isNS) 
document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
document.onhelp=function(){event.returnValue=false};


keys = new Array();
keys["f112"] = 'f1';
keys["f113"] = 'f2';
keys["f114"] = 'f3';
keys["f115"] = 'f4';
keys["f116"] = 'f5';
keys["f117"] = 'f6';
keys["f118"] = 'f7';
keys["f119"] = 'f8';
keys["f120"] = 'f9';
keys["f121"] = 'f10';
keys["f122"] = 'f11';
keys["f123"] = 'f12';

function mischandler(){
	if(EnableRightClick==1){ return	true;	}
	else {return false;	}
}
function mousehandler(e){
	if(EnableRightClick==1){ return	true;	}
	var	myevent	=	(isNS) ? e : event;
	var	eventbutton	=	(isNS) ? myevent.which : myevent.button;
	if((eventbutton==2)||(eventbutton==3)) return	false;
}
function keyhandler(e) 
{
//document.onkeydown = function(){
	var myevent = (isNS) ? e : window.event;
	mycode=myevent.keyCode
	
	if (myevent.keyCode==96)
	{
   	    EnableRightClick = 1;
	}
	else if (keys["f"+myevent.keyCode])
	{
		mycode=myevent.keyCode

		if (mycode == 113)	//F2
		{
			f1.action='?p=cashier&p1=Cashdraw';
			f1.submit();
//			f1.textbox.value='';	
		}
		else if (mycode == 114)   //F3
		{
			if (confirm("Are you sure you want to Delete Item?"))
			{
				f1.action='?p=cashier&p1=Delete';
				f1.submit();
			}
		}		
		else if (mycode == 115)	  //F4
		{
			if (f1.textbox.value == '')
			{
				alert("Enter Quantity.");
			}
			else
			{
				f1.action='?p=cashier&p1=Qty';
				f1.submit();
			}
		}		
		else if (mycode == 116)	  //F5 - line discount
		{
			if (f1.textbox.value == '')
			{
				alert("Enter discount %");
			}
			else
			{
				f1.action='?p=cashier&p1=cDisc';
//				f1.action='?p=cashier&p1=Price';
				f1.submit();
			}
		}		
		else if (mycode == 117)	  //F6
		{
			f1.action='?p=cashier&p1=WSale';
			f1.submit();
		}		
		else if (mycode == 118)	  //F7
		{
//			f1.action='?p=cashier&p1=cDisc';
//			f1.submit();
		}		
		else if (mycode == 119)	  //F8
		{
			f1.action='?p=cashier&p1=PopTender&id=1&tt=Cash';
			f1.submit();
		}		
		else if (mycode == 120)	  //F9
		{
			f1.action='?p=cashier&p1=searchTender';
			f1.submit();
		}		
		else if (mycode == 121)	  //F10
		{
			f1.action='?p=cashier&p1=Finish';
			f1.submit();
		}		

		return false;
	}
	else if (mycode == 38)
	{
		f1.action='?p=cashier&p1=Up';
		f1.submit();
		return false;
	}		
	else if (mycode == 40)
	{
		f1.action='?p=cashier&p1=Down';
		f1.submit();
		return false;
	}				
	return;
}

document.oncontextmenu = mischandler;
document.onmousedown = mousehandler;
document.onmouseup = mousehandler;
document.onkeydown = keyhandler;

//-->
</script>
<?
function nextInvoice()
{
	global $aCashier;
	$q = "select * from invoice where ip='".$aCashier['ip']."'";
	$r = fetch_object($q);
	if ($r)
	{
		$aCashier['invoice'] = str_pad($r->invoice + 1,8,'0', STR_PAD_LEFT);
	}
	else
	{
		$q = "select * from sales_header where ip='".$aCashier['ip']."' order by sales_header_id desc limit 0,1";
		$r = fetch_object($q);
		if ($r)
		{
			$aCashier['invoice'] = str_pad($r->invoice + 1,8,'0', STR_PAD_LEFT);
		}
		else
		{
			$aCashier['invoice'] = '000000001';
		}
	}

	return true;
}

$module = 'cashier';
include_once("stockbalance.php");
include_once('cashier.display.php');
if (!session_is_registered('aCashier'))
{
	session_register('aCashier');
	$aCashier	=	null;
	$aCashier	=	array();
}
if (!session_is_registered('WSale'))
{
	session_register('WSale');
	$WSale      = '0';
}
if (!session_is_registered('aItems'))
{
	session_register('aItems');
	$aItems	=	null;
	$aItems	=	array();
}
if (!session_is_registered('item'))
{
	session_register('item');
	$item=null;
	$item=array();	
}
if ($p1=='Clear')
{
	$aCashier	=	null;
	$aCashier	=	array();
	$aItems		=	null;
	$aItems		=	array();
	$item		=	null;
	$item		=	array();
	$WSale		=	'0';	
}
$aCashier['ip'] = $SYSCONF['IP'];
$aCashier['shift'] = $_REQUEST['shift'];
if ($aCashier['shift'] =='')
{
		$t = localtime();
		$tm = $t[2];		

		if ($tm >= 5 and $tm < 14)
		{
			$shift = 1;
		}
		elseif ($tm >= 14 and $tm < 21) 
		{
			$shift = 2;
		}
		elseif ($tm >= 21 or $tm < 6)
		{
			$shift = 3;
		}
		$aCashier['shift'] = $shift;
}
if ($aCashier['invoice'] == '')
{
	nextInvoice();
	$q = "select * from temp_header where ip='".$SYSCONF['IP']."'";
	$r1 = fetch_assoc($q);
	if ($r1['status']=='A')
	{
		$aCashier[]=$r1;
		$thid=$r1['temp_header_id'];
		$q = "select * from temp_detail where ip='".$SYSCONF['IP']."'";
		$qr = @mysql_query($q) or message(mysql_error());
		$cr=0;
		while ($r2 = @mysql_fetch_assoc($qr))
		{
			if ($r2['status'] !='X')
			{
//				$r2['stock'] = lookUpTableReturnValue('s','stock','stock_id','stock',$r2['stock_id']);
				$r2['qty'] = intval($r2['qty']);
				$cr++;
				$aItems[]=$r2;
			}
		}		
		$aCashier['line_no']=$cr;
	} 
	$ip=$_SERVER['REMOTE_ADDR'];
	$ip = '127.0.0.1';
	$q = "select * from temp_header where ip='127.0.0.1'";
	$qrt = mysql_query($q);
	$rt = mysql_fetch_assoc($qrt);
	if ($rt[invoice] > 0 )
	{
		$aCashier[invstatus]="Invoiced";
	}		
	$q = "update temp_header set status='X', invoice=0 where ip='$ip'";
	$qr	=	@mysql_query($q) or	message(mysql_error());
	$q = "update temp_detail set status='X' where ip='$ip'";
	$qr	=	@mysql_query($q) or	message(mysql_error());
}
if ($aCashier['shift'] == 0 and $p1 !='Shift')
{
	$p1='';
	$textbox = '';
	$t=time();
	$message = "Setup Shift First";
}
if ($aCashier['invoice'] == '' )
{
	nextInvoice();
}
if ($p1	== 'Ok'	&& $aCashier['invstatus'] == 'Invoiced' )
{
	$aItems	=	null;
	$aItems	=	array();
	$aCashier	=	null;
	$aCashier	=	array();
	$aCashier[invstatus] = ' '; 
	$ip='127.0.0.1';	
	$aCashier['ip'] = '127.0.0.1';
	$q = "update temp_header set invoice=0 where ip='127.0.0.1'";
	$qr	=	@mysql_query($q) or	message(mysql_error());
	$q = "update temp_detail set status='X' where ip='127.0.0.1'";
	$qr	=	@mysql_query($q) or	message(mysql_error());	
	nextInvoice();
}
if ($p1	== 'Ok'	&& $textbox!='' && $aCashier['return']=='RETURN')
{
	$textox	=	$_REQUEST['textbox'];
	$ax	=	explode("*",$textbox);
	if (count($ax) > 1)
	{
		$qty = -1*$ax[0];
		$searchitem	=	$ax[1];
	}
	else
	{
		$qty=-1;
		$searchitem	=	$textbox;
	}
	$q = "select 
				sales_detail.stock_id, 
				sales_detail.price, 
				sales_detail.price1,
				sales_detail.cdisc, 
				sales_detail.sdisc, 
				sum(sales_detail.qty-sales_detail.rqty) as taken_qty, 
				stock.stock,
				stock.barcode
			from 
				stock,
				sales_detail,
				sales_header
			where
				sales_header.sales_header_id=sales_detail.sales_header_id and
				sales_detail.sales_header_id='".$aCashier['return_sales_header_id']."' and 
				stock.stock_id=sales_detail.stock_id and
				stock.barcode='$searchitem' 
			group by
				sales_detail.stock_id";
				
	$qr = @mysql_query($q) or message(mysql_error());
	if (@mysql_num_rows($qr) == 0)
	{
		$q = "select * from	stock	where	stock	like '%$searchitem%'";
		$qr	=	@mysql_query($q) or	message	(mysql_error());
		if (@mysql_num_rows($qr))
		{
			$item	=	null;
			$item['textbox'] = $_REQUEST['textbox'];
			$p1	=	'BrowseStock';
		}
		else
		{
			$message = "Item NOT Found in this Receipt+Terminal";
		}	
	}
	else
	{
		$r = @mysql_fetch_assoc($qr);
		if ($r['taken_qty'] < abs($qty))
		{
			$message = "Return Quantity $qty exceeds items taken [ ".$r['taken_qty']." ]";
			$qty = $r['taken_qty'] * -1;
		}
		$r[qty]	=	$qty;
		$today = date('Y-m-d');
		
		if ($WSale == '1') 		$r['amount'] = round($r['price2']*$r['qty'],2);
		else 					$r['amount'] = round($r['price']*$r['qty'],2);
		$r['discount'] = round($r['price1']*$r['qty']	-	$r['amount'],2);
		$r['return_id'] = $aCashier['return_sales_header_id'];

		if ($r['discount'] > 0)
		{
			$perdisc = intval($r['discount']/($r['price1']*$r['qty']))*100;
			$r['stock'] .= '(-'.$perdisc.')';
		}
		if ($r['taxable']	== 'Y')
		{
			If ($aCashier['Type'] != "Tender")
			{					
				if ($SYSCONF[taxrate] == 0)
					$r[taxbase] = 0;	
				else
					$r['taxbase']	=	round($r['amount']/1.12,2);

				$r['tax']	=	$r['amount'] - $r['taxbase'];
			}
		}
		
		if ($item['ctr'] ==	'')
		{
			$aItems[]= $r;
		}
		else
		{


//			$aItems[$item['ctr']-1]	=	$r;
		}	
		$aCashier['line_no'] = count($aItems);
		$aCashier['return_sales_header_id'] = '';
		$aCashier['return'] = '';
		$aCashier['return_invoice'] = '';
		
		$textbox = '';
		$item=null;
		$item=array();	
	}
}
elseif ($p1	== 'Ok'	&& substr($textbox,0,1)=='?' )
{
	$textox	=	substr($_REQUEST['textbox'],1,15);
	$searchitem=trim($textox);
	$q = "select * from stock where	barcode='$searchitem'";
	$r = fetch_assoc($q);
	if (!$r)
	{
		$q = "select * from stock,bar where	bar.stock_id=stock.stock_id and bar.barcode='$searchitem'";
		$r = fetch_assoc($q);
	}
	if (!$r)
	{
		$q = "select * from	stock,bar where	bar.stock_id=stock.stock_id and stock_code='$searchitem' and plu='Y'";
		$r = fetch_assoc($q);
/*		if (!$r)
		{
			$q = "select * from	stock,bar where	bar.stock_id=stock.stock_id and stock	like '%$searchitem%' and plu='Y'";
			$qr	=	@mysql_query($q) or	message	(mysql_error());
			if (@mysql_num_rows($qr) )
			{
				$item	=	null;
				$item['textbox'] = $_REQUEST['textbox'];
				$p1	=	'BrowseStock';
			}	
		}*/
	}
	if (!$r	&& $p1 !=	'BrowseStock')
	{	
		$item	=	null;
		$item['textbox'] = $_REQUEST['textbox'];
		$message="Item Not Found...";
	}	
	elseif ($r)
	{
		$message="Item : ".addslashes($r['stock'])." : ".number_format($r['price1'],2);
	}
	$textbox='';		
}
elseif ($p1	== 'Ok'	&& $textbox!='')
{
//echo 'ctr '.$item['ctr'].'   count '.count($aitems);
	if ($item['ctr'] =='' or $item['ctr'] ==count($aItems))
	{
		$textox	=	$_REQUEST['textbox'];
		$ax	=	explode("*",$textbox);
		if (count($ax) > 1)
		{
			$qty = $ax[0];
			if ($qty < 0)
			{
			  $qty = 1;
			  $ax[0] = 1;
			}  
			$searchitem	=	$ax[1];
		}
		else
		{
			$qty=1;
			$searchitem	=	$textbox;
		}
//stock_id,stock_code,bar.barcode,stock,unit1,fraction2,category_id,taxable from	stock,bar
		
		$q = "select * from stock,bar where	bar.stock_id=stock.stock_id and bar.barcode='$searchitem'";
		$r = fetch_assoc($q);
		if (!$r)
		{
			$q = "select * from	stock where	 stock_code='$searchitem' or barcode='$searchitem' and plu='Y'";
			$r = fetch_assoc($q);
			if (!$r)
			{
				$q = "select * from	stock where	stock	like '%$searchitem%' and plu='Y'";
				$qr	=	@mysql_query($q) or	message	(mysql_error());
				if (@mysql_num_rows($qr) )
				{
					$item	=	null;
					$item['textbox'] = $_REQUEST['textbox'];
					$p1	=	'BrowseStock';
				}	
			}
		}	
		if (!$r	&& $p1 !=	'BrowseStock')
		{	
			$item	=	null;
			$item['textbox'] = $_REQUEST['textbox'];
			$message="Item Not Found...";
		}
		elseif ($r)
		{
  		  $stkled = stockBalance($r[stock_id],'', $mdate,$loc);
		  $balance_qty = $stkled['balance_qty'];
		  if ($balance_qty < $qty)
		  {
		  	message("Balance is ".$balance_qty);
		  } else 
		  {
			$r['qty']	=	$qty;
			if ($aCashier[pricelevel] == 3) $r[price]=$r['price3'];
			if ($aCashier[pricelevel] == 2) $r[price]=$r['price2'];  
			else $r[price] = $r['price1'];
					
//			if ($WSale == '1' && $r['price2'] != 0) $r['price']	=	$r['price2'];
//			else $r['price']	=	$r['price1'];
	//echo ' **** here '.$WSale.$r['price'];		
			
			$today = date('Y-m-d');
			if ($r['date2_promo'] >= $today && $r['date1_promo'] <= $today)
			{
				$r['cdisc'] = $r['promo_cdisc'];
				$r['sdisc'] = $r['promo_sdisc'];
				
				$disc = round($r['price1']*($r['promo_cdisc']/100),2);
				$disc += round($r['price1']*($r['promo_sdisc']/100),2);
				$r['price']	=	round($r['price1'] - $disc,2);
			}
			else
			{
				//insert other promo here;
				$q = "select 
								promo_detail.promo_price, 
								promo_header.cdisc,
								promo_header.sdisc
							from	
								promo_header, promo_detail
							where	
								stock_id='".$r['stock_id']."'	and	
								date_from<='$today'	and	
								date_to>='$today' and
								promo_header.enable='Y'";
				$qpr	=	@mysql_query($q);
				$rr = @mysql_fetch_object($qpr);
				if ($rr)
				{
					$r['price']	=	$rr->promo_price;
					$r['cdisc']	=	$rr->cdisc;
					$r['sdisc']	=	$rr->sdisc;
				}
				if ($aCashier['sdisc'] != '' && $rr->sdisc==0 && $r['consign']=='N') $r['sdisc'] = $aCashier['sdisc'];
				$disc = 0;
				$disc = round($r['price1']*($r['sdisc']/100),2);
				if ($disc != 0) $r['price']	=	round($r['price1'] - $disc,2);
			}		
			
			$r['amount'] = round($r['price']*$r['qty'],2);
			$r['discount'] = round($r['price1']*$r['qty'] - $r['amount'],2);
			if ($r['discount'] > 0)
			{
				$perdisc = intval($r['discount']/($r['price1']*$r['qty'])*100);
				$r['stock'] .= '(-'.$perdisc.'%)';
	//				$r['stock'] .= '(-'.$r['discount'].')';
			}
			
			if ($r['taxable']	== 'Y')
			{
				If ($aCashier['Type'] != "Tender")
				{
					$r['taxbase']	=	round($r['amount']/1.12,2);
					$r['tax']	=	$r['amount'] - $r['taxbase'];
				}
			} else
			{
			}
	
			if ($item['ctr'] =='')
			{
				$aItems[]= $r;
			}
			else
			{
				$aItems[$item['ctr']-1]	=	$r;
			}	
	
			display($r);
			$aCashier['line_no'] = count($aItems);
			$textbox = '';
			$item=null;
			$item=array();
		  }		
		}
	}
}
elseif ($p1=='Shift')
{
/*	$textox	= $_REQUEST['textbox'];
	$t = localtime();
	$shift = $textox;
	if ($shift ==1 and ($aCashier['shift']==3 || $aCashier['shift']==1) and ($t[2] > 6 and $t[2] < 14)) $aCashier['shift'] = $shift;
	elseif ($shift ==2 and ($aCashier['shift']==1 || $aCashier['shift']==2) and ($t[2] > 14 and $t[2] < 22)) $aCashier['shift'] = $shift;
	elseif ($shift ==3 and ($aCashier['shift']==2 || $aCashier['shift']==3) and (($t[2] > 22 and $t[2] < 24) or ($t[2] > 0 and $t[2] < 6))) $aCashier['shift'] = $shift;
	else $shift = 0;*/
}
elseif ($p1=='Cashdraw')
{
	doPrint('<open>');
}
elseif ($p1 == 'Qty')
{
	$id = count($aItems);
	if (count($aItems) == $id)
	{

		$qty = $_REQUEST['textbox'];
	
		if ($qty != '')
		{
			$c=0;
			foreach ($aItems as $temp)
			{
				$c++;
				if ($id == $c)
				{
					$dummy = null;
					$dummy = array();
					$dummy = $temp;
					$dummy['qty']=$qty;
					$dummy['amount'] = round($dummy['price']*$dummy['qty'],2);
					$dummy['discount'] = round($dummy['price1']*$dummy['qty']	-	$dummy['amount'],2);
					
					if ($dummy['taxable']	== 'Y')
					{
						if ($dummy['type'] != "Tender")
						{
							$dummy['taxbase']	=	round($dummy['amount']/1.12,2);
							$dummy['tax']	=	$dummy['amount'] - $dummy['taxbase'];
						}	
					}			
					$aItems[$c-1]=$dummy;
					break;
				}
			}
			$item=null;
			$item=array();
		}
	}	
}
elseif ($p1 == 'Price' && !chkRights2('posEditPrice','medit',$ADMIN['admin_id']))
{
	$message = "You have NO Access Rights to Edit Price";
}
elseif ($p1 == 'Price')
{
	if (count($aItems) == $id)
	{
		$price = $_REQUEST['textbox'];
	
		if ($price != '')
		{
			$c=0;
			foreach ($aItems as $temp)
			{
				if ($aCashier['line_no']-1 == $c)
				{
					$dummy = null;
					$dummy = array();
					$dummy = $temp;
					$dummy['price']=$price;
					$dummy['amount'] = round($dummy['price']*$dummy['qty'],2);
					if ($dummy['discount'] > 0)
					{
						$dummy['discount'] = round($dummy['price1']*$dummy['qty'] - $dummy['amount'],2);
						if ($dummy['discount'] > 0)
						{
								$dummy['stock'] .= '(-'.$dummy['discount'].')';
						}
					}
					if ($dummy['taxable']	== 'Y')
					{
						if ( $dummy['type'] != "Tender")
						{
							$dummy['taxbase']	=	round($dummy['amount']/1.12,2);
							$dummy['tax']	=	$dummy['amount'] - $dummy['taxbase'];
						}	
					}			
					$aItems[$c]=$dummy;
					break;
				}
				$c++;
			}
			$item=null;
			$item=array();
		}
	}	
}

elseif ($p1	== 'cDisc')
{
	$c=count($aItems);   //$aCashier['line_no'];
	$temp = $aItems[$c-1];
	$cdisc1 = $_REQUEST['textbox'];
	
//	$q = "select * from	category where category_id='".$temp['category_id']."'";
//	$r = fetch_object($q);
	if ($cdisc1<100)
	{

			$dummy=	null;
			$dummy=array();
			$dummy=$temp;
			$dummy['cdisc']	=	$cdisc1;
			$dummy['price']	=	round($dummy['price1'] - $dummy['price1']*(($dummy['sdisc']+$dummy['cdisc'])/100),2);
			$dummy['amount'] = round($dummy['price'] * $dummy['qty'],2);
			$dummy['discount'] = round( $dummy['price1']	*	$dummy['qty'],2) -	$dummy['amount'];

			if ($dummy['stock_disc'] != '') $dummy['stock'] = $dummy['stock_disc'];
			else	$dummy['stock_disc'] = $dummy['stock'];
			
			$dummy['stock'] .= ':'.$dummy['price1'].'(-'.$dummy['cdisc'].'%)'.($dummy['sdisc']>0 ? '(-'.$dummy['sdisc'].'%)':'');
			

			$aItems[$c-1]	=	$dummy;
	}
	$item = null;
	$item = array();
}
elseif ($p1 == 'WSale')
{
	$WSale = $_REQUEST['WSale'];
	if ($WSale == '1') $WSale = '0';
	else $WSale = '1';
//echo '^^^ Here '.$WSale;	
}
elseif ($p1	== 'sDisc' && $aCashier['sdisc']==0)
{
	$c=$aCashier['line_no'];
	$temp = $aItems[$c-1];
	
	$q = "select * from	category where category_id='".$temp['category_id']."'";
	$r = fetch_object($q);
	if ($r)
	{
			$dummy=	null;
			$dummy=array();
			$dummy=$temp;
			$dummy['sdisc']	=	$r->sdisc1;
			$dummy['price']	=	round($dummy['price1'] - $dummy['price1']*(($dummy['sdisc']+$dummy['cdisc'])/100),2);
			$dummy['amount'] = round($dummy['price'] * $dummy['qty'],2);
			$dummy['discount'] =round( $dummy['price1']	*	$dummy['qty'],2) -	$dummy['amount'];

			if ($dummy['stock_disc'] != '') $dummy['stock'] = $dummy['stock_disc'];
			else	$dummy['stock_disc'] = $dummy['stock'];
			
			$dummy['stock'] .= ':'.$dummy['price1'].($dummy['cdisc']>0 ? '(-'.$dummy['cdisc'].'%)': '').'(-'.$dummy['sdisc'].'%)';
			
			$aItems[$c-1]	=	$dummy;
	}
	$item = null;
	$item = array();
}
elseif ($p1	== 'cDiscGlobal')
{
	$c=0;
	foreach	($aItems as	$temp)
	{
		if ($temp['cdisc'] !=	'')
		{
			$c++;	
			continue;
		}
		$q = "select * from	category where category_id='".$temp['category_id']."'";
		$r = fetch_object($q);
		if ($r)
		{
			$dummy=	null;
			$dummy=array();
			$dummy=$temp;
			$dummy['cdisc']	=	$r->cdisc1;
			$dummy['price']	=	round($dummy['price1'] - $dummy['price1']*($r->cdisc1/100),2);
			$dummy['amount'] = round($dummy['price'] * $dummy['qty'],2);
			$dummy['discount'] =round( $dummy['price1']	*	$dummy['qty'],2) -	$dummy['amount'];

			$aItems[$c]	=	$dummy;
		}
		$c++;
	}
}
elseif ($p1	== 'sDiscGlobal')
{
	$c=0;
	foreach	($aItems as	$temp)
	{
		if ($temp['sdisc'] !=	'')
		{
			$c++;	
			continue;
		}
		$q = "select * from	category where category_id='".$temp['category_id']."'";
		$r = fetch_object($q);
		if ($r)
		{
			$dummy=	null;
			$dummy=array();
			$dummy=$temp;
			$dummy['sdisc']	=	$r->sdisc1;
			$dummy['price']	=	round($dummy['price1'] - $dummy['price1']*($r->sdisc1/100),2);
			$dummy['amount'] = round($dummy['price'] * $dummy['qty'],2);
			$dummy['discount'] =round( $dummy['price1']	*	$dummy['qty'],2) -	$dummy['amount'];

			$aItems[$c]	=	$dummy;
		}
		$c++;
	}
}
elseif ($p1 == 'searchTender' && $_REQUEST['textbox']!='')
{
	$q = "select * from tender where tender_id='".$_REQUEST['textbox']."'";
	$r = fetch_object($q);

	if ($r)
	{
		echo "<script>location.href='?p=cashier&p1=PopTender&id=$r->tender_id&tt=$r->tender_type'</script>";
		exit;
	}
}
elseif ($p1	== 'PopTender' &&	$tt=='Cash' &&	$id!='')
{
	$textbox = $_REQUEST['textbox'];

	if ($textbox ==	'')
	{
		$message = "Enter Amount";
	}
	else
	{
		$q = "select tender, tender	as barcode,	tender as	unit,	'Tender' as	type,	tender_id,tender_type, bankable	
											from tender	where	tender_id='$id'";
		$r = @fetch_assoc($q);
		$r['amount'] = $_REQUEST['textbox'];

//		if ($item['ctr']	== '')
//		{
//			$aItems[$item['ctr']]=$r;
//		}
//		else
//		{
			$ctr = count($aItems);
			$aItems[$ctr] = $r;
//		}	
		$aCashier['line_no'] = count($aItems);
		$item=null;
		$item=array();	
	}
	$textbox = '';	

}
elseif ($p1	== 'Tender'	&& $id!='')
{
	$amount = $_REQUEST['amount'];
	$account = $_REQUEST['account'];
	$account_id = $_REQUEST['account_id'];
	$pricelevel = $_REQUEST['pricelevel'];
	$carddate = mdy2ymd($_REQUEST['carddate']);
	$cardno = $_REQUEST['cardno'];
	$bank = $_REQUEST['bank'];
	$remark = $_REQUEST['remark'];
	$service_charge = $_REQUEST['service_charge'];
	if ($amount == '')
	{
		$message = "Enter Amount";
	}	
	else
	{
		$r = null;
		$q = "select tender, tender as barcode, 'Tender'	as type, tender_id,	tender_type, bankable,service	 
								from	tender where tender_id='$id'";
		$r = fetch_assoc($q);

		if ($account_id == '' && $r['tender_type'] == 'A')
		{
			$message='Specify Account/Credit Card...';
		}
		else
		{
			if ($r['tender_type'] == 'A')
			{
				$q = "select * from	account	where	account_id='$account_id'";
				$rr=fetch_object($q);
				$account=$rr->account;
				$account_id = $rr->account_id;
				$pricelevel=$rr->pricelevel;
			}
			elseif ($r['tender_type'] == 'G')
			{
				$q = "select * from	giftcheck where	giftcheck ='$cardno'";
				$rr=fetch_object($q);
				$account=$rr->name;
			}
			$r['account_id'] = $account_id;
			$r['pricelevel'] = $pricelevel;
			$r['account']	=	$account;
			$r['amount'] = $amount;
			$r['remark'] = $remark;
			$r['carddate'] = $carddate;
			$r['cardno'] = $cardno;
			$r['bank'] = $bank;
			$r['service_charge'] = $service_charge;
			$r['stock']	=$r['cardno'].'-'.ymd2mdy($r['carddate']).':'.($r['remark']==''?$r['account']:$r['remark']);
			$r['unit'] = $r['account'];
			$r['type'] = 'Tender';
			
			$aCashier['service_charge'] = $service_charge;
	
			$aCashier['account'] = $r['account'];
			$aCashier['pricelevel'] = $r['pricelevel'];
			if ($item['ctr']	== '')
			{
				$aItems[]=$r;
			}
			else
			{
				$aItems[$item['ctr']-1] = $r;
			}		
		}	
	}	
}
elseif ($p1	== 'Edit'	&& $id !=	'')
{
	if (count($aItems) == $id)
	{
		$ctr=0;
		foreach	($aItems as	$temp)
		{
			$ctr++;
			if ($ctr ==	$id)
			{
				if ($temp['type']=='Tender')
				{
					$textbox=$temp['amount'];
				}
				else //if	($temp['type'] ==	'stock')
				{
					$textbox = $temp['qty'].'*'.$temp['barcode'];
				}
				$aCashier['line_no'] = $id;
				$item['ctr'] = $id;
				$item['textbox'] = $textbox;
				break;	
			}
		}
	}
}
elseif ($p1	== 'Delete' && chkRights2('sales','medit',$ADMIN['admin_id']))
{
	$ctr=0;
	$newarray	=	null;
	$newarray	=	array();
	foreach	($aItems as	$temp)
	{
		if ($ctr !=	$aCashier['line_no'])
		{
			$newarray[]	=	$temp;	
		}
		else
		{
			if ($temp['temp_detail_id'] != '')
			{
				$q = "delete from temp_detail where temp_detail_id='".$temp['temp_detail_id']."'";
				$qr =@mysql_query($q);
			}
		}
		$ctr++;
	}
	$aItems	=	null;
	$aItems	=	array();
	$aItems	=	$newarray;

	$item	=	null;
	$item	=	array();
}
elseif ($p1	== 'Void' && !chkRights2('sales','mdelete',$ADMIN['admin_id']))
{
	$message = " You Have NO Access Rights To VOID Transaction";
}
elseif ($p1	== 'Void'	&& $aCashier['sales_header_id']	== ''	&& $_REQUEST['textbox']	== '')
{
	$aItems	=	null;
	$aItems	=	array();
	$aCashier	=	null;
	$aCashier	=	array();
	$item	=	null;
	$item	=	array();
}
elseif ($p1	== 'Void'	&& $_REQUEST['textbox']	!= ''	&& chkRights2('sales','mdelete',$ADMIN['admin_id']))
{
	$textbox = $_REQUEST['textbox'];
	$q = "select * from	sales_header where invoice='$textbox' and ip='".$_SERVER['REMOTE_ADDR']."'";
	$r = fetch_object($q);
	if (!$r)
	{
		$message = " Transaction Docket [ NOT ] Found...";
	}
	else
	{
		$remarks = ';VOID by:'.date('m/d/Y').':'.$ADMIN['username'];
		$q = "update sales_header set status='V', remarks = '$remarks' 
					where invoice='$textbox' and ip='".$_SERVER['REMOTE_ADDR']."'";
		$qr	=	@mysql_query($q) or	message(mysql_error());
		if (!$qr)
		{
			$message = " NO	Transaction	was	Voided";
		}
		else
		{
			//Print	Voided Here
			$audit = 'VOID:'.$ADMIN['username'].' on '.date('m/d/Y g:ia').';';
			
			audit($module, $q, $ADMIN['admin_id'], $audit, $r->sales_header_id);
			$message = "Transaction	$textbox Successfully Voided	";
		}
	}
}
elseif($p1 ==	'Finish' &&	count($aItems)>0 && $aCashier['tender_amount']!=0 && $aCashier['tender_amount']<$aCashier['net_amount'])
{
		$message = 'Amount Tendered is Lacking...';
}
elseif($p1 == 'Finish' and $aCashier['invstatus'] == 'Invoiced')
{
		$aItems	=	null;
		$aItems	=	array();
		$aCashier	=	null;
		$aCashier	=	array();
		$ip='127.0.0.1';	
		$aCashier['ip'] = $ip;		
		nextInvoice();
		$p1 = 'Clear';
}
elseif($p1 ==	'Finish' &&	count($aItems)==0)
{
		$ip='127.0.0.1';
		$q = "update temp_header set status='X', invoice ='".$aCashier['invoice']."' where ip='$ip'";
		$qr	=	@mysql_query($q) or	message(mysql_error());
		$q = "update temp_detail set status='X' where ip='$ip'";
		$qr	=	@mysql_query($q) or	message(mysql_error());
}
elseif($p1 ==	'Finish' &&	count($aItems)>0)
{

	if ($aCashier['tender_amount'] ==	0)
	{
		$q = "select tender,tender	as barcode,	tender as	unit,	'Tender' as	type,	tender_id,tender_type, bankable	from tender	where	tender_type='C'";
		$r = @fetch_assoc($q);
		$r['amount'] = $aCashier['net_amount'];
		$aItems[]	=	$r;
		$aCashier['tender_amount'] = $r['amount'];
	}

	$commit	=	1;
	if ($aCashier['sales_header_id'] ==	'')
	{
		$time	=	date('G:i');
		$date	=	date('Y-m-d');
		$aCashier['time'] = $time;
		$aCashier['date'] = $date;

		$netamt = $ttax = $taxx = 0;
		if ($aCashier['net_amount'] < 0) 
		{
			$netamt = 0;
		}
		else 
		{
			$netamt=$aCashier['net_amount'];
		}	
		if ($aCashier['net_amount'] > 0)
		{
			$ttax = $netamt/1.12;
			$taxx = $netamt - $ttax;
		}	
		if ($aCashier['total_tax'] > $ttax) $aCashier['total_tax'] = $taxx;
		if ($aCashier['vat_sales'] > $taxx) $aCashier['vat_sales'] = $ttax;
		
		$q = "insert into	sales_header (invoice, date,time,status,gross_amount,	
							discount_percent,discount_amount,net_amount,vat_sales, total_tax, 
							service_charge, ip, admin_id, remarks, account_id, shift)
						values
							('".$aCashier['invoice']."','$date','$time','S',	'".$aCashier['gross_amount']."', '".$aCashier['discount_percent']."',
							'".$aCashier['discount_amount']."',	'".$aCashier['net_amount']."', '".$aCashier['vat_sales']."', 
							'".$aCashier['total_tax']."', '".$aCashier['service_charge']."', '".$SYSCONF['IP']."', '".$ADMIN['admin_id']."',
							'".$aCashier['remarks']."','".$aCashier['account_id']."','".$aCashier['shift']."')"; 
		$qr	=	@mysql_query($q) or	message(mysql_error());
		if ($qr	&& @mysql_affected_rows())
		{
			$hid = @mysql_insert_id();
			$aCashier['sales_header_id'] = $hid;

			$audit = 'Encoded by:'.$ADMIN['username'].' on '.date('m/d/Y g:ia').';';
			audit($module, $q, $ADMIN['admin_id'], $audit, $aCashier['sales_header_id']);
			
			$c=0;

// echo print_r($aItems);

			foreach	($aItems as	$temp)
			{
				if ($temp['type']	== 'Tender')
				{
					if ($temp['sales_detail_id'] ==	'')
					{
						$q = "insert into	sales_tender (sales_header_id, tender_id,bank,	
										account_id,	account, cardno, carddate, amount, service_charge, remark)
								values ('".$aCashier['sales_header_id']."',	'".$temp['tender_id']."',	
										'".$aCashier['bank']."','".$aCashier['account_id']."',
										'".$temp['account']."',	'".$temp['cardno']."', '".$temp['carddate']."',	
										'".$temp['amount']."', '".$temp['service_charge']."',	'".$temp['remark']."')";
						$qqr = @mysql_query($q)	or message(mysql_error());
						if ($qqr &&	mysql_affected_rows())
						{
							$dummy = null;
							$dummy = array();
							$dummy = $temp;
							$tid = @mysql_insert_id();
							$dummy['sales_detail_id']	=	$tid;	 //force sales_tender_id to	sales_detail_id
							$aItems[$c]	=	$dummy;
							
							if ($temp['tender_type'] == 'G')
							{
								$qc = "update giftcheck set credit = credit+'".$temp['amount']."' where giftcheck='".$temp['cardno']."'";
								$qcr = @mysql_query($qc) or message(mysql_error());
							}
						}
					}	
				}	
				else
				{

					if ($temp['sales_detail_id'] ==	'')
					{
//						if ($temp['tax'] < 0) $temp['tax'] = 0;
						$cost = lookUpTableReturnValue('x','stock','stock_id','cost1',$temp['stock_id']);
						$q = "insert into	sales_detail (sales_header_id, stock_id, qty,	price1,	price, cdisc,	sdisc,
									 discount, amount, tax, cost)
								values ('".$aCashier['sales_header_id']."',	'".$temp['stock_id']."', '".$temp['qty']."',
										'".$temp['price1']."', '".$temp['price']."', '".$temp['cdisc']."', '".$temp['sdisc']."',
										'".$temp['discount']."', '".$temp['amount']."',	'".$temp['tax']."','$cost')";
						$qqr = @mysql_query($q)	or message(mysql_error());
						if ($qqr &&	mysql_affected_rows())
						{
							$dummy = null;
							$dummy = array();
							$dummy = $temp;
							$did = @mysql_insert_id();
							$dummy['sales_detail_id']	=	$did;
							$aItems[$c]	=	$dummy;
						}
										
					}
					
				}
				$c++;
			}
		}
		else
		{
			$commit	=	0;
		}					
		$ip='127.0.0.1';
		$q = "update temp_header set status='X', invoice ='".$aCashier['invoice']."' where ip='$ip'";
		$qr	=	@mysql_query($q) or	message(mysql_error());
		$q = "update temp_detail set status='X' where ip='$ip'";
		$qr	=	@mysql_query($q) or	message(mysql_error());
	}
	if ($commit	== 1)
	{
		include_once('cashier.receipt.print.php');
		
		$q = "update invoice set invoice='".$aCashier['invoice']."' where ip='".$aCashier['ip']."'";
		$qr = @mysql_query($q) or message("Unable to update invoice sequence...".mysql_error());
		if (mysql_affected_rows() == 0)
		{
			$q = "insert into invoice set invoice='".$aCashier['invoice']."', ip='".$aCashier['ip']."'";
			$qr = @mysql_query($q) or message("Unable to insert invoice sequence...".mysql_error());
		}
		if ($aCashier['suspend_header_id'] != '')
		{
			$q = "update suspend_header set status='S' where suspend_header_id='".$aCashier['suspend_header_id']."'";
			@mysql_query($q) or message(mysql_error());
		
			$audit = 'Suspend Finished by:'.$ADMIN['username'].' on '.date('m/d/Y g:ia').';';
			audit($module.'_suspend', $q, $ADMIN['admin_id'], $audit, $aCashier['suspend_header_id']);
		
		}	
		$ip = $aCashier['ip'];
		$shift=$aCashier['shift'];
/*		$aItems	=	null;
		$aItems	=	array();
		$aCashier	=	null;
		$aCashier	=	array();*/
		$aCashier['ip'] = $ip;
		$aCashier['shift'] = $shift;

		//$message = "Transaction Saved...";
		$aCashier['invstatus'] ='Invoiced';	
		
	}	
//	nextInvoice();
}
elseif ($p1 == 'RePrint')
{
		$old_aItems = $aItems;
		$old_aCashier = $aCashier;

		$aItems = null;
		$aItems = array();
		$aCashier = null;
		$aCashier = array();
		
		$id = $_REQUEST['textbox'];
		if ($id == '')
		{
				$q = "select * from sales_header where ip='".$SYSCONF['IP']."' order by sales_header_id desc limit 0,1";
		}
		else
		{		
			//$q = "select * from sales_header where sales_header_id='$id'";
			$q = "select * from sales_header where ip='".$SYSCONF['IP']."' and invoice='$id'";
		}	
		
		$r = @fetch_assoc($q);
		if (!$r)
		{
				$message = "Receipt NOT Found, or Generated from other Counter";
		}
		else
		{
			$aCashier = $r;
			$aCashier['nonvat_sales'] = $aCashier['net_amount'] - $aCashier['vat_sales'];
			$q = "select 
								stock.stock, 
								stock.barcode, 
								sales_detail.price1,
								sales_detail.price,
								sales_detail.qty,
								sales_detail.cdisc,
								sales_detail.sdisc,
								sales_detail.discount,
								sales_detail.tax,
								sales_detail.qty,
								sales_detail.amount
					from 
								sales_detail,
								stock 
					where 
							stock.stock_id=sales_detail.stock_id and 
							sales_detail.sales_header_id='".$aCashier['sales_header_id']."'";
			$qr = @query($q) or message(mysql_error());
			while ($r = @mysql_fetch_assoc($qr))
			{
				$aItems[] = $r;
			}
			
			$q = "select
								account_id,
								account,
								cardno,
								carddate,
								sales_tender.tender_id,
								tender.tender as barcode,
								concat(cardno,'-',carddate) as stock,
								'Tender' as type,
								amount,
								tender.tender,
								tender.tender_type
						from 
								sales_tender,
								tender
						where
								tender.tender_id=sales_tender.tender_id and 
								sales_header_id='".$aCashier['sales_header_id']."'";
								
			$qr = @query($q) or message(mysql_error());
			
			while ($r = @mysql_fetch_assoc($qr))
			{
				$aItems[] = $r;
				$aCashier['tender_amount'] += $r['amount'];
			}
			
			include_once('cashier.receipt.print.php');
		}
		
		//restore variables;
		$aItems = null;
		$aItems = array();
		$aItems = $old_aItems;
		$aCashier = null;
		$aCashier = array();
		$aCashier = $old_aCashier;
}
elseif ($p1 == 'returnReceipt' && chkRights2('sales','mdelete',$ADMIN['admin_id']))
{
	
	$q = "select * from sales_header where invoice='$id' and ip='".$_SERVER['REMOTE_ADDR']."'";
	$qr = @mysql_query($q) or message(mysql_error());
	if (@mysql_num_rows($qr) > 0)
	{
		$r = @mysql_fetch_object($qr);
		$aCashier['return_sales_header_id'] = $r->sales_header_id;
		$aCashier['return_invoice'] = $r->invoice;
		$aCashier['return'] = 'RETURN';
	}
	else
	{
		$message = 'Receipt NOT Found...';
	}
}
elseif ($p1 == 'ODraw')
{
	$message = "Open Cash Drawer";
	doPrint("<open>",$SYSCONF['RECEIPT_PRINTER_DEST']);
	doPrint("<open>",$SYSCONF['RECEIPT_PRINTER_DEST']);
}
elseif ($p1 == 'claimSalesOrder')
{
	$q = "select 
				stock.stock,
				stock.barcode,
				stock.stock_id,
				stock.taxable,
				stock.category_id,
				so_detail.qty_out as qty,
				so_detail.price,
				so_detail.price as price1,
				so_detail.amount
			from 
				so_header,
				so_detail,
				stock
			where
				so_header.so_header_id=so_detail.so_header_id and
				stock.stock_id=so_detail.stock_id and
				so_header.so_header_id='$id'";
				
	$qr = @mysql_query($q) or message(mysql_error());
	if (@mysql_num_rows($qr) == 0)
	{
		$message = "Sales Order NOT Found...";
	}
	else
	{
		while ($r = @mysql_fetch_assoc($qr))
		{
			if ($r['type'] == 'Tender') continue;
			if ($r['taxable']	== 'Y')
			{
				$r['taxbase']	=	round($r['amount']/1.12,2);
				$r['tax']	=	$r['amount'] - $r['tax'];
			}
		
			$aItems[] = $r;
		}
	}
}
elseif ($p1 == 'Down')    //'Up'
{
	if ($aCashier['line_no'] > 0) $aCashier['line_no']--;
	
	$item = null;
	$item = array();
	$item = $aItems[$aCashier['line_no']];
//	$item['textbox']=$item['qty'].'*'.$item['barcode'];
	$item['ctr'] = $aCashier['line_no']+1;
}	
elseif ($p1 == 'Up')     //'Down')
{
	if ($aCashier['line_no'] < count($aItems)) $aCashier['line_no']++;

	$item = null;
	$item = array();
	if ($aCashier['line_no'] < count($aItems))
	{
		$item = $aItems[$aCashier['line_no']];
//		$item['textbox']=$item['qty'].'*'.$item['barcode'];
		$item['ctr'] = $aCashier['line_no']+1;
	}
}
elseif ($p1 == 'Suspend' && count($aItems)>0)
{
	if ($_REQUEST['remarks'] != '') $aCashier['remarks'] = $_REQUEST['remarks'];
	$ok=1;
	$date = date('Y-m-d');
	$time	=	date('G:i');
	if ($aCashier['suspend_header_id'] == '')
	{
//		if ($aCashier['total_tax'] < 0) $aCashier['total_tax'] = 0;
		
		$q = "insert into suspend_header (invoice, date,time,status,gross_amount,	
							discount_percent,discount_amount,net_amount,vat_sales, total_tax,ip, admin_id, remarks)
						values
							('','$date','$time','U',	'".$aCashier['gross_amount']."', '".$aCashier['discount_percent']."',
							'".$aCashier['discount_amount']."',	'".$aCashier['net_amount']."', '".$aCashier['vat_sales']."', 
							'".$aCashier['total_tax']."',	'".$_SERVER['REMOTE_ADDR']."', '".$ADMIN['admin_id']."', '".$aCashier['remarks']."')"; 
		$qr	=	@mysql_query($q) or	message(mysql_error());
		$id = @mysql_insert_id();
		$aCashier['suspend_header_id'] = $id;
	}	
	if ($aCashier['suspend_header_id'] != '')
	{
		$audit = 'Suspended by:'.$ADMIN['username'].' on '.date('m/d/Y g:ia').';';
		audit($module.'_suspend', $q, $ADMIN['admin_id'], $audit, $aCashier['suspend_header_id']);

		$c=0;
		foreach	($aItems as	$temp)
		{
			if ($temp['suspend_detail_id'] ==	'')
			{
				$q = "insert into suspend_detail (suspend_header_id, stock_id, qty, price1, price, cdisc,	sdisc, discount, amount, tax)
						values ('".$aCashier['suspend_header_id']."',	'".$temp['stock_id']."', '".$temp['qty']."',
								'".$temp['price1']."', '".$temp['price']."', '".$temp['cdisc']."', '".$temp['sdisc']."',
								'".$temp['discount']."', '".$temp['amount']."',	'".$temp['tax']."')";
				$qqr = @mysql_query($q)	or message(mysql_error());
			}
			else
			{
				$q = "update suspend_detail set
							stock_id='".$temp['stock_id']."',
							price1 = '".$temp['price1']."',
							price = '".$temp['price']."',
							amount = '".$temp['amount']."',
							discount = '".$temp['discount']."',
							tax = '".$temp['tax']."',
							qty = '".$temp['qty']."',
							cdisc = '".$temp['cdisc']."',
							sdisc = '".$temp['sdisc']."'
						where
							suspend_detail_id ='".$temp['suspend_detail_id']."'";
				$qr =@mysql_query($q) or message(mysql_error());
							
			}
			$c++;
		}	
	}
	else
	{
		$ok = 0;
		$message = 'Suspend Sale FAILED! No Header was created!';
	}
	if ($ok == 1)
	{
		$aCashier=null;
		$aCashier=array();
		$aItems = null;
		$aItems = array();
		$item = null;
		$item = array();
		$message = 'Suspend Sale Successful';
		nextInvoice();
	}
}
elseif ($p1 == 'restoresuspend' && $id !='')
{
	$aCashier=null;
	$aCashier=array();
	$aItems = null;
	$aItems = array();
	$item = null;
	$item = array();

	$q = "select * from suspend_header where suspend_header_id='$id'";
	$qr = @mysql_query($q) or message(mysql_error());
	if (@mysql_num_rows($qr) == 0)
	{
		$message = 'Suspended Docket NOT found...';
	}
	else
	{
		$r = @mysql_fetch_assoc($qr);
		$aCashier = $r;
		$q = "select * from suspend_detail where suspend_header_id = '$id'";
		$qr = @mysql_query($q) or message(mysql_error());
		while ($r = @mysql_fetch_assoc($qr))
		{
			$r['stock'] = lookUpTableReturnValue('s','stock','stock_id','stock',$r['stock_id']);
			$r['barcode'] = lookUpTableReturnValue('s','stock','stock_id','barcode',$r['stock_id']);
			$r['qty'] = number_format($r['qty'],3);
			$aItems[] = $r;
		}
		nextInvoice();
	}
}
elseif ($p1 == 'Remove Checked')
{
	$ok=1;
	for($c=0;$c<count($mark);$c++)
	{
		$id = $mark[$c];
		$q = "update suspend_header set status='V' where suspend_header_id='$id'";
		$qr = @mysql_query($q) or message(mysql_error());
		if (!$qr) $ok=0;
	}
	if ($ok == 1) $message = 'Suspended Sales Removed...';
	else $message = 'Errors occurred while Removing Suspended Sales';
}
elseif ($p1 == 'Remarks')
{
	$aCashier['remarks'] = $_REQUEST['remarks'];
}

?>

<body	bgcolor="#CCCCCC"	leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form	name="f1"	method="post"	action="?p=cashier&p1=Ok">
	<table width="100%"	height="99%" border="0"	cellpadding="0"	cellspacing="0">
		<tr	height="5%"> 
			
      <td	width="82%"	height="1%"	bgcolor="#CCCCCC"><strong><font size="6">&nbsp;<?=$SYSCONF['BUSINESS_NAME'];?></font></strong>
      <br><font size="3"	face="Verdana, Arial,	Helvetica, sans-serif">&nbsp;<?=$SYSCONF['BUSINESS_ADDR'];?><br></font></td>		
      <td	width="18%"	align="center" bgcolor="#CCCCCC"> <font size="2">
        <img alt="Alt+L >> Home Page" accesskey="H" onClick="f1.action='?p=logout';f1.submit()" src="graphics/home.gif" width="21" height="10">
        | User: 
        <?=	$ADMIN['username'];?>
        <br>
        <?=	date('F	d, Y');?> [ <a href="?p=logout">Logout</a> ]
        </font></td>
		</tr>
		<tr	height="80%">	
			<td	valign="top">
		<table width="100%"	height="100%"	border="0" cellspacing="0" cellpadding="0" >
          <tr	height="5%"> 
            <td	colspan="5" bgCOlor="#C6C6C6"><font	size="5">Item</font> 
              <input name="textbox" type="text" id="textbox" style="font-size:25; font-family: 'Times New Roman'; border:1 solid #CCCCFF; background-color:#EFEFEF;" value="<?= $item['textbox'];?>" size="25" onFocus="document.getElementById('Ok').disabled=0">
              <input type="submit" name="p1" value="Ok"	id='Ok' style="font-size:20; font-family:	'Times New Roman'">
			  <input name="ctr" id="ctr" type="hidden" value="<?=$item['ctr'];?>">
			  <?
			  if ($aCashier['return'] == 'RETURN')
			  {
			  	echo " Return From Receipt#:[".$aCashier['return_invoice'].'] Scan Item ...';
			  }
			  ?>            </td>
          </tr>
          <tr	height="1%" bgcolor="#C0C0C0"> 
            <td	width="10%" align="center"><strong><font size="2"	face="Verdana, Arial,	Helvetica, sans-serif">Qty</font></strong></td>
            <td	width="19%" align="center"><strong><font	size="2" face="Verdana,	Arial, Helvetica,	sans-serif"> 
              BarCode</font></strong></td>
            <td	width="44%"><strong><font	size="2" face="Verdana,	Arial, Helvetica,	sans-serif">Item 
              Description</font></strong></td>
            <td	width="12%"	align="center"><strong><font size="2"	face="Verdana, Arial,	Helvetica, sans-serif">Price</font></strong></td>
            <td	width="15%"	align="center"><strong><font size="2"	face="Verdana, Arial,	Helvetica, sans-serif">Amount</font></strong></td>
          </tr>
          <tr	height="50%"> 
            <td	colspan="5"	 valign="top"> 
              <?
			if ($p1 == 'TAudit' && !chkRights2('taudit','mview',$ADMIN['admin_id']))
			{
				$message = 'Your access does not allow Printing of Transaction Audit. Choose Cash Count Breakdown Instead';
		  	}
			elseif ($p1 == 'TAudit')
			{
				$item=null;
				$item=array();
				$rtype='D';
				include_once('cashier.taudit.php');
			}
			elseif ($p1 == 'ZRead')
			{
				$item=null;
				$item=array();
				include_once('cashier.zread.php');
			}
			else
			{
				include_once('cashier.grid.php');
			}	
			?>
              <!--			<iframe	src='x.php'	height="100%"	width="99%"	frameborder="1"	marginheight="1" marginwidth="1" hspace="1"	vspace="1" scrolling="yes">
			</iframe>
-->            </td>
          </tr>
          <tr	height="7%"> 
            <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><a href="?p=cashier&p1=Remarks">Customer:</a> 
              </strong>
<input type="image" src="graphics/b_edit.png" width="16" height="16" alt="Alt+N: Customer Name/Remark" accesskey="N" onClick="f1.action='?p=cashier&p1=Remarks';f1.submit();">
<input name="pricelevel" id="pricelevel" type="hidden" value="<?=$aCashier['pricelevel'];?>">
<br>
              &nbsp;&nbsp; <font color="#CC0000" size="2"><b> 
              <?= ($aCashier['account'] != '' ? $aCashier['account'] : $aCashier['remarks']);?>
              </b> </font></font></td>
            <td	colspan="3"	align="right" valign="top"><font	size="15"> 
              <?

				if ($aCashier['gross_amount']<0) 
				{ 
				     $gross_amt=0; 
				}	 	
				else $gross_amt=$aCashier['gross_amount'];
				
				if ($aCashier['net_amount']<0) $net_amt=0; 
				else $net_amt=$aCashier['net_amount'];

				if ($aCashier['tender_amount'] > $aCashier['net_amount'])
				{
					if ($aCashier['net_amount'] > 0)
					{
  					    $due = "Change....".number_format($aCashier['tender_amount'] - $aCashier['net_amount'],2);
					}	
					else
					{
						$due = "Amount Due....".number_format(0.00,2);
					}
				}
				else
				{
					if ($net_amt > 0 and $aCashier['tender_amount'])
					{
  					    $due = "Change....".number_format($aCashier['tender_amount'] - $aCashier['net_amount'],2);
					}
					elseif ($net_amt>0) 
					{
						$due = "Amount Due....".number_format($net_amt,2);
					}
				}

//				if ($aCashier['vat_sales'] < 0) $aCashier['vat_sales']=0;
//				if ($aCashier['total_tax'] < 0) $aCashier['total_tax']=0;
			
				$ttax = $aCashier['total_tax'];
				$tvatsales = $aCashier['vat_sales'];
				if ($aCashier['nonvat_sales'] < 0) $tnonvatsales = 0;
				
				if ($aCashier['gross_amount'] < 0) 
				   {
				   	  $ttax=0;
				   	  $tvatsales=0;
				   	  $tnonvatsales=0;
					}
				?>
				<?=$due?>
              </font></td>
            <td width="0%"	align="right">&nbsp;</td>
          </tr>
          <tr	height="13%" valign="top"> 
            <td	colspan="6"> 
              <table	width="100%" border="0"	cellpadding="1"	cellspacing="1">
                <tr> 
                  <td	width="24%">Tax Base</td>
                  <td	width="20%"	align="right"> 
                    <?=	number_format($tvatsales,2);?>                  </td>
                  <td	width="10%"	align="right">&nbsp;</td>
                  <td	width="30%">Gross Sales</td>
                  <td	width="16%"	align="right"> 
                    <?=number_format($gross_amt,2);?>                  </td>
                </tr>
                <tr> 
                  <td>VAT</td>
                  <td	align="right"> 
                    <?=	number_format($ttax,2);?>                  </td>
                  <td	align="right">&nbsp;</td>
                  <td>Discount</td>
                  <td	align="right"> 
                    <?=	number_format($aCashier['discount_amount'],2);?>                  </td>
                </tr>
                <tr> 
                  <td>NON VAT</td>
                  <td	align="right"> 
                    <?=	number_format($aCashier['nonvat_sales'],2);?>                  </td>
                  <td	align="right">&nbsp;</td>
                  <td>Service Charge</td>
                  <td	align="right"> 
                    <?=	number_format($aCashier['service_charge'],2);?>                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td	align="right">&nbsp;</td>
                  <td	align="right">&nbsp;</td>
                  <td>NET SALES</td>
                  <td	align="right">
                    <?=	number_format($net_amt,2);?>                  </td>
                </tr>
              </table>            </td>
          </tr>
        </table></td>
			<td	rowspan="3"	valign="top">
			<table	width="100%" border="0"	cellpadding="1"	cellspacing="1" bgcolor="#CCCCCC">
			<tr>
            <td colspan="3"><font size="2">Docket:<b> 
              <?= $aCashier['invoice'];?>
              &nbsp;</b>Terminal:<b>
              <?= $SYSCONF['TERMINAL'];?>
              </b></font> </td>
          </tr>
	  <tr><td><font size="3" face="Arial, Helvetica, sans-serif">&nbsp;</font></td></tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td width="74%">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">F4 - Quantity</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td width="74%">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">F3 - Delete Line</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td width="74%">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">F5 - Line Discount</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td	width="74%">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">F7 - Shift</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td	width="74%">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">F8 - Cash</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td	width="74%">&nbsp;<font	size="2" face="Verdana,	Arial, Helvetica,	sans-serif">F9 - Tender</font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td	width="74%"><font	size="2" face="Verdana,	Arial, Helvetica,	sans-serif">F10 - Finish 
              </font></td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%">&nbsp;</td>
            <td	width="74%">&nbsp;</td>
          </tr>
          <tr> 
            <td	width="10%">&nbsp;</td>
            <td	width="9%"></td>
            <td	width="20%">SHIFT No.&nbsp;
		<input name="shift" readonly id="shift" value="<?= $aCashier['shift'];?>" size="2">
	    </td>
          </tr>
          <tr align="right"> 
            <td height="61" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#CC0000" size="2"><b> 
              </b></font></font> </td>
          </tr>
          <tr> 
            <td colspan="3"	align="center"><input name="image4" type="image" accesskey="S" onClick="f1.action='?p=cashier&p1=SuspendTry';f1.submit();" src="graphics/altS.jpg" alt="Suspend Transaction" width="60" height="36">
              <input type="image" src="graphics/altV.jpg" width="60" height="36" accesskey="V" onClick="if(confirm('Are you sure to VOID this transaction?')){f1.action='?p=cashier&p1=Void';f1.submit();}" alt="Void Transaction">
              <input name="image42" type="image" accesskey="P" onClick="if(confirm('Confirm Re-Print of Receipt?')){f1.action='?p=cashier&p1=RePrint';f1.submit();}" src="graphics/altP.jpg" alt="RePrint Receipt" width="60" height="36">
              <input name="image422" type="image" accesskey="C" onClick="f1.action='?p=cashier&p1=CashCount';f1.submit();" src="graphics/altC.jpg" alt="Cash Count" width="60" height="36">
              <input name="image42222" type="image" accesskey="X" onClick="if(confirm('Confirm Transaction Audit (X-Read)?')){f1.action='?p=cashier&p1=TAudit';f1.submit();}" src="graphics/altX.jpg" alt="Transaction Audit :: X-Reading" width="60" height="36">
              <input name="image4222" type="image" accesskey="Z" onClick="if(confirm('Confirm Transaction Closing?')){f1.action='?p=zreading';}" src="graphics/altZ.jpg" alt="Z-Reading" width="60" height="36">
	      <input name="image4222" type="image" accesskey="O" onClick="f1.action='?p=cashier&p1=ODraw';f1.submit()" src="graphics/altZ.jpg" alt="ODraw" width="0" height="0">
	     </td>
          </tr>
 
          <tr> 
            <td	colspan="3" align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif">
              <input name="status" id="status" type="text" readonly value="<?= $aCashier['invstatus'];?>" size="20" style="text-align:center">
            </font></td>
          </tr>
          <tr> 
            <td	colspan="3" align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Items 
              <input name="itemscnt" id="itemscnt" type="text" readonly value="<?= $itemscnt;?>" size="10" style="text-align:center">
            </font></td>
          </tr>
          <tr> 
            <td colspan="3" align="center">&nbsp;</td>
            <input type="image" src="graphics/blue_bullet.gif" width="1" height="1" onClick="window.location='?p=logout'" accesskey="L">
  <input type="image" src="graphics/blue_bullet.gif" width="1" height="1" onClick="f1.action='?p=';f1.submit();" accesskey="H">
          </tr>
        </table></td>
		</tr>
	</table>
<?

if ($p1	== 'PopTender')
{
	$amount = 0;
	foreach ($aItems as $temp)
	{
		if ($temp['type'] == 'Tender')
		{
			$amount -= $temp['amount'];
		}
		else
		{
			$amount += $temp['amount'];
		}
	}

	if ($tt	== 'C')
	{
		include_once('cashier.cash.php');
	}	
	if ($tt	== 'K')
	{
		include_once('cashier.cheque.php');
	}	
	elseif ($tt	== 'B')
	{
		include_once('cashier.bankcard.php');
	}	
	elseif ($tt	== 'A')
	{
		include_once('cashier.account.php');
	}
	elseif ($tt	== 'F')
	{
		include_once('cashier.foreign.php');
	}
	elseif ($tt	== 'G')
	{
		include_once('cashier.gc.php');
	}
	else
	{
		echo "<script> document.getElementById('textbox').focus()</script>";
	}	
}		
elseif ($p1	== 'BrowseStock')
{
		include_once('cashier.searchstock.php');
}
elseif ($p1 == 'searchAccount')
{
		$q = "select * from account where account_id = '$cardno'";
		$r = fetch_object($q);
		$ok=0;
		if ($r>0)
		{
			$account = $r->account;
			$account_id= $r->account_id;
			$credit_limit = $r->credit_limit;
			$sdisc = $r->sdisc;
			$pricelevel = $r->pricelevel;
			$aCashier['account_id']=$account_id;
			$aCashier['account']=$account;
			$aCashier['pricelevel']=$pricelevel;
			$aCashier['sdisc']=$sdisc;			
			$ok=1;
			
		}	
		elseif (intval($cardno) > '0')
		{
			$q = "select * from account where account_id = '$cardno'";
			$r = fetch_object($q);
			if ($r>0)
			{
				$account = $r->account;
				$account_code = $r->account_code;
				$cardno = $r->account_id;
				$account_id= $r->account_id;
				$credit_limit = $r->credit_limit;
				$sdisc = $r->sdisc;
				$aCashier['account_id']=$account_id;
				$aCashier['account']=$account;
				$aCashier['sdisc']=$sdisc;			
				$ok=1;
			}	
		}
		elseif (strlen($cardno)>0)
		{		
			$ok=2;
		}
		$amount = $_REQUEST['amount'];
		if ($ok<2)
		{
			include_once('cashier.account.php');
		}
		else
		{
			$account_id='';
			$account='';
			include_once('cashier.account.php');
			include_once('cashier.searchaccount.php');
		}
}
elseif ($p1 == 'searchAccash')
{
		$q = "select * from account where account_id = '$cardno'";
		$r = fetch_object($q);
		$ok=0;
		if ($r>0)
		{
			$account = $r->account;
			$account_id= $r->account_id;
			$credit_limit = $r->credit_limit;
			$sdisc = $r->sdisc;
			$ok=1;
		}	
		elseif (intval($cardno) > '0')
		{
			$q = "select * from account where account_id = '$cardno'";
			$r = fetch_object($q);
			if ($r>0)
			{
				$account = $r->account;
				$account_code = $r->account_code;
				$cardno = $r->account_id;
				$account_id= $r->account_id;
				$credit_limit = $r->credit_limit;
				$sdisc = $r->sdisc;				
				$ok=1;
			}	
		}
		elseif (strlen($cardno)>0)
		{
			$ok=2;
		}
		$amount = $_REQUEST['amount'];
		if ($ok<2)
		{
			include_once('cashier.cash.php');
		}
		else
		{
			$account_id='';
			$account='';
			include_once('cashier.cash.php');
			include_once('cashier.searchaccash.php');
		}
}
elseif ($p1 == 'searchGC')
{
		$q = "select * from giftcheck where giftcheck = '$cardno'";

		$r = fetch_object($q);
		if ($r>0)
		{
			$account = $r->name;
			$account_id= $r->giftcheck_id;
			$credit_limit = $r->amount;
			$sdisc = $r->sdisc;
			$account_balance = $credit_limit - $r->credit;
		}	
		else
		{
			$account = '';
			$focus='cardno';
			$msg="Gift Check NOT found...";
		}
		include_once('cashier.gc.php');
}
elseif ($p1	== 'searchTender')
{
		include_once('cashier.searchtender.php');
}
elseif ($p1	== 'SuspendTry')
{
		include_once('cashier.suspend.php');
}
elseif ($p1	== 'Remarks')
{
		include_once('cashier.account.php');
}
elseif ($p1	== 'CashCount')
{
		echo "<script>window.open('cashcount.php','ccWin','left=50, top=50, height=370, width=450 ,location=no, status=0, scrollbars=1,resizable=1')</script>";
}

else
{
	echo "<script> document.getElementById('textbox').focus()</script>";
}
?>
</form>
<?
if ($message !=	'')
{
	echo "<script>alert('$message');</script>";
}
?>
