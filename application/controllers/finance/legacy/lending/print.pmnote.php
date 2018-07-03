<STYLE TYPE="text/css">
.breakhere {page-break-after: always}
	 
.altNumField {
	background-color: #ececec;
	font-family: verdana;
	font-size: 12pt;
	color: #000000;
	text-align:right;
	} 

.style1 {
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
}

.tline1 {
	font-family:"Times New Roman", Times, serif;
	font-weight: bold;
	font-size:16pt;
}
.tline2 {
	font-family:"Times New Roman", Times, serif;
	font-weight: bold;
	font-size:22pt;
}
.tline3 {
	font:Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size:14pt;
}
.tline4 {
	font:Arial, Helvetica, sans-serif;
	font-weight: bold;
	font:14pt;
	text-align:right;
}
.body {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:8pt;
	text-align:justify;
	height:auto;
	vertical-align:middle;
}
.body1 {
	font:Arial, Helvetica, sans-serif;
	size:8px;
	text-align:center;
	height:auto;
	vertical-align:middle;
}
.boxed {
	font:Arial, Helvetica, sans-serif;
	font:10px;
	text-align:center;
	height:auto;
	border: 1px solid black;		
}


</STYLE> 

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

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
//-->
</script>
<?
	$address = lookUpTableReturnValue('x','account','account_id','address',$aLoan['account_id']);
	$court = lookUpTableReturnValue('x','branch','branch_id','court',$aLoan['branch_id']);
	$acnta = explode('/',$aLoan[account]);
	$account = $acnta[0];
	if ($aLoan['withdraw_day']==1) $dday='st';
	elseif ($aLoan['withdraw_day']==2) $dday='nd';
	elseif ($aLoan['withdraw_day']==3) $dday='rd';
	else $dday='th';
	$ldate = strtotime($aLoan[date]);

	if (substr($aLoan[date],8,2) > $aLoan[withdraw_day])
		$ds = date('Y-m-d',strtotime("+31 days",$ldate));
	else $ds = $aLoan[date];	
	$datestart =substr($ds,0,8).str_pad($aLoan[withdraw_day],2,'0',STR_PAD_LEFT);
	$ldate = strtotime($datestart);
	$yr = substr($datestart,0,4);
	$mo = substr($datestart,5,2);
	for ($x = 1; $x < $aLoan[term]; $x++)
	{
		if ($mo == 12) 
		{
			$yr++;
			$mo=1;
		} else $mo++;	
	}
	$dateend =$yr.'-'.str_pad($mo,2,'0',STR_PAD_LEFT).'-'.str_pad($aLoan[withdraw_day],2,'0',STR_PAD_LEFT);
	$q = "select * from comaker where releasing_id='".$aLoan[releasing_id]."'";
	$rc = fetch_assoc($q);
	$aComaker = $rc;
