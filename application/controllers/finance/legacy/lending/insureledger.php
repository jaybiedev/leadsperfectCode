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

$href = '?p=insureledger';
require_once('../lib/dbconfig.php');
require_once('../lib/connect.php');

if ($ADMIN[admin_id] != 1) 
{
	message("*****  UNDER CONSTRUCTION  *****");
	exit;
}
if (!chkRights2('releasing','medit',$ADMIN['admin_id']))
{
	message("You have no permission to Pay Insurance...");
	exit;
}

$provinces=array();

$q = "select * from bankcard where enable order by bankcard";
$qr = @pg_query($q) or message(pg_errormessage());
$provinces['NONE'] = '0';
while ($r = @pg_fetch_object($qr))
{
	$provinces[$r->bankcard] = $r->bankcard_id;
}

if ($year==0 or $year=='') $year=date("Y");
if ($p1=="Pay Checked" && !chkRights2("releasing","madd",$ADMIN['admin_id']))
{
	message("You have no permission to modify or add...");
}
if ($p1=="Printer")
{

	$prtform = "<font style='font-family:monospace;line-height:120%;font-size:100%;'>".$header.$detail."</font>";

	echo "<input type='hidden' id='print_area' name='print_area' value=".'"'.$prtform.'"'.">";
	echo "<iframe name='printit' id='printit' style='width:0px;height:0px;'>";
	echo "</iframe>";
	echo "<script>printIframe(print_area)</script>"; 

}
elseif ($p1=='Select')
{
	echo "selected ";
	exit;
}
?>
<form name="form1" id="form1" method="post">
  <table width="81%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td nowrap><font face="Verdana, Arial, Helvetica, sans-serif" size="2">
        <select name = "province_id" id = "province_id" onChange="document.getElementById('form1').action='?p=payinsure';document.getElementById('form1').submit();">
          <?
				$q = "select * from bankcard where enable ";
          		echo "<option value=''>".None."</option>";
	      
				$q .= "order by bankcard";
				
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($province_id == $r->bankcard_id)
					{
						echo "<option value=$r->bankcard_id selected>$r->bankcard</option>";
					}
					else
					{	
						echo "<option value=$r->bankcard_id>$r->bankcard</option>";
					}	
				}
				
			?>
        </select>
        <select name = "branch_id">
            <?
				$q = "select * from branch where enable ";
				if ($ADMIN['branch_id'] > '0')
				{
	                echo "<option value=''>Select Branch</option>";
					$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
					if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
					if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
					if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
					if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
					if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
					if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
					if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
					if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
					if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
					$q .= ") ";
				} else
				{
					?>
            <option value=''>All Branches</option>
            <?
				}
				$q .= "order by branch";
				
				$qr = @pg_query($q);
				while ($r = @pg_fetch_object($qr))
				{
					if ($province_id != '' and $province_id != $r->province) continue; 		
									
					if ($branch_id == $r->branch_id)
					{
						echo "<option value=$r->branch_id selected>$r->branch</option>";
					}
					else
					{	
						echo "<option value=$r->branch_id>$r->branch</option>";
					}	
				}
 				
			?>
          </select> 
          Name :<input name="search" type="text" id="search" value="<?= $search;?>"  onkeypress="if(event.keyCode==13) {document.getElementById('go').click();return false;}" />
          <?=lookUpAssoc('searchby',array('Name'=>'account','Account No.'=>'account_code','RecordId'=>'account_id'),$searchby);?>
          <input name="p1" type="submit" id="p1" value="Go">
          <input name="p1" type="submit" id="p1" value="Print" />
          <input name="p1" type="button" id="p1" onClick="window.location='?p='" value="Close">
        </font>
        <hr color="#CC3300">
      <font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; </font></td>
    </tr>
  </table>
  </form>
  <?
