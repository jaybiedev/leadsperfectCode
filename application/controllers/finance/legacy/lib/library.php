<?
function myBranch()
{
	global $ADMIN;
	
	$_branch = '';
	if ($ADMIN['branch_id'] == '0')
	{
		$_branch = 'ALL';
	}
	else
	{
		$q = "select * from branch where branch_id ='".$ADMIN['branch_id']."'";
		$qr = @pg_query($q);
		if (@pg_num_rows($qr) == 0)
		{
			$_branch = 'No Branch';
		}
		else
		{
			$r = @pg_fetch_object($qr);
			$_branch = $r->branch;
		}
	} 
	return $_branch;
}
function vLogic($l)
{
	if ($l == true)
		return 'Yes';
	else
		return 'No';
}
function vMClass($l)
{
	$aC=null;
	$aC=array();
	if ($l == '1')
	{
		$aC['cmclass']='Personal Pension';
		$aC['maxterm'] = '';
	}
	elseif ($l == '2')
	{
		$aC['cmclass']='Survivor/Beneficiary';
		$aC['maxterm'] = '36';
	}
	elseif ($l == '3')
	{
		$aC['cmclass']='Permanent Disability';
		$aC['maxterm'] = '6';
	}
	elseif ($l == '4')
	{
		$aC['cmclass']='Temporary Disability';
		$aC['maxterm'] = '';
	}
	elseif ($l == '5')
	{
		$aC['cmclass']='Guardian';
		$aC['maxterm'] = '6';
	}
	elseif ($l == '6')
	{
		$aC['cmclass']='Record of Bank Change';
		$aC['maxterm'] = '6';
	}
	else
	{
		$aC['cmclass']='No Classification';
		$aC['maxterm'] = '';
	}
	return $aC;
}

function checkBox($name, $default, $enable)
 {
 	if ($default == 1) $checked = 'checked';
 	else $checked = '';
 	
 	if ($enable == 1) $enable= '';
 	else $enable = 'disabled';
 	
 	echo "<input type='checkbox' name='$name' id='$name'  value='1' $checked $enable>";
 }
function message1($message)
{
?>
 <div align="center"><font color="#FF0000" face="Geneva, Arial, Helvetica, san-serif" size="2"><b><?=$message;?></b>
  </font> </div>
 <?
}

function updateReleasing($id)
{
	
	$q = "select sum(credit) as credit, sum(debit) as debit
			 from ledger where releasing_id='$id' and status!='C' ";
	$QR = @pg_query($q) or message(pg_errormessage());

	$R = @pg_fetch_object($QR);
	$credit = $R->credit;
	$debit = $R->debit;

	$balance = $debit-$credit;
	$q = "update releasing set total_paid='$credit', balance='$balance'
		 where releasing_id='$id'";
	$QR = @pg_query($q) or message(pg_errormessage());
	
}
function accountBalance($maccount_id)
{
	$q = "select sum(balance) as account_balance 
				from 
					releasing 
				where 
					status!='C' and 
					account_id = '$maccount_id'";
	$QR = @pg_query($q) or message(pg_errormessage());
	$R = @pg_fetch_object($QR);
	return $R->account_balance;
}
function accountBalancexxx($maccount_id)
{
	/*$Q = "select sum(amount) as credit 
				from 
					payment_header,
					payment_detail
				where
					payment_header.payment_header_id = payment_detail.payment_header_id and
					payment_detail.account_id ='$maccount_id' and
					payment_header.status!='C'";

	$Q = "select sum(gross) as debit,
					sum(previous_balance) as credit
				from
					releasing
					
				where
					account_id = '$maccount_id' and 
					releasing.status!='C'";
	$QR = @pg_query($Q) or message(pg_errormessge().$Q);
	$R = @pg_fetch_assoc($QR);
	
	$debit = $R['debit'];
	

	*/
	
	
	$Q = "select * 
				from 
					ledger 
				where 
					account_id = '$maccount_id' and
					status !='C'";
	
	$QR = @pg_query($Q) or message(pg_errormessge().$Q);
	
	$credit=$debit=0;
	while ($R = @pg_fetch_assoc($QR))
	{
		if ($R['type'] == 'D')
		{
			$debit += $R['debit'] - $R['credit'];
		}
		elseif ($R['remarks'] == 'RENEW')
		{
		}
		else
		{
			$q= "select payment_detail.amount
					from 
						payment_header,
						payment_detail 
					where 
						payment_header.payment_header_id = payment_detail.payment_header_id and
						payment_header.status!='C' and 
						payment_detail.payment_header_id = '".$R['reference']."' and
						payment_detail.account_id = '$maccount_id'";
			$QQR = @pg_query($q) or message(pg_errormessage());
			if (@pg_num_rows($QQR) == 0)
			{
				$credit += $R['credit'] - $R['debit'];
			}
			else
			{
				$RR = @pg_fetch_assoc($QQR);
				$credit += $RR['amount'];
			}
		}
			
	}
	/*
					
	$Q = "select sum(credit) as credit, sum(debit) as debit 
		from 
			ledger 
		where 
			account_id='$maccount_id' and
			status!='C'";
	$QR = @pg_query($Q) or message(pg_errormessge().$Q);
	$R = @pg_fetch_assoc($QR);
	*/
	$ACCOUNT_BALANCE=$debit-$credit;
	return $ACCOUNT_BALANCE;
}
function accountProfile($maccount_id)
{
	$profile = null;
	$profile = array();
	
	$q = "select * 
		from 
			releasing 
		where 
			account_id='$maccount_id' and
			status!='C'
		order by
			releasing.date desc";

	$QR = pg_query($q) or message(pg_errormessage());
	if (pg_num_rows($QR) > 0)
	{
		$R = pg_fetch_assoc($QR);
		$profile = $R;
		$q = "select sum(credit) as credit, sum(debit) as debit 
			from 
				ledger 
			where 
				account_id='$maccount_id' and
				status!='C' 
			group by 
				releasing_id
			order by 
				date desc";
		$QQR = pg_query($q) or message(pg_errormessage().$q);
		
		while ($RR = pg_fetch_object($QQR))
		{
			$credit += $RR->credit;	
		}
	}	
	return $profile;
}
function releaseProfile($mreleasing_id,$date)
{
	$profile = null;
	$profile = array();
	
	$q = "select
			releasing.date as releasing_date,
			releasing.releasing_id,
			releasing.account_id,
			releasing.mode,
			releasing.loan_type_id,
			releasing.term,
			releasing.total_paid,
			releasing.balance,
			releasing.ammort,
			releasing.rate,
			releasing.principal
		from 
			releasing 
		where 
			releasing_id='$mreleasing_id'";

	$QR = @pg_query($q) or message(pg_errormessage().$q);
	if (pg_num_rows($QR)>0)
	{
		$profile = pg_fetch_assoc($QR);
		$q = "select * from loan_type where loan_type_id='".$profile['loan_type_id']."'";
		$QR = @pg_query($q) or message(pg_errormessage());
		$R = pg_fetch_assoc($QR);
		$profile += $R;
		
		$period = monthDiff($profile['releasing_date'],$date);
		
		//print_r($profile);
		$profile['period'] = $period;
		if ($profile['basis'] == 'I')
		{
			$amount_due = $period*$profile['ammort']*(1+$profile['rate']/100);
			$amount_due -= $profile['total_paid'];
			if ($amount_due < 0) $amount_due = 0;
			$profile['amount_due'] = $amount_due;

			//overide
			$profile['amount_due'] = $profile['ammort'];
			$profile['balance'] = $profile['principal'];
		}
		else
		{
//				$amount_due = $period*$profile['ammort'];
			$profile['amount_due'] = $profile['ammort'];
			$profile['balance'] = $profile['balance'];
		}
	}
	return $profile;
}

