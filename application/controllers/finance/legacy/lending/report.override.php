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
if (!chkRights2('financereport','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this Area [ Finance Report ]...");
	exit;
}


if ($p1=='Go' || $p1=='Print Draft' || $p1 == 'Print')
{
	$mdate_from = mdy2ymd($date_from);
	$mdate_to = mdy2ymd($date_to);
	
	$xfrom_date  = substr($mdate_from,0,7); //substr($from_date,0,3).substr($from_date,6,4);
	$xto_date  = substr($mdate_to,0,7); //substr($to_date,0,3).substr($to_date,6,4);
	
	$q = "select 
				account.account,
				releasing.account_id,
				releasing.releasing_id,
				releasing.loan_type_id,
				releasing.status,
				releasing.term,
				releasing.date_child21,
				releasing.nchangebank,
				releasing.age,
				releasing.mclass,
				releasing.npension,
				releasing.date,
				releasing.principal
				
			from 
				releasing,
				account
			where
				account.account_id=releasing.account_id and
				date>='$mdate_from' and
				date<='$mdate_to'";
				
			if ($branch_id != '')
			{
				$q .= " and branch_id='$branch_id'";
				$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
			} else $branch='All';
				
	$q .= " order by releasing.date, account";


	$qr = @pg_query($q) or message(pg_errormessage());
	if ($p1 == 'Print Draft')
	{
		$header = "<small3>";
	}
	else
	{
		$header = '';
	}
	$header .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$header .= center('EXCEPTION LISTING FOR LOAN OVERRIDES MADE',80)."\n";
	$header .= center('Report Date '.$date_from.' To '.$date_to,80)."\n";
	$header .= center('Branch : '.$branch,80)."\n\n";
	
	if ($p1 == 'Print Draft') $header .= "<bold>";
	$header .= space(5)."Printed: ".date('m/d/Y g:ia')." ".$ADMIN['username']."\n";
	if ($p1 == 'Print Draft') $header .= "</bold>";
	$header .= space(5)."---- ---------------------------------- --------- ---------- ---------------- ------- ------- ------------ ---------------------------\n";
	$header .= space(5)."  #   NAME OF ACCOUNT                   Reference   Date       Loan Type      MTerm    Term   Principal   Remarks   \n";
	$header .= space(5)."---- ---------------------------------- --------- ---------- ---------------- ------- ------- ------------ ---------------------------\n";

	$details = $details1 = '';
	$total_amount = $subtotal  = $total_balance = $total_amount_due = 0;
	$sub_due;
	$maccount_id = '';
	$lc=6;
	$ctr=0;
	while ($r = @pg_fetch_object($qr))
	{
		$remarks= '';
		if ($r->age < 70)
		{
			$max_term = 36;
		}
		elseif ($r->age < 74)
		{
			$max_term = 24;
		}
		elseif ($age<78)
		{
			$max_term = 12;
		}
		else
		{
			$max_term = 6;
		}		
		$remarks .= 'Age: '.$r->age.'; ';

		if ($r->mclass == 2)
		{
			$remarks .= 'Beneficiary;';

			if ($date_child21=='')
			{
				$max_term = 36;
			}
			else
			{
				$max_term = (21-$child21age)*12;
				$remarks .= 'w/Child21 on '.$r->date_child21.';';
			}
			if ($max_term >36) $max_term = 36;
			if ($max_term <= 0) $max_term = 36;
		}
		elseif ($r->mclass == 3)
		{
			$max_term = 6;
			$remarks .= "w/Pemanent Disability; ";
		}
		elseif ($r->mclass == 4)
		{
			//-- temporary disability
			$remarks .= 'Termporary Disability;';
			if ($npension != '' && $npension<6)
			{
				$max_term = $npension;
				$remarks .= 'Pensions='.$r->npension.'; ';
			}	
			else
			{
				$max_term = 6;
			}
		}
		elseif ($r->mclass == 5)
		{
			$remarks .= 'Guardian; ';
			$max_term=6;
		}

		if ($r->nchangebank > '0') 
		{
			$remarks .= 'ChangedBanks ';
			if ($r->nchangebank>2)
			{
				$max_term = ' 0';
				$remarks .= $r->nchangebank.'x;';
			}
			else
			{
				$remarks .= '<2x;';
			}
		}	

		if ($r->term <= $max_term)  continue;
		
		$cc++;
		$details.=  space(4).adjustRight($cc,4).'.'.
					adjustSize($r->account,35).' '.
					str_pad($r->releasing_id,8,'0',str_pad_left).'  '.
					adjustSize(ymd2mdy($r->date),10).'  '.
					adjustSize(lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id),15).' '.
					adjustRight($max_term,7).' '.
					adjustRight($r->term,7).' '.
					adjustRight(number_format($r->principal,2),12).' '.
					adjustSize($remarks,20)."\n";
					
		$total_amount += $r->principal;
		$lc++;			
		if ($lc>55 && $p1 == 'Print Draft')
		{
			$details .= "\n";
			$details1 .= $header.$details;
			$details .= "<eject>";
			doPrint($header.$details);
			$details = '';
			$lc=6;
		}			
	}
//	$details .=	adjustRight(number_format($sub_due,2),13)."\n";

	$details .= space(5)."---- ---------------------------------- ---------- -------- --------------\n";
	$details .= space(39).adjustSize('TOTAL AMOUNT ->',24).'  '.
				adjustRight(number_format($total_amount,2),13)."\n";
	$details .= space(5)."---- ---------------------------------- ---------- -------- --------------\n";
	
	
	$details1 .= $header.$details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($header.$details);
	}	
}	

