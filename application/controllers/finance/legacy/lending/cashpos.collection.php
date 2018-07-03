 <script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script><body topmargin="0">
<?
if (!chkRights2('cashpos','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Cash Position ]...");
	exit;
}

function forwardCashcollection($currdate)
{
	global $aCashcollection, $ADMIN;

	$q = "select 
					sum(debit) as debit,
					sum(credit) as credit
				from 
					cashpos
				where
					branch_id = '".$aCashcollection['branch_id']."' and
					type in ('C') and 
					date = '$currdate' and 
					enable";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$balance = $r->debit - $r->credit;
				

	$q = "select 
					distinct (date) as date 			
				from 
					cashpos
				where
					branch_id = '".$aCashcollection['branch_id']."' and
					date>'$currdate' and 
					type in ('C') and 
					enable
				order by
					date";
	$qur = @pg_query($q) or message(pg_fetch_object($qr));

	while ($ru = @pg_fetch_object($qur))
	{
		$date = $ru->date;
		$q = "select * from cashpos
						where		
							mcheck ='BB' and
							date = '$date' and 
							branch_id = '".$aCashcollection['branch_id']."' and
							type in ('C') ";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		if (@pg_num_rows($qr) > 0)
		{
			$r = @pg_fetch_object($qr);
			$cashpos_id = $r->cashpos_id;
		
			$q = "update cashpos set 
							debit='$balance',
							descr = 'Beginning Balance', 
							balance = '$balance',
							admin_id = '".$ADMIN['admin_id']."',
							enable='TRUE'
						where
							mcheck='BB' and 
							type = 'C' and 
							date = '$date' and 
							branch_id = '".$aCashcollection['branch_id']."' and
							cashpos_id = '$cashpos_id'";
				$qr = @pg_query($q) or message(pg_errormessage().$q);

		}
		else
		{
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type, mcheck, admin_id, enable)
						values 
							('".$aCashcollection['branch_id']."','$date','Beginning Balance',
							'$balance','0','$balance','C','BB', '".$ADMIN['admin_id']."','TRUE')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);

		}
		
		$q = "select 
						sum(debit) as debit,
						sum(credit) as credit
					from 
						cashpos
					where
						branch_id = '".$aCashcollection['branch_id']."' and
						date = '$date' and 
						type in  ('C') and 
						enable";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$balance = $r->debit - $r->credit;
		
	}

	
}

function updateDeposit($date)
{	
	global $aCashcollection;
	
	$q = "select sum(debit) as debit,
					sum(credit) as credit
				from 
					cashpos 
				where 
					type='C' and 
					date='$date' and
					mcheck!='BB' and
					branch_id='".$aCashcollection['branch_id']."' and 
					enable=TRUE";
					
	$qr = @pg_query($q) or message('Error Summing Undeposited Amount...');
	$r = @pg_fetch_object($qr);
	$add = $r->debit - $r->credit;

	$q= "select * from deposit where branch_id ='".$aCashcollection['branch_id']."'  and date='$date' order by date desc offset 0 limit 1";
	$qr = @pg_query($q) or message(pg_errormessage());
	if (@pg_num_rows($qr) == 0)
	{
		$q= "select * from deposit where branch_id ='".$aCashcollection['branch_id']."'  and date<'$date' order by date desc offset 0 limit 1";
		$qr = @pg_query($q) or message('Error Summing Undeposited Amount...');
		$r = @pg_fetch_object($qr);
	
		$deposit_id = $r->deposit_id; 	
		$lastdate = $r->date;
		$oldbalance = $r->balance;
		$beginning = $r->beginning;
		$minus = $r->minus;
		
		if ($minus == '') $minus=0;
		if ($oldbalance == '') $oldbalance = 0;
		if ($newbalance == '') $newbalance = 0;
		if ($add == '') $add=0;
		$newbalance = $beginning + $add - $minus;

		$q = "insert into deposit (date,beginning,add,balance, branch_id)
					values ('$date', '$oldbalance', '$add', '$newbalance','".$aCashcollection['branch_id']."')";
		$qr = @pg_query($q); // or message('Unable to Add To Undeposited Amount...'.pg_errormessage().$q);


	}
	else
	{
		$r = @pg_fetch_object($qr);
		if ($add == '') $add=0;
		$newbalance = $r->beginning + $add - $r->minus;
		$deposit_id = $r->deposit_id;
		
		if ($newbalance== '') $newbalance=0;
		$q = "update deposit set add='$add', balance='$newbalance'
						where
							deposit_id ='$deposit_id'";
		$qr = @pg_query($q) or message('Unable to Update Undeposited Amount...');

	}		
}	


