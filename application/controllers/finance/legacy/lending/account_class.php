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
$this->View->setPageTitle("Manage Account Classifications");

$href = '?p=account_class';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("account_class","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aaccount_class'))
{
	session_register('aaccount_class');
	$aaccount_class=array();
}


if ($p1=="Save Checked" && !chkRights2("account_class","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($account_class[$c]!='')
		{
			if ($account_class_id[$c] == '')
			{
				$q = "insert into account_class (enable, account_class, account_class_code)
					values
						('".$enable[$c]."','".$account_class[$c]."','".$account_class_code[$c]."')";
				$qr = @pg_exec($q) or message (pg_errormessage());
			}
			else
			{
				@pg_exec("update account_class set
						enable='".$enable[$c]."',
						account_class='".$account_class[$c]."',
						account_class_code = '".$account_class_code[$c]."',
						account_class_id = '".$account_class_id[$c]."'
					where
						account_class_id='".$account_class_id[$c]."'") or message (pg_errormessage());
			}			
		}
		$ctr++;
	} 
	$aaccount_class['status']='SAVED';
}
?>
<form name="form1" method="post" action="">
  <table class="table" >
    <tr> 
      <td nowrap>Find 
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
        
        <hr color="#CC3300">
        &nbsp; </td>
    </tr>
  </table>
  <table class="table" align="center">
    <tr bgcolor="#E9E9E9"> 
      <td width="8%"><b>#</b></td>
      <td nowrap><a href="<?=$href.'&sort=account_class_id&start=$start&xSearch=$xSearch';?>"><b>Account_Group 
        Classification</b></a></td>
      <td nowrap><b> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        Enabled</b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$aaccount_class['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap>
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="account_class_id[]" type="hidden" id="account_class_id[]" size="5">
         </td>
      <td> <input type="text" name="account_class[]" size="40"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
        &nbsp;  </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="3" height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan="3" height="26">Saved 
        Account Group Classifications</td>
    </tr>
    <?
	} //if insert
	else
	{
		$aaccount_class['status']='LIST';
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
	$q = "select * from account_class ";
	if ($xSearch != '')
	{
		$q .= " where account_class like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='account_class')
	{
		$sort = 'account_class';
	}
	$q .= " order by $sort offset $start limit 10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right>
        <input type="hidden" name="account_class_id[]" size="5" value="<?= $r->account_class_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td> <input name="account_class[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->account_class;?>" size="40"> 
        &nbsp;  </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="3"> 
        <input type="submit" name="p1" value="Save Checked">
         </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=account_class&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=account_class&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
