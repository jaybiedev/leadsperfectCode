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
$this->View->setPageTitle("Account Groups");
$href = '?p=account_group';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("account_group","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aaccount_group'))
{
	session_register('aaccount_group');
	$aaccount_group=array();
}


if ($p1=="Save Checked" && !chkRights2("account_group","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($account_group[$c]!='')
		{
			if ($account_class_id[$c] == '')
			{
				$account_class_id[$c] = 0;
				message('Please Specify Account Classification for '.$account_group[$c]);
			}
			
			if ($account_group_id[$c] == '')
			{
				$q = "insert into account_group (enable, account_group, account_group_code, account_class_id)
					values
						('".$enable[$c]."','".$account_group[$c]."','".$account_group_code[$c]."','".$account_class_id[$c]."')";
				$qr = @pg_exec($q) or message (pg_errormessage());
				if (pg_affected_rows($qr) <= 0 && $qr)
				{
					message('Record '.$account_group[$c].' Not Saved...');
				}
			}
			else
			{
				@pg_exec("update account_group set
						enable='".$enable[$c]."',
						account_group='".$account_group[$c]."',
						account_group_code = '".$account_group_code[$c]."',
						account_class_id = '".$account_class_id[$c]."'
					where
						account_group_id='".$account_group_id[$c]."'") or message (pg_errormessage());
			}			
		}
		$ctr++;
	} 
	$aaccount_group['status']='SAVED';
}
?>
<form name="form1" method="post" action="" style="margin:10px">
  <table class="table" align="center">
    <tr> 
      <td nowrap>Find 
        <input type="text" name="xSearch" value="<?= $xSearch;?>">
        <input name="p1" type="submit" id="p1" value="Go" class="btn btn-primary btn-sm">
        Insert 
        <select name="insertcount">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
        </select>
        <input name="p1" type="submit" id="p1" value="Insert" class="btn btn-secondary btn-sm">
        <input name="p1" type="submit" id="p1" value="List" class="btn btn-secondary btn-sm">
        <input name="p1" type="button" id="p1" onClick="window.location='?p='" value="Close"  class="btn btn-secondary btn-sm">
        
        <hr color="#CC3300"></td>
    </tr>
  </table>
  <table class="table" bgcolor="#EFEFEF" align="center">
    <tr bgcolor="#E9E9E9"> 
      <td width="8%"><b>#</b></td>
      <td width="20%" nowrap><a href="<?=$href.'&sort=account_class_id&start=$start&xSearch=$xSearch';?>"><b>Account_Group</b></a></td>
      <td nowrap><a href="<?=$href.'&sort=account_group&start=$start&xSearch=$xSearch';?>"><b>Classification</b></a></td>
      <td nowrap><b> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        Enabled</b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$aaccount_group['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="account_group_id[]" type="hidden" id="account_group_id[]" size="5">
        </td>
      <td> <input type="text" name="account_group[]" size="40"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td >
        <?
		$q = "select * from account_class where enable order by account_class";
		$qr = pg_query($q) or message(pg_errormessage());
		echo "<select name='account_class_id[]'>";
		echo "<option value=''>Select Classification</option>";
		while ($r= pg_fetch_object($qr))
		{
			if ($account_class_id == $r->account_class_id)
			{
				echo "<option value=$r->account_class_id>$r->account_class</option>";
			}
			else
			{
				echo "<option value=$r->account_class_id>$r->account_class</option>";
			}

		}
		echo "</select>";
	   ?>
         </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="4" height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan="4" height="26">Saved 
        Categories</td>
    </tr>
    <?
	} //if insert
	else
	{
		$aaccount_group['status']='LIST';
		$c=0;
	}
	if ($p1=='List') 
	{
		$start=0;
		$xSearch='';
	}	
	if ($start == '' or $p1 == 'Go') $start=0;
	if ($p1=='Next') $start = $start + 10;
	if ($p1=='Previous') $start = $start - 10;
	if ($start < 0) $start=0;	
	$q = "select * from account_group ";
	if ($xSearch != '')
	{
		$q .= " where account_group ilike '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='account_group')
	{
		$sort = 'account_group';
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
        <input type="hidden" name="account_group_id[]" size="5" value="<?= $r->account_group_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td> <input name="account_group[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->account_group;?>" size="40"> 
      </td>
      <td>
        <?
		$q = "select * from account_class where enable order by account_class";
		$qqr = pg_query($q) or message(pg_errormessage());
		echo "<select name='account_class_id[]'  id='h$ctr'  onChange='vChk(this)'>";
		echo "<option value=''>Select Classification</option>";
		while ($rr= pg_fetch_object($qqr))
		{
			if ($rr->account_class_id == $r->account_class_id)
			{
				echo "<option value=$rr->account_class_id selected>$rr->account_class</option>";
			}
			else
			{
				echo "<option value=$rr->account_class_id>$rr->account_class</option>";
			}

		}
		echo "</select>";
	   ?>
         </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="4"> 
        <input type="submit" name="p1" value="Save Checked" class="btn btn-primary">
         </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=account_group&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=account_group&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
