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
if (!chkRights2('bankrecon','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Bank Recon ]...");
	exit;
}

function forwardBankRecon($currdate)
{
	global $aBankRecon, $ADMIN;
	
	$q = "select 
					sum(debit) as debit,
					sum(credit) as credit
				from 
					bankrecon
				where
					branch_id = '".$aBankRecon['branch_id']."' and
					bank_id =  '".$aBankRecon['bank_id']."' and
					date = '$currdate' and 
					enable=TRUE";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$balance = $r->debit - $r->credit;
//				echo $q;
//				echo "balance $balance";
//echo "balance ".$aBankRecon['date'].":".$balance;
	$q = "select 
					distinct (date) as date 			
				from 
					bankrecon
				where
					branch_id = '".$aBankRecon['branch_id']."' and
					bank_id =  '".$aBankRecon['bank_id']."' and
					date>'$currdate' and 
					enable=TRUE
				order by
					date";
//					echo $q;exit;
	$qur = @pg_query($q) or message(@pg_fetch_object($qr));

	while ($ru = @pg_fetch_object($qur))
	{
		$date = $ru->date;
		$q = "select * from bankrecon 
						where		
							mcheck ='BB' and
							type='B' and 
							date = '$date' and 
							branch_id = '".$aBankRecon['branch_id']."' and
							bank_id =  '".$aBankRecon['bank_id']."' and
							date = '$date' and
							enable=TRUE";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
		if (@pg_num_rows($qr) > 0)
		{
//		echo "row ".pg_num_rows($qr);
			$r = @pg_fetch_object($qr);
			$bankrecon_id = $r->bankrecon_id;
		
			$q = "update bankrecon set debit='$balance'
						where
							mcheck='BB' and
							type='B' and  
							date = '$date' and 
							branch_id = '".$aBankRecon['branch_id']."' and
							bank_id =  '".$aBankRecon['bank_id']."' and
							bankrecon_id = '$bankrecon_id' and
							enable=TRUE";
				$qr = @pg_query($q) or message(pg_errormessage().$q);
				if ($qr) 
				{ 
//					 message1("Beginning Balance ($date) Updated...".number_format($balance,2));
				}

		}
		else
		{
					$q = "insert into bankrecon (branch_id,bank_id,date,descr,debit,credit, balance, type, mcheck, admin_id)
						values 
							('".$aBankRecon['branch_id']."','".$aBankRecon['bank_id']."','$date','Beginning Balance',
							'$balance','0','$balance','B','BB', '".$ADMIN['admin_id']."')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);

					if ($qr) 
					{ 
//						message1("Beginning Balance ($date) Posted...".number_format($balance,2));
					}

		}
//echo "date $date $q ";
		
		$q = "select 
						sum(debit) as debit,
						sum(credit) as credit
					from 
						bankrecon
					where
						branch_id = '".$aBankRecon['branch_id']."' and
						bank_id =  '".$aBankRecon['bank_id']."' and
						date = '$date' and 
						enable=TRUE";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$balance = $r->debit - $r->credit;
		
	}
	return;
}

