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
function addspace($ads,$mxl=40)
{
	$sl = strlen($ads);
	$ad = $mxl - $sl;
	$adspace = ' '.$ads;
	for ($x=0;$x < $ad;$x++)
	{
		$adspace .= "&nbsp;"; 
	}
	return $adspace;
}
	$civils = array('S'=>'Single','M'=>'Married','W'=>'Widow');

	$q = "select 
				account.address, 
				sss,
				member,
				account_group as mship,
				account.telno,
				spouse,
				spouse_sss,
				civil_status,
				clientbank,
				date_child21,
				date_child21b,
				date_child21c,
				date_child21d,
				child1, child2, child3, child4				
			from 
				account, 
				account_group,
				clientbank 
			where 
				account_group.account_group_id = account.account_group_id and
				clientbank.clientbank_id = account.clientbank_id and
				account.account_id='".$aLoan[account_id]."'";
	$loaninfo=fetch_assoc($q);

	$actname = explode('/',$aLoan[account]);
	$accountname = $actname[0];
	$age = $aLoan['age'];
	$birthday = ymd2mdy($aLoan['date_birth']).' ';
	$branch_address = lookUpTableReturnValue('x','branch','branch_id','branch_address',$aLoan['branch_id']);

	if ($aLoan['withdraw_day']==1) $dday='st';
	elseif ($aLoan['withdraw_day']==2) $dday='nd';
	elseif ($aLoan['withdraw_day']==3) $dday='rd';
	else $dday='th';
	$ldate = strtotime($aLoan[date]);
	$ds = date('Y-m-d',strtotime("+30 days",$ldate));
	$datestart =substr($ds,0,8).str_pad($aLoan[withdraw_day],2,'0',STR_PAD_LEFT);
	$dd = $aLoan[term]*30;
	$dterm = '+'.$dd.' days';
	$ds = date('Y-m-d',strtotime($dterm,$ldate));
	$dateend =substr($ds,0,8).str_pad($aLoan[withdraw_day],2,'0',STR_PAD_LEFT);

	$q = "select * from comaker where releasing_id='".$aLoan[releasing_id]."'";
	$rc = fetch_assoc($q);
	$aComaker = $rc;
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
	<div id="print_area"  name="print_area" style="position:relative; width:95%; height:inherit; z-index:1; overflow: auto; left:5%;">          
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" style="page-break-after:always">
            <tr> 
              <td height="50" valign="top" width="100%" >
			  <table width="100%" height="1%" border="0" cellpadding="0" cellspacing="0">
			    <tr>
				  <td colspan="7" align="center"><img src="../graphics/jgmlogo.png" /></td>
				  </tr>
                  <tr> 
                    <td colspan="7" align="center" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:14px;"><b>JGM FINANCE CORPORATION</b></td>
                  </tr>
                  <tr> 
                    <td colspan="7" align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:8pt;"><?=$branch_address;?></td>
                  </tr>
			  <tr>				    
				    <td class="body1" width="9%">&nbsp;</td>
				    <td class="body" width="12%">&nbsp;</td>
				    <td class="body" width="26%">&nbsp;</td>
				    <td class="body" width="4%">&nbsp;</td>
				    <td class="body" width="16%">&nbsp;</td>
				    <td class="body" width="13%">&nbsp;</td> 
				    <td class="body1" width="20%">&nbsp;</td>
			  </tr>	  
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Name of Applicant: <u><?=addspace($accountname,90);?></u>&nbsp;&nbsp;Age : <u><?=addspace($age,15);?></u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:auto; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
(<i>Last, First, Middle</i>)</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Address: <u><?=addspace($loaninfo[address],60);?></u>&nbsp; Tel./Cell# : <u><?=addspace($loaninfo[telno],15);?></u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Birthday: <u><?=addspace($birthday,15);?></u>&nbsp; Status : <u><?=addspace($loaninfo[civil_status],15);?></u> Name Spouse : <u><?=addspace($loaninfo[spouse],67);?></u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[mship],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[mship],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Card No : <u><?=addspace($aLoan[bank_account],22);?></u>
						<?
							if ($aLoan[mclass]=='1')
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Pensioner	&nbsp;&nbsp;
						<?
							if ($aLoan[mclass]=='2')
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Survivor	&nbsp;&nbsp;
						<?
							if ($aLoan[mclass]=='3')
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Guardian	&nbsp;&nbsp;
				    </td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Member's Name if Survivor/Guardian: <u><?=addspace($loaninfo[member],35);?></u>&nbsp;&nbsp;SSS/GSIS Card No.  <u><?=($aLoan[mclass]!='2'?addspace($loaninfo[sss],15):addspace($loaninfo[spouse_sss],15));?></u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Type of Claim: 
						<?
							if ($aLoan[mclass]==1)
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Retirement	&nbsp;&nbsp;
						<?
							if ($aLoan[mclass]==2)
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Death &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Disability : 
						<?
							if ($aLoan[mclass]==4)
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Partial	&nbsp;&nbsp;
						<?
							if ($aLoan[mclass]==3)
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;Total
				    </td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Depository Bank: <u><?=addspace($loaninfo[clientbank],35);?></u>&nbsp; Acct. No. : <u><?=addspace($aLoan[bank_account],25);?></u> Pension : <u><?=addspace(number_format($aLoan[salary],2),20);?></u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:16pt; vertical-align:middle;">Total Loan Granted: <u><?=addspace(number_format($aLoan[gross],2),25);?></u>&nbsp; Term : <u><?=addspace($aLoan[term],25);?></u> Number of Installment : <u><?=addspace($aLoan[term],20);?></u></td>
				  </tr>					  
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:20pt; vertical-align:middle;">Dependents/Beneficiaries:</td>
				  </tr>
				  <tr><td>&nbsp;</td>					  
				  	<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:14pt; vertical-align:middle;">NAME</td>
					<td>&nbsp;</td>
				  	<td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:14pt; vertical-align:middle;">BIRTHDATE</td>
				  </tr>			
				  <?
				  if ($loaninfo[child1]!='')
				  {
				  ?>
				  <tr><td>&nbsp;</td>
				  	<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=$loaninfo[child1];?></td>
					<td>&nbsp;</td>
				  	<td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=ymd2mdy($loaninfo[date_child21]);?></td>
				  </tr>
				  <?
				  }
				  if ($loaninfo[child2]!='')
				  {
				  ?>
				  <tr><td>&nbsp;</td>
				  	<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=$loaninfo[child2];?></td>
					<td>&nbsp;</td>
				  	<td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=ymd2mdy($loaninfo[date_child21b]);?></td>
				  </tr>
				  <?
				  }
				  if ($loaninfo[child3]!='')
				  {
				  ?>
				  <tr><td>&nbsp;</td>
				  	<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=$loaninfo[child3];?></td>
					<td>&nbsp;</td>
				  	<td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=ymd2mdy($loaninfo[date_child21c]);?></td>
				  </tr>
				  <?
				  }
				  if ($loaninfo[child4]!='')
				  {
				  ?>
				  <tr><td>&nbsp;</td>
				  	<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=$loaninfo[child4];?></td>
					<td>&nbsp;</td>
				  	<td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;"><?=ymd2mdy($loaninfo[date_child21d]);?></td>
				  </tr>
				  <?
				  }
				  ?>
				  <tr><td style="height:6pt;">&nbsp;</td></tr>
				  <tr><td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I hereby certify that the above information are true and correct and the <b>JGM Finance Corporation</b> reserves the right to reject my application for any incorrect/untrue data which I have provided.</td></tr>
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" align="center" style="border-bottom:thin solid #000000; height:20pt; vertical-align:bottom"><?=$accountname;?></td>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Applicant's Signature Over Printed Name</td>
				  </tr>
				  <tr><td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:left;
