 <script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>

<?
if (!chkRights2('cashpos','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Cash Position ]...");
	exit;
}

if (!session_is_registered('aCashPetty'))
{
	session_register('aCashPetty');
	$aCashPetty=null;
	$aCashPetty=array();
}
if (!session_is_registered('iCashPetty'))
{
	session_register('iCashPetty');
	$iCashPetty=null;
	$iCashPetty=array();
}
if ($date == '' or $date=='//')
{
	$aCashPetty['date'] = date('Y-m-d');
}

if (!in_array($p1, array(Null,'Delete Checked','Edit','Load')))
{
	$iCashPetty['descr'] = $_REQUEST['descr'];
	$iCashPetty['debit'] = $_REQUEST['debit'];
	$iCashPetty['credit'] = $_REQUEST['credit'];
	$iCashPetty['reference'] = $_REQUEST['reference'];
	$iCashPetty['mcheck'] = $_REQUEST['mcheck'];
	$aCashPetty['date'] = mdy2ymd($_REQUEST['date']);
	$aCashPetty['branch_id'] = $_REQUEST['branch_id'];
	if ($aCashPetty['branch_id'] == '')
	{
		$aCashPetty['branch_id'] = lookUpTableReturnValue('x','branch','local','branch_id','Y');
	}
	
	if ($iCashPetty['debit'] == '')
	{
		$iCashPetty['debit'] = 0;
	}
	if ($iCashPetty['credit'] == '')
	{
		$iCashPetty['credit'] = 0;
	}
	$iCashPetty['type'] = $_REQUEST['type'];
	$iCashPetty['rid'] = $_REQUEST['rid'];
	$iCashPetty['tbranch_id'] = $_REQUEST['tbranch_id'];
	
	if ($iCashPetty['rid'] == '')
	{
		$iCashPetty['rid'] = '0';
	}
}

if ($iCashPetty['tbranch_id']=='' or $p1=='Go') $iCashPetty['tbranch_id'] = $tbranch_id = $aCashPetty['branch_id'];

if ($aCashPetty['date'] > date('Y-m-d'))
{
	message('Cannot advance date...Current date is '.date('m/d/Y'));
}
if ($p1 == 'Submit' && $aCashPetty['date'] != date('Y-m-d') and $ADMIN['admin_id'] != 581)
{
	message('Date should be current');
}
elseif ($p1 == 'Submit' && $iCashPetty['descr'] == '')
{
	message('Please provide description...');
}
elseif ($p1 == 'Submit' && ($iCashPetty['debit'] == '' and $iCashPetty['credit']==''))
{
	message('Please provide Amount...');
}
elseif ($p1 == 'Submit' && $aCashPetty['branch_id']=='')
{
	message('Please specify for what branch....');
}
elseif ($p1 == 'Submit' && $iCashPetty['cashpos_id']==''&& !chkRights2('cashpos','madd',$ADMIN['admin_id']))
{
	message("You have no permission to ADD Transaciton in this Area [ Cash Position ]...");
}
elseif ($p1 == 'Submit' && $iCashPetty['cashpos_id']!=''&& !chkRights2('cashpos','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Update/Modify Transaciton in this Area [ Cash Position ]...");
}
elseif ($p1 == 'Submit')
{
	if ($iCashPetty['cashpos_id']=='')
	{
		$q = "insert into cashpos (branch_id,date,mcheck, reference, descr,debit,credit,admin_id, ip, type, rid,tbranch_id)
				values 
					('".$aCashPetty['branch_id']."','".$aCashPetty['date']."','".$iCashPetty['mcheck']."','".$iCashPetty['reference']."','".$iCashPetty['descr']."',
					'".$iCashPetty['debit']."','".$iCashPetty['credit']."','".$ADMIN['admin_id']."','$REMOTE_ADDR', '".$iCashPetty['type']."',
					'".$iCashPetty['rid']."', '".$iCashPetty['tbranch_id']."')";
		$qr = @pg_query($q) or message('Unable to save data...'.pg_errormessage().$q);
	}
	else
	{
		$audit = $iCashPetty['audit'].'; Updated by '.$ADMIN['username'].' on '.date('d/m/Y g:ia');
		$q = "update cashpos set 
					descr = '".$iCashPetty['descr']."',
					debit = '".$iCashPetty['debit']."',
					credit = '".$iCashPetty['credit']."',
					reference = '".$iCashPetty['reference']."',
					mcheck = '".$iCashPetty['mcheck']."',
					type = '".$iCashPetty['type']."',
					rid = '".$iCashPetty['rid']."',
					tbranch_id = '".$iCashPetty['tbranch_id']."',
					audit = '$audit'
				where
					cashpos_id='".$iCashPetty['cashpos_id']."'";
		$qr = @pg_query($q) or message('Unable to update record...'.pg_errormessage());
	}		
	$iCashPetty = null;
	$iCashPetty = array();			
}
elseif ($p1 == 'Delete Checked' and ($aCashPetty['date'] == date('Y-m-d') or $ADMIN['admin_id'] == 581))
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
		$iCashPetty = null;
		$iCashPetty = array();
		$iCashPetty = $r;
		
		$aCashPetty['branch_id'] = $r['branch_id'];
		$aCashPetty['date'] = $r['date'];
	}
}


