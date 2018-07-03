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
			document.f2.action="?p=report.accountledger&p1=CancelConfirm"
		}	
		else
		{
			document.f2.action="?p=report.accountledger&p1=Cancel"
		}
	}
	else if (ul.name=='Go')
	{
		document.f1.action="?p=report.accountledger&p1="+ul.id;
	}	
	else
	{
		document.f2.action="?p=report.accountledger&p1="+ul.id;
	}
}
</script>
<?

if (!session_is_registered('aLedger'))
{
	session_register('aLedger');
	$aLedger = null;
	$aLedger = array();
}
if (!session_is_registered('aLedgerDetail'))
{
	session_register('aLedgerDetail');
	$aLedgerDetail = null;
	$aLedgerDetail = array();
}

if ($p1=='Selectreleasing' )
{
	$aLedger=null;
	$aLedger=array();
	
	
	$q = "select 
				account.account_id,
				account.account,
				account.account_group_id,				
				account.address,
				salary,
				clientbank_id
		 from 
		 		account
		where 
				account.account_id='$c_id'";
	$r = fetch_assoc($q);
	$aLedger = $r;

	$aLedger['account_id'] = $_REQUEST['c_id'];
	$aLedger['releasing_id'] = $_REQUEST['r_id'];
	$aLedger['show'] = $_REQUEST['show'];
	$aLedger['account_group']=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLedger['account_group_id']);

	$clientbank='';
	if ($aLedger['clientbank_id'] > '0')
	{
		$clientbank = lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$aLedger['clientbank_id']);
	}
}
if ($aLedger['account_id'] != '')
{
	$details = '<small3>';
	$details .= center('A C C O U N T   L E D G E R',80)."\n";
	$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$details .= center($SYSCONF['BUSINESS_ADDR'],80)."\n";
	$details .= center('Date Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$details .= 'Customer: '.adjustSize($aLedger['account'],35).'  '.'Pension:'. number_format($aLedger['salary'],2)."\n";
	$details .= 'Group   : '.adjustSize($aLedger['account_group'],20).' Bank:'.$clientbank."\n";
	
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	$details .= '    Date       Reference       Debit      Withdrawn     Credit    Excess      Balance '."\n";
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";

	$aRep = null;
	$aRep = array();
	
	$q = "select * 
				from
					releasing
				where
					account_id = '".$aLedger['account_id']."'";
					
	if ($aLedger['releasing_id'] != '')
	{
		$q .=  " and releasing_id = '".$aLedger['releasing_id']."'";
	}
	else
	{
		if ($aLedger['show'] == 'B')
		{
			$q .= " and balance > '0'";
		}
		elseif ($aLedger['show'] == 'P')
		{
			$q .= " and balance <= '0'";
		}

	}
	$qr = @pg_query($q) or message(pg_errormessage());
	
	$total_ammort = $total_obligation =0;
	while ($r = @pg_fetch_object($qr))
	{

		$total_ammort += $r->ammort;
		$total_obligation += $r->gross;
		
		$dummy = null;
		$dummy = array();
		
		$balance = $r->gross - $r->advance_payment;
		$dummy['date'] = $r->date;
		$dummy['type'] = 'C';
		$dummy['reference'] = lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id);
		$dummy['debit'] =  $r->gross;
		$dummy['releasing_id'] =  $r->releasing_id;
		$dummy['credit'] =$r->advance_payment;
		$dummy['excess'] = 0.00;
		$dummy['withdrawn'] = 0.00;
		$dummy['balance'] = $balance;
		$dummy['rem1'] = "   Loan Type : ".lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id).
						"   Mode :".($r->mode=='S' ? 'Semi-Monthly' : ($r->mode=='W' ? 'Weekly' : 'Monthly')).
						"   Terms :".$r->term.'  RecNo. '.str_pad($r->releasing_id,8,'0',str_pad_left);
		$dummy['rem2'] = "   Principal :".adjustRight(number_format($r->principal,2),12)." ".
						"   Ammortization : ".number_format($r->ammort,2);				

		$aRep[] = $dummy;
		
		$q = "select * 
						from 
							payment_header,
							payment_detail
						where
							payment_header.payment_header_id=payment_detail.payment_header_id and
							payment_header.status!='C' and
							payment_detail.releasing_id='$r->releasing_id'";

		$qpr = @pg_query($q) or message(pg_errormessage());

		while ($rp = @pg_fetch_object($qpr))
		{
			$dummy = null;
			$dummy = array();

			if ($rp->admin_id > '0')
			{
			
				$dummy['reference'] = lookUpTableReturnValue('x','admin','admin_id','username',$rp->admin_id);
			
			}
			else
			{
				$dummy['reference'] ='';
			}
			$balance -= $rp->amount;
			$dummy['date'] = $rp->date;
			$dummy['type']= 'D';
			$dummy['debit'] =  0.00;
			$dummy['releasing_id'] =  $rp->releasing_id;
			$dummy['credit'] =$rp->amount;
			$dummy['excess'] = $rp->excess;
			$dummy['withdrawn'] = $rp->withdrawn;
			$dummy['balance'] = $balance;
			$aRep[] = $dummy;
		}
	}


	$atemp = null;
	$atemp = array();
	foreach ($aRep as $temp)
	{
		$temp1=$temp['releasing_id'].$temp['date'].$temp['reference'];
		$atemp[]=$temp1;
	}
		
	if (count($atemp) > 0)
	{
		asort($atemp);
		reset($atemp);
	}
	
	$ln=0;
	$sub_debit = $sub_credit = $sub_withdrawn = $sub_excess = 0;
