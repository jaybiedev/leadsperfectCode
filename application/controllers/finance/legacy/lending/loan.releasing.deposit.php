<?
	$curdate = date('Y-m-d');
	$q	= "select * from loandeposit where releasing_id ='".$aLoan['releasing_id']."' order by loandeposit_id";
	$qr = pg_query($q);
	$cc=0;
	while ($rr = pg_fetch_object($qr))
	{
		if ($cc == 1)
		{
			$out1 = $rr->debit;
			$dte1 = $rr->date;
			$lid1 = $rr->loandeposit_id;
		}	
		elseif ($cc == 2)
		{
			$out2 = $rr->debit;
			$dte2 = $rr->date;
			$lid2 = $rr->loandeposit_id;
		}	
		elseif ($cc == 3)
		{
			$out3 = $rr->debit;
			$dte3 = $rr->date;
			$lid3 = $rr->loandeposit_id;
		}	
		elseif ($cc == 4)
		{
			$out4 = $rr->debit;
			$dte4 = $rr->date;
			$lid4 = $rr->loandeposit_id;
		}	
		elseif ($cc == 5)
		{
			$out5 = $rr->debit;
			$dte5 = $rr->date;
			$lid5 = $rr->loandeposit_id;
		}				
		$cc++;
	}
	$new1 = $new2 = $new3 = $new4 = $new5 = 0;
	if ($dte1 == '') {$dte1 = $curdate;$new1=1;}
	elseif ($dte2 == '') {$dte2 = $curdate;$new2=1;}
	elseif ($dte3 == '') {$dte3 = $curdate;$new3=1;}
	elseif ($dte4 == '') {$dte4 = $curdate;$new4=1;}
	elseif ($dte5 == '') {$dte5 = $curdate;$new5=1;}
	
?>
  <table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td width="18%" height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Deposit</font></td>
    <td width="11%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
      <?= ymd2mdy($aLoan[date]);?>
    </font></td>
    <td width="10%" nowrap>&nbsp;</td>
    <td width="14%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
    <?= number_format($aLoan[deposit],2);?>   </font></td>
    <td width="17%">&nbsp;</td>
    <td width="30%">&nbsp;</td>
  </tr>
  <tr> 
    <td height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">First Release 
      <input name="lid1" type="hidden" id="lid1" value="<?= $lid1;?>" size="12"/>
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
		if ($dte1 <= $curdate and $new1==0)
		{
			echo ymd2mdy($dte1);
		} else
		{
		?>
      <input name="dte1" type="text" id="dte1" value="<?= $dte1;?>" size="10"
	  	  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
         <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.dte1, 'mm/dd/yyyy')">
	  
	 <?
	 	} 
	?>  
    </font></td>
    <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
		if ($dte1 <= $curdate and $new1==0)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197)
			{
			?>
      			<input name="out1" type="text" id="out1" value="<?= $out1;?>" size="12"/>	
			<?		
			} else
			{
				echo number_format($out1,2);
			}	
		} else
		{
			?>
      			<input name="out1" type="text" id="out1" value="<?= $out1;?>" size="12"/>
	 		<?
	 	} 
	?>  
    </font></td>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="ReleaseDeposit1" onclick="vSubmit(this)" name="ReleaseDeposit1" id="ReleaseDeposit1" /></td>	
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Second Release 
      <input name="lid2" type="hidden" id="lid2" value="<?= $lid2;?>" size="12"/>
    </font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
		if ($dte2 <= $curdate and $new2==0)
		{
			echo ymd2mdy($dte2);
		} else
		{
		?>
      <input name="dte2" type="text" id="dte2" value="<?= $dte2;?>" size="10"
	  	  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
         <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.dte2, 'mm/dd/yyyy')">
	 <?
	 	} 
	?>  
    </font></td> 
     <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
       <?
		if ($dte2 <= $curdate and $new2==0)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197)
			{
			?>
      			<input name="out2" type="text" id="out2" value="<?= $out2;?>" size="12"/>	
			<?		
			} else
			{
				echo number_format($out2,2);
			}	
		} else
		{
			?>
		   <input name="out2" type="text" id="out2" value="<?= $out2;?>" size="12"/>
			 <?
	 	} 
	?>  
	</font></td>
    <td >&nbsp;</td>
   <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
     <input type="button" value="ReleaseDeposit2" onclick="vSubmit(this)" name="ReleaseDeposit2" id="ReleaseDeposit2" />
   </font></td>
  </tr>
  <tr> 
    <td height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Third Release
      <input name="lid3" type="hidden" id="lid3" value="<?= $lid3;?>" size="12"/>
    </font></td>
    <td>
		<?
		if ($dte3 <= $curdate and $new3==0)
		{
			echo ymd2mdy($dte3);
		} else
		{
		?>
      <input name="dte3" type="text" id="dte3" value="<?= $dte3;?>" size="10"
	  	  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
         <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.dte3, 'mm/dd/yyyy')">
	 <?
	 	} 
	?>	</td>
     <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
		if ($dte3 <= $curdate and $new3 == 0)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197)
			{
			?>
      			<input name="out3" type="text" id="out3" value="<?= $out3;?>" size="12"/>	
			<?		
			} else
			{
				echo number_format($out3,2);
			}	
		} else
		{
		?>
	      <input name="out3" type="text" id="out3" value="<?=$out3;?>" size="12"/>
		 <?
	 	} 
	?>  
	</font></td>
    <td>&nbsp;</td>
   <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
     <input type="button" value="ReleaseDeposit3" onclick="vSubmit(this)" name="ReleaseDeposit3" id="ReleaseDeposit3" />
   </font></td>
  </tr>
  <tr> 
    <td height="26"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fourth Release 
      <input name="lid4" type="hidden" id="lid4" value="<?= $lid4;?>" size="12"/>
    </font></td>
    <td>
		<?
		if ($dte4 <= $curdate and $new4==0)
		{
			echo ymd2mdy($dte4);
		} else
		{
		?>
      <input name="dte4" type="text" id="dte4" value="<?= $dte4;?>" size="10"
	  	  onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
         <img src="../graphics/dwn-arrow-grn.gif" onClick="popUpCalendar(this, f1.dte4, 'mm/dd/yyyy')">
	 <?
	 	} 
	?>	</td>
     <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?
		if ($dte4 <= $curdate and $new4==0)
		{
			if ($ADMIN[admin_id]==1 or $ADMIN[admin_id]==197)
			{
			?>
      			<input name="out4" type="text" id="out4" value="<?= $out4;?>" size="12"/>	
			<?		
			} else
			{
				echo number_format($out4,2);
			}	
		} else
		{
		?>
      <input name="out4" type="text" id="out4" value="<?=$out4;?>" size="12"/>
	 <?
	 	} 
	?>  
	</font></td>    <td>&nbsp;</td>
   <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
     <input type="button" value="ReleaseDeposit4" onclick="vSubmit(this)" name="ReleaseDeposit4" id="ReleaseDeposit4" />
   </font></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; </font></td>
    <td>&nbsp;</td>
    <td nowrap>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td bgcolor="#DADADA" height="26" align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Balance</font></td>
    <td bgcolor="#DADADA" height="26" align="right">&nbsp;</font></td>
    <td bgcolor="#DADADA" height="26" align="right">&nbsp;</font></td>
   <td bgcolor="#DADADA" height="26" align="right"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">
	<?=adjustSize(number_format($aLoan[deposit] - ($out1+$out2+$out3+$out4+$out5),2),16);?></font></td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td height="22" nowrap>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
</body>
