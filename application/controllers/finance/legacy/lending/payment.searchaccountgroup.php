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
<div id="popLayer" style="position:absolute; width:800px; height:350px; z-index:1; top: 28%; left: 5%; background-color: #0066CC; layer-background-color: #0066CC; border: 1px none #000000;">
  <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF">
    <tr> 
      <td height="23" background="../graphics/table_horizontal.PNG" ><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Select 
        Account Group</strong></font></td>
      <td align="right" background="../graphics/table_horizontal.PNG" ><img src="../graphics/table_close.PNG" width="21" height="21" onClick="document.getElementById('popLayer').style.visibility='hidden'"></td>
    </tr>
    <tr> 
      <td colspan="2"><div id="Layer2" style="position:relative; width:100%; height:320px; z-index:2; overflow: auto;"> 
          <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
            <tr bgcolor="#E1E7F1"> 
              <td width="5%" height="24"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
              <td width="45%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
                Group</font></strong></td>
              <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            </tr>
            <?
		$aPay['account_group'] = $account_group;
		$aPay['account_group_id'] = '';
		
		$q = "select * from account_group where enable and account_group ilike '$account_group%' order by account_group";
		$qr = pg_query($q) or message(pg_errormessage());
		$ctr=0;
		while ($r = pg_fetch_object($qr))
		{
			$ctr++;
		?>
            <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" onClick="window.location='?p=payment.entry&p1=selectAccountGroupId&id=<?= $r->account_group_id;?>'"> 
              <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=$ctr;?>
                .</font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href='?p=payment.entry&p1=selectAccountGroupId&id=<?= $r->account_group_id;?>'> 
                <?= $r->account_group;?>
                </a></font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
            </tr>
            <?
		}
		?>
          </table>
        </div></td>
    </tr>
  </table>
 </div>
