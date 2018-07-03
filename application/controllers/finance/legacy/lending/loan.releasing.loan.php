  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td width="17%" height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></td>
    <td width="18%"><select name="loan_type_id" onBlur="xajax_loanfees(xajax.getFormValues('f1'))" id="loan_type_id"  style="width:180"   onKeypress="if(event.keyCode==13) {document.getElementById('term').focus();return false;}">
        <option value=''>Select</option>
        <?
	  $qr = pg_query("select * from loan_type where enable");
	  while ($r=pg_fetch_object($qr))
	  {
	  	if ($aLoan['loan_type_id'] == $r->loan_type_id)
		{
		  	echo "<option value=$r->loan_type_id selected>$r->loan_type</option>";
		}
		else
		{
		  	echo "<option value=$r->loan_type_id>$r->loan_type</option>";
		}	
	  }
	  ?>
    </select></td>
    <td width="16%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Service 
      Charge</font></td>
    <td width="11%"><input name="service_charge" type="text" id="service_charge"  style="text-align:right"  tabindex="15"  value="<?= $aLoan['service_charge'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('collection_fee').focus();return false;}"></td>
    <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">ATM 
      Charges </font></td>
    <td width="22%"><input name="atm_charge" type="text" id="atm_charge"  style="text-align:right"  tabindex="19"  value="<?= $aLoan['atm_charge'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('other_charges').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mode</font></td>
    <td> <select name="mode" id="mode" onBlur="if (term.value > max_term.value){xajax_checkOverride(xajax.getFormValues('f1'))};xajax_loanfees(xajax.getFormValues('f1'))" style="width:180">
        <option value='M' <?= ($aLoan['mode']=='M' ? 'selected':'');?>>Monthly</option>
        <option value='S' <?= ($aLoan['mode']=='S' ? 'selected':'');?>>Semi-Monthly</option>
        <option value='W' <?= ($aLoan['mode']=='W' ? 'selected':'');?>>Weekly</option>
      </select> </td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Collection 
      Fee</font></td>
    <td><input name="collection_fee" type="text" id="collection_fee" style="text-align:right"   tabindex="16"  value="<?= $aLoan['collection_fee'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('referral_fee').focus();return false;}"></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Other Charges 
      </font></td>
    <td><input name="other_charges" type="text" id="other_charges"  style="text-align:right"  tabindex="21"  value="<?= $aLoan['other_charges'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"  onKeypress="if(event.keyCode==13) {document.getElementById('other_remarks').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Rate</font></td>
    <td><input name="rate" type="text" tabindex="10" style="text-align:right" value="<?= $aLoan['rate'];?>" id="rate" size="10" onChange="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('term').focus();return false;}">
      / 
      <input name="rate_basis" type="text" id="rate_basis" readonly value="<?= $aLoan['rate_basis'];?>" size="3" style="text-align:left;background:none;border:none">    </td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Referral Fee</font></td>
    <td><input name="referral_fee" type="text" id="referral_fee"  tabindex="17"  style="text-align:right"  value="<?= $aLoan['referral_fee'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('printout').focus();return false;}"></td>
    <td colspan="2" rowspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">.:: 
      Remarks<br>
      <textarea name="other_remarks"  tabindex="22"  cols="30" rows="2" id="other_remarks"  onKeypress="if(event.keyCode==13) {document.getElementById('advance_payment').focus();return false;}"><?= $aLoan['other_remarks'];?></textarea>
      </font></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Term/mos.</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="term" type="text"  tabindex="11" style="text-align:right"   value="<?= $aLoan['term'];?>" id="term" size="10"  onBlur="if (term.value > max_term.value){xajax_checkOverride(xajax.getFormValues('f1'))};xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('principal').focus();return false;}">
