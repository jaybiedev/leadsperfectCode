  <table width="99%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td width="166"  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Birth 
      Date</font></td>
    <td width="441"  nowrap>
	<input name="date_birth" type="text" id="date_birth" value="<?= ymd2mdy($aaccount['date_birth']);?>" size="20" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');xajax_age(xajax.getFormValues('f1'))" onKeyUp="setDate(this,'MM/dd/yyyy','en')"  onKeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}"> 
    <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_birth, 'mm/dd/yyyy')">&nbsp;&nbsp;Age 
    <input name="age" type="text" id="age" value="<?= $aaccount['age'];?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}" />
    </font></td>
    <td colspan="2"  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Classification</strong></font> 
    </td>
  </tr>
  <tr> 
    <td  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gender</font></td>
    <td  nowrap><?= lookUpAssoc('gender',array('Male'=>'M','Female'=>'F'),$aaccount['gender']);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Civil 
    Status 
      <?= lookUpAssoc('civil_status',array('Single'=>'S','Married'=>'M','Widow'=>'W'),$aaccount['civil_status']);?>
    </font></td>
    <td colspan="2"  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="1" <?= ($aaccount['mclass'] == '1'  || $aaccount['mclass'] == ''? 'checked' : '');?>  onKeypress="if(event.keyCode==13) {document.getElementById('Save').focus();return false;}">
      Pensioner (Personal )</font></td>
  </tr>
  <tr> 
    <td  nowrap>&nbsp;</td>
    <td  nowrap>&nbsp;</td>
    <td colspan="2"  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="2" <?= ($aaccount['mclass'] == '2' ? 'checked' : '');?>>
      Survivor/Widower/Beneficiary</font></td>
  </tr>
  <tr> 
    <td  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Member's Name</font></td>
    <td nowrap><input name="member" type="text" id="member" value="<?= $aaccount['member'];?>" size="40" maxlength="40"  onkeypress="if(event.keyCode==13) {document.getElementById('age').focus();return false;}" /></td>
    <td width="66" nowrap align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">1. &nbsp;</font></td>
    <td width="610" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="child1" type="text" id="child1" value="<?= $aaccount['child1'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21').focus();return false;}" /> 
      B-Day before 21 
      <input name="date_child21" type="text" id="date_child21" value="<?= ymd2mdy($aaccount['date_child21']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child2').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_child21, 'mm/dd/yyyy')"> 
    </font></td>
  </tr>
  <tr> 
    <td  nowrap>&nbsp;</td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><i>If Survivor/Guardian</i></font></td>
    <td width="66" nowrap align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">2. &nbsp;</font></td>
    <td width="610" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="child2" type="text" id="child2" value="<?= $aaccount['child2'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21b').focus();return false;}" />
       
      B-Day before 21 
      <input name="date_child21b" type="text" id="date_child21b" value="<?= ymd2mdy($aaccount['date_child21b']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child3').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_child21b, 'mm/dd/yyyy')"> 
    </font></td>
  </tr>
  <tr> 
    <td  nowrap style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">JGM Clients's Name</td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="firstname" type="text" readonly="" id="firstname" value="<?= $aaccount['firstname'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('lastname').focus();return false;}" />
    </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
    <input name="lastname" type="text" readonly id="lastname" value="<?= $aaccount['lastname'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('address').focus();return false;}" />
    </font></td>
    <td width="66" nowrap align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">3. &nbsp;</font></td>
    <td width="610" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="child3" type="text" id="child3" value="<?= $aaccount['child3'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21c').focus();return false;}" />
       
      B-Day before 21 
      <input name="date_child21c" type="text" id="date_child21c" value="<?= ymd2mdy($aaccount['date_child21c']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('child4').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_child21c, 'mm/dd/yyyy')"> 
    </font></td>
  </tr>
  <tr> 
    <td  nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td nowrap style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;&nbsp;&nbsp;&nbsp;<i>First Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Last Name</i></td>
    <td width="66" nowrap align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">4. &nbsp;</font></td>
    <td width="610" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="child4" type="text" id="child4" value="<?= $aaccount['child4'];?>" size="30" maxlength="30"  onkeypress="if(event.keyCode==13) {document.getElementById('date_child21d').focus();return false;}" /> 
      B-Day before 21 
      <input name="date_child21d" type="text" id="date_child21d" value="<?= ymd2mdy($aaccount['date_child21d']);?>" size="10" maxlength="10"  onBlur="IsValidDate(this,'MM/dd/yyyy');>"  onKeypress="if(event.keyCode==13) {document.getElementById('changebank').focus();return false;}">
      <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f1.date_child21d, 'mm/dd/yyyy')"> 
    </font></td>
  </tr>
  <tr> 
    <td rowspan="3" valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Home 
      Address</font></td>
    <td rowspan="3" valign="top"><textarea name="address" cols="50" rows="2" id="textarea"  onKeypress="if(event.keyCode==13) {document.getElementById('telno').focus();return false;}"><?= $aaccount['address'];?></textarea></td>
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="3"  <?= ($aaccount['mclass'] == '3' ? 'checked' : '');?>>
      Permanent Disability</font></td>
  </tr>
  <tr> 
    <td colspan="2" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input type="radio" name="mclass" value="4"  <?= ($aaccount['mclass'] == '4' ? 'checked' : '');?>>
      Temporary Disability</font></td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remaining 
      Pension (Months) 
      <input name="npension" type="text" id="npension" value="<?= $aaccount['npension'];?>" size="5" maxlength="5">
      </font></td>
  </tr>
  <tr> 
    <td height="23" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone 
      (Home)</font></td>
    <td><input name="telno" type="text" id="telno" value="<?= $aaccount['telno'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('sss').focus();return false;}"></td>
    <td colspan="2"> <input type="radio" name="mclass" value="5"  <?= ($aaccount['mclass'] == '5' ? 'checked' : '');?>> 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Guardian</font></td>
  </tr>
  <tr> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Member SSS 
      No.</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="sss" type="text" id="sss" value="<?= $aaccount['sss'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('spouse').focus();return false;}">
      </font> </td>
    <td colspan="2"><input type="checkbox" name="changebank"  id="changebank" value=""  <?= ($aaccount['nchangebank'] > '0' ? 'checked' : '');?> > 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Record of Bank 
      Change </font></td>
  </tr>
  <tr> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Spouse 
      Name </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="spouse" type="text" id="spouse" value="<?= $aaccount['spouse'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('spouse_sss').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">No. Times 
      <input name="nchangebank" type="text" id="nchangebank" value="<?= $aaccount['nchangebank'];?>" size="5" maxlength="5"  onKeypress="if(event.keyCode==13) {document.getElementById('tab2').focus();return false;}">
      </font></td>
  </tr>
  <tr> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Spouse 
      SSS No.</font></td>
    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="spouse_sss" type="text" id="spouse_sss" value="<?= $aaccount['spouse_sss'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('remarks').focus();return false;}">
      </font></td>
  </tr>
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
    <td colspan="3" ><textarea name="remarks" cols="50" rows="2" id="remarks"><?= stripslashes($aaccount['remarks']);?></textarea> 
    </td>
  </tr>
</table>
</form>
