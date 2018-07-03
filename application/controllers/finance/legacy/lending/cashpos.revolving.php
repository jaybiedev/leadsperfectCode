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

if (!session_is_registered('aCashPos'))
{
	session_register('aCashPos');
	$aCashPos=null;
	$aCashPos=array();
}
if (!session_is_registered('iCashPos'))
{
	session_register('iCashPos');
	$iCashPos=null;
	$iCashPos=array();
}
if ($date == '' or $date=='//')
{
	$aCashPos['date'] = date('Y-m-d');
}

if (!in_array($p1, array(Null,'Delete Checked','Edit','Load')))
{
	$iCashPos['descr'] = $_REQUEST['descr'];
	$iCashPos['debit'] = $_REQUEST['debit'];
	$iCashPos['credit'] = $_REQUEST['credit'];
	$aCashPos['date'] = mdy2ymd($_REQUEST['date']);
	$aCashPos['branch_id'] = $_REQUEST['branch_id'];
	if ($aCashPos['branch_id'] == '')
	{
		$aCashPos['branch_id'] = lookUpTableReturnValue('x','branch','local','branch_id','Y');
	}
	
	if ($iCashPos['debit'] == '')
	{
		$iCashPos['debit'] = 0;
	}
	if ($iCashPos['credit'] == '')
	{
		$iCashPos['credit'] = 0;
	}
	$iCashPos['type'] = $_REQUEST['type'];
	$iCashPos['rid'] = $_REQUEST['rid'];
	if ($iCashPos['rid'] == '')
	{
		$iCashPos['rid'] = '0';
	}
}

if ($aCashPos['date'] > date('Y-m-d'))
{
	message('Cannot advance date...Current date is '.date('m/d/Y'));
	$aCashPos['date'] = date('Y-m-d');
}
if ($p1 == 'Submit' && $iCashPos['descr'] == '')
{
	message('Please provide description...');
}
elseif ($p1 == 'selectRid' && $iCashPos['rid']=='')
{
        message("No Account Selected...");
}
elseif ($p1 == 'selectRid')
{
	if ($iCashPos['type'] == 'L')
	{
		$q = "select released as credit, deposit as debit, account from releasing, account where  account.account_id=releasing.account_id and releasing_id = '".$iCashPos['rid']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_assoc($qr);
		$iCashPos['credit'] = $r['credit'];
		$iCashPos['debit'] = $r['debit'];
		$iCashPos['descr'] = strtoupper($r['account']);

	}
	elseif ($iCashPos['type'] == 'E')
	{
                $q = "select net_amount as credit, account from wexcess, account where  account.account_id=wexcess.account_id and  wexcess_id = '".$iCashPos['rid']."'";
                $qr = @pg_query($q) or message(pg_errormessage().$q);
		$r = @pg_fetch_assoc($qr);
		$iCashPos['credit'] = $r['credit'];
		$iCashPos['descr'] = strtoupper($r['account']);
	}
	else
	{
		$iCashPos['credit'] = '';
		$iCashPos['descr'] = '';
	}
	
}
elseif ($p1 == 'Submit' && ($iCashPos['debit'] == '' and $iCashPos['credit']==''))
{
	message('Please provide Amount...');
}
elseif ($p1 == 'Submit' and $aCashPos['date'] != date('Y-m-d') and $ADMIN['admin_id'] != 581)
{
	message('Date should be current....');
}
elseif ($p1 == 'Submit' && $aCashPos['branch_id']=='')
{
	message('Please specify for what branch....');
}
elseif ($p1 == 'Submit' && $iCashPos['cashpos_id']==''&& !chkRights2('cashpos','madd',$ADMIN['admin_id']))
{
	message("You have no permission to ADD Transaciton in this Area [ Cash Position ]...");
}
elseif ($p1 == 'Submit' && $iCashPos['cashpos_id']!=''&& !chkRights2('cashpos','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Update/Modify Transaciton in this Area [ Cash Position ]...");
}
elseif ($p1 == 'Submit')
{
	if ($iCashPos['cashpos_id']=='')
	{
		$q = "insert into cashpos (branch_id,date,descr,debit,credit,admin_id, ip, type, rid)
				values 
					('".$aCashPos['branch_id']."','".$aCashPos['date']."','".$iCashPos['descr']."',
					'".$iCashPos['debit']."','".$iCashPos['credit']."','".$ADMIN['admin_id']."','$REMOTE_ADDR', '".$iCashPos['type']."', '".$iCashPos['rid']."')";
		$qr = pg_query($q) or message('Unable to save data...'.pg_errormessage());
//		echo "here 3".$q;
	}
	else
	{
		$audit = $iCashPos['audit'].'; Updated by '.$ADMIN['username'].' on '.date('d/m/Y g:ia');
		$q = "update cashpos set 
					descr = '".$iCashPos['descr']."',
					debit = '".$iCashPos['debit']."',
					credit = '".$iCashPos['credit']."',
					type = '".$iCashPos['type']."',
					rid = '".$iCashPos['rid']."',
					audit = '$audit'
				where
					cashpos_id='".$iCashPos['cashpos_id']."'";
		$qr = @pg_query($q) or message('Unable to update record...'.pg_errormessage());
	}		
	$iCashPos = null;
	$iCashPos = array();			
}
elseif ($p1 == 'Delete Checked' and ($aCashPos['date'] == date('Y-m-d') or $ADMIN['admin_id'] == 581))
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
		$iCashPos = null;
		$iCashPos = array();
		$iCashPos = $r;
		
		$aCashPos['branch_id'] = $r['branch_id'];
		$aCashPos['date'] = $r['date'];
	}
}
if ($iCashPos['type']=='') $iCashPos['L'];

