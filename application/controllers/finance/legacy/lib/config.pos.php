<?
	$BUSINESS_NAME='';
	$BUSINESS_ADDR='BACOLOD CITY';
	$BUSINESS_TEL='XXX-XXXX';
	$MODULE = 'Management Information System';
	$MODULE_PIX='../graphics/storage.gif';
	
	$q = "select * from sysconfig";
	
	if ($BUSINESS_NAME == '') $BUSINESS_NAME = 'HOPE ePOS System';
	if ($RECEIPT_FOOTER == '') $RECEIPT_FOOTER = 'THANK YOU, COME AGAIN.';
	if ($POS_SERIAL == '') $POS_SERIAL = 'EPSON1234567';
	if ($LESSEE_NO == '') $LESSEE_NO = 'XXXXXX';
	
?>