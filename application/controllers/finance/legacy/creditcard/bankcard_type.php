<script>
function vChk(t)
{
	var id = t.id
	var n = id.substring(1)
	var mid = eval("this.form1.m"+n)
	mid.checked = true
}
</script>
<?
$href = '?p=bankcard_type';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("bankcard_type","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('abankcard_type'))
{
	session_register('abankcard_type');
	$abankcard_type=array();
}


if ($p1=="Save Checked" && !chkRights2("bankcard_type","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($bankcard_type[$c]!='')
		{
		
			if ($init_balance[$c] == '') $init_balance[$c] = 0.00;
			if ($bankcard_type_id[$c] == '')
			{
				$q = "insert into bankcard_type (enable, bankcard_type, percent_bankcharge,service_charge)
					values
						('".$enable[$c]."','".$bankcard_type[$c]."','".$percent_bankcharge[$c]."','".$service_charge[$c]."')";
				$qr = pg_query($q) or message (pg_errormessage());
				
				$q = "select currval('bankcard_type_bankcard_type_id_seq')" ;
				$r = fetch_object($q);
				$bankcard_type_id[$c] = $r->currval;

			}
			else
			{
				pg_exec("update bankcard_type set
						enable='".$enable[$c]."',
						bankcard_type='".$bankcard_type[$c]."',
						percent_bankcharge = '".$percent_bankcharge[$c]."',
						service_charge = '".$service_charge[$c]."'
					where
						bankcard_type_id='".$bankcard_type_id[$c]."'") or message (pg_errormessage());
			}	
		}
		$ctr++;
	} 
	$abankcard_type['status']='SAVED';
}
?>
<form name="form1" method="post" action="">
  <table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td nowrap><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Find 
        <input type="text" name="xSearch" value="<?= $xSearch;?>">
        <input name="p1" type="submit" id="p1" value="Go">
        Insert 
        <select name="insertcount">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
        </select>
        <input name="p1" type="submit" id="p1" value="Insert">
        <input name="p1" type="submit" id="p1" value="List">
        <input name="p1" type="button" id="p1" onClick="window.location='?p='" value="Close">
        </font>
        <hr color="#CC3300">
        <font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; </font></td>
    </tr>
  </table>
  <table width="50%" border="0" cellspacing="1" cellpadding="1" bgcolor="#EFEFEF" align="center">
    <tr bgcolor="#C8D7E6"> 
      <td height="27" colspan="6" background="../graphics/table0_horizontal.PNG"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
        <img src="../graphics/mail.gif" width="16" height="17"> Bankcard_type 
        Setup <a name="top"></a></b></font></td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td width="8%"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">#</font></b></td>
      <td width="20%" nowrap><a href="<?=$href.'&sort=bankcard_type&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Bankcard_type</font></b></a></td>
      <td align="center" nowrap><a href="<?=$href.'&sort=bankcard_type&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Bank<br>
        Percentage</font></b></a></td>
      <td align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Service 
        <br>
        Charge </font></b></td>
      <td align="center" nowrap><b></b></td>
      <td nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        </font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Enabled</font></b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$abankcard_type['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap> <font size=1> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="bankcard_type_id[]" type="hidden" id="bankcard_type_id[]" size="5">
        </font> </td>
      <td> <input type="text" name="bankcard_type[]" size="30"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td ><input name="percent_bankcharge[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="8" maxlength="8"> 
      </td>
      <td ><input name="service_charge[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="8" maxlength="8"></td>
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="6" height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan="6" height="26"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Saved 
        Categories</font></td>
    </tr>
    <?
	} //if insert
	else
	{
		$abankcard_type['status']='LIST';
		$c=0;
	}
	if ($p1=='List') 
	{
		$start=0;
		$xSearch='';
	}	
	if ($start == '') $start=0;
	if ($p1=='Next') $start = $start + 10;
	if ($p1=='Previous') $start = $start - 10;
	if ($start < 0) $start=0;	
	$q = "select * from bankcard_type ";
	if ($xSearch != '')
	{
		$q .= " where bankcard_type like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='bankcard_type')
	{
		$sort = 'bankcard_type';
	}
	$q .= " order by $sort offset $start limit 10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right><font size=1> 
        <input type="hidden" name="bankcard_type_id[]" size="5" value="<?= $r->bankcard_type_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
        </font> </td>
      <td> <input name="bankcard_type[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->bankcard_type;?>" size="30"> 
      </td>
      <td><input name="percent_bankcharge[]" type="text" id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->percent_bankcharge;?>" size="8" maxlength="8"> 
      </td>
      <td><input name="service_charge[]" type="text"  id="<?='k'.$ctr;?>" onChange="vChk(this)" value="<?= $r->service_charge;?>" size="8" maxlength="8"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="6"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="submit" name="p1" value="Save Checked">
        </font> </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=bankcard_type&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=bankcard_type&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
