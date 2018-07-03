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
if (!chkRights2('payment','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Payment View Reports ]...");
	exit;
}

if (($p1=='Go' || $p1=='Print Draft') && ($year==''))
{
	message('Please provide date coverage...');
}
elseif ($p1=='Go' || $p1=='Print Draft')
{
	$mfrom_date = mdy2ymd($from_date);
	$mto_date = mdy2ymd($to_date);

	$q = "select account.account, 
				account.clientbank_id, 
				account.branch_id,
				account.account_code, 
				account_group.account_group, 
				branch.branch,
				branch.branch_code,
				clientbank.clientbank,
				releasing.date as loandate,
				releasing.ammort,
				releasing.balance,
				releasing.releasing_id
			from 
				account,
				account_group,
				branch,
				clientbank,
				releasing
			where 
				account_group.account_group_id=account.account_group_id and 
				clientbank.clientbank_id = account.clientbank_id and 
				branch.branch_id=account.branch_id and 
				releasing.account_id =account.account_id";
				// and
			//	substring(releasing.date,1,4)='$year'";
//				releasing.balance>0";
	if ($branch_id != '')
	{
		$q .= " and account.branch_id = '$branch_id'";
	}
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id = '$account_group_id'";
	}
	$q .= "	order by branch, clientbank, account.account ";
	$qr = pg_query($q) or message(pg_errormessage());

	if ($p1 == 'Print Draft')
	{
				doPrint('<small3>');
	}
	$page=1;
	$header1 = "\n\n";
	$hd = explode('-',$SYSCONF['BUSINESS_NAME']);
	if ($branch_id !=0) 	$hdr = $hd[0].' - '.lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	else $hdr = $SYSCONF['BUSINESS_NAME'];
	$header1 .= center($hdr,120)."\n";
	$header1 .= center('PAYMENT CALENDAR',120)."\n";
	$header1 .= center('Year : '.$year.'   Page '.$page,120)."\n\n";
	
	$aCal=null;
	$aCal=array();
	$aCal = array('NA','January','Febuary','March','April','May','June','July','August','September','October','November','December');
	
	$line1 = "    #      Account                       Date        Ammort  ";
	$line2 = '------- ------------------------------- ---------- ----------' ;
	for ($c=1;$c<=12;$c++)
	{
		$line1 .= ' '.center($aCal[$c],9);
		$line2 .= ' ---------';
	}
	$line1 .= "\n";
	$line2 .= "\n";
	
	$header =  $header1.$line1.$line2;
	$details = $details1 = '';
	$total_amount =0;
	$subtotal  = 0;
	$lc=6;
	$mbranch_id = '';

	$subtotal = 0;
	$ctr = 0;
	$mclientbank_id = '';
	
	$aBR =  $aGT = null;
	$aBR =  $aGT = array();
	while ($r = @pg_fetch_object($qr))
	{
		$ctr++;
		$lc++;
		
		if ($r->branch_id != $mbranch_id)
		{
			if ($mbranch_id != '')
			{
				$details .= space(20).adjustSize('SUB-TOTAL',32);
				for ($c=0;$c<=12;$c++)
				{
					$details .= adjustRight(number_format($aBR[$c],2,'.',''),9).' ';
				}					
				$details .= "\n";
				$details .= "\n";
				$lc++;
			}
			$mbranch_id = $r->branch_id;
			$details .= space(3)."BRANCH : ".strtoupper($r->branch)."\n";
			
			$aBR=null;
			$aBR=array();
			$lc++;
		}

		$q = "select 
						ledger.credit as amount,
						ledger.date as pdate
					from 
						ledger
					where
						status!='C' and
						type='C' and 
						extract(year from ledger.date)='$year' and
						releasing_id = '$r->releasing_id'
					group by
						ledger.date, ledger.credit
					order by
						extract(year from ledger.date)";

		/*					
		$q = "select sum(payment_detail.amount) as amount,
							substring(payment_detail.ddate,1,7) as pdate
						from 
							payment_header,
							payment_detail
						where
							payment_header.payment_header_id = payment_detail.payment_header_id and
							payment_header.status!='C' and
							substring(payment_detail.ddate,1,4)='$year' and
							payment_detail.releasing_id = '$r->releasing_id'
						group by
							substring(payment_detail.ddate,1,7)
						order by
							substring(payment_detail.ddate,1,7)";
		*/
		$qqr = @pg_query($q) or die(pg_errormessage());
		if (@pg_num_rows($qqr)==0 && $r->balance<= 0 ) continue;


		$details .= space(3).
					adjustRight($ctr,3).'. '.
					adjustSize($r->account,30).' '.
					adjustSize(ymd2mdy($r->loandate),10). ' '.
					adjustRight(number_format($r->ammort,2),11). ' ';
					
		$aP = null;
		$aP=array();
		
		$aBR[0] += $r->ammort;
		$aGT[0] += $r->ammort;
		while ($rr = @pg_fetch_object($qqr))
		{
			$month = substr($rr->pdate,5,2)*1;
			$aP[$month] = $rr->amount;
			$aBR[$month] += $rr->amount;
			$aGT[$month] += $rr->amount;
		}	
		
		for ($c=1;$c<=12;$c++)
		{
			$details .= adjustRight(number_format2($aP[$c],2),9).' ';
		}					
		$details .= "\n";
					

		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details1 .= $header.$details;
			$details .= "<eject>";
			if ($page_from == '' or ($page>= $page_from and $page<=$page_to))
			{
				doPrint($header.$details);
			}
			$details = $detailsx = '';

			$page++;
			$header1 = "\n\n";
			$header1 .= center($SYSCONF['BUSINESS_NAME'],120)."\n";
			$header1 .= center('PAYMENT CALENDAR',120)."\n";
			$header1 .= center('Year : '.$year.'   Page '.$page,120)."\n\n";
			$header  = $header1.$line1.$line2;
			$lc=6;
		}			
	}	