//-- forwarding
if ($aCashPetty['date'] != date('Y-m-d'))
{
	$q = "select 
					sum(debit) as debit,
					sum(credit) as credit
				from 
					cashpos
				where
					branch_id = '".$aCashPetty['branch_id']."' and
					type in ('P') and 
					date = '".$aCashPetty['date']."' and 
					enable";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$balance = $r->debit - $r->credit;
				

	$q = "select 
					distinct (date) as date 			
				from 
					cashpos
				where
					branch_id = '".$aCashPetty['branch_id']."' and
					date>'".$aCashPetty['date']."' and 
					type in ('P') and 
					enable
				order by
					date";
	$qur = @pg_query($q) or message(pg_errormessage());

	while ($ru = @pg_fetch_object($qur))
	{
		$date = $ru->date;
		$q = "select * from cashpos
						where		
							mcheck ='BB' and
							date = '$date' and 
							branch_id = '".$aCashPetty['branch_id']."' and
							type in ('P') ";
		$qr = @pg_query($q) or message(pg_errormessage().$q);
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
								type = 'P' and 
								date = '$date' and 
								branch_id = '".$aCashPetty['branch_id']."' and
								cashpos_id = '$cashpos_id'";
					$qr = @pg_query($q) or message(pg_errormessage().$q);
			}
		}
		else
		{
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type, mcheck, admin_id, enable)
						values 
							('".$aCashPetty['branch_id']."','$date','Beginning Balance',
							'$balance','0','$balance','P','BB', '".$ADMIN['admin_id']."','TRUE')";
					$qr = @pg_query($q) or message(pg_errormessage().$q);

		}
		
		$q = "select 
						sum(debit) as debit,
						sum(credit) as credit
					from 
						cashpos
					where
						branch_id = '".$aCashPetty['branch_id']."' and
						date = '$date' and 
						type in  ('P') and 
						enable";
		$qr = @pg_query($q) or message(pg_errormessage());
		$r = @pg_fetch_object($qr);
		$balance = $r->debit - $r->credit;
		
	}

}


if ($iCashPetty['type']=='') $iCashPetty['P'];
if ($aCashPetty['branch_id'] == '') $aCashPetty['branch_id'] = $ADMIN['branch_id'];

