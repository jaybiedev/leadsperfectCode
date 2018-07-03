<script>
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f1.action="?p=account&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=account&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=account&p1="+ul.id;
	}	
}
function switchPix(o)
{
	var folder = "../../graphics/";
	var n = o.name;
	var obj = new Array('patient_data','address','admission','account','diagnosis');

	for (c = 0;c<obj.length;c++)
	{
		eval("this.f1."+obj[c]).src=folder+obj[c]+"_lo.jpg";
		eval("this."+obj[c]+".style").visibility="hidden";
	}

	o.src=folder+o.name+"_hi.jpg"
	eval("this."+n+".style").visibility = "visible"
	
}
</script>
<?
if (!session_is_registered('aaccount'))
{
	session_register('aaccount');
	$aaccount = null;
	$aaccount = array();
}
$fields = array('account_code','account','address','telno', 'collection_type_id',
			'account_group_id','account_status','sss',
			'ofc_address','office','ofc_telno','bank_account','salary',
			'comaker1','comaker1_address','comaker2','comaker2_address',
			'remarks','enable');

if (!in_array($p1,array(null,'showaudit','Load')))
{
	for ($c=0;$c<count($fields);$c++)
	{
		$aaccount[$fields[$c]] = $_REQUEST[$fields[$c]];
		if (in_array($fields[$c],array('account_group_id','collection_type_id','salary')) && $aaccount[$fields[$c]]=='')
		{
			$aaccount[$fields[$c]]=0;
		}
	}
}	

if ($p1 == 'New')
{
	$aaccount = null;
	$aaccount = array();
}
elseif ($p1=='Load' && $id=='')
{
	message("No account to edit...");
	exit;
}
elseif ($p1=='Load' && $id!='')
{
	$aaccount = null;
	$aaccount = array();
	$q = "select * from account where account_id = '$id'";
	$r = fetch_assoc($q);
	$aaccount = $r;
}		

