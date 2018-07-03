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
$this->View->setPageTitle("Manage Provinces");

$href = '?p=province';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("branch","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aprovince'))
{
	session_register('aprovince');
	$aprovince=array();
}


if ($p1=="Save Checked" && !chkRights2("branch","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;

		if ($province[$c]!='')
		{
			if ($province_id[$c] == '')
			{
				$q = "insert into bankcard (enable, bankcard, telno)
					values
						('".$enable[$c]."','".$province[$c]."','".$province_code[$c]."')";
				$qr = pg_exec($q) or message (pg_errormessage());
			}
			else
			{
				$q = "update bankcard set
						enable='".$enable[$c]."',
						bankcard='".$province[$c]."',
						telno = '".$province_code[$c]."'
					where
						bankcard_id='".$province_id[$c]."'";
				$qr = pg_exec($q) or message (pg_errormessage().$q);
			}			
		}
		$ctr++;
	} 
	$aprovince['status']='SAVED';
}
?>
<form name="form1" id="form1" method="post" action="" style="margin:10px">
  <table class="table">
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
      </td>
    </tr>
  </table>
  <table  class="table" align="center">
    <tr bgcolor="#E9E9E9">
      <td width="9%" nowrap><b>#</b></td>
      <td width="22%" nowrap><a href="<?=$href.'&sort=province&start=$start&xSearch=$xSearch';?>"><b>Province</b></a></td>
      <td width="4%" nowrap><a href="<?=$href.'&sort=province&start=$start&xSearch=$xSearch';?>"><b>Code</b></a></td>
      <td width="40%" nowrap><b> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        Enabled</b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$aprovince['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="province_id[]" type="hidden" id="province_id[]" size="5">
         </td>
      <td> <input type="text" name="province[]" size="30"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td ><input name="province_code[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="5" maxlength="5"> 
      </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="8" height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan="8" height="26">Saved 
        Categories</td>
    </tr>
    <?
	} //if insert
	else
	{
		$aprovince['status']='LIST';
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
	$q = "select * from bankcard ";
	if ($xSearch != '')
	{
		$q .= " where bankcard like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='bankcard')
	{
		$sort = 'bankcard';
	}
	$q .= " order by $sort " ; // limit $start,10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap>
        <input type="hidden" name="province_id[]" size="5" value="<?= $r->bankcard_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td> <input name="province[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->bankcard;?>" size="30"> 
      </td>
      <td><input name="province_code[]" type="text"  id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->telno;?>" size="5" maxlength="5"> 
      </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="8"> 
        <input type="submit" name="p1" value="Save Checked">
         </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=province&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=province&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