if ($p1=='Go')
{
	  $q = "select *, sum(ins.debit) as debit, sum(ins.credit) as credit
				from account ac, insurance ins
				where 
					ac.account_id = ins.account_id and
					ac.account ilike '$search%' and
					ac.enable and ins.status!='C' ";
		if ($account_group_id != '')
		{
			$q .= " and ac.account_group_id='$account_group_id'";
		}
		if ($branch_id != '')
		{
			$q .= " and ac.branch_id='$branch_id'";
			$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
		}
		else
		{
			$branch = '';
		}
		$q .= "group by ins.account_id	order by account ";
echo $q;
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
?>
  
<table width="75%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
  <tr bgcolor="#CFD3E7"> 
    <td width="7%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">#</font></strong></td>
    <td width="38%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name</font></strong></td>
    <td width="21%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Account 
      Group </font></strong></td>
    <td width="11%"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Releasing ID# </font></strong></td>
    <td width="12%" align="center"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></strong></td>
    <td width="11%" align="center">&nbsp;</td>
  </tr>
  <?
  	$ctr=0;
  	while ($r = pg_fetch_assoc($qr))
	{
		$ctr++;
  ?>
	  <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'"> 
		<td align="right" nowrap width="7%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <?=$ctr;?>
		  .</font></td>
		<td width="38%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <a href="?p=insureledger&p1=Select&c_id=<?= $r['account_id'];?>"> 
		  <?= $r['account'];?>
		  </a> </font></td>
		<td width="21%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
		  </font></td>
		<td width="11%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <?= status($r['account_status']);?>
		  </font></td>
		<td align="right" width="12%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  <?= number_format($account_balance,2);?>
		  </font></td>
		<td width="11%" height="22"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> &nbsp; 
		  <a href="?p=report.accountledger&p1=Selectreleasing&show=<?=$show;?>&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>"></a></font></td>
	  </tr>
  <?
	}
	?>
	 </table>
	<?	
}
if ($p1=='Select')
{
?>
<br />

 <form action="" method="post" name="f2" id="f2" style="margin:0">
 <table width="81%" border="0" cellspacing="1" cellpadding="1" bgcolor="#EFEFEF" align="center">
    <tr bgcolor="#C8D7E6" background="../graphics/table_horizontal.PNG"> 
      <td height="27" colspan="9"  background="../graphics/table_horizontal.PNG"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> 
        <img src="../graphics/mail.gif" width="16" height="17"> Insurance Payment
        Ledger <a name="top"></a></b></font></td>
    </tr>
    <tr bgcolor="#E9E9E9">
      <td colspan="4"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">NAME OF ACCOUNT: </font></b></td>
      <td colspan="5"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">ACCOUNT GROUP: </font></b></td>
	</tr>
    <tr bgcolor="#E9E9E9"> 
      <td width="8%"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">#</font></b></td>
      <td width="12%" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">LOAN RELEASE# </font></b></td>
      <td width="10%" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">DATE RELEASE </font></b></td>
      <td width="10%" nowrap align="center"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">LOAN</font></b></td>
      <td width="13%" align="center" nowrap><b><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">INSURANCE</font></b></td>
      <td width="13%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">TERM APPLIED </font></b></td>
      <td width="12%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">PAYMENT</font></b></td>
      <td width="10%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">BALANCE</font></b></td>
      <td width="12%" align="center" nowrap><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">REFERENCE</font></b></td>
    </tr>
	<?
	$dt = date("Y-m-d");
	$payrefer = date("Ym");
	$q = "select reference from insurance where substr(reference,1,6)='$payrefer' order by reference DESC";
	$qr = pg_query($q) or message(pg_errormessage());
	$r = pg_fetch_object($qr);
	if (substr($r->reference,0,6) == $payrefer)
	{
		$sub = substr($r->reference,6,2)*1;	
		$sub++;
		$payrefer = $payrefer.str_pad($sub,2,'0',STR_PAD_LEFT); 
	} else $payrefer = $payrefer.'00';
	
	if ($p1=='List') 
	{
		$start=0;
		$xSearch='';
	}	
	if ($start == '') $start=0;
	if ($p1=='Next') $start = $start + 10;
	if ($p1=='Previous') $start = $start - 10;
	if ($start < 0) $start=0;	
	$month=$_REQUEST['month'];
	$year=$_REQUEST['year'];
	$payperiod = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT);
	if ($month <12) $date_to = $year.'-'.str_pad($month+1,2,'0',STR_PAD_LEFT).'-01';
	else 
	{
		$yr = $year+1;
		$date_to = $yr.'-01-01';
	}	
	$q = "select sum(debit) as debit, sum(credit) as credit, insurance.releasing_id, account.branch_id, account.account, account.account_group_id, insurance.reference, 
					insurance.insurance_id, apmonth, apterm, termbal
			from
				insurance, account
			where
				account.account_id = insurance.account_id and
				insurance.status != 'C' and (insurance.apmonth = '$payperiod' or 
				(insurance.apmonth < '$payperiod' and (reference='' or reference ISNULL)))";
				
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id='$account_group_id'";
	}
	if ($branch_id != '')
	{
		$q .= " and account.branch_id='$branch_id'";
		$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$branch_id);
	}
	else
	{
		$branch = '';
	}
	$q .= " 	group by insurance.releasing_id, account.branch_id, account.account, account.account_group_id, insurance.reference, insurance.insurance_id
			    order by branch_id, account";
	$qr = pg_query($q) or message(pg_errormessage());
	$ctr = 0;
	
	while ($r = pg_fetch_assoc($qr))
	{
		if ($province_id != '')
		{
			$province=lookUpTableReturnValue('x','branch','branch_id','province',$r['branch_id']);
			if ($province_id != $province) 	continue;
		}
		$ctr++;
		$accountgroup=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r[account_group_id]);
		if (substr($accountgroup,0,8)=='Deceased')  continue;
				
		$qu = "select * from releasing where releasing_id='".$r['releasing_id']."'";
		$qur = pg_query($qu) or message(pg_errormessage());
		$ru = pg_fetch_object($qur);
		if ($r['reference'] !='' ) 
		{
			$due = $r['debit'];
			$bal = $r['credit']-$r['debit'];
		}	
		elseif ($r[termbal]==0) 
		{
			$due = $r['credit'];
			$bal = 0.00;
		}	
		else 
		{
			$due = ($r['credit']/($r['termbal']+$r['apterm'])) * $r['apterm'];
			$bal = $r['credit'] - $due;
		}	
	?>
    <tr  bgcolor=<?= ($r->enable=='N')? '#FFCCCC' :'#FFFFFF';?>> 
      <td  align=right nowrap><font size=1> 
        <input type="hidden" name="account_id[]" size="5" value="<?= $ru->account_id;?>">
        <input type="hidden" name="releasing_id[]" size="5" value="<?= $ru->releasing_id;?>">
        <input type="hidden" name="insurance_id[]" size="5" value="<?= $r['insurance_id'];?>">
        <? 
 	    echo "$ctr."; 
