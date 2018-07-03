  <table width="91%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td width="11%" nowrap>Group</td>
    <td width="38%"> 
      <?= lookUpTable3('account_group_id','account_group','account_group_id','account_group',$aaccount['account_group_id']);?>
      </td>
    <td colspan="2" rowspan="5" valign="top"> 
      <?
	  if (chkRights3('bank_pin','madd',$ADMIN['admin_id']) || chkRights3('bank_pin','medit',$ADMIN['admin_id']))
	  {
	  ?>
      <strong>Priviledged 
      Info</strong> 
      <table width="100%" border=0 cellpadding="0" cellspacing="1">
        <tr> 
          <td width="25%">ATM 
            Card#</td>
          <td width="75%"><input name="bank_cardno" type="text" id="bank_cardno" value="<?= $aaccount['bank_cardno'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_in').focus();return false;}"></td>
        </tr>
        <tr> 
          <td>ATM 
            In</td>
          <td><input name="date_atm_in" type="text" id="date_atm_in" value="<?= ymd2mdy($aaccount['date_atm_in']);?>" size="8" maxlength="10"  onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_out').focus();return false;}"> 
            <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date_atm_in, 'mm/dd/yyyy')"></td>
        </tr>
        <tr> 
          <td>ATM 
            Out</td>
          <td><input name="date_atm_out" type="text" id="date_atm_out" value="<?= ymd2mdy($aaccount['date_atm_out']);?>" size="8" maxlength="10"  onKeypress="if(event.keyCode==13) {document.getElementById('bank_pin').focus();return false;}"> 
            <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date_atm_out, 'mm/dd/yyyy')"></td>
        </tr>
        <tr> 
          <td>PIN 
          </td>
          <td> 
		  <?
		  if ($ADMIN[usergroup]=='A'  or (chkRights3('bank_pin','medit',$ADMIN['admin_id']) and chkRights3('bank_pin','mview',$ADMIN['admin_id'])))
		  {
		  ?>
            <input name="bank_pin" type="text" id="bank_pin" value="<?= $aaccount['bank_pin'];?>" size="6" maxlength="8"  onKeypress="if(event.keyCode==13) {document.getElementById('withdraw_day').focus();return false;}">
		  <?
		  } else
		  {
		  ?>
            <input name="bank_pin" type="text" id="bank_pin" value="<?= $bank_pin;?>" size="6" maxlength="8"  onKeypress="if(event.keyCode==13) {document.getElementById('withdraw_day').focus();return false;}">
		  <?	
		  }
		  ?>	
          Wday: Original  
		  <?
		  if (chkRights4('account','mdelete',$ADMIN['admin_id']) or $aaccount['withdraw_day']==0)
		  { 
			?>					
             <input name="withdraw_day" type="text" id="withdraw_day" value="<?= $aaccount['withdraw_day'];?>" size="2" maxlength="2"  onKeypress="if(event.keyCode==13) {document.getElementById('current_day').focus();return false;}">
			<?
		  } 
		  else
		  {
			?>
			<input name="withdraw_day" readonly id="withdraw_day" value="<?= $aaccount['withdraw_day'];?>" size="2" maxlength="2" />			
			<?
		  }
			?>Current 
            <input name="current_day" type="text" id="current_day" value="<?= $aaccount['current_day'];?>" size="2" maxlength="2"  onKeypress="if(event.keyCode==13) {document.getElementById('bank_cardno').focus();return false;}">
            </td>
        </tr>
		  <?
		  if ($ADMIN[usergroup]=='A' or (chkRights3('bank_pin','medit',$ADMIN['admin_id']) and chkRights3('bank_pin','mview',$ADMIN['admin_id'])))
		  {
		  ?>		
			<tr><td>Account#</td>
			<td><input name="bank_account" type="text" id="bank_account" value="<?= $aaccount['bank_account'];?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('salary').focus();return false;}" /></td>
			</tr>
		  <?	
		  }	
		  elseif (chkRights3('bank_pin','madd',$ADMIN['admin_id']) || chkRights3('bank_pin','medit',$ADMIN['admin_id']))
		  {
		  ?>		
			<tr><td>Account#</td>
			<td><input name="bank_account" type="text" id="bank_account" value="<?= $bank_account;?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('salary').focus();return false;}" /></td>
			</tr>
		  <?	
		  }	
		  ?>
      </table>
      <?
	  }
	  elseif (chkRights3('bank_pin','mview',$ADMIN['admin_id']) or $ADMIN[usergroup]=='Q')
	  {
	  ?>
      <strong>Priviledged 
      Info</strong> 
      <table width="100%" border=0 cellpadding="0" cellspacing="1">
        <tr> 
          <td width="33%">ATM 
            Card#</td>
          <td width="67%"><input name="bank_cardno" type="text" id="bank_cardno" value="<?= $aaccount['bank_cardno'];?>" size="20" maxlength="20"  onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_in').focus();return false;}"></td>
        </tr>
        <tr> 
          <td>ATM 
            In</td>
          <td><input name="date_atm_in" type="text" readOnly id="date_atm_in" value="<?= ymd2mdy($aaccount['date_atm_in']);?>" size="8" maxlength="10"  onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_out').focus();return false;}"> 
            <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date_atm_in, 'mm/dd/yyyy')"></td>
        </tr>
        <tr> 
          <td>ATM 
            Out</td>
          <td><input name="date_atm_out" type="text" readOnly id="date_atm_out" value="<?= ymd2mdy($aaccount['date_atm_out']);?>" size="8" maxlength="10"  onKeypress="if(event.keyCode==13) {document.getElementById('bank_pin').focus();return false;}"> 
            <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.date_atm_out, 'mm/dd/yyyy')"></td>
        </tr>
        <tr> 
          <td>PIN 
          </td>
          <td> 
            <input name="bank_pin" type="hidden" id="bank_pin" readOnly value="<?= $aaccount['bank_pin'];?>" size="6" maxlength="8"  onKeypress="if(event.keyCode==13) {document.getElementById('withdraw_day').focus();return false;}">
            Withday 
            <input name="withdraw_day" type="text" id="withdraw_day" readOnly value="<?= $aaccount['withdraw_day'];?>" size="2" maxlength="2"  onKeypress="if(event.keyCode==13) {document.getElementById('current_day').focus();return false;}">
            Curday 
            <input name="current_day" type="text" id="current_day" readonly="readOnly" value="<?= $aaccount['current_day'];?>" size="2" maxlength="2"  onkeypress="if(event.keyCode==13) {document.getElementById('current_day').focus();return false;}" />
          </td>
        </tr>
		<tr><td>Account#</td>
		<td><input name="bank_account" type="text" id="bank_account" readonly value="<?= $aaccount['bank_account'];?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('salary').focus();return false;}" /></td>
		</tr>
      </table>
      <?
		}
		else
		{
		?>
      <input name="bank_cardno" type="hidden" id="bank_cardno" value="<?= $aaccount['bank_cardno'];?>" size="20" maxlength="20" onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_out').focus();return false;}">	
      <input name="date_atm_out" type="hidden" id="date_atm_out" value="<?= ymd2mdy($aaccount['date_atm_out']);?>" size="8" maxlength="10" onKeypress="if(event.keyCode==13) {document.getElementById('date_atm_in').focus();return false;}"> 
      <input name="date_atm_in" type="hidden" id="date_atm_in" value="<?= ymd2mdy($aaccount['date_atm_in']);?>" size="8" maxlength="10" onKeypress="if(event.keyCode==13) {document.getElementById('bank_pin').focus();return false;}"> 
      <input name="bank_pin" type="hidden" id="bank_pin" value="<?= $aaccount['bank_pin'];?>" size="6" maxlength="8" onKeypress="if(event.keyCode==13) {document.getElementById('withdraw_day').focus();return false;}"> 
      <input name="withdraw_day" type="hidden" id="withdraw_day" value="<?= $aaccount['withdraw_day'];?>" size="2" maxlength="2" onKeypress="if(event.keyCode==13) {document.getElementById('current_day').focus();return false;}">
      <input name="current_day" type="hidden" id="current_day" value="<?= $aaccount['current_day'];?>" size="2" maxlength="2" onKeypress="if(event.keyCode==13) {document.getElementById('smartno').focus();return false;}"/> 
      <input name="bank_account" type="hidden" id="bank_account" value="<?= $aaccount['bank_account'];?>" size="2" maxlength="2" onKeypress="if(event.keyCode==13) {document.getElementById('smartno').focus();return false;}"/> 
      <?
		}
		?>    </td>
  </tr>
  <tr> 
    <td nowrap>Type</td>
    <td> 
      <?= lookUpTable2('collection_type_id','collection_type','collection_type_id','collection_type',$aaccount['collection_type_id']);?>
      </td>
  </tr>
  <tr> 
    <td nowrap>Client 
      Bank </td>
    <td>  
      <select name="clientbank_id"  onKeypress="if(event.keyCode==13) {document.getElementById('bank_account').focus();return false;}" style="height:25px;width:350px;">
        <option value="0">Select Client Bank</option>
        <?
		$q = "select * from clientbank where enable order by clientbank, clientbank_address";
		$qr = @pg_query($q) or message(pg_errormessage());
		
		while ($r = @pg_fetch_object($qr))
		{
			if ($aaccount['clientbank_id'] == $r->clientbank_id)
			{
				echo "<option value=$r->clientbank_id selected>$r->clientbank-$r->clientbank_address</option>";
			}
			else
			{
				echo "<option value=$r->clientbank_id>$r->clientbank-$r->clientbank_address</option>";
			}	
		}
		?>
      </select>
       </td>
  </tr>
  <tr> 
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td nowrap>Salary/Pension <br />Including EC</td>
    <td> <input name="salary" type="text" id="salary" value="<?= $aaccount['salary'];?>" size="20" maxlength="20"  onBlur="checknumber(this)" style="text-align:right"  onKeypress="if(event.keyCode==13) {document.getElementById('ecamount').focus();return false;}"></td>
  </tr>
  <tr> 
    <td nowrap>EC Only</td>
    <td> <input name="ecamount" type="text" id="ecamount" value="<?= $aaccount['ecamount'];?>" size="12" maxlength="20"  onBlur="checknumber(this)" style="text-align:right"  onKeypress="if(event.keyCode==13) {document.getElementById('disability').focus();return false;}"></td>  </tr>
  <tr> 
    <td nowrap>Disability Only</td>
    <td> <input name="disability" type="text" id="disability" value="<?= $aaccount['disability'];?>" size="12" maxlength="20"  onBlur="checknumber(this)" style="text-align:right"  onKeypress="if(event.keyCode==13) {document.getElementById('bank_cardno').focus();return false;}"></td>    <td width="9%">&nbsp;</td>
    <td width="42%"><?
	  if (chkRights3('smartno','madd',$ADMIN['admin_id']) || chkRights3('smartno','medit',$ADMIN['admin_id']))
	  {
	  ?>
      <strong>Security Smart Card No: 
      <input name="smartno" type="text" id="smartno" value="<?= $aaccount['smartno'];?>" size="20" maxlength="20"  onkeypress="if(event.keyCode==13) {document.getElementById('date_atm_in').focus();return false;}" />
    </strong></td>
  </tr>
  <tr valign="top"> 
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="Submit" name="p3" value="Clear Pin"/></td>
    <?
		} else 
		{
		?>
      <input name="smartno" type="hidden" id="smartno" value="<?= $aaccount['smartno'];?>" size="20" maxlength="20" />
	  <?
	  }
	  ?>
  </tr>
  <tr valign="top"> 
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