if (!session_is_registered('aCashcollection'))
{
	session_register('aCashcollection');
	$aCashcollection=null;
	$aCashcollection=array();
}
if (!session_is_registered('iCashcollection'))
{
	session_register('iCashcollection');
	$iCashcollection=null;
	$iCashcollection=array();
}
if ($date == '' or $date=='//')
{
	$aCashcollection['date'] = date('Y-m-d');
}

if ($aCashcollection['branch_id'] == '')
{
	$aCashcollection['branch_id'] = lookUpTableReturnValue('x','branch','local','branch_id','Y');
}
if (!in_array($p1, array(Null,'Delete Checked','Edit','Load')))
{
	$iCashcollection['descr'] = $_REQUEST['descr'];
	$iCashcollection['mcheck'] = $_REQUEST['mcheck'];
	$iCashcollection['reference'] = $_REQUEST['reference'];
	$iCashcollection['debit'] = $_REQUEST['debit'];
	$iCashcollection['credit'] = $_REQUEST['credit'];
	$aCashcollection['date'] = mdy2ymd($_REQUEST['date']);
	$aCashcollection['branch_id'] = $_REQUEST['branch_id'];
	
	if ($iCashcollection['debit'] == '')
	{
		$iCashcollection['debit'] = 0;
	}
	if ($iCashcollection['credit'] == '')
	{
		$iCashcollection['credit'] = 0;
	}
	$iCashcollection['type'] = $_REQUEST['type'];
	$iCashcollection['rid'] = $_REQUEST['rid'];
	$iCashcollection['mcheck'] = $_REQUEST['mcheck'];
	if ($iCashcollection['rid'] == '')
	{
		$iCashcollection['rid'] = '0';
	}
}

