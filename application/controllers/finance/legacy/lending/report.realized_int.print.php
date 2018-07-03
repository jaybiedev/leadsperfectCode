<STYLE TYPE="text/css">
<!--
  .altSelectFormat {
	background-color: #FFFFFF;
	font-family: verdana;
	border: #CCCCCC 1px solid;
	padding:0px 0px 0px 0px; 
	font-size: 11px;
	color: #000000
	} 

  .altSubHdr {
	font-family: Cambria,'Times New Roman','Nimbus Roman No9 L','Freeserif',Times,serif;
	border: #CCCCCC 1px solid;
	font-size: 14px;
	color: #000000
	} 
  .altSubHdr1 {
	font-family: Cambria,'Times New Roman','Nimbus Roman No9 L','Freeserif',Times,serif;
	border: 2px solid black;
	font-size: 14px;
	color: #000000
	} 

  .altSubDet {
	font-family: Arial,Helvetica, sans-serif, verdana;	
	border: 1px solid black;
	font-size: 10px;
	color: #000000;
	} 
  .altSubDet1 {
	font-family: Arial,Helvetica, sans-serif, verdana;
	border: #CCCCCC 1px solid;
	font-size: 10px;
	color: #000000;
	} 
   
  .altTextFormat {
	background-color: #FFFFFF;
	font-family: verdana;
	border: #CCCCCC 1px solid;
	font-size: 11px;
	color: #000000;
	} 
	.altNumFormat {
	background-color: #FFFFFF;
	font-family: verdana;
	border: #CCCCCC 1px solid;
	font-size: 11px;
	text-align:right;
	color: #000000
	} 
	.altButtonFormat {
	background-color: #C1C1C1;
	font-family: verdana;
	border: #4B4B4B 1px solid;
	font-size: 11px;
	padding: 0px;
	margin: 0px;
	color: #1F016D
	} 
	
	.altTextField {
	background-color: #ececec;
	font-family: verdana;
	font-size: 12pt;
	color: #000000
	} 
	.altNumField {
	background-color: #ececec;
	font-family: verdana;
	border: #CCCCCC 2px solid;
	font-size: 14px;
	text-align:right;
	color: #000000
	} 

	.altTextMain {
	background-color: #ececec;
	font-family: verdana;
	font-size: 14pt;
	color: #1F016D
	} 
	
	.radioStyle {
	background-color: #FF0000;
	border: #000000 solid 1px;
	font-family: verdana;
	font-size: 12px;
	color: #000000
	}
	-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);


function printIframe2(taName) {

var c=document.getElementById(taName).innerHTML;
//using this function create an invisible iframe within your page
//<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
//      or you can change printScript = frames['printit'] with printScript=window.open("","printVersion","scriollbar=yes, location=no")
//      instead of using iframe
//taName is the object of a textarea you wish to print
var printItElem = document.getElementById("printit");

if (printItElem == null)
{
	alert("Could not find the printReady section in the HTML");
	return;
}
var printScript = frames['printit'];
printScript.document.open();
printScript.document.write('<pre>'+c+'</pre>');
printScript.document.close();
printScript.focus();
printScript.print();

}

//-->
</script>
<?
	include_once('lib/library.php');
	include_once('lib/dbconfig.php');
	include_once('lib/connect.php');		
?> 

<div id="printLayer"  name="printLayer" style="position:absolute; width:1270px; height:548px; z-index:1; left:25px; top: 22%;">
  <table width="1254" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC" align="center">
    <tr>
      <td background="../graphics/table0_horizontal.PNG" width="1215"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Print 
        Preview</font></strong></td>
      <td align="right" background="../integral/graphics/table0_horizontal.PNG" width="30"><img src="graphics/table_close.PNG" onClick="document.getElementById('printLayer').style.visibility='hidden'"></td>
    </tr>
