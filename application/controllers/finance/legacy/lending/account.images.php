<?php
$branch=lookUpTableReturnValue('x','branch','branch_id','branch',$aaccount['branch_id']);
$pensioner=$aaccount['account_code'];
$branch1 = $branch; // preg_replace('/\s/', '', $branch);
$strip = array(' ',',','/','-','*','.');
$branchn = str_replace($strip, "", $branch1);
echo $branch.' : '.$branchn."\n";
//$url = "/prog/data/maps/".$branchn."/".$pensioner.".kml";

$map_url = $this->Storage->getMapUrl($branchn, $pensioner.".kml");
?>
<table width="99%" border="0" cellpadding="0" cellspacing="1">
  <tr>
       <td width="39%" align="center">
           Photo:
           <br />
	   	<img src="<?=$this->Storage->getPhotoUrl($aaccount['pix']);?>" name="pix" width="200" height="200">
<!--        <input type="hidden" name="MAX_FILE_SIZE" value="79872">
		<input type="file" name="pixfile" onChange="vPix()" style="border:1px color:#CCCCCC; font-size=10">-->      </td>
       <td width="61%" align="center">
           Signature:
           <br />
           <img src="<?=$this->Storage->getSignatureUrl($aaccount['pixsign']);?>" name="pixsign" width="400" height="200">
<!--        <input type="hidden" name="MAX_FILE_SIZE" value="79872"></td>
		 <input type="file" name="pixsignfile" onChange="vPix()" style="border:1px color:#CCCCCC; font-size=10">  -->  </tr>
	<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
<?
if (!empty($map_url))
{
?>
	<tr><td style="text-align:center; vertical-align:middle"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Google Map </font><input type='image' name="View Map" id="View Map" onClick="vSubmit(this)"  src="<?=base_url();?>graphics/maps.jpg" width="65" height="65"></td>
	</tr>
<?
}
?>
	<tr><td>&nbsp;</td></tr>
</table>
<?
	if ($p1=='View Map')
		echo "<script>window.open('?p=../maps/maplocate')</script>";
?>