 <script>
 function vComputeSalary()
 {
 	var pay_category = document.getElementById('pay_category').value;
	var dayear = document.getElementById('dayear').value;
	if (pay_category%2 != 0)
	{
		if (dayear == 0)
		{
			document.getElementById('f1').adwr.value = twoDecimals((document.getElementById('ratem').value*12)/365);
			document.getElementById('f1').hourly.value = twoDecimals(document.getElementById('adwr').value/8);
		} else
		{
			document.getElementById('f1').adwr.value = twoDecimals((document.getElementById('ratem').value*12)/dayear);
			document.getElementById('f1').hourly.value = twoDecimals(document.getElementById('adwr').value/8);
		}	
	}
 }
 
 function vAdwr(obj)
 {
 	var adwr = obj.value;
	document.getElementById('f1').hourly.value = twoDecimals(document.getElementById('adwr').value/8);
 }
 </script> 
<?
if (chkRights2("hrempmast","mview",$ADMIN['admin_id'])) 
{
	$rghts='hidden';
	$rghts1='readonly';
	$rghts2='readonly';
}	
if (chkRights2("hrempmast","medit",$ADMIN['admin_id'])) 
{
	$rghts1='';
	$rghts='hidden';
	$rghts2='readonly';
}	
if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","madd",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id'])) 
{
	$rghts='text';
	$rghts1='';
	$rghts2='text';
}
?>  
<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr bgcolor="#EFEFEF"> 
    <td width="19%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Employment 
      Status</font></td>
    <td width="24%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <? 
	  	if ($rghts1=='') echo lookUpAssoc('emp_status',array('Active'=>'1','Resigned'=>'2','Retired'=>'3','In Active'=>'4','AWOL'=>'5','Casual'=>'6'),$paymast['emp_status']);
		else echo array_search($paymast['emp_status'],array('Active'=>'1','Resigned'=>'2','Retired'=>'3','In Active'=>'4','AWOL'=>'5','Casual'=>'6'));
	  ?>
      </font></td>
    <td width="20%">&nbsp;</td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;</font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Permanent Branch</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?
	  if ($rghts1=='') echo lookUpTable2('branch_id','branch','branch_id','branch',$paymast['branch_id']);
	  else echo lookUpTableReturnValue('x','branch','branch_id','branch',$paymast['branch_id']);
	  ?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Salary Level</font></td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<?
	if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id']))
	{
	?>
    <select name="level_id" id="level_id" onChange="xajax_paymastLevel(xajax.getFormValues('f1'))">
	<option value='0'>Salary Level</option>
	<?
		$qq="select * from level where enable='Y' order by level";
		$qqr = @pg_query($qq);
		while ($rr = @pg_fetch_object($qqr))
		{
			if ($paymast['level_id'] == $rr->level_id)
			{
				echo "<option value='$rr->level_id' selected>$rr->level</option>";
			}
			else
			{
				echo "<option value='$rr->level_id'>$rr->level</option>";
			}
			
		}
	?>
	</select>
      </font>
	<?
	}
	?>  
	  </td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Temporary Branch</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?
	  if ($rghts1=='') echo lookUpTable2('tempbranch_id','branch','branch_id','branch',$paymast['tempbranch_id']);
	  else echo lookUpTableReturnValue('x','branch','branch_id','branch',$paymast['tempbranch_id']);
	  ?>
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tenure Allowance</font></td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="tenureallowance" type="<?=$rghts;?>" id="tenureallowance" value="<?= $paymast['tenureallowance'];?>" size="15" style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('ratem').focus();return false;}">
	  	<?
	if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id']))
	{
	?>
      <?= lookUpAssoc("tenurew",array("None"=>'0',"Daily"=>"D","Monthly"=>"M"),$paymast['tenurew']);?>
	<?
	}
	?>  
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Department</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	  <?
	  if ($rghts1=='') echo lookUpTable2('department_id','department','department_id','department',$paymast['department_id']);
	  else echo lookUpTableReturnValue('x','department','department_id','department',$paymast['department_id']);
	  ?>
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pay Category 
      </font></td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<?
	if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id']))
	{
	?>
      <?= lookUpAssoc('pay_category',array('Regular Monthly'=>'1','Regular Daily'=>'2','ProB Monthly'=>'3','ProB Daily'=>'4','Contractual'=>'5','Daily Casual'=>'6'),$paymast['pay_category']);?>
	 <?
	 	if($paymast[pay_category] == '1' or $paymast[pay_category] == '3')
		{ ?>
			&nbsp;&nbsp;Days per year
			<input name="dayear" type="$rghts2" id="dayear" value="<?= $paymast['dayear'];?>" size="5" onBlur="vComputeSalary()" >
		<? }
	 ?> 
      </font>
	  <?
	  }
	  ?>
	  </td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Section</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	  <?
	  if ($rghts1=='') echo lookUpTable2('section_id','section','section_id','section',$paymast['section_id']);
	  else echo lookUpTableReturnValue('x','section','section_id','section',$paymast['section_id']);
	  ?>
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Monthly Rate</font></td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="ratem" type="<?=$rghts;?>" id="ratem" value="<?= $paymast['ratem'];?>" size="15" onBlur="vComputeSalary()" style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('adwr').focus();return false;}" >
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Postition</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="position" type="text" <?=$rghts1;?> id="position" value="<?= $paymast['position'];?>" size="30" />
    </font></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Average 
      Daily Wage</font></td>
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="adwr" type="<?=$rghts;?>" id="adwr" value="<?= $paymast['adwr'];?>" size="15" style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('hourly').focus();return false;}" onBlur="vAdwr(this)">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Rank</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	  <? 
	  	if ($rghts1=='') echo lookUpAssoc('rank',array('Rank & File'=>'R', 'Supervisor'=>'S','BOD'=>'B','Resigned'=>'D'),$paymast['rank']);
		else echo array_search($paymast['rank'],array('Rank & File'=>'R', 'Supervisor'=>'S','BOD'=>'B','Resigned'=>'D'));
	  ?>