elseif ($p1=='showaudit')
{
	$aaccount['showaudit'] =1;
}
elseif ($p1 == 'Save' && $aaccount['account']!='')
{
	$aaccount['salary'] = str_replace(',','',$aaccount['salary']);

	$aaccount['account'] = $aaccount['account'];
	if ($aaccount['account_id'] == '')
	{
		$aaccount['enable']=true;
		$audit = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$aaccount['audit'] = $audit;
		$q = "insert into account 
						(account_code, account, address, ofc_telno, telno, 
						 collection_type_id, account_group_id,
						 account_status, ofc_address, office, 
						 bank_account, salary,sss,
						 comaker1,comaker1_address, 
						 comaker2,comaker2_address,
						 remarks, enable)
					values 
						('".$aaccount['account_code']."','".$aaccount['account']."','".$aaccount['address']."',
						'".$aaccount['ofc_telno']."','".$aaccount['telno']."','".$aaccount['collection_type_id']."',
						'".$aaccount['account_group_id']."',
						'".$aaccount['account_status']."',
						'".$aaccount['ofc_address']."','".$aaccount['office']."',
						'".$aaccount['bank_account']."','".$aaccount['salary']."',
						'".$aaccount['sss']."',
						'".$aaccount['comaker1']."','".$aaccount['comaker1_address']."',
						'".$aaccount['comaker2']."','".$aaccount['comaker2_address']."',
						'".$aaccount['remarks']."','".$aaccount['enable']."')";
	}
	else
	{
		$audit = $aaccount['audit'].'Updated by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$aaccount['audit'] = $audit;
		$q = "update account set audit='$audit'  ";
	
		for($c=0;$c<count($fields);$c++)
		{
			$q .= ", ".$fields[$c] ."='".$aaccount[$fields[$c]]."'";
		}
		$q .= " where account_id='".$aaccount['account_id']."'";
		
	}

	$qr = @pg_query($q) or message("Error saving account data...".pg_errormessage().$q);
	if ($qr)
	{
		if ($aaccount['account_id'] == '')
		{
			$qr = query("select currval('account_account_id_seq'::text)");
			$r = pg_fetch_object($qr);
			$aaccount['account_id'] = $r->currval;
		}
		message("account Data Saved...");
	}
}
?>
<form action="?p=account.browse" method="post" name="f1" id="f1">
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
        <input name="p1" type="button" id="p1" value="Go" onClick="window.location='?p=account.browse&p1=Go&search='+search.value">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=account&p1=New'">
        <input type="button" name="Submit23" value="Browse" onClick="window.location='?p=account.browse'"> 
        <input type="button" name="Submit232" value="Close" onClick="window.location='?p='"> 
        <hr color="#CC0000"></td>
    </tr>
  </table>
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td colspan="4"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <img src="../graphics/post_discussion.gif" width="20" height="20"> Account 
        Data Entry</font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="17%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">SSS 
        No.</font></td>
      <td width="31%" nowrap><input name="sss" type="text" id="sss" value="<?= $aaccount['sss'];?>" size="15" maxlength="15"> 
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
        <?= str_pad($aaccount['account_id'],8,'0',str_pad_left);?>
        </font></td>
      <td width="22%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Civil 
        Status</font></td>
      <td width="30%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpAssoc('account_status',array('Single'=>'S','Married'=>'M','Widow'=>'W'),$aaccount['account_status']);?>
        Account Status 
        <?= lookUpAssoc('account_status',array('Active'=>'A','InActive'=>'I','Legal'=>'L'),$aaccount['account_status']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
        Name</font></td>
      <td><input name="account" type="text" id="account" value="<?= $aaccount['account'];?>" size="40" maxlength="40"></td>
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTable2('account_group_id','account_group','account_group_id','account_group',$aaccount['account_group_id']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td rowspan="2" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Home 
        Address</font></td>
      <td rowspan="2" valign="top"><textarea name="address" cols="30" rows="2" id="address"><?= $aaccount['address'];?></textarea></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone 
        (Home)</font></td>
      <td valign="top"><input name="telno" type="text" id="telno4" value="<?= $aaccount['telno'];?>" size="15" maxlength="15"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></td>
      <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= lookUpTable2('collection_type_id','collection_type','collection_type_id','collection_type',$aaccount['collection_type_id']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Employer/Ofc. 
        </font></td>
      <td><input name="office" type="text" id="office2" value="<?= $aaccount['office'];?>" size="40" maxlength="50"> 
      </td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank Acount</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="bank_account" type="text" id="bank_account" value="<?= $aaccount['bank_account'];?>" size="15" maxlength="15">
        </font></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td rowspan="2" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Office 
        Address</font></td>
      <td rowspan="2"><textarea name="ofc_address" cols="30" rows="2" id="ofc_address2"><?= $aaccount['ofc_address'];?></textarea></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Salary/Pension</font></td>
      <td bgcolor="#FFFFFF"><input name="salary" type="text" id="salary" value="<?= $aaccount['salary'];?>" size="15" maxlength="15"  onBlur="checknumber(this)"></td>
    </tr>
    <tr valign="top" bgcolor="#FFFFFF"> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone 
        (Ofc)</font></td>
      <td><input name="ofc_telno" type="text" id="ofc_telno2" value="<?= $aaccount['ofc_telno'];?>" size="15" maxlength="15"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker1</font></td>
      <td><input name="comaker1" type="text" id="office3" value="<?= $aaccount['comaker1'];?>" size="40" maxlength="50"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
      <td><input name="comaker1_address" type="text" id="office5" value="<?= $aaccount['comaker1_address'];?>" size="40" maxlength="50"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker2</font></td>
      <td><input name="comaker2" type="text" id="office4" value="<?= $aaccount['comaker2'];?>" size="40" maxlength="50"> 
      </td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
      <td><input name="comaker2_address" type="text" id="office6" value="<?= $aaccount['comaker2_address'];?>" size="40" maxlength="50"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
      <td colspan="3"><textarea name="remarks" cols="40" rows="2" id="remarks"><?= $aaccount['remarks'];?></textarea></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Audit</font></td>
      <td colspan="3"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?
	  if ($aaccount['showaudit']==1)
	  {
	  	echo $aaccount['audit'];
	  }
	  else
	  {
	  	echo "<a href='?p=account&p1=showaudit'>Show</a>";
	  }
	  ?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Enabled</font></td>
      <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=lookUpAssoc('enable',array('Yes'=>'true','No'=>'false'),$aaccount['enable']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="4" valign="top" nowrap><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save"  accesskey="S">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input name="Print" type="image" id="Print" onClick="vSubmit(this)" src="../graphics/print.jpg" alt="Print This Claim Form" width="65" height="18"   accesskey="P">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="../../graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20"> 
            </td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