height:14pt; vertical-align:middle;"><b>CO-MAKER'S DATA 1</b></td>
				  </tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Name:</td>
					<td colspan="4" align="center" valign="baseline" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;"><?=$aLoan[comaker1];?></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age : </td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td></tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">&nbsp;</td>
					<td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Last&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Middle</i></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Residence Address : </td>
					<td colspan="5" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000; vertical-align:bottom"><?=$aLoan[comaker1_address];?></td>
				</tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Office Address : </td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">Tel./Cell#</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;  border-bottom:thin solid #000000;">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[cosss1],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[cogsis1],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Pensioner&nbsp;&nbsp;&nbsp;&nbsp; GSIS/SSS No: <u>&nbsp;&nbsp;<?=$loaninfo[cosssno1];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;Monthly Pension : <u>&nbsp;&nbsp;<?=$loaninfo[copension1];?>&nbsp;&nbsp;</u>
				    </td>
				  </tr> 
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">JGM Pensioner ? <u>&nbsp;&nbsp;<?=$loaninfo[cojgm1];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;If Yes, Outstanding Balance to Date  : <u>&nbsp;&nbsp;<?=$loaninfo[cobalance1];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;Term  : <u>&nbsp;&nbsp;<?=$loaninfo[coterm1];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Relation to Applicant/Maker <u>&nbsp;&nbsp;<?= $aLoan['comaker1_relation'];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;
height:17pt; vertical-align:middle;"><i>To JGM Finance</i></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I agree to become the <b>co-maker</b> of Applicant <u>&nbsp;&nbsp;<?=$accountname;?>&nbsp;&nbsp;</u> for a loan of <u>&nbsp;&nbsp;<?=number_format($aLoan[gross],2);?>&nbsp;&nbsp;</u> PESOS and to co-sign the Promissory Note executed by the applicant.</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>I AM FULLY AWARE OF THE RESPONSIBILITY WHICH I WILL ASSUME IN SIGNING THE PROMISSORY NOTE AS A CO-MAKER.</td>
				  </tr>	
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td><td colspan="3" align="center" style="border-bottom:thin solid #000000; vertical-align:bottom"><?=$aLoan[comaker1];?></td></tr>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Signature Over Printed Name</td>
				  </tr>
				  <tr><td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:left;
