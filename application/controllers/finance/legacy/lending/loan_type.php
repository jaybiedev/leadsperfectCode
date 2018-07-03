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
$this->View->setPageTitle("Manage Loan Types");
$href = '?p=loan_type';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("loan_type","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('aloan_type'))
{
	session_register('aloan_type');
	$aloan_type=array();
}


if ($p1=="Save Checked" && !chkRights2("loan_type","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;

		if ( ($loan_rate[$c]) == '') $loan_rate[$c]=0;
		if ( ($eir[$c]) == '') $eir[$c]=0;

		if ($loan_type[$c]!='')
		{
			if ($loan_type_id[$c] == '')
			{
				$q = "insert into loan_type (enable, loan_type, loan_type_code, loan_rate, basis, eir,loan_interest)
					values
						('".$enable[$c]."','".$loan_type[$c]."','".$loan_type_code[$c]."',
						'".$loan_rate[$c]."','".$basis[$c]."','".$eir[$c]."','".$loan_interest[$c]."')";
				$qr = pg_exec($q) or message (pg_errormessage());
			}
			else
			{
				$q = "update loan_type set
						enable='".$enable[$c]."',
						loan_type='".$loan_type[$c]."',
						loan_type_code = '".$loan_type_code[$c]."',
						loan_interest = '".$loan_interest[$c]."',
						basis = '".$basis[$c]."',
						eir = '".$eir[$c]."',
						loan_rate='".$loan_rate[$c]."'
					where
						loan_type_id='".$loan_type_id[$c]."'";
				$qr = pg_exec($q) or message (pg_errormessage().$q);
			}			
		}
		$ctr++;
	} 
	$aloan_type['status']='SAVED';
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
      <td width="22%" nowrap><a href="<?=$href.'&sort=loan_type&start=$start&xSearch=$xSearch';?>"><b>Loan 
        Type</b></a></td>
      <td width="4%" nowrap><a href="<?=$href.'&sort=loan_type&start=$start&xSearch=$xSearch';?>"><b>Code</b></a></td>
      <td width="6%" align="center" nowrap><b>Interest<br>
        Rate(%)</b></td>
      <td width="8%" nowrap><b>Computed</b></td>
      <td width="6%" nowrap><b>Interest</b></td>
      <td width="5%" nowrap><b>EIR</b></td>
      <td width="40%" nowrap><b> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        Enabled</b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$aloan_type['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap>  
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="loan_type_id[]" type="hidden" id="loan_type_id[]" size="5">
         </td>
      <td> <input type="text" name="loan_type[]" size="30"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td ><input name="loan_type_code[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="5" maxlength="5"> 
      </td>
      <td ><input name="loan_rate" type="text" id="<?='r'.$ctr;?>"  onChange="vChk(this)" size="5" maxlength="5" style="text-align:right"></td>
      <td > 
        <?= lookUpAssoc('basis[]',array("Monthly"=>"M","Annual"=>"A","Interest Only"=>"I"),'');?>
      </td>
      <td >
        <?= lookUpAssoc('loan_interest[]',array("AddOn"=>"A","Discounted"=>"D","Fixed"=>"F"),'');?>
      </td>
      <td > <input name="eir[]" type="text" id="<?='r'.$ctr;?>"   onChange="vChk(this)" size="7" maxlength="10" style="text-align:right"> 
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
		$aloan_type['status']='LIST';
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
	$q = "select * from loan_type ";
	if ($xSearch != '')
	{
		$q .= " where loan_type like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='loan_type')
	{
		$sort = 'loan_type';
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
        <input type="hidden" name="loan_type_id[]" size="5" value="<?= $r->loan_type_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td> <input name="loan_type[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->loan_type;?>" size="30"> 
      </td>
      <td><input name="loan_type_code[]" type="text"  id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->loan_type_code;?>" size="5" maxlength="5"> 
      </td>
      <td><input name="loan_rate[]" type="text" id="<?='r'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->loan_rate;?>" size="5" maxlength="5" style="text-align:right"></td>
      <td> 
        <?= lookUpAssoc('basis[]',array("Monthly"=>"M","Annual"=>"A","Interest Only"=>"I"),$r->basis);?>
      </td>
      <td>
        <?= lookUpAssoc('loan_interest[]',array("AddOn"=>"A","Discounted"=>"D","Fixed"=>"F"),$r->loan_interest);?>
      </td>
      <td><input name="eir[]" type="text" id="<?='t'.$ctr;?>" style="text-align:right"   onChange="vChk(this)" value="<?= $r->eir;?>" size="7" maxlength="10"></td>
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
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=loan_type&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=loan_type&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
