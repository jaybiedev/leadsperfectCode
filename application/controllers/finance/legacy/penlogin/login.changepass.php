<div id="passwordLayer" name="passwordLayer" style="position:absolute; width:301px; z-index:1; background-color: #999999; layer-background-color: #999999; border: 1px none #000000; left: 30%; top: 25%; height: 96px;">
  <table width="99%" height="99%" border="2" align="left" cellpadding="1" cellspacing="1" bordercolor="#0000FF" bgcolor="#99FFFF">
    <tr> 
      <td height="28" colspan="3" background="graphics/table0_horizontal.PNG" class="style1"> 
        <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" background="graphics/table0_horizontal.PNG">
          <tr> 
            <td height="23" background="graphics/table0_horizontal.PNG"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><img src="../graphics/achtung.gif" width="18" height="23"> 
              Access key</strong></font></td>
            <td align="right" background="graphics/table0_horizontal.PNG"><input type="image" src="../graphics/close2.gif" width="15" height="13" onClick="suspendLayer.style.visibility='hidden'; getElementById('textbox').focus();document.getElementById('Ok').disabled=0;"></td>
          </tr>
        </table></td>
    </tr>
    <tr valign="bottom"> 
      <td colspan="3"><font size="4"> Old Pin # &nbsp;&nbsp;</font>
        <input name="passpin1" type="password" id="passpin1" value="<?= $passpin1;?>" size="20" style="font-size:20" onChange="Ok.click()" onFocus="document.getElementById('Ok').disable=0" onkeypress="if(event.keyCode==13) {document.getElementById('passpin2').focus();return false;}"></td>
    </tr>
    <tr valign="bottom">
      <td colspan="3"><font size="4">New Pin #&nbsp;</font>
        <input name="passpin2" type="password" id="passpin2" value="<?= $passpin2;?>" size="20" style="font-size:20" onChange="Ok.click()" onFocus="document.getElementById('Ok').disable=0">
	  </td>
    </tr>
    <tr valign="bottom"> 
      <td width="66%"> <input name="Ok" type="submit" id="Ok" value="Ok"  style="font-size:20 ; font-family: 'Times New Roman'" alt="T" onClick="getElementById('passpin1').focus();document.getElementById('Ok').disabled=0;"></td>
    </tr>
  </table>
</div>
<script>document.getElementById('Ok').disabled=1;document.getElementById('passpin1').focus()</script>
