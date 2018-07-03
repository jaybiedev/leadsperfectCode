<BODY leftMargin=0 topMargin=1>
<table width="100%" cellpadding="2" id='topMark' name='topMark'  cellspacing="0" background="../graphics/menubar.gif">
  <tr> 
    <td height="60" valign="top"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong>&nbsp; 
      <?= $SYSCONF['BUSINESS_NAME'].' '.($SYSCONF['DB'] != '' ? '(db='.$SYSCONF['DB'].')': '');?>
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
<SCRIPT language=Javascript><!--
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
	var StartTop=40; 		//number; set vertical offset
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
	
	
	var NoOfFirstLineMenus=8; //set number of main menu items

	//Menux=new Array("text to show","Link",No of sub elements,element height,element width);
	Menu1 = new Array("File","", 15, 20, 100);
		Menu1_1 = new Array("Account Info", "?p=account", 0, 17, 180);
		Menu1_2 = new Array("Account Group", "?p=account_group", 0, 17, 150);
		Menu1_3 = new Array("Client Banks Monitor", "?p=atmmonitor", 0, 17, 150);
		Menu1_4 = new Array("Client Banks", "?p=clientbank", 0, 17, 150);
		Menu1_5 = new Array("----------------------------------", "", 0, 17, 150);
		Menu1_6 = new Array("Recalculate Loans Ledger", "?p=recalc", 0, 17, 150);
		Menu1_7 = new Array("Process Penalties  ->", "?p=computepenalty", 2, 17, 150);
			Menu1_7_1 = new Array("Process Penalties", "?p=computepenalty", 0, 22, 150);
			Menu1_7_2 = new Array("Browse Penalties", "?p=penalty.browse", 0, 22, 150);
		Menu1_8 = new Array("----------------------------------", "", 0, 17, 150);
		Menu1_9 = new Array("Collection Fee Table", "?p=collectionfee", 0, 17, 150);
		Menu1_10 = new Array("Service Charge Table", "?p=servicecharge", 0, 17, 150);
		Menu1_11 = new Array("Branches", "?p=branch", 0, 17, 150);
		Menu1_12 = new Array("Partners", "?p=province", 0, 17, 150);
		Menu1_13 = new Array("Loan Type", "?p=loan_type", 0, 17, 150);
		Menu1_14 = new Array("Account Classification", "?p=account_class", 0, 17, 150);
		Menu1_15 = new Array("----------------------------------", "", 0, 17, 150);
		Menu1_16 = new Array("Exit", "javascript: window.close()", 0, 17, 150);

	Menu2 = new Array("Loans","?p=loan.releasing.browse", 11, 19, 100);
		Menu2_1 = new Array("Loan Releasing", "?p=loan.releasing.browse", 0, 19, 200);
		Menu2_2 = new Array("Overrides", "?p=override", 0, 19, 150);
		Menu2_3 = new Array("Summary of Loan Releases", "?p=report.releasing", 0, 19, 150);
		Menu2_4 = new Array("--- Administration ------------", "", 0, 19, 200);
		//Menu2_5 = new Array("Adjusting Entry", "?p=loan.adjust", 0, 17, 150);
		Menu2_5 = new Array("Recalculate Loans Ledger", "?p=recalc", 0, 19, 150);
		Menu2_6 = new Array("Process Penalties", "?p=computepenalty", 0, 19, 150);
		Menu2_7 = new Array("Browse Penalties", "?p=penalty.browse", 0, 17, 150);
		Menu2_8 = new Array("-------------------------------", "", 0, 19, 200);
		Menu2_9 = new Array("Loan Deposit Summary", "?p=report.loandeposit", 0, 17, 150);
		Menu2_10 = new Array("Deposit Release Summary", "?p=report.depositrelease", 0, 17, 150);
		Menu2_11 = new Array("Interbranch Loan Transactions", "?p=report.interbranch_loan", 0, 17, 280);
		
	Menu3 = new Array("Payment", "?p=payment.browse", 9, 15, 150);
		Menu3_1 = new Array("Payment Entry", "?p=payment.browse", 0, 17, 200);
		Menu3_2 = new Array("Gawad/Redeem/Transfer", "?p=redeem", 0, 17, 200);
		Menu3_3 = new Array("-------------------------------------", "", 0, 17, 150);
		Menu3_4 = new Array("Summary of Payments", "?p=report.payment", 0, 17, 150);
		Menu3_5 = new Array("Withdrawals Per Branch", "?p=report.paymentbranch", 0, 17, 150);
		Menu3_6 = new Array("-------------------------------------", "", 0, 17, 150);
		Menu3_7 = new Array("Upload Data(From Branch)", "?p=payment.upload", 0, 17, 150);
		Menu3_8 = new Array("-------------------------------------", "", 0, 17, 150);
		Menu3_9 = new Array("Download Data(To Branch)", "?p=payment.download", 0, 17, 150);
		Menu3_10 = new Array("SL Penalty Correction", "?p=penaltyrestore", 0, 17, 150);
	
	Menu4 = new Array("Excess", "?p=excess.ledger", 10, 15, 150);
		Menu4_1 = new Array("Excess Withdrawal/CA", "?p=excess.withdraw", 0, 17, 280);
		Menu4_2 = new Array("Excess Ledger", "?p=excess.ledger", 0, 17, 150);
		Menu4_3 = new Array("Excess Withdrawal List", "?p=report.wexcess2", 0, 17, 170);
		Menu4_4 = new Array("-----------------------------------------", "", 0, 17, 150);
		Menu4_5 = new Array("Summary of Excess", "?p=report.excess", 0, 17, 150);
		Menu4_6 = new Array("Excess/Change Withdrawn Report ", "?p=report.wexcess", 0, 17, 200);
		Menu4_7 = new Array("Excess/Change Advances Summary", "?p=report.wexcessadvance", 0, 17, 240);
		Menu4_8 = new Array("Excess Released Based on Starting Month", "?p=report.wexcessadvance2", 0, 17, 240);
		Menu4_9 = new Array("Excess Released for Specific Month Under Period Covered", "?p=report.excess_released", 0, 17, 280);
		Menu4_10 = new Array("Interbranch Excess Transactions", "?p=report.interbranch_excess", 0, 17, 280);
		Menu4_11 = new Array("", "", 0, 17, 150);

	Menu5 = new Array("Loan Reports","", 24, 20, 100);
		Menu5_1 = new Array("Account Ledger", "?p=report.accountledger_oldledger", 0, 20, 235);
		Menu5_2 = new Array("Receivable Listing", "?p=report.receivable", 0, 20, 235);
		Menu5_3 = new Array("Delinquent Accounts","?p=report.delinquent", 0, 20, 235);
		Menu5_4 = new Array("Summary of Loan Releases", "?p=report.releasing", 0, 20, 235);