//	$details .= space(20).adjustSize('SUB-TOTAL',32);
//	for ($c=0;$c<=12;$c++)
//	{
//		$details .= adjustRight(number_format($aBR[$c],2,'.',''),9).' ';
//	}					
//	$details .= "\n";

	$details .= $line2;
	
//	$details .= space(20).adjustSize('GRAND TOTAL',31);
//	for ($c=0;$c<=12;$c++)
//	{
//		$details .= adjustRight(number_format($aGT[$c],2,'.',''),9).' ';
//	}					
	$details .= "\n";
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		doPrint($header.$details."<eject>");
	}	

/*	if ($branch_id != '')
	{
		
		$aip = explode('.',$_SERVER['REMOTE_ADDR']);

		$reportfile= 'reports/COLLECT-'.$branch_code.'-'.$aip[3].'.txt';
		$fo = fopen($reportfile,'w+');
		if (@!fwrite($fo, $exportdata))
		{
			 message("Unable to create report file...");
		}
	}*/
}

if ($year == '') 
{
	if (date('m') < 2)
	{
		$year = date('Y')-1;	
	}
	else
	{
		$year = date('Y');	
	}
}
?>	
<form name="form1" method="post" action="">
  <div align="center"> 
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Payment 
          Calendar </b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
            <!--DWLayoutTable-->
            <tr> 
              <td width="68" height="24" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Year</font></td>
              <td valign="top"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <input name="year" type="text" id="year" value="<?= $year;?>" size="8">
                </font></td>
              <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
            </tr>
            <tr> 
              <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch</font></td>
              <td valign="top" nowrap><select name = "branch_id">
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
	                  <option value=''>All Branches</option>
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
                </select> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                </font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font> 
                <select name = "account_group_id" style="width:240">
                  <option value=''>All Account Groups</option>
                  <?
				$q = "select * from account_group where enable order by account_group";
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($account_group_id == $r->account_group_id)
					{
						echo "<option value=$r->account_group_id selected>$r->account_group</option>";
					}
					else
					{	
						echo "<option value=$r->account_group_id>$r->account_group</option>";
					}	
				}
				
			?>
                </select>
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pages 
                <input name="page_from" type="text" id="page_from" size="3" maxlength="3">
                To
<input name="page_to" type="text" id="page_to" size="3" maxlength="3">
                </font></td>
              <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <input name="p1" type="submit" id="p1" value="Go">
                <input name="p1" type="submit" id="p1" value="Print Draft">
                </font></td>
            </tr>
            <tr bgcolor="#A4B9DB"> 
              <td height="24" colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
                Preview</strong> &nbsp; </font><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
                <?
		if (file_exists($reportfile))
		{
			echo "| <a href=$reportfile>Download</a>";
		}
		?>
                </font></td>
            </tr>
            <tr align="left"> 
              <td height="24" colspan="3"><textarea name="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
            </tr>
          </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
</form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