function amountDue($arrRelease, $mdate=null)
{
	$aAd = null;
	$aAd = array();
	
	$aAd = $arrRelease;
	$aid = $aAd[account_id];
//	recalculate($arrRelease['releasing_id'],'noneform');
	

	$q = "select sum(credit) as credit, sum(debit) as debit 
			from 
				ledger 
			where 
				status!='C' and 
				type='P' and 
				releasing_id ='".$arrRelease['releasing_id']."'";

	if ($mdate !='')
	{
		$q .= " and date <= '$mdate'";
	}
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
	}
	$r = @pg_fetch_object($qr);
//	galert($q);
	$penalty = $r->debit;
	$aAd['penalty'] = $penalty;

	$q = "select sum(credit) as credit, sum(debit) as debit 
			from 
				ledger 
			where
				 status!='C' and
				releasing_id ='".$arrRelease['releasing_id']."'";
	if ($mdate !='')
	{
		$q .= " and date <= '$mdate'";
	}

	$qr = @pg_query($q);
	if (!$qr) galert(pg_errormessage().$q);
	$r = @pg_fetch_object($qr);

	$credit = $r->credit;	
	$aAd['credit'] = $r->credit;
	$aAd['debit']= $r->debit;

	$q = "select date 
			from 
				ledger 
			where
				 status!='C' and credit > 0 and
				releasing_id ='".$arrRelease['releasing_id']."'";
	if ($mdate !='')
	{
		$q .= " and date <= '$mdate'";
	}
	$q .= "order by date DESC";
	$qr = @pg_query($q);
	if (!$qr) galert(pg_errormessage().$q);
	$r = @pg_fetch_object($qr);
	$aAd['lastpay'] = $r->date;
	
	$releasing_date= $aAd['releasing_date'];
	$ald = explode('-',$releasing_date);
	$withdraw_day = $aAd['withdraw_day'];

	if ($withdraw_day < '1')
	{
		$withdraw_day = $ald[2];
	}

	$term = $aAd['term'];
	$mode = $aAd['mode'];	

	if ($mdate == '')
	{
		$today = date('Y-m-d');
	}
	else
	{
		$today = $mdate;
	}
	$a2d =explode('-',$today);

	if ($ald[1] == 12 and $a2d[0] == $ald[0]+1 and $a2d[1] == 12 and $ald[0] >= 2016 )
	{
		$ald[1] = 1;
		$ald[0] ++;
	}
	
	$months_due = ($a2d[0] - $ald[0])*12;	//--year to months
	$months_due += $a2d[1] - $ald[1] ; //--months

	if ($mode == 'S')
	{
		$months_due = $months_due * 2;
		
		if ($a2d[2]< $withdraw_day)
		{
			$months_due--;
		} 
		if ($a2d[2]< $withdraw_day+15)
		{
			$months_due--;
		}
		if ($withdraw_day > $ald[2])
		{
			$months_due++;
		}
		if ($withdraw_day+15 > $ald[2] and $ald[1] != 12)
		{
			$months_due++;
		}
	} else
	{
		if ($a2d[2]< $withdraw_day)
		{
			$months_due--;
		}
		
		if ($withdraw_day > $ald[2] and $ald[1] != 12)
		{
			//-- if loan date is after withdrawal date
			$months_due++;
		}
		
			if ($months_due == '0')
			{
				//-- incase advance arrival of pension
				/*	
				$last_cutoff =  date ("Y-m-d", mktime (0,0,0,$a2d[1],-27,$a2d[0]));
				$q = "select date from ledger where type='C' and releasing_id = '".$arrRelease['releasing_id']."' order by date desc";
				$qrl = @pg_query($q);
				if (!$qrl)
				{
					galert(pg_errormessage().$q);
				}
				
				$rl=@pg_fetch_object($qrl);
			
				if ($rl->date < $last_cutoff)
				{
					$months_due=1;
				}
				*/
			}
	}
	if ($mode=='S') $term = $arrRelease['term'] * 2;
	else  $term = $arrRelease['term'];
	if ($months_due < $term) 
	{

		$termDue = $months_due*$aAd['ammort']; //-- due as of today
		$aAd['paid_due'] = intval($credit/$aAd['ammort']);
		
		if ($months_due >= 1)
		{
			$aAd['amount_due'] = $termDue - $credit + $penalty;

			$md = $months_due - $credit/$aAd['ammort'];

			if ($md > intval($months_due - $credit/$aAd['ammort']))
			{
				$md = intval($months_due - $credit/$aAd['ammort'])+1;
			}
			else 
			{
				$md = intval($months_due - $credit/$aAd['ammort']);
			}
		}
		else
		{
			$aAd['amount_due'] = $termDue;

			$md = $months_due;
		}
	//	galert('months_due '.$months_due.'  md '.$md.' td '.$termDue);
		

		$aAd['months_due'] = intval($md);
		$aAd['actual_due'] = intval($md);
		$aAd['remaining_due'] = $aAd['term'] - $aAd['paid_due'];

	}
	else
	{
		$aAd['paid_due'] = intval($credit/$aAd['ammort']);
		$aAd['remaining_due'] = 0;
		$aAd['amount_due'] = $arrRelease['balance'];
		$aAd['months_due'] = intval($aAd['term'] - $credit/$aAd['ammort']);
		$aAd[actual_due] = $months_due;
	}