//		Menu5_5 = new Array("Summary of Payments", "?p=report.payment", 0, 20, 235);
		Menu5_5 = new Array("Summary of Payments/Collection", "?p=report.paymentbranch", 0, 20, 235);
		Menu5_6 = new Array("Summary of Branch Collection", "?p=report.collectbranch", 0, 20, 235);
		Menu5_7 = new Array("Payment/Collection Calendar", "?p=report.wcalendar", 0, 17, 220);
		Menu5_8 = new Array("---------------------------------------------", "", 0, 17, 220);
		Menu5_9 = new Array("Withdrawal Schedule/Date Summary", "?p=report.withdrawday", 0,  20, 220);
		Menu5_10 = new Array("Periodic ATM Summary", "?p=report.wperiodic", 0,  20, 220);
		Menu5_11 = new Array("Individual ATM Report", "?p=report.windividual", 0,  20, 220);
		Menu5_12 = new Array("Uncollected Accounts for the Period", "?p=report.uncollected", 0,  20, 220);
		Menu5_13 = new Array("In/Out of Passbook/ATM", "?p=atmmonitor", 0,  20, 220);
		Menu5_14 = new Array("List of Active Accounts", "?p=report.activeaccount", 0,  20, 220);
		Menu5_15 = new Array("Passbook/ATM Inventory per Bank", "?p=report.passbookinventory", 0, 20, 22);
		Menu5_16 = new Array("Penalty Report", "?p=report.penalty", 0, 20, 150);
		Menu5_17 = new Array("Interest Income Report", "?p=report.interestincome", 0, 20, 220);
		Menu5_18 = new Array("Aging of Accounts", "?p=report.aging", 0, 20, 220);
		Menu5_19 = new Array("Summary for Accounting Entry", "?p=report.accounting", 0, 20, 150);
		Menu5_20 = new Array("List of Overrides", "?p=report.override", 0, 17, 150);
		Menu5_21 = new Array("Interest Income Straight Line", "?p=report.interest1", 0, 17, 150);
		Menu5_22 = new Array("Fund Transfer Report", "?p=report.fundxfer", 0, 17, 150);
		Menu5_23 = new Array("Receivable Listing w/ Penalty", "?p=report.receivable2", 0, 17, 150);
		Menu5_24 = new Array("Pensioner Listing w/ birthdate", "?p=report.customers", 0, 17, 150);

	Menu6 = new Array("Account Queue","?p=waiting.browse", 2, 19, 150);	
		Menu6_1 = new Array("Account Queue","?p=waiting.browse", 0, 19, 150);	
		Menu6_2 = new Array("Manual Queue","?p=manpenque", 0, 19, 150);
			
	Menu7 = new Array("Logout","?p=logout", 0, 19, 100);
	Menu8 = new Array("<img src='../graphics/home.gif'>Home","?p=", 2, 19, 100);
		Menu8_1 = new Array("Financing", "?p=", 0, 17, 150);
		Menu8_2 = new Array("Back To Main", "../", 0, 17, 150);
//-->	
</SCRIPT>
<SCRIPT src="../js/menucom.js" type=text/javascript></SCRIPT>