//-- forwarding
if ($aCashPos['date'] != date('Y-m-d'))
{
	$q = "select 
					sum(debit) as debit,
					sum(credit) as credit
				from 
					cashpos
				where
					branch_id = '".$aCashPos['branch_id']."' and
					type in ('L','E','N') and 
					date = '".$aCashPos['date']."' and 
					enable";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$balance = $r->debit - $r->credit;
				

	$q = "select 
					distinct (date) as date 			
				from 
					cashpos
				where
					branch_id = '".$aCashPos['branch_id']."' and
					date>'".$aCashPos['date']."' and 
					type in ('L','E','N') and 
					enable
				order by
					date";
	$qur = @pg_query($q) or message(pg_errormessage().$q);

	while ($ru = @pg_fetch_object($qur))
	{
		$date = $ru->date;
		$q = "select * from cashpos
						where		
							mcheck ='BB' and
							date = '$date' and 
							branch_id = '".$aCashPos['branch_id']."' and
							type in ('L') ";
		$qr = @pg_query($q) or message(pg_errormessage($qr).$q);

		if (@pg_num_rows($qr) > 0)
		{
			$r = @pg_fetch_object($qr);

			$cashpos_id = $r->cashpos_id;
		
			if ($ADMIN['admin_id'] == 581)
			{
				$q = "update cashpos set 
								debit='$balance',
								descr = 'Beginning Balance', 
								balance = '$balance',
								admin_id = '".$ADMIN['admin_id']."',
								enable='TRUE'
							where
								mcheck='BB' and 
								type = 'L' and 
								date = '$date' and 
								branch_id = '".$aCashPos['branch_id']."' and
								cashpos_id = '$cashpos_id'";
					$qr = @pg_query($q) or message(pg_errormessage().$q);
			}
		}
		else
		{
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type, mcheck, admin_id, enable)
						values 
							('".$aCashPos['branch_id']."','$date','Beginning Balance',
							'$balance','0','$balance','L','BB', '".$ADMIN['admin_id']."','TRUE')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);

		}

		$q = "select 
						sum(debit) as debit,
						sum(credit) as credit
					from 
						cashpos
					where
						branch_id = '".$aCashPos['branch_id']."' and
						date = '$date' and 
						type in  ('L','E','N') and 
						enable";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$balance = $r->debit - $r->credit;
		
	}

}
if ($aCashPos['branch_id'] == '') $aCashPos['branch_id'] = $ADMIN['branch_id'];