//if ( $aid == '2139')
//	echo $aAd[account].' months due '.$months_due.' term '.$arrRelease['term'].'  '."<br>";		

	$aAd['credit'] = $credit;
	$aAd['penalty'] = $penalty;
	$aAd['today'] = $today;
	$aAd['term'] = $arrRelease['term'];

	if ($aAd['amount_due'] > $arrRelease['balance']) $aAd['amount_due'] = $arrRelease['balance'];

	if ($aAd['amount_due'] < 0) $aAd['amount_due'] = 0;
	return $aAd;
}

function addDate($d,$a)
{
	//$d - date in mm/dd/yyyy format
	//$a - add $a number of days
	//$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));

	$d1 = explode('/',$d);
	$d2 = date('m/d/Y',mktime(0, 0, 0, $d1[0]  , $d1[1]+$a, $d1[2]));
	return $d2;
}        
function monthDiff($d1,$d2)
{
	$a1 = explode('/',$d1);
	$a2 = explode('/',$d2);
	
	//must be modified
	$diff = $a2[0] - $a1[0];
	return $diff;
}

function transaction_type($t)
{
	$str='';
	if ($t=='C')
		$str = 'Cash';
	elseif ($t=='H')
		$str = 'Charge';
	return $str;
		
}
function mode($t)
{
	$str='';
	if ($t=='M')
		$str = 'Monthly';
	elseif ($t=='S')
		$str = 'Semi-Monthly';
	elseif ($t=='W')
		$str = 'Weekly';
	return $str;
		
}
function status($t)
{
	$str='';
	if ($t=='S')
		$str = 'Saved';
	elseif ($t=='P')
		$str = 'Printed';
	elseif ($t=='C')
		$str = 'Cancelled';
	elseif ($t=='V')
		$str = 'Voided';
	elseif ($t=='R')
		$str = 'Returned';
	elseif ($t=='N' or $t=='')
		$str = 'New';
	elseif ($t=='M')
		$str = 'Modified';
	elseif ($t=='T')
		$str = 'Posted';
	elseif ($t=='U')
		$str = 'UnPosted';
	elseif ($t=='A')
		$str = 'Active';
	elseif ($t=='I')
		$str = 'In-Active';
	elseif ($t=='L')
		$str = 'Legal';
	elseif ($t=='O')
		$str = 'Closed';
	return $str;
		
}

//---
function begin()
{
	$_qr = pg_query("begin transaction");
	return $_qr;
}
function rollback()
{
	$_qr = pg_query("rollback transaction");
	return $_qr;
}
function commit()
{
	$_qr = pg_query("commit transaction");
	return $_qr;
}
function query($str)
{
	$_qr = pg_query($str);
	return $_qr;
}
function number_format2($n,$d)
{
	if ($n == 0)
		return '';
	else
		return number_format($n,$d);
}

