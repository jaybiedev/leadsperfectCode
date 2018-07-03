<?
if (!chkRights2('financereport','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Finance Reports ]...");
	exit;
}

if (!session_is_registered('aPenalty'))
{
	session_register('aPenalty');
	$aPenalty = null;
	$aPenalty = array();
}
if ($date == '') $date = date('Y-m-d');
if ($month == '') $month = date('m')-1;
if ($withinloan == '') $withinloan =10;
if ($beyondloan == '') $beyondloan = 20;
if ($year == '')
{
	if (date('m') <2)
	{
		$year = date('Y')-1;
	}
	else
	{
		$year = date('Y');
	}
}
?> 
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>

<br>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr> 
      <td height="23" colspan="3" background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"  color="#EFEFEF"> 
        <strong><img src="../graphics/bluelist.gif" width="16" height="17"> Interest 
        Income Report</strong></font></td>
    </tr>
    <tr> 
      <td width="21%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Account Group</font><br> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name = "account_group_id">
          <option value=''>All Account Groups</option>
          <?
				$q = "select * from account_group where enable order by account_group";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($account_group_id == $r->account_group_id)
					{
						echo "<option value=$r->account_group_id selected>$r->account_group</option>";
					}
					else
					{	
						echo "<option value=$r->account_group_id>$r->account_group</option>";
					}	
				}
				
			?>
        </select>
        </font> </td>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Loan Type </font><br> <select name="loan_type_id" id="loan_type_id"  style="width:180">
          <option value=''>All Loan Types</option>
          <?
	  $qr = pg_query("select * from loan_type where enable");
	  while ($r=pg_fetch_object($qr))
	  {
	  	if ($loan_type_id == $r->loan_type_id || ($loan_type_id == '' && $r->loan_interest != 'A'))
		{
		  	echo "<option value=$r->loan_type_id selected>$r->loan_type</option>";
		}
		else
		{
		  	echo "<option value=$r->loan_type_id>$r->loan_type</option>";
		}	
	  }
	  ?>
        </select> </td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Applicable 
        Month/Yr<br>
        <?= lookUpMonth('month',$month);?>
        <input name="year" type="text" id="year" value="<?= $year;?>" size="5" maxlength="5">
        </font></td>
      <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="button" id="p1" value="Go" onCLick="wait('Please wait. Processing data...');xajax_rInterestIncome(xajax.getFormValues('f1'),'Go');">
        <input name="p13" type="button" id="p13" value="Print Draft"  onCLick="wait('Please wait. Processing data...');xajax_rInterestIncome(xajax.getFormValues('f1'),'Print Draft');">
        <input name="p122" type="button" id="p122" value="Print">
        <input type="button" name="Button" value="Close" onClick="window.location='?p='";>
        </font></td>
    </tr>
    <tr> 
      <td height="20" colspan="3"><hr color="red" height="1"></td>
    </tr>
    <tr valign="top" > 
      <td height="300px" colspan="3"><div id="grid" name="grid" style="position:virtual; width:100%; height:100%; z-index:1; overflow: scroll;">
          <textarea name="textarea" id="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea>
        </div></td>
    </tr>
    <tr align="center"> 
      <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="p132" type="button" id="p132" value="Print Draft"  onCLick="wait('Please wait. Processing data...');xajax_rInterestIncome(xajax.getFormValues('f1'),'Print Draft');">
        </font> 
        <input name="p12" type="button" id="p12" value="Print"></td>
    </tr>
  </table>

</form>
