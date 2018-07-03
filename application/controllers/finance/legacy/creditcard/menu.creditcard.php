<script>
function vMenu()
{
	fm1.action="?p="+fm1.p.value;
	fm1.submit();
}
</script>
<title>Hope - ePOS</title><body leftmargin="0" topmargin="0">
<form name="fm1" method="post" action="">
<table width="100%" height="10%" border="0" cellpadding="0" cellspacing="0" background="../graphics/menubar.gif" >
  <tr> 
      <td width="78%" height="44"><font size="5" face="Bookman Old Style " color="#E7C69A"><strong>
         <?= ($SYSCONF['BUSINESS_NAME'] ==''?'Hope Systems' : $SYSCONF['BUSINESS_NAME']);?></strong></font><br>
        <font face="Verdana" size="2" color="#FFFFCC">
         <?= ($SYSCONF['BUSINESS_ADDR']==''?'Villa Angela Subd., Bacolod City' : $SYSCONF['BUSINESS_ADDR']);?>		
		 </font></td>
      <td width="22%" align="center" valign="bottom" nowrap> <a href="?p="><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>Home</strong></font></a> <font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>| Menu</strong></font> &nbsp;  
        <?= lookUpMenu('p',
		array(
			'Bankcard Transaction'=>'creditcard.transaction',
			'Bank Payment'=>'creditcard.bankpayment',
			'-----------------------------------------'=>'',
			'Bankcard Types'=>'bankcard_type',
			'Bankcard Information'=>'bankcard',
			'------------------------------- ---------'=>'',
			'Daily Transaction Report'=>'report.dailytransaction',
			'Income Report'=>'report.income',
			'------------------------------ ----------'=>'',
			'data Backup'=>'databackup',
			'System Configuration'=>'sysconfig',
			'Password'=>'password',
			'Log-Out'=>'logout',
			'Home'=>'',
		),
		$p)
	;?>
        &nbsp;&nbsp;&nbsp; <br>
	<font face="Verdana" size="2" color="#FFFFCC"> 
      Date: 
      <?= date('m/d/Y');?> &nbsp;User: 
      <?= $ADMIN['username'];?>
      &nbsp; </font></td>
  </tr>
</table>
</form>
<?
if ($p == '' or !file_exists($p.".php"))
{
	include_once('home.php');
}
else
{
	include_once($p.".php");
}

function lookUpMenu($name,$arr,$value)
 {
  $str = "\n\t<select name=\"$name\" style=\"border:1px solid black\" onChange=\"vMenu()\">";
  $ctr = count($arr);
  while (list ($key, $val) = each ($arr))
  {
   if ($val == $value)
   {
    $str .= "\n\t\t<option value=\"$val\" selected>$key</option>";
   }
   else
   {
    $str .= "\n\t\t<option value=\"$val\">$key</option>";
   }
  }
  $str .= "\n\t</select>";
  return $str;
 }
 
?>