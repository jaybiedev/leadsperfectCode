 <br>
 <br>
 <br>
 <table width="100%" height="60%" border="0" cellpadding="2" cellspacing="0">
  <tr valign="top"> 
    <td width="47%" align="center">
	<form name="f1" method="post" action="">
        <?
	if ($p1 == 'Go' && $xSearch != '')
	{
		$q = "select * from account where account ilike '%$xSearch%' order by account ";
		$qr = @pg_query($q) or message(pg_errormessage());
	?>
       <table width="90%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td colspan="2"><font size="6" face="Bookman Old Style"> <img src="../graphics/eyeglass.gif" width="20" height="20"> 
              <strong>PENSIONER LOGIN </strong> </font> 
              <hr color="#993300"></td>
          </tr>
          <tr valign="middle"> 
            <td width="16%"><font size="5" face="Verdana, Arial, Helvetica, sans-serif">Account Name</font></td>
			<td width="84%"><font size="5" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="account" type="text" id="account" value="<?= $aPenlogin['account'];?>" size="60">
              </font></td>
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="address" type="text" id="address" value="<?= $aPenlogin['address'];?>" size="60">
              </font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="branch" type="text" id="branch" value="<?= $aPenlogin['branch'];?>" size="60">
              </font></td>			
          </tr>
		  <tr> <td colspan="2">&nbsp;</td></tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Sequence No.</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="seqno" type="text" id="seqno" value="<?= $aPenlogin['seqno'];?>" size="60">
              </font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date" type="text" id="date" value="<?= $aPenlogin['date'];?>" size="60">
              </font></td>			
          </tr>
       </table>
       <?
	}
	?>
   </form></td>
</table>
<div align="center" valign=bottom> <img src="../graphics/elephantSmall.gif" width="50" height="50"> 
  <img src="../graphics/php-small-white.gif" width="88" height="31"><img src="../graphics/worm_in_hole.gif" width="23" height="33"><br>
  <em><font size="2">Developed by: Jared O. Santibañez, ECE, MT </font> </em> 
  <font size="2"><br>
  email: <a href="mailto:%20jay_565@yahoo.com">jay_565@yahoo.com</a><br>
  </font> </div>