?>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="22%" height="31" bgcolor="#DADADA"><font size="4" face="Arial, Helvetica, sans-serif"><strong>&nbsp;Petty 
        Cash Fund</strong></font></td>
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
        <input name="date" type="text" id="date" value="<?= ymd2mdy($aCashPetty['date']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"> 
        <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font> 
        <input type="submit" value="Go" name="p1">
        <input type="button" value="Revolving Fund" name="p122" onClick="window.location='?p=cashpos.revolving'">
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
      <td colspan="6"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Entry 
        Details</strong></font></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference 
        <br>
        <input name="reference" type="text" id="reference" value="<?= $iCashPetty['reference'];?>" size="8" maxlength="10"  style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('mcheck').focus();return false;}">
      </font></td>
      <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Payee<br>
        <input name="mcheck" type="text" id="mcheck"  style="border: #CCCCCC 1px solid; " onKeypress="if(event.keyCode==13) {document.getElementById('descr').focus();return false;}" value="<?= $iCashPetty['mcheck'];?>" size="20" maxlength="20">
      </font></td>
      <td width="19%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description 
        <input name="type" type="hidden" id="type" value="P">
        <input name="descr" type="text" id="descr" value="<?= $iCashPetty['descr'];?>" size="35"  style="border: #CCCCCC 1px solid;" onKeypress="if(event.keyCode==13) {document.getElementById('credit').focus();return false;}">
      </font></td>
      <td width="6%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit<br>
        <input name="credit" type="text" id="credit" value="<?= $iCashPetty['credit'];?>" size="10" style="border: #CCCCCC 1px solid; text-align:right" " onKeypress="if(event.keyCode==13) {document.getElementById('debit').focus();return false;}">
      </font></td>
      <td width="7%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit<br>
        <input name="debit" type="text" id="debit" value="<?= $iCashPetty['debit'];?>" size="10"  style="border: #CCCCCC 1px solid; text-align:right" onKeypress="if(event.keyCode==13) {document.getElementById('Submit').focus();return false;}">
        </font>
</td>
      <td width="50%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">InterBranch<br>
        <select name = "tbranch_id" id = "tbranch_id">
          <?
				$q = "select * from branch where enable ";
				$q .=  "order by branch";
				echo "<option value='' selected>Select Branch</option>";
				
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($tbranch_id == $r->branch_id)
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
      </font>
        <input name="p1" type="submit" id="Submit" value="Submit">
