<script language="JavaScript" type="text/JavaScript">

function vSubmit(ul)
{
	if (ul.name == 'Cancel')
	{
		if (confirm("Are you sure to CANCEL account Record?"))
		{
			document.f2.action="?p=report.accountledger&p1=CancelConfirm"
		}	
		else
		{
			document.f2.action="?p=report.accountledger&p1=Cancel"
		}
	}
	else if (ul.name=='Go')
	{
		document.f1.action="?p=report.accountledger&p1="+ul.id;
	}	
	else
	{
		document.f2.action="?p=report.accountledger&p1="+ul.id;
	}
}
</script>
<?
$this->View->setPageTitle('Account Ledger');

if (!session_is_registered('aLedger'))
{
	session_register('aLedger');
	$aLedger = null;
	$aLedger = array();
}
if (!session_is_registered('aLedgerDetail'))
{
	session_register('aLedgerDetail');
	$aLedgerDetail = null;
	$aLedgerDetail = array();
}

if ($p1=='Selectreleasing' )
{
	$aLedger=null;
	$aLedger=array();

	$module = 'accountledger';
	$q = "select * from accountrems where account_id = '{$c_id}' and module='{$module}'";
	$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
	$r = pg_fetch_object($qr);
	$remarks = $r->remark;
		
	$q = "select 
				account.account_id,
				account.account,
				account.account_group_id,				
				account.address,
				salary,
				clientbank_id
		 from 
		 		account
		where 
				account.account_id='$c_id'";
	$r = fetch_assoc($q);
	$aLedger = $r;

	$aLedger['account_id'] = $_REQUEST['c_id'];
	$aLedger['releasing_id'] = $_REQUEST['r_id'];
	$aLedger['show'] = $_REQUEST['show'];
	$aLedger['account_group']=lookUpTableReturnValue('x','account_group','account_group_id','account_group',$aLedger['account_group_id']);

	$clientbank='';
	if ($aLedger['clientbank_id'] > '0')
	{
		$clientbank = lookUpTableReturnValue('x','clientbank','clientbank_id','clientbank',$aLedger['clientbank_id']);
	}
}
if ($aLedger['account_id'] != '')
{
	$details = '<small3>';
	$details .= center('A C C O U N T   L E D G E R',80)."\n";
	$details .= center($SYSCONF['BUSINESS_NAME'],80)."\n";
	$details .= center($SYSCONF['BUSINESS_ADDR'],80)."\n";
	$details .= center('Date Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$details .= 'Customer: '.adjustSize($aLedger['account'],35).'  '.'Pension:'. number_format($aLedger['salary'],2)."\n";
	$details .= 'Group   : '.adjustSize($aLedger['account_group'],20).' Bank:'.$clientbank."\n";
	
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	$details .= '    Date       Reference       Debit      Withdrawn     Credit    Excess      Balance '."\n";
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";

	$aRep = null;
	$aRep = array();
	
	$q = "select * 
				from
					releasing
				where
					account_id = '".$aLedger['account_id']."'";
					
	if ($aLedger['releasing_id'] != '')
	{
		$q .=  " and releasing_id = '".$aLedger['releasing_id']."'";
	}
	else
	{
		if ($aLedger['show'] == 'B')
		{
			$q .= " and balance > '0'";
		}
		elseif ($aLedger['show'] == 'P')
		{
			$q .= " and balance <= '0'";
		}

	}
	$qr = @pg_query($q) or message(pg_errormessage());
	
	$total_ammort = $total_obligation =0;
	while ($r = @pg_fetch_object($qr))
	{

		$total_ammort += $r->ammort;
		$total_obligation += $r->gross;
		
		$dummy = null;
		$dummy = array();
		
		$balance = $r->gross - $r->advance_payment;
		$dummy['date'] = $r->date;
		$dummy['type'] = 'C';
		$dummy['reference'] = lookUpTableReturnValue('x','admin','admin_id','username',$r->admin_id);
		$dummy['debit'] =  $r->gross;
		$dummy['releasing_id'] =  $r->releasing_id;
		$dummy['credit'] =$r->advance_payment;
		$dummy['excess'] = 0.00;
		$dummy['withdrawn'] = 0.00;
		$dummy['balance'] = $balance;
		$dummy['rem1'] = "   Loan Type : ".lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id).
						"   Mode :".($r->mode=='S' ? 'Semi-Monthly' : ($r->mode=='W' ? 'Weekly' : 'Monthly')).
						"   Terms :".$r->term.'  RecNo. '.str_pad($r->releasing_id,8,'0',str_pad_left);
		$dummy['rem2'] = "   Principal :".adjustRight(number_format($r->principal,2),12)." ".
						"   Ammortization : ".number_format($r->ammort,2);				

		$aRep[] = $dummy;
		
		$q = "select * 
						from 
							payment_header,
							payment_detail
						where
							payment_header.payment_header_id=payment_detail.payment_header_id and
							payment_header.status!='C' and
							payment_detail.releasing_id='$r->releasing_id'";

		$qpr = @pg_query($q) or message(pg_errormessage());

		while ($rp = @pg_fetch_object($qpr))
		{
			$dummy = null;
			$dummy = array();

			if ($rp->admin_id > '0')
			{
			
				$dummy['reference'] = lookUpTableReturnValue('x','admin','admin_id','username',$rp->admin_id);
			
			}
			else
			{
				$dummy['reference'] ='';
			}
			$balance -= $rp->amount;
			$dummy['date'] = $rp->date;
			$dummy['type']= 'D';
			$dummy['debit'] =  0.00;
			$dummy['releasing_id'] =  $rp->releasing_id;
			$dummy['credit'] =$rp->amount;
			$dummy['excess'] = $rp->excess;
			$dummy['withdrawn'] = $rp->withdrawn;
			$dummy['balance'] = $balance;
			$aRep[] = $dummy;
		}
	}


	$atemp = null;
	$atemp = array();
	foreach ($aRep as $temp)
	{
		$temp1=$temp['releasing_id'].$temp['date'].$temp['reference'];
		$atemp[]=$temp1;
	}
		
	if (count($atemp) > 0)
	{
		asort($atemp);
		reset($atemp);
	}
	
	$ln=0;
	$sub_debit = $sub_credit = $sub_withdrawn = $sub_excess = 0;
//	foreach ($aRep as $temp)
//	{
	while (list ($key, $val) = each ($atemp))
	{
			$temp=$aRep[$key];
			
			if ($temp['type'] == 'C')
			{
				if ($ln > 1)
				{
					$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
					$details .= space(9).adjustSize('Sub Total',20).
							adjustRight(number_format2($sub_debit,2),12).' '.
							adjustRight(number_format2($sub_withdrawn,2),10).' '.
							adjustRight(number_format2($sub_credit,2),10).' '.
							adjustRight(number_format2($sub_excess,2),10).' '.
							adjustRight(number_format($sub_balance,2),10)."\n";

					$details .= "\n";
					$lc++;
				}
				
				$details .= $temp['rem1']."\n";
				$details .= $temp['rem2']."\n";
				
				$balance = $sub_debit = $sub_credit = $sub_withdrawn = $sub_excess = 0;
				$lc = $lc + 2;
				$ln=0;
			}
			$balance += ($temp['debit'] - $temp['credit']);
			
			$ln++;
			$details .= adjustRight($ln,2).'. '.
							adjustSize(ymd2mdy($temp['date']),10).' '.
							adjustSize($temp['reference'],10).' '.
							adjustSize($temp['type'],2).' '.
							adjustRight(number_format2($temp['debit'],2),12).' '.
							adjustRight(number_format2($temp['withdrawn'],2),10).' '.
							adjustRight(number_format2($temp['credit'],2),10).' '.
							adjustRight(number_format2($temp['excess'],2),10).' '.
							adjustRight(number_format($balance,2),10)."\n";
							
			$accountbalance += $temp['debit'] - $temp['credit'];
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'];
			$total_excess += $temp['excess'];
			$total_withdrawn += $temp['withdrawn'];
			$total_balance = $balance;

			$sub_debit += $temp['debit'];
			$sub_credit += $temp['credit'];
			$sub_excess += $temp['excess'];
			$sub_withdrawn += $temp['withdrawn'];
			$sub_balance = $balance;
			
							

			$lc++;	
			if ($lc > 55)
			{			
				if ($p1=='Print Draft')
				{
					$details .= "<eject>";
					doPrint($details);
				}
			}
		}
	
	if ($sub_debit != '0' ||  $sub_credit != '0')
	{
		$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
		$details .= space(9).adjustSize('Sub Total',20).
				adjustRight(number_format2($sub_debit,2),12).' '.
				adjustRight(number_format2($sub_withdrawn,2),10).' '.
				adjustRight(number_format2($sub_credit,2),10).' '.
				adjustRight(number_format2($sub_excess,2),10).' '.
				adjustRight(number_format($sub_balance,2),10)."\n";
	}
	
	$details .= '--- ---------- ---------- -- ------------ ---------- ---------- ---------- ------------'."\n";
	$details .= space(9).adjustSize('Grand Total',20).
			adjustRight(number_format2($total_debit,2),12).' '.
			adjustRight(number_format2($total_withdrawn,2),10).' '.
			adjustRight(number_format2($total_credit,2),10).' '.
			adjustRight(number_format2($total_excess,2),10).' '.
			adjustRight(number_format2($total_balance,2),10)."\n\n";

	$details .= space(10).'Total Obligation......'.adjustRight(number_format($total_obligation,2),12)."\n";
	$details .= space(10).'Total Ammortization...'.adjustRight(number_format($total_ammort,2),12)."\n";
	$details .= space(10).'Total Balance.........'.adjustRight(number_format($accountbalance,2),12)."\n\n";
	$details .= 'Remarks :'."\n";
	$details .= $remarks."\n";
	$details1 .= $details;
	if ($p1=='Print Draft')
	{
		$details .= "<eject>";
		doPrint($details);
	}
}
?>
<form action="" method="post" name="f1" id="f1" class="row well">
    <div class="col-lg-12">
        <div class="col-lg-4 pad-left-0">
            <input name="xSearch" type="text" id="xSearch" value="<?= $xSearch;?>" placeholder="Search Account Name"  class="form-control">
        </div>
        <div class="col-lg-3">
            <?=lookUpAssoc('sortby',array('Name'=>'account','Releasing No'=>'releasing_id'), $sortby, null, "form-control");?>
        </div>
        <div class="col-lg-3">
            <?=lookUpAssoc('show',array('Show All'=>'A','Show Balance'=>'B','Show First'=>'F', 'Show Paid'=>'P'), $show, null, "form-control");?>
        </div>
        <div class="col-lg-2">
            <input name="p1" type="submit" id="p1" value="Go" class="btn btn-primary">
        </div>
    </div>