function doPrint($pln)
{
	global $SYSCONF;
	$serverPort = $_SERVER['REMOTE_ADDR'];	

	if ($SYSCONF['serverPort'] == '')
	{
		$Q = "select * from printque where source_ip='$serverPort'";
		
		$QR = @pg_query($Q) or message("Error querying printer database...");
		if (@pg_num_rows($QR) > 0)
		{
			$R = @pg_fetch_object($QR);
			$destination_ip = $R->destination_ip;
			$SYSCONF['serverPort'] = $destination_ip;
		}	
		else
		{
			$SYSCONF['serverPort'] = $serverPort;
		}

	}	
	$dest = $SYSCONF['serverPort'] ;
	
	$printType = $SYSCONF['PRINTER_TYPE'];
	
	if ($printType == '' || $SYSCONF['PRINTER_TYPE'] == 'UDP DRAFT' || $SYSCONF['PRINTER_TYPE'] == 'DRAFT')
	{
		$URI = "udp://".$SYSCONF['serverPort'];
	
		$fp = fsockopen($URI, 5003, $errno, $errstr, 10) or die("Can't connect...");

		if (!$fp) 
		{
		     echo "$errstr ($errno)<br>\n";
		 }
		 else
		 {	
			@fputs($fp,$pln);
			@fputs($fp,"eof");
        	@fclose ($fp);
		 }
	}
  elseif ($printType == 'HTTP DRAFT')
	{
		 	$fp = fsockopen("udp://$dest", 5003, $errno, $errstr, 30) or die("Can't connect");
			if (!$fp) 
			{
		     echo "Socket Connection Error $errstr ($errno) after $mtry tries<br>\n";
		 	}
		 	else
		 	{	
				fwrite($fp,$pln);
	      	fclose ($fp);
	      }
		
	}
  elseif ($printType == 'TCP DRAFT')
	{
	   $m = "tcp://$dest";
	   
		$mtry=0;	   
	   while (true)
	   {
			 $fp = @fsockopen("tcp://$dest", 5003, $errno, $errstr, 30);
		 	if ($fp || $mtry>100) 
		 	{
		 		break;
		 	}
		 	//echo " Try...";
		 	$mtry++;
		} 	

		 if (!$fp) {
		     message1("Unable to connect to Remote Printer... $errstr ($errno) after $mtry tries...<br>");
		 }
		 else
		 {	
			   @fputs($fp,$pln);
	        	@fclose ($fp);
	      //  	echo "<pre>$pln</pre>";
		 }
	}

	elseif ($printType=='LINUX LP Printer')
	{
	   $m = "tcp://$dest";
		$fp = @fsockopen("tcp://$dest", 5003, $errno, $errstr, 10);
		if (!$fp) {
		    message($m."Unable to connect to Nix Printer $errstr ($errno)<br>\n");
		}
		else
		{	
			   @fwrite($fp,$pln);
	        	@fclose ($fp);
		 }
   }
	elseif ($printType=='LINUX LP Printer -- LOCAL')
	{
		$file ="/tmp/".rand();
		$fl = fopen($file,"w+");
		if (!$fl)
		{
			$rmsg="Unable To Open Temporary Printing File...";
		}
		else
		{
		    if (fwrite($fl, $pln) === FALSE) 
		    {
		    	fclose($fl);
			    $rmsg="Unable To Find [ $dest ] Printing Device...";
		    }
		    else
		    {
		    	fclose($fl);
		    	/*
		    	if (!is_null($dest))
		    	{
		    		system("lp -d $dest $file",$msg);
		    		//system("lp -d $dest $file");
		    	}
		    	else
		    	{
		    		system("lp $file");
		    	}	
		    	system("rm $file");
		    	*/
		    }	
		    
		}
	}
	elseif ($printType=='GRAPHICS')
	{
		echo "<form name='form1'>";
		echo "<input type='hidden' name='print_area' cols='110' rows='25' readonly value='$pln'>";
		echo "</form>";
		echo "<script>printIframe(form1.print_area)</script>";
	}
	elseif ($printType == 'PHP Printer(DRAFT)')
	{
		$handle = printer_open($dest);
		if (!handle)
		{
			$rmsg="Unable to Open Port...";
			echo "Error Printing...".$dest;
			exit;
		}
		else
		{
			printer_set_option($handle, PRINTER_MODE, raw);
			if (!@printer_write($handle, $pln))
			{
				$rmsg="Unable to Write To Port...".$handle;
				//@printer_write($handle, $pln);
				echo "Error Printing...".$dest;
				exit;
			}
			else
			{
				$rmsg = "";
			}
			printer_close($handle);
		}	
	}	
	else //if ($printType == 'PHP Printer(TEXT)') //graphics
	{
		$handle = printer_open($dest);
		if (!handle)
		{
			$rmsg="Unable to Open Port...";
		}
		else
		{
			@printer_set_option($handle, PRINTER_MODE, text);
			if (!@printer_write($handle, $pln))
			{
				$rmsg="Unable to Write To Port...".$handle;
				printer_write($handle, $pln);
			}
			else
			{
				$rmsg = "Printed";
			}
			printer_close($handle);
		}	
	}	

}