//		if ($r['reference'] == '') echo "<input type='checkbox' name='mark[]' value='$ctr' id='m$ctr'>";
 	    ?>
        </font> </td>
      <td> <input name="releasing_id" readonly type="text" value="<?= $r['releasing_id'];?>" size="15"> </td>
      <td> <input name="date_release" readonly type="text"  value="<?=$r['date_release'];?>" size="10"> </td>
      <td align="center"><input name="loan[]" type="text" readonly id="<?='k'.$ctr;?>"  onChange="vChk(this)" value="<?=number_format($ru->principal,2);?>" size="13" style="text-align:right"> 
      </td>
      <td align="center"><input name="credit[]" type="text" readonly id="<?='k'.$ctr;?>"   value="<?=number_format($r['credit'],2);?>"  size="13" style="text-align:right"></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="term[]" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= $r['apterm'];?>" size="5" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="due[]" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= number_format($due,2);?>" size="12" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="bal" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?= number_format($bal,2);?>" size="12" style="text-align:right">
        </font></td>
      <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
        <input name="reference" type="text"  readonly id="<?='k'.$ctr;?>"   value="<?=$r['reference'];?>" size="10" style="text-align:left">
        </font></td>
    </tr>
    <?
	}
	?>
    <tr bgcolor="#FFFFFF"> 
      <td colspan="4">&nbsp;</td>
      <td colspan="5"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp; 
       </font> </td>
    </tr>
  </table>
</form>
<?
}
?>
<div align="center">
<?
	echo "<img src='../graphics/redarrow_left.gif'><a href='?p=clientbank&p1=Previous&start=$start&sort=$sort&xSearch=$xSearch'> Previous</a>";
	echo " <b> | </b>";
	echo "<a href='?p=clientbank&p1=Next&start=$start&sort=$sort&xSearch=$xSearch'> Next </a><img src='../graphics/redarrow_right.gif'> ";
?>	
</div>