?>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="22%" height="31" bgcolor="#DADADA"><font size="4" face="Arial, Helvetica, sans-serif"><strong>&nbsp;Revolving 
        Fund</strong></font></td>
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
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aCashPos['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"> 
        <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
        <input type="submit" value="Go" name="p1">
        <input type="button" value="Petty Cash" name="p122" onClick="window.location='?p=cashpos.petty'">
        <input type="button" value="Collection" name="p12" onClick="window.location='?p=cashpos.collection'">
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
      <td width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type<br>
	  <select name="type" id="type"  style="border: #CCCCCC 1px solid;" onKeypress="if(event.keyCode==13) {document.getElementById('rid').focus();return false;}" onChange="document.getElementById('f1').action='?p=cashpos.revolving&p1=vtype';document.getElementById('f1').submit()">
	  <option value="L" <?= ($iCashPos['type'] == 'L' ? 'selected' : '');?>>Loan</option>
	  <option value="E" <?= ($iCashPos['type'] == 'E' ? 'selected' : '');?>>Excess</option>
	  <option value="N" <?= ($iCashPos['type'] == 'N' ? 'selected' : '');?>>None</option>
	  </select>
        </font></td>
      <td width="24%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Particulars<br>
       <select name="rid" id="rid"  style="border: #CCCCCC 1px solid; width:200" onKeypress="if(event.keyCode==13) {document.getElementById('descr').focus();return false;}" onChange="document.getElementById('f1').action='?p=cashpos.revolving&p1=selectRid';document.getElementById('f1').submit()">
	   <option value="0">Select Particulars</option>
	   <?
	   $mdate = $aCashPos['date'];
           if ($iCashPos['type'] == '')
           {
                $iCashPos['type'] ="L";
           }
	   if ($iCashPos['type'] == 'L')
	   {
	   	$q = "select releasing_id as rid, released as amount, account
					from 
						account,
						releasing
					where
						account.account_id=releasing.account_id and
						date='$mdate' and
						status!='C'"; 
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
					$q .= ") ";
				}
/*			if ($ADMIN['branch_id'] > '0')
			{
				$q .= " and account.branch_id = '".$ADMIN['branch_id']."'";
			}*/
		   $qrp = @pg_query($q);
	   }
	   elseif ($iCashPos['type'] == 'E')
	   {
	   	$q = "select wexcess_id as rid, net_amount as amount, account
					from 
						account,
						wexcess
					where
						account.account_id=wexcess.account_id and
						date='$mdate' and
						status!='C'"; 
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
					$q .= ") ";
				}						
