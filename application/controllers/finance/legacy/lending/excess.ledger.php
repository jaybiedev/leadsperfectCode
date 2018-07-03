<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f2.action="?p=excess.ledger&p1=CancelConfirm"
		}	
		else
		{
			document.f2.action="?p=excess.ledger&p1=Cancel"
		}
	}
	else if (ul.name=='Go')
	{
		document.f1.action="?p=excess.ledger&p1="+ul.id;
	}	
	else
	{
		document.f2.action="?p=excess.ledger&p1="+ul.id;
	}
}
</script>
<?
if (!chkRights2('excessledger','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aExcessL'))
{
	session_register('aExcessL');
	$aExcessL = null;
	$aExcessL = array();
}

if ($c_id!= ''&& $p1 == 'Selectaccount')
{
	$aExcessL=null;
	$aExcessL=array();
	$q = "select 
				account.bank_cardno,
				account.account_code,
				account.account_id,
				account.account,
				account.address,
				account.date_child21,
				account.date_child21b,
				account.date_child21c,
				account.date_child21d
		 from 
		 		account
		where 
				account.account_id='$c_id'";
	$qr = @pg_query($q) or message(db_error());			
	$r = @pg_fetch_assoc($qr);
	$rem1 = $rem2 = $rem3 = $rem4 = '';
	$curdate = strtotime(date('Y-m-d'));
	$date2 = strtotime($r[date_child21]);
	$diff0 = ($curdate - $date2);
	$diff1 = intval(abs($diff0)/86400)/365;
	$yr21 = 21*86400*365;

	if ($date2 > 0)
	{
		$diff = $yr21 - $diff0;
		if ($diff > 0) $sd = ' < ';
		else $sd = ' > ';
		$diff = number_format(abs(intval(abs($diff)/86400)/30),1);
		$sd1='';
		if (round($diff,1) - intval($diff) > .1) $sd1 = '+';  
		$rem1 = 'Child 1 : '.ymd2mdy($r[date_child21]).$sd.$diff.' mo(s)';		
		$r[rem1] = $rem1;	
	}	
	$date2 = strtotime($r[date_child21b]);
	$diff0 = ($curdate - $date2);
	$diff1 = intval(abs($diff0)/86400)/365;
	$yr21 = 21*86400*365;

	if ($date2 > 0)
	{
		$diff = $yr21 - $diff0;
		if ($diff > 0) $sd = ' < ';
		else $sd = ' > ';
		$diff = number_format(abs(intval(abs($diff)/86400)/30),1);
		$rem2 = 'Child 2 : '.ymd2mdy($r[date_child21b]).$sd.$diff.' mo(s)';		
		$r[rem2] = $rem2;	
	}	
	$date2 = strtotime($r[date_child21c]);
	$diff0 = ($curdate - $date2);
	$diff1 = intval(abs($diff0)/86400)/365;
	$yr21 = 21*86400*365;

	if ($date2 > 0)
	{
		$diff = $yr21 - $diff0;
		if ($diff > 0) $sd = ' < ';
		else $sd = ' > ';
		$diff = number_format(abs(intval(abs($diff)/86400)/30),1);
		$rem3 = 'Child 3 : '.ymd2mdy($r[date_child21c]).$sd.$diff.' mo(s)';		
		$r[rem3] = $rem3;	
	}	
	$date2 = strtotime($r[date_child21d]);
	$diff0 = ($curdate - $date2);
	$diff1 = intval(abs($diff0)/86400)/365;
	$yr21 = 21*86400*365;

	if ($date2 > 0)
	{
		$diff = $yr21 - $diff0;
		if ($diff > 0) $sd = ' < ';
		else $sd = ' > ';
		$diff = number_format(abs(intval(abs($diff)/86400)/30),1);
		$rem4 = 'Child 4 : '.ymd2mdy($r[date_child21d]).$sd.$diff.' mo(s)';		
		$r[rem4] = $rem4;	
	}	
	
	$aExcessL = $r;
	$p1='selectaccountId';
}	
?> 