//	foreach ($aRep as $temp)
//	{
	while (list ($key, $val) = each ($atemp))
	{
			$temp=$aRep[$key];
			
			if ($temp['type'] == 'C')
			{
				if ($ln > 1)
				{
					$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
					$details .= space(9).adjustSize('Sub Total',20).
							adjustRight(number_format2($sub_debit,2),12).' '.
							adjustRight(number_format2($sub_withdrawn,2),10).' '.
							adjustRight(number_format2($sub_credit,2),10).' '.
							adjustRight(number_format2($sub_excess,2),10).' '.
							adjustRight(number_format($sub_balance,2),10)."\n";

					$details .= "\n";
					$lc++;
				}
				
				$details .= $temp['rem1']."\n";
				$details .= $temp['rem2']."\n";
				
				$balance = $sub_debit = $sub_credit = $sub_withdrawn = $sub_excess = 0;
				$lc = $lc + 2;
				$ln=0;
			}
			$balance += ($temp['debit'] - $temp['credit']);
			
			$ln++;
			$details .= adjustRight($ln,2).'. '.
							adjustSize(ymd2mdy($temp['date']),10).' '.
							adjustSize($temp['reference'],10).' '.
							adjustSize($temp['type'],2).' '.
							adjustRight(number_format2($temp['debit'],2),12).' '.
							adjustRight(number_format2($temp['withdrawn'],2),10).' '.
							adjustRight(number_format2($temp['credit'],2),10).' '.
							adjustRight(number_format2($temp['excess'],2),10).' '.
							adjustRight(number_format($balance,2),10)."\n";
							
			$accountbalance += $temp['debit'] - $temp['credit'];
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'];
			$total_excess += $temp['excess'];
			$total_withdrawn += $temp['withdrawn'];
			$total_balance = $balance;

			$sub_debit += $temp['debit'];
			$sub_credit += $temp['credit'];
			$sub_excess += $temp['excess'];
			$sub_withdrawn += $temp['withdrawn'];
			$sub_balance = $balance;
			
							

			$lc++;	
			if ($lc > 55)
			{			
				if ($p1=='Print Draft')
				{
					$details .= "<eject>";
					doPrint($details);
				}
			}
		}
	
	if ($sub_debit != '0' ||  $sub_credit != '0')
	{
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
		$details .= space(9).adjustSize('Sub Total',20).
				adjustRight(number_format2($sub_debit,2),12).' '.
				adjustRight(number_format2($sub_withdrawn,2),10).' '.
				adjustRight(number_format2($sub_credit,2),10).' '.
				adjustRight(number_format2($sub_excess,2),10).' '.
				adjustRight(number_format($sub_balance,2),10)."\n";
	}
	
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	$details .= space(9).adjustSize('Grand Total',20).
			adjustRight(number_format2($total_debit,2),12).' '.
			adjustRight(number_format2($total_withdrawn,2),10).' '.
			adjustRight(number_format2($total_credit,2),10).' '.
			adjustRight(number_format2($total_excess,2),10).' '.
			adjustRight(number_format2($total_balance,2),10)."\n\n";

	$details .= space(10).'Total Obligation......'.adjustRight(number_format($total_obligation,2),12)."\n";
	$details .= space(10).'Total Ammortization...'.adjustRight(number_format($total_ammort,2),12)."\n";
	$details .= space(10).'Total Balance.........'.adjustRight(number_format($accountbalance,2),12)."\n\n";
	$details .= 'Remarks :'."\n";
	$details .= $remarks."\n";
	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($details);
	}
}
?> 
<br>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr> 
      <td background="../graphics/table_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/bluelist.gif" width="16" height="17"> 
        <font color="#CCCCCC">Search Client Payment Entry</font></strong></font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Account 
        Search 
        <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>">
        <?=lookUpAssoc('sortby',array('Name'=>'account','Releasing No'=>'releasing_id'),$sortby);?>
        <input name="p1" type="submit" id="p1" value="Go">
        </font></td>
    </tr>
    <tr> 
      <td><hr color="red" height="1"></td>
    </tr>
  </table>