</form>
<?
  if ($p1 == 'Save Remarks' and $aLedger['account_id']!=0)
  {
  		$module = 'accountledger';
  		$q = "select * from accountrems where account_id = '".$aLedger['account_id']."' and module='$module'";
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
		$r = pg_fetch_object($qr);
		if ($r->account_id == $aLedger['account_id'])
		{
			$q = "update accountrems set remark = '$remarks' where 
							account_id = '".$aLedger['account_id']."' and module='$module'";
			$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
		} else
		{
			$date = date('Y-m-d');
			$q = "insert into accountrems (account_id,modules,remark,date) values 
										('".$aLedger[account_id]."','$modules','$remarks','$date')";
			@pg_query($q) or message(pg_errormessage().$q);
		} 
  }
  if ($p1 == 'Go')
  {
	  $q = "select * 
				from 
					account
				where 
					account ilike '$xSearch%' and
					enable 
				order by
					account";
					
					
		$qr = pg_query($q)	or message("Error Querying account file...".pg_errormessage());
?>
    <div class="row">
        <table class="table table-stripped table-responsive">
            <thead>
                <tr>
                    <td width="7%" align="center">#</td>
                    <td width="38%">Name</td>
                    <td width="21%">Account Group</td>
                    <td width="11%">Status</td>
                    <td width="12%" align="center">Balance</td>
                    <td width="11%" align="center">&nbsp;</td>
                </tr>
            </thead>
          <?
            $ctr=0;
            while ($r = pg_fetch_assoc($qr))
            {
                $account_balance=accountBalance($r['account_id']);
                $ctr++;
          ?>
          <tr>
            <td align="right" nowrap width="7%" height="22">
              <?=$ctr;?>. </td>
            <td width="38%" height="22">
              <a href="javascript:document.getElementById('f1').action='?p=report.accountledger&p1=Selectreleasing&c_id=<?= $r['account_id'];?>';document.getElementById('f1').submit();">
              <?= $r['account'];?>
              </a> </td>
            <td width="21%" height="22">
              <?= lookUpTableReturnValue('x','account_group','account_group_id','account_group',$r['account_group_id']);?>
              </td>
            <td width="11%" height="22">
              <?= status($r['account_status']);?>
              </td>
            <td align="right" width="12%" height="22">
              <?= number_format($account_balance,2);?>
              </td>
            <td width="11%" height="22"> &nbsp;
              <a href="?p=report.accountledger&p1=Selectreleasing&show=<?=$show;?>&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>">
            All</a></td>
          </tr>
          <?
                $q = "select * from releasing where status!='C' and account_id='".$r['account_id']."'";
                $qqr = @pg_query($q) or message(pg_errormessage());
                if (@pg_numrows($qqr)>1)
                {
                    while ($rr= @pg_fetch_object($qqr))
                    {
                ?>
          <tr>
            <td width="7%"></td>
            <td width="38%"></td>
            <td width="21%">
              <a href="javascript:document.getElementById('f1').action='?p=report.accountledger&p1=Selectreleasing&c_id=<?= $r['account_id'];?>&r_id=<?= $rr->releasing_id;?>';document.getElementById('f1').submit();">
              <?= ymd2mdy($rr->date);?></a>
              </td>
            <td align="right" width="11%">
              <?= number_format($rr->principal,2);?>
              </td>
            <td align="right" width="12%">
              <?= number_format($rr->balance,2);?>
              </td>
            <td width="11%"> &nbsp;
              <a href="?p=report.accountledger&p1=Selectreleasing&show=1&r_id=<?=$rr->releasing_id;?>&c_id=<?=$r['account_id'];?>&xSearch=<?=$xSearch;?>">
            Select</a></td>
          </tr>
          <?
                    }
                }
          }
          ?>
        </table>
    </div>

<?	
  }
  elseif ($aLedger['account_id'] != '')
  {
?>
        <form action="" method="post" name="f2" id="f2" style="margin:0">
        <table width="85%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#EFEFEF">
          <tr>
            <td colspan="4"><strong>Account
              Ledger</strong></td>
          </tr>
          <tr>
                <td valign="top" bgcolor="#FFFFFF">
               <textarea name="print_area" cols="97" rows="18" readonly wrap="OFF"><?= $details1;?></textarea>
                <br>Remarks<br>
                <textarea name="remarks" cols="97" id="remarks"><?= $remarks;?>
        </textarea> </td>
          </tr>
        </table>
          <div align="center">
            <input name="p1" type="button" id="p1" value="Print Draft"  onClick="document.getElementById('f1').action='?p=report.accountledger&p1=Print Draft';document.getElementById('f1').submit()">
            <input name="p1" type="button" id="p1" value="Print"  onClick="printIframe(print_area)" >
            <input name="p1" type="button" id="p1" value="Save Remarks">
          </div>
        </form>
        <iframe name="printit" id="printit" style="width:0px;height:0px;"></iframe>

<?
}
?>