if ($aCashcollection['date'] > date('Y-m-d'))
{
	message('Cannot advance date...Current date is '.date('m/d/Y'));
	$aCashcollection['date'] = date('Y-m-d');
}
if ($p1 == 'Submit' && $iCashcollection['descr'] == '')
{
	message('Please provide description...');
}
elseif ($p1 == 'Submit' && ($iCashcollection['debit'] == '' and $iCashcollection['credit']==''))
{
	message('Please provide Amount...');
}
elseif ($p1 == 'Submit' && $aCashcollection['branch_id']=='')
{
	message('Please specify for what branch....');
}
elseif ($p1 == 'Submit' && $iCashcollection['cashpos_id']==''&& !chkRights2('cashpos','madd',$ADMIN['admin_id']))
{
	message("You have no permission to ADD Transaciton in this Area [ Cash Position ]...");
}
elseif ($p1 == 'Submit' && $iCashcollection['cashpos_id']!=''&& !chkRights2('cashpos','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Update/Modify Transaciton in this Area [ Cash Position ]...");
}

elseif ($p1 == 'Submit')
{
	if ($iCashcollection['cashpos_id']=='')
	{
		$q = "insert into cashpos (branch_id,date,descr,reference, mcheck, debit,credit,admin_id, ip, type, rid)
				values 
					('".$aCashcollection['branch_id']."','".$aCashcollection['date']."','".$iCashcollection['descr']."','".$iCashcollection['reference']."','".$iCashcollection['mcheck']."',
					'".$iCashcollection['debit']."','".$iCashcollection['credit']."','".$ADMIN['admin_id']."','$REMOTE_ADDR', 
					'".$iCashcollection['type']."', '".$iCashcollection['rid']."')";
		$qr =@pg_query($q) or message('Unable to save data...'.pg_errormessage());
	}
	else
	{
		$audit = $iCashcollection['audit'].'; Updated by '.$ADMIN['username'].' on '.date('d/m/Y g:ia');
		$q = "update cashpos set 
					descr = '".$iCashcollection['descr']."',
					mcheck = '".$iCashcollection['mcheck']."',
					reference = '".$iCashcollection['reference']."',
					debit = '".$iCashcollection['debit']."',
					credit = '".$iCashcollection['credit']."',
					type = '".$iCashcollection['type']."',
					rid = '".$iCashcollection['rid']."',
					audit = '$audit'
				where
					cashpos_id='".$iCashcollection['cashpos_id']."'";
		$qr = @pg_query($q) or message('Unable to update record...'.pg_errormessage());
	}

	$iCashcollection = null;
	$iCashcollection = array();			
}
elseif ($p1 == 'Delete Checked')
{
	for ($c=0;$c<count($delete);$c++)
	{
		$q = "update cashpos set enable='f' where cashpos_id='".$delete[$c]."'";
		pg_query($q);
		
	}
}
elseif ($p1 == 'Edit' && $id != '')
{
	$q = "select * from cashpos where cashpos_id='$id'";
	$qr = @pg_query($q) or message('Unable To Query Cash Position Table...');
	if ($qr)
	{
		$r = pg_fetch_assoc($qr);
		$iCashcollection = null;
		$iCashcollection = array();
		$iCashcollection = $r;
		
		$aCashcollection['branch_id'] = $r['branch_id'];
		$aCashcollection['date'] = $r['date'];
	}
}
if ($iCashcollection['type']=='') $iCashcollection['C'];

if (strlen($aCashcollection['date']) == 10)
{ 
	updateDeposit($aCashcollection['date']);
}

//---
if ($aCashcollection['date'] != date('Y-m-d'))
{
	forwardCashcollection($aCashcollection['date']);
}

if ($aCashcollection['branch_id'] == '') $aCashcollection['branch_id'] = $ADMIN['branch_id'];
?>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="22%" height="31" bgcolor="#DADADA"><font size="4" face="Arial, Helvetica, sans-serif"><strong>&nbsp;Collection</strong></font></td>
      <td width="78%" valign="bottom"><hr size='1'></td>
    </tr>
    <tr> 
      <td colspan="2" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>Cash Position:</strong> Branch 
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
          <option value=''>Select Branch</option>
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
        For Date </font> 
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aCashcollection['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"> 
        <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
        <input type="submit" value="Go" name="p1">
        <input type="button" value="Revolving Fund" name="p122" onClick="window.location='?p=cashpos.revolving'">
        <input type="button" value="Petty Cash Fund" name="p12" onClick="window.location='?p=cashpos.petty'">
        <input type="button" value="Close" name="p123" onClick="window.location='?p=cashpos'"> 
      </td>
    </tr>
    <tr> 
      <td colspan="2"><hr color="red"></td>
    </tr>
  </table>
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td colspan="5"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Entry 
        Details</strong></font></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td width="6%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference<br>
        <input name="reference" type="text" id="reference" value="<?= $iCashcollection['reference'];?>" size="10" maxlength="20" style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('mcheck').focus();return false;}">
        </font></td>
      <td width="6%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font><br>
	  <select name="mcheck" id="mcheck"  style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('descr').focus();return false;}">
	  <option value="S" <?= ($iCsahcollection['mcheck']=='S' ? 'selected':'');?>>ATM/Passbook-SSS</option>
	  <option value="L" <?= ($iCsahcollection['mcheck']=='L' ? 'selected':'');?>>ATM/Passbook-LGU</option> 
	  <option value="I" <?= ($iCsahcollection['mcheck']=='I' ? 'selected':'');?>>Interbranch</option>
	  <option value="O" <?= ($iCsahcollection['mcheck']=='O' ? 'selected':'');?>>Others</option>
	  </select>
      </td>
      <td width="13%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
        <input name="type" type="hidden" id="type" value="C">
        <br>
        <input name="descr" type="text" id="descr" value="<?= $iCashcollection['descr'];?>" size="35" style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('credit').focus();return false;}">
        </font></td>
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit<br>
        <input name="credit" type="text" id="credit" value="<?= $iCashcollection['credit'];?>" size="10"  style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('debit').focus();return false;}">
        </font></td>
      <td width="68%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit<br><input name="debit" type="text" id="debit" value="<?= $iCashcollection['debit'];?>" size="10"   style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('Submit').focus();return false;}">
        </font><input name="p1" type="submit" id="Submit" value="Submit">
        </td>
    </tr>
  </table>
  <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#C2D6C0"> 
      <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="9%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></strong></td>
      <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
      <td width="37%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="13%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit</font></strong></td>
      <td width="13%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit</font></strong></td>
      <td width="13%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td height="250" colspan="7"> <div id="Layer1" style="position:abolute; width:100%; height:100%; z-index:1; overflow: auto;"> 
          <table width="99%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
            <?
		if ($aCashcollection['branch_id'] != '')
		{
			$q = "select * from cashpos 
						where 
							date='".$aCashcollection['date']."' and
							branch_id='".$aCashcollection['branch_id']."' and 
							type in ('C') and 
							enable
						order by cashpos_id ";
			$qr = @pg_query($q) or message('Unable to query database...');
			if ($qr && pg_num_rows($qr) == 0)
			{
				$q = "select * from cashpos 
							where 
								date<'".$aCashcollection['date']."' and 
								type in ('C') and 
								branch_id='".$aCashcollection['branch_id']."' and enable
						order by cashpos_id  desc";
				$qr = @pg_query($q) or message1(pg_errormessage());
				
				if (@pg_num_rows($qr)>0)
				{
					$r = @pg_fetch_object($qr);
					$balance = $r->balance;
					
					//-- beginning balance = previous balance less deposits made
					$q = "select sum(debit) as debit,
								sum(credit) as credit  
								from 
									bankrecon
								where 
									type='D' and 
									date='$r->date' and
									mcheck!='BB' and
									enable";
					$qr = @pg_query($q) or message1(pg_errormessage());
					$r = @pg_fetch_object($qr);
					$balance -= ($r->debit - $r->credit);
					
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type, mcheck)
						values 
                                                        ('".$aCashcollection['branch_id']."','".$aCashcollection['date']."','Beginning Balance',
                                                        '$balance','0','$balance','C','BB')";
	
					$qr = @pg_query($q) or message('Unable to save date...'.pg_errormessage());

					$q = "select * from cashpos 
						where 
							date='".$aCashcollection['date']."' and
							branch_id='".$aCashcollection['branch_id']."' and 
							type in ('C') and 
							enable
						order by cashpos_id ";
						

					$qr = @pg_query($q) or message('Unable to query database...');
		
				}
				else
				{
					message('No Previous Balance...');
				}				
			}
			$ctr=0;
			$aCashcollection['balance'] =$aCashcollection['debit']=$aCashcollection['credit']= $balance = 0;
			
			$header  = "\n\n\n";
			$header .= $SYSCONF['BUSINESS_NAME'].' - '. lookUpTableReturnValue('x','branch','branch_id','branch',$aCashcollection['branch_id'])."\n";
			$header .= lookUpTableReturnValue('x','branch','branch_id','branch_address',$aCashcollection['branch_id'])."\n\n";
			$header .= 'Collection Transaction Date:'.ymd2mdy($aCashcollection['date']).'     Printed:'.date('m/d/Y g:ia')."\n";
			$header .= " --- --------- --------------------------- - ------------- ------------ ------------- \n";
			$header .= "  #  Reference  Description                      Debit        Credit       Balance \n";
			$header .= " --- --------- --------------------------- - ------------- ------------ ------------- \n";
			$lc=10;
			$total_loan = $total_excess = $total_other = $total_debit =0;
			$tatmsss = $tatmlgu = $tinterb = $tothers = 0;
			while ($r = @pg_fetch_object($qr))
			{
				$ctr++;
				$aCashcollection['debit'] += $r->debit ;
				$aCashcollection['credit'] += $r->credit;
				$aCashcollection['balance'] += $r->debit - $r->credit;
				$balance += $r->debit - $r->credit;

				if ($r->mcheck != 'BB' && $r->debit!=0)
				{
					$total_debit += $r->debit;
				}
				
				$q = "update cashpos set balance='$balance' where cashpos_id='$r->cashpos_id'";
				@pg_query($q) or message("Unable to update balance...");
				
				forwardCashcollection($aCashcollection['date']);

				if (substr($r->mcheck,0,1) == 'S') $tatmsss += $r->debit; 
				elseif (substr($r->mcheck,0,1) == 'L') $tatmlgu += $r->debit; 
				elseif (substr($r->mcheck,0,1) == 'I') $tinterb += $r->debit; 
				elseif (substr($r->mcheck,0,1) == 'O') $tothers += $r->debit; 

				$details .= adjustRight($ctr,3).'. '.
							adjustSize($r->reference,9).' '.
							adjustSize($r->descr,27).' '.
							adjustSize($r->type,1).' '.
							adjustRight(number_format($r->debit,2),13).' '.
							adjustRight(number_format($r->credit,2),12).' '.
							adjustRight(number_format($balance,2),13)."\n";
							$descr = $r->descr;
							if (strlen(trim($descr)) > 27)
							{
								$details .= space(16).substr($descr,27,27)."\n";
							}
				$lc++;
				if ($lc > 55)
				{
					if ($p1 == 'Print Draft')
					{
						$details .= "\n<eject>\n\n";
						doPrint($header.$details);
						$details = '';
					}
					$lc=10;
				}			
		  ?>
            <tr onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF"> 
              <td width="6%" align="right" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=$ctr;?>
                . 
                <input name="delete[]" type="checkbox" id="delete[]" value="<?= $r->cashpos_id;?>">
                </font></td>
              <td width="9%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="?p=cashpos.collection&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->reference;?>
                </a></font></td>
              <td width="5%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <a href="?p=cashpos.collection&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->mcheck;?></a>
                </font></td>
              <td width="40%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
			  <a href="?p=cashpos.collection&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->descr;?>
                </a> </font> </td>
              <td width="13%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->credit,2);?>
                </font></td>
              <td width="13%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->debit,2);?>
                </font></td>
              <td width="13%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($balance,2);?>
                </font></td>
            </tr>
            <?
			}
			$details .= " --- --------- ---------------------------- -------------- ------------ ------------- \n";
			$details .= space(6).adjustSize('Total Debits: '.number_format($total_debit,2),30).' '.
							space(6).' '.
							adjustRight(number_format($aCashcollection['debit'],2),14).' '.
							adjustRight(number_format($aCashcollection['credit'],2),12).' '.
							adjustRight(number_format($balance,2),13)."\n";
			$details .= " --- --------- ---------------------------- -------------- ------------ ------------- \n";
			$details .= "     TOTAL ATM/Passbook - SSS ".adjustRight(number_format($tatmsss,2),13)."\n";
			$details .= "     TOTAL ATM/Passbook - LGU ".adjustRight(number_format($tatmlgu,2),13).'     '.
						adjustRight(number_format($tatmsss+$tatmlgu,2),14)."\n";
			$details .= "     INTER-BRANCH             ".adjustRight(number_format($tinterb,2),13)."\n";
			$details .= "     OTHERS                   ".adjustRight(number_format($tothers,2),13)."\n";
						
