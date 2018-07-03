<script src="shortcuts.js" type="text/javascript"></script>
<script>
shortcut.add("*",function() {
			f1.action='?p=penlogin&p1=Clear';
			f1.submit();},{
			'type':'keypress',
			'propagate':false,
			'target':document
			});
shortcut.add("/",function() {
			f1.action='?p=penlogin&p1=ChangePin&aid=<?=$aid;?>';
			f1.submit();},{
			'type':'keypress',
			'propagate':false,
			'target':document
			});
</script>
<br>
<br>
<form name="f1" method="post" action="">
<table width="100%" height="60%" border="0" cellpadding="2" cellspacing="0">
  <tr valign="top"> 
    <td width="47%" align="center">
<?

$aid=$_REQUEST['aid'];
if ($p1 == 'Clear' and $pin=='')
{
	$p1 = '';
	$aid = '';
	$aPenlogin ='';
	$aPenlogin = array();
	$pin = '';
	$pwd = '';
}
if ($aid != '')
{
	$q = "select * from account where account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr) == '0')
	{
		echo "<script>alert('Account NOT found...')</script>";
		$p1='';
	}
	$r = @pg_fetch_assoc($qr);
	$aPenlogin = $r;
	$aPenlogin['branch'] = lookUpTableReturnValue('x','branch','branch_id','branch',$aPenlogin['branch_id']);
	$aPenlogin['date'] = date('m-d-Y');
	$aPenlogin['schednum'] = $_REQUEST['schednum'];
}
if ($passpin1 != '' and $passpin2 != '')
{
	$p1 = '';
//	echo 'Change password of '.$aid;
	$q = "select * from account where account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr) == '0')
	{
		echo "<script>alert('Account NOT found...')</script>";
		$p1='';
	}
	$r = @pg_fetch_assoc($qr);
	if ($r['pensionpin'] != $passpin1)
	{
		message('Wrong pin number...');
		$pin = $aid = $pwd = $p1 = '';
		$aPenlogin = '';
		$aPenlogin = array();
	} 
	elseif ($passpin2 == '')
	{
		message('Your new pin number is blank .... ');
	} 
	else
	{
		$aPenlogin = $r;
		$pin = $r['smartno'];
		$aPenlogin['branch'] = lookUpTableReturnValue('x','branch','branch_id','branch',$aPenlogin['branch_id']);
		$aPenlogin['date'] = date('m-d-Y');

		$qu = "update account set pensionpin = '$passpin2' where smartno ='$pin'";
		$qru = @pg_query($qu) or message(pg_errormessage());
	}
	$passpin1 = $passpin2 = $p1 = '';
}
elseif ($pin != '' and $pin != null)
{
		$q = "select * from account where smartno ='$pin'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if (@pg_num_rows($qr) == '0')
		{
			echo "<script>alert('Account NOT found...')</script>";
			$p1='';
		}
		$r = @pg_fetch_assoc($qr);		
		if ($r['pensionpin'] == '')
		{
			$qu = "update account set pensionpin = '$pwd' where smartno ='$pin'";
			$qru = @pg_query($qu) or message(pg_errormessage());

			$q = "select * from account where smartno ='$pin'";
			$qr = @pg_query($q) or message(pg_errormessage());
			$aPenlogin = $r;
			$aid = $r['account_id'];
			$aPenlogin['branch'] = lookUpTableReturnValue('x','branch','branch_id','branch',$aPenlogin['branch_id']);
			$aPenlogin['date'] = date('m-d-Y');
		} 
		elseif ( $r['pensionpin'] != $pwd)
		{
			message('Wrong pin number...');
			$pin = $aid = $pwd = $p1 = '';
			$aPenlogin = '';
			$aPenlogin = array();
		} 
print_r($r);
echo "<br">.$pwd;		
		if ($r['pensionpin'] != $pwd)
		{
			$aPenlogin = $r;
			$aid = $r['account_id'];
			$aPenlogin['branch'] = lookUpTableReturnValue('x','branch','branch_id','branch',$aPenlogin['branch_id']);
			$aPenlogin['date'] = date('m-d-Y');
			$t = date('H:i:s',time());
			$date =date('Y-m-d');
			$qs = "select * from schedule where branch_id = '".$aPenlogin['branch_id']."'";
			$qrs = @pg_query($qs) or message(pg_errormessage());
echo $qs."<br>";
echo @pg_num_rows($qrs);
			if (@pg_num_rows($qrs) == '0')
			{
				$qs = "insert into schedule (branch_id,account_id,smartno,date,timeref,schednum,active)
									values ('".$aPenlogin['branch_id']."',
											'".$aPenlogin['account_id']."',
											'".$aPenlogin['smartno']."',
											'$date','$t',1,1)";
				$qru = @pg_query($qs) or message(pg_errormessage());
				$Penlogin['schednum'] = 1;											
			} else
			{
				while ($rs = @pg_fetch_object($qrs))
				{
					$t1 = explode(':',$rs->timeref);
					$t2 = explode(':',$t);
					if ($aPenlogin['smartno'] == $rs->smartno and $rs->active = 1)
					{
						if (($t2[0] - $t1[0] > 3) or ($t2[0] - $t1[0] == 3 and $t2[1] > $t1[1]))
						{
						} else
						{
							$aPenlogin['schednum'] = $rs->schednum;
							continue;
						}
					}
				}
			}	
		}
}
?>
    <table width="90%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td colspan="2"><font size="6" face="Bookman Old Style"> <img src="../graphics/eyeglass.gif" width="20" height="20"> 
              <strong>PENSIONER LOGIN </strong> </font><a href="../main/"><img src="../graphics/home.gif" width="25" height="15" border="0"></a> 
 
              <hr color="#993300"></td>
          </tr>
          <tr valign="middle"> 
            <td width="18%"><font size="5" face="Verdana, Arial, Helvetica, sans-serif">Account Name</font></td>
			<td width="82%"><font size="5" face="Verdana, Arial, Helvetica, sans-serif"><?= $aPenlogin['account'];?></font></td>
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= $aPenlogin['address'];?></font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= $aPenlogin['branch'];?>
			</font></td>			
          </tr>
		  <tr> <td colspan="2">&nbsp;</td></tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Sequence No.</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= str_pad($aPenlogin['schednum'],4,'0',STR_PAD_LEFT);?>
			</font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date" readonly type="text" id="date" value="<?= $aPenlogin['date'];?>" size="15">
              <input name="aid" readonly="readonly" type="hidden" id="aid" value="<?= $aid;?>" size="10" />
			  <input name="schednum" readonly="readonly" type="hidden" id="schednum" value="<?= $aPenlogin['schednum'];?>" size="10" />
			</font></td>			
          </tr>
		  
		  <tr> <td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td colspan="2"><font size="6" face="Bookman Old Style">&nbsp;</font> 
              <hr color="#993300"></td>
          </tr>
	     <tr> 
     	 <td height="58" colspan="4"><table width="22%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="16%"><img src="../graphics/slash.png" alt='+' width="56" height="56" border="0" onClick="{f1.action='?p=penlogin&p1=Change Pin&aid=<?=$aid;?>';f1.submit();}" type="image";></td>
            <td width="44%" valign="middle"><font size="3" face="Verdana, Arial, Helvetica, sans-serif">Change Pin </font></td>
            <td nowrap width="17%"><img src="../graphics/asterisk.png" alt="*" width="56" height="56" border="0" onclick="{f1.action='?p=penlogin&amp;p1=Clear&amp';f1.submit();}" type="image"; /></td>
            <td nowrap width="23%"><font size="3" face="Verdana, Arial, Helvetica, sans-serif">Clear</font></td>
          </tr>
	     </table></td>		  
		 </tr>
    </table>
<?
if (($p1 == 'Change Pin' or $p1 == 'ChangePin') and $aid != 0)
{
	$p1='';
	include_once('login.changepass.php');	
?>
	<script>
	document.getElementById('passpin1').focus();
	</script>
<?
}
if ($pin == '' and $aid=='')
{
	$p1 = '';
	include_once('loan.smarta.php');	
?>
	<script>
	document.getElementById('pin').focus();
	</script>
<?
}
?>
</td></tr></table>
</form></td>


<div align="center" valign=bottom> <img src="../graphics/elephantSmall.gif" width="50" height="50"> 
  <img src="../graphics/php-small-white.gif" width="88" height="31"><img src="../graphics/worm_in_hole.gif" width="23" height="33"><br>
  <em><font size="2">Developed by: Jared O. Santibañez, ECE, MT </font> </em> 
  <font size="2"><br>
  email: <a href="mailto:%20jay_565@yahoo.com">jay_565@yahoo.com</a><br>
</font> </div>
