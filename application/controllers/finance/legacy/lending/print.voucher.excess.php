<STYLE TYPE="text/css">
@media screen
{
		
#printLayer		{
	position:absolute;
	width:8.5in; 
	height:30%;
	z-index:1;
	left:10%;
	top: 35%;
	/*display:none;*/
		}
.display	{
	height:400px;
		}
.breakhere {page-break-after: always}

}

@media print {

	 * {
    visibility: hidden !important;
  	}
	
	#noprint {
	visibility:hidden;
	display:none;
	}
	
	#print_area * {
    visibility: visible !important;
  }
  
	#print_area{
    left: 0;
    top: 0;
	background-color:#FF0000;
	display:block !important; 
	width:8.5in;
	height:11in;
	margin:0in;
	position:relative;
	page-break-after:always;
	page-break-inside:avoid;
	float:none !important;  
  }
 #printLayer	{
    left: 0;
    top: 0;
	background-color:#FF0000;
	position:absolute;
	float:left; 	
 		}
		
.breakhere {page-break-after: always}
 }
.ctext {
	font-family:"Courier New", Courier, monospace; 
	font-size:10pt;
	text-align:left;
	vertical-align:bottom;
		}
.text 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:10pt;
	text-align:left;
	vertical-align:bottom;
		}
.text1 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:9pt;
	text-align:left;
	vertical-align:bottom;
		}
.textbig 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12pt;
	text-align:left;
	vertical-align:bottom;
	line-height:165%;
	}
.textita 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:11pt;
	text-align:left;
	vertical-align:bottom;
	line-height:165%;
	font-style:italic;
		}
.textbigger 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14pt;
	text-align:center
	vertical-align:bottom;
	line-height:175%;
		}
.textsmall 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:8pt;
	text-align:left;
	vertical-align:bottom;
		}
.line {
	border-bottom:1px dashed black;
	font-size:5px;
	}
.vline	{
	border-right:1px dotted #AAAAAA;
	}
  @page
  {
   size: 8.5in 5.5in;
   size: portrait;
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

?> 
<div id="printLayer"  name="printLayer">
  <table width="100%" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC" align="center">
   <div id=noprint>
    <tr>
      <td background="graphics/table0_horizontal.PNG" width="100%"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Print 
        Preview</font></strong></td>
      <td align="right" background="graphics/table0_horizontal.PNG" width="1%"><img src="../graphics/table_close.PNG" onClick="document.getElementById('printLayer').style.visibility='hidden'"></td>
    </tr>
   </div>
   <tr>
	<td class="display" colspan="2" bgcolor="#CCCCCC" valign="top">
	<div id="print_area"  name="print_area">
	
       <table width="75%" border="0" align="left" style="margin-left:.25in" cellpadding="0" cellspacing="0">
        <tr>
          <td height="50" valign="top" width="100%" ><table width="100%" height="1%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td colspan="5" style="height:.75in;">&nbsp;</td>
              </tr>
 		      <tr>
                <td class="body1" style="width:1.25in;">&nbsp;</td>
				<td class="body1" style="width:1in;">&nbsp;</td>
				<td class="body1" style="width:2.25in;">&nbsp;</td>
				<td width="139" class="body1" style="width:1in;">&nbsp;</td>
				<td width="279" class="body1" style="width:1in;">&nbsp;</td>
              </tr>
			  <tr>  
			  	<td class="text"> RELEASE No.</td>
				<td class="text"><?=str_pad($aExcess['wexcess_id'],8,'0',STR_PAD_LEFT);?></td>
				<td colspan="3" class="text">&nbsp;&nbsp;&nbsp;<?='Date & Time Printed : '.date('m/d/Y g:ia').'    Times Printed '.adjustRight($aExcess[printx],3);?></td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="line-height:4pt;">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td class="text" style="line-height:14pt;">Pay To Client</td>
				<td colspan="2" class="text1"><?=strtoupper(htmlspecialchars($aExcess['account']));?></td>
				<td colspan="2" class="text">Excess Withdrawal/Advances</td>
			  </tr>	
			  <tr>
			    <td class="text">Account Group :</td>
				<td colspan="2" class="text"><?=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aExcess['account_group_id']);?></td>
				<td class="text">Date</td>
				<td class="text"><?=ymd2mdy($aExcess['date']);?></td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="border-bottom:thin solid black; line-height:6pt;">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="line-height:4pt;">&nbsp;</td>
			  </tr>	
			  <?
		$as ='';
		if ($aExcess['refund_amount'] != '0')
		{
			  ?>
			  <tr>
			    <td class="text">&nbsp;</td>
				<td class="text" colspan="3"><?=str_pad('For Refund('.$aExcess['refund_remark'].')',16,'.').'  '.number_format($aExcess['refund_amount'],2);?></td>
				<td colspan="1" class="text">&nbsp;</td>
			  </tr>	
			  <?
  			$as = 'Change Refund';
		}	  

		if ($aExcess['ps_amount'] != '0')
		{
			if ($as != '') $as .= "\n and ";
			$as .= 'PS Withdrawal';			
			  ?>
			  <tr>
			    <td class="text">&nbsp;</td>
				<td class="text" colspan="3"><?=str_pad('P-Savings('.$aExcess['ps_remark'].')',16,'.').'  '.number_format($aExcess['ps_amount'],2);?></td>
				<td colspan="1" class="text">&nbsp;</td>
			  </tr>	
			  <?
		}	  
		
		$year_flag = $months = 0;
		$myear = substr($aExcess['date'],0,4);
