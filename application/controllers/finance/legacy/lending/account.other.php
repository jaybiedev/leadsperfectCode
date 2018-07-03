 <table width="1%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Employer/Ofc. 
      </font></td>
    <td><input name="office" type="text" id="office" value="<?= $aaccount['office'];?>" size="52" maxlength="50"  onKeypress="if(event.keyCode==13) {document.getElementById('ofc_address').focus();return false;}"></td>
  </tr>
  <tr valign="top"> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Office 
      Address</font></td>
    <td><textarea name="ofc_address" id="ofc_address" cols="40" rows="2" tabindex='60'><?= $aaccount['ofc_address'];?></textarea></td>
  </tr>
  <tr> 
    <td height="25" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone</font></td>
    <td><input name="ofc_telno" type="text" id="ofc_telno"  tabindex="61" value="<?= $aaccount['ofc_telno'];?>" size="15" maxlength="15"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker1').focus();return false;}"></td>
  </tr>
  <tr> 
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker1</font></td>
    <td nowrap><input name="comaker1" type="text" id="comaker1" value="<?= $aaccount['comaker1'];?>" size="50" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker1_relation').focus();return false;}">
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Relationship </font>
<input name="comaker1_relation" type="text" id="comaker1_relation" value="<?= $aaccount['comaker1_relation'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker1_address').focus();return false;}"></td>
  </tr>
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td><input name="comaker1_address" type="text" id="comaker1_address" value="<?= $aaccount['comaker1_address'];?>" size="50" maxlength="50"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker2').focus();return false;}">
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
  </tr>
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker2</font></td>
    <td nowrap><input name="comaker2" type="text" id="comaker2" value="<?= $aaccount['comaker2'];?>" size="50" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker2_relation').focus();return false;}">
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship 
      <input name="comaker2_relation" type="text" id="comaker2_relation" value="<?= $aaccount['comaker2_relation'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker2_address').focus();return false;}">
      </font> </td>
  </tr>
  <tr> 
    <td valign="top" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td><input name="comaker2_address" type="text" id="comaker2_address" value="<?= $aaccount['comaker2_address'];?>" size="50" maxlength="50"  onKeypress="if(event.keyCode==13) {document.getElementById('tab3').focus();return false;}"></td>
  </tr>
  <tr> 
    <td valign="top" nowrap>&nbsp;</td>
    <td> 
      <input name="picture" type="hidden" id="picture" value="<?= $picture;?>" size="15" maxlength="15"></td>
  </tr>
</table>
</form> 