if ($ADMIN[admin_id]==1) print_r($aComaker);
//	$dd = ($aLoan[term]*30)-30;
//	$dterm = '+'.$dd.' days';
//	$ds = date('Y-m-d',strtotime($dterm,$ldate));
//	$dateend =substr($ds,0,8).str_pad($aLoan[withdraw_day],2,'0',STR_PAD_LEFT);
//echo $ldate.' + '.$dterm;
?> 
<div id="printLayer"  name="printLayer" style="position:absolute; width:8.5in; height:30%; z-index:1; left:10%; top: 35%;">

  <table width="100%" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC" align="center">
    <tr>
      <td background="graphics/table0_horizontal.PNG" width="100%"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Print 
        Preview</font></strong></td>
      <td align="right" background="graphics/table0_horizontal.PNG" width="1%"><img src="../graphics/table_close.PNG" onClick="document.getElementById('printLayer').style.visibility='hidden'"></td>
    </tr>
	<tr>
	<td colspan="2" bgcolor="#CCCCCC" height="400px" valign="top">
	<div id="print_area"  name="print_area" style="position:relative; width:95%; height:inherit; z-index:1; overflow: auto; left:5%">          
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr> 
              <td height="50" valign="top" width="100%" >
			  <table width="100%" height="1%" border="0" cellpadding="0" cellspacing="0">
			    <tr>
				  <td colspan="5">&nbsp;</td>
				  <td colspan="2" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:12px;"><b>PN No. &nbsp;&nbsp;<?=str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT);?>&nbsp;&nbsp;</b></td>			 
                  <tr> 
                    <td colspan="7" align="center" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:14px;"><b>PROMISSORY NOTE</b></td>
                  </tr>
                  <tr> 
                    <td colspan="4" align="center" >&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font:8pt; text-align:right;"><u>&nbsp;&nbsp;<?=lookUpTableReturnValue('x','branch','branch_id','branch',$aLoan['branch_id']);?>&nbsp;, Philippines</u></td>
                  </tr>
			  <tr>				    
				    <td class="body1" width="22%">&nbsp;</td>
				    <td class="body" width="6%">&nbsp;</td>
				    <td class="body" width="20%">&nbsp;</td>
				    <td class="body" width="4%">&nbsp;</td>
				    <td class="body" width="20%">&nbsp;</td>
				    <td class="body" width="6%">&nbsp;</td> 
				    <td class="body1" width="22%">&nbsp;</td>
			  </tr>	  
				  <tr>
				  	<td colspan="5">&nbsp;</td>
				  	<td colspan="2" align="center" style="font:Arial, Helvetica, sans-serif; font-weight: bold; font-size:8pt";><u>&nbsp;&nbsp;<?=ymd2mdy($aLoan[date]);?>&nbsp;&nbsp;</u><br />Date</td>
				  </tr>	
				  <tr><td colspan="7">&nbsp;</td></tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 <b>FOR VALUE RECEIVED</b>, on or before the dates listed below, I/We promise to pay jointly and severally to <b>JGM FINANCE CORPORATION</b>, the sum of PESOS: <u>&nbsp;&nbsp;<?=numWord($aLoan['gross']);?>&nbsp;&nbsp;</u> <b>(Php <u>&nbsp;&nbsp;<?=number_format($aLoan['gross'],2);?>&nbsp;</u>)</b>, including an interest of <u>&nbsp;<?= number_format($aLoan['rate'],0);?>%&nbsp;</u>/annum, payable in <u>&nbsp;<?= $aLoan['term'];?>&nbsp;</u> months, and due every <u>&nbsp;<?= $aLoan['withdraw_day'].$dday.' day';?>&nbsp;</u> of the month starting <u>&nbsp;<?=ymd2mdy($datestart);?>&nbsp;</u> to <u>&nbsp;<?=ymd2mdy($dateend);?>&nbsp;</u>, without the need for demand, the installment amount of <u>&nbsp;<?= numWord($aLoan['ammort']). ' PESOS';?>&nbsp;</u> <b>(Php <u>&nbsp;<?= number_format($aLoan['ammort'],2);?>&nbsp;</u>)</b>, per month until the total amount of Php <u>&nbsp;&nbsp;<?= number_format($aLoan['gross'],2);?>&nbsp;</u> has been fully paid.</td>
				  </tr>
				  <tr class="body"><td colspan="7">&nbsp;</td></tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;"> 
					 I understand that I shall be charged a 10% penalty, based on my amortization, for every late payment of monthly amortization and which will be added to each unpaid installment from due date thereof until fully paid. (Amortization payment shall be applied using the STRAIGHT LINE METHOD).</td>
				  </tr>
				  <tr><td class="body" colspan="7">&nbsp;</td></tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					 I shall be charged an additional 1.67% penalty based on the unpaid outstanding balance at the end of the term.</td>
				  </tr>				  
				  <tr><td class="body">&nbsp;</td></tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif;; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					 The debit memo issued by the bank shall serve as the official receipt of the amortization paid for the period.</td>
				  </tr>
				  <tr><td colspan="7" class="body">&nbsp;</td></tr>		  
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					 At any time for as long as the obligation under this NOTE remains un-liquidated in whole or in part, I/We hereby jointly and severally agree and authorize the payee without need of notice or demand, to increase or decrease the rate of interest and/or other service charges and/or fees (herein referred to collectively as "interest") collectible under this NOTE to the maximum of other rates allowed under the law should there be an increase in the cost of funds or should there be any decree, statute, circular or regulation (herein referred to as "enabling law") increasing the interest rate, or removing the interest rate ceiling, or amending the circular or regulations to allow the increase and decrease of interest rates, or otherwise legalizing rates of interest higher or lower than the rate herein stipulated. Any such increase or decrease made pursuant hereto should be effective and be payable as of the date of increase in the cost of funds or effectivity of enabling law. In the event of the increase, I/We jointly and severally agree to pay any interest differential, which maybe debited to my/our account if requested by the payee, to execute a replacement Promissory Note. Should there be a decrease, I/We agree that the interest differential, if any, be applied and credited to my/our account.</td>
				  </tr>
 				  <tr>
				     <td colspan="7" class="body">&nbsp;</td>
				  </tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">In case I opt to pay off my remaining balance before it falls due or before the term of the amortization expires, I agree to pay the whole amount.
				  </td></tr>
 				  <tr>
				     <td colspan="7" class="body">&nbsp;</td>
				  </tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					 In case of failure to perform or violation of any provision of this note; any defect, fraud or misrepresentation in obtaining the loan; winding up of the business and if the MAKER is a natural person or a partnership upon his death or death of any partners or death of the MAKER as a representative of a company as the case may be; any significant changes in the business of in the control of ownership of the MAKER which in the opinion of JGM FINANCE CORPORATION shall become immediately due and payable without need of further notice to the MAKER.</td>
				  </tr>
				  <tr>
				     <td colspan="7" class="body">&nbsp;</td>
				  </tr>
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					I/We hereby waive our rights to make application of payment under Article 1252 of the Civil Code of the Philippines. I/We agree that  in the case of repayment of this NOTE prior to full maturity, the outstanding obligation under this NOTE shall be determined on the basis of the STRAIGHT LINE METHOD.</td>
				  </tr>				  
				  <tr>
				     <td colspan="7" class="body">&nbsp;</td>
				  </tr>	
				  <tr>
				     <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					If any amount due on this NOTE is not paid at its maturity, I/we understand that such payment shall be demandable immediately thereafter. However, if this NOTE is placed in the hands of an attorney or collection agency for collection, I/we jointly and severally agree to pay, in addition to the aggregate of the principal amount and interest due, a sum equivalent to twenty five (25%) percent, thereof, as attorney's and/or collection fees, in case no legal action filed; otherwise, the sum will be equivalent to twenty five (25%) percent of the amount due which shall not in any case be less than Five Hundred Pesos (P500.00) plus the cost of the suit and other litigation expenses, and in addition a further sum of ten (10%) percent of said amount which in no case shall be less than Five Hundred Pesos (P500.00) as liquidated damages.</td>
				  </tr>
				  <tr><td colspan="7">&nbsp;</td></tr>	  
				  <tr>	
					<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					I/We expressly agree that all legal action arising out of this NOTE may be brought in or submitted exclusively to the jurisdiction of the proper court in <u>&nbsp;&nbsp;<?="Bacolod City";?>&nbsp;&nbsp;</u> or the place of execution of this NOTE.  The parties hereby waiving any other venue.</td>
				  </tr>
				  <tr><td colspan="7">&nbsp;</td></tr>	  
				  <tr>	
					<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					<b>JGM FINANCE CORPORATION is hereby fully authorized and empowered to assign this Promissory Note without the need of prior advice or notice to the undersigned maker.</b></td>
				  </tr>
				  <tr><td colspan="7">&nbsp;</td></tr>	  
				  <tr>	
					<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					Acceptance by the payee hereof of the payment of any installment or any part thereof after due date shall not be considered as extending the time for the payment of any of the installments aforesaid or as modification of any of the condition thereof.</td>
				  </tr>
				  <tr><td>&nbsp;</td></tr>	  
				  <tr>	
					<td colspan="7" style="font-family:Arial, Helvetica, sans-serif;; font-size:8pt; text-align:justify;
