<?
echo "there ".numWord(2123.25);
function numWord($num)
{
	$Ones[01] = "ONE";
	$Ones[02] = "TWO";
	$Ones[03] = "THREE";
	$Ones[04] = "FOUR";
	$Ones[05] = "FIVE";
	$Ones[06] = "SIX";
	$Ones[07] = "SEVEN";
	$Ones[08] = "EIGHT";
	$Ones[09] = "NINE";
	$Ones[10] = "TEN";
	$Ones[11] = "ELEVEN";
	$Ones[12] = "TWELVE";
	$Ones[13] = "THIRTEEN";
	$Ones[14] = "FOURTEEN";
	$Ones[15] = "FIFTEEN";
	$Ones[16] = "SIXTEEN";
	$Ones[17] = "SEVENTEEN";
	$Ones[18] = "EIGHTEEN";
	$Ones[19] = "NINETEEN";
	$Tens[01] = "TEN";
	$Tens[02] = "TWENTY";
	$Tens[03] = "THIRTY";
	$Tens[04] = "FORTY";
	$Tens[05] = "FIFTY";
	$Tens[06] = "SIXTY";
	$Tens[07] = "SEVENTY";
	$Tens[08] = "EIGHTY";
	$Tens[09] = "NINETY";

	$tn=0;
	$wrdn='';
	if ($num >= 1000000)
	{
	  $tn = intval($num/1000000);
	  if (tn>=1)
	  {
	    $tnh = intval($tn/100);
	    if (tnh >= 1)
	    {
	      $wrdn = $Ones[$tnh].' HUNDRED';
	    }  
	    $tno = $tn-($tnh*100);
	    $tnt = intval($tno/10);
	    if  ($tnt>1)
	    {
	       $wrdn = $wrdn + ' ' + $Tens[$tnt];
	       $nn   = $tno-$tnt*10;
	       if (nn>=1)
	       {
	         $wrdn .=  ' ' . $Ones[$nn];
	       }
	    }   
	    elseif ($tnt==1)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }
	    elseif (tno>0)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }   
	    $wrdn=$wrdn+' MILLION';
	  }
	}
	$nm = $num-$tn*1000000;
	
	if ($nm >= 1000)
	{
	  $tn = intval($nm/1000);

	  if ($tn>=1)
	  {
	    $tnh = intval($tn/100);
	    if ($tnh >= 1)
	    {
	      $wrdn .= ' '.$Ones[$tnh].' HUNDRED';
	    }
	    
	    $tno = $tn-($tnh*100);
	    $tnt = intval($tno/10);

	    if ($tnt>1)
	    {
	       $wrdn .= ' ' . $Tens[$tnt] ;
	       $nn   = $tno-$tnt*10;
	       if (nn>=1)
	       {
	         $wrdn = $wrdn + ' ' . $Ones[$nn];
	       }
	    }    
	    elseif ($tnt==1)
	    {
	       $wrdn = $wrdn + ' ' . $Ones[$tno];
	    }  
	    elseif ($tno>0)
	    {
	       $wrdn .=  ' ' . $Ones[$tno];
	    }
	    
	    $wrdn .= ' THOUSAND';

	  }
	}
	
	$tnm = $nm-$tn*1000;
	$tnh = intval($tnm/100);
	if ($tnh >= 1)
	{
	  $wrdn .= ' '.$Ones[$tnh].' HUNDRED';
	}  
	
	$tno = $tnm-($tnh*100);
	$tnt = intval($tno/10);
	
	if ($tnt>1)
	{
	    $wrdn .=  ' ' . $Tens[$tnt];
	    $nn   = $tno-$tnt*10;
	    if ($nn>=1)
	    {
	      $wrdn .= ' ' . $Ones[$nn];
	    }  
	}    
	elseif ($tnt==1)
	{
	    $wrdn .= ' '. $Ones[$tno];
	}    
	elseif (intval($tno)>0)
	{
	    $wrdn .= ' ' . $Ones[$tno];
	}    
	
	$cnts=($nm-intval($nm))*100;
	if ($cnts != 0)
	{
	  if (strlen($wrdn)<2)
	  {
	    $wrdn .= ltrim(substr($cnts,0,2)).'/100';
	  }  
	  $wrdn .= ' AND '.ltrim(substr($cnts,0,2)).'/100';
	}
return $wrdn;
}
