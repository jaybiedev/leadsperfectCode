<SCRIPT language=Javascript><!--
var isNS = (navigator.appName == "Netscape") ? 1 : 0;
var EnableRightClick = 0;
if(isNS) 
document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
document.onhelp=function(){event.returnValue=false};

keys = new Array();
keys["f112"] = 'f1';
keys["f113"] = 'f2';
keys["f114"] = 'f3';
keys["f115"] = 'f4';
keys["f116"] = 'f5';
keys["f117"] = 'f6';
keys["f118"] = 'f7';
keys["f119"] = 'f8';
keys["f120"] = 'f9';
keys["f121"] = 'f10';
keys["f122"] = 'f11';
keys["f123"] = 'f12';

function mischandler(){
  if(EnableRightClick==1){ return true; }
  else {return false; }
}
function mousehandler(e){
  if(EnableRightClick==1){ return true; }
  var myevent = (isNS) ? e : event;
  var eventbutton = (isNS) ? myevent.which : myevent.button;
  if((eventbutton==2)||(eventbutton==3)) return false;
}
function keyhandler(e) {
//document.onkeydown = function(){
	var myevent = (isNS) ? e : window.event;
	//alert(myevent.keyCode)
	mycode=myevent.keyCode
	
	if (myevent.keyCode==96)
	{
    	    EnableRightClick = 1;
    	}
    	else if (keys["f"+myevent.keyCode])
    	{
    		mycode=myevent.keyCode
    		myevent.keyCode = 505
		if (mycode == 116) location.href='?p=sub.cashier'; // F5	
		
		return false;
    	}
	return 
}
document.oncontextmenu = mischandler;
document.onmousedown = mousehandler;
document.onmouseup = mousehandler;
document.onkeydown = keyhandler;

var preloadFlag = false;
function preloadImages() {
	if (document.images) {
		images_01_over = newImage('/graphics/images_01_over.gif');
		images_02_over = newImage('/graphics/images_02_over.gif');
		images_03_over = newImage('/grsphics/images_03_over.gif');
		images_04_over = newImage('/grsphics/images_04_over.gif');
		preloadFlag = true;
	}
}

function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function changeImages() {
	if (document.images && (preloadFlag == true)) {
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}
}
//-->
</script>
<BODY leftMargin=0 topMargin=1>
<table width="100%" height="57" cellpadding="2" cellspacing="0" background="../graphics/menubar.gif">
  <tr> 
    <td height="55" valign="top"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong>&nbsp; 
      <?= $SYSCONF['BUSINESS_NAME'];?>
      </strong></font><br>
      <font color="#EFEFEF" size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
      &nbsp;
      <?= $SYSCONF['BUSINESS_ADDR'];?>
      </font> </td>
    <td width="17%" valign="top" nowrap><strong> <font color="#CCCCCC" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
      <?= date('F d,Y');?>
      &nbsp;&nbsp;<br>
      Welcome! <?=$ADMIN['name'];?></font></strong></td>
  </tr>