</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Hourly Rate 
      </font></td>
    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="hourly" type="<?=$rghts;?>" id="hourly" value="<?= $paymast['hourly'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_sss').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Biomatrix # </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="biomatrix" type="text" <?=$rghts1;?> id="biomatrix" value="<?= $paymast['biomatrix'];?>" size="6" maxlength="6"  style="text-align:left"   onkeypress="if(event.keyCode==13) {document.getElementById('fixed_ssse').focus();return false;}" />
</font></td>
    <td bgcolor="#DADADA"><font color="#000066" size="2" face="Verdana, Arial, Helvetica, sans-serif">Monthly 
      Fixed Deductions</font></td>
    <td width="17%" bgcolor="#DADADA"><font color="#000066" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      Employee</font></td>
    <td width="20%" bgcolor="#DADADA"><font color="#000066" size="2" face="Verdana, Arial, Helvetica, sans-serif">Employer</font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fixed SSS 
      Deduction </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_sss" type="<?=$rghts;?>" id="fixed_sss" value="<?= $paymast['fixed_sss'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_ssse').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_ssse" type="<?=$rghts;?>" id="fixed_ssse" value="<?= $paymast['fixed_ssse'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_phic').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date Employed </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
	if ($rghts1=='')
	{
	?>
      <input name="date_employ" type="text" id="date_employ" value="<?= ymd2mdy($paymast['date_employ']);?>" size="12" onblur="IsValidDate(this,'MM/dd/yyyy')" onkeyup="setDate(this,'MM/dd/yyyy','en')"  onkeypress="if(event.keyCode==13) {document.getElementById('tenureallowance').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" alt="date_employ" width="12" height="12" onclick="popUpCalendar(this, f1.date_employ, 'mm/dd/yyyy')" /> </font>
	<?
	} else
	{
	?>  
	  <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?= ymd2mdy($paymast['date_employ']);?></font></td>
	<?
	}
	?>  
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fixed PHIC 
      Deduction </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_phic"  type="<?=$rghts;?>" id="fixed_phic" value="<?= $paymast['fixed_phic'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_phice').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_phice" type="<?=$rghts;?>" id="fixed_phice" value="<?= $paymast['fixed_phice'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_wtax').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date Resigned</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
	if ($rghts1=='')
	{
	?>	
    <input name="date_resign" type="text" <?=$rghts1;?> id="date_resign" value="<?= ymd2mdy($paymast['date_resign']);?>" size="12" onblur="IsValidDate(this,'MM/dd/yyyy')" onkeyup="setDate(this,'MM/dd/yyyy','en')"  onkeypress="if(event.keyCode==13) {document.getElementById('tenureallowance').focus();return false;}" />
    <img src="../graphics/dwn-arrow-grn.gif" alt="date_resign" width="12" height="12" onclick="popUpCalendar(this, f1.date_resign, 'mm/dd/yyyy')" /> </font>
	<?
	} else
	{
	?>  
	  <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;<?= ymd2mdy($paymast['date_resign']);?></font></td>
	<?
	}
	?>  
	</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fixed WTax 
      Deduction</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_wtax" type="<?=$rghts;?>" id="fixed_wtax" value="<?= $paymast['fixed_wtax'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_wtaxe').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_wtaxe" type="<?=$rghts;?>" id="fixed_wtaxe" value="<?= $paymast['fixed_wtaxe'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_pagibig').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fixed 
      Pag-Ibig Deduction</font></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_pagibig" type="<?=$rghts;?>" id="fixed_pagibig" value="<?= $paymast['fixed_pagibig'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('fixed_pagibige').focus();return false;}">
      </font></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="fixed_pagibige" type="<?=$rghts;?>" id="fixed_pagibige" value="<?= $paymast['fixed_pagibige'];?>" size="15"  style="text-align:right"   onKeypress="if(event.keyCode==13) {document.getElementById('date_employed').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#EFEFEF"> 
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3" valign="top"><font size="1" face="Times New Roman, Times, serif"><em>*Fixed 
      Deductions will NOT follow deduction table. Leave blank if table is used.</em></font></td>
  </tr>
</table>