<?
  if ($p1 == 'Go')
  {
  
	  $q = "select * 
				from 
					account
				where 
					account ilike '$xSearch%' and
					enable = 'Y' ";
		if ($ADMIN['branch_id'] > '0')
		{
			$q .= " and  (branch_id  = '".$ADMIN['branch_id']."'";
			if ($ADMIN['branch_id2'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id2']."'";
			if ($ADMIN['branch_id3'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id3']."'";
			if ($ADMIN['branch_id4'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id4']."'";
			if ($ADMIN['branch_id5'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id5']."'";
			if ($ADMIN['branch_id6'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id6']."'";
			if ($ADMIN['branch_id7'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id7']."'";
			if ($ADMIN['branch_id8'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id8']."'";
			if ($ADMIN['branch_id9'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id9']."'";
			if ($ADMIN['branch_id10'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id10']."'";
			if ($ADMIN['branch_id11'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id11']."'";
			if ($ADMIN['branch_id12'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id12']."'";
			if ($ADMIN['branch_id13'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id13']."'";
			if ($ADMIN['branch_id14'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id14']."'";
			if ($ADMIN['branch_id15'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id15']."'";
			if ($ADMIN['branch_id16'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id16']."'";
			if ($ADMIN['branch_id17'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id17']."'";
			if ($ADMIN['branch_id18'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id18']."'";
			if ($ADMIN['branch_id19'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id19']."'";
			if ($ADMIN['branch_id20'] > '0') $q .= " or  branch_id  = '".$ADMIN['branch_id20']."'";
			$q .= ") ";
		}
					
		$q .= "		order by
					account";
					
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());

?>
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CFD3E7"> 
      <td width="6%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="26%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
      <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        Group </font></strong></td>
      <td width="13%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></strong></td>
      <td width="12%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ammort</font></strong></td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{

		$account_balance=accountBalance($r['account_id']);
		$ctr++;
  ?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align="right" nowrap width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=$ctr;?>
        .</font></td>
      <td width="26%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="<?=$href;?>"> 
        <?= $r['account'];?>
        </a> </font></td>
      <td width="22%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
        </font></td>
      <td width="13%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= status($r['account_status']);?>
        </font></td>
      <td align="right" width="12%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($account_balance,2);?>
        </font></td>
      <td height="22" colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        &nbsp; <a href="<?=$href;?>"> All</a></font></td>
    </tr>
    <?

		$q = "select * from payment_header, payment_detail
							 where 
							 	payment_header.payment_header_id=payment_detail.payment_header_id and
								payment_detail.account_id='".$r['account_id']."'";
		$qqr = @pg_query($q) or message(pg_errormessage());


		if (@pg_num_rows($qqr) <= 1 ) continue;
		if (@pg_numrows($qqr)>1)
		{
			while ($rr= @pg_fetch_object($qqr))
			{

				if ($rr->account_group_id != '')
				{
					$particulars = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r->account_group_id);
					$href = '?p=payment.entry&p1=Load&id='.$rr->payment_header_id;
				}
				elseif ($rr->entry_type=='W')
				{
					$particulars = "Passbook/ATM Withdrawal Entry";
					$href = '?p=payment.withdraw&p1=Load&id='.$rr->payment_header_id;
				}	
				elseif ($rr->entry_type=='I')
				{
					$particulars = "Passbook/ATM Withdrawal Entry";
					$href = '?p=payment.individual&p1=Load&id='.$rr->payment_header_id;
				}
				if ($rr->status == 'C')
				{
					$bgColor ='#FFCC99';
				}
				else
				{
					$bgColor = '#EFEFEF';
				}
		?>
    <tr  bgcolor="<?= $bgColor;?>" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='<?= $bgColor;?>'"> 
      <td width="6%"></td>
      <td width="26%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $r['account'];?>
        </font></td>
      <td width="22%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <a href="<?= $href;?>"> 
        <?= ymd2mdy($rr->ddate);?>
        </a> </font></td>
      <td align="right" width="13%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($rr->withdrawn,2);?>
        </font></td>
      <td align="right" width="12%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($rr->amount,2);?>
        </font></td>
      <td width="9%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $rr->status;?>
        </font></td>
      <td width="12%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        <a href="<?= $href;?>"> Select</a></font></td>
    </tr>
    <?

			}
		}
		
  }
	?>
  </table>
<?
 }
  ?>
  </form>