</table>
<SCRIPT type=text/javascript>
      
	var Blue1 = '#333399';		// lighter blue
	var Blue2 = '#89BFED';		// darker
	var Blue3 = '#44AFB5'
	var Green1 = '#0FFABC';		// light1
	var Green2 = '#4FFABC';		//light2
	var Green3 = '#0FF0BC';		//dark1
	var Green4 = '#0FF000';		// dark2
	var White1 = '#FFFFFF';
	var Gray1 ='#CCCCCC';
	var Gray2 = '#E9E9E9';
	var Black1 = '#000000';
	var Black2 = '#2F0002';
	
	var LowBgColor=Blue3;	//string; background color when the mouse is not over the element
	var HighBgColor= Gray1;	//string; background color when the mouse is over the element
	var FontLowColor=White1;	//string; font color when the mouse is not over the element
	var FontHighColor=White1; 	//string; font color when the mouse is over the element
	var BorderColor=Gray1;	//string; color of the border around the elements
	var BorderWidth=01;		//number; thickness of the border around the element in pixels
	var BorderBtwnElmnts=1;		//number; Controls if there is a border between the elements. 0 is no border between the elements
	var FontFamily="'Arial MT', 'MS Sans Serif'" //string; more than one font can be declared separated by comma
	var FontSize=9;			//number; size of the font in pt
	var FontBold=1;			//number; makes the font weight bold. 0 makes the font weight normal
	var FontItalic=0;		//number; makes the font weight italic. 0 makes the font weight italic
	var MenuTextCentered=0;		//number; 1 centers the element text. 0 aligns to left
	var MenuCentered='left';	//string; can be left, center or right
	var MenuVerticalCentered='top';	//string; can be top, middle, bottom
	var ChildOverlap=0;		// number between 0 and 1. Controls which part of a level is covered by its sublevel
	var ChildVerticalOverlap=0;	//number between 0a nd 1.Controls the vertical offset of a sublevel from its parent level
	var StartTop=37; 		//number; set vertical offset
	var StartLeft=20;		 //number; set horizontal offset
	var VerCorrect=0;		//defines the vertical correction of the second line of the menu in the document
	var HorCorrect=0;		//defines the horizontal correction of the second line of the menu in the document
	var LeftPaddng=5;		//defines the distance of the left side of the menu text and the border of the element
	var TopPaddng=2;		//defines the distance of the top side or the menu text and the border of the element
	var FirstLineHorizontal=1; 	//set menu layout (1=horizontal, 0=vertical)
	var MenuFramesVertical=0;
	var DissapearDelay=500;		//number; time in milliseconds the menu's sublevel stays visible after the mouse is no longer over the menu
	var TakeOverBgColor=0;
	var FirstLineFrame='self';
	var SecLineFrame='self';
	var DocTargetFrame='self';
	var WebMasterCheck=0;		//When set to 1 the script performs a check on the frame names and the menu tree. When your menu is running this should be set to 0
	
	
	var NoOfFirstLineMenus=5; //set number of main menu items

	//Menux=new Array("text to show","Link",No of sub elements,element height,element width);
	Menu1 = new Array("File","", 4, 20, 100);
		Menu1_1 = new Array("Location/Branch Master", "?p=branch", 0, 18, 150);
		Menu1_2 = new Array("Bank Master", "?p=bank", 0, 18, 150);
		Menu1_3 = new Array("----------------------------","", 0, 18, 150);
		Menu1_4 = new Array("","", 0, 18, 150);

	Menu2 = new Array("Transaction","", 8, 19, 100);
		Menu2_1 = new Array("Branch Cash Position", "?p=cashpos", 0, 18, 180);
		Menu2_2 = new Array("Bank Reconciliation", "?p=bankrecon", 0, 18, 130);
		Menu2_3 = new Array("List of UnCleared Checks", "?p=ucheck", 0, 18, 130);
		Menu2_4 = new Array("----------------------------------", "", 0, 15, 130);
		Menu2_5 = new Array("Bank Summary", "?p=bankrecon.summary", 0, 18, 130);
		Menu2_6 = new Array("Bank Transactions", "?p=report.bankrecon", 0, 18, 130);
		Menu2_7 = new Array("Cleared Checks", "?p=report.bankrecon2", 0, 18, 130);
		Menu2_8 = new Array("Inter-branch Petty Cash", "?p=report.interbranch_cashpos", 0, 18, 130);
		Menu2_9 = new Array("", "", 0, 15, 130);
		

	Menu3 = new Array("Main","?p=", 0, 20, 100);
	Menu4 = new Array("Logout","?p=logout", 0, 19, 100);
	Menu5 = new Array("Home","../", 0, 19, 100);
	
</SCRIPT>
<SCRIPT src="../js/menucom.js" type=text/javascript></SCRIPT> 
<br>
