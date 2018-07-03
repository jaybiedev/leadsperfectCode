<?php
$this->View->setPageTitle("Bank Account Monitor");
?>
<table  class="table" align="center" >
  <tr bgcolor="#E3F6F1">
    <td width="15%" align="center"><strong>#</strong></td>
    <td width="39%">Bank</td>
    <td width="26%">IN</td>
    <td width="26%">OUT</td>
  </tr>
  <tr> 
    <td height="380px" colspan="4" valign="top"><div id="Layer1" style="position:relative; width:100%; height:100%; z-index:1; overflow: scroll;"> 
        <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#EFEFEF">
          <?
  if (!chkRights2('atmmonitor','mview',$ADMIN['admin_id']))
{
	message("You have no permission in this area...");
	exit;
}
  	$mdate = date('Y-m-d');

  	$q = "select distinct TRIM(clientbank) AS clientbank from clientbank where enable order by clientbank";
	$qr = @pg_query($q) or message(pg_errormessage());
	$c=0;
	while ($r = @pg_fetch_object($qr))
	{
	    $clientbank = trim(strtolower($r->clientbank));
		$c++;
		$q = "select count(*) as atm_count_in
				from
					account, clientbank
				where
					clientbank.clientbank_id=account.clientbank_id
					AND date_atm_in IS NOT NULL
					AND date_atm_out IS NOT NULL
					AND  lower(clientbank) like '{$clientbank}%'";
		$rr = fetch_object($q);
		$atm_count_in = $rr->atm_count_in;

		$q = "select count(*) as atm_count_out
				from
					account, clientbank
				where
					clientbank.clientbank_id=account.clientbank_id
					AND date_atm_out IS NOT NULL
					AND  lower(clientbank) like '{$clientbank}%'";
		$rr = fetch_object($q);
		$atm_count_out = $rr->atm_count_out;
  ?>
          <tr bgcolor="#FFFFFF" onMouseOver="bgColor='#FFFFCC'" onMouseOut="bgColor='#FFFFFF'">
            <td width="14%" align="right">  
              <?= $c;?>.&nbsp;</td>
            <td width="38%">  
              <?= $r->clientbank;?>
              </td>
            <td width="24%" align="center">  
              <?= $atm_count_in;?>
              </td>
            <td width="24%" align="center">  
              <?= $atm_count_out;?>
              </td>
          </tr>
          <?
  }
  ?>
        </table>
      </div></td>
  </tr>
</table>
