
<body leftmargin="2" topmargin="2" marginwidth="2" marginheight="2">
<?
	if ($message!="") echo "<center> $message </center>";
?>
<br><br>
<div align=center>Today is 
  <?=date("l F j, Y g:ia");?>
</div>
<form name="form1" method="post" action="?p=authenticate">
  <table width="22%" cellspacing="0" align="center">
    <tr bgColor="#CCCCCC"> 
      <td> <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#E2E2E2">
          <tr bgcolor="#009999"> 
            <td height="25" colspan="2"><strong><font color="#FFCC33" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <img src="../graphics/team_wksp.gif" width="16" height="17">LOGIN</font></strong></td>
          </tr>
          <tr bgcolor="#EFEFEF"> 
            <td width="44%" height="25"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Username</b></font></div></td>
            <td width="56%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input type="text" name="username">
              </font></td>
          </tr>
          <tr bgcolor="#EFEFEF"> 
            <td width="44%"> <div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Password</b></font></div></td>
            <td width="56%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input type="password" name="mpassword">
              </font></td>
          </tr>
          <tr bgcolor="#EFEFEF"> 
            <td colspan="2"> <div align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input type="submit" name="Submit" value="Submit">
                </font></div></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<div align="center"><br>
  <br>
  <br>
  <br>
  <br>
</div>
<address>
<center>
  <img src="../graphics/mysql-powered-by.JPG" width="60" height="30"> <img src="../graphics/php-small-white.gif" width="88" height="31"><img src="../graphics/worm_in_hole.gif" width="23" height="33"><br>
  <font size="2">Developed by: Jared O. Santibañez, ECE, MT </font><br>
</center>
</address>
<script>
this.form1.username.focus()
</script>