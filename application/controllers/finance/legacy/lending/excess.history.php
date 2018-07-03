<?
if ($aExcess['account_id'] == '')
{
	echo "NO ACCOUNT SPECIFIED...";
	exit;
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
	
  <div id="pop" style="position:absolute; width:650px; height:400px; z-index:1; top: 15%; left: 30%;"  onClick="this.style.visibility='hidden'">
<form name="form1" method="post" action="">
    <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%" height="10"><img src="../graphics/table0_upper_left.PNG" width="8" height="22"></td>
        <td width="83%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Excess 
          Withdrawal/Advances History</b></font></td>
        <td width="15%" align="right" background="../graphics/table0_horizontal.PNG">
		<img src="../graphics/table_close.PNG" width="21" height="20" onClick="document.getElementById('pop').display='hidden'"></td>
        <td width="1%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="22"></td>
      </tr>
      <tr valign="top" bgcolor="#A4B9DB" height="100px"> 
        <td colspan="4"> <div id="Layer1" style="position:virtual; width:100%; height:100%; z-index:1 overflow:scroll"> 
            <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
              <!--DWLayoutTable-->
              <tr bgcolor="#EFEFEF" height="20px"> 
                <td width="47" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
                <td width="61" valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Starting</font></td>
                <td width="60" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Net </font></td>
                <td width="49" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">User</font></td>
                <td width="173" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
              </tr>
              <?

			$q = "select * from wexcess where status!='C' and type='C' and account_id = '".$aExcess['account_id']."' order by date desc ";
			$qr = @pg_query($q) or message(pg_errormessage());
			$wcc=0;
			while ($r = @pg_fetch_assoc($qr))
			{
				$wcc++;

				$details = ' ';
				$myear = substr($r['date'],0,4);
				$year_flag = $months = 0;
				for ($cc = 0 ; $cc< 8; $cc++)
				{
					$mc = $r['starting_month'] + $cc ;
					if ($mc > 13)
					{
						$mc -= 13;
						if ($year_flag == '0')
						{
							$myear++;
							$year_flag = 1;
						}
					}
					if ($mc == 13)
					{
						$cmonth = '13th Month';
					}
					else
					{
						$cmonth = cmonth($mc);
					}
					$mi = 'month'.($cc+1);
					
					if ($r[$mi] > 0.00)
					{
						$months++;
//						$details .= $cmonth.', '.$myear.' '.adjustRight(number_format($$mi,2),10).' ;';
						$details .= $cmonth.' '.adjustRight(number_format($r[$mi],2),10).' ;';
					}
				}	
							
				?>
              <tr> 
                <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?= ymd2mdy($r['date']);?>
                  </font></td>
                <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?= cmonth($r['starting_month']);?>
                  </font></td>
                <td valign="top" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?= number_format($r['net_amount'],2);?>
                  </font></td>
                <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                  <?= lookUpTableReturnValue('x','admin','admin_id','name',$r['admin_id']);?>
                  </font></td>
                <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$details;?></font></td>
              </tr>
              <?
			  }
			  ?>
            </table>
          </div></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
</form>
  </div>