<tr>
 <td colspan="2" bgcolor="#CCCCCC" height="500px" valign="top" >
 <div id="print_area"  name="print_area" style="position:absolute; top:32px; width:1255px; height:90%; z-index:1; overflow: auto; left: 16px;">
   <table width="1230" height="0%" border="0" align="center" cellpadding="0" cellspacing="0">
  		   <tr>
			<td colspan="23" align="center"><font size="4" face="Times New Roman, Times, serif"><strong><?=$SYSCONF['BUSINESS_NAME'];?></strong></font></td>
		   </tr>
   		   <tr>
		  	<td colspan="23" align="center"><font size="3" face="Times New Roman, Times, serif"><strong>SCHEDULE OF REALIZED  INCOME - PENSION </strong></font></td>
		   </tr>
		   <td colspan="23" align="center"><font size="2" face="Arial, Helvetica, sans-serif"><?='Year Ending '.$year;?></font></td>		   
		   </tr>
   		   <tr>
			<td colspan="23" align="center">&nbsp;</td>
		   </tr>
			<tr>
             <td width="4%" align="center" class="altSubHdr1">Transaction<br />Date </td>
             <td width="10%" align="center" class="altSubHdr1">Name of Pensioner</td>
             <td width="4%" align="center" class="altSubHdr1">Issuing Branch</td>
             <td width="6%" align="center" class="altSubHdr1">CLAIMS</td>
             <td  width="7%" align="center" class="altSubHdr1">Loan Type </td>
             <td  width="4%" align="center" class="altSubHdr1">Loan<br />Amount</td>
             <td width="4%" align="center" class="altSubHdr1">Total<br />Unrealized</td>
             <td width="6%" align="center" class="altSubHdr1">Period<br />Covered</td>
             <td width="6%" align="center" class="altSubHdr1">Ammort</td>
             <td  width="3%" align="center" class="altSubHdr1">TRM</td>
             <td  width="4%" align="center" class="altSubHdr1">Last Saved<br />Collection</td>
             <td width="4%" align="center" class="altSubHdr1">Jan</td>
             <td width="4%" align="center" class="altSubHdr1">Feb</td>
             <td width="4%" align="center" class="altSubHdr1">Mar</td>
             <td width="4%" align="center" class="altSubHdr1">Apr</td>
             <td width="4%" align="center" class="altSubHdr1">May</td>
             <td width="4%" align="center" class="altSubHdr1">Jun</td>
             <td width="4%" align="center" class="altSubHdr1">Jul</td>
             <td width="4%" align="center" class="altSubHdr1">Aug</td>
             <td width="4%" align="center" class="altSubHdr1">Sep</td>
             <td width="4%" align="center" class="altSubHdr1">Oct</td>
             <td width="4%" align="center" class="altSubHdr1">Nov</td>
             <td width="4%" align="center" class="altSubHdr1">Dec</td>
           </tr>
           <? 
			$tjan = $tjanuc = $januc = $jan = 0;
			$tfeb = $tfebuc = $febuc = $feb = 0;
			$tmar = $tmaruc = $maruc = $mar = 0;
			$tapr = $tapruc = $apruc = $apr = 0;
			$tmay = $tmayuc = $mayuc = $may = 0;
			$tjun = $tjunuc = $junuc = $jun = 0;
			$tjul = $tjuluc = $juluc = $jul = 0;
			$taug = $tauguc = $auguc = $aug = 0;
			$tsep = $tsepuc = $sepuc = $sep = 0;
			$toct = $toctuc = $octuc = $oct = 0;
			$tnov = $tnovuc = $novuc = $nov = 0;
			$tdec = $tdecuc = $decuc = $dec = 0;
			$tgross = $tinterest = $tcharges = $tcapayment = $tchange = $toldbal = $tnet = 0;
			$gtgross = $gtinterest = $gtcharges = $gtcapayment = $gtchange = $gtoldbal = $gtnet = 0;
		   	$first = 0;
		   	$tqty = $total = $ctr = 0;		
			$ctr=$total=0;
			$aUncoll = array();
			$atemp = array();
		   	$q = "select * from branch where enable='t' and branch_code !=' '";
			if ($br_id != 0 and $br_id != '') $q .= " and branch_id = '$br_id' "; 
			$q .= "order by brbir_code";
			$qb = @mysql_query($q);
			while ($rs = mysql_fetch_object($qb))
			{		
				$branch_id = $rs->branch_id;
				$branch    = $rs->branch;
				$hdflag    = 0;
				$q = "select *
							from pension as pn
							INNER JOIN account as ac ON ac.account_id = pn.account_id							  
							where pn.status='RELEASED' and left(type,1) !='G' and
							      (left(date_start,4) >= '$year' and (
								  (left(date_end1,4) <= '$year' and left(date_end1,4) > '1970') or ( 
								  	left(date_end2,4) <= '$year' and left(date_end2,4) > '1970'))) and 
								  (branch_id_lr='$branch_id' or branch_id_gw='$branch_id' or 
								   (branch_id_lr='0' and pn.branch_id = '$branch_id'))";

				$q .= "     order by ac.account";
//echo $q;				
				$qr = mysql_query($q);	
/*				$q = "select *
							from pension as pn
							INNER JOIN account as ac ON ac.account_id = pn.account_id							  
							where pn.status='RELEASED' and left(type,1) !='G' and
							      (left(date_start,4) = '$year' or left(date_end1,4) = '$year' or 
								  	left(date_end2,4) = '$year') and 
								  (branch_id_lr='$branch_id' or branch_id_gw='$branch_id' or 
								   branch_id_lr='0' or pn.branch_id = '$branch_id')";
*/
				if ($month < 10) $sdate = $year.'-0'.$month.'-01';
				else  $sdate = $year.'-'.$month.'-01';
				$cx = 0;				
				while ($rr = @mysql_fetch_object($qr))
				{
					if (substr($rr->loan_type,0,1)=='G') continue;
					 
					$date_start = $rr->date_start;
					if ($rr->date_end2 == '0000-00-00') $date_end = $rr->date_end1;
					else $date_end = $rr->date_end2;
					if ($date_end == '0000-00-00' or  $date_end == '1970-01-01')
					{
						if ($rr->date_end1 == '0000-00-00' or  $rr->date_end1 == '1970-01-01')
					 		$date_end = $date_start;
						else	
					 		$date_end = $rr->date_end1;
					}
/*if ($rr->account_id == 5492)
{
echo $date_start.'  '.$date_end."<br>";					
echo 'year : '.substr($sdate,0,4).' < '.substr($date_start,0,4).'  month : '.substr($sdate,5,2).' < '.substr($date_start,5,2)."<br>";			
echo 'year : '.substr($sdate,0,4).' > '.substr($date_end,0,4).'   month : '.substr($sdate,5,2).' > '.substr($date_end,5,2)."<br>";
}
/*$cx++;
if ($cx > 5)
exit;*/		
					if ((substr($sdate,0,4) >= substr($date_end,0,4) and substr($sdate,5,2) > substr($date_end,5,2)) or 
						(substr($sdate,0,4) <= substr($date_start,0,4) and substr($sdate,5,2) < substr($date_start,5,2))) 
					{
						continue;
					}					 
					$ctr++;
					$loan_type = $rr->loan_type;
					$interest=$rr->interest_orig+$rr->interest_add+($rr->loan_type='A1'?$rr->interest_adv:0.00);
					$charges =$rr->fee_legal+$rr->fee_ci+$rr->fee_sc+$rr->fee_cf;
					$change =($rr->loan_type != 'A1'?$rr->interest_adv:0.00) + $rr->excesscr;
					$oldbal = $rr->old_balance + $rr->bal_fward;
					$pawnamt = $rr->pawned_orig + $rr->pawned_add;
					$pawned = $interest / ($rr->term_orig+$rr->term_add);
					$mbranch=lookUpTableReturnValue('x','branch','branch_id','branch',$rr->branch_id);
					$loantype=lookUpTableReturnValue('x','transtype','type','transtype',$loan_type);
					
					$claims=lookUpTableReturnValue('x','claim_type','claim_type_id','claim_type',$rr->claim_type_id);					
					$coverage = substr(ymd2mdy($date_start),0,6).substr(ymd2mdy($date_start),8,2).' To ';
					$coverage.= substr(ymd2mdy($date_end),0,6).substr(ymd2mdy($date_end),8,2);
					$tgross += $rr->original_prin+$rr->additional_prin;
					$tinterest += $interest;
					$tcharges += $charges;
					$tcapayment += $rr->capayment;
					$tchange += $change;
					$toldbal += $oldbal;
					$tnet += $rr->netcash;
					$date_s = substr($date_start,0,8).'01';
					if (substr($date_end,5,2)==12)
					{
						$yr = substr($date_end,0,4)+1;
						$date_e = $yr.'-01-01';
					} else
					{
						$mo = substr($date_end,5,2)+1;
						if ($mo < 10)
							$date_e = substr($date_end,0,5).'0'.$mo.'-01';
						else
							$date_e = substr($date_end,0,5).$mo.'-01';
					}
					
					if ($month == 0 or $month == 1)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='01' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
//if ($rr->account_id == 5492)
//echo $rr->date_end1.' '.$rr->date_end2.'  month '.$month.'  '.$q."<br>";						
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-01-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $januc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $jan = $pawned;						
						else $jan = $rcc->credit;					
					}	

					if ($month == 0 or $month == 2)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='02' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-02-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $febuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $feb = $pawned;	
						else $feb = $rcc->credit;	
					}					
					
					if ($month == 0 or $month == 3)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='03' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-03-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $maruc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $mar = $pawned;						
						else $mar = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 4)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='04' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-04-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $apruc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $apr = $pawned;	
						else $apr = $rcc->credit;
					}
