<script>
function vChk(t)
{
	var id = t.id
	var n = id.substring(1)

	document.getElementById('m'+n).checked =1;

}
</script>
<?
$this->View->setPageTitle("Manage Collection Fee");

$href = '?p=feetable';

if (!session_is_registered('afeetable'))
{
	session_register('afeetable');
	$afeetable=array();
}

if ($p1=="Close")
{
	session_unregister('afeetable');
	echo "<script> window.location='index.php' </script>";
}
if ($p1=="Save Checked" && $afeetable['status']=='INSERT')
{
	$c=0;
//	print_r($afrom);
//	echo "here ";exit;
	while ($c < count($mark))
	{
		if ($afrom[$c]!='')
		{
			if ($ato[$c] == '') $ato[$c] = 0;
			if ($afrom[$c] == '') $afrom[$c] = 0;
			if ($fee[$c] == '') $fee[$c] = 0;
			
			$q = "insert into feetable (afrom, ato, fee, type, enable)
					values ('".$afrom[$c]."','".$ato[$c]."',
							 '".$fee[$c]."','C','".$enable[$c]."')";
			@pg_query($q) or die (pg_errormessage().$q);
		}
		$c++;
	} 
	$afeetable['status']='SAVED';
}
elseif ($p1=="Save Checked" && $afeetable['status']=='LIST')
{
	$c=0;

	while ($c < count($mark))
	{
		$index = $mark[$c]-1;
		if ($afrom[$index]!='')
		{
			pg_query("update feetable set 
						enable='".$enable[$index]."',
						afrom='".$afrom[$index]."',
						ato = '".$ato[$index]."',
						fee='".$fee[$index]."',
						type='C'
					where feetable_id='".$feetable_id[$index]."'") or die (pg_errormessage());
		}
		$c++;
	} 
	$afeetable['status']='SAVED';
}
?><br>
<form name="form1" method="post" action="">
  <table class="table">
    <tr> 
      <td>Find 
        <input type="text" name="xSearch2" value="<?= $xSearch;?>">
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
         <hr color="#CC3300"></td>
    </tr>
  </table>
  <table class="table">
    <tr bgcolor="#E9E9E9"> 
      <td width="3%"><b>#</b></td>
      <td width="18%"><a href="<?=$href.'&sort=afrom';?>"><b>From</b></a></td>
      <td width="21%"><a href="<?=$href.'&sort=afrom';?>"><b>To</b></a></td>
      <td><b>Fee</b></td>
      <td width="13%"><b>
        <input type="hidden" name="sort" size="5" value="<?= $sort;?>">
        Enable</b></td>
    </tr>
    <?
	if ($p1=='Insert')
	{
		$afeetable['status']='INSERT';
		$c=0;
		while ($c < $insertcount)
		{
			$c++;
	?>
    <tr bgcolor="#FFFFFF"> 
      <td width="3%" align=right nowrap> 
        <?= $c;?>
        <input type="checkbox" name="mark[]" value="<?= $c;?>" id="m<?= $c;?>">
         </td>
      <td width="18%"> <input type="text" name="afrom[]" size="15" id="<?= 'F'.$c;?>"  onChange="vChk(this)" style="text-align:right"> 
      </td>
      <td width="21%"><input type="text" name="ato[]" size="15" id="<?= 'T'.$c;?>"  onChange="vChk(this)" style="text-align:right"></td>
      <td><input name="fee[]" type="text"  size="10" id="<?= 'R'.$c;?>"  onChange="vChk(this)" style="text-align:right"> 
      </td>
      <td width="13%"> <select name="enable[]"  id="<?= 'A'.$c;?>"  onChange="vChk(this)">
          <option value="Y">Yes</option>
          <option value="N">No</option>
        </select> </td>
    </tr>
    <?
		}
		?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan=5 height="2" valign="bottom">&nbsp;</td>
    </tr>
    <tr bgcolor="#E9E9E9"> 
      <td colspan=5 height="26">Saved 
        Table Range</td>
    </tr>
    <?
	} //if insert
	else
	{
		$afeetable['status']='LIST';
		$c=0;
	}
	
	$q = "select * from feetable  where type='C'";
	
	if ($sort == '' || $sort=='afrom')
	{
		$sort = 'afrom';
	}
	$q .= " order by $sort ";

	$qr = pg_query($q) or die (pg_error());
	$ctr = $c;
	while ($r = pg_fetch_object($qr))
	{
		$ctr++;
		if ($ctr %10 == 0)
		{
			echo "<tr bgcolor='#FFFFFF'> 
				 	<td colspan=10><input type='submit' name='p1' value='Save Checked'> <a href='#top'>Top</a></td></tr>";

		}
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td width="3%" align=right nowrap>
        <input type="hidden" name="feetable_id[]" size="5" value="<?= $r->feetable_id;?>">
        <? 
	  echo "$ctr."; 
	  if ($p1!='Insert')
	  {
         echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
	  }
	  ?>
         </td>
      <td width="18%"> <input name="afrom[]" type="text"  value="<?= $r->afrom;?>" size="15"  id="<?= 'F'.$ctr;?>"  onChange="vChk(this)"  style="text-align:right"> 
      </td>
      <td width="21%"> <input name="ato[]" type="text"  id="<?= 'T'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->ato;?>" size="15" style="text-align:right"></td>
      <td><input name="fee[]" type="text"  id="<?= 'R'.$ctr;?>"  onChange="vChk(this)" value="<?= $r->fee;?>" size="10" style="text-align:right"> 
      </td>
      <td width="13%"> <select name="enable[]"  id="<?= 'A'.$ctr;?>"  onChange="vChk(this)">
          <option value="Y"  <?= ($r->enable ? 'Selected' :'');?>>Yes</option>
          <option value="N"  <?= (!$r->enable ? 'Selected' :'');?>>No</option>
        </select> </td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="5">
        <input type="submit" name="p1" value="Save Checked">
        <a href="#top">Go Top</a></td>
    </tr>
  </table>
</form>
