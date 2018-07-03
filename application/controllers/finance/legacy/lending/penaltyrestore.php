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
<?
if ($from_date == '') $from_date=date('m/d/Y');
if ($to_date == '') $to_date=date('m/d/Y');

if (!session_is_registered('aBIN'))
{
	session_register('aBIN');
	$aBIN=null;
	$aBIN=array();
}
?> 

<form action="" method="post" name="f1" id="f1">
  <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="p1" value="Go">
        </font></td>
    </tr>
    <tr>
      <td><hr color="red"></td>
    </tr>
  </table>
<?
if ($p1 == 'Go')
{
	$q = "select ledger.type,ledger.account_id,ledger.debit,ledger.status,ledger.date,
				 ledger.ledger_id,ledger.reference,ledger.releasing_id,account.account
	 		from ledger,account 
			where account.account_id=ledger.account_id and ledger.date > '2010-12-31' and ledger.type='P'
			order by date";
	$qr = pg_query($q);
	$cc=0;
	
	while ($r = pg_fetch_object($qr))
	{
		if (substr($r->date,0,4)!= '2011' or $r->status !='C') continue;
		$cc++;
		$ledger_id = $r->ledger_id;			
		$details .=	adjustRight(number_format($cc,0),8).'.  '.adjustSize($r->date,10).' '.
					adjustSize($r->type,3).' '.
					adjustSize($ledger_id,8).' '.					
					adjustSize($r->account_id,8).' '.
					adjustSize($r->account,35).' '.
					adjustRight(number_format($r->debit,2),8).'  '.
					adjustSize($r->status,2).' '.
					adjustSize($r->reference,8).' '.
					adjustSize($r->releasing_id,8).' '.					
					"\n";
//		$qu = "update ledger set status = 'S' where ledger_id='$ledger_id'";
//					$qqqr = @pg_query($qu) or message(pg_errormessage());			
	}
	$details .= str_repeat('-',78)."\n\n";
	$details1 = $details;
	if ($p1 = 'Print Draft')
	{
		doPrint($header.$details);
	}	
?>
  <div align="center">
    <table width="1%" border="0" align="center" cellpadding="2" cellspacing="1">
      <tr> 
        <td height="27"><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr bgcolor="#000033"> 
              <td width="34%" nowrap><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
                <img src="../graphics/bluelist.gif" width="16" height="17">Penalty 
                Preview</strong></font></td>
              <td width="66%" align="right" nowrap>&nbsp; </td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td valign="top" bgcolor="#FFFFFF">
	   <textarea name="print_area" cols="90" rows="20"  wrap="off" readonly><?= $details1;?></textarea>
        </td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
<?
}
?>
</form>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