<form action="" method="post" name="f1" id="f1" style="margin:10px">
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td background="../graphics/table0_horizontal.PNG" bgcolor="#003366"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
        :: Excess Ledger ::</strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?=lookUpAssoc('show',array('Show All'=>'','Show Balance'=>'B','Show Paid'=>'P'),$show);?>
        </font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2"> 
        <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= $from_date;?>" size="8">
        </font></strong></font></font><font size="3" color="#000000"><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2" color="#000000"><img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.from_date, 'mm/dd/yyyy')"></font></strong></font></font><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="p1" type="submit" id="p1" value="Go">
        <input name="p12" type="button" id="p12" value="Close" onClick="window.location='?p='">
        </font></td>
    </tr>
    <tr> 
      <td><hr color="#BBBBEE" size="1"></td>
    </tr>
  </table>
</form>
<?
  if ($p1 == 'Go')
  {
		$acc = 0;
		$bscan = array('1','2','3','7','10','11','18','19');
		$acid = array();
		$scdat = date('Y-m-d');
		$q="select * from schedule 
				where 
					date='$scdat' and branch_id = '".$ADMIN['branch_id']."' and
					active!='9' and status!='Finished'";
		$qs = pg_query($q) or message(pg_errormessage());
		while ($rs = pg_fetch_object($qs))
		{
			$acid[] = $rs->account_id;
		}
		$acc = count($acid);
  
	  $q = "select * 
				from 
					account
				where 
					account ilike '$xSearch%' ";
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
					if ($acc == 0)
						$q .= ") ";
					else
					{
						$cids = join(',',$acid);
						$q .= " or account_id IN ($cids)) ";
					}
		}
		$q .= "order by
					account";
					
					
		$qr = @pg_query($q)	or message("Error Querying account file...".db_error());
?>
  
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="34%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account#</font></strong></td>
    <td width="18%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account Group</font></strong></td>
    <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></strong></td>
    <td width="15%" align="center"><strong></strong></td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		if ($r[account_status]=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;

		$ctr++;
		if ($r['branch_id'] == '') $branch_id = '0';
		else $branch_id = $r['branch_id'];
		
  ?>
  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
    <td align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?=$ctr;?>
      .</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <a href="javascript: document.getElementById('f1').action='?p=excess.ledger&p1=Selectaccount&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>&rtype=<?=$rtype;?>';document.getElementById('f1').submit()"> 
      <?= $r['account'];?>
      </a> </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= $r['account_code'] ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']) ;?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		<?= lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id) ;?>
       
    </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?=($r['enable']=='t' ? 'Enabled' : 'Disabled' );?> 
      </font></td>
  </tr>
  <?
  }
  ?>
</table>

