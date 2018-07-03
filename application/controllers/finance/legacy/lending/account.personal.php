  <table width="99%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td width="166"  nowrap>Birth 
      Date</td>
    <td width="519"  nowrap>
	<input name="date_birth" type="date" id="date_birth" value="<?= ($aaccount['date_birth']);?>" size="20" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');xajax_age(xajax.getFormValues('f1'))" onKeyUp="setDate(this,'MM/dd/yyyy','en')"  onKeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}">
    &nbsp;&nbsp;Age
    <input name="age" type="text" id="age" value="<?= $aaccount['age'];?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}" />
    </td>
    <td colspan="2"  nowrap><strong>Classification</strong> 
    </td>
  </tr>
  <tr> 
    <td  nowrap>Gender</td>
    <td  nowrap><?= lookUpAssoc('gender',array('Male'=>'M','Female'=>'F'),$aaccount['gender']);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Civil 
    Status 
      <?= lookUpAssoc('civil_status',array('Single'=>'S','Married'=>'M','Widow'=>'W'),$aaccount['civil_status']);?>
    </td>
    <td colspan="2"  nowrap> 
      <input type="radio" name="mclass" value="1" <?= ($aaccount['mclass'] == '1'  || $aaccount['mclass'] == ''? 'checked' : '');?>  onKeypress="if(event.keyCode==13) {document.getElementById('Save').focus();return false;}">
      Pensioner (Personal )</td>
  </tr>
  <tr> 
    <td  nowrap>&nbsp;</td>
    <td  nowrap>&nbsp;</td>
    <td colspan="2"  nowrap> 
      <input type="radio" name="mclass" value="2" <?= ($aaccount['mclass'] == '2' ? 'checked' : '');?>>
      Survivor/Widower/Beneficiary and Birthday before 21</td>
  </tr>
  <tr> 
    <td  nowrap>Member's Name</td>
    <td nowrap><input name="member" type="text" id="member" value="<?= $aaccount['member'];?>" size="40" maxlength="40"  onkeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}" /></td>
    <td width="61" nowrap align="right">1. &nbsp;</td>
    <td  nowrap>
      <input name="child1" type="text" id="child1" value="<?= $aaccount['child1'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21').focus();return false;}" /> 
      <input name="date_child21" type="date" id="date_child21" value="<?= ($aaccount['date_child21']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child2').focus();return false;}">
    </td>
  </tr>
  <tr> 
    <td  nowrap>&nbsp;</td>
    <td nowrap><i>If Survivor/Guardian</i></td>
    <td width="61" nowrap align="right">2. &nbsp;</td>
    <td  nowrap>
      <input name="child2" type="text" id="child2" value="<?= $aaccount['child2'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21b').focus();return false;}" />
      <input name="date_child21b" type="date" id="date_child21b" value="<?= ($aaccount['date_child21b']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child3').focus();return false;}">
    </td>
  </tr>
  <tr> 
    <td  nowrap valign="top">JGM Clients's Name</td>
    <td>
        <input name="firstname" type="text" id="firstname" placeholder="First name" size="40" maxlength="40"   value="<?= $aaccount['firstname'];?>" onkeypress="if(event.keyCode==13) {document.getElementById('lastname').focus();return false;}" />
        <br />
        <input name="lastname" type="text" id="lastname" placeholder="Last name" size="40"  value="<?= $aaccount['lastname'];?>" maxlength="40"  onkeypress="if(event.keyCode==13) {document.getElementById('address').focus();return false;}" />
        <br />
        <input name="middlename" type="text" id="middlename" placeholder="Middle name" size="40"  value="<?= $aaccount['middlename'];?>" maxlength="40"  onkeypress="if(event.keyCode==13) {document.getElementById('address').focus();return false;}" />
    </td>
    <td width="61" nowrap align="right">3. &nbsp;</td>
    <td  nowrap>
      <input name="child3" type="text" id="child3" value="<?= $aaccount['child3'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21c').focus();return false;}" />
      <input name="date_child21c" type="date" id="date_child21c" value="<?= ($aaccount['date_child21c']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child4').focus();return false;}">
    </td>
  </tr>
  <tr> 
    <td  nowrap>&nbsp;</td>
    <td nowrap></td>
    <td width="61" nowrap align="right">4. &nbsp;</td>
    <td  nowrap>
      <input name="child4" type="text" id="child4" value="<?= $aaccount['child4'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21d').focus();return false;}" /> 
      <input name="date_child21d" type="date" id="date_child21d" value="<?= ($aaccount['date_child21d']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('changebank').focus();return false;}">
    </td>
  </tr>
  <tr> 
    <td rowspan="3" valign="top" nowrap>Home 
      Address</td>
    <td rowspan="3" valign="top"><textarea name="address" cols="50" rows="2" id="textarea"  onKeypress="if(event.keyCode==13) {document.getElementById('telno').focus();return false;}"><?= $aaccount['address'];?></textarea></td>
    <td colspan="2" valign="top"> 
      <input type="radio" name="mclass" value="3"  <?= ($aaccount['mclass'] == '3' ? 'checked' : '');?>>
      Permanent Disability</td>
  </tr>
  <tr> 
    <td colspan="2" valign="top"> 
      <input type="radio" name="mclass" value="4"  <?= ($aaccount['mclass'] == '4' ? 'checked' : '');?>>
      Temporary Disability</td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top">Remaining 
      Pension (Months) 
      <input name="npension" type="text" id="npension" value="<?= $aaccount['npension'];?>" size="5" maxlength="5">
      </td>
  </tr>
  <tr> 
    <td height="23" nowrap>Telephone 
      (Home)</td>
    <td><input name="telno" type="text" id="telno" value="<?= $aaccount['telno'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('sss').focus();return false;}"></td>
    <td colspan="2"> <input type="radio" name="mclass" value="5"  <?= ($aaccount['mclass'] == '5' ? 'checked' : '');?>> 
       Guardian</td>
  </tr>
  <tr> 
    <td nowrap>Member SSS 
      No.</td>
    <td> 
      <input name="sss" type="text" id="sss" value="<?= $aaccount['sss'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('spouse').focus();return false;}">
       </td>
    <td colspan="2"><input type="checkbox" name="changebank"  id="changebank" value=""  <?= ($aaccount['nchangebank'] > '0' ? 'checked' : '');?> > 
       Record of Bank 
      Change </td>
  </tr>
  <tr> 
    <td nowrap>Spouse 
      Name </td>
    <td> 
      <input name="spouse" type="text" id="spouse" value="<?= $aaccount['spouse'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('spouse_sss').focus();return false;}">
      </td>
    <td>&nbsp;</td>
    <td>No. Times 
      <input name="nchangebank" type="text" id="nchangebank" value="<?= $aaccount['nchangebank'];?>" size="5" maxlength="5"  onKeypress="if(event.keyCode==13) {document.getElementById('tab2').focus();return false;}">
      </td>
  </tr>
  <tr> 
    <td nowrap>Spouse 
      SSS No.</td>
    <td colspan="3"> 
      <input name="spouse_sss" type="text" id="spouse_sss" value="<?= $aaccount['spouse_sss'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('remarks').focus();return false;}">
      </td>
  </tr>
  <tr> 
    <td valign="top" nowrap>Remarks</td>
    <td colspan="3" ><textarea name="remarks" cols="50" rows="2" id="remarks"><?= stripslashes($aaccount['remarks']);?></textarea> 
    </td>
  </tr>
</table>
</form>