function updateDeposit($date)
{	
	global $aBankRecon;
	
	$q = "select sum(debit) as debit,
				sum(credit) as credit  
				from 
					bankrecon
				where 
					type='D' and 
					date='$date' and
					mcheck!='BB' and
					branch_id ='".$aBankRecon['branch_id']."' and 
					enable=TRUE";
					
	$qr = @pg_query($q) or message('Error Summing Undeposited Amount...'."<br>".$q);
	$r = @pg_fetch_object($qr);
	$minus = $r->debit - $r->credit;

	$q= "select * from deposit where branch_id ='".$aBankRecon['branch_id']."' order by date desc offset 0 limit 1";
	$qr = @pg_query($q) or message('Error Summing Undeposited Amount...'."<br>".$q);
	$r = @pg_fetch_object($qr);

	$deposit_id = $r->deposit_id; 	
	$lastdate = $r->date;
	$oldbalance = $r->balance;
	$beginning = $r->beginning;
	$add = $r->add;
	
	if ($minus == '') $minus=0;
	if ($oldbalance == '') $oldbalance = 0;
	if ($newbalance == '') $newbalance = 0;
	if ($add == '') $add=0;

	$newbalance = $beginning + $add - $minus;
	if ($lastdate == $date)	
	{
		$q = "update deposit set minus='$minus', balance='$newbalance'
						where
							deposit_id ='$deposit_id'";
		$qr = @pg_query($q) or message('Unable to Update Undeposited Amount...');

	}		
	else
	{
	
		$q = "insert into deposit (date,beginning,minus,balance, branch_id)
					values ('$date', '$oldbalance', '$minus', '$newbalance','".$aBankRecon['branch_id']."')";
		$qr = @pg_query($q); // or message('Unable to Add To Undeposited Amount...'.pg_errormessage().$q);

	}
	
	//-forward balances if work back is made
	if ($date != date('Y-m-d'))
	{
		$beginning_balance=$newbalance;

		$q = "select * 
					from 
						deposit 
					where 
						date>'$date' and  
						branch_id ='".$aBankRecon['branch_id']."'
					order by
						date";
		$qur= @pg_query($q) or message(pg_errormessage().$q);
		
		while  ($ru= @pg_fetch_object($qur))
		{
			$rudate = explode('-',$ru->date);
			if ($rudate[0] < 2000 or $rudate[1] < 0 or $rudate[1] > 12 or $rudate[2] < 0 or $rudate[2] > 31)
			{
				continue;
			}
			$deposit_id = $ru->deposit_id;
			
			$q = "select sum(debit) as debit,
					sum(credit) as credit
				from 
					cashpos 
				where 
					type='C' and 
					date='$ru->date' and
					mcheck!='BB' and
					enable=TRUE";
					
			$qr = @pg_query($q) or message('Error Summing Undeposited Amount...'."<br>".$q);
			$r = @pg_fetch_object($qr);
			$add = $r->debit - $r->credit;

			$q = "select sum(debit) as debit,
						sum(credit) as credit  
					from 
						bankrecon
					where 
						type='D' and 
						date='$ru->date' and
						mcheck!='BB' and
						branch_id ='".$aBankRecon['branch_id']."' and 
						enable=TRUE";
						
			$qr = @pg_query($q) or message('Error Summing Undeposited Amount...'."<br>".$q);
			$r = @pg_fetch_object($qr);
			$minus = $r->debit - $r->credit;

			$newbalance = $balance + $add - $minus;
			$qf = "update deposit set
							beginning_balance = '$beginning_balance', 
							minus='$minus', 
							balance='$newbalance'
						where
							deposit_id ='$deposit_id'";
			$qfr = @pg_query($q) or message('Unable to Update Undeposited Amount...');
			
			$beginning_balance = $newbalance;
		}
	}
}	
if (!session_is_registered('aBankRecon'))
{
	session_register('aBankRecon');
	$aBankRecon=null;
	$aBankRecon=array();
}
if (!session_is_registered('iBankRecon'))
{
	session_register('iBankRecon');
	$iBankRecon=null;
	$iBankRecon=array();
}
if ($aBankRecon['branch_id'] == '' || $aBankRecon['branch_id'] == 0)
{
	$aBankRecon['branch_id'] = $ADMIN['branch_id']; //lookUpTableReturnValue('x','branch','local','branch_id','Y');
}
if ($date == '' or $date=='//')
{
	$aBankRecon['date'] = date('Y-m-d');
}