//echo $q.'  '.$rcc->credit.'  date '.$wdate.'  start '.$date_start.'  end '.$date_end.'  '.$apruc;
//exit;
										
					if ($month == 0 or $month == 5)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='05' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-05-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $mayuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $may = $pawned;						
						else $may = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 6)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='06' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-06-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $junuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $jun = $pawned;	
						else $jun = $rcc->credit;					
					}					

					if ($month == 0 or $month == 7)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='07' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-07-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $juluc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $jul = $pawned;						
						else $jul = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 8)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='08' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-08-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $auguc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $aug = $pawned;						
						else $aug = $rcc->credit;					
					}	

					if ($month == 0 or $month == 9)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='09' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";								
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-09-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $sepuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $sep = $pawned;						
						else $sep = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 10)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='10' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";
									
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-10-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $octuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $oct = $pawned;	
						else $oct = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 11)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='11' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-11-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $novuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $nov = $pawned;						
						else $nov = $rcc->credit;					
					}	
					
					if ($month == 0 or $month == 12)
					{
						$q = "select sum(credit) as credit,date from slpension where account_id = '$rr->account_id' and 
									left(type,1)='F' and substr(date,6,2)='12' and left(date,4)='$year' and
									(date >= '$date_s' and date < '$date_e')
							  group by 
									account_id	";
						$qcc = mysql_query($q); 			
						$rcc = mysql_fetch_object($qcc);
						$wdate = $year.'-12-'.'15';
						if ($rcc->credit == NULL and ($wdate >=$date_s and $wdate < $date_e)) $decuc = $pawned;
						if ($rcc->date != NULL) $lastpayd = $rcc->date;	
						if ($rcc->credit > $pawned) $dec = $pawned;						
						else $dec = $rcc->credit;					
					}	
					$tjan += $jan;
					$tfeb += $feb;
					$tmar += $mar;
					$tapr += $apr;
					$tmay += $may;
					$tjun += $jun;
					$tjul += $jul;
					$taug += $aug;
					$tsep += $sep;
					$toct += $oct;
					$tnov += $nov;
					$tdec += $dec;
					$tjanuc += $januc;
					$tfebuc += $febuc;
					$tmaruc += $maruc;
					$tapruc += $apruc;
					$tmayuc += $mayuc;
					$tjunuc += $junuc;
					$tjuluc += $juluc;
					$tauguc += $auguc;
					$tsepuc += $sepuc;
					$toctuc += $octuc;
					$tnovuc += $novuc;
					$tdecuc += $decuc;
					if ($januc+$febuc+$maruc+$apruc+$mayuc+$junuc+$juluc+$auguc+$sepuc+$octuc+$novuc+$decuc > 0)
					{
						$atemp[date_release] = $rr->date_release;
						$atemp[account] = $rr->account;
						$atemp[branch] = $mbranch;
						$atemp[claims] = $claims;
						$atemp[loantype] = $loantype;
						$atemp[gross] = $rr->original_prin+$rr->additional_prin;
						$tgross += $gross;
						$atemp[interest] = $interest;
						$tinterest += $interest;
						$atemp[coverage] = $coverage;
						$atemp[term] = $rr->term_orig+$rr->term_add;
						$atemp[pawned] = $pawned;
						$atemp[pawnamt] = $pawnamt;
						$atemp[lastpayd] = $lastpayd;
						$atemp[januc] = $januc;
						$atemp[febuc] = $febuc;
						$atemp[maruc] = $maruc;
						$atemp[apruc] = $apruc;
						$atemp[mayuc] = $mayuc;
						$atemp[junuc] = $junuc;
						$atemp[juluc] = $juluc;
						$atemp[auguc] = $auguc;
						$atemp[sepuc] = $sepuc;
						$atemp[octuc] = $octuc;
						$atemp[novuc] = $novuc;
						$atemp[decuc] = $decuc;
						$aUncoll[] = $atemp;
					}
				?>
			   <tr>
				 <td class="altSubDet" align="left"><?=$rr->date_release;?></td>
				 <td class="altSubDet" align="left" ><?=$rr->account;?></td>
				 <td class="altSubDet" align="center"><?=$mbranch;?></td>
				 <td class="altSubDet" align="center"><?=$claims;?></td>
				 <td class="altSubDet" align="center"><?=$loantype;?></td>
				 <td class="altSubDet" align="right"><?=number_format($rr->original_prin+$rr->additional_prin,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($interest,2);?></td>
				 <td class="altSubDet" align="center"><?=$coverage;?></td>
				 <td class="altSubDet" align="right"><?=number_format($pawnamt,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($rr->term_orig+$rr->term_add,0);?></td>
				 <td class="altSubDet" align="center"><?=$lastpayd;?></td>
				 <td class="altSubDet" align="right"><?=number_format($jan+$januc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($feb+$febuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($mar+$maruc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($apr+$apruc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($may+$mayuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($jun+$junuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($jul+$juluc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($aug+$auguc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($sep+$sepuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($oct+$octuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($nov+$novuc,2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($dec+$decuc,2);?></td>
			   </tr>
			<?
					$gtgross += $rr->original_prin+$rr->additional_prin;
					$gtinterest += $interest;
					$gtcharges += $charges;
					$gtcapayment += $rr->capayment;
					$gtchange += $change;
					$gtoldbal += $oldbal;
					$gtnet += $rr->netcash;
					$januc = $jan = 0;
					$febuc = $feb = 0;
					$maruc = $mar = 0;
					$apruc = $apr = 0;
					$mayuc = $may = 0;
					$junuc = $jun = 0;
					$juluc = $jul = 0;
					$auguc = $aug = 0;
					$sepuc = $sep = 0;
					$octuc = $oct = 0;
					$novuc = $nov = 0;
					$decuc = $dec = 0;
			
				}
			}
			?>
		   <tr>
			 <td colspan="5" align="right">&nbsp;&nbsp;&nbsp;</td>
			 <td class="altSubDet" align="right"><?=number_format($tgross,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tinterest,2);?></td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right"><?=number_format($tjan+$tjanuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tfeb+$tfebuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmar+$tmaruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tapr+$tapruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmay+$tmayuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjun+$tjunuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjul+$tjuluc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($taug+$tauguc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tsep+$tsepuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($toct+$toctuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tnov+$tnovuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tdec+$tdecuc,2);?></td>
		   </tr>
		   <tr>
			 <td colspan="10" align="right">&nbsp;&nbsp;&nbsp;<font  size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 Less: Interest Income of Uncollected Amounts</font></td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right"><?=number_format($tjanuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tfebuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmaruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tapruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmayuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjunuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjuluc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tauguc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tsepuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($toctuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tnovuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tdecuc,2);?></td>
		   </tr>
 		   <tr>
			 <td colspan="10" align="right">&nbsp;&nbsp;&nbsp;<font  size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 Total Realized Interest Income  </font></td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right"><?=number_format($tjan,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tfeb,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmar,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tapr,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmay,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjun,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjul,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($taug,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tsep,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($toct,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tnov,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tdec,2);?></td>
		   </tr>
           <tr>
             <td colspan="4" align="right"><font size="3" face="Arial, Helvetica, sans-serif"></font></td>
             <td colspan="2" align="left">&nbsp;</td>
             <td colspan="1" align="right">&nbsp;</td>
			 <td colspan="16" align="right">&nbsp;</td>
           </tr>
           <tr>
             <td colspan="23"><font  size="2" face="Verdana, Arial, Helvetica, sans-serif">Summary of Uncollected Accounts Interest Income</font></td>
           </tr>
		   <?
		   foreach ($aUncoll as $temp)
		   {
		   ?>
			   <tr>
				 <td class="altSubDet" align="left"><?=$temp[date_release];?></td>
				 <td class="altSubDet" align="left" ><?=$temp[account];?></td>
				 <td class="altSubDet" align="center"><?=$temp[branch];?></td>
				 <td class="altSubDet" align="center"><?=$temp[claims];?></td>
				 <td class="altSubDet" align="center"><?=$temp[loantype];?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[gross],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[interest],2);?></td>
				 <td class="altSubDet" align="center"><?=$temp[coverage];?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[pawnamt],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[term],0);?></td>
				 <td class="altSubDet" align="center"><?=$temp[lastpayd];?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[januc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[febuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[maruc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[apruc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[mayuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[junuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[juluc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[auguc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[sepuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[octuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[novuc],2);?></td>
				 <td class="altSubDet" align="right"><?=number_format($temp[decuc],2);?></td>
			   </tr>
		<?
		}
		?>	   
 		   <tr>
			 <td colspan="10" align="right">&nbsp;&nbsp;&nbsp;<font  size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 Total Interest Income of Uncollected Amounts</font></td>
			 <td class="altSubDet" align="right">&nbsp;</td>
			 <td class="altSubDet" align="right"><?=number_format($tjanuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tfebuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmaruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tapruc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tmayuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjunuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tjuluc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tauguc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tsepuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($toctuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tnovuc,2);?></td>
			 <td class="altSubDet" align="right"><?=number_format($tdecuc,2);?></td>
           <tr>
             <td colspan="4" align="right"><font size="3" face="Arial, Helvetica, sans-serif"></font></td>
             <td colspan="2" align="left">&nbsp;</td>
             <td colspan="1" align="right">&nbsp;</td>
			 <td colspan="7" align="right">&nbsp;</td>
           </tr>
        </table>
	   </div>
   </td>
 </tr>
</table>
</div>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
<script>printIframe2('print_area')</script>