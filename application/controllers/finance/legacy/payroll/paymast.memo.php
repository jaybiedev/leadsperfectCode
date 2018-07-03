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
 <div id="payroll_memo" style="position:relative; width:99%; height:330px; z-index:3;">
    
  <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
    <tr valign="top" bgcolor="#E2E2E2"> 
      <td width="37%" bgcolor="#EFEFEF"><table width="100%%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <tr> 
            <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></strong></td>
            <td width="82%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Subject</font></strong></td>
          </tr>
		  <?
		  if ($paymast['paymast_id'] != '')
		  {
		  	$q = "select * from memo where paymast_id = '".$paymast['paymast_id']."'";
			$qr = @pg_query($q) or meesage1(pg_errormessage());
			while ($r = @pg_fetch_object($qr))
			{
					if (strlen($r->regarding)>25)
					{
						$regarding = substring($r->regarding,0,25).'...';
					}
					else
					{
						$regarding = $r->regarding;
					}
		  ?>
          <tr> 
            <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= ymd2mdy($r->date);?></font></td>
            <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
			<a href="#" onClick="xajax_viewmemo(<?= $r->memo_id;?>)"><?= $regarding;?></a></font></td>
          </tr>
		  <?
		  	}
		}
		?>
        </table></td>
      <td width="63%" bgcolor="#EFEFEF"><textarea name="memo"  id="memo" cols="80" rows="20" readOnly></textarea></td>
    </tr>
  </table>
  </div>