if (!in_array($p1, array(Null,'Delete Checked','Edit','Load','LoadCheck')))
{

	$aBankRecon['date'] = mdy2ymd($_REQUEST['date']);
	$aBankRecon['branch_id'] = $_REQUEST['branch_id'];
	$aBankRecon['bank_id'] = $_REQUEST['bank_id'];


	$iBankRecon['descr'] = $_REQUEST['descr'];
	$iBankRecon['debit'] = $_REQUEST['debit'];
	$iBankRecon['credit'] = $_REQUEST['credit'];
	$iBankRecon['flag'] = $_REQUEST['flag'];


	$fields_detail = array('checkdate','descr','type','reference','mcheck','debit','credit','flag');
	for ($c=0;$c<count($fields_detail);$c++)
	{
			$iBankRecon[$fields_detail[$c]] = $_REQUEST[$fields_detail[$c]];
	}
	if ($iBankRecon['debit'] == '')
	{
		$iBankRecon['debit'] = 0;
	}
	if ($iBankRecon['credit'] == '')
	{
		$iBankRecon['credit'] = 0;
	}
	
	if ($iBankRecon['flag'] == '')
	{
		$iBankRecon['flag'] = 'U';
	}
}
if ($p1 == 'LoadCheck')
{

	$q = "select * from bankrecon where bankrecon_id = '$id'";
	$qr = @pg_query($q) or message(pg_errormessage().$q);
	$r = @pg_fetch_assoc($qr);

	$aBankRecon=null;
	$aBankRecon=array();
	$iBankRecon=null;
	$iBankRecon=array();
	
	$aBankRecon = $r;
}
if ($aBankRecon['date'] > date('Y-m-d'))
{
	message('Cannot advance date...Current date is '.date('m/d/Y'));
	$aBankRecon['date'] = date('Y-m-d');
}
elseif ($p1 == 'Submit' && $aBankRecon['bank_id'] == '')
{
	message('Please provide Bank...');
}
elseif ($p1 == 'Submit' && $iBankRecon['descr'] == '')
{
	message('Please provide description...');
}
elseif ($p1 == 'Submit' && ($iBankRecon['debit'] == '' and $iBankRecon['credit']==''))
{
	message('Please provide Amount...');
}
elseif ($p1 == 'Submit' && $aBankRecon['branch_id']=='')
{
	message('Please specify for what branch....');
}
elseif ($p1 == 'Submit' && $iBankRecon['bankrecon_id']==''&& !chkRights2('bankrecon','madd',$ADMIN['admin_id']))
{
	message("You have no permission to ADD Transaciton in this Area [ Bank Recon ]...");
}
elseif ($p1 == 'Submit' && $iBankRecon['bankrecon_id']!=''&& !chkRights2('bankrecon','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Update/Modify Transaciton in this Area [ Bank Recon ]...");
}

elseif ($p1 == 'Submit')
{
	if ($iBankRecon['bankrecon_id'] == '')
	{
		$q = "insert into bankrecon (branch_id,bank_id, date, descr, reference, checkdate, mcheck, flag, type,debit,credit,admin_id, ip)
			values 
				('".$aBankRecon['branch_id']."','".$aBankRecon['bank_id']."',
					'".$aBankRecon['date']."','".$iBankRecon['descr']."','".$iBankRecon['reference']."',
					'".$iBankRecon['checkdate']."','".$iBankRecon['mcheck']."','".$iBankRecon['flag']."','".$iBankRecon['type']."',
					'".$iBankRecon['debit']."','".$iBankRecon['credit']."',
					'".$ADMIN['admin_id']."','$REMOTE_ADDR')";
		$qr = @pg_query($q) or message('Unable to save record...'.pg_errormessage());
	}
	else
	{
		$audit = $iBankRecon['audit'].'; Updated by '.$ADMIN['username'].' on '.date('d/m/Y g:ia');
		$q = "update bankrecon set 
					date = '".$aBankRecon['date']."',
					descr = '".$iBankRecon['descr']."',
					checkdate = '".$iBankRecon['checkdate']."',
					mcheck = '".$iBankRecon['mcheck']."',
					flag = '".$iBankRecon['flag']."',
					type = '".$iBankRecon['type']."',
					reference = '".$iBankRecon['reference']."',
					debit = '".$iBankRecon['debit']."',
					credit = '".$iBankRecon['credit']."',
					audit = '$audit'
				where
					bankrecon_id='".$iBankRecon['bankrecon_id']."'";
		$qr = @pg_query($q) or message('Unable to update record...'.pg_errormessage());
	}	

	$iBankRecon = null;
	$iBankRecon = array();			


}
elseif ($p1 == 'Delete Checked')
{
	for ($c=0;$c<count($delete);$c++)
	{
		$q = "update bankrecon set enable='f' where bankrecon_id='".$delete[$c]."'";
		@pg_query($q) or message(pg_errormessage());
		
	}
}
elseif ($p1 == 'Edit' && $id != '')
{
	$q = "select * from bankrecon where bankrecon_id='$id'";
	$qr = @pg_query($q) or message('Unable To Query Cash Position Table...');
	if ($qr)
	{
		$r = pg_fetch_assoc($qr);
		$iBankRecon = null;
		$iBankRecon = array();
		$iBankRecon = $r;

		$aBankRecon['branch_id'] = $r['branch_id'];
		$aBankRecon['bank_id'] = $r['bank_id'];
		$aBankRecon['date'] = $r['date'];
	}
}

if ($aBankRecon['date'] != date('Y-m-d'))
{
	forwardBankRecon($aBankRecon['date']);
}
?>
<form action="" method="post" name="f1" id="f1">
  <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <strong>Bank Reconciliation :</strong> Branch 
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
        Bank 
        <select name="bank_id" id="bank_id" onChange="document.getElementById('go').click()">
          <?
		$Q = "select * from bank where enable='t' ";
		
		$Q .= " and branch_id ='$branch_id' ";

		$Q .= " order by bank ";
		$QR = @pg_query($Q);
		
		$mbank_id ='';
		while ($R = @pg_fetch_object($QR))
		{
			if (chkRights5('bankrecon','medit',$ADMIN['admin_id']) or $R->braccess=='t')
			{	
				if ($R->bank_id == $aBankRecon['bank_id'])
				{
					echo "<option value='$R->bank_id' selected>$R->bank</option>";
				}
				else
				{
					echo "<option value='$R->bank_id'>$R->bank</option>";
				}
				if ($aBankRecon['bank_id'] == '' && $mbank_id=='')
				{
					$mbank_id = $R->bank_id;
				}
			}
		}
		if ($aBankRecon['bank_id'] == '')
		{
			$aBankRecon['bank_id']  = $mbank_id;
		}
		
		
		?>
        </select>
        For Date </font> 
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aBankRecon['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
      <input type="submit" value="Go" name="p1" id="go"> </td>
    </tr>
    <tr> 
      <td><hr color="red"></td>
    </tr>
  </table>
  <table width="97%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td colspan="7"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Entry 
        Details &nbsp; | &nbsp; <a href="#" onClick="document.getElementById('f1').action='?p=bankrecon&p1=viewundeposited';document.getElementById('f1').submit();return false;">View Undeposited</a></strong></font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type<br>
        <?= lookUpAssoc('type',array('Withdraw'=>'W','Deposit(Collection)'=>'D','Deposit(Other)'=>'S','Interest'=>'I','WTax'=>'T','CM'=>'C','Checkbook'=>'K', 'Others'=>'O'),$iBankRecon['type'], 110);?>
     		</font></td>
      <td width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description<br>
        <input name="descr" type="text" id="descr" value="<?= $iBankRecon['descr'];?>" size="30" style="border: #CCCCCC 1px solid;"  onKeypress="if(event.keyCode==13) {document.getElementById('reference').focus();return false;}">
        </font></td>
      <td width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font><br>
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="reference" type="text" id="reference" value="<?= $iBankRecon['reference'];?>" size="8" maxlength="9" style="border: #CCCCCC 1px solid; "  onKeypress="if(event.keyCode==13) {document.getElementById('checkdate').focus();return false;}">
        </font> </td>
      <td width="7%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Check 
        Date<img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.checkdate, 'mm/dd/yyyy')"><br>
        <input name="checkdate" type="text" id="checkdate" value="<?= $iBankRecon['checkdate'];?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('mcheck').focus();return false;}">
        </font></td>
      <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Check 
        No. </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input type="checkbox" name="flag" id="flag" value="R"  <?= (($iBankRecon['flag'] == 'R' or $iBankRecon['flag'] == '' )? 'checked' : '');?> alt="If  Check Released" onMouseOver="showToolTip(event,'For Withdrawals or Check Disbursement.  Check Box To Indicate Cleared cheque. UnCheck To Indicate UnCleared cheque..');return false" onMouseOut="hideToolTip()">
        </font><br> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="mcheck" type="text" id="mcheck" value="<?= $iBankRecon['mcheck'];?>" size="12" maxlength="15" style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('credit').focus();return false;}">
        </font> </td>
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit<br>
        <input name="credit" type="text" id="credit" value="<?= $iBankRecon['credit'];?>" size="9" style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('debit').focus();return false;}">
        </font></td>
      <td width="29%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit<br>
        <input name="debit" type="text" id="debit" value="<?= $iBankRecon['debit'];?>" size="9" style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('Submit').focus();return false;}">
        <input name="p1" type="submit" id="Submit" value="Submit" style="margin:0px">
        </font></td>
    </tr>
  </table>
  <table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#C2D6C0"> 
      <td width="7%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="5%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
      <td width="33%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="8%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></strong></td>
      <td width="10%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Check 
        Date </font></strong></td>
      <td width="12%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Check 
        No </font></strong></td>
      <td width="14%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit</font></strong></td>
      <td width="15%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit</font></strong></td>
    </tr><tr valign="top" bgcolor="#FFFFFF"><td height="250" colspan="8">
    <div id="Layer1" style="position:abolute; width:100%; height:100%; z-index:1; overflow: auto;"> 
      <table width="99%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
        <?
		if ($aBankRecon['branch_id'] != '' && $aBankRecon['bank_id'] != ''  && $p1 != '')
		{
			$q = "select * from bankrecon 
						where 
							date='".$aBankRecon['date']."' and
							branch_id='".$aBankRecon['branch_id']."' and 
							bank_id='".$aBankRecon['bank_id']."' and 
							enable=TRUE
						order by 
							type, bankrecon_id ";
			$qr = @pg_query($q) or message('Unable to query database...');
			
			if (@pg_num_rows($qr) == 0)
			{
				$q = "select * from bankrecon 
							where 
								date<'".$aBankRecon['date']."' and 
								branch_id='".$aBankRecon['branch_id']."' and 
								bank_id='".$aBankRecon['bank_id']."' and 
								enable=TRUE
						order by date  desc offset 0 limit 1";

				$qr = @pg_query($q) or message1(pg_errormessage());
				
				if (@pg_num_rows($qr)>0)
				{

					$r = @pg_fetch_object($qr);

					$q = "select sum(debit) as debit, sum(credit) as credit from bankrecon 
								where 
									date ='$r->date' and 
									branch_id='".$aBankRecon['branch_id']."' and 
									bank_id='".$aBankRecon['bank_id']."' and 
									enable=TRUE";
									
					$qr = @pg_query($q) or message1(pg_errormessage());
					$r = @pg_fetch_object($qr);
					$balance = $r->debit - $r->credit;
	
					$q = "insert into bankrecon (branch_id,bank_id,date,descr,debit,credit, balance, type, mcheck, admin_id)
						values 
							('".$aBankRecon['branch_id']."','".$aBankRecon['bank_id']."','".$aBankRecon['date']."','Beginning Balance',
							'$balance','0','$balance','B','BB', '".$ADMIN['admin_id']."')";
	
					$qr = @pg_query($q) or message('Unable to save date...'.pg_errormessage());

					if ($qr) 
					{ 
						message1('Beginning Balance Posted...'.number_format($balance,2));
					}
					$q = "select * from bankrecon 
								where 
									date='".$aBankRecon['date']."' and
									branch_id='".$aBankRecon['branch_id']."' and 
									bank_id='".$aBankRecon['bank_id']."' and 
									enable=TRUE
								order by bankrecon_id ";
					$qr = @pg_query($q) or message('Unable to query database...');

				}
				else
				{
					message('No Previous Balance...');
				}				
					
			}
			
			$ctr=0;
			$aBankRecon['balance'] =$aBankRecon['debit']=$aBankRecon['credit']= $balance = 0;
			
			$header  = "\n\n\n";
			if ($p1 == 'Print') $header .= "<small3>";
			$header .= $SYSCONF['BUSINESS_NAME'].' - '. lookUpTableReturnValue('x','branch','branch_id','branch',$aBankRecon['branch_id'])."\n";
			$header .= lookUpTableReturnValue('x','branch','branch_id','branch_address',$aBankRecon['branch_id'])."\n\n";
			$header .= lookUpTableReturnValue('x','bank','bank_id','bank',$aBankRecon['bank_id'])."\n\n";
			$header .= 'Bank Recon Transaction Date:'.ymd2mdy($aBankRecon['date']).'     Printed:'.date('m/d/Y g:ia')."\n";
			$header .= " --- ----------------------------------------  ---- ---------- ----------------- --------------- -------------- \n";
			$header .= "  #   Description                              Type Check Date     Check No.          Debit           Credit        \n";
			$header .= " --- ----------------------------------------  ---- ---------- ----------------- --------------- -------------- \n";
			$lc=10;
			$details ='';
			$total_withdrawal = $total_deposit = $total_cm = $total_wtax = $total_other = 0;

			if ($p1 == 'Print Draft')
			{
				doPrint("<small3>");
			}
			$total_uncleared = $count_uncleared =  $beginning_balance = 0;
			updateDeposit($aBankRecon['date']);

			$qu = "select sum(credit) as credit,
								sum(debit) as debit,
								count(*) as count_uncleared
							from
								bankrecon
							where
								bank_id='".$aBankRecon['bank_id']."' and
								branch_id='".$aBankRecon['branch_id']."' and
								type='W' and 
								flag = 'U' and
								enable='TRUE' and 
								date<='".$aBankRecon['date']."'";
			$qur = @pg_query($qu) or message(pg_errormessage());
			$ru = @pg_fetch_object($qur);
			$total_uncleared = ($ru->credit - $ru->debit);
			$count_uncleared = $ru->count_uncleared;

			while ($r = @pg_fetch_object($qr))
			{
				$ctr++;
				$aBankRecon['debit'] += $r->debit ;
				$aBankRecon['credit'] += $r->credit;
				$aBankRecon['balance'] += $r->debit - $r->credit;
				$balance += $r->debit - $r->credit;
				if ($r->type == 'W')
				{
					$total_withdrawal += $r->debit - $r->credit;
				}	
				if ($r->mcheck=='BB') $beginning_balance = $r->debit;
				if ($r->type == 'D' or $r->type == 'S')
					$total_deposit += $r->debit - $r->credit;
				if ($r->type == 'C')
					$total_cm += $r->debit - $r->credit;
				if ($r->type == 'I')
					$total_interest += $r->debit - $r->credit;
				if ($r->type == 'T')
					$total_wtax += $r->debit - $r->credit;
				if ($r->type == 'O')
					$total_other += $r->debit - $r->credit;

				$q = "update bankrecon set balance='$balance' where bankrecon_id='$r->bankrecon_id'";
				@pg_query($q) or message("Unable to update balance...");
				
				forwardBankRecon($aBankRecon['date']);
				$details .= adjustRight($ctr,3).'. '.
							adjustSize($r->descr,40).'  '.
							adjustSize($r->type.($r->type=='W'?'-'.$r->flag:''),3).' '.
							adjustSize($r->checkdate,10).'  '.
							adjustSize($r->mcheck,18).' '.
							adjustRight(number_format($r->debit,2),14).' '.
							adjustRight(number_format($r->credit,2),14).' '."\n";
							//adjustRight(number_format($balance,2),15)."\n";
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
          <td width="7%" align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?=$ctr;?>
            . 
            <input name="delete[]" type="checkbox" id="delete[]" value="<?= $r->bankrecon_id;?>">
            </font></td>
          <td width="5%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <a href="?p=bankrecon&p1=Edit&id=<?=$r->bankrecon_id;?>"> 
            <?= $r->type;?>
            </a> </font></td>
          <td width="33%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankrecon&p1=Edit&id=<?=$r->bankrecon_id;?>"> 
            <?= $r->descr;?>
            </a></font></td>
          <td width="8%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankrecon&p1=Edit&id=<?=$r->bankrecon_id;?>"> 
            <?= $r->reference;?>
            </a></font></td>
          <td width="10%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankrecon&p1=Edit&id=<?=$r->bankrecon_id;?>"> 
            <?= $r->checkdate;?>
            </a></font></td>
          <td width="12%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=bankrecon&p1=Edit&id=<?=$r->bankrecon_id;?>"> 
            <?= $r->mcheck. ($r->type=='W' ? ' ('.$r->flag.')' : '');?>
            </a></font></td>
          <td width="12%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?= number_format($r->credit,2);?>
            </font></td>
          <td width="12%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?= number_format($r->debit,2);?>
            </font></td>
          <!--  <td width="13%" align="right" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($balance,2);?>--></font></td></tr>
        <?
			}
			$details .= " --- ----------------------------------------  ---- ---------- ----------------- --------------- --------------\n";
			$details .= space(80).'  '.
							adjustRight(number_format($aBankRecon['debit'],2),14).' '.
							adjustRight(number_format($aBankRecon['credit'],2),14).' '."\n";
							//adjustRight(number_format($balance,2),15)."\n";
			$details .= " --- ----------------------------------------  ---- ---------- ----------------- --------------- -------------- \n";
			$details .="\n";

			$q = "select * from bank where bank_id='".$aBankRecon['bank_id']."'";
			$r = fetch_object($q);
		
			$init_balance = $r->init_balance;
			$date_init = $r->date_init;

			$q = "select 
							sum(debit) as debit, 
							sum(credit) as credit 
						from
							bankrecon 
						where 
							date <= '".$aBankRecon['date']."' and
							branch_id='".$aBankRecon['branch_id']."' and 
							bank_id='".$aBankRecon['bank_id']."' and 
							enable";
							
			$r = fetch_object($q);
			
			$total_deposit = $r->debit - $beginning_balance;
			$total_withdrawal= $r->credit;
			//$initial_balance = $r->debit-$r->credit;
			
			
			$details .= space(15).str_pad('Initial Bank Balance as of '.ymd2mdy($date_init),45,'.').' '.
						adjustRight(number_format($init_balance,2),14)."\n";
			$details .= space(15).str_pad('[+] Overall Deposit',45,'.').' '.
						adjustRight(number_format($total_deposit,2),14)."\n";
			$details .= space(15).str_pad('[-] Overall Withdrawal',45,'.').' '.
						adjustRight(number_format($total_withdrawal,2),14)."\n";

			if ($total_interest != 0)
			{
				$details .= space(15).str_pad('[+] Overall Interest',45,'.').' '.
						adjustRight(number_format($total_interest,2),14)."\n";
			}

			if ($total_wtax != 0)
			{
				$details .= space(15).str_pad('[-] Overall WTax',45,'.').' '.
						adjustRight(number_format($total_wtax,2),14)."\n";
			}

			if ($total_cm != 0)
			{
				$details .= space(15).str_pad('[ ] Overall Credit Memo',45,'.').' '.
						adjustRight(number_format($total_cm,2),14)."\n";
			}

			if ($total_other != 0)
			{
				$details .= space(15).str_pad('[ ] Overall Other',45,'.').' '.
						adjustRight(number_format($total_other,2),14)."\n";
			}
			
			$current_balance = $init_balance + $total_deposit - $total_withdrawal + $total_interest - $total_wtax + $total_cm + $total_other;
			$details .= space(15).str_repeat('-',65)."\n";
			$details .= space(15).str_pad('Bank Balance as of '.ymd2mdy($aBankRecon['date']),45,'.').' '.
						adjustRight(number_format($current_balance,2),14)."\n";
			$details .= space(15).str_repeat('-',65)."\n";


		} //if with branch_id				

			?>
      </table>
    </div></td></tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="3"><strong> 
        <input name="p1" type="submit" id="p1" value="Delete Checked">
        <input name="p1" type="submit" id="p1" value="Print Draft">
        <font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        &nbsp;&nbsp;
        <input name="p1" type="submit" id="p1" value="Print">
        </font></strong></td>
      <td colspan="3" align="center"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif">TOTAL</font></strong></td>
      <td align="right" width="14%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aBankRecon['credit'],2);?>
        </font></strong></td>
      <td align="right" width="15%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aBankRecon['debit'],2);?>
        </font></strong></td>
      <!-- <td align="right" width="13%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aBankRecon['balance'],2);?>
        </font></strong></td>-->
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="3" align="center"><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">BALANCE 
        PER PASSBOOK<br>
        P 
        <?= number_format($balance + $total_uncleared,2);?>
        </font> </td>
      <td colspan="3" align="center"><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        UN-CLEARED CHEQUE<br>
        P 
        <?= number_format($total_uncleared,2).'   #'.$count_uncleared;?>
        </font></td>
      <td colspan="2" align="center"><strong><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        ACTUAL BALANCE<br>
        P 
        <?= number_format($balance,2);?>
        </font></strong></td>
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
  <?
  if ($p1 == 'viewundeposited')
  {
		if (!file_exists('undeposited.php'))
		{
			message1("File Does NOT exists...");
		}
		else
		{
	  		include_once('undeposited.php');
		}
  }

	$details .= " Un-Cleared Cheque: P". adjustRight(number_format($total_uncleared,2),14).'   #'.$count_uncleared.' '.
					"Balance Per Passbook: P".adjustRight(number_format($balance + $total_uncleared,2),14).' '.
					"Actual Balance : P ".adjustRight(number_format($balance,2),14)."\n\n";

//		echo "<pre>$header$details</pre>";

	if ($p1 == 'Print Draft')
	{
			$details .= "\n<eject>\n\n";
			doPrint($header.$details);
	}
	if ($p1 == 'Print')
	{
				$detprint = "<font style='font-family:Courier New, Courier, monospace; font-size:12px; line-height:10pt'>".$header.$details."</font>";
	
		echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$detprint.'"'.">";
		echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
		echo "</iframe>";
		echo "<script>printIframe(print_area)</script>";
	}

  ?>

<input type='hidden' id='print_area' name='print_area' value="<font size='3'><?=$header.$details;?></font>">
<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>
