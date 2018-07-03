<?
if (!chkRights2('salesreports','mview',$ADMIN['admin_id']))
{
	message('You are NOT allowed to view Reports...');
	exit;
}

if ($p1=="") 
{
	$sd = date("m/d/Y");
	$ed = date("m/d/Y");
	$q = "select date from sales_header order by date desc limit 0,1";
	$qr = mysql_query($q) or die (mysql_error());
	if (mysql_num_rows($qr) > 0)
	{
		$r = mysql_fetch_object($qr);
		$sd = ymd2mdy($r->date);
		$ed = ymd2mdy($r->date);
	}	
}
if ($p1=="Go" || $p1 == 'Print' || $p1 == 'Print Draft' || $p1 == 'Export Excel')
{
	$msd=mdy2ymd($sd);
	$med=mdy2ymd($ed);
	
  if ($source == 'P' or $source == 'A')
  {
	if ($rtype == 'S')
	{
		$q = "select 
					*
			from 
					sales_header
			where 
					date>='$msd'  and 
					date<='$med'  
			order by 
					date, 
					admin_id";
	}
	else
	{
		$q = "select 
					*
			from 
					sales_header,
					sales_detail,
					stock
			where 
					sales_header.sales_header_id=sales_detail.sales_header_id and
					sales_detail.stock_id=stock.stock_id and
					date>='$msd'  and 
					date<='$med'  
			order by 
					date,
					admin_id";
	}		
		$qr = @mysql_query($q) or message (mysql_error());
	
	$header = "\n\n\n";
	if ($p1 == 'Print Draft')
		$header = "<small3>";
	if ($p1 =='Export Excel')
		include("excelwriter.inc.php");	
		
	$page=1;
	$header .= center($SYSCONF['BUSINESS_NAME'],88)."\n";
	$header .= center('DAILY SALES REPORT',88)."\n";
	$header .= center('Transaction Date '.$sd.' To '.$ed,88)."\n\n";
	
	$header .= str_repeat('-',88)."\n";
	$header .= '   #     Invoice    Time                Gross     Discount  Net Amount      '."\n";
	if ($rtype == 'D')
		$header .= '       /Product Description             /Qty      /Sales'."\n";
	$header .= str_repeat('-',88)."\n";

	if ($p1 =='Export Excel')
	{
		$excel=new ExcelWriter("/home/excel/DailySales.xls");
		$myArr = array('','DAILY SALES REPORT','');
		$excel->writeLine($myArr);
		$trdate = 'Transaction Date '.$sd.' To '.$ed;
		$myArr = array('',$trdate,'','','');
		$excel->writeLine($myArr);
		$excel->writeRow();
		$myArr = array(' # ','Invoice','Time','Gross','Discount','Net Amount');
		$excel->writeLine($myArr);
		if ($rtype == 'D')
		{
			$myArr = array('','/Product Description','','/Qty','Sales','');
			$excel->writeLine($myArr);
		}
	}
	
	$lc=12;
  	$total_gross = $total_discount = $total_net = 0;
  	$sub1_gross = $sub1_discount = $sub1_net = 0;
  	$sub2_gross = $sub2_discount = $sub2_net = 0;
	$total_charges = 0;
	$msales_header_id='x';
	$ictr=0;
	$mdate='';
	$madmin_id='';
	$aCashierSales = array();

  	while ($r = mysql_fetch_object($qr)) 
	{
		$flag = 0;
		if ($ttype !='S')
		{
			$shid = $r->sales_header_id;
			$qq = "select * from sales_tender, tender 
							where sales_tender.tender_id=tender.tender_id and sales_tender.sales_header_id = '$shid'";
			$qqd = mysql_query($qq);
			$flag=1;
			while ($rq = mysql_fetch_object($qqd))
			{
				if ($ttype=='Y')
				{
					if ($rq->bankable=='Y') $flag=0;
				}	
				else
				{					
					if ($rq->bankable=='N') $flag=0;
				}	
			}			
		}
		if ($flag==1)
			continue;
		if ($mdate !=  $r->date)
		{
			if ($mdate != '') 
			{
				if ($madmin_id != '')
				{
					$details .= space(8).str_pad("CASHIER SUB-TOTAL",26,'.').
							adjustRight(number_format($sub1_gross,2),12).' '.
							adjustRight(number_format($subl_discount,2),10).' '.
							adjustRight(number_format($sub1_net,2),12)."\n";
					$lc += 2;		
					if ($p1 == 'Export Excel')
					{
						$myArr = array('','CASHIER SUB-TOTAL','',$sub1_gross,$sub1_discount,$sub1_net);
						$excel->writeLine($myArr);
					}
				}

				$details .= space(8).str_pad("DATE SUB-TOTAL",26,'.').
						adjustRight(number_format($sub2_gross,2),12).' '.
						adjustRight(number_format($sub2_discount,2),10).' '.
						adjustRight(number_format($sub2_net,2),12)."\n";
				$details .= "\n";
				$lc += 2;		
				if ($p1 == 'Export Excel')
				{
					$myArr = array('','DATE SUB-TOTAL','',$sub2_gross,$sub2_discount,$sub2_net);
					$excel->writeLine($myArr);
					$excel->writeRow();
				}
			}
			$details .= "*** TRANSACTION DATE: ".ymd2mdy($r->date)."\n";
			if ($p1 == 'Export Excel')
			{
				$trdate = '*** TRANSACTION DATE : '.ymd2mdy($r->date);
				$myArr = array('',$trdate,'','','');
				$excel->writeLine($myArr);
			}
			$lc++;
			$mdate=$r->date;
		  	$sub2_gross = $sub2_discount = $sub2_net = 0;
		  	$sub1_gross = $sub1_discount = $sub1_net = 0;
		}
		if ($madmin_id != $r->admin_id)
		{
			if ($madmin_id != '')
			{
				$details .= space(8).str_pad("CASHIER SUB-TOTAL",15,'.').
						adjustRight(number_format($sub1_gross,2),12).' '.
						adjustRight(number_format($subl_discount,2),10).' '.
						adjustRight(number_format($sub1_net,2),12)."\n";
				$details .= "\n";
				$lc += 2;		
				if ($p1 == 'Export Excel')
				{
					$myArr = array('','CASHIER SUB-TOTAL','',$sub1_gross,$sub1_discount,$sub1_net);
					$excel->writeLine($myArr);
					$excel->writeRow();
				}
			}
			$details .= "Cashier: ".lookUpTableReturnValue('a','admin','admin_id','username',$r->admin_id)."\n";
			$cashier = "Cashier: ".lookUpTableReturnValue('a','admin','admin_id','username',$r->admin_id)."\n";
			if ($p1 == 'Export Excel')
			{
				$myArr = array('',$cashier,'');
				$excel->writeLine($myArr);
			}
			$madmin_id = $r->admin_id;
  			$sub1_gross = $sub1_discount = $sub1_net = 0;
			$lc++;
		}
		if ($msales_header_id != $r->sales_header_id)
		{
			if ($msales_header_id != 'x' && $rtype!='S') 
			{
				$details .= "\n";
				$lc++;
				
			}
			
			$status='';
			if ($r->status != 'S')
				$status=status($r->status);
			
			$ictr++;
			$details .= ' '.adjustRight(number_format($ictr,0),5).'. '.
						adjustSize(str_pad($r->invoice,9,'0', STR_PAD_LEFT),9).' '.
						adjustSize($r->time,5).' '.
						adjustSize($status,9).' ';

			if (!in_array($r->status, array('U','V')))
			{
				$details .=	adjustRight(number_format($r->gross_amount,2),12).' '.
						adjustRight(number_format($r->discount_amount,2),10).' '.
						adjustRight(number_format($r->net_amount,2),12);
				$oadmin_id = lookUpTableReturnValue('a','suspend_header','suspend_header_id','admin_id',$r->suspend_header_id);
				$order = lookUpTableReturnValue('a','admin','admin_id','username',$oadmin_id);
				if ($order == 'No Record') $order = '';
				$details .= "  ".$order."\n";
//						adjustSize(lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id),15)."\n";
				if ($p1 == 'Export Excel')
				{
					$stat = $r->time.' '.$status;
					$myArr = array($ictr,$r->invoice,$stat,$r->gross_amount,$r->discount_amount,$r->net_amount);
					$excel->writeLine($myArr);
				}
				$total_gross += $r->gross_amount;
				$total_discount += $r->discount_amount;
				$total_net += $r->net_amount;
				$total_charges += $r->service_charge;

				$sub1_gross += $r->gross_amount;
				$sub1_discount += $r->discount_amount;
				$sub1_net += $r->net_amount;

				$sub2_gross += $r->gross_amount;
				$sub2_discount += $r->discount_amount;
				$sub2_net += $r->net_amount;

				$cc=0;
				foreach ($aCashierSales as $temp)
				{
					$fnd=0;
					if ($temp['admin_id'] == $r->admin_id)
					{
						$dummy = $temp;
						$dummy['count'] += 1;
						$dummy['discount'] += $r->discount_amount;
						$dummy['net_amount'] += $r->net_amount;
						$aCashierSales[$cc] = $dummy;
						$fnd=1;
						break;
					}
					$cc++;
				}		
				if ($fnd == 0)
				{
					$dummy = null;
					$dummy = array();
					$dummy['admin_id'] = $r->admin_id;
					$dummy['count'] = 1;
					$dummy['discount'] = $r->discount_amount;
					$dummy['net_amount'] = $r->net_amount;
					$aCashierSales[] = $dummy;
				}
			}
			else
			{
				$details .= "***** \n";
				if ($p1 == 'Export Excel')
				{
					$stat = $r->time.' '.$status;
					$myArr = array($ictr,$r->invoice,$stat,'*****','');
					$excel->writeLine($myArr);
				}
			}			
						
			$lc++;			
			$msales_header_id = $r->sales_header_id;
			
		}
		if ($rtype != 'S')
		{
      			$details .= space(8).
					adjustSize(substr($r->barcode,0,10).' '.stripslashes(htmlspecialchars($r->stock)),30).' '.
					adjustRight(number_format($r->qty,3),8).' '. //' x '.
					//adjustRight(number_format($r->price,2),8).' '.
					adjustRight(number_format($r->amount,2),9)."\n";
			$lc++;
			if ($p1 == 'Export Excel')
			{
				$myArr = array('',$r->stock,'',$r->qty,$r->amount);
				$excel->writeLine($myArr);
			}
		}	
					
		if ($lc > 75 && ($p1 == 'Print Draft' || $p1 == 'Go'))
		{
			$details1 .= $header.$details;
			$details .= "\n\n";
			$details .= "<eject>";
			
			if ($p1 == 'Print Draft')
			{
			 doPrint($header.$details);
			} 
			
			$page++;
			$details = '';
			$header = "\n\n\n";
			$page=1;
			$header .= center($SYSCONF['BUSINESS_NAME'],88)."\n";
			$header .= center('DAILY SALES REPORT',88)."\n";
			$header .= center('Transaction Date '.$sd.' To '.$ed,88)."\n\n";
			
			$header .= str_repeat('-',88)."\n";
			$header .= '   #     Invoice    Time                Gross     Discount  Net Amount      '."\n";
			if ($rtype == 'D')
				$header .= '       /Product Description             /Qty      /Sales'."\n";
			$header .= str_repeat('-',88)."\n";
			
			$lc=12;
		}	
		

	} //while
	$details .= space(8).str_pad("CASHIER SUB-TOTAL",26,'.').
			adjustRight(number_format($sub1_gross,2),12).' '.
			adjustRight(number_format($subl_discount,2),10).' '.
			adjustRight(number_format($sub1_net,2),12)."\n";

	$details .= space(8).str_pad("DATE SUB-TOTAL",26,'.').
			adjustRight(number_format($sub2_gross,2),12).' '.
			adjustRight(number_format($sub2_discount,2),10).' '.
			adjustRight(number_format($sub2_net,2),12)."\n";

	$details .= str_repeat('-',88)."\n";
	$details .= space(8).str_pad("GRAND TOTAL",26,'.').
						adjustRight(number_format($total_gross,2),12).' '.
						adjustRight(number_format($total_discount,2),10).' '.
//						adjustRight(number_format($total_net,2),12)."  ".
						adjustRight(number_format($total_net,2),12)."\n";

	if ($p1 == 'Export Excel')
	{
		$myArr = array('','CASHIER SUB-TOTAL','',$sub1_gross,$sub1_discount,$sub1_net);
		$excel->writeLine($myArr);
		$myArr = array('','DATE SUB-TOTAL','',$sub2_gross,$sub2_discount,$sub2_net);
		$excel->writeLine($myArr);
		$myArr = array('','GRAND TOTAL','',$total_gross,$total_discount,$total_net);
		$excel->writeLine($myArr);
		$excel->writeRow();
	}

	$details .= str_repeat('-',88)."\n";
	$details1 .= $header.$details;
	if ($p1 =='Print Draft')
	{
		doPrint($header.$details);
	}
	$details  = '';
	
/*	
	$q = "select sum()
			from 
				sales_header,
				sales_tender
			where
				sales_header.sales_header_id=sales_tender.sales_header_id and 
				sales_header.status!='V' and 
				date>='$msd'  and 
				date<='$med'" 
	$qr = @mysql_query($q) or message(mysql_error());			
	$r = @mysql_fetch_object($qr);
*/
	$details .= str_pad("Total Sales for the period",69,".");
	$details .= adjustRight(number_format($total_net,2),14)."\n";
	if ($p1 == 'Export Excel')
	{
		$myArr = array('','Total Sales for the period','','','',$total_net);
		$excel->writeLine($myArr);
		$excel->writeRow();
	}
//	$details .= str_pad("Bank Charges on Food Sales Paid thru credit card",69,".");
//	$details .= adjustRight(number_format($total_charges,2),14)."\n";
	
	$total_net += $total_charges;
	$details1 .= $details;
	if ($p1 =='Print Draft')
	{
		doPrint($header.$details);
	}
	
/*	$details  = '';				
	$details .= "\nDown Payment on Sales Order \n";
	$details .= "      #    SOF No.    Customer Name                         Amount \n";
	$details .= "   ------ --------- ------------------------------------- ------------\n";				
	$q = "select *
			from 
				so_header
			where
				so_header.status!='C' and
				date>='$msd'  and 
				date<='$med'";
	$qr = @mysql_query($q) or message(mysql_error());
	$ctr=0;
	$total_down = 0;
	while ($r = @mysql_fetch_object($qr))
	{
		$ctr++;
		if ($ctr >1) $details .= "\n";
		$details .=  space(3).adjustRight($ctr,4).'. '.
					str_pad($r->so_header_id,9,'0',STR_PAD_LEFT).' '.
					adjustSize(lookUpTableReturnValue('x','account','account_id','account',$r->account_id),40).' '.
					adjustRight(number_format($r->downpaid,2),10).' ';
		$total_down += $r->downpaid;
		$total_net += $r->downpaid;			
	}*/
	
	if ($total_down == 0) $details .= space(79);
	$details .= adjustRight(number_format($total_down,2),12)."\n";
	$details .= space(3).str_repeat('-',85)."\n";
	$details .= str_pad("TOTAL ACCOUNTABILITY OF THE PERIOD",69,'.');
	$details .= adjustRight(number_format($total_net,2),14)."\n";
	$details .= str_repeat('=',88)."\n\n";
	$details1 .= $details;
	if ($p1 == 'Export Excel')
	{
		$myArr = array('','TOTAL ACCOUNTABILITY FOR THE PERIOD','','','',$total_net);
		$excel->writeLine($myArr);
		$excel->writeRow();
	}
	if ($p1 =='Print Draft')
	{
		doPrint($header.$details);
		doPrint('<eject>');
	}
	$details  = '';
	
	$q = "select 
				if(tender.tender_type in('C'),'',sales_header.invoice) as invoice,
				sum(amount) as amount,
				account,
				carddate,
				cardno,
				sales_tender.tender_id,
				tender.tender,
				tender.bankable,
				tender.tender_type,
				sales_tender.remark
			from
				sales_header,
				sales_tender,
				tender
			where
				tender.tender_id=sales_tender.tender_id and
				sales_header.sales_header_id=sales_tender.sales_header_id and
				sales_header.status!='V' and
				date>='$msd'  and 
				date<='$med'
			group by
				tender_id, cardno, sales_header.sales_header_id";
	$qr = @mysql_query($q) or message(mysql_error());

	$details = "\n\n\n";
	if ($p1 == 'Print Draft')
		$details = "<small3>";
	$page=1;
	$details .= center($SYSCONF['BUSINESS_NAME'],88)."\n";
	$details .= center('DAILY SALES REPORT',88)."\n";
	$details .= center('Transaction Date '.$sd.' To '.$ed,88)."\n\n";

	$details .= "SALES BREAKDOWN\n";
	$details .= '       Reference  Invoice/Check#   Particulars                 Amount '."\n";
	$details .= '----- ---------- --------------- -------------------------- -----------'."\n";
	$sub_total = 0;
	$total_breakdown = 0;
	$mtender_id = '';
	$total_cash=0;
	$other_bankable = 0;
	$ctr=0;	
	if ($p1 == 'Export Excel')
	{
		$excel->writeRow();
		$myArr = array('','SALES BREAKDOWN','');
		$excel->writeLine($myArr);
		$myArr = array('','Reference','Invoice/Check#','Particulars','Amount');
		$excel->writeLine($myArr);
	}

	while ($r = @mysql_fetch_object($qr))
	{
		if ($r->tender_type=='C') continue;
		if ($r->bankable =='Y')
		{
			$other_bankable += $r->amount;
		}
		if ($mtender_id !=  $r->tender_id)
		{
			if ($mtender_id != '')
			{
				$details .= adjustRight(number_format($sub_total,2),13)."\n";
				if ($p1 == 'Export Excel')
				{
					$myArr = array('','','','',$sub_total);
					$excel->writeLine($myArr);
				}
			}
			$details .= adjustSize($r->tender,25)."\n";
			if ($p1 == 'Export Excel')
			{
				$myArr = array('',$r->tender,'');
				$excel->writeLine($myArr);
			}
			$ctr=0;
			$mtender_id = $r->tender_id;
			$sub_total = 0;
		}
		$ctr++;
		if ($ctr>1) $details .= "\n";
		
		$remark = '';
		$cardno = '';
		
		if ($r->tender_type == 'K')
		{
			$remark = ymd2mdy($r->carddate);
			$cardno = substr($r->cardno,0,15);
		}
		else
		{
		  $remark = substr($r->remark,0,10);
		  $cardno = str_pad($r->invoice,9,'0', STR_PAD_LEFT);
		} 
		
		$details .= space(6).adjustSize($remark,10).' '.
					adjustSize($cardno,15).' '.
					adjustSize($r->account,25).' '.
					adjustRight(number_format($r->amount,2),11);
		if ($p1 == 'Export Excel')
		{
			$myArr = array('',$remark,$cardno,$r->account,$sub_total);
			$excel->writeLine($myArr);
		}
		$sub_total += $r->amount;
		$total_breakdown += $r->amount;
	}		
	$details .= adjustRight(number_format($sub_total,2),13)."\n";
	$total_cash = $total_net - $total_breakdown;
	$total_breakdown += $total_cash;
	
	$details .= "\n";
	$details .= adjustSize('Cash',25).space(34).
				adjustRight(number_format($total_cash,2),11).' '.
				adjustRight(number_format($total_cash,2),12)."\n";
	if ($p1 == 'Export Excel')
	{
		$myArr = array('','','','',$sub_total);
		$excel->writeLine($myArr);
		$excel->writeRow();
		$myArr = array('','Cash','',$total_cash,$total_cash);
		$excel->writeLine($myArr);

	}
	
	if ($total_down != 0)
	{
		$details .= "\n";
		$details .= adjustSize('Sales Order Downpayment',68).' '.
				adjustRight(number_format($total_down,2),14)."\n";
		$total_breakdown += $total_down;
	}	

	$q = "select sum(totalcashcount) as totalcashcount
		from
			cashcount
		where
			date>='$msd'  and 
			date<='$med'";

	$qr = @mysql_query($q) or message(mysql_error());
	$r = @mysql_fetch_object($qr);
	$details .= adjustSize('Cash Count',25).space(5).
				adjustRight(number_format($r->totalcashcount,2),11).' ';

	$total_bankable = $total_cash + $other_bankable;
	if ($r->totalcashcount > $total_bankable)
	{
		$details .= "OVER ".number_format($r->totalcashcount - $total_bankable,2);
		if ($p1 == 'Export Excel')
		{
			$myArr = array('','Cash Count',$r->totalcashcount,'OVER',$r->totalcashcount - $total_bankable);
			$excel->writeLine($myArr);
		}		
	}
	
	elseif ($r->totalcashcount < $total_bankable)
	{
		$details .= "SHORT (".number_format($total_bankable - $r->totalcashcount,2).")";
		if ($p1 == 'Export Excel')
		{
			$myArr = array('','Cash Count',$r->totalcashcount,'SHORT',$total_bankable - $r->totalcashcount);
			$excel->writeLine($myArr);
		}
	}
	else
	{
		$details .= "BALANCED";
		if ($p1 == 'Export Excel')
		{
			$myArr = array('','Cash Count',$r->totalcashcount,'BALANCED','');
			$excel->writeLine($myArr);
		}
	}
	$details .= "\n";

			
	$details .= str_repeat('-',88)."\n";
	$details .= str_pad("TOTAL ACCOUNTABILITY OF THE PERIOD",69,'.');
	$details .= adjustRight(number_format($total_breakdown,2),14)."\n";
	$details .= str_repeat('=',88)."\n\n";
	$details .= "____________________                   ________________________\n";
	$details .= "   Prepared by                              Checked by\n\n";
	$details1 .= $details;
	if ($p1 == 'Export Excel')
	{
		$excel->writeRow();
		$myArr = array('','TOTAL ACCOUNTABILITY OF THE PERIOD','','',$total_breakdown);
		$excel->writeLine($myArr);
		$excel->close();
		message("Excel file written Successfully.");

	}
	if ($p1 =='Print Draft')
	{
		doPrint($header.$details);
		doPrint('<eject>');
	}
	$details  = '';
  }
  if ($source == 'D' or $source == 'A')
  {			
	if ($rtype == 'S')
	{
		$q = "select 
					*
			from 
					so_header
			where 
					date>='$msd'  and 
					date<='$med'  
			order by 
					date, 
					admin_id";
	}
	else
	{
		$q = "select 
					*
			from 
					so_header,
					so_detail,
					stock
			where 
					so_header.so_header_id=so_detail.so_header_id and
					so_detail.stock_id=stock.stock_id and
					date>='$msd'  and 
					date<='$med'  
			order by 
					date,
					admin_id";
	}		
		$qr = @mysql_query($q) or message (mysql_error());
	
	$header = "\n\n\n";
	if ($p1 == 'Print Draft')
		$header = "<small3>";
	if ($p1 =='Export Excel')
		include("excelwriter.inc.php");	
		
	$page=1;
	$header .= center($SYSCONF['BUSINESS_NAME'],110)."\n";
	$header .= center('DAILY DELIVERY REPORT',110)."\n";
	$header .= center('Transaction Date '.$sd.' To '.$ed,110)."\n\n";
	
	$header .= str_repeat('-',110)."\n";
	$header .= '   #     Invoice    Customer                     Status        Gross     Discount   Net Amount  Type    '."\n";
	if ($rtype == 'D')
		$header .= '       /Product Description             /Qty      /Sales'."\n";
	$header .= str_repeat('-',110)."\n";

	if ($p1 =='Export Excel')
	{
		$excel=new ExcelWriter("/home/excel/DailySales.xls");
		$myArr = array('','DAILY DELIVERY REPORT','');
		$excel->writeLine($myArr);
		$trdate = 'Transaction Date '.$sd.' To '.$ed;
		$myArr = array('',$trdate,'','','');
		$excel->writeLine($myArr);
		$excel->writeRow();
		$myArr = array(' # ','Invoice','Customer','Gross','Discount','Net Amount','Type');
		$excel->writeLine($myArr);
		if ($rtype == 'D')
		{
			$myArr = array('','/Product Description','','/Qty','Sales','');
			$excel->writeLine($myArr);
		}
	}
	
	$lc=12;
  	$total_gross = $total_discount = $total_net = 0;
  	$sub1_gross = $sub1_discount = $sub1_net = 0;
  	$sub2_gross = $sub2_discount = $sub2_net = 0;
	$tcash = $tcharge = 0;
	$total_charges = 0;
	$msales_header_id='x';
	$ictr=0;
	$mdate='';
	$madmin_id='';
	$aCashierSales = array();

  	while ($r = mysql_fetch_object($qr)) 
	{
		$flag = 0;
		if ($ttype !='S')
		{
			$details .= "*** TRANSACTION DATE: ".ymd2mdy($r->date)."\n";
			if ($p1 == 'Export Excel')
			{
				$trdate = '*** TRANSACTION DATE : '.ymd2mdy($r->date);
				$myArr = array('',$trdate,'','','');
				$excel->writeLine($myArr);
			}
			$lc++;
			$mdate=$r->date;
		  	$sub2_gross = $sub2_discount = $sub2_net = 0;
		  	$sub1_gross = $sub1_discount = $sub1_net = 0;
		}
		if ($mso_header_id != $r->so_header_id)
		{
			if ($mso_header_id != 'x' && $rtype!='S') 
			{
				$details .= "\n";
				$lc++;		
			}
			
			$status='';
			if ($r->status != 'S')
				$status=status($r->status);
			
			$ictr++;
			$details .= ' '.adjustRight(number_format($ictr,0),5).'. '.
						adjustSize(str_pad($r->so_header_id,9,'0', STR_PAD_LEFT),9).' '.
						adjustSize($r->account,30).' '.
						adjustSize($status,9).' ';

			if ($r->status!='C')
			{
				if ($r->tender_id=='1')
				{
					$tender = 'CASH';
					$tcash += $r->net_amount;
				} else
				{
					$tender = 'CHARGE';
					$tcharge += $r->net_amount;
				}
				$details .=	adjustRight(number_format($r->gross_amount,2),12).' '.
						adjustRight(number_format($r->discount_amount,2),10).' '.
						adjustRight(number_format($r->net_amount,2),12).' '.$tender;
				if ($rtype !='S') $details .= "\n";
				if ($p1 == 'Export Excel')
				{
					$stat = $r->time.' '.$status;
					$myArr = array($ictr,$r->so_header_id,$stat,$r->account,$r->gross_amount,$r->discount_amount,$r->net_amount);
					$excel->writeLine($myArr);
				}
				$total_gross += $r->gross_amount;
				$total_discount += $r->discount_amount;
				$total_net += $r->net_amount;
				$total_charges += $r->service_charge;

				$sub1_gross += $r->gross_amount;
				$sub1_discount += $r->discount_amount;
				$sub1_net += $r->net_amount;

				$sub2_gross += $r->gross_amount;
				$sub2_discount += $r->discount_amount;
				$sub2_net += $r->net_amount;

			}
			else
			{
				$details .= "******** ";
				if ($rtype !='S') $details .= "\n";
				if ($p1 == 'Export Excel')
				{
					$stat = $r->time.' '.$status;
					$myArr = array($ictr,$r->so_header_id,$stat,'*****','');
					$excel->writeLine($myArr);
				}
			}			
						
			$lc++;			
			$mso_header_id = $r->so_header_id;
			
		}
		if ($rtype != 'S')
		{
      			$details .= space(8).
					adjustSize(substr($r->stock_code,0,10).' '.stripslashes(htmlspecialchars($r->stock)),30).' '.
					adjustRight(number_format($r->qty,3),8).' '. //' x '.
					//adjustRight(number_format($r->price,2),8).' '.
					adjustRight(number_format($r->amount,2),9)."\n";
			$lc++;
			if ($p1 == 'Export Excel')
			{
				$myArr = array('',$r->stock,'',$r->qty,$r->amount);
				$excel->writeLine($myArr);
			}
		}	
		else $details .= "\n";
					
		if ($lc > 70 && ($p1 == 'Print Draft' || $p1 == 'Go'))
		{
			$details .= "\n\n";
			$details .= "<eject>";
			
			if ($p1 == 'Print Draft')
			{
			 doPrint($header.$details);
			} 
			
			$details1 .= $header.$details;
			$page++;
			$details = '';
			$header = "\n\n\n";
			$header .= center($SYSCONF['BUSINESS_NAME'],110)."\n";
			$header .= center('DAILY DELIVERY REPORT',110)."\n";
			$header .= center('Transaction Date '.$sd.' To '.$ed,110)."\n\n";
			
			$header .= str_repeat('-',110)."\n";
			$header .= '   #     Invoice    Customer                     Status        Gross     Discount   Net Amount  Type    '."\n";
			if ($rtype == 'D')
				$header .= '       /Product Description             /Qty      /Sales'."\n";
			$header .= str_repeat('-',110)."\n";
			
			$lc=12;
		}	
		

	} //while
	if ($msd != $med)
		$details .= space(8).str_pad("DATE SUB-TOTAL",26,'.').space(25).
				adjustRight(number_format($sub2_gross,2),12).' '.
				adjustRight(number_format($sub2_discount,2),10).' '.
				adjustRight(number_format($sub2_net,2),12)."\n";

	$details .= str_repeat('-',110)."\n";
	$details .= space(8).str_pad("GRAND TOTAL",26,'.').space(25).
						adjustRight(number_format($total_gross,2),12).' '.
						adjustRight(number_format($total_discount,2),10).' '.
//						adjustRight(number_format($total_net,2),12)."  ".
						adjustRight(number_format($total_net,2),12)."\n";

	if ($p1 == 'Export Excel')
	{
		$myArr = array('','DATE SUB-TOTAL','',$sub2_gross,$sub2_discount,$sub2_net);
		$excel->writeLine($myArr);
		$myArr = array('','GRAND TOTAL','',$total_gross,$total_discount,$total_net);
		$excel->writeLine($myArr);
		$excel->writeRow();
	}

	$details .= str_repeat('-',110)."\n";
	
	$details .= str_pad("Total Cash Sales for the period",68,".");
	$details .= adjustRight(number_format($tcash,2),14)."\n";
	$details .= str_pad("Total Charge Sales for the period",68,".");
	$details .= adjustRight(number_format($tcharge,2),14)."\n";
	if ($p1 == 'Export Excel')
	{
		$myArr = array('','Total Sales for the period','','','',$total_net);
		$excel->writeLine($myArr);
		$excel->writeRow();
	}
	
	$total_net += $total_charges;

	$details .= "\n\n____________________                   ________________________\n";
	$details .= "   Prepared by                              Checked by\n\n";

	if ($p1 =='Print Draft')
	{
		doPrint($header.$details);
		doPrint('<eject>');
	}
	$details1 .= $header.$details;
	$details  = '';
  }					
} //with print