<!--       <input name="term" type="text"  tabindex="11" style="text-align:right"   value="<?= $aLoan['term'];?>" id="term" size="10"  onChange="checkTerm(<?=chkRights3('loanoverride','madd',$ADMIN['admin_id']);?>);xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('principal').focus();return false;}">-->
      <input type="button" name="Button" value="~" onClick="vRequestOverride()" alt="Click To Request or Check for Term Override..." onmouseover="showToolTip(event,'Click To Request Term Override and Click Again To Check for Term Override Approval...');return false" onmouseout="hideToolTip()">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Printout</font></td>
    <td><input name="printout" type="text" id="printout"  tabindex="17"  style="text-align:right"  value="<?= $aLoan['printout'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('photo').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Principal 
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="principal" type="text" id="principal"  style="text-align:right"  tabindex="13"  value="<?= $aLoan['principal'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'));document.getElementById('referral_fee').focus();return false;"  onKeypress="if(event.keyCode==13) {document.getElementById('referral_fee').focus();return false;}">
      <input name="salary" type="hidden" id="salary"  style="text-align:right"  tabindex="13"  value="<?= $aLoan['salary'];?>" size="10"  />
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Photo</font></td>
    <td><input name="photo" type="text" id="photo"  tabindex="18"  style="text-align:right"  value="<?= $aLoan['photo'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('ded_insure').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Ammortization</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="ammort" type="text" id="ammort"  readOnly style="text-align:right"  tabindex="14"  value="<?= $aLoan['ammort'];?>" size="10"    onKeypress="if(event.keyCode==13) {document.getElementById('withdraw_day').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Insurance Deduction  </font></td>
    <td><input name="ded_insure" type="text" readonly id="ded_insure"  tabindex="18"  style="text-align:right"  value="<?= $aLoan['ded_insure'];?>" size="10" onblur="xajax_loanfees(xajax.getFormValues('f1'))"   onkeypress="if(event.keyCode==13) {document.getElementById('atm_charge').focus();return false;}" /></td>
    <td nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advance 
      Payment </font></td>
    <td><input name="advance_payment" type="text" id="advance_payment"  tabindex="23"   style="text-align:right" value="<?= $aLoan['advance_payment'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('interest').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Withdrawal Day</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<?
		if ($ADMIN[admin_id]!=581 and $ADMIN[admin_id]!=1) $admact='readonly';
	?>	
      <input name="withdraw_day" type="text" id="withdraw_day" <?=$admact;?> style="text-align:right"  tabindex="4"  value="<?= $aLoan['withdraw_day'];?>" size="10"    onKeypress="if(event.keyCode==13) {document.getElementById('previous_balance').focus();return false;}">
      </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
    <td>&nbsp;</td>
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2" bgcolor="#DADADA"> <input name="vat" type="hidden" id="vat" tabindex="20"  style="text-align:right"   value="<?= $aLoan['vat'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('advance_charges').focus();return false;}"> 
      <input name="insurance" type="hidden" id="insurance" tabindex="20"  style="text-align:right"   value="<?= $aLoan['insurance'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('advance_charges').focus();return false;}"> 
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">.:: Previous 
      Balances</font></td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross</font></td>
    <td><input name="gross" type="text" id="gross"  tabindex="25"   style="text-align:right"  value="<?= $aLoan['gross'];?>" size="10"  onKeypress="if(event.keyCode==13) {document.getElementById('released').focus();return false;}"></td>
  </tr>
  <tr> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Previous Loan</font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <input name="previous_balance" type="text" style="text-align:right" readonly  tabindex="12"  id="previous_balance" value="<?= $aLoan['previous_balance'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"  onKeypress="if(event.keyCode==13) {document.getElementById('advance_change').focus();return false;}">
      </font> </td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Interest</font></td>
    <td><input name="interest" type="text" id="interest"  tabindex="24"   style="text-align:right" value="<?= $aLoan['interest'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('gross').focus();return false;}">
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">(-)</font></td>
  </tr>
  <tr> 
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Advanced 
      Change</font></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
	<?
		$rwonly = '';
		if ($ADMIN['admin_id'] != 54 and $ADMIN['admin_id'] != 197 and $ADMIN['admin_id'] != 1) $rwonly='readonly';
	?>
      <input name="advance_change" type="text" id="advance_change"  <?=$rwonly;?>  tabindex="20"  style="text-align:right"   value="<?= $aLoan['advance_change'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('ca_balance').focus();return false;}">
      </font></td>
    <td height="22" nowrap>&nbsp;</td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total Deductions</font></td>
    <td><input name="charges" type="text" id="charges"  style="text-align:right"  tabindex="26"  value="<?= $aLoan['charges'];?>" size="10"  readOnly>
      <font size="2" face="Verdana, Arial, Helvetica, sans-serif">(-)</font></td>
  </tr>
  <tr> 
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cash 
      Advance</font></td>
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <input name="ca_balance" type="text" id="ca_balance2"  style="text-align:right"  tabindex="14"  value="<?= $aLoan['ca_balance'];?>" size="10"  onBlur="xajax_loanfees(xajax.getFormValues('f1'))"  onKeypress="if(event.keyCode==13) {document.getElementById('redeem').focus();return false;}">
      </font> </td>
    <td><?
	  if ($ADMIN[usergroup]=='R' or $ADMIN[usergroup]=='A')
	  {
		  ?>
      <select name="cashier_id" id="cashier_id"  style="width:180"   onkeypress="if(event.keyCode==13) {document.getElementById('term').focus();return false;}">
        <option value=''>Select Cashier</option>
        <?
		  $qr = pg_query("select * from admin where enable='Y' and (usergroup='C' or usergroup='U') and 
	                  (branch_id=0 or branch_id='".$aLoan[branch_id]."' or branch_id2='".$aLoan[branch_id]."' or branch_id3='".$aLoan[branch_id]."' or
					   branch_id4='".$aLoan[branch_id]."' or branch_id5='".$aLoan[branch_id]."' or branch_id6='".$aLoan[branch_id]."' or branch_id7='".$aLoan[branch_id]."' or 
					   branch_id8='".$aLoan[branch_id]."' or branch_id9='".$aLoan[branch_id]."' or branch_id10='".$aLoan[branch_id]."' or branch_id11='".$aLoan[branch_id]."' or 
					   branch_id12='".$aLoan[branch_id]."' or branch_id13='".$aLoan[branch_id]."' or branch_id14='".$aLoan[branch_id]."' or branch_id15='".$aLoan[branch_id]."' or 
					   branch_id16='".$aLoan[branch_id]."' or branch_id17='".$aLoan[branch_id]."' or branch_id18='".$aLoan[branch_id]."' or branch_id19='".$aLoan[branch_id]."' or 
					   branch_id20='".$aLoan[branch_id]."')");
	  while ($r=pg_fetch_object($qr))
	  {
	  	if ($aLoan['cashier_id'] == $r->admin_id)
		{
		  	echo "<option value=$r->admin_id selected>$r->name</option>";
		}
		else
		{
		  	echo "<option value=$r->admin_id>$r->name</option>";
		}	
	  }  
	  ?>
      </select>
      <?
	}
	?></td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Released</font></td>
    <td><input name="released" type="text" id="released"  style="text-align:right"  tabindex="26"  value="<?= $aLoan['released'];?>" size="10"   onKeypress="if(event.keyCode==13) {document.getElementById('rate').focus();return false;}"></td>
  </tr>
  <tr> 
    <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gawad/Redeem</font></td>
    <td valign="top"><input name="redeem" type="text" id="redeem" tabindex="20"  style="text-align:right"   value="<?= $aLoan['redeem'];?>" size="10" onBlur="xajax_loanfees(xajax.getFormValues('f1'))"   onKeypress="if(event.keyCode==13) {document.getElementById('service_charge').focus();return false;}"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Deposit</font></td>
    <td><input name="deposit" type="text" id="deposit"  style="text-align:right"  tabindex="26"  value="<?= $aLoan['deposit'];?>" size="10"   onkeypress="if(event.keyCode==13) {document.getElementById('rate').focus();return false;}" /></td>
  </tr>
  <tr> 
    <td valign="bottom" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; text-align:right"><input type="checkbox" name="tpenalty" id="tpenalty" value="1" <?=($aLoan[tpenalty]==1?'checked':'');?>/> No penalty&nbsp;&nbsp;&nbsp;</td>
    <td valign="bottom" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; text-align:right" ><input type="checkbox" name="insure" id="insure" value="1" <?=($aLoan[insure]==1?'checked':'');?> onchange="xajax_manuinsure(xajax.getFormValues('f1'))"/>With Insurance&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td><input name="insureamt" readonly type="text" id="insureamt" style="text-align:right"   value="<?= number_format($aLoan['insureamt'],2);?>" size="14"/></td>
    <td>&nbsp;</td>
	<?
	if ($aLoan[deposit] > 0)
	{
	?>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>N E T</b></font></td>
    <td><font size="4" face="Verdana, Arial, Helvetica, sans-serif" color="red"><b>**<?=number_format($aLoan['released']-$aLoan['deposit'],2);?>**</b></font></td>
	<?
	}
	else
	{
	?>
    <td width="5%" colspan="2">&nbsp;</td>
	<?
	}
	?>
  </tr>
</table>
</body>
