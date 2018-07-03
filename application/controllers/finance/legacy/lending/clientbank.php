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
$this->View->setPageTitle("Manage Client Banks");

$href = '?p=clientbank';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("clientbank","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aclientbank'))
{
	session_register('aclientbank');
	$aclientbank=array();
}


if ($p1=="Save Checked" && !chkRights2("clientbank","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($clientbank[$c]!='')
		{
			if ($withdraw_day[$c] == '') $withdraw_day[$c] = 0;
			if ($clientbank_id[$c] == '')
			{
				$q = "insert into clientbank (enable, clientbank, clientbank_address, telno, withdraw_day)
					values
						('".$enable[$c]."','".$clientbank[$c]."','".$clientbank_address[$c]."','".$telno[$c]."','".$withdraw_day[$c]."')";
				$qr = pg_query($q) or message (pg_errormessage());
				
				$q = "select currval('clientbank_clientbank_id_seq')" ;
				$r = fetch_object($q);
				$clientbank_id[$c] = $r->currval;

			}
			else
			{
				pg_exec("update clientbank set
						enable='".$enable[$c]."',
						clientbank='".$clientbank[$c]."',
						telno='".$telno[$c]."',
						withdraw_day='".$withdraw_day[$c]."',
						clientbank_address = '".$clientbank_address[$c]."'
					where
						clientbank_id='".$clientbank_id[$c]."'") or message (pg_errormessage());
			}	
		}
		$ctr++;
	} 
	$aclientbank['status']='SAVED';
}
?>
<form name="form1" method="post" action="">
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
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
  <table class="table" align="center">
    <tr bgcolor="#E9E9E9"> 
      <td width="8%"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">#</font></b></td>
      <td width="20%" nowrap><a href="<?=$href.'&sort=clientbank&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Clientbank</font></b></a></td>
      <td nowrap><a href="<?=$href.'&sort=clientbank&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Branch 
        Address </font></b></a></td>
      <td align="center" nowrap><b><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">W. 
        Day</font></b></td>
      <td align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Tel 
        No. </font></b></td>
      <td nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        </font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Enabled</font></b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$aclientbank['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap> <font size=1> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="clientbank_id[]" type="hidden" id="clientbank_id[]" size="5">
        </font> </td>
      <td> <input type="text" name="clientbank[]" size="30"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td ><input name="clientbank_address[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="35" maxlength="40"> 
      </td>
      <td ><input name="withdraw_day[]" type="text" id="<?='k'.$c;?>"   onChange="vChk(this)" size="5" style="text-align:right"></td>
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="telno" type="text" size="15"  id="<?='k'.$c;?>"   onChange="vChk(this)">
        </font></td>
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
		$aclientbank['status']='LIST';
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
	$uSearch = strtoupper($xSearch);
	$q = "select * from clientbank ";
	if ($xSearch != '')
	{
		$q .= " where upper(clientbank) like '$uSearch%' ";
	}
	
	if ($sort == '' || $sort=='clientbank')
	{
		$sort = 'clientbank, clientbank_address';
	}
	$q .= " order by $sort offset $start limit 10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap><font size=1> 
        <input type="hidden" name="clientbank_id[]" size="5" value="<?= $r->clientbank_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
        </font> </td>
      <td> <input name="clientbank[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->clientbank;?>" size="30"> 
      </td>
      <td><input name="clientbank_address[]" type="text" id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->clientbank_address;?>" size="35" maxlength="40"> 
      </td>
      <td><input name="withdraw_day[]" type="text" id="<?='k'.$ctr;?>"   value="<?=$r->withdraw_day;?>" onChange="vChk(this)"  size="5" style="text-align:right"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="telno" type="text"  id="<?='k'.$ctr;?>"   value="<?= $r->telno;?>" size="15">
        </font></td>
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
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=clientbank&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=clientbank&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
