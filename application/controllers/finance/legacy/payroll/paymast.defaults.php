<?
if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","madd",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id']))
{
?>
    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr bgcolor="#F0F0F0"> 
    <td width="14%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Laundry Allowance </font></td>
    <td width="29%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="laundry" type="text" id="laundry" value="<?= $paymast[laundry];?>" size="15"  onkeypress="if(event.keyCode==13) {document.getElementById('uniform').focus();return false;}" />
    </font></td>
    <td width="17%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Meal Benefit </font></td>
    <td width="40%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="meal" type="text" id="meal" value="<?= $paymast['meal'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('management').focus();return false;}" />
    </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Uniform Allowance </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="uniform" type="text" id="uniform" value="<?= $paymast[uniform];?>" size="15"  onkeypress="if(event.keyCode==13) {document.getElementById('rice').focus();return false;}" />
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Management Allowance</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="management" type="text" id="management" value="<?= $paymast['management'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('tenure').focus();return false;}" />
    </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Rice Allowance </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="rice" type="text" id="rice" value="<?= $paymast[rice];?>" size="15"  onkeypress="if(event.keyCode==13) {document.getElementById('medical').focus();return false;}" />
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tenure Incentive</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="tenure" type="text" id="tenure" value="<?= $paymast['tenure'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('dcola').focus();return false;}" />
    </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Medical Allowance </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="medical" type="text" id="medical" value="<?= $paymast[medical];?>" size="15"  onkeypress="if(event.keyCode==13) {document.getElementById('medepend').focus();return false;}" />
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">COLA / CTPA </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="dcola" type="text" id="dcola" value="<?= $paymast['dcola'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('transpo').focus();return false;}" />
    </font>
</td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Med. Allowance for <br />Dependents
      
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="medepend" type="text" id="medepend" value="<?= $paymast['medepend'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('meal').focus();return false;}" />
    </font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Transpo Allowance</font>&nbsp;</td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="transpo" type="text" id="transpo" value="<?= $paymast['transpo'];?>" size="15" onkeypress="if(event.keyCode==13) {document.getElementById('transpo').focus();return false;}" />
    </font></td>
  </tr>
</table>
<?
}
?>