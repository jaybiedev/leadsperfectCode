<?
if (chkRights2("paymast","mview",$ADMIN['admin_id']) or chkRights2("paymast","medit",$ADMIN['admin_id']) or chkRights2("paymast","mdelete",$ADMIN['admin_id']))
{
?>
    <table width="91%" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr bgcolor="#F0F0F0"> 
    <td width="9%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Birth 
      Date</font></td>
    <td width="30%" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; vertical-align:text-top;"> <input name="date_birth" type="text" id="date_birth" value="<?= ymd2mdy($paymast['date_birth']);?>" size="12" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')"  onKeypress="if(event.keyCode==13) {document.getElementById('gender').focus();return false;}"> 
      <img src="../graphics/dwn-arrow-grn.gif" width="12" height="12" onClick="popUpCalendar(this, f1.date_birth, 'mm/dd/yyyy')">&nbsp;&nbsp;&nbsp;
	  Age : <?=$age;?> 
    </td>
    <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">SSS 
      Id</font></td>
    <td width="26%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="sssid" type="text" id="sssid" value="<?= $paymast['sssid'];?>" size="15"  onKeypress="if(event.keyCode==13) {document.getElementById('phicid').focus();return false;}">
      <?=checkBox('sssw',$paymast['sssw'],true);?>
    Deduct SSS </font></td>
	<td width="24%" rowspan="8" align="center" valign="middle" bgcolor="#FFFFFF"><img src="photo/<?= $paymast['idnum'].'.jpg';?>" name="pix" width="200" height="200"><br></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gender</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpAssoc('sex',array('Male'=>'1','Female'=>'2'),$paymast['sex']);?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">PhilHealth 
      Id</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="phicid" type="text" id="phicid" value="<?= $paymast['phicid'];?>" size="15"  onKeypress="if(event.keyCode==13) {document.getElementById('pagibigid').focus();return false;}">
      <?=checkBox('phicw',$paymast['phicw'],true);?>
      Deduct PHIC </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Civil Status</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpAssoc('civil_status',array('Single'=>'1','Married'=>'2','Widowed'=>'3','Legally Separated'=>'4'),$paymast['civil_status']);?>
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Pag-Ibig Id</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="pagibigid" type="text" id="pagibigid" value="<?= $paymast['pagibigid'];?>" size="15" onKeypress="if(event.keyCode==13) {document.getElementById('tin').focus();return false;}">
      <?=checkBox('pagibigw',$paymast['pagibigw'],true);?>
      Deduct PabIbig </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Telephone</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="telno" type="text" id="telno" value="<?= $paymast['telno'];?>" size="15"  onKeypress="if(event.keyCode==13) {document.getElementById('mobile').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">TIN</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="tin" type="text" id="tin" value="<?= $paymast['tin'];?>" size="15"  onKeypress="if(event.keyCode==13) {document.getElementById('taxcode').focus();return false;}">
      <?=checkBox('taxw',$paymast['taxw'],true);?>
      Deduct WTax </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mobile</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="mobile" type="text" id="mobile" value="<?= $paymast['mobile'];?>" size="15" onKeypress="if(event.keyCode==13) {document.getElementById('address).focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">TaxCode</font></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="taxcode" type="text" id="taxcode" value="<?= $paymast['taxcode'];?>" size="15" onKeypress="if(event.keyCode==13) {document.getElementById('atm').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#F0F0F0"> 
    <td rowspan="3" valign="top" bgcolor="#F0F0F0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td rowspan="3" valign="top">
      <textarea name="address" cols="35" id="address"><?= $paymast['address'];?></textarea>
    </td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ATM</font><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="atm" type="text" id="atm" value="<?= $paymast['atm'];?>" size="15"  onKeypress="if(event.keyCode==13) {document.getElementById('date_employ').focus();return false;}">
      </font></td>
  </tr>
  <tr bgcolor="#E2E2E2"> 
    <td bgcolor="#F0F0F0">&nbsp;</td>
    <td bgcolor="#F0F0F0">&nbsp;</td>
  </tr>
  <tr bgcolor="#E2E2E2"> 
    <td bgcolor="#F0F0F0">&nbsp;</td>
    <td bgcolor="#F0F0F0">&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#F0F0F0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></td>
    <td colspan="4" bgcolor="#F0F0F0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <textarea name="remarks" id="remarks" cols="60"><?= $paymast['remarks'];?></textarea>
      </font></td>
  </tr>
  <tr> 
    <td bgcolor="#F0F0F0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Record 
      Enabled </font></td>
    <td colspan="4" bgcolor="#F0F0F0"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= lookUpAssoc("enable",array("Yes"=>"Y","No"=>"N"),$paymast['enable']);?>
      </font></td>
  </tr>
</table>
<?
}
?>