//			$details .= space(10).'INITIAL BANK BALANCE .............'.number_format(lookUpTableReturnValue('x','branch','branch_id','init_balance',$aCashcollection['branch_id']),2)."\n";
//echo "<pre>$header$details</pre>";

			if ($p1 == 'Print Draft')
			{
					$details .= "\n<eject>\n\n";
					doPrint($header.$details);
			}
			if ($p1 == 'Print')
			{
				$detprint = "<font style='font-family:monospace; font-size:14px; letter-spacing:1px;'>".$header.$details."</font>";
			
				echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$detprint.'"'.">";
				echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
				echo "</iframe>";
				echo "<script>printIframe(print_area)</script>";
			}
		} //if with branch_id				
			?>
          </table>
        </div></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td>&nbsp;</td>
      <td colspan="3" align="center"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif">TOTAL (Debits : <?= number_format($total_debit,2);?>)</font></strong></td>
      <td align="right" width="14%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashcollection['credit'],2);?>
        </font></strong></td>
      <td align="right" width="15%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashcollection['debit'],2);?>
        </font></strong></td>
      <td align="right" width="13%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashcollection['balance'],2);?>
        </font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="7"><input name="p1" type="submit" id="p1" value="Delete Checked">
        <input name="p1" type="submit" id="p1" value="Print Draft">
        <input name="p1" type="submit" id="p1" value="Print"></td>
    </tr>
  </table>
  </form>
  <script>
  if (f1.branch_id.value != '')
  {
  	f1.descr.focus()
  }
  else
  {
  	f1.branch_id.focus()
  }
  </script>
<input type='hidden' id='print_area' name='print_area' value="<?=$header.$details;?>">
<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>