function delay($d)
{
	$c=0;
	while ($c < $d)
	{
		$e=0;
		while ($e< $d)
		{
			$e++;
		}
		$c++;
	}
	return;
}

function space($sp)
{
	$s = str_repeat(" ",$sp);
	return $s;
}

function adjustSize($s, $size)
{
	if (strlen($s) > $size)
	{
		$s = substr($s,0,$size);
	}
	else
	{
		$s = str_pad($s,$size);
	}
	
	return $s;
}


function center($s,$size)
{
	$s = str_pad($s,$size," ",STR_PAD_BOTH);
	return $s;
}

function adjustRight($s,$size)
{
	$s = str_pad($s,$size," ",STR_PAD_LEFT);
	return $s;
}

function udate($ymd)
{
	$mdy = ymd2mdy($ymd);
	$ud  = substr($mdy,0,6).substr($mdy,8,10);
	return $ud;
}


function ymd2mdy($ymd)
{
    return date('m/d/Y', strtotime($ymd));
}

function mdy2ymd($mdy)
{
    return date('Y-m-d', strtotime($mdy));
}

function timeElapse($t1, $t2)
{
	
	$a1 = explode(':',$t1);
	$a2 = explode(':',$t2);
	$hr = intval($a2[0]) - intval($a1[0]);
	if ($a2[1] > $a1[1])
		$min .= intval($a2[1]) - intval($a1[1]);
	else
	{
		$hr--;
		$min .= intval($a2[1])+ 60 - $a1[1];
	}	
	$elapse = $hr.":".$min;
	return $elapse;
	
}
function m2c($time)
{
	//military to civilian time
	$time_string = explode(':',$time);
	if (intval($time_string[0]) < 12)
	{
		$new_time = $time_string[0].':'.$time_string[1].'a';
	}
	elseif (intval($time_string[0]) == 12)
	{
		$new_time = $time_string[0].':'.$time_string[1].'n';
	}
	else
	{
		$hrs = intval($time_string[0])-12;
		$new_time = $hrs.':'.$time_string[1].'p';
	}
	return $new_time;
}

function lookUpMonth($name,$value, $width=null,  $notreadOnly=true)
 {
 	
  $arr = array("Select Month"=>"0","January"=>"1","February"=>"2","March"=>"3",
  		"April"=>"4","May"=>"5","June"=>"6","July"=>"7",
		"August"=>"8","September"=>"9","October"=>"10",
		"November"=>"11","December"=>"12");
 		
  $str = "\n\t<select name=\"$name\"  id=\"$name\" style=\"width: $width; border:1px solid black\">";
  $ctr = count($arr);
  while (list ($key, $val) = each ($arr))
  {
   if ($val == $value)
   {
    $str .= "\n\t\t<option value=\"$val\" selected>$key</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$val\">$key</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
}
function cMonth($month)
{
	$m=date("F", mktime(0, 0, 0, $month, 1, 2000));
	return $m;
}


function redirectJS($amessage)
{
  echo  "<SCRIPT language=JavaScript> window.location.href = \'?$amessage\'</SCRIPT>";
  exit();
}

function exitMessage($message)
{
?>
 <div align="center"><font color="#FF0000" face="Geneva, Arial, Helvetica, san-serif"><?=$message;?>
  </font> </div>
 <?
 exit();
}

function message($message)
{
?>
 <div align="center"><font color="#FF0000" face="Geneva, Arial, Helvetica, san-serif" size=2><br><b><?=$message;?></b>
  </font> </div>
 <?
}

function msgBox($message)
{
	echo "<script>  alert('$message')</script>";
}
function numWord($num)
{
	$Ones[1] = "ONE";
	$Ones[2] = "TWO";
	$Ones[3] = "THREE";
	$Ones[4] = "FOUR";
	$Ones[5] = "FIVE";
	$Ones[6] = "SIX";
	$Ones[7] = "SEVEN";
	$Ones[8] = "EIGHT";
	$Ones[9] = "NINE";
	$Ones[10] = "TEN";
	$Ones[11] = "ELEVEN";
	$Ones[12] = "TWELVE";
	$Ones[13] = "THIRTEEN";
	$Ones[14] = "FOURTEEN";
	$Ones[15] = "FIFTEEN";
	$Ones[16] = "SIXTEEN";
	$Ones[17] = "SEVENTEEN";
	$Ones[18] = "EIGHTEEN";
	$Ones[19] = "NINETEEN";
	$Tens[1] = "TEN";
	$Tens[2] = "TWENTY";
	$Tens[3] = "THIRTY";
	$Tens[4] = "FORTY";
	$Tens[5] = "FIFTY";
	$Tens[6] = "SIXTY";
	$Tens[7] = "SEVENTY";
	$Tens[8] = "EIGHTY";
	$Tens[9] = "NINETY";

	$tn=0;
	$wrdn='';
	if ($num >= 1000000)
	{
	  $tn = intval($num/1000000);
	  if (tn>=1)
	  {
	    $tnh = intval($tn/100);
	    if (tnh >= 1)
	    {
	      $wrdn = $Ones[$tnh].' HUNDRED';
	    }  
	    $tno = $tn-($tnh*100);
	    $tnt = intval($tno/10);
	    if  ($tnt>1)
	    {
	       $wrdn = $wrdn + ' ' + $Tens[$tnt];
	       $nn   = $tno-$tnt*10;
	       if (nn>=1)
	       {
	         $wrdn .=  ' ' . $Ones[$nn];
	       }
	    }   
	    elseif ($tnt==1)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }
	    elseif (tno>0)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }   
	    $wrdn=$wrdn+' MILLION';
	  }
	}
	$nm = $num-$tn*1000000;
	
	if ($nm >= 1000)
	{
	  $tn = intval($nm/1000);

	  if ($tn>=1)
	  {
	    $tnh = intval($tn/100);
	    if ($tnh >= 1)
	    {
	      $wrdn .= ' '.$Ones[$tnh].' HUNDRED';
	    }
	    
	    $tno = $tn-($tnh*100);
	    $tnt = intval($tno/10);

	    if ($tnt>1)
	    {
	       $wrdn .= ' ' . $Tens[$tnt] ;
	       $nn   = $tno-$tnt*10;
	       if ($nn>=1)
	       {
	         $wrdn .= ' ' .$Ones[$nn];
	       }
	    }    
	    elseif ($tnt==1)
	    {
	       $wrdn .=  ' ' .$Ones[$tno];
	    }  
	    elseif ($tno>0)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }
	    
	    $wrdn .= ' THOUSAND';

	  }
	}
	
	$tnm = $nm-$tn*1000;
	$tnh = intval($tnm/100);
	if ($tnh >= 1)
	{
	  $wrdn .= ' '.$Ones[$tnh].' HUNDRED';
	}  
	
	$tno = $tnm-($tnh*100);
	$tnt = intval($tno/10);
	
	if ($tnt>1)
	{
	    $wrdn .=  ' ' . $Tens[$tnt];
	    $nn   = $tno-$tnt*10;
	    if ($nn>=1)
	    {
	      $wrdn .= ' ' . $Ones[$nn];
	    }  
	}    
	elseif ($tnt==1)
	{
	    $wrdn .= ' '. $Ones[$tno];
	}    
	elseif (intval($tno)>0)
	{
	    $wrdn .= ' ' . $Ones[$tno];
	}    
	
	$cnts=($nm-intval($nm))*100;
	if ($cnts != 0)
	{
	  if (strlen($wrdn)<2)
	  {
	    $wrdn .= ltrim(substr($cnts,0,2)).'/100';
	  }  
	  $wrdn .= ' AND '.ltrim(substr($cnts,0,2)).'/100';
	}
