  <table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td width="21%" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <select name="comake3_id" style="width:250px" onkeypress="if(event.keyCode==13) {document.getElementById('comaker3').focus();return false;}">
        <option value="0">Select Co-Maker3 link</option>
        <?
		$q = "select * from account where enable and branch_id='".$aLoan['branch_id']."' order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aComaker['comake3_id'] == $r->account_id)
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
    <td width="11%" bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker3</font></td>
    <td width="23%" bgcolor="#EFEFEF"><input name="comake3" type="text" id="comake3" value="<?= $aComaker['comake3'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comake3_relation').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;&nbsp;&nbsp;Co-Maker1</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake1" type="text" id="comake1" readonly value="<?= $aComaker['comake1'];?>" size="35" maxlength="35" />
    <input name="comake1_id" type="hidden" id="comake1_id" readonly="readonly" value="<?= $aComaker['comake1_id'];?>" size="35" maxlength="35" /></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship</font></td>
    <td bgcolor="#EFEFEF"><input name="comake3_relation" type="text" id="comake3_relation" value="<?= $aComaker['comake3_relation'];?>" size="35" maxlength="50"   onKeypress="if(event.keyCode==13) {document.getElementById('comake3_address').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake1_relation" type="text" readonly id="comake1_relation" value="<?= $aComaker['comake1_relation'];?>" size="35" maxlength="50" /></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td bgcolor="#EFEFEF"><input name="comake3_address" type="text" id="comake3_address" value="<?= $aComaker['comake3_address'];?>" size="35" maxlength="50"   onKeypress="if(event.keyCode==13) {document.getElementById('comake3').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake1_address" readonly="" type="text" id="comake1_address" value="<?= $aComaker['comake1_address'];?>" size="50" maxlength="50"    /></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <select name="comake4" style="width:250px" onkeypress="if(event.keyCode==13) {document.getElementById('comake4').focus();return false;}">
        <option value="0">Select Co-Maker4 link</option>
        <?
		$q = "select * from account where enable and branch_id='".$aLoan['branch_id']."' order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aComaker['comake4_id'] == $r->account_id)
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
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker4</font></td>
    <td bgcolor="#EFEFEF"><input name="comake4" type="text" id="comake4" value="<?= $aComaker['comake4'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comake4_relation').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;&nbsp;&nbsp;Co-Maker2</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake2" type="text" id="comake2" readonly="readonly" value="<?= $aComaker['comake2'];?>" size="35" maxlength="35" />
    <input name="comake2_id" type="hidden" id="comake2_id" readonly="readonly" value="<?= $aComaker['comake2_id'];?>" size="35" maxlength="35" /></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship</font></td>
    <td bgcolor="#EFEFEF"><input name="comake4_relation" type="text" id="comake4_relation" value="<?= $aComaker['comake4_relation'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comake4_address').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake2_relation" readonly type="text" id="comake2_relation" value="<?= $aComaker['comake2_relation'];?>" size="35" maxlength="50" /></td>
  </tr>
  <tr>
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td bgcolor="#EFEFEF"><input name="comake4_address" type="text" id="comake4_address" value="<?= $aComaker['comake4_address'];?>" size="35" maxlength="50" onKeypress="if(event.keyCode==13) {document.getElementById('comake5_id').focus();return false;}"></td>
    <td width="8%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px">&nbsp;</td>
    <td width="37%" bgcolor="#EFEFEF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px"><input name="comake2_address" readonly type="text" id="comake2_address" value="<?= $aComaker['comake2_address'];?>" size="50" maxlength="50"></td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <select name="comake2" style="width:250px" onkeypress="if(event.keyCode==13) {document.getElementById('comaker5').focus();return false;}">
        <option value="0">Select Co-Maker5 link</option>
        <?
		$q = "select * from account where enable and branch_id='".$aLoan['branch_id']."' order by account";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aComaker['comake5_id'] == $r->account_id)
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
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Co-Maker5</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker5" type="text" id="comaker5" value="<?= $aComaker['comaker5'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker5_relation').focus();return false;}"></td>
    <td colspan="2" valign="top" bgcolor="#EFEFEF">&nbsp;</td>
  </tr>
  <tr> 
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Relationship</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker5_relation" type="text" id="comaker5_relation" value="<?= $aComaker['comaker5_relation'];?>" size="35" maxlength="35"  onKeypress="if(event.keyCode==13) {document.getElementById('comaker5_address').focus();return false;}"></td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#EFEFEF">&nbsp;</td>
    <td bgcolor="#EFEFEF"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Address</font></td>
    <td bgcolor="#EFEFEF"><input name="comaker5_address" type="text" id="comaker5_address" value="<?= $aComaker['comaker5_address'];?>" size="35" maxlength="50" onKeypress="if(event.keyCode==13) {document.getElementById('comake3_id').focus();return false;}"></td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
    <td valign="top" bgcolor="#EFEFEF">&nbsp;</td>
  </tr>
</table>
