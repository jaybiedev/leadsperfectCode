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
$this->View->setPageTitle("Manage Branches");
$href = '?p=branch';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("branch","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('abranch'))
{
	session_register('abranch');
	$abranch=array();
}
$provinces=array();

$q = "select * from bankcard where enable order by bankcard";
$qr = @pg_query($q) or message(pg_errormessage());
$provinces['NONE'] = '0';
while ($r = @pg_fetch_object($qr))
{
	$provinces[$r->bankcard] = $r->bankcard_id;
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
		if ($branch[$c]!='')
		{
			if ($init_balance[$c] == '') $init_balance[$c] = 0;

			if ($branch_id[$c] == '')
			{
				$q = "insert into branch (enable, branch, branch_code, branch_address, init_balance, printer_type,province,location,long,lati,swipe)
					values
						('".$enable[$c]."','".$branch[$c]."','".$branch_code[$c]."','".$branch_address[$c]."','".$init_balance[$c]."','".$printer_type[$c]."','".$province_id[$c]."','".$location[$c]."','".$long[$c]."','".$lati[$c]."','".$swipe[$c]."')";
				$qr = pg_exec($q) or message (pg_errormessage());
			}
			else
			{
				$q = "update branch set
						enable='".$enable[$c]."',
						branch='".$branch[$c]."',
						branch_code='".$branch_code[$c]."',
						init_balance='".$init_balance[$c]."',
						printer_type='".$printer_type[$c]."',
						province='".$province_id[$c]."',
						location='".$location[$c]."',
						long='".$long[$c]."',
						lati='".$lati[$c]."',
						swipe='".$swipe[$c]."',
						branch_address = '".$branch_address[$c]."'
					where
						branch_id='".$branch_id[$c]."'";
					
				@pg_query($q) or message (pg_errormessage());
			}			
		}
		$ctr++;
	} 
	$abranch['status']='SAVED';
}
?><br>
<form name="form1" method="post" action="">
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
		<?
			if ($ADMIN[usergroup]=='A' and $ADMIN[branch_id]=='0')
			{
		?>
        <input name="p1" type="submit" id="p1" value="Insert">
		<?
			}
		?>	
        <input name="p1" type="submit" id="p1" value="List">
        <input name="p1" type="button" id="p1" onClick="window.location='?p='" value="Close">
        
        <hr color="#CC3300"></td>
    </tr>
  </table>
  <table  class="table" align="center">
    <tr bgcolor="#E9E9E9">
      <td width="8%"><b>#</b></td>
      <td width="13%" nowrap><a href="<?=$href.'&sort=branch&start=$start&xSearch=$xSearch';?>"><b>Branch</b></a></td>
      <td width="4%" nowrap><a href="<?=$href.'&sort=branch_code&start=$start&xSearch=$xSearch';?>"><b>Code</b></a></td>
      <td width="21%" nowrap><a href="<?=$href.'&sort=branch_address&start=$start&xSearch=$xSearch';?>"><b>Address</b></a></td>
      <td width="17%" nowrap><b>Longitude/Latitude</b></td>
      <td width="10%" nowrap><b>Printing</b></td>
      <td width="15%" nowrap><b>Partners</b></td>
      <td width="6%" nowrap><b>Swipe</b></td>
      <td width="6%" nowrap><b>
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
      Enabled</b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$abranch['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap>  
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="branch_id[]" type="hidden" id="branch_id[]" size="5">
         </td>
      <td> <input type="text" name="branch[]" size="25"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td><input type="text" name="branch_code[]" size="5"  onChange="vChk(this)" id="<?='d'.$c;?>"></td>
      <td ><input name="branch_address[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="40" maxlength="40"> 
      </td>
     <td><input type="text" name="long[]" size="10" maxlength="10" onChange="vChk(this)" id="<?='lo'.$c;?>">&nbsp;
	 <input type="text" name="lati[]" size="10" maxlength="10" onChange="vChk(this)" id="<?='la'.$c;?>"></td>
      <td > 
        <?=lookUpAssoc('printer_type[]',array('NONE'=>'NONE', 'UDP Draft PRINTER'=>'UDP DRAFT','TCP Draft PRINTER'=>'TCP DRAFT','GRAPHIC'=>'GRAPHIC','LINUX LP Printer'=>'LINUX LP Printer','PHP Printer(DRAFT)'=>'PHP Printer(DRAFT)','PHP Printer(TEXT)'=>'PHP Printer(TEXT)'),'');?>
      </td>
      <td ><?=lookUpAssoc('province_id[]',$provinces,'');?></td>
      <td> 
        <?= lookUpAssoc('swipe[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),'');?>
      </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="9" height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan="8" height="26">Saved 
        Categories</td>
    </tr>
    <?
	} //if insert
	else
	{
		$abranch['status']='LIST';
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
	$q = "select * from branch ";
	if ($ADMIN['branch_id'] > '0')
	{
		$q .= " where (branch_id ='".$ADMIN['branch_id']."'";
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
	}
	if ($xSearch != '')
	{
		$q .= " and branch like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='branch')
	{
		$sort = 'branch';
	}
	$q .= " order by $sort offset $start limit 10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap> 
        <input type="hidden" name="branch_id[]" size="5" value="<?= $r->branch_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td> <input name="branch[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->branch;?>" size="25"> 
      </td>
      <td><input name="branch_code[]" type="text"  id="<?='d'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->branch_code;?>" size="5"></td>
      <td><input name="branch_address[]" type="text" id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->branch_address;?>" size="40" maxlength="40"> 
      </td>
      <td><input name="long[]" type="text"  id="<?='lo'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->long;?>" size="10" maxlength="10">&nbsp;<input name="lati[]" type="text"  id="<?='lo'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->lati;?>" size="10" maxlength="10">
	  </td>
      <td>
        <?=lookUpAssoc('printer_type[]',array('NONE'=>'NONE', 'UDP Draft PRINTER'=>'UDP DRAFT','TCP Draft PRINTER'=>'TCP DRAFT','GRAPHIC'=>'GRAPHIC','LINUX LP Printer'=>'LINUX LP Printer','PHP Printer(DRAFT)'=>'PHP Printer(DRAFT)','PHP Printer(TEXT)'=>'PHP Printer(TEXT)'),$r->printer_type);?>
      </td>
      <td>
        <?=lookUpAssoc('province_id[]',$provinces,$r->province);?>
      </td>
      <td> 
        <?= lookUpAssoc('swipe[]',array("Yes"=>"t","No"=>"f"),$r->swipe);?>
      </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="9">
        <input type="submit" name="p1" value="Save Checked">
         </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=branch&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=branch&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