height:14pt; vertical-align:middle;"><b>CO-MAKER'S DATA 2</b></td>
				  </tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Name:</td>
					<td colspan="4" align="center" valign="baseline" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;"><?=$aLoan[comaker2];?></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age : </td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td></tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">&nbsp;</td>
					<td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Last&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Middle</i></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Residence Address : </td>
					<td colspan="5" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000; vertical-align:bottom"><?=$aLoan[comaker2_address];?></td>
				</tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Office Address : </td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">Tel./Cell#</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;  border-bottom:thin solid #000000;">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[cosss2],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[cogsis2],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Pensioner&nbsp;&nbsp;&nbsp;&nbsp; GSIS/SSS No: <u>&nbsp;&nbsp;<?=$loaninfo[cosssno2];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;Monthly Pension : <u>&nbsp;&nbsp;<?=$loaninfo[copension2];?>&nbsp;&nbsp;</u>
				    </td>
				  </tr> 
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">JGM Pensioner ? <u>&nbsp;&nbsp;<?=$loaninfo[cojgm2];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;If Yes, Outstanding Balance to Date  : <u>&nbsp;&nbsp;<?=$loaninfo[cobalance2];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;Term  : <u>&nbsp;&nbsp;<?=$loaninfo[coterm2];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Relation to Applicant/Maker <u>&nbsp;&nbsp;<?= $aLoan['comaker2_relation'];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;
height:17pt; vertical-align:middle;"><i>To JGM Finance</i></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I agree to become the <b>co-maker</b> of Applicant <u>&nbsp;&nbsp;<?=$accountname;?>&nbsp;&nbsp;</u> for a loan of <u>&nbsp;&nbsp;<?=number_format($aLoan[gross],2);?>&nbsp;&nbsp;</u> PESOS and to co-sign the Promissory Note executed by the applicant.</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;<b>I AM FULLY AWARE OF THE RESPONSIBILITY WHICH I WILL ASSUME IN SIGNING THE PROMISSORY NOTE AS A CO-MAKER.</td>
				  </tr>
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td><td colspan="3" align="center" style="border-bottom:thin solid #000000; vertical-align:bottom"><?=$aLoan[comaker2];?></td></tr>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Signature Over Printed Name</td>
				  </tr>
        	</table>
			</td>
           </tr>
          </table>
 		<?
		if ($aComaker[comake3]!='')
		{
		?>
		  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr> 
              <td height="50" valign="top" width="100%" >
			  <table width="100%" height="1%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
				    <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:left;
height:14pt; vertical-align:middle;"><b>CO-MAKER'S DATA 3  </b></td>
				  </tr>
				  <tr><td width="6%" colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Name:</td>
					<td colspan="4" align="center" valign="baseline" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;"><?=$aComaker[comake3];?></td>
					<td width="9%" colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age : </td>
					<td width="25%" colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
				  </tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">&nbsp;</td>
					<td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Last&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Middle</i></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Residence Address : </td>
					<td colspan="5" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000; vertical-align:bottom"><?=$aComaker[comake3_address];?></td>
				</tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Office Address : </td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">Tel./Cell#</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;  border-bottom:thin solid #000000;">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[cosss3],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[cogsis3],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Pensioner&nbsp;&nbsp;&nbsp;&nbsp; GSIS/SSS No: <u>&nbsp;&nbsp;<?=$loaninfo[cosssno3];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;Monthly Pension : <u>&nbsp;&nbsp;<?=$loaninfo[copension3];?>&nbsp;&nbsp;</u>
				    </td>
				  </tr> 
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">JGM Pensioner ? <u>&nbsp;&nbsp;<?=$loaninfo[cojgm3];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;If Yes, Outstanding Balance to Date  : <u>&nbsp;&nbsp;<?=$loaninfo[cobalance3];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;Term  : <u>&nbsp;&nbsp;<?=$loaninfo[coterm3];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Relation to Applicant/Maker <u>&nbsp;&nbsp;<?= $aComaker['comake3_relation'];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;
height:17pt; vertical-align:middle;"><i>To JGM Finance</i></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I agree to become the <b>co-maker</b> of Applicant <u>&nbsp;&nbsp;<?=$accountname;?>&nbsp;&nbsp;</u> for a loan of <u>&nbsp;&nbsp;<?=number_format($aLoan[gross],2);?>&nbsp;&nbsp;</u> PESOS and to co-sign the Promissory Note executed by the applicant.</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>I AM FULLY AWARE OF THE RESPONSIBILITY WHICH I WILL ASSUME IN SIGNING THE PROMISSORY NOTE AS A CO-MAKER.</td>
				  </tr>	
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td><td colspan="3" align="center" style="border-bottom:thin solid #000000; vertical-align:bottom"><?=$aComaker[comake3];?></td></tr>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Signature Over Printed Name</td>
				  </tr>
				  <tr><td>&nbsp;</td></tr>
				  <tr>
				    <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:left;
height:14pt; vertical-align:middle;"><b>CO-MAKER'S DATA 4 </b></td>
				  </tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Name:</td>
					<td colspan="4" align="center" valign="baseline" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;"><?=$aComaker[comake4];?></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age : </td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td></tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">&nbsp;</td>
					<td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Last&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Middle</i></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Residence Address : </td>
					<td colspan="5" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000; vertical-align:bottom"><?=$aComaker[comake4_address];?></td>
				</tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Office Address : </td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">Tel./Cell#</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;  border-bottom:thin solid #000000;">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[cosss4],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[cogsis4],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Pensioner&nbsp;&nbsp;&nbsp;&nbsp; GSIS/SSS No: <u>&nbsp;&nbsp;<?=$loaninfo[cosssno4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;Monthly Pension : <u>&nbsp;&nbsp;<?=$loaninfo[copension4];?>&nbsp;&nbsp;</u>
				    </td>
				  </tr> 
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">JGM Pensioner ? <u>&nbsp;&nbsp;<?=$loaninfo[cojgm4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;If Yes, Outstanding Balance to Date  : <u>&nbsp;&nbsp;<?=$loaninfo[cobalance4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;Term  : <u>&nbsp;&nbsp;<?=$loaninfo[coterm4];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Relation to Applicant/Maker <u>&nbsp;&nbsp;<?= $aComaker['comake4_relation'];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;
height:17pt; vertical-align:middle;"><i>To JGM Finance</i></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I agree to become the <b>co-maker</b> of Applicant <u>&nbsp;&nbsp;<?=$accountname;?>&nbsp;&nbsp;</u> for a loan of <u>&nbsp;&nbsp;<?=number_format($aLoan[gross],2);?>&nbsp;&nbsp;</u> PESOS and to co-sign the Promissory Note executed by the applicant.</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>I AM FULLY AWARE OF THE RESPONSIBILITY WHICH I WILL ASSUME IN SIGNING THE PROMISSORY NOTE AS A CO-MAKER.</td>
				  </tr>	
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td><td colspan="3" align="center" style="border-bottom:thin solid #000000; vertical-align:bottom"><?=$aLoan[comake4];?></td></tr>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Signature Over Printed Name</td>
				  </tr>
				  <tr><td>&nbsp;</td></tr>
				  <tr>
				    <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:left;
height:14pt; vertical-align:middle;"><b>CO-MAKER'S DATA 5 </b></td>
				  </tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Name:</td>
					<td colspan="4" align="center" valign="baseline" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;"><?=$aComaker[comake5];?></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age : </td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td></tr>
				  <tr><td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">&nbsp;</td>
					<td colspan="4" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Last&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;First&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Middle</i></td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">&nbsp;</td></tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Residence Address : </td>
					<td colspan="5" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000; vertical-align:bottom"><?=$aComaker[comake5_address];?></td>
				</tr>
				<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:left;
height:17pt; vertical-align:middle;">Office Address : </td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; border-bottom:thin solid #000000;">&nbsp;</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;">Tel./Cell#</td>
					<td colspan="1" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt;  border-bottom:thin solid #000000;">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Membership: 
						<?
							if ('SSS'==substr($loaninfo[cosss4],0,3))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;SSS	&nbsp;&nbsp;
						<?
							if ('GSIS'==substr($loaninfo[cogsis4],0,4))
							{
						?>
							<img src="../graphics/checked.gif" height="16" width="16">
						<?
							} else
							{
						?>
							<img src="../graphics/unchecked.gif" height="16" width="15">
						<?
							}
						?>		
						&nbsp;GSIS Pensioner&nbsp;&nbsp;&nbsp;&nbsp; GSIS/SSS No: <u>&nbsp;&nbsp;<?=$loaninfo[cosssno4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;Monthly Pension : <u>&nbsp;&nbsp;<?=$loaninfo[copension4];?>&nbsp;&nbsp;</u>
				    </td>
				  </tr> 
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">JGM Pensioner ? <u>&nbsp;&nbsp;<?=$loaninfo[cojgm4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;If Yes, Outstanding Balance to Date  : <u>&nbsp;&nbsp;<?=$loaninfo[cobalance4];?>&nbsp;&nbsp;</u>&nbsp;&nbsp;Term  : <u>&nbsp;&nbsp;<?=$loaninfo[coterm4];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">Relation to Applicant/Maker <u>&nbsp;&nbsp;<?= $aComaker['comake5_relation'];?>&nbsp;&nbsp;</u></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;
height:17pt; vertical-align:middle;"><i>To JGM Finance</i></td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:12pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I agree to become the <b>co-maker</b> of Applicant <u>&nbsp;&nbsp;<?=$accountname;?>&nbsp;&nbsp;</u> for a loan of <u>&nbsp;&nbsp;<?=number_format($aLoan[gross],2);?>&nbsp;&nbsp;</u> PESOS and to co-sign the Promissory Note executed by the applicant.</td>
				  </tr>	
				  <tr>
				  	<td colspan="7" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:justify;
height:17pt; vertical-align:middle;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>I AM FULLY AWARE OF THE RESPONSIBILITY WHICH I WILL ASSUME IN SIGNING THE PROMISSORY NOTE AS A CO-MAKER.</td>
				  </tr>	
				  <tr><td>&nbsp;</td></tr>	
				  <tr><td colspan="4">&nbsp;</td><td colspan="3" align="center" style="border-bottom:thin solid #000000; vertical-align:bottom"><?=$aComaker[comake5];?></td></tr>
				  <tr><td colspan="4">&nbsp;</td>
					<td colspan="3" style="font-family:Arial, Helvetica, sans-serif; font-size:10pt; text-align:center;
height:12pt; vertical-align:middle;">Signature Over Printed Name</td>
				  </tr>
			  </table>
          </table>
		<?
		}
		?>
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