?> 
<form name='form1' method='post' action=''>
  <table width="90%" border="0" align="center">
    <tr> 
      <td> <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Transaction 
        Date 
        <input name="sd" type="text" id="sd" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $sd;?>" size="8">
        <img src="graphics/arrow_down.jpg" onClick="popUpCalendar(this, form1.sd, 'mm/dd/yyyy')"> 
        To 
        <input name="ed" type="text" id="ed" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $ed;?>" size="8">
        <img src="graphics/arrow_down.jpg" onClick="popUpCalendar(this, form1.ed, 'mm/dd/yyyy')">
        <?= lookUpAssoc('source',array('ALL'=>'A','POS Sales'=>'P','Stocks Delivery'=>'D'),$source);?>
        <?= lookUpAssoc('rtype',array('Summary'=>'S','Detailed'=>'D'),$rtype);?>
        <?= lookUpAssoc('ttype',array('Select Accountability'=>'S','Bankable'=>'Y','Non-Bankable'=>'N'),$ttype);?>
        <input type="Submit" name="p1" value="Go">
        &nbsp; </font> 
      <hr color="#993300"></td>
    </tr>
  </table>
  <table width="1%" border="0" cellspacing="1" cellpadding="1" height="1%" bgcolor="#999999" align="center">
    <tr bgcolor="#333366"> 
      <td height="27"> <font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Report 
        Daily Sales Preview</b></font></td>
    </tr>
    <tr> 
      <td valign="top" bgcolor="#FFFFFF" align="center"> 
	  <textarea name="print_area" cols="110" rows="20" wrap="OFF"><?= $details1;?></textarea></td>
    </tr>
  </table>
  <div align=center>
    <input name="p1" type="submit" id="p1" value="Print Draft"  >
    <input name="p122" type="button" id="p122" value="Print"  onClick="printIframe(print_area)" >
    <input name="p1" type="submit" id="p1" value="Export Excel" />
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