height:auto; vertical-align:middle;">
					Proceeds of the loan are payable to the maker.</td>
				  </tr>
				  <tr><td>&nbsp;</td></tr>	  
				  <tr>
				    <td style="font:Arial, Helvetica, sans-serif; font-size:8px;	text-align:center;	height:auto; vertical-align:middle;">&nbsp;</td>
				    <td class="body" rowspan="5">&nbsp;</td>
				    <td rowspan="6" style="border:thin solid black;">&nbsp;</td>
				    <td class="body" rowspan="5">&nbsp;</td>
				    <td rowspan="6" style="border:thin solid black;">&nbsp;</td>
				    <td class="body" rowspan="5">&nbsp;</td> 
				    <td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">&nbsp;</td>
				  </tr>
				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$account;?></td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aLoan[comaker1];?></td>
				  </tr>
				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle; border-top:thin solid black;">Maker</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle; border-top:thin solid black;">Co-Maker 1</td>
				  </tr>
				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$address;?></td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aLoan[comaker1_address];?></td>
				  </tr>
				  <tr>
				    <td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">_________________</td>
				    <td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">_________________</td>
				  </tr>
				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				  	<td>&nbsp;</td>
				  	<td>&nbsp;</td>
				  	<td>&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				  </tr>
				  <tr>
				  	<td>&nbsp;</td>
				  	<td>&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><b>LEFT</b></td>
				  	<td>&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><b>RIGHT</b></td>
				  </tr>
				  <tr>
				  	<td>&nbsp;</td>
				  	<td>&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;" colspan="3">MAKER'S THUMB MARK</td>
				  	<td>&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aLoan[comaker2];?></td>
				  </tr>
 				  <tr>
				    <td class="body1" colspan="6">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle; border-top:thin solid black;">Co-Maker 2</td>
				  </tr>
   				  <tr>
				    <td class="body1" colspan="6">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aLoan[comaker2_address];?></td>
				  </tr>
   				  <tr>
				    <td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">__________________</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">__________________</td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
					<td class="body1">&nbsp;</td>
			    	<td style="font:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;">_________________</td>
				  </tr>
   				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Witness</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Witness</td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				  </tr>		 
				  <?
				  if ($aComaker[comake3] != '')
				  {
				  ?>
				  <tr><td>&nbsp;</td></tr> 
				  <tr><td>&nbsp;</td></tr> 
  				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker['comake3'];?></td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker['comake4'];?></td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:10px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker['comake5'];?></td>
				    <td class="body1">&nbsp;</td>
					<td class="body1">&nbsp;</td>
				  </tr>
   				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Co-Maker 3</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Co-Maker 4</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Co-Maker 5</td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
				  </tr>
   				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker[comake3_address];?></td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker[comake4_address];?></td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;"><?=$aComaker[comake5_address];?></td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
				  </tr>
   				  <tr>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				    <td class="body1">&nbsp;</td>
				    <td style="font-family:Arial, Helvetica, sans-serif; font-size:9px;	text-align:center;	height:auto; vertical-align:middle;">Address</td>
				    <td class="body1">&nbsp;</td>
				    <td class="body1">&nbsp;</td>
				  </tr>
				  <?
				  }
				  ?>		 
        </table>
			  </td>
            </tr>
          </table>
        </div>
     </td>  
	</tr>
  </table>

</div>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
<script>printIframe2('print_area')</script> 

<!--
<form>
<input type="button" value="Print this page" onclick="printDiv('print_area')">
</form>
-->