//		if (substr($aExcess['date'],5,2) > $aExcess['starting_month']) $myear++;
		$curmo = substr($aExcess['date'],5,2)-4;
		if ($curmo > $aExcess['starting_month']) $myear++;
		$details ='';
		for ($cc = 0 ; $cc< 13; $cc++)
		{
			$mc = $aM[($aExcess['starting_month']+$cc-1)%13];
			$mc = $aExcess['starting_month'] + $cc ;
			if ($mc > 13)
			{
				$mc -= 13;
				if ($year_flag == '0')
				{
					$myear++;
					$year_flag = 1;
				}
			}
			
			if ($mc <13)
			{
				$cmonth = cmonth($mc);
			}
			else
			{
				$cmonth = '13th Month ';
			}
			$mi = 'month'.($cc+1);
			
			if ($aExcess[$mi] > '0')
			{
				$ccc++;
				$months++;
				$details .= adjustSize(trim($cmonth).', '.$myear.'........',18).adjustRight(number_format($aExcess[$mi],2),10)."&nbsp;&nbsp;&nbsp;";

				if ($ccc==2)
				{
				  ?>
				  <tr>
					<td class="text">&nbsp;</td>
					<td colspan="4" class="ctext"><?=$details;?></td>
				  </tr>	
				  <?
				  	$ccc=0;
					$details = "";
				}
			}
		}	
		if ($details != '')
		{
				  ?>
				  <tr>
					<td class="text">&nbsp;</td>
					<td colspan="3" class="ctext" style="line-height:10pt"><?=$details;?></td>
				  </tr>	
				  <?
					$details = "";
		}
			  ?>  
			  
			  <tr>
			    <td colspan="2" class="text">Gross Amount</td>
				<td class="text"><?=str_pad(' ',50,'.');?></td>
				<td  class="text">&nbsp;</td>
				<td class="text" style="text-align:right"><?=number_format($aExcess['gross_amount']-$aExcess['refund_amount'],2);?></td>
			  </tr>	
			  <tr>
				<td  class="text">&nbsp;</td>
			    <td colspan="1" class="text">Less:  Interest </td>
				<td class="text"><?=str_pad(' ',50,'.');?></td>
				<td class="text" style="text-align:right"><?=number_format($aExcess['interest'],2);?></td>
				<td  class="text">&nbsp;</td>
			  </tr>	
		<?
		if ($aExcess['interest'] > 0 or $aExcess['charges'] > 0)
		{
			if ($as != '') $as .= "\n and ";
			$as .= ' Advance Change';			
		
		}
		?>	  
			  <tr>
				<td  class="text">&nbsp;</td>
				<td colspan="2" class="text"><?="Other Charges (".$aExcess['charges_remark'].")....................................";?></td>
				<td class="text" style="text-align:right"><?=number_format($aExcess['charges'],2);?></td>
				<td  class="text">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="2" class="text">Net Amount Released</td>
				<td class="text"><?=str_pad(' ',50,'.');?></td>
				<td  class="text">&nbsp;</td>
				<td class="text" style="text-align:right"><?=number_format($aExcess['net_amount'],2);?></td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="border-bottom:thin solid black; line-height:2pt;">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="line-height: 4pt;">&nbsp;</td>
			  </tr>	
		<?
				$obligate = $aExcess['gross_amount']-$aExcess['refund_amount'];
				$details = "Obligation: ".number_format($obligate,2).'  '.
				" for ".$months." Month/s \n";
		?>	  
			  <tr>
			    <td colspan="5" class="text" style="text-align:center"><?=$details;?></td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="line-height:2pt; border-bottom:thin solid black">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="5" style="line-height:4pt;">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="5" class="text"><?='Remarks : '.$aExcess['remarks'];?></td>
			  </tr>
			  <tr>
			    <td colspan="5" style="line-height:4pt;">&nbsp;</td>
			  </tr>	
		<?
			$details = "Received from ".$SYSCONF['BUSINESS_NAME']." the amount of \n".
				numWord($aExcess['net_amount'])." (".number_format($aExcess['net_amount'],2).")  as $as.";	
			if ($aExcess['username'] == '') $prepared = $ADMIN['username'];
			else $prepared = $aExcess['username'];		
			if ($aExcess[cashier_id]=='' or $aExcess[cashier_id]==0) $cashier = $ADMIN['username'];
			else $cashier = lookUpTableReturnValue('x','admin','admin_id','username',$aExcess['cashier_id']);
			
		?>	  
			  <tr>
			    <td colspan="5" class="text"><?=$details;?></td>
			  </tr>
			  <tr>
			    <td colspan="5">&nbsp;</td>
			  </tr>	
			  <tr>
			  	<td class="text">Received by:</td>
			    <td colspan="2" class="text1"><?=strtoupper($aExcess['account']);?></td>
				<td colspan="2" class="text">Prepared by: <?=$prepared;?></td>
			  </tr>	
			  <tr>
			    <td colspan="5">&nbsp;</td>
			  </tr>	
			  <tr>
			    <td colspan="5">&nbsp;</td>
			  </tr>	
			  <tr>
			  	<td class="text">Reviewed by:</td>
			    <td colspan="2" class="text1"><?=$ADMIN['username'];?></td>
				<td colspan="2" class="text">Released by: <?=$cashier;?></td>
			  </tr>	
		</table>
		</td>
		</tr>
		</table>
		</div>
</td>
</tr>
</table>
</div>

<!--<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
<script>printIframe2('print_area')</script>-->

<script type="text/javascript">
 window.onload = function() { window.print(''); }
</script>

<!--
<form>
<input type="button" value="Print this page" onclick="printDiv('print_area')">
</form>
-->
