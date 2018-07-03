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
<div id="popLayer" style="position:absolute; width:800px; height:350px; z-index:1; top: 38%; left: 5%; background-color: #0066CC; layer-background-color: #0066CC; border: 1px none #000000;">
  <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF">
    <tr> 
      <td height="23" background="../graphics/table_horizontal.PNG" ><font color="#FFFFCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Select 
        Account</strong></font></td>
      <td align="right" background="../graphics/table_horizontal.PNG" ><img src="../graphics/table_close.PNG" width="21" height="21" onClick="document.getElementById('popLayer').style.visibility='hidden'"></td>
    </tr>
    <tr> 
      <td colspan="2"><div id="Layer2" style="position:relative; width:100%; height:320px; z-index:2; overflow: auto;"> 
          <table width="99%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
            <tr bgcolor="#E1E7F1"> 
              <td width="3%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
              <td width="25%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Select 
                Account </font></strong></td>
              <td width="17%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></strong></td>
              <td width="13%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Released</font></strong></td>
              <td width="10%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Obligation</font></strong></td>
              <td width="11%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Ammort</font></strong></td>
              <td width="10%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></strong></td>
              <td width="11%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Due</font></strong></td>
            </tr>
            <?
		$iPayD['account'] = $account;
		$iPayD['account_id'] = '';
		
		$q = "select 
					account.account_id, 
					account.account,
					account.branch_id,
					account.account_status,
					account.withdraw_day
				from
					account
				where 
					(account ilike '$account%'  or account_code ilike '$account%')";

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
			}
					
		$q .= "	order by account";
		
		$qr = pg_query($q) or message(pg_errormessage());
		$ctr=0;
		$maccount_id=0;
		while ($r = pg_fetch_object($qr))
		{
			if ($r->account_status=='L' and $ADMIN[usergroup] != 'A' and $ADMIN[usergroup] != 'Y') continue;
		
				$q = "select 
								releasing.releasing_id,
								releasing.gross,
								releasing.loan_type_id,
								releasing.date as releasing_date,
								releasing.ammort,
								releasing.balance,
								releasing.term,
								releasing.mode,
								releasing.withdraw_day
						from
							releasing
						where
							releasing.balance>0  and
							releasing.status!='C' and
							releasing.account_id = '$r->account_id'";
							
				$qqr = @pg_query($q) or message(pg_errormessage());

				$temp = null;
				$temp = array();
				while ($rr = @pg_fetch_assoc($qqr))
				{
					if ($rr[mode]=='S') 
					{
//						$rr[ammort] = $rr[ammort]/2;
						$rr[term] = $rr[term]*2;
					}	
					$rr[branch_id] = $r->branch_id;
					$temp[] = $rr;
				}	

				$account = $r->account;
				$ctr++;
				$cc= $ctr.'.';
				
				if (count($temp) == '0')
				{
		?>
            <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" onClick="window.location='?p=payment.entry&p1=selectAccountId&id=<?= $r->account_id;?>'"> 
              <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=$cc;?>
                </font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="?p=payment.entry&p1=selectAccountId&id=<?= $r->account_id;?>"> 
                <?= $account;?>
                </a></font></td>
              <td colspan="6"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="?p=payment.entry&p1=selectAccountId&id=<?= $r->account_id;?>"> 
                No loan or Other Branch ( 
                <?= lookUpTableReturnValue('x','branch','branch_id', 'branch', $r->branch_id);?>
                ) </a></font></td>
            </tr>
            <?
				}
				else
				{
					$ccc = 0;
					foreach ($temp as $temp1)
					{
						$rid = $temp1['releasing_id'];
						$ccc++;
						if ($ccc == '1')
						{
							$xc = $cc;
							$account = $r->account;
						}
						else
						{
							$cc = '';
							$account = '';
						}
						if ($temp1[withdraw_day] == 0)
							$temp1['withdraw_day'] = $r->withdraw_day;
						if ($ddate != '' && $ddate!='//' && $ddate != '--') $d = mdy2ymd($ddate);
						elseif ($date != '' && $date!='//' && $date != '--') $d = mdy2ymd($date);
						else $d = '';

						$aDue = amountDue($temp1, $d);
						$href='';
						if ($ADMIN['province_id'] !=0)
						{
							$province_id=lookUpTableReturnValue('x','branch','branch_id','province', $temp1[branch_id]);
							if ($ADMIN['province_id']==$province_id) $href="?p=payment.entry&p1=selectAccountId&id=$r->account_id";
							else $href=''; 
						} 
						else $href="?p=payment.entry&p1=selectAccountId&id=$r->account_id";
				?>
            <tr onMouseOver="bgColor='#FFFFDD'" onMouseOut="bgColor='#FFFFFF'" bgcolor="#FFFFFF" onClick="window.location='?p=payment.entry&p1=selectReleaseId&id=<?= $rid;?>&aid=<?= $r->account_id;?>'"> 
              <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=$cc;?>
                </font></td>
			<?	
			  if ($href=='')
			  {
			?>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= $province_id.'  '.$account.' ('.lookUpTableReturnValue('x','branch','branch_id', 'branch_code', $r->branch_id).')';?>
                </font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$temp1['loan_type_id']);?>
                </font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?= ymd2mdy($temp1['releasing_date']);?>
                </font></td>
			<?  
			  }
			  else
			  {
			?>  	
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="<?=$href;?>"> 
                <?= $account.' ('.lookUpTableReturnValue('x','branch','branch_id', 'branch_code', $r->branch_id).')';?>
                </a> </font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <!--             <a href="?p=payment.entry&p1=selectReleaseId&id=<?= $rid;?>"> -->
                <a href="<?=$href;?>"> 
                <?= lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$temp1['loan_type_id']);?>
                </a> </font></td>
              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <a href="<?=$href;?>"> 
                <?= ymd2mdy($temp1['releasing_date']);?>
                </a> </font></td>
			<?
			  }
			?>  	
              <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=number_format($temp1['gross'],2);?>
                </font></td>
              <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=number_format($temp1['ammort'],2);?>
                </font></td>
              <td align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=number_format($temp1['balance'],2);?>
                </font></td>
              <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                <?=number_format($aDue['amount_due'],2);?>
                </font></td>
            </tr>
            <?
				} //end foreach
			}
		} //end while
		?>
          </table>
        </div></td>
    </tr>
  </table>
 </div>