return $wrdn;
}
function chkRights2($module,$rights, $admin_id)
{

  global $ADMIN;
  $validate_string=" ";

  if ($module == '') return false;
  $q = "select * from admin where admin_id = '$admin_id'";
  $QR =@pg_query($q);
  
  $R = @pg_fetch_object($QR);
  
  if ($R->usergroup =='A')
  {
  	return true;
  }

  if ($rights=="madd") 
  	$validate_string=md5("Y".$admin_id."100".$module);
  elseif ($rights=="medit") 
  	$validate_string=md5("Y".$admin_id."250".$module);
  elseif ($rights=="mdelete") 
  	$validate_string=md5("Y".$admin_id."400".$module);
  elseif ($rights=="mview") 
  	$validate_string=md5("Y".$admin_id."550".$module);

 $Q = "select * from adminrights, module 
 	where 
 		module.module_id=adminrights.module_id and 
 		module.module='$module' and 
 		$rights='$validate_string' and 
 		adminrights.admin_id='$admin_id' and
 		adminrights.enable='Y'";
 
 $Qr = @pg_query($Q);

 if ($Qr && pg_num_rows($Qr)>0)
 	return true;
 else
 	return false;
}
function chkRights3($module,$rights, $admin_id)
{

  global $ADMIN;
  $validate_string=" ";

  if ($module == '') return false;
  $q = "select * from admin where admin_id = '$admin_id'";
  $QR =@pg_query($q);
  
  $R = @pg_fetch_object($QR);
  
  if ($R->usergroup =='A')
  {
  	return true;
  }

  if ($rights=="madd") 
  	$validate_string=md5("Y".$admin_id."100".$module);
  elseif ($rights=="medit") 
  	$validate_string=md5("Y".$admin_id."250".$module);
  elseif ($rights=="mdelete") 
  	$validate_string=md5("Y".$admin_id."400".$module);
  elseif ($rights=="mview") 
  	$validate_string=md5("Y".$admin_id."550".$module);

 $Q = "select * from adminrights, module 
 	where 
 		module.module_id=adminrights.module_id and 
 		module.module='$module' and 
 		$rights='$validate_string' and 
 		adminrights.admin_id='$admin_id' and
 		adminrights.enable='Y'";
 
 $Qr = @pg_query($Q);

 if ($Qr && pg_num_rows($Qr)>0)
 	return true;
 else
 	return false;
}
function chkRights4($module,$rights, $admin_id)
{
// check rights even if administrator
  global $ADMIN;
  $validate_string=" ";

  if ($module == '') return false;
  $q = "select * from admin where admin_id = '$admin_id'";
  $QR =@pg_query($q);
  
  $R = @pg_fetch_object($QR);
  
  if ($rights=="madd") 
  	$validate_string=md5("Y".$admin_id."100".$module);
  elseif ($rights=="medit") 
  	$validate_string=md5("Y".$admin_id."250".$module);
  elseif ($rights=="mdelete") 
  	$validate_string=md5("Y".$admin_id."400".$module);
  elseif ($rights=="mview") 
  	$validate_string=md5("Y".$admin_id."550".$module);

 $Q = "select * from adminrights, module 
 	where 
 		module.module_id=adminrights.module_id and 
 		module.module='$module' and 
 		$rights='$validate_string' and 
 		adminrights.admin_id='$admin_id' and
 		adminrights.enable='Y'";
 
 $Qr = @pg_query($Q);

 if ($Qr && pg_num_rows($Qr)>0)
 	return true;
 else
 	return false;
}
function chkRights5($module,$rights, $admin_id)
{
// Allow administrator and audit
  global $ADMIN;
  $validate_string=" ";

  if ($module == '') return false;
  $q = "select * from admin where admin_id = '$admin_id'";
  $QR =@pg_query($q);
  
  $R = @pg_fetch_object($QR);
  
  if ($R->usergroup =='A' or $R->usergroup =='B' or $R->usergroup =='D')
  {
  	return true;
  }

  if ($rights=="madd") 
  	$validate_string=md5("Y".$admin_id."100".$module);
  elseif ($rights=="medit") 
  	$validate_string=md5("Y".$admin_id."250".$module);
  elseif ($rights=="mdelete") 
  	$validate_string=md5("Y".$admin_id."400".$module);
  elseif ($rights=="mview") 
  	$validate_string=md5("Y".$admin_id."550".$module);

 $Q = "select * from adminrights, module 
 	where 
 		module.module_id=adminrights.module_id and 
 		module.module='$module' and 
 		$rights='$validate_string' and 
 		adminrights.admin_id='$admin_id' and
 		adminrights.enable='Y'";
 
 $Qr = @pg_query($Q);

 if ($Qr && pg_num_rows($Qr)>0)
 	return true;
 else
 	return false;
}
function chkRights($rights)
{
 global $admin;
 $rights = strtoupper($rights);
 return (strpos(strtoupper($admin->rights),$rights) !== false);
}

