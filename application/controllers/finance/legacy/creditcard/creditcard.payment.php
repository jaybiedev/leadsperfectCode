<?
if (!session_is_registered('aPAY'))
{
	session_register('aPAY');
	$aPAY = null;
	$aPAY = array();
}
if ($p1 == 'Load' && $id!='')
{
	$aPAY= null;
	$aPAY = array();
	$q = "select bankcard_transaction.bankcard_id,
				bankcard_transaction.status,
				bankcard_transaction.amount,
				bankcard_transaction.gross,
				bankcard_transaction.date,
				bankcard_transaction.date_paid,
				bankcard_transaction.paid,
				bankcard_transaction.trace,
				bankcard_transaction.reference,
				bankcard_transaction.bankcard_transaction_id,
				bankcard.bankcard,
				bankcard.name,
				bankcard.address,
				bankcard_type.bankcard_type,
				bankcard_type.bankcard_type_id,
				bankcard_type.percent_bankcharge,
				bankcard_type.service_charge
			from 
				bankcard_transaction, bankcard, bankcard_type
			where
				bankcard_type.bankcard_type_id=bankcard.bankcard_type_id and
				bankcard.bankcard_id=bankcard_transaction.bankcard_id and
				bankcard_transaction_id='$id'";
	$qr = @pg_query($q) or message(pg_errormessage());
	$r = @pg_fetch_assoc($qr);
	
	$aPAY = $r;
	
	if ($aPAY['status'] == 'A' and $aPAY['paid'] == 0)
	{
		$aPAY['paid'] = $aPAY['amount'] + $aPAY['amount']*($aPAY['percent_bankcharge']/100);
		$aPAY['date_paid'] = date('Y-m-d');
	}
}	
elseif ($p1 == 'Save' && $aPAY['paid'] > 0)
{
	$aPAY['paid'] = $_REQUEST['paid'];
	$aPAY['date_paid'] = mdy2ymd($_REQUEST['date_paid']);
	if ($aPAY['date_paid'] == '--')
	{
		message("No Date Specified...");
	}
	else
	{
		$q = "update bankcard_transaction set date_paid='".$aPAY['date_paid']."', paid='".$aPAY['paid']."', status='P'
					where
							bankcard_transaction_id='".$aPAY['bankcard_transaction_id']."'";
		$qr = @pg_query($q) or message(pg_errormessage());
		if ($qr)
		{
			message("Payment Saved...");
		}
	}
}
?>
<form name="f2" method="post" action="">
  <table width="50%" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr> 
      <td colspan="3" background="../graphics/table0_horizontal.PNG"> <strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Bank 
        Reimbursement/Collection</font></strong></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Txn Date</font></td>
      <td colspan="2"><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= ymd2mdy($aPAY['date']);?>
        </font></td>
    </tr>
    <tr> 
      <td width="35%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card 
        Type</font></td>
      <td width="65%" colspan="2"> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['bankcard_type'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Card No.</font></td>
      <td colspan="2"> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['bankcard'];?>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Name of 
        Account</font></td>
      <td> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['name'];?>
        </font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Trace #</font></td>
      <td> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['trace'];?>
        </font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Receipt 
        # </font></td>
      <td> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['reference'];?>
        </font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Amount</font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif">Php 
        <?= number_format($aPAY['amount'],2);?>
        </font></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gross</font></td>
      <td> <font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        Php 
        <?= number_format($aPAY['gross'],2);?>
        </font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
    </tr>
    <tr> 
      <td colspan="3"><hr></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Status</font></td>
      <td><font color="#003366" size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?= $aPAY['status'];?>
        </font></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="date_paid" type="text" id="date_paid" value="<?= ymd2mdy($aPAY['date_paid']);?>" size="8" onBlur="IsValidDate(this,'MM/dd/yyyy')" onKeyUp="setDate(this,'MM/dd/yyyy','en')">
        <img src="../graphics/dwn-arrow-grn.gif" onclick="popUpCalendar(this, f2.date, 'mm/dd/yyyy')"> 
        </font></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Paid</font></td>
      <td><input name="paid" type="text" id="paid" value="<?= $aPAY['paid'];?>" size="10"></td>
      <td><img src="../graphics/savedisc.gif" width="99" height="24" onClick="f2.action='?p=creditcard.payment&p1=Save';f2.submit()"></td>
    </tr>
  </table>
</form>
<?
include_once('creditcard.monitor.php');
?>
