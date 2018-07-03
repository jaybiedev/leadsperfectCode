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
	font:Verdana, Arial, Helvetica, sans-serif;
	size:10pt;
	text-align:center;
	height:auto;
	vertical-align:middle;
}
.body2 {
	font:Verdana, Arial, Helvetica, sans-serif;
	size:10pt;
	text-align:left;
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
				  <td colspan="2" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:12px;">&nbsp;</td>			 
				</tr>  
                <tr> 
                    <td colspan="7" align="center" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:14px;"><b>ACCOUNT INFO </b></td>
                </tr>
                <tr> 
                    <td colspan="4" align="center" >&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font:8pt; text-align:right;"><u>&nbsp;&nbsp;<?=lookUpTableReturnValue('x','branch','branch_id','branch',$aaccount['branch_id']);?>&nbsp;, Philippines</u></td>
                </tr>
			    <tr>				    
				    <td class="body" width="19%">Name</td>
				    <td class="body" width="11%">&nbsp;</td>
				    <td class="body" width="14%">&nbsp;</td>
				    <td class="body" width="19%">&nbsp;</td>
				    <td class="body" width="4%">&nbsp;</td>
				    <td class="body" width="13%">Status</td> 
				    <td class="body" width="7%">Enable</td>
			    </tr>	  
				<tr>
				  	<td colspan="5" class="body2"><?=$aaccount['account'];?></td>
				  	<td align="center" style="font:Arial, Helvetica, sans-serif; font-weight: bold; font-size:8pt";><?=($aaccount['account_status']=='L'? ' Legal ':($aaccount['account_status']=='I'?' InActive ':($aaccount['account_status']=='A'?' Active ':'')));?></td>
				  	<td align="center" style="font:Arial, Helvetica, sans-serif; font-weight: bold; font-size:8pt";><?=($aaccount[enable]?' Yes ':'  No');?></td>
				</tr>	
				<tr><td colspan="7">&nbsp;</td></tr>
				<tr>
				     <td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;">
					 <b>PERSONAL INFO</b></td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">
					 <b>Classification</b></td>
				</tr>
				<tr class="body"><td colspan="7">&nbsp;</td></tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Birth Date</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[date_birth];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Age</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[age];?></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="3"><?= ($aaccount['mclass'] == '1'  || $aaccount['mclass'] == ''? 'Pensioner (Personal )' : ($account['mclass']=='3'?'Permanent Disability':($account['mclass']=='4'?'Temporary Disability':($account['mclass']=='5'?'Guardian':''))));?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Gender</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[gender];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Civil Status</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[civil_status];?></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="3"><?= ($aaccount['mclass'] == '2' ? 'Survivor/Widower/Beneficiary' : ($account['mclass']=='4'?'Remaining Pension Months:'.$aaccount['npension']:''));?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;" colspan="3"><i>If Survivor/Guardian</i></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?= ($aaccount['mclass'] == '2' ? '1. '.$aaccount['child1']: '');?></td>
					 <td width="13%" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= ($aaccount['mclass'] == '2' ? ymd2mdy($aaccount['date_child21']): '');?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Member's Name</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[member];?></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?= ($aaccount['mclass'] == '2' ? '2. '.$aaccount['child2']: '');?></td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= ($aaccount['mclass'] == '2' ? ymd2mdy($aaccount['date_child21b']): '');?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">JGM Clients Name</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[firstname];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[lastname];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount[middlename];?></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?= ($aaccount['mclass'] == '2' ? '3. '.$aaccount['child3']: '');?></td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= ($aaccount['mclass'] == '2' ? ymd2mdy($aaccount['date_child21c']): '');?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;"><i>First Name</i></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;"><i>Last Name</i></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; text-align:justify;"><i>Middle Name</i></td>
					 <td>&nbsp;</td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?= ($aaccount['mclass'] == '2' ? '4. '.$aaccount['child4']: '');?></td>
					 <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= ($aaccount['mclass'] == '2' ? ymd2mdy($aaccount['date_child21d']): '');?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Home Address</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=substr($aaccount['address'],0,50);?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=substr($aaccount['address'],50,50);?></td>
					 <td>&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?= ($aaccount['nchangebank'] > '0' ? 'Bank Change (No of Times)' : '');?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= $aaccount['nchangebank'];?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Telephone (Home)</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['telno'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Member SSS No.</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['sss'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Spouse Name</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['spouse'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Spouse SSS No.</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['spouse_sss'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Remarks</td>
				     <td colspan="6" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$$aaccount['remarks'];?></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
				     <td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;">
					 <b>OTHER INFO</b></td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;
					 </td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Employer Ofc.</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= $aaccount['office'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Office Address</td>
				     <td colspan="6" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?= $aaccount['ofc_address'];?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Telephone (Office)</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['ofc_telno'];?></td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 1</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker1'];?></td>
					 <td>&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Relationship</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?=$aaccount['comaker1_relation'];?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 1 Address</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker1_address'];?></td>
					 <td>&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 2</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker2'];?></td>
					 <td>&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Relationship</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?=$aaccount['comaker2_relation'];?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 2 Address</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker2_address'];?></td>
					 <td>&nbsp;</td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 3</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker3'];?></td>
					 <td>&nbsp;</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Relationship</td>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="2"><?=$aaccount['comaker3_relation'];?></td>
				</tr>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">Comaker 3 Address</td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;"><?=$aaccount['comaker3_address'];?></td>
					 <td>&nbsp;</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
				     <td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;">
					 <b>Image</b></td>
				     <td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;">&nbsp;
					 </td>
				<tr>
				     <td style="font-family:Arial, Helvetica, sans-serif; font-size:9pt; text-align:justify;" colspan="3" rowspan="4"><img src="../photo/<?= $aaccount['pix'];?>" alt="pic" name="pix" width="200" height="200" id="pix" /></td>
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
 
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
<script>printIframe2('print_area')</script> 

<!--
<form>
<input type="button" value="Print this page" onclick="printDiv('print_area')">
</form>
-->