?>
<style type="text/css">
<!--
.style1 {font-family: Arial, Helvetica, sans-serif}
-->
</style>

<form action="" method="post" name="f1" id="f1" style="margin:10px">
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" background="../graphics/table0_horizontal.PNG">
      <tr> 
        <td width="1%"><img src="../graphics/table0_upper_left.PNG" width="8" height="30"></td>
        
      <td width="49%" align="left" background="../graphics/table0_horizontal.PNG"><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Listing 
        of Overrides Made</b></font></td>
        <td width="50%" align="right" background="../graphics/table0_horizontal.PNG"><img src="../graphics/table_close.PNG" width="21" height="21" onClick="window.location='?p='"></td>
        <td width="0%" align="right"><img src="../graphics/table0_upper_right.PNG" width="8" height="30"></td>
      </tr>
      <tr bgcolor="#A4B9DB"> 
        <td colspan="4"> <table width="99%" height="99%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
          <!--DWLayoutTable-->
          <tr> 
            <td width="81" height="24" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
              From </font></td>
            <td width="90" valign="top" nowrap> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date_from" type="text" id="date_from" value="<?= ($date_from);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_from, 'mm/dd/yyyy')"> 
              </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
            </font></td>
            <td width="58" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Branch </font></td>
            <td width="555" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
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
              </select>
            </font></td>
          </tr>
          <tr> 
            <td height="22" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date 
              To </font></td>
            <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
              <input name="date_to" type="text" id="date_to" value="<?= ($date_to);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
              <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_to, 'mm/dd/yyyy')"> 
              </font></td>
            <td valign="top" nowrap><!--DWLayoutEmptyCell-->&nbsp;</td>
            <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
              <input name="p1" type="submit" id="p1" value="Go" />
              <input name="p1" type="submit" id="p1" value="Print Draft" />
            </font></td>
          </tr>
          <tr bgcolor="#A4B9DB"> 
            <td height="24" colspan="4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Report 
              Preview</strong> &nbsp; </font></td>
          </tr>
          <tr align="left"> 
            <td height="24" colspan="4"><textarea name="textarea" cols="110" rows="20" readonly wrap="OFF"><?= $details1;?></textarea></td>
          </tr>
        </table></td>
      </tr>
      <tr> 
        <td colspan="4" height="3"  background="../graphics/table0_vertical.PNG"></td>
      </tr>
    </table>
	<div align="center">
    <input name="p1" type="submit" id="p1" value="Print Draft" >
    <input name="p12" type="button" id="p12" value="Print"  onClick="printIframe(print_area)" >
  </div>
 </form>

<iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>
