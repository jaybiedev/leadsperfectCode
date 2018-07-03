<?
if (!chkRights2('penalty','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aPenalty'))
{
	session_register('aPenalty');
	$aPenalty = null;
	$aPenalty = array();
}
if ($date == '') $date = date('Y-m-d');
if ($month == '') $month = date('m')-1;
if ($withinloan == '') $withinloan =10;
if ($beyondloan == '') $beyondloan = 20;
if ($year == '')
{
	if (date('m') <2)
	{
		$year = date('Y')-1;
	}
	else
	{
		$year = date('Y');
	}
}
?> 
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

<br>
<form action="" method="post" name="f1" id="f1" style="margin:0">
  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr> 
      <td height="23" colspan="5" background="../graphics/table0_horizontal.PNG"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"  color="#EFEFEF"> 
        <strong><img src="../graphics/bluelist.gif" width="16" height="17"> Process 
        Penalty Computation</strong></font></td>
    </tr>
    <tr> 
      <td width="33%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Loan Release No.</font> <font size="1"><br>
        (Leave Blank For All Accounts)</font> <input name="rid" type="text" id="rid" value="<?= $rid;?>">      </td>
      <td width="5%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Class</font><br> 
        <select name="account_class_id">
          <?
		$q = "select distinct(account_class_id) as account_class_id from account_group where account_class_id>'0' order by account_class_id";
		$qr = @pg_query($q);
		while ($r = @pg_fetch_object($qr))
		{
			if ($r->account_class_id == '1')
			{
				$account_class = 'SSS';
			}
			elseif ($r->account_class_id == '2')
			{
				$account_class = 'LGU';
			}
			elseif ($r->account_class_id == '3')
			{
				$account_class = 'Schools';
			}
			else
			{
				$account_class = 'Others';
			}
			
			if  ($r->account_class_id == $account_class_id)
			{
				echo "<option value='$r->account_class_id' selected>$account_class</option>";
			}
			else
			{
				echo "<option value='$r->account_class_id'>$account_class</option>";
			}
		}
		?>
      </select> </td>
      <td width="13%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        %w/n Term<br>
        <input name="withinloan" type="text" id="withinloan" value="<?= $withinloan;?>" size="5" maxlength="5" style="text-align:right">
      </font></td>
      <td width="20%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        %beyond Term<br>
        <input name="beyondloan" type="text" id="beyondloan" value="<?= $beyondloan;?>" size="5" maxlength="5"  style="text-align:right">
      </font></td>
      <td width="29%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Branch<br>
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
          <option value='99'>All Branches</option>
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
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Applicable 
        Month/Yr<br>
        <?= lookUpMonth('month',$month);?>
        <input name="year" type="text" id="year" value="<?= $year;?>" size="5" maxlength="5">
        </font></td>
      <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CurrentDate<br>
        <input name="date" type="text" id="date" value="<?= ymd2mdy($date);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date, 'mm/dd/yyyy')"> 
        <input name="p1" type="button" id="p1" value="Proceed With Posting" onCLick="wait('Please wait. Processing data...');xajax_computepenalty(xajax.getFormValues('f1'));">
        <input type="button" name="Button" value="Browse" onClick="window.location='?p=penalty.browse'";>
      </font></td>
		<td><br /><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
		  <input type="button" name="Button2" value="Close" onclick="window.location='?p='"; />
		</font></td>
    </tr>
    <tr> 
      <td height="20" colspan="5"><hr color="red" height="1"></td>
    </tr>
    <tr valign="top" > 
      <td height="300px" colspan="5"><div id="grid" name="grid" style="position:virtual; width:100%; height:100%; z-index:1; overflow: scroll;"></div></td>
    </tr>
    <tr> 
      <td colspan="3"><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <img src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" name="Save"  onCLick="wait('Please wait. Posting Penalties To Ledger..');xajax_savePenalty(xajax.getFormValues('f1'));"> 
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <img src="../graphics/print.jpg" alt="Print This Form"  name="Print" id="Print" onClick="window.location='?p=report.penalty'"> 
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
          <td nowrap width="25%"> <input type='image' name="Cancel" id="Cancel" onClick="window.location='?p'"  src="../graphics/cancel.jpg" alt="Cancel Form" width="77" height="20">          </tr>
        </table></td>
      <td><input name="p1" type="button" id="p1" value="Delete Checked"></td>
    </tr>
  </table>

</form>
