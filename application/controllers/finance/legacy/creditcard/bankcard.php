<script>
function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL bankcard Record?"))
		{
			document.f1.action="?p=../bankcard&p1=CancelConfirm"
		}	
		else
		{
			document.f1.action="?p=../bankcard&p1=Cancel"
		}
	}
	else
	{
		document.f1.action="?p=bankcard&p1="+ul.id;
	}	
}
function switchPix(o)
{
	var folder = "graphics/";
	var n = o.name;
	var obj = new Array('patient_data','address','admission','bankcard','diagnosis');

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
if (!session_is_registered('abankcard'))
{
	session_register('abankcard');
	$abankcard = null;
	$abankcard = array();
}
$fields = array('bankcard','name','address','telefax', 'bankcard_type_id', 'status',
			'telno','remarks','enable');


if (!in_array($p1,array(null,'showaudit','Load')))
{
	for ($c=0;$c<count($fields);$c++)
	{
		$abankcard[$fields[$c]] = $_REQUEST[$fields[$c]];
	}
}	

if ($p1 == 'New')
{
	$abankcard = null;
	$abankcard = array();
}
elseif ($p1=='showaudit')
{
	$abankcard['showaudit'] =1;
}
elseif ($id!='' && $p1== 'Load')
{
	$abankcard = null;
	$abankcard = array();
	$q = "select * from bankcard where bankcard_id = '$id'";
	$r = fetch_assoc($q);
	$abankcard = $r;
}		

elseif ($p1 == 'Save' && $abankcard['name']!='')
{
	if ($abankcard['bankcard_id'] == '')
	{
		$audit = 'Encoded by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$abankcard['audit'] = $audit;
		$q = "insert into bankcard (bankcard,name,bankcard_type_id, address, telefax, telno, remarks, status)
				values ('".$abankcard['bankcard']."','".$abankcard['name']."','".$abankcard['bankcard_type_id']."','".$abankcard['address']."',
				'".$abankcard['telefax']."','".$abankcard['telno']."','".$abankcard['remarks']."','".$abankcard['status']."')";
	}
	else
	{
		$audit = $abankcard['audit'].'Updated by '.$ADMIN['name'].' on '.date('m/d/Y g:ia').'; ';
		$abankcard['audit'] = $audit;
		$q = "update bankcard set audit='$audit'  ";
	
		for($c=0;$c<count($fields);$c++)
		{
			$q .= ", ".$fields[$c] ."='".$abankcard[$fields[$c]]."'";
		}
		$q .= " where bankcard_id='".$abankcard['bankcard_id']."'";
	}

	$qr = pg_query($q) or message("Error saving bankcard data...".pg_errormessage().$q);
	if ($qr)
	{
		if ($abankcard['bankcard_id'] == '')
		{
			$q = "select currval('bankcard_bankcard_id_seq')" ;
			$r = fetch_object($q);
			$abankcard['bankcard_id'] = $r->currval;
		}
		message("bankcard Data Saved...");
	}
}
?>
<form name="form1" method="post" action="">
  <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1">
    <tr> 
      <td>Search 
        <input name="search" type="text" id="search" value="<?= $search;?>">
        <?=lookUpAssoc('searchby',array('Card No.'=>'bankcard','Name of Holder'=>'name','Bank Card'=>'bankcard_type'),$searchby);?>
        <input name="button" type="button" id="p1" value="Go" onClick="window.location='?p=bankcard.browse&p1=Go'+'&search='+search.value+'&searchby='+searchby.value" accesskey="G">
        <input type="button" name="Submit2" value="Add New" onClick="window.location='?p=bankcard&p1=New'" accesskey="N">
        <input type="button" name="Submit22" value="Browse" onClick="window.location='?p=bankcard.browse'" accesskey="B"> 
        <input type="button" name="Submit23" value="Close" onClick="window.location='?p='" accesskey="C">
        <hr color="#CC0000"></td>
    </tr>
  </table>
</form>
<form action="" method="post" name="f1" id="f1">
  <table width="80%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
    <tr bgcolor="#CCCCCC"> 
      <td colspan="2"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <img src="../graphics/post_discussion.gif" width="16" height="16"> Bankcard 
        Data Entry</font></strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td width="19%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bankcard 
        No.</font></td>
      <td width="81%"><input name="bankcard" type="text" id="bankcard" value="<?= $abankcard['bankcard'];?>" size="15" maxlength="15">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bankcard Type <?= lookUpTable2('bankcard_type_id','bankcard_type','bankcard_type_id','bankcard_type',$abankcard['bankcard_type_id']);?>
        Status 
        <?= lookUpAssoc('status',array('Active'=>'A','In-Active'=>'I','Black List'=>'B'),$abankcard['status']);?>
        </font> </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name 
        of Holder</font></td>
      <td><input name="name" type="text" id="bankcard" value="<?= $abankcard['name'];?>" size="40" maxlength="40"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
      <td><textarea name="address" cols="40" rows="2" id="address"><?= $abankcard['address'];?></textarea></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone/Fax</font></td>
      <td><input name="telefax" type="text" id="telefax" value="<?= $abankcard['telefax'];?>" size="15" maxlength="15"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone</font></td>
      <td><input name="telno" type="text" id="telno" value="<?= $abankcard['telno'];?>" size="15" maxlength="15"></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
      <td><textarea name="remarks" cols="40" id="remarks"><?= $abankcard['remarks'];?></textarea></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Audit</font></td>
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?
	  if ($abankcard['showaudit']==1)
	  {
	  	echo $abankcard['audit'];
	  }
	  else
	  {
	  	echo "<a href='?p=bankcard&p1=showaudit'>Show</a>";
	  }
	  ?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Enabled</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?=lookUpAssoc('enable',array('Yes'=>'t','No'=>'f'),$abankcard['enable']);?>
        </font></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="2" valign="top" nowrap><table width="1%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
          <tr bgcolor="#FFFFFF"> 
            <td nowrap width="3%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input type="image" src="../graphics/save.jpg" alt="Save This Claim Form" width="57" height="15" id="Save" onClick="vSubmit(this)" name="Save" accesskey="S">
              </strong></font></td>
            <td nowrap width="46%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong> 
              <input name="Print" type="image" id="Print" onClick="vSubmit(this)" src="../graphics/print.jpg" alt="Print This Claim Form" width="65" height="18" accesskey="P">
              </strong></font></td>
            <!--<td nowrap width="26%"> <input name="Cancel" id="Cancel" type="image" src="graphics/cancel.jpg" onClick="vSubmit(this)" alt="Cancel This Entry" width="77" height="20"></td>-->
            <td nowrap width="25%"> <input type='image' name="New" id="New" onClick="vSubmit(this)"  src="../graphics/new.jpg" alt="New Claim Form" width="63" height="20" accesskey="N"> 
            </td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
