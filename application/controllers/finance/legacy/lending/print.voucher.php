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
.text 	{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12pt;
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
function addspace($ads,$mxl=40)
{
	$sl = strlen($ads);
	$ad = ($mxl - $sl)/2;
	$adspace = '';
	for ($x=0;$x < $ad;$x++)
	{
		$adspace .= "&nbsp;"; 
	}
	return $adspace;
}
	$wday = substr($aStatement[date],8,2);
	if ($wday==1) $dday='st';
	elseif ($wday==2) $dday='nd';
	elseif ($wday==3) $dday='rd';
	else $dday='th';
	$ldate = strtotime($aStatement[date]);
	$ds = date('Y-m-d',strtotime("+30 days",$ldate));
	$due1 =strtotime(substr($ds,0,8).str_pad($wday,2,'0',STR_PAD_LEFT));
	$ldate = strtotime($ds);
	$ds = date('Y-m-d',strtotime("+30 days",$ldate));
	$due2 =strtotime(substr($ds,0,8).str_pad($wday,2,'0',STR_PAD_LEFT));
	$ldate = strtotime($aStatement[date]);

	if ($aPL[account_id]==0)
		$account = $aPL[remark];
	else
	{
		$aPL['account'] = lookUpTableReturnValue('x','account','account_id','account',$aPL['account_id']);
		$account = $aPL['account'];
	}	
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
	
       <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="50" valign="top" width="100%" ><table width="100%" height="1%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td colspan="5" style="height:01in;">&nbsp;</td>
              </tr>
 		      <tr>
                <td class="body1" style="width:1.5in;">&nbsp;</td>
				<td class="body1" style="width:0.75in;">&nbsp;</td>
				<td class="body1" style="width:2.5in;">&nbsp;</td>
				<td width="139" class="body1" style="width:1.25in;">&nbsp;</td>
				<td width="279" class="body1" style="width:1.25in;">&nbsp;</td>
              </tr>
			  <tr>  
			  	<td class="text1">LOAN RELEASE No.</td>
				<td class="text1"><?=str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT);?></td>
				<td colspan="3" class="text1">&nbsp;&nbsp;&nbsp;<?='Date & Time Printed : '.date('m/d/Y g:ia').'    Times Printed '.adjustRight($aLoan[printx],3);?></td>
			  </tr>	
			  <tr>
			    <td class="text1">Pay To Client</td>
				<td colspan="2" class="text1"><?=strtoupper(htmlspecialchars($aLoan['account']));?></td>
				<td colspan="2" class="text1"><?=lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type_code',$aLoan['loan_type_id']);?></td>
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
