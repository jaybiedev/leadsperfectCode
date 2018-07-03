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
$href = '?p=bank';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if (!chkRights2("bank","mview",$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}

if (!session_is_registered('abank'))
{
	session_register('abank');
	$abank=array();
}


if ($p1=="Save Checked" && !chkRights2("bank","madd",$ADMIN['admin_id']))
	{
		message("You have no permission to modify or add...");
	}
if ($p1=="Save Checked")
{

	$ctr=0;
	while ($ctr < count($mark))
	{
		$c = $mark[$ctr]-1;
		if ($bank[$c]!='')
		{
		
			if ($init_balance[$c] == '') $init_balance[$c] = 0.00;
			if ($branch_id[$c] == '') $branch_id[$c] = 0;
			if ($bank_id[$c] == '')
			{
				$q = "insert into bank (enable, braccess, bank, bank_account, init_balance,branch_id)
					values
						('".$enable[$c]."','".$braccess[$c]."','".$bank[$c]."','".$bank_account[$c]."',
						 '".$init_balance[$c]."','".$branch_id[$c]."')";
				$qr = pg_query($q) or message (pg_errormessage());
				
				$q = "select currval('bank_bank_id_seq')" ;
				$r = fetch_object($q);
				$bank_id[$c] = $r->currval;

			}
			else
			{
				$q = "select * from bank where bank_id ='".$bank_id[$c]."'";
				$qr = pg_query($q) or message (pg_errormessage());
				$rb = fetch_object($q);
				$oldbranch_id = $rb->branch_id;
								
				pg_exec("update bank set
						braccess='".$braccess[$c]."',
						enable='".$enable[$c]."',
						bank='".$bank[$c]."',
						init_balance='".$init_balance[$c]."',
						branch_id='".$branch_id[$c]."',
						bank_account = '".$bank_account[$c]."'
					where
						bank_id='".$bank_id[$c]."'") or message (pg_errormessage());
				if ($oldbranch_id != $branch_id[$c])
				{
					pg_exec("update bankrecon set
							branch_id='".$branch_id[$c]."',
							oldbranch_id='$oldbranch_id'
						where
							branch_id='$oldbranch_id'") or message (pg_errormessage());						
				}
			}	
			if ($date_init[$c] != '' && $date_init[$c] != '//'  && $date_init[$c] != '00/00/0000' &&  $date_init[$c] != '0000-00-00')
			{	
				$q = "update bank set date_init='".mdy2ymd($date_init[$c])."'";
				@pg_query($q);
			}	
			if ($oldbranch_id > 0)
			{
//				$pg_exec("update");
			}
		}
		$ctr++;
	} 
	$abank['status']='SAVED';
}
?>
<form name="form1" method="post" action="">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
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
        </td>
    </tr>
  </table>
  <table width="90%" border="0" cellspacing="1" cellpadding="1" bgcolor="#EFEFEF" align="center">
    <tr bgcolor="#C8D7E6" background="../graphics/table_horizontal.PNG"> 
      <td height="27" colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
        <img src="../graphics/mail.gif" width="16" height="17"> Bank Setup <a name="top"></a></b></font></td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td width="12%"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">#</font></b></td>
      <td width="11%" nowrap><a href="<?=$href.'&sort=bank&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Bank</font></b></a></td>
      <td width="12%" nowrap><a href="<?=$href.'&sort=bank&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Branch</font></b></a></td>
      <td width="12%" nowrap><a href="<?=$href.'&sort=bank&start=$start&xSearch=$xSearch';?>"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Account 
        No. </font></b></a></td>
      <td width="11%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Initial<br>
        Bank Deposit</font></b></td>
      <td width="6%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">As 
        of <br>
        Date </font></b></td>
      <td width="6%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">
	  Branch<br>Access</font></b></td>		
      <td width="36%" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        </font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">Enabled</font></b><b></b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$abank['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
      <td align=right nowrap> <font size=1> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="<?= 'm'.$c;?>">
        <input name="bank_id[]" type="hidden" id="bank_id[]" size="5">
        </font> </td>
      <td> <input type="text" name="bank[]" size="30"  onChange="vChk(this)" id="<?='t'.$c;?>"> 
      </td>
      <td><?= lookUpTable2('branch_id[]','branch','branch_id','branch','');?></td>
      <td ><input name="bank_account[]" type="text" id="<?='k'.$c;?>"  onChange="vChk(this)" size="15" maxlength="15"> 
      </td>
      <td ><input name="init_balance[]" type="text" id="<?='k'.$c;?>"   onChange="vChk(this)" size="10" style="text-align:right"></td>
      <td ><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date_init[]" type="text" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"  id="<?='k'.$c;?>"   onChange="vChk(this)">
        </font></td>
      <td> 
        <?= lookUpAssoc('braccess[]',array("Yes"=>"t","No"=>"f"),'');?>
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
      <td colspan="8" height="26"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Saved 
        Categories</font></td>
    </tr>
    <?
	} //if insert
	else
	{
		$abank['status']='LIST';
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
	$q = "select * from bank ";
	if ($xSearch != '')
	{
		$q .= " where bank like '$xSearch%' ";
	}
	
	if ($sort == '' || $sort=='bank')
	{
		$sort = 'bank';
	}
	$q .= " order by $sort "; // offset $start limit 10";

	$qr = pg_exec($q) or die (pg_errormessage());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap><font size=1> 
        <input type="hidden" name="bank_id[]" size="5" value="<?= $r->bank_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
        </font> </td>
      <td> <input name="bank[]" type="text" id="<?='t'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->bank;?>" size="30"> 
      </td>
      <td>
        <?= lookUpTable2('branch_id[]','branch','branch_id','branch',$r->branch_id);?>
      </td>
      <td><input name="bank_account[]" type="text" id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=$r->bank_account;?>" size="15" maxlength="15"> 
      </td>
      <td><input name="init_balance[]" type="text" id="<?='k'.$ctr;?>"   value="<?=$r->init_balance;?>"  style="text-align:right" onChange="vChk(this)" size="10"></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date_init[]" type="text"  id="<?='k'.$ctr;?>"   onChange="vChk(this)" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')" value="<?= ymd2mdy($r->date_init);?>" size="8">
        </font></td>
      <td> 
        <?= lookUpAssoc('braccess[]',array("Yes"=>"t","No"=>"f"),$r->braccess);?>
      </td>
      <td> 
        <?= lookUpAssoc('enable[]',array("Yes"=>"t","No"=>"f"),$r->enable);?>
      </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="8"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> 
        <input type="submit" name="p1" value="Save Checked">
        </font> </td>
    </tr>
  </table>
</form>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=bank&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=bank&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
