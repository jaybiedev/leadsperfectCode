<?
if (!chkRights2('manqueue','madd',$ADMIN['admin_id']))
{
	message("You have no permission to Manually Queue...");
	exit;
}
?>
<script>
function vSubmit(ul)
{
	document.getElementById('f1').action="?p=manpenque&p1="+ul.id;
	document.getElementById('f1').submit();
}
</script>
<br>
<br>
<form name="f1" method="post" action="">
<table width="100%" height="60%" border="0" cellpadding="2" cellspacing="0">
  <tr valign="top"> 
    <td width="47%" align="center">
<?

if ($p1 == 'Clear')
{
	$p1 = $aid = $account = $pin = $pwd = '';
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
	$account = $r[account];
	$address = $r[address];
	$branch = lookUpTableReturnValue('x','branch','branch_id','branch',$r['branch_id']);
	$date = date('m-d-Y');
	$qs = "select * from schedule where branch_id = '$branch_id' and date='$date'";
	$qrs = @pg_query($qs) or message(pg_errormessage());

	$rs = @pg_num_rows($qrs);
	$schednum = $rs +1;											
}
elseif ($p1=='Save' AND  $aid != '' and $branch_id!=0)
{
	$q = "select * from account where account_id ='$aid'";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr) == '0')
	{
		echo "<script>alert('Account NOT found...')</script>";
		$p1='';
	}
	$r = @pg_fetch_assoc($qr);
	$account = $r[account];
	$account_id = $r[account_id];
	$smartno = $r[smartno];
	$address = $r[address];
	$branch = lookUpTableReturnValue('x','branch','branch_id','branch',$r['branch_id']);
	$date = date('m-d-Y');
	$t = date('H:i:s',time());

	$qs = "select * from schedule where branch_id = '$branch_id' order by date desc";

	$qrs = @pg_query($qs) or message(pg_errormessage());
	if (@pg_num_rows($qrs) == 0)
	{
		$qs = "insert into schedule (branch_id,account_id,smartno,date,timeref,schednum,active,admin_id)
							values ('$branch_id',
									'account_id',
									'smartno',
									'$date','$t',1,1,'".$ADMIN['admin_id']."')";
		$qru = @pg_query($qs) or message(pg_errormessage());

		$schednum = 1;											
	} else
	{
		$ctnum = $flg = $sid = 0;
		while ($rs = @pg_fetch_object($qrs))
		{
			$ctnum++;
			if ($rs->date == $date) continue;
			if ($flg == 0)
			{
				$sid = $rs->sched_id;
			}
		}
		if ($sid == 0)
		{
			$schednum = $ctnum+1;
			$qs = "insert into schedule (branch_id,account_id,smartno,date,timeref,schednum,active,admin_id)
							values ('$branch_id',
									'$account_id',
									'$smartno',
									'$date','$t','$schednum','1','".$ADMIN['admin_id']."')";

			$qru = @pg_query($qs) or message(pg_errormessage());
		} else
		{
			$schednum = $ctnum;
			$qs = "update schedule set account_id = '$account_id',
									smartno='$smartno',
									date='$date',
									timeref='$t',
									schednum='$schednum',
									active='1',
									admin_id='".$ADMIN['admin_id']."'
								 where sched_id='$sid'";

			$qru = @pg_query($qs) or message(pg_errormessage());
		}
	}	
}
?>
    <table width="90%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td colspan="2"><font size="6" face="Bookman Old Style"> <img src="../graphics/eyeglass.gif" width="20" height="20"> 
              <strong>Manual Pensioner Queue </strong> </font><a href="../main/"><img src="../graphics/home.gif" width="25" height="15" border="0"></a> 
 
              <hr color="#993300"></td>
          </tr>
         <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Processing Branch</font></td>
			<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
			  <select name = "branch_id">
                <?
				$q = "select * from branch where enable";
				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
					if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
					if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
					if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
					if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
					if ($ADMIN['branch_id11'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id11']."'";
					if ($ADMIN['branch_id12'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id12']."'";
					if ($ADMIN['branch_id13'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id13']."'";
					if ($ADMIN['branch_id14'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id14']."'";
					if ($ADMIN['branch_id15'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id15']."'";
					if ($ADMIN['branch_id16'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id16']."'";
					if ($ADMIN['branch_id17'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id17']."'";
					if ($ADMIN['branch_id18'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id18']."'";
					if ($ADMIN['branch_id19'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id19']."'";
					if ($ADMIN['branch_id20'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id20']."'";
					$q .= ") ";
				} else
				{
				?>
                <option value='99'>Select Branch</option>
                <?
				}
				$q .= " order by branch";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($branch_id == $r->branch_id)
					{
						echo "<option value=$r->branch_id selected>$r->branch</option>";
					}
					else
					{	
						echo "<option value=$r->branch_id>$r->branch</option>";
					}	
				}			
			?>
              </select>
			</font></td>			
		  </tr>
          <tr valign="middle"> 
            <td width="28%"><font size="5" face="Verdana, Arial, Helvetica, sans-serif">Account Name</font></td>
			<td width="72%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
			  <input name="account" type="text" id="account" value="<?=$account;?>" size="50" />
			  <input name="p1" type="submit" id="p1" value="Go" onClick="window.location='?p=manpenque&p1=Go&branch_id=<?= $branch_id;?>'"/>
			</font></td>
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= $address;?></font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= $branch;?>
			</font></td>			
          </tr>
		  <tr> <td colspan="2">&nbsp;</td></tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Sequence No.</font></td>
			<td><font size="5" face="Verdana, Arial, Helvetica, sans-serif">
			  <?= str_pad($schednum,4,'0',STR_PAD_LEFT);?>
			</font></td>			
          </tr>
          <tr>             
		  	<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
			<td><font size="4" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date" readonly type="text" id="date" value="<?= $date;?>" size="15">
              <input name="aid" readonly="readonly" type="hidden" id="aid" value="<?= $aid;?>" size="10" />
			  <input name="schednum" readonly="readonly" type="hidden" id="schednum" value="<?= $schednum;?>" size="10" />
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
            <td nowrap width="16%"><img src="../graphics/save.jpg" alt='+' width="55" height="25" border="0" onClick="{f1.action='?p=manpenque&p1=Save&aid=<?=$aid;?>';f1.submit();}" type="image";></td>
            <td nowrap width="17%"><img src="../graphics/new.jpg" alt="*" width="55" height="25" border="0" onclick="{f1.action='?p=manpenque&p1=Clear';f1.submit();}" type="image"; /></td>
          </tr>
	     </table></td>		  
		 </tr>
    </table>
</td></tr></table>
</form></td>


<?
  if ($p1 == 'Go')
  {
	  $q = "select * 
				from 
					account
				where 
					account ilike '$account%' and
					enable 
				order by
					account";
					
					
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
?>
  
<table width="75%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="38%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="21%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      Group </font></strong></td>
    <td width="11%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></strong></td>
    <td width="12%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		$account_balance=accountBalance($r['account_id']);
		$ctr++;
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap width="7%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td width="38%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <a href="?p=manpenque&aid=<?= $r['account_id'];?>&branch_id=<?= $branch_id;?>"> 
      <?= $r['account'];?>
      </a> </font></td>
    <td width="21%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
      </font></td>
    <td width="11%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= status($r['account_status']);?>
      </font></td>
    <td align="right" width="12%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= number_format($account_balance,2);?>
      </font></td>
 </tr>
  <?
  	}
  }
  ?>
</table>

<div align="center" valign=bottom> <img src="../graphics/elephantSmall.gif" width="50" height="50"> 
  <img src="../graphics/php-small-white.gif" width="88" height="31"><img src="../graphics/worm_in_hole.gif" width="23" height="33"><br>
  <em><font size="2">Developed by: Jared O. Santiba�ez, ECE, MT </font> </em> 
  <font size="2"><br>
  email: <a href="mailto:%20jay_565@yahoo.com">jay_565@yahoo.com</a><br>
</font> </div>