/*				if ($ADMIN['branch_id'] > '0')
				{
					$q .= " and account.branch_id = '".$ADMIN['branch_id']."'";
				}*/

		   $qrp = @pg_query($q);
	   }
	   while ($r = @pg_fetch_object($qrp))
	   {
	   	if ($iCashPos['rid'] == $r->rid)
		{
	   		echo "<option value=$r->rid selected>$r->account</option>";
		}
		else
		{
	   		echo "<option value=$r->rid>$r->account</option>";
		}
	   }
	   ?>
	   </select> </font></td>
      <td width="26%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
        <input name="descr" type="text" id="descr" value="<?= $iCashPos['descr'];?>" size="35" style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('credit').focus();return false;}">
        </font></td>
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit<br>
        <input name="credit" type="text" id="credit" value="<?= $iCashPos['credit'];?>" size="10" style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('debit').focus();return false;}">
        </font></td>
      <td width="40%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit<br>
        <input name="debit" type="text" id="debit" value="<?= $iCashPos['debit'];?>" size="10"   style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('Submit').focus();return false;}">
        <input name="p1" type="submit" id="Submit" value="Submit">
        </font></td>
    </tr>
  </table>
  <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#C2D6C0"> 
      <td width="8%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="39%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="4%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
      <td width="17%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit</font></strong></td>
      <td width="15%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit</font></strong></td>
      <td width="17%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td height="250" colspan="6"> <div id="Layer1" style="position:abolute; width:100%; height:100%; z-index:1; overflow: auto;"> 
          <table width="99%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
            <?
		if ($aCashPos['branch_id'] != '' )
		{
			$q = "select * from cashpos 
						where 
							date='".$aCashPos['date']."' and
							branch_id='".$aCashPos['branch_id']."' and 
							type in ('L','E','N') and 
							enable
						order by cashpos_id ";
			$qr = @pg_query($q) or message('Unable to query database...');
			if ($qr && pg_num_rows($qr) == 0)
			{
				$q = "select * from cashpos 
							where 
								date<'".$aCashPos['date']."' and 
								type in ('L','E','N') and 
								branch_id='".$aCashPos['branch_id']."' and enable
						order by cashpos_id  desc";
				$qr = @pg_query($q) or message1(pg_erroemessage());
				
				if (@pg_num_rows($qr)>0)
				{
					$r = @pg_fetch_object($qr);
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type,mcheck)
						values 
							('".$aCashPos['branch_id']."','".$aCashPos['date']."','Beginning Balance',
							'$r->balance','0','$r->balance','L','BB')";
	
					$qr = @pg_query($q) or message('Unable to save date...'.pg_errormessage());
				}
				else
				{
					message('No Previous Balance...');
				}				
			}
			$ctr=0;
			$aCashPos['balance'] =$aCashPos['debit']=$aCashPos['credit']= $balance = 0;
			
			$header  = "\n\n\n";
			$header .= $SYSCONF['BUSINESS_NAME'].' - '. lookUpTableReturnValue('x','branch','branch_id','branch',$aCashPos['branch_id'])."\n";
			$header .= lookUpTableReturnValue('x','branch','branch_id','branch_address',$aCashPos['branch_id'])."\n\n";
			$header .= 'Revolving Fund Transaction Date:'.ymd2mdy($aCashPos['date']).'     Printed:'.date('m/d/Y g:ia')."\n";
			$header .= " --- -------------------------------- - ------------ ----------- ------------ \n";
			$header .= "  #   Description                        Debit        Credit      Balance \n";
			$header .= " --- -------------------------------- - ------------ ----------- ------------ \n";
			$lc=10;
			$total_loan = $total_excess = $total_other = 0;
			
			while ($r = pg_fetch_object($qr))
			{
				$ctr++;
				$aCashPos['debit'] += $r->debit ;
				$aCashPos['credit'] += $r->credit;
				$aCashPos['balance'] += $r->debit - $r->credit;
				$balance += $r->debit - $r->credit;
				if ($r->type == 'L')
				{
					$total_loan += $r->credit;
				}
				if ($r->type == 'E')
				{
					$total_excess += $r->credit;
				}
				
				$q = "update cashpos set balance='$balance' where cashpos_id='$r->cashpos_id'";
				@pg_query($q) or message("Unable to update balance...");
				
				
				$details .= adjustRight($ctr,3).'. '.
							adjustSize($r->descr,32).' '.
							adjustSize($r->type,1).' '.
							adjustRight(number_format($r->debit,2),11).' '.
							adjustRight(number_format($r->credit,2),12).' '.
							adjustRight(number_format($balance,2),12)."\n";
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
              <td width="10%" align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=$ctr;?>
                . 
                <input name="delete[]" type="checkbox" id="delete[]" value="<?= $r->cashpos_id;?>">
                </font></td>
              <td width="37%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="?p=cashpos.revolving&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->descr;?>
                </a> </font></td>
              <td width="5%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <?= $r->type;?>
                </font> </td>
              <td width="16%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->credit,2);?>
                </font></td>
              <td width="16%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->debit,2);?>
                </font></td>
              <td width="16%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($balance,2);?>
                </font></td>
            </tr>
            <?
			}
			$details .= " --- --------------------------------- ------------- ----------- ------------ \n";
			$details .= space(6).
							adjustSize($r->descr,34).' '.
							adjustRight(number_format($aCashPos['debit'],2),11).' '.
							adjustRight(number_format($aCashPos['credit'],2),11).' '.
							adjustRight(number_format($balance,2),12)."\n";
			$details .= " --- --------------------------------- ------------- ----------- ------------ \n";
			$details .= space(10).'INITIAL BANK BALANCE .............'.number_format(lookUpTableReturnValue('x','branch','branch_id','init_balance',$aCashPos['branch_id']),2)."\n";
			$details .= "\n".space(10).
							'TOTAL LOANS RELEASES.......'.adjustRight(number_format($total_loan,2),11)."\n";
			$details .= space(10).
							'TOTAL EXCESS RELEASES......'.adjustRight(number_format($total_excess,2),11)."\n";

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
      <td colspan="2" align="center"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif">TOTAL</font></strong></td>
      <td align="right" width="17%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPos['credit'],2);?>
        </font></strong></td>
      <td align="right" width="15%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPos['debit'],2);?>
        </font></strong></td>
      <td align="right" width="17%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPos['balance'],2);?>
        </font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="6"><input name="p1" type="submit" id="p1" value="Delete Checked"> 
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
