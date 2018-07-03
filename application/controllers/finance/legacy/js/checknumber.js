function checknumber(numbr)
{
	if (numbr.value == '') return
	var num=numbr.value
	var anum=/(^\d+$)|(^\d+\.\d+$)/
	if (!anum.test(num))
	{
		if (numbr.value != '')
		{
	   		alert('Enter a Valid Number for '+numbr.name);
	   	}	
	   	numbr.value=0;
	   	numbr.focus();
	}
	return false;
}