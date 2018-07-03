 <table width="100%" height="60%" border="0" cellpadding="2" cellspacing="0">
  <tr> 
    <td colspan="2" align="center" valign="top"><p>&nbsp;</p>
      Credit Card Monitor<br>
      Transaction Date: 
      <?= date('F d, Y');?>
      <table width="50%" border="0" cellspacing="1" cellpadding="2">
        <tr bgcolor="#FFFFFF"> 
          <td align="center"><a href="?p=creditcard.transaction"><img src="../graphics/swipe.jpg" width="70" height="65" border="0"></a> 
            <a href="?p=sales"></a></td>
          <td align="center"> <a href="?p=sales"></a><a href="?p=creditcard.monitor"><img src="../graphics/monitor.jpg" width="65" height="65" border="0"></a></td>
          <td align="center"><a href="?p=home&p1=menu.setup"><img src="../graphics/bank.jpg" width="70" height="65" border="0"></a> 
            <a href="?p=browse_service"></a></td>
          <td align="center"><a href="?p=home&p1=menu.reports"><img src="../graphics/report.jpg" width="67" height="65" border="0"></a> 
            <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
            </font></td>
          <td align="center"><a href="?p=logout"><img src="../graphics/logout.jpg" width="65" height="65" border="0"></a> 
          </td>
        </tr>
        <tr> 
          <td height="20" align="center"><a href="?p=creditcard.transaction"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transaction</font></a></td>
          <td align="center"><a href="?p=creditcard.monitor"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Monitor</font></a></td>
          <td align="center"><a href="?p=home&p1=menu.setup"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Setup</font></a></td>
          <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=home&p1=menu.reports">Reports</a></font></td>
          <td align="center"><a href="?p=logout"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Logout</font></a> 
          </td>
        </tr>
      </table>
      <br>
	  <?
	  if ($p1 != '')
	  {
		include_once($p1.'.php');
	  }
	  else
	  {
	  ?>
        <table width="70%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr align="center" bgcolor="#EFEFEF"> 
            <td width="100%"><strong> Card Quick Search 
              <input name="search" type="text" id="search" value="<?=$search;?>" size="35">
              <input name="p1" type="button" id="p1" value="Search" onClick="window.location='?p=bankcard.browse&p1=Go'+'&search='+search.value+'&searchby=All'" >
              </strong></td>
          </tr>

        </table>
	<?
	}
	?>
      </td>
  </tr>
</table>
<table width="100%" height="30%" align="center"><tr><td bgcolor="#FFFFFF">
<div align="center" valign=bottom> <img src="../graphics/elephantSmall.gif" width="50" height="50"> 
  <img src="../graphics/php-small-white.gif" width="88" height="31"><img src="../graphics/worm_in_hole.gif" width="23" height="33"><br>
  <em><font size="2">Developed by: Jared O. Santibañez, ECE, MT </font> </em> 
  <font size="2"><br>
  email: <a href="mailto:%20jay_565@yahoo.com">jay_565@yahoo.com</a><br>
  </font> </div>
</td></tr></table>