<?	
  }
  elseif ($aExcessL['account_id'] != '')
  {
?>
<form action="" method="post" name="f2" id="f2">
  <div align="center">
    <table width="85%" border="0" cellspacing="1" cellpadding="1">
      <tr> 
        <td width="15%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></td>
        <td width="35%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= $aExcessL['account'];?>
          </font></strong></td>
        <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
      </tr>
      <tr> 
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rem1;?></font></td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rem2;?></font></td>
      </tr>
      <tr> 
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
        <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></strong></td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rem3;?></font></td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rem4;?></font></td>
      </tr>
    </table>
    <table width="85%" border="0" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
      <tr align="center" bgcolor="#DADADA"> 
        <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
        <td width="8%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
        <td width="20%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></td>
        <td width="12%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Add</font></td>
        <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Minus</font></td>
        <td width="15%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></td>
        <td width="24%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remark</font></td>
      </tr>
      <?
	  	$q = "select * from 
					payment_header,
					payment_detail
				where
					payment_header.payment_header_id = payment_detail.payment_header_id and
					payment_detail.account_id='".$aExcessL['account_id']."'  and
					status != 'C' and 
					excess>'0' and
					mcheck not in ('G','T')
				order by
					date";
					
		$ctr=$balance=$total_excess=$total_minus=$total_balance =0;
		$qr = @pg_query($q) or message(pg_errormessage());
		$aRep = null;
		$aRep = array();
		
		while ($r = @pg_fetch_assoc($qr))
		{
			$r['reference'] = $r['payment_header_id'];
			$aRep[] = $r;
			
		}
		$q = "select wexcess_id as reference, ps_remark, remarks as excessrem, date, gross_amount , type, refund_remark
						from 
							wexcess 
						where 
							account_id = '".$aExcessL['account_id']."' and status !='C' order by date";
		$qr = @pg_query($q) or message(pg_errormessage());
		while ($r = @pg_fetch_assoc($qr))
		{
			if (in_array($r['type'], array('D','G','T','R')))
			{
				$r['reference'] = $r['type'].$r['ps_remark'].' /'.$r['reference'];
				$r['excess'] = $r['gross_amount'];
//				if ($r['excessrem'] != '') $r['remarks']=trim($r['remarks']).' : '.$r['excessrem'];
//				if ($r['refund_remark'] !='' and $r['refund_remark'] !='0') 
//				{
//					$r['remarks']=trim($r['remarks']).' : '.$r['refund_remark'];
//				}
			}
			else
			{
				$r['minus'] = $r['gross_amount'];
			}
			if ($r['remarks'] != '') $r['remarks']=trim($r['remarks']).' : '.$r['excessrem'];
			else $r['remarks'] = $r['excessrem'];
			if ($r['refund_remark'] !='' and $r['refund_remark'] !='0') $r['remarks']=trim($r['remarks']).' : '.$r['refund_remark'];
			$aRep[] = $r;
		}

		$header = "Excess Ledger\n";
		$header .= "As of  ".$date."\n";
		$header .= "Account : ".$aExcess['account']."\n";
		$header .= "Printed  : ".date('m/d/Y g:ia')."\n";
		$header .= " #    Date           Add         Minus        Balance       Remarks \n";
		$header .= "----- ---------- ------------ ------------ ------------ ------------------------------\n";
		$details = '';
	
		$atemp = null;
		$atemp = array();

		foreach ($aRep as $temp)
		{
			$temp1=$temp['date'].$temp['reference'];
			$atemp[]=$temp1;
		}
			
		if (count($atemp) > 0)
		{
			asort($atemp);
			reset($atemp);
		}

		$begbal_flag=0;
		while (list ($key, $val) = each ($atemp))
		{
			$temp=$aRep[$key];

			
			if ($temp['date'] < mdy2ymd($from_date) && $from_date!='') 
			{
				$balance += ($temp['excess'] - $temp['minus']);
				continue;
			}
			elseif ($begbal_flag == 0)
			{
			echo "<tr><td></td><td colspan='4'><font size='2' face='Verdana'>Forwarded Balance</font></td><td align='right'><font size='2' face='Verdana'>".number_format($balance,2)."&nbsp;</font></td><td></td></tr>";

			$details .= adjustSize('Forwarded Balance',42).' '.
							adjustRight(number_format($balance,2),12).' '.
							adjustSize($temp['remarks'],30)."\n";

				$begbal_flag=1;
			}
			$balance += ($temp['excess'] - $temp['minus']);
			
			$ctr++;
			$total_excess += $temp['excess'];
			$total_add += $temp['excess'];
			$total_minus += $temp['minus'];
			$total_balance = $balance;
			$details .= adjustRight($ctr,4).'. '.
							adjustSize(ymd2mdy($temp['date']),10).' '.
							adjustRight(number_format($temp['excess'],2),12).' '.
							adjustRight(number_format($temp['minus'],2),12).' '.
							adjustRight(number_format($balance,2),12).' '.
							adjustSize($temp['remarks'],30)."\n";
	  ?>
      <tr bgcolor="#FFFFFF"> 
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?=$ctr;?>
          . </font></td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= ymd2mdy($temp['date']);?>
          </font></td>
        <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp; 
          <?= $temp['reference'];?>
          </font></td>
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($temp['excess'],2);?>
          </font></td>
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($temp['minus'],2);?>
          &nbsp;</font></td>
        <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($balance,2);?>
          &nbsp;</font></td>
        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?= $temp['remarks'];?></font></td>
      </tr>
      <?
	  }
		$details .= "----- ---------- ------------ ------------ ------------ ------------------------------\n";
		$details .= space(17).
							adjustRight(number_format($total_add,2),12).' '.
							adjustRight(number_format($total_minus,2),12).' '.
							adjustRight(number_format($total_balance,2),12).' '."\n";

		$details .= "----- ---------- ------------ ------------ ------------ ------------------------------\n";
	  ?>
      <tr bgcolor="#FFFFFF"> 
        <td colspan="3" align="center"><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Total 
          </strong></font></td>
        <td align="right"><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($total_excess,2);?>
          </font></strong></td>
        <td align="right"><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($total_minus,2);?>
          &nbsp;</font></strong></td>
        <td align="right"><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
          <?= number_format($total_balance,2);?>
          &nbsp;</font></strong></td>
        <td><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></strong></td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>
<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>

<?
//echo "<pre>$header.$details</pre>";
	if ($p1 == 'Print Draft')
	{

		doPrint($header.$details);
	}
}
?>