</td>
    </tr>
  </table>
  <table width="85%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#C2D6C0"> 
      <td width="6%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
      <td width="8%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Reference</font></strong></td>
      <td width="19%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Payee</font></strong></td>
      <td width="22%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Description</font></strong></td>
      <td width="15%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credit</font></strong></td>
      <td width="13%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debit</font></strong></td>
      <td width="17%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td height="250" colspan="7"> <div id="Layer1" style="position:abolute; width:100%; height:100%; z-index:1; overflow: auto;"> 
          <table width="99%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
            <?
		if ($aCashPetty['branch_id'] != '')
		{
			$q = "select * from cashpos 
						where 
							date='".$aCashPetty['date']."' and
							branch_id='".$aCashPetty['branch_id']."' and 
							type in ('P') and 
							enable
						order by cashpos_id ";
			$qr = @pg_query($q) or message('Unable to query database...');
			if ($qr && pg_num_rows($qr) == 0)
			{
				$q = "select * from cashpos 
							where 
								date<'".$aCashPetty['date']."' and 
								type in ('P') and 
								branch_id='".$aCashPetty['branch_id']."' and enable
						order by cashpos_id  desc";
				$qr = @pg_query($q) or message1(pg_erroemessage());
				
				if (@pg_num_rows($qr)>0)
				{
					$r = @pg_fetch_object($qr);
					$q = "insert into cashpos (branch_id,date,descr,debit,credit, balance, type, mcheck)
						values 
							('".$aCashPetty['branch_id']."','".$aCashPetty['date']."','Beginning Balance',
							'$r->balance','0','$r->balance','P','BB')";
	
					$qr = @pg_query($q) or message('Unable to save date...'.pg_errormessage());
				}
				else
				{
					message('No Previous Balance...');
				}				
			}
			$ctr=0;
			$aCashPetty['balance'] =$aCashPetty['debit']=$aCashPetty['credit']= $balance = 0;
			
			$header  = "\n\n\n";
			$header .= $SYSCONF['BUSINESS_NAME'].' - '. lookUpTableReturnValue('x','branch','branch_id','branch',$aCashPetty['branch_id'])."\n";
			$header .= lookUpTableReturnValue('x','branch','branch_id','branch_address',$aCashPetty['branch_id'])."\n\n";
			$header .= 'Petty Cash Transaction Date:'.ymd2mdy($aCashPetty['date']).'     Printed:'.date('m/d/Y g:ia')."\n";
			$header .= " --- --------- -------------------------------- - ---------- ---------- ---------- \n";
			$header .= "  #  Reference  Payee/Description                    Debit     Credit     Balance \n";
			$header .= " --- --------- -------------------------------- - ---------- ---------- ---------- \n";

			$lc=10;
			$total_loan = $total_excess = $total_other = 0;
			
			while ($r = pg_fetch_object($qr))
			{
				$ctr++;
				$aCashPetty['debit'] += $r->debit ;
				$aCashPetty['credit'] += $r->credit;
				$aCashPetty['balance'] += $r->debit - $r->credit;
				$balance += $r->debit - $r->credit;
				
				$q = "update cashpos set balance='$balance' where cashpos_id='$r->cashpos_id'";
				@pg_query($q) or message("Unable to update balance...");
				
				
				$details .= adjustRight($ctr,3).'. '.
							adjustSize($r->reference,9).' '.
							adjustSize($r->mcheck,8).' '.
							adjustSize($r->descr,23).' '.
							adjustSize($r->type,1).' '.
							adjustRight(number_format($r->debit,2),10).' '.
							adjustRight(number_format($r->credit,2),10).' '.
							adjustRight(number_format($balance,2),10)."\n";
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
                <input name="delete[]" type="checkbox" id="delete[]" value="<?= $r->cashpos_id;?>">
                </font></td>
              <td width="10%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="?p=cashpos.petty&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->reference;?>
                </a> </font></td>
              <td width="22%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=cashpos.petty&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->mcheck;?>
                </a></font></td>
              <td width="25%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="?p=cashpos.petty&p1=Edit&id=<?=$r->cashpos_id;?>"> 
                <?= $r->descr;?>
                </a>&nbsp; </font> </td>
              <td width="12%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->credit,2);?>
                </font></td>
              <td width="12%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($r->debit,2);?>
                </font></td>
              <td width="12%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= number_format($balance,2);?>
                </font></td>
            </tr>
            <?
			}
			$details .= " --- --------- --------------------------------- ----------- ---------- ---------- \n";
			$details .= space(15).
							adjustSize($r->descr,34).' '.
							adjustRight(number_format($aCashPetty['debit'],2),10).' '.
							adjustRight(number_format($aCashPetty['credit'],2),10).' '.
							adjustRight(number_format($balance,2),10)."\n";
			$details .= " --- --------- --------------------------------- ----------- ---------- ---------- \n";
	//		$details .= space(10).'INITIAL BANK BALANCE .............'.number_format(lookUpTableReturnValue('x','branch','branch_id','init_balance',$aCashPetty['branch_id']),2)."\n";
// echo "<pre>$header$details</pre>";
			if ($p1 == 'Print Draft')
			{
					$details .= "\n<eject>\n\n";
					doPrint($header.$details);
			}
			if ($p1 == 'Print')
			{
				$detprint = "<font style='font-family:Courier New, Courier, monospace; font-size:14px; line-height:10pt'>".$header.$details."</font>";
			
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
      <td colspan="3" align="center"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif">TOTAL</font></strong></td>
      <td align="right" width="15%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPetty['credit'],2);?>
        </font></strong></td>
      <td align="right" width="13%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPetty['debit'],2);?>
        </font></strong></td>
      <td align="right" width="17%"><strong><font color="#000099" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= number_format($aCashPetty['balance'],2);?>
        </font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="7"><input name="p1" type="submit" id="p1" value="Delete Checked"> 
        <input name="p1" type="submit" id="p1" value="Print Draft">
        <input name="p1" type="submit" id="p1" value="Print">
<!--        <input name="p13" type="button" id="p12" value="Print" onClick="printIframe(print_area)"></td> -->
		</td>
    </tr>
  </table>
  </form>
  
  <script>
  if (f1.branch_id.value != '')
  {
  	f1.reference.focus()
  }
  else
  {
  	f1.branch_id.focus()
  }
  
  </script>
<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>