function chkMenuRights($rights)
{
 global $admin;
 $rights = strtoupper($rights);
 return (strpos(strtoupper($admin->menu),$rights) !== false);
}
function yesterday()
{
  $d =date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
  
  return $d;
}
function tomorrow()
{
  $d =date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
  return $d;
}

function audit($module, $sql, $admin_id, $remark, $row_id)
{
	
	$d = date('Y-m-d');
	
	$dbsql = addslashes($sql);
	
	$Q = "insert into audit 
					(date, module,  admin_id, remark, row_id, dbsql)
				values
					('$d','$module', '$admin_id', '$remark', '$row_id', '$dbsql')";
	$QR = @pg_query($Q);		

	if (!$QR)
	{
		$c = "CREATE TABLE audit (
				  audit_id bigint(20) NOT NULL AUTO_INCREMENT,
				  date date,
				  module varchar(25) collate latin1_general_ci default NULL,
				  dbsql blob,
				  admin_id int(11) default NULL,
				  remark blob,
				  row_id bigint(20) default NULL,
				  PRIMARY KEY  (audit_id),
				  KEY module (module,row_id))";
		@pg_query($c);	
		$qr = @mysql_query($Q);
	}				
}


/* function swapifnull($this,$that)
 {
     if ($this == null)
     {
          $this = $that;
     }
     else
     {
          $that = $this;
     }
 }
*/
 function textField($name,$size,$default,$password)
 {
  if ($password == null)
 {
   echo "<input name='$name' size='$size' value='$default'>";
 }
 else
 {
  echo "<input type=password name='$name' size='$size' value='$default'>";
 }
 }

 function textArea($name,$rows, $cols, $default)
 {
  echo "<textarea name='$name' rows='$rows' cols='$cols'>$default</textarea>";
 }

 function lookUpTable($name,$table,$keyfield,$valuefield,$value,$notreadOnly = true)
 {
  global $o;
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid black\" ";
  if (!$notreadOnly)  $str .= " disabled";
  $str .= ">";
  $q = "select * from $table order by $valuefield";
  $qR = pg_exec($q,$o);
  if (pg_num_rows($qR) == 0)
  {
  	return "No record for table $table...";
  }
  while ($row = pg_fetch_assoc($qR))
  {
   if ($row[$keyfield] == $value)
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\" selected>$row[$valuefield]</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\">$row[$valuefield]</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }

 function lookUpTable2($name,$table,$keyfield,$valuefield,$value,$notreadOnly = true)
 {
  global $o;
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid #ccc;height:25px;\" ";
  if (!$notreadOnly)  $str .= " disabled";
  $str .= ">";
  $q = "select * from $table order by $valuefield";
  $qR = pg_exec($q);
  if (pg_num_rows($qR) == 0)
  {
  	return "No record for table $table...";
  }
  //added to display select item
  $str .= "\n\t\t<option value=''>-- Select $table</option>";
  while ($row = pg_fetch_assoc($qR))
  {
   if ($row[$keyfield] == $value)
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\" selected>$row[$valuefield]</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\">$row[$valuefield]</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }
 function lookUpTable3($name,$table,$keyfield,$valuefield,$value,$notreadOnly = true)
 {
  global $o;
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid #ccc; height:25px;\" ";
  if (!$notreadOnly)  $str .= " disabled";
  $str .= ">";
  $q = "select * from $table where enable order by $valuefield";
  $qR = pg_exec($q);
  if (pg_num_rows($qR) == 0)
  {
  	return "No record for table $table...";
  }
  //added to display select item
  $str .= "\n\t\t<option value=''>-- Select $table</option>";
  while ($row = pg_fetch_assoc($qR))
  {
   if ($row[$keyfield] == $value)
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\" selected>$row[$valuefield]</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$row[$keyfield]\">$row[$valuefield]</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }

 function getFieldSize($table,$field)
 {
 	$q = "select * from $table";
	$qr = pg_exec($q) or die(pg_errormessage());
	
	$num_fields = pg_num_fields($qr) or die(pg_errormessage()); 	
	
	for ($i = 0; $i < $num_fields; $i++)
	{
			$fieldName = pg_field_name($qr,$i);
			$fieldType = pg_field_type($qr,$i);
			$fieldLen = pg_field_len($qr,$i);
			
			if ($fieldName == $field)
			{
				return $fieldLen;
			}
	}		
	echo "\nField $field is not found in table $table...\n";
	return 0;
 }

function lookUpTableReturn($name,$table,$keyfield,$valuefield,$value)
 {
  global $o;
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid black\" >";
  $q = "select * from $table where $keyfield = '$value'";
  $qR = pg_exec($q);
  //echo $value;
  if (pg_num_rows($qR) == 0)
  {
  	return "No Record";
  }
  else
  {
  	$r = @pg_fetch_assoc($qR);
  	return $r[$valuefield]."[$value]";
  }
 }

function lookUpTableReturnValue($name,$table,$keyfield,$valuefield,$value)
 {
  global $o;
  $q = "select * from $table where $keyfield = '$value'";
  $qR = @pg_query($q);
  if (@pg_numrows($qR) == 0)
  {
  	return "No Record";
  }
  else
  {
  	$r = @pg_fetch_assoc($qR);
  	return $r[$valuefield];
  }
 }

 function lookUpArray($name,$arr,$value,$notreadOnly=true)
 {
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid #ccc;height:25px;\"";
  
  if (!$notreadOnly) $str .= " disabled";
  
  $str .= ">";
  $ctr = count($arr);
  for($i = 0; $i < $ctr; $i++)
  {
   if ($arr[$i] == $value)
   {
    $str .= "\n\t\t<option value=\"$arr[$i]\" selected>$arr[$i]</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$arr[$i]\">$arr[$i]</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }

 function lookUpAssoc($name,$arr,$value, $width=null, $class=null)
 {
     if (empty($class))
        $str = "\n\t<select name=\"$name\"  id=\"$name\" style=\"width:$width;border:1px solid #ccc;height:25px\">";
     else
         $str = "\n\t<select name=\"$name\"  id=\"$name\" class=\"{$class}\">";


  $ctr = count($arr);
  while (list ($key, $val) = each ($arr))
  {
   if ($val == $value)
   {
    $str .= "\n\t\t<option value=\"$val\" selected>$key</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$val\">$key</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }
function db_insert_id($table)
{
  global $SYSCONF;
  $id='';
 if (substr($SYSCONF['DB_ENGINE'],0,2)=='my')
 {
    $id = mysql_insert_id();
 }
 else  
 {
  	  $t = explode('.',$table);
	  if (count($t)>1)
	  {
	  	$schema=$t[0];
	  	$tablename = $t[1];
		$seqname = $t[1];
	  }
	  else
	  {
	  	$schema='public';
	  	$tablename = $t[0];
		$seqname = $t[0];
	  }
	  if (in_array($tablename, array('sales_header','sales_detail','sales_tender','stockledger')))
	  {
	  	$tables = $SYSCONF['tables'];
		$schematablename = $tables[$table];	
  	  	$t = explode('.',$schematablename);
	  	if (count($t)>1)
	  	{
	  		$schema=$t[0];
	  		$seqname = $tablename;
	  		$tablename = $t[1];
	  	}
	  	else
	  	{
	  		$schema='public';
	  		$seqname = $tablename;
	  		$tablename = $schematablename;
	  	}
	  }
      $seq = $schema.'.'.$tablename.'_'.$seqname.'_id_seq';  //::text';
	  
	  //galert($seq);return;
      $Q = "select currval('".$seq."'::text)";
		$QR = pg_query($Q) or die (pg_errormessage());
		$R 	= pg_fetch_object($QR);
		$id = $R->currval;
  }
   return $id;
}

if (false == function_exists('fetch_assoc')) {

    function fetch_assoc($q)
    {
        $qR = @pg_exec($q) or message("Error query. " . pg_errormessage());
        $R = @pg_fetch_assoc($qR);
        return $R;
    }
}

if (false == function_exists('fetch_object')) {

    function fetch_object($Q)
    {
        $qR = @pg_exec($Q) or message("Error query. " . pg_errormessage() . " " . $Q);
        $R = @pg_fetch_object($qR);
        return $R;
    }
}
 function tableToArray($table, $field)
 {
  global $o;
  $q = "select * from $table order by $field";
  $qR = pg_exec($q,$o);
  $arr = array();
  while ($r = pg_fetch_assoc($qR))
  {
   $arr[] = $r[$field];
  }
  return $arr;
 }

?>