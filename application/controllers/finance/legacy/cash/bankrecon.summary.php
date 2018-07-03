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
if (!chkRights2('bankrecon','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Bank Recon ]...");
	exit;
}


if ($from_date == '') $from_date = date('m/d/Y');
if ($to_date == '') $to_date = date('m/d/Y');
if ($branch_id == '' && $ADMIN['branch_id'] > '0')
{
	$branch_id = $ADMIN['branch_id'];
}

if ($branch_id == '') 
{
	$q = "select * from branch where local";
	$qr =  @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_object($qr);
	$branch_id = $r->branch_id;
	
	$q = "select * from bankrecon where enable and branch_id = '$branch_id' and flag='U' order by date offset 0 limit 1";
	$qr = @pg_query($q);
	$r = @pg_fetch_object($qr);
	$from_date = ymd2mdy($r->date);
}

	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);

?>
<body bgcolor="#EFEFEF">	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_horizontal.PNG" width="10" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Bank 
          Transactions </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_horizontal.PNG" width="10" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="94" height="24"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
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
              </font></td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">From 
                Date</font></td>
              <td width="501" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="from_date" type="text" id="from_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$from_date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, form1.from_date, 'mm/dd/yyyy')"> 
                To Date 
                <input name="to_date" type="text" id="to_date"  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?=$to_date;?>" size="10">
                <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, form1.to_date, 'mm/dd/yyyy')"> 
                <input name="p13" type="submit" id="p13" value="Go">
                <input name="p14" type="button" id="p14" value="Close" onClick="window.location='?p='">
                </font></td>
              <td width="223" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                </font></td>
            </tr>
            <tr> 
              <td height="24" colspan="3"><hr></td>
            </tr>
            <tr align="left" valign="top"> 
              <td height="24" colspan="3"><table width="100%%" border="0" cellspacing="1" cellpadding="0">
                  <tr bgcolor="#A4B9DB"> 
                    <td width="23%" rowspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank</font></td>
                    <td width="13%" rowspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Acct.No.</font></td>
                    <td width="12%" rowspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Beginning</font></td>
                    <td colspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Credits</font></td>
                    <td width="13%" rowspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Debits</font></td>
                    <td width="15%" rowspan="2" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></td>
                  </tr>
                  <tr bgcolor="#A4B9DB"> 
                    <td width="11%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cleared</font></td>
                    <td width="13%" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">UnCleared</font></td>
                  </tr>
                  <?
					$header  = "\n\n\n";
					$header .= $SYSCONF['BUSINESS_NAME'].' - '. lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id)."\n";
					$header .= lookUpTableReturnValue('x','branch','branch_id','branch_address',$branch_id)."\n\n";
					$header .= 'Bank Transactions - Date:'.($from_date).'  To '.($to_date).'    Printed:'.date('m/d/Y g:ia').' by: '.$ADMIN['name']."\n";
					$header .= " --- --------------------------- -------------------- -------------- -------------- -------------- -------------- -------------- \n";
					$header .= "                                                                                              Credits\n";
					$header .= "  #   Bank                            Account No.       Beginning      Cleared  UnCleared        Debits         Balance \n";
					$header .= " --- --------------------------- -------------------- -------------- -------------- -------------- -------------- -------------- \n";
					$lc=10;

					if ($p1 == 'Print Draft')
					{
						doPrint("<small3>");
					}
				  
				  
				  $q = "select * from bank where enable ";
				  if ($branch_id > '0')
				  {
				  	$q .= " and branch_id = '$branch_id' ";
				  }
				  $q .= " order by bank  ";
				  $qr = @pg_query($q) or message(pg_errormessage().$q);
				  $c=0;
				  while ($r = @pg_fetch_object($qr))
				  {
				  	$c++;
					
					$beginning = $credit = $debit = $balance =0;
					$q = "select balance from bankrecon where type='B' and mcheck='BB' and date ='$mfrom_date' and enable and 
									branch_id ='$branch_id' and bank_id ='$r->bank_id' order by bankrecon_id  offset 0 limit 1";

					$qqr = @pg_query($q) or message(pg_errormessage().$q);

					if (@pg_num_rows($qqr) == 0)
					{
						
						$q = "select balance from bankrecon where  enable and 
										branch_id ='$branch_id' and bank_id ='$r->bank_id' order by date desc  offset 0 limit 1";
	
						$qqr = @pg_query($q) or message(pg_errormessage().$q);
					}
					$rr = @pg_fetch_object($qqr);
					$beginning = $rr->balance;

/*					$q = "select balance from bankrecon where date <='$mto_date' and enable  and branch_id ='$branch_id' and bank_id ='$r->bank_id' order by bankrecon_id desc offset 0 limit 1";
					$qqr = @pg_query($q) or message(pg_errormessage().$q);
					$rr = @pg_fetch_object($qqr);
					$balance = $rr->balance;
*/					
					$q = "select sum(debit) as debit, sum(credit) as credit 
								from 
										bankrecon 
								where 
										type!='B' and 
										enable  and 
										branch_id ='$branch_id' and 
										bank_id ='$r->bank_id' and
										date>='$mfrom_date' and
										date<='$mto_date'";
										
					$qqr = @pg_query($q) or message(pg_errormessage().$q);
					$rr = @pg_fetch_object($qqr);
					$credit = $rr->credit;
					$debit = $rr->debit;

					$q = "select sum(debit) as debit, sum(credit) as credit 
								from 
										bankrecon 
								where 
										type!='B' and 
										flag='R' and 
										enable  and 
										branch_id ='$branch_id' and 
										bank_id ='$r->bank_id' and
										date>='$mfrom_date' and
										date<='$mto_date'";
										
					$qqr = @pg_query($q) or message(pg_errormessage().$q);
					$rr = @pg_fetch_object($qqr);
					$creditR = $rr->credit;

					$q = "select sum(debit) as debit, sum(credit) as credit 
								from 
										bankrecon 
								where 
										type='W' and 
										flag='U' and 
										enable  and 
										branch_id ='$branch_id' and 
										bank_id ='$r->bank_id' and
										date>='$mfrom_date' and
										date<='$mto_date'";
					
					if ($ADMIN['branch_id'] > '0')
					{
						$q .= "and branch_id = '".$ADMIN['branch_id']."'";
					}					
					$qqr = @pg_query($q) or message(pg_errormessage().$q);

					$rr = @pg_fetch_object($qqr);
					$creditU = $rr->credit;

					$balance = $beginning + $debit - $credit;
					
					$total_beginning += $beginning;
					$total_debit += $debit;
					$total_creditR += $creditR;
					$total_creditU += $creditU;
					$total_balance += $balance;
					
					$details .= adjustRight($c,3).'. '.
									adjustSize(trim($r->bank),30).' '.
									adjustSize($r->bank_account,17).' '.
									adjustRight(number_format($beginning,2),14).' '.		
									adjustRight(number_format($creditR,2),14).' '.		
									adjustRight(number_format($creditU,2),14).' '.		
									adjustRight(number_format($debit,2),14).' '.		
									adjustRight(number_format($balance,2),14)."\n";


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
//					echo "<pre>$header$details</pre>";		
				  ?>
                  <tr bgColor="<?= ($c%2 == 0 ? '#EFEFEF' : '#FFFFFF');?>"> 
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?=$r->bank;?>
                      </font></td>
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= $r->bank_account;?>
                      </font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($beginning,2);?>
                      &nbsp; </font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($creditR,2);?>
                      &nbsp;</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                      <?= number_format($creditU,2);?>
                      </font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($debit,2);?>
                      &nbsp;</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($balance,2);?>
                      &nbsp;</font></td>
                  </tr>
                  <?
				  }

					$details .= " --- --------------------------- -------------------- -------------- -------------- -------------- -------------- -------------- \n";
					$details .= space(35).
									adjustSize('Total -->',18).' '.
									adjustRight(number_format($total_beginning,2),14).' '.		
									adjustRight(number_format($total_creditR,2),14).' '.		
									adjustRight(number_format($total_creditU,2),14).' '.		
									adjustRight(number_format($total_debit,2),14).' '.		
									adjustRight(number_format($total_balance,2),14)."\n";
					$details .= " --- --------------------------- -------------------- -------------- -------------- -------------- -------------- -------------- \n";
				//			echo "<pre>$header$details</pre>";
				  
					if ($p1 == 'Print Draft')
					{
							$details .= "\n<eject>\n\n";
							doPrint($header.$details);
					}
				  ?>
                  <tr> 
                    <td>&nbsp;</td>
                    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($total_beginning,2);?>
                      &nbsp;</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($total_creditR,2);?>
                      &nbsp;</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                      <?= number_format($total_creditU,2);?>
                      </font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($total_debit,2);?>
                      &nbsp;</font></td>
                    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <?= number_format($total_balance,2);?>
                      &nbsp;</font></td>
                  </tr>
                </table></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
    <input name="p1" type="button" id="p1" value="Close" onClick="window.location='?p='">
    <input name="p1" type="submit" id="p1" value="Print Draft">
    <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>
<input type='hidden' id='print_area' name='print_area' value="<font size='2'><?=$header.$details;?></font>">
<iframe name='printit' id='printit' style='width:0px;height:0px;'></iframe>
