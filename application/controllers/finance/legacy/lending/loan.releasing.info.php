  <table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td width="17%" rowspan="6" nowrap bgcolor="#EFEFEF">&nbsp;
      <!-- picutre -->
    </td>
    <td width="10%" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Group</font></td>
    <td width="29%" bgcolor="#EFEFEF"> 
      <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLoan['account_group_id']);?>
    </td>
    <td colspan="2" nowrap bgcolor="#EFEFEF"><font color="#990000" size="2" face="Verdana, Arial, Helvetica, sans-serif">.:: 
      Classification</font> </td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Salary/Pension</font></td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      P 
      <input type="text" size="12" name="salary" id="salary" value="<?= number_format($aLoan['salary'],2);?>" style="text-align:right; font-weight:bold; background:none; border:none">
      </b></font></td>
    <td colspan="2" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" id="mclass" value="1" <?= ($aLoan['mclass'] == '1'  || $aLoan['mclass'] == ''? 'checked' : '');?> onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))">
      Pensioner (Personal )</font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank</font></td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> 
      <?= lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$aLoan['clientbank_id']);?>
      </b></font></td>
    <td colspan="2" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="2" <?= ($aLoan['mclass'] == '2' ? 'checked' : '');?>  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))">
      Survivor/Widower/Beneficiary</font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b> </b> </font></td>
    <td width="3%" bgcolor="#EFEFEF">&nbsp;</td>
    <td width="41%" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Child 
      B-Day before 21 
      <input name="date_child21" type="text" id="date_child21" value="<?= ymd2mdy($aLoan['date_child21']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');xajax_computeMaxTerm(xajax.getFormValues('f1'))"  onKeypress="if(event.keyCode==13) {document.getElementById('changebank').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_child21, 'mm/dd/yyyy')"> 
      </font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Birth 
      Date</font></td>
    <td bgcolor="#EFEFEF"><input name="date_birth" type="text" id="date_birth" value="<?= ymd2mdy($aLoan['date_birth']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');xajax_computeMaxTerm(xajax.getFormValues('f1'))" onKeyUp="setDate(this,'MM/dd/yyyy','en')" onKeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}"> 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_birth, 'mm/dd/yyyy')"></font></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="3"  <?= ($aLoan['mclass'] == '3' ? 'checked' : '');?>  id="mclass3" onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))">
      Permanent Disability</font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Age</font></td>
    <td bgcolor="#EFEFEF"><input name="age" type="text" id="age" value="<?= $aLoan['age'];?>" size="10" maxlength="10"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker1').focus();return false;}"></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="4"  <?= ($aLoan['mclass'] == '4' ? 'checked' : '');?>  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))">
      Temporary Disability</font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <select name="comake1_id" style="width:250px" onkeypress="if(event.keyCode==13) {document.getElementById('comaker1').focus();return false;}">
        <option value="0">Select Co-Maker1 link</option>
        <?
		$q = "select * from account where enable and branch_id='".$aLoan['branch_id']."' order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aLoan['comake1_id'] == $r->account_id)
			{
				echo "<option value=$r->account_id selected>$r->account - $r->account_code</option>";
			}
			else
			{
				echo "<option value=$r->account_id>$r->account - $r->account_code</option>";
			}	
		}
		?>
      </select>
    </font></td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker1</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker1" type="text" id="comaker1" value="<?= $aLoan['comaker1'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker1_relation').focus();return false;}"></td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remaining 
      Pension (Months) 
      <input name="npension" type="text" id="npension" value="<?= $aLoan['npension'];?>" size="5" maxlength="5"  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))">
      </font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker1_relation" type="text" id="comaker1_relation" value="<?= $aLoan['comaker1_relation'];?>" size="35" maxlength="50"   onKeypress="if(event.keyCode==13) {document.getElementById('comaker1_address').focus();return false;}"></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF"><input type="radio" name="mclass" value="5"  <?= ($aLoan['mclass'] == '5' ? 'checked' : '');?>  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))"> 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Guardian</font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker1_address" type="text" id="comaker1_address" value="<?= $aLoan['comaker1_address'];?>" size="35" maxlength="50"   onKeypress="if(event.keyCode==13) {document.getElementById('comaker2').focus();return false;}"></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF"><hr size=1></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <select name="comake2" style="width:250px" onkeypress="if(event.keyCode==13) {document.getElementById('comaker2').focus();return false;}">
        <option value="0">Select Co-Maker2 link</option>
        <?
		$q = "select * from account where enable and branch_id='".$aLoan['branch_id']."' order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aLoan['comake2_id'] == $r->account_id)
			{
				echo "<option value=$r->account_id selected>$r->account - $r->account_code</option>";
			}
			else
			{
				echo "<option value=$r->account_id>$r->account - $r->account_code</option>";
			}	
		}
		?>
      </select>
    </font></td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker2</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker2" type="text" id="comaker2" value="<?= $aLoan['comaker2'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker2_relation').focus();return false;}"></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF"><input type="checkbox" name="changebank" id="changebank" value=""  <?= ($aLoan['nchangebank'] > '0' ? 'checked' : '');?>  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))"> 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Record of Bank 
      Change </font></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker2_relation" type="text" id="comaker2_relation" value="<?= $aLoan['comaker2_relation'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker2_address').focus();return false;}"></td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
    <td valign="top" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">No. 
      Times 
      <input name="nchangebank" type="text" id="nchangebank" value="<?= $aLoan['nchangebank'];?>" size="5" maxlength="5"  onBlur="xajax_computeMaxTerm(xajax.getFormValues('f1'))"  onKeypress="if(event.keyCode==13) {document.getElementById('date_birth').focus();return false;}">
      </font></td>
  </tr>
  <tr>
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker2_address" type="text" id="comaker2_address" value="<?= $aLoan['comaker2_address'];?>" size="35" maxlength="50" onKeypress="if(event.keyCode==13) {document.getElementById('mclass').focus();return false;}"></td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
  </tr>
</table>
