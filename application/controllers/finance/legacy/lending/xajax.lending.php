<?
	$xajax->registerFunction('savePayment');
	function savePayment($form) 
	{
	
		global $aPay, $aPayD, $ADMIN, $SYSCONF;
		
		$msd = date('Y-m-d');
		$med = date('Y-m-d',strtotime ('-3 day' , strtotime ( $msd)));
		if($aPay['date'] < $med and $ADMIN['usergroup'] != 'A' )
		{	
			galert(" You have no permission to edit this dates.... ");
			return done();
		}
		if($aPay['date'] > $msd and $ADMIN['usergroup'] != 'A' )
		{
			galert(" You have no permission to edit this dates.... ");
			return done();
		}
		if($aPay['payment_header_id'] == '' && count($aPayD) == 0)
		{
			galert(" Nothing to save.  Maybe you have not clicked Ok.... ");
			return done();
		}

		$fields_header = array('reference','date','name','total_amount',
									'mcheck','account_group_id');

		for ($c=0;$c<count($fields_header);$c++)
		{
			if ($fields_header[$c] == 'date')
			{
				$aPay[$fields_header[$c]] = mdy2ymd($form[$fields_header[$c]]);
			}
			else
			{
				$aPay[$fields_header[$c]] = $form[$fields_header[$c]];
				if ($aPay[$fields_header[$c]] == '' )
				{
					$aPay[$fields_header[$c]] = '0';
				}
			}	
		}

		$aPay['total_amount'] = str_replace(',','',$aPay['total_amount']);

		if ($aPay['total_amount'] == '' or $aPay['total_amount'] == 0) 
		{
			$aPay['total_amount'] = $aPay['sub_withdrawn'];
		}
	
		if ($aPay['remarks'] == 'ADVANCE PAYMENT')
		{
			$aPay['total_amount'] =0;
		}
		
		
		//begin();
		$ok_save = 1;		
		if ($aPay['payment_header_id'] == '')
		{
			$q = "insert into payment_header (reference,date,name,account_group_id,total_amount,mcheck, admin_id)
					values ('".$aPay['reference']."','".$aPay['date']."','".$aPay['name']."',
							'".$aPay['account_group_id']."','".$aPay['total_amount']."','".$aPay['mcheck']."','".$ADMIN['admin_id']."')";
			$qr = @pg_query($q);
			if (!$qr)
			{
				$ok_save = 0;
				galert(pg_errormessage().$q);
			//	rollback();
				return done();
			}
			if ($qr and pg_affected_rows($qr)>0)
			{
				$qr = query("select currval('payment_header_payment_header_id_seq'::text)");
				$r = pg_fetch_object($qr);
				$aPay['payment_header_id']=$r->currval;
				$aPay['status']='S';
				$ok_save = 1;			
			}
		}
		else
		{
			$audit = $aPay['audit'].'; Updated on '.date('m/d/Y g:ia').' by:'.$ADMIN['username'];
			$q = "update payment_header set audit='$audit' ";
			for ($c=0;$c<count($fields_header);$c++)
			{
				$q .= ", ".$fields_header[$c]."= '".$aPay[$fields_header[$c]]."'";
			}
			$q .= " where payment_header_id='".$aPay['payment_header_id']."'";
		
			$qr = @pg_query($q);
			if (!$qr)
			{
			//	rollback();
				$ok_save = 0;
				galert (pg_errormessage().$q);
				return done();
			}
			else
			{
				$ok_save = 2;
			}
		}
				

		if ($aPay['payment_header_id'] != '')
		{	
		
			if ($ok_save == 2) 		//--if edited
			{
				$ql = "update ledger set status='C' 
								where
									type='C' and 
									reference='".$aPay['payment_header_id']."'";
									
				$qrl = @pg_query($ql);
	

				if (!$qrl)
				{
				//	rollback();
					galert("Error Updating Ledger Unposts...".pg_errormessage().$q);
					return done();
				}
				
				//--recalculate Balances										 
				$ql = "select * 
								from 
									ledger 
								where 
									type='C' and 
									reference='".$aPay['payment_header_id']."'";

				$qrl = @pg_query($ql);

				if (!$qrl)
				{
				//	rollback();
					galert("Error Querying Ledger Uposts...".pg_errormessage().$q);
					return done();
				}
				
				
				while ($rl = @pg_fetch_object($qrl))
				{
					@recalculate($rl->releasing_id, 'noneform');
				}
		

			} //-- end of ledger unposting

			reset($aPayD);	
			$c=$lc=0;

			foreach ($aPayD as $temp)
			{
				
				if ($temp['account_id'] == '')
				{
					galert(" Entry Line No. ".($c+1)." CANNOT be saved. It has no account specified, Please check...");
					continue;
				}

				// begin();

				$dummy = $temp;
				$payment_balance = $temp['amount'];
				$account_id = $temp['account_id'];
				if ($temp['ddate'] == '//' || $temp['ddate']=='' || $temp['ddate']=='--')
				{
					$ddate = $aPay['date'];
					$temp['ddate'] = $ddate;
				}
				else
				{
					$ddate = $temp['ddate'];
				}
				
				if ($temp['releasing_id'] == '') $temp['releasing_id'] = 0;

				$qc = "select account_class_id , account_group, 
							account_group.account_group_id, withdraw_day
						from 
							account_group,
							account
						where
							account_group.account_group_id = account.account_group_id and
							account.account_id = '$account_id'";
				$qcr = @pg_query($qc);


				if (!$qcr)
				{
				//	rollback();
					galert(pg_errormessage().$qc);
					return done();
				}
				$rc = @pg_fetch_object($qcr);
				$account_class_id = $rc->account_class_id;
				
				$withdraw_day = $rc->withdraw_day;


				//--insert to payment detail
				if ($temp['payment_detail_id'] == '')
				{
					$q = "insert into payment_detail (account_id, releasing_id, payment_header_id, amount, ddate, delete, withdrawn, excess, remark)
							values 
								('".$temp['account_id']."','".$temp['releasing_id']."','".$aPay['payment_header_id']."',
								'".$temp['amount']."','$ddate','f', '".$temp['withdrawn']."', '".$temp['excess']."', 
								'".addslashes($temp['remark'])."')";
									

					$qr = @pg_query($q);

					if (!$qr)
					{
					//	rollback();
						galert(pg_errormessage().$q);
						return done();
					}
					else
					{
						$qrid = @query("select currval('payment_detail_payment_detail_id_seq'::text)");
						$rpid = @pg_fetch_object($qrid);
						$temp['payment_detail_id']=$rpid->currval;
						$dummy['payment_detail_id']=$rpid->currval;
					}
					$aPayD[$c] = $dummy;
				}
				elseif ($temp['record_status'] == 'deleted' && $temp['payment_detail_id'] != '')
				{
					$q = "delete from payment_detail where payment_detail_id='".$temp['payment_detail_id']."'";
					$qr = @pg_query($q);
					if (!$qr)
					{
					//	rollback();
						galert(pg_errormessage().$q);
						return done();
					}
					$c++;
					continue;
				}
				elseif ($temp['payment_detail_id'] != '')
				{
					$q = "update payment_detail set 
								account_id='".$temp['account_id']."',
								releasing_id='".$temp['releasing_id']."',
								amount = '".$temp['amount']."',
								withdrawn = '".$temp['withdrawn']."',
								excess = '".$temp['excess']."',
								ddate = '$ddate',
								delete='f'
							where
								payment_detail_id='".$temp['payment_detail_id']."'";
								
					$qr = @pg_query($q);
					
					if (!$qr)
					{
					//	rollback();
						galert(pg_errormessage().$q);
						return done();
					}
				}
				if ($temp['payment_detail_id'] =='')
				{
				//	rollback();
					galert(" No Payment Detail Record Number Generated....");
				}
				else
				{				
				
										
					$qq = "select 
									releasing.releasing_id,
									releasing.account_id,
									releasing.date as releasing_date,
									releasing.ammort as ammort,
									releasing.balance,
									releasing.term,
									releasing.withdraw_day
 
								from
									releasing
								where
									releasing.balance>0 and
									status!='C' and
									status!='U' and
									account_id = '".$temp['account_id']."'
								order by
									releasing.date  ";
									
					$qqr = @pg_query($qq);

					if (!$qqr)
					{
					//	rollback();
						galert("Error Querying Loans...".pg_errormessage().$q);
						return done();
					}			
					
					//--iterate payment application to loans/ledger
					
					$loan_count = 0;
					$updflag = 0;
					
					while ($rr = @pg_fetch_assoc($qqr))
					{
						$loan_count++;
						if ($rr['withdraw_day'] ==0 or is_null($rr['withdraw_day']))
							$rr['withdraw_day'] = $withdraw_day;

						if ($account_class_id != 1)
						{
							//-- none SSS
							if (@pg_num_rows($qqr) == $loan_count)
							{
								//-- apply the remaining payment balance to the 
								//-- last loan; unfortunately not the oldest
								$loan_balance = $payment_balance;
							}
							else
							{
								$aDue = amountDue($rr, $ddate);
								if ($aDue['amount_due'] <= 0) continue;
								$loan_balance = $aDue['amount_due'];
							}
						}
						else
						{
							//-- SSS
							$aDue = amountDue($rr, $ddate);

							if ($aDue['amount_due'] <= 0) continue;
							$loan_balance = $aDue['amount_due'];
						}
								
						$lc++; //-- ledgers affected...
						$ledger_id = '';
						
						$ql = "select * 
										from 
											ledger 
										where 
											type='C' and 
											reference='".$aPay['payment_header_id']."' and
											releasing_id = '".$rr['releasing_id']."' and
											account_id = '".$rr['account_id']."'";

						$qrl = @pg_query($ql);

						if (!$qrl)
						{
						//	rollback();
							galert("Error Querying Ledger...".pg_errormessage().$q);
							return done();
						}
						if (@pg_num_rows($qrl) > 0)
						{
							$rl = @pg_fetch_object($qrl);
							$ledger_id = $rl->ledger_id;
						}
						$credit = $loan_balance;
						if ($payment_balance > $credit)
						{
							$payment_balance -= $credit;						
						}
						else
						{
							$credit = $payment_balance;
							$payment_balance=0;
						}
					
						$releasing_id_latest = $rr['releasing_id'];
						$updflag=1;

						if ($ledger_id == '')
						{
							$ql = "insert into ledger (account_id, releasing_id,  date, 
															reference, type, credit)
									values
										('".$temp['account_id']."','".$rr['releasing_id']."','$ddate',
											'".$aPay['payment_header_id']."','C','$credit')";
							$qrl = @pg_query($ql);
							if (!$qrl)
							{
							//	rollback();
								galert(pg_errormessage().$ql);
								return done();
							}
							else
							{
								$qpid = @query("select currval('ledger_ledger_id_seq'::text)");
								$rpid = @pg_fetch_object($qpid);
								$temp['ledger_id']=$rpid->currval;
								$dummy['ledger_id']=$rpid->currval;
								$aPayD[$c] = $dummy;
							}
						}
						else
						{
							$ql  = "update ledger set
											status='S',
											date = '$ddate',
											credit = '$credit'
									where
											releasing_id = '".$rr['releasing_id']."' and
											account_id = '$account_id' and
											reference = '".$aPay['payment_header_id']."'";
							$qrl = @pg_query($ql);
							
							if (!$qrl)
							{
					//			rollback();
								galert(pg_errormessage().$ql);
								return done();
							}

						}


						@recalculate($rr['releasing_id'],'noneform');

						if ($payment_balance*1 <= '0') 
						{
							break;
						}

					}	//-- end of iteration to ledger			
				
				} // -- if payment_detail_id
				
				if ( $payment_balance*1 > 0 and $updflag==0)
				{
							$ql  = "update ledger set
											status='S',
											date = '$ddate',
											credit = '$payment_balance'
									where
											account_id = '$account_id' and
											reference = '".$aPay['payment_header_id']."'";
							$qrl = @pg_query($ql);
//											releasing_id = '".$rr['releasing_id']."' and
				}

				if ($temp['excess'] > 0 && $releasing_id_latest!='')
				{
					$dummy['releasing_id'] = $releasing_id_latest;
					$q = "update payment_detail set releasing_id = '$releasing_id_latest' 
								where payment_detail_id='".$dummy['payment_detail_id']."'";
					//galert($q);
					//return done();
					$qpd = @pg_query($q);
					if (!$qpd)
					{
						galert('Error Updating  PD Excess/ Releasing Id '.pg_errormessage().$q);
					}
					
				}
				$aPayD[$c] = $dummy;
				$c++;
				
				//commit();	
			}
			
		} //-- if payment_header_id
		
		
		if ($ok_save == '2')
		{
		//	commit();
			galert("Payment/Collection Updated...\n ". ($lc)." Account Ledger(s) Updated...");
		}
		elseif ($ok_save == '1')
		{
		//	commit();
			$aPay['status'] ='S';
			galert("Payment/Collection Saved...\n ". ($lc)." Account Ledger(s) Updated...");
		}
		else
		{
			$aPay['payment_header_id'] = '';
		//	rollback();
		}	
		gset('ph_id',$aPay['payment_header_id']);
	
		return done();
	}


	$xajax->registerFunction('selectRedeemType');
	function selectRedeemType($form) 
	{
		global $SYSCONF;
		$mcheck = $form['mcheck'];
		if ($mcheck == 'G')
		{
			gset('excess',$SYSCONF['REDEEM_CHARGE']);
			gset('account_group_id','99999');
		}
		else
		{
			gset('excess',$SYSCONF['TRANSFER_CHARGE']);
		}
		return done();
	}
	
	$xajax->registerFunction('computeRedeem');
	function computeRedeem($form) 
	{
		$loan_due = str_replace(',','',$form['amount']);
		$charges = str_replace(',','',$form['excess']);
		$discount = str_replace(',','',$form['discount']);
		$charges = $form['excess'];

		$total_due = $loan_due + $charges - $discount;
		gset('withdrawn',number_format($total_due,2));
		return done();
	}
	$xajax->registerFunction('saveRedeem');
	function saveRedeem($form) 
	{
		global $aRedeem, $ADMIN, $SYSCONF;

		if ($aRedeem['payment_header_id'] != '' && !chkRights2('payment','medit',$ADMIN['admin_id']))
		{
			galert("You have no permission to update/modify entry..");
			return done();
		}
		elseif ($aRedeem['payment_header_id'] != '' && !chkRights2('payment','madd',$ADMIN['admin_id']))
		{
			galert("You have no permission to Save entry..");
			return done();
		}


		$fields = array('date','amount','excess','withdrawn','remark','mcheck','reference','account_group_id','discount','discrem');

		$ok_save = 1;
		for ($c=0;$c<count($fields);$c++)
		{
			if (substr($fields[$c],0,4) == 'date' or $fields[$c] == 'advance_applied')
			{
				if ($form[$fields[$c]] == ''or $form[$fields[$c]]=='--')
				{
					$aRedeem[$fields[$c]] = '';
				}
				else
				{
					$aRedeem[$fields[$c]] = mdy2ymd($form[$fields[$c]]);
				}
			}
			else
			{
				$aRedeem[$fields[$c]] = $form[$fields[$c]];
				if ($aRedeem[$fields[$c]] == '' && !in_array($fields[$c],array('remarks','remark','reference')))
				{
					$aRedeem[$fields[$c]] = 0;
				}
				else
				{
					$aRedeem[$fields[$c]] = str_replace(',','',$aRedeem[$fields[$c]]);
				}
			}	

		}
		if ($aRedeem['payment_header_id'] == '')
		{
				$q = "insert into payment_header (
								date,reference,total_amount,mcheck,account_group_id,discrem,admin_id)
							values
								('".$aRedeem['date']."',
								'".$aRedeem['reference']."',
								'".$aRedeem['withdrawn']."',
								'".$aRedeem['mcheck']."',
								'".$aRedeem['account_group_id']."',
								'".$aRedeem['discrem']."',
								'".$ADMIN['admin_id']."')";
				$qr = @pg_query($q);
				if (!$qr)
				{
					$ok_save=0;
					galert("Error Saving Payment Header...".pg_errormessage().$q);
				}				 
				else
				{
					$q = "select currval('payment_header_payment_header_id_seq'::text)";
					$qir = @pg_query($q);
					if (!$qir)
					{
						galert("error ".pg_errormessage().$q);
					}
						
					$ri = @pg_fetch_object($qir);
					$aRedeem['payment_header_id'] = $ri->currval;
				}
		}
		else
		{
				$q = "update payment_header set
								date = '".$aRedeem['date']."',
								reference = '".$aRedeem['reference']."',
								mcheck = '".$aRedeem['mcheck']."',
								discrem = '".$aRedeem['discrem']."',
								account_group_id = '".$aRedeem['account_group_id']."',
								total_amount ='".$aRedeem['withdrawn']."'
							where
								payment_header_id = '".$aRedeem['payment_header_id']."'";
				$qr = @pg_query($q);
				if (!$qr)
				{	
					$ok_save = 0;
					galert("Error Updating Payment Header...".pg_errormessage().$q);
				}				 
				else
				{
					$ok_save=2;
				}
		}		
		if ($aRedeem['account_group_id'] > '0')
		{
			//-- use account_group_id field as transfer_to_branch_id
/*			$qa = "update account set branch_id='".$aRedeem['account_group_id']."'
						where account_id = '".$aRedeem['account_id']."'";
			@pg_query($qa);*/
			
// If transfer, add new account and transfer old profile data to new account of new branch
			if ($aRedeem['account_group_id'] != 0)
			{
//				$account_code = accountCode($aRedeem['account_group_id']);
// ------------ Generate Account Code
				$branch_id = $aRedeem['account_group_id'];
				$q = "select * from branch where branch_id = '$branch_id'";
				$qr = @pg_query($q) or message(pg_errormessage());
				$r = @pg_fetch_object($qr);
				
				$q = "select * from cache where type='account_code' and value1='$r->branch_id'";
				$qqr = @pg_query($q) or message(pg_errormessage());
				$rr = @pg_fetch_object($qqr);
				if (@pg_num_rows($qqr) == 0)
				{
					$value2=1;
			
					while (true)
					{
						$account_code = $r->branch_code.'-'.str_pad($value2,5,'0', STR_PAD_LEFT);
						$q = "select * from account where account_code= '$account_code'";
						$qr = @pg_query($q) or message(pg_errormessage().$q);
						if (@pg_num_rows($qr) == 0)
						{
							break;
						}
						$value2++;
					}
			
			
					$q = "insert into cache (type,value1,value2,description) values ('account_code','$rr->branch_id','$value2','$rr->branch_code')";
					@pg_query($q) or message(pg_errormessage().$q);			
				}
				else
				{
					$value2 = $rr->value2+1;
					while (true)
					{
						$account_code = $r->branch_code.'-'.str_pad($value2,5,'0', STR_PAD_LEFT);
						$q = "select * from account where account_code= '$account_code'";
						$qr = @pg_query($q) or message(pg_errormessage().$q);
						if (@pg_num_rows($qr) == 0)
						{
							break;
						}
						$value2++;
					}
					$q = "update cache set value2='$value2' where type='account_code' and value1='$branch_id'";
					@pg_query($q) or message(pg_errormessage().$q);
				}	
//--------- End of generate account code
				$temp = null;
				$temp = array();
				$qt = "select * from account where account_id = '".$aRedeem['account_id']."'";
				$qrt=@pg_query($qa);
				$temp = @pg_fetch_assoc($qrt);
				$qn = "insert into account 
						(account_code, account, address, ofc_telno, telno, 
						 collection_type_id, account_group_id,
						 civil_status, account_status, ofc_address, office, 
						 clientbank_id, bank_pin, 
						 bank_account, bank_cardno, salary,sss,
						 comaker1,comaker1_address, 
						 comaker2,comaker2_address,
						 comaker1_relation,  comaker2_relation,
						 branch_id,remarks,
						 date_birth, age, 
						 spouse, spouse_sss, 
						 gender, mclass, date_child21,
						 npension,nchangebank,
						 withdraw_day, enable)
					values 
						('$account_code','".$temp['account']."','".$temp['address']."',
						'".$temp['ofc_telno']."','".$temp['telno']."','".$temp['collection_type_id']."',
						'".$temp['account_group_id']."',
						'".$temp['civil_status']."',
						'".$temp['account_status']."',
						'".$temp['ofc_address']."','".$temp['office']."',
						'".$temp['clientbank_id']."','".$temp['bank_pin']."',
						'".$temp['bank_account']."','".$temp['bank_cardno']."',
						'".$temp['salary']."','".$temp['sss']."',
						'".$temp['comaker1']."','".$temp['comaker1_address']."',
						'".$temp['comaker2']."','".$temp['comaker2_address']."',
						'".$temp['comaker1_relation']."','".$temp['comaker2_relation']."',
						'".$aRedeem['account_group_id']."','".$temp['remarks']."',
						'".$temp['date_birth']."','".$temp['age']."',
						'".$temp['spouse']."','".$temp['spouse_sss']."',
						'".$temp['gender']."', '".$temp['mclass']."', '".$temp['date_child21']."', 
						'".$temp['npension']."','".$temp['nchangebank']."',
						'".$aaccount['withdraw_day']."','".$aaccount['enable']."')";
				$qrn=@pg_query($qn);
			}
// Disable old account profile				
				$qa = "update account set enable='N'
							where account_id = '".$aRedeem['account_id']."'";
				@pg_query($qa);			
			
		}
		if ($aRedeem['payment_header_id'] != '')
		{
			if ($aRedeem['payment_detail_id'] == '')
			{
				$q = "insert into payment_detail (
								payment_header_id, ddate,account_id,withdrawn,mischarge,discount,amount,remark)
							values
								('".$aRedeem['payment_header_id']."',
								'".$aRedeem['date']."',
								'".$aRedeem['account_id']."',
								'".$aRedeem['withdrawn']."',
								'".$aRedeem['excess']."',
								'".$aRedeem['discount']."',
								'".$aRedeem['amount']."',
								'".$aRedeem['remark']."')";
				$qr = @pg_query($q);
				if (!$qr)
				{
					$ok_save = 0;
					galert("Error Saving Payment Detail...".pg_errormessage().$q);
				}				 
				else
				{
					$qir = @query("select currval('payment_detail_payment_detail_id_seq'::text)");
					$ri = @pg_fetch_object($qir);
					$aRedeem['payment_detail_id'] = $ri->currval;
				}
			}
			else
			{
				$q = "update payment_detail set
								ddate = '".$aRedeem['date']."',
								account_id ='".$aRedeem['account_id']."',
								withdrawn = '".$aRedeem['withdrawn']."',
								mischarge = '".$aRedeem['excess']."',
								discount = '".$aRedeem['discount']."',
								amount = '".$aRedeem['amount']."',
								remark = '".$aRedeem['remark']."'
							where
								payment_detail_id = '".$aRedeem['payment_detail_id']."'";
				$qr = @pg_query($q);

				if (!$qr)
				{	
					$ok_save=0;
					galert("Error Updating Payment Header...".pg_errormessage().$q);	
				}				 
							
			}
		}
		
		if ($aRedeem['payment_detail_id'] != '')
		{
			$q = "select * from releasing where account_id ='".$aRedeem['account_id']."' and status!='C' and balance>0";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert('Error Querying Loan Releasing....');
			}
			else
			{
				while ($r = @pg_fetch_object($qr))
				{
					$q = "select * from ledger where releasing_id = '$r->releasing_id' and
							account_id = '$r->account_id' and
							type='C' and
							reference='".$aRedeem['payment_header_id']."'";
					$qlr = @pg_query($q);
					if (!$qr)
					{
						galert('Error Querying Loan Ledger....');
					}
					else
					{
						if (@pg_num_rows($qlr) == 0)
						{
							$qq = "insert into ledger (account_id, releasing_id, date, type, credit, reference)
										values
											('".$aRedeem['account_id']."','$r->releasing_id','".$aRedeem['date']."',
											'C','$r->balance','".$aRedeem['payment_header_id']."')";

							$qqr = @pg_query($qq);
							if (!$qr)
							{
								galert('Error Saving Loan Ledger....'.pg_errormessage().$qq);
							}
							else
							{
								recalculate($r->releasing_id,'noneform');
							}								
						}
						else
						{
								//-- update
						}
					}
												
				}
			}
			
			include_once('accountbalance.php');
		
			$aBal = excessBalance($aRedeem['account_id']);
			//--update wexcess ledger		
			if ($aBal['balance'] < 0)
			{
				$excessbalance = abs($aBal['balance']);
				
				$q = "select * 
						from 
							wexcess 
						where 
							account_id = '".$aRedeem['account_id']."' and
							type='".$aRedeem['mcheck']."' and  
							remarks = 'REDEEMCREDIT' and
							ps_remark = '".$aRedeem['payment_header_id']."'";
				$qr = @pg_query($q);
				if (!$qr)
				{
					galert(pg_errormessage().$q);
				}
				else
				{

					if (@pg_num_rows($qr) == 0)
					{
						$q = "insert into wexcess (type,date,account_id, gross_amount, remarks, ps_remark, admin_id, audit)
								values ('".$aRedeem['mcheck']."','".$aRedeem['date']."','".$aRedeem['account_id']."', '$excessbalance',
								'REDEEMCREDIT','".$aRedeem['payment_header_id']."','".$ADMIN['admin_id']."',
								'Credit from Loan Redeem')";

						$qr = @pg_query($q);
						if (!$qr)
						{
							galert(pg_errormessage().$q);
						}
							 
					}
					else
					{
						$r = @pg_fetch_object($qr);
						$q = "update wexcess set date='".$aRedeem['date']."', 
										gross_amount='$excessbalance'
									where
										wexcess_id = '$r->wexcess_id'";
						
						
						$qr = @pg_query($q);	

						if (!$qr)
						{
							galert(pg_errormessage().$q);
						}
											
						
					}
				}
			}
			
		}
		if ($ok_save)
		{
			gset('ph_id',$aRedeem['payment_header_id']);
		}
		if ($ok_save == '2')
		{
			gset('ph_id',$aRedeem['payment_header_id']);
			galert('Transaction Updated...Click Print for Voucher...');
			$aRedeem['status']='S';
		}
		elseif ($ok_save == '1')
		{
			gset('ph_id',$aRedeem['payment_header_id']);
			galert('Transaction Saved...Click Print for Voucher...');
			$aRedeem['status']='S';
		}
		return done();
	}
	
	$xajax->registerFunction('paymentDownload');
	function paymentDownload($form) 
	{
		global $SYSCONF;
		$date = $form['date'];
		$filename = $form['filename'];
			
		if ($date == '')
		{
			galert('No date specified...'.$form['date']);
			return done();
		}
		
		if ($filename == '')
		{
			galert('No Filename specified...'.$form['filename']);
			return done();
		}
		$mdate = mdy2ymd($date);
		
		$q = "select 
						ph.payment_header_id,
						ph.date,
						ph.reference,
						ph.account_group_id,
						ph.entry_type,
						ph.date_withdrawn,
						ph.withdraw_day,
						ph.clientbank_id,
						ph.total_amount,
						ph.mcheck,
						pd.account_id,
						pd.delete,
						pd.releasing_id,
						pd.ddate,
						pd.mconfirm,
						pd.withdrawn,
						pd.amount,
						pd.excess,
						pd.remark,
						account.account_code,
						account.branch_id,
						admin.name as user
					from 
						payment_header as ph,
						payment_detail as pd,
						account,
						admin
					where 
						ph.payment_header_id = pd.payment_header_id and
						account.account_id = pd.account_id and
						admin.admin_id=ph.admin_id and  
						ph.date >= '$mdate' and 
						ph.status!='C' and
						account.branch_id='".$SYSCONF['BRANCH_ID']."'";
		
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
			return done();
		}

		$details = '';
		$fields_header = array('payment_header_id','date','reference','account_group_id','entry_type',
						'date_withdrawn','withdraw_day','clientbank_id','total_amount',
						'mcheck','user','branch_id');

		$fields_detail = array('payment_header_id','account_id','account_code','delete','releasing_id','ddate',
						'mconfirm','withdrawn','amount','excess','remark');
		$c=0;
		$payment_header_id = '';
		while ($r = @pg_fetch_assoc($qr))
		{
			if ($payment_header_id != $r['payment_header_id'])
			{
				$details .= 'PAYMENTHEADER||';
				for ($c = 0;$c<count($fields_header);$c++)
				{
					if ($c>0)
					{
						$details .= '||';
					}
					$details .= $r[$fields_header[$c]];
				}
				$details .="\n";
				$payment_header_id=$r['payment_header_id'];
			}	
			for ($c = 0;$c<count($fields_detail);$c++)
			{
				if ($c>0)
				{
					$details .= '||';
				}
				$details .= $r[$fields_detail[$c]];
			}
			$details .="\n";
		}		

		$fl = "../var/".$filename;
		
  		$handle = @fopen($fl,"w+");
  		if (!$handle)
  		{
  			galert('Cannot create file...'.$fl);
  			return done();
  		}
  		
  		$w = @fwrite($handle,$details);
  		if (!$w)
  		{
  			galert('Cannot write into file...'.$fl);
  			return done();
  		}
  		fclose($handle);

		if (file_exists($fl))
		{
  			$t = "<table align=\"center\">";
  		
  			$t .= "<tr><td><a href='$fl'><h3>$filename</a></h3></td></tr>";
  			$t .="</table>";
  		
		 	glayer('grid.layer','Finished Downloading Account Info...Please Right Click File (Save Target) To Download...'."\n".$t);
		}
		else
		{
			galert('Unable to create File for Account Info into Target Destination '.$s);
		}
		glayer('wait.layer','');
		return done();
		
	}

	$xajax->registerFunction('savePaymentUpload');
	function savePaymentUpload($form)
	{
		global $ADMIN, $SYSCONF, $aPay, $aPayD;
		
		$good_ctr = $bad_ctr = $ctr = 0;
		$badcontent = $goodcontent = '';
		pg_query("BEGIN TRANSACTION");
		foreach ($aPay as $temp)
		{
				$ctr++;
				$checker_remarks = 'UPLOAD PHID:'.$temp['payment_header_id'];
				$q = "select * from payment_header where remarks='$checker_remarks'";
				$qr = @pg_query($q);
				if (!$qr)
				{
					galert(pg_errormessage().$q);
					return done();
				}
				
				if (@pg_num_rows($qr) > 0)
				{
					$badctr++;
					$error = 'Transaction Already Posted...';
					$account_group = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$temp['account_group_id']); 
					$badcontents .= "<tr class=\"gridRow\"><td align='right'>$badctr.</td>
								<td><input type='checkbox' name='delete[]' id='b$ctr' value='$ctr'></td>
								<td>".$account_group."</td>
								<td>".ymd2mdy($temp['date'])."</td>
								<td>".$temp['mcheck']."</td>
								<td align='right'>".number_format($temp['total_amount'],2)."</td>
								<td>&nbsp;".$temp['user']." ERR $error </td>
								</tr>";
					continue;
				}
				
				$audit = 'Encoded by: '.$temp['user'].';Uploaded by:'.$ADMIN['name'];
			 	$q0 = "insert into payment_header (remarks,admin_id, audit,";
			 	$q1 = " values ('$checker_remarks','".$ADMIN['admin_id']."','$audit',";

				$c=0;
				foreach ($temp as $key => $value)
				{
				
					$value = addslashes($value);
					if ($value == ''&& in_array($key,array('account_group_id','clientbank_id','withdraw_day','total_amount')))
					{
						$value = 0;
					}
					if (in_array($key,array('payment_header_id','user','branch_id')))
					{
						continue;
					}
					if ($c>0)
					{
						$q0 .= ',';
						$q1 .= ',';
					}
					$q0 .= $key;
					$q1 .= "'$value'";
					$c++;
			 	}
			 	$q0 .= ")";
			 	$q1 .= ")";
			 	$q = $q0.$q1;
			 	
			 	$qr = @pg_query($q);
			 	
				$qir = @query("select currval('payment_header_payment_header_id_seq'::text)");
				$ri = @pg_fetch_object($qir);

				$payment_header_id=$ri->currval;

			 	if ($qr && @pg_affected_rows($qr)>0 && $payment_header_id != '')
			 	{
					//details
					foreach ($aPayD as $temp1)
					{
							if ($temp['payment_header_id'] != $temp1['payment_header_id'])
							{
								 continue;
							}
							$dctr++;	

						 	$q0 = "insert into payment_detail ( payment_header_id,";
						 	$q1 = " values ( '$payment_header_id',";	

							$c=0;
							foreach ($temp1 as $key => $value)
							{
								$value = addslashes($value);
								if ($value == ''&& in_array($key,array('account_id','amount','withdrawn','excess')))
								{
									$value = 0;
								}
								if (in_array($key,array('payment_header_id','user','branch_id','account_code')))
								{
									continue;
								}
								if ($c>0)
								{
									$q0 .= ',';
									$q1 .= ',';
								}
								$q0 .= $key;
								$q1 .= "'$value'";
								$c++;
						 	}
				 			$q0 .= ")";
						 	$q1 .= ")";
						 	$q = $q0.$q1;
			 	
						 	$qr = @pg_query($q);
							if (!$qr)
							{
							
								galert("Error Posting To Payment Details...".pg_errormessage());
								@pg_query("ROLLBACK TRANSACTION");
								return done();
							}

							$qr = query("select currval('payment_detail_payment_detail_id_seq'::text)");
							$r = pg_fetch_object($qr);
							$temp1['payment_detail_id']=$r->currval;

							$payment_heder_id = $temp1['payment_header_id'];

							if ($temp1['account_code']!='')
							{
								$q = "select account_id from account where account_code='".$temp1['account_code']."'";
								$qr = @pg_query($q);
								$r = @pg_fetch_object($qr);
								$account_id = $r->account_id;
							}
							else
							{
								$account_id = $temp1['account_id'];
							}			
							
							$ddate = $temp1['ddate'];
							
							//--insert to ledger
							if ($temp1['releasing_id'] != '')
							{

								$credit = $temp1['amount'];
	
								$q = "insert into ledger (account_id, releasing_id,  date, reference, type, credit, admin_id)
										values
											('$account_id','".$temp1['releasing_id']."','$ddate',
												'$payment_header_id','C','$credit','".$ADMIN['admin_id']."')";
								$qr = @pg_query($q) or message(pg_errormessage());
								if (!$qr)
								{
									@pg_query("ROLLBACK TRANSACTION");
									
									galert("Error Posting To Ledger...".pg_errormessage());
									return done();
								}
					
								recalculate($temp1['releasing_id'],'noneform');
							}
							//

					}

					//--display
					$goodctr++;
					$account_group = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$temp['account_group_id']); 
					$goodcontents .= "<tr  class=\"gridRow\"><td align='right'>$goodctr.</td>
								<td><input type='checkbox' name='delete[]' id='g$ctr' value='$ctr'></td>
								<td>".$account_group."</td>
								<td>".ymd2mdy($temp['date'])."</td>
								<td>".$temp['mcheck']."</td>
								<td align='right'>".number_format($temp['total_amount'],2)."</td>
								<td>&nbsp;".$temp['user']."</td>
								</tr>";
				}
				else
				{
					if (!$qr)
					{
						
						$error = pg_errormessage().$q;
					}
					else
					{
						$error = 'NOT ADDED';
					}
					$badctr++;
					$account_group = lookUpTableReturnValue('x','account_group','account_group_id','account_group',$temp['account_group_id']); 
					$badcontents .= "<tr class=\"gridRow\"><td align='right'>$badctr.</td>
								<td><input type='checkbox' name='delete[]' id='b$ctr' value='$ctr'></td>
								<td>".$account_group."</td>
								<td>".ymd2mdy($temp['date'])."</td>
								<td>".$temp['mcheck']."</td>
								<td align='right'>".number_format($temp['total_amount'],2)."</td>
								<td>&nbsp;".$temp['user']." ERR $error </td>
								</tr>";

				}

			@pg_query("COMMIT TRANSACTION");	
		}		
		$contents = "<table width='100%' cellpadding='0' cellspacing='0'>";
		$contents .= "<tr><td colspan='5' bgColor='#DADADA'>Good Records</td></tr>";
		$contents .= $goodcontents;
		
		$contents .= "<tr><td colspan='5' bgColor='#FF9999'>Bad Records</td></tr>";
		$contents .= $badcontents;
			
		$contents .="</table>";
		glayer('grid',$contents);
	
		galert($goodctr ." Good Information and ".$badctr." Bad Information ('NOT Saved')\n     Please Check Information ");		
		return done();
	}

$xajax->registerFunction('uniqueAccount');
function uniqueAccount($form)
{
	global $aaccount;
	
	
	$account = strtoupper($form['account']);
	$account_id = $aaccount['account_id'];
	if ($account_id == '') $account_id = 0;
	
	$q = "select * from account where upper(account)='$account' and account_id != '$account_id'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
	} 
	if (@pg_num_rows($qr)>0)
	{
		glayer('wait.layer','');
		galert("Account Already Exists...Please Check...");
		gset("account","");
		$script = "document.getElementById('account').focus()";
		gscript($script);

	}
	return done();
	
}

//-- Reports
$xajax->registerFunction('rInterestIncome');
function rInterestIncome($form, $output) 
{

	global $SYSCONF;
	
	$account_group_id = $form['account_group_id'];
	$loan_type_id = $form['loan_type_id'];
	$month = $form['month'];
	$year = $form['year'];
	
	if (strlen($year)!= 4)
	{
		glayer('Bad Year Specified....');
		return done();
	}
	if (strlen($month) == 1) $month = '0'.$month; 
	$yearmonth = $year.'-'.$month;
	
	$q = "select
				account.account,
				account.account_id,
				account.account_group_id,
				releasing.loan_type_id,
				releasing.releasing_id,
				releasing.balance,
				loan_type.eir,
				loan_type.loan_type,
				ledger.credit,
				ledger.date
			from
				account,
				ledger,
				releasing,
				loan_type
			where
				account.account_id = ledger.account_id and
				releasing.releasing_id = ledger.releasing_id and
				loan_type.loan_type_id = releasing.loan_type_id and  
				ledger.status!='C' and
				loan_type.eir>0 and 
				substring(ledger.date,1,7) = '$yearmonth'";
	if ($account_group_id != '')
	{
		$q .= " and account.account_group_id = '$account_group_id'";
	}
	if ($loan_type_id != '')
	{
		$q .= " and releasing.loan_type_id = '$loan_type_id'"; 
	}
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert('Error Querying Ledger...'.pg_errormessage().$q);
		return done();
	}
	
	$header .= center(rtrim($SYSCONF['BUSINESS_NAME']),80)."\n";
	$header .= center('INTEREST INCOME REPORT',80)."\n";
	$header .= center('For '.cmonth($month).', '.$year,80)."\n";
	$header .= center('Printed '.date('m/d/Y g:ia'),80)."\n\n";
	$header .= space(5)."----- ------------------------------- ------------ ------------ \n";
	$header .= space(5)."  #   Account                        Collected     IIncome \n";
	$header .= space(5)."----- ------------------------------- ------------ ------------ \n";
	$details = ''; 
	$total = $ctr = 0;
	
	while ($r = @pg_fetch_object($qr))
	{
		$q = "select sum(credit) as paidafter
					from
						ledger
					where
						substring(date,1,7) > '$yearmonth' and
						releasing_id = '$r->releasing_id' and
						status!='C'";			  
		$qqr = @pg_query($q);
		if (!$qqr)
		{
			galert('Error Summing Payments...'.pg_errormessage().$q);
			return done();
		}
		
		$rr= @pg_fetch_object($qqr);
		
		$runbalance = $r->balance + $rr->paidafter;
		$interestincome = $runbalance*$r->eir;
		
		$ctr++;
		$details .= space(5).
						adjustRight($ctr,4).'. '.adjustSize($r->account,30).' '.
						adjustRight(number_format($r->credit,2),12)." ".	
						adjustRight(number_format($interestincome,2),12)."\n";	
		$total += $interestincome;
		$collected += $r->credit;
	}		
	$details .= space(5)."----- ------------------------------- ------------ ------------ \n";

	$details .= space(5).adjustSize('TOTAL ',35).' '.
					adjustRight(number_format($collected,2),13)." ".
					adjustRight(number_format($total,2),12)."\n";
	$details .= space(5)."----- ------------------------------- ------------ ------------\n";

	$details1 = $header.$details;
	gset('textarea', $details1);
	return done();
}

//--
$xajax->registerFunction('computepenalty');
function computepenalty($form) 
{
	global $ADMIN, $aPenalty;
	
	$aPenalty = null;
	$aPenalty = array();
	$withinloan = $form['withinloan'];
	$beyondloan = $form['beyondloan'];
	$month = $form['month'];
	$year = $form['year'];
	$rid = $form['rid'];
	$date = mdy2ymd($form['date']);
	$branch_id = $form['branch_id'];
	
	
	if (strlen($year)!=4 || $year<2000 || $year>2050)
	{
		galert("The specified Year is Invalid...");
		return done();
	}
	if ($month == '' or $month=='0')
	{
		galert("The specified Month is Invalid...");
		return done();
	}
	$account_class_id = $form['account_class_id'];
	if (strlen($month) == 1) $month = '0'.$month;

	$now = $year.'-'.$month;
	
	$q = "select
				releasing.balance,
				releasing.date as loan_date,
				releasing.term,
				releasing.ammort,
				releasing.releasing_id,
				releasing.account_id,
				releasing.tpenalty,
				releasing.withdraw_day as withday_loan,
				account.account,
				account.withdraw_day
			from
				releasing,
				account,
				account_group
			where
				account.account_id=releasing.account_id and 
				account.account_group_id=account_group.account_group_id and
				account_group.account_class_id = '$account_class_id' and
				releasing.status!='C' and account_group != 'w/ compromise'";

	if ($branch_id > 0 and $branch_id < 99) $q .= " and account.branch_id='$branch_id' ";
/*	if ($ADMIN['branch_id'] > '0')
	{
		$q .= " and (branch_id ='".$ADMIN['branch_id']."'";
		if ($ADMIN['branch_id2'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id2']."'";
		if ($ADMIN['branch_id3'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id3']."'";
		if ($ADMIN['branch_id4'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id4']."'";
		if ($ADMIN['branch_id5'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id5']."'";
		if ($ADMIN['branch_id6'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id6']."'";
		if ($ADMIN['branch_id7'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id7']."'";
		if ($ADMIN['branch_id8'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id8']."'";
		if ($ADMIN['branch_id9'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id9']."'";
		if ($ADMIN['branch_id10'] > '0') $q .= " or branch_id ='".$ADMIN['branch_id10']."'";
		$q .= ") ";
	}
*/				
	if ($rid != '')
	{
		$q .= " and releasing_id = '$rid'";
	}
	else
	{ 
		$q .= " and releasing.balance>'1'";
	}
			
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert('Error Querying Loans...'.@pg_errormessage().$q);
		return done();
	}
	$ctr=0;

	while ($r = @pg_fetch_object($qr))
	{

		$aid = $r->account_id;
		$tpenalty = $r->tpenalty;

		$qp = "select date, credit from ledger where releasing_id = '$r->releasing_id' and type='D'";
		$qpr = @pg_query($qp);
		$rap = pg_fetch_object($qpr);
		$tpapp = 1;
		if ($rap->credit > 0)  // with advance payment
		{
			$appmonth = intval($rap->credit/$r->ammort);
			if ($r->withday_loan == 0)
				$withdraw_day = $r->withdraw_day;
			else	
				$withdraw_day = $r->withday_loan;

			$loanday = substr($r->loan_date,8,2);
			if ( $loanday > $withdraw_day) $appmonth++;
			$dterm = '+'.$appmonth.' months';
			$ldate = strtotime($r->loan_date);
			$appdate = date('Y-m-d',strtotime($dterm,$ldate));
			if (substr($appdate,0,7) > $now)
				$tpapp = 0;
			else
				$tpapp = 1;	
		}
//	if ($ADMIN[admin_id]==1)
//		galert('test point '.$qp.' now '.$now.'  appdate '.$appdate.' $tpapp '.$tpapp);	
			
		if ($tpapp > 0)
		{	
			$qp = "select date, credit from ledger where releasing_id = '$r->releasing_id' and status!='C'";
			$qpr = @pg_query($qp);

			while ($rp = @pg_fetch_object($qpr))
			{
				$date = $rp->date;
				if (substr($date,0,7) == $now)
					$pay = $rp->credit;
			}
	
			recalculate($r->releasing_id,'noneform');
	
			if ($r->withday_loan == 0)
				$withdraw_day = $r->withdraw_day;
			else	
				$withdraw_day = $r->withday_loan;
	
			$loan_date = $r->loan_date;
			$ammort = $r->ammort;
			
			$q = "select count(*), payment_detail.amount
						from
							payment_header,
							payment_detail
						where
							payment_detail.payment_header_id=payment_header.payment_header_id and
							payment_header.status!='C' and
							payment_detail.releasing_id='$r->releasing_id'
							and	substring(payment_detail.ddate,1,7) = '$now'	
					group by
							payment_detail.amount";
	
	//		$q .= " order by ddate offset 0 limit 1";
		
			$qqr = @pg_query($q);				
	
			if (!$qqr)
			{
				galert('Error Querying Payments...'.@pg_errormessage().$q);
				return done();
			}
			$rr = @pg_fetch_object($qqr);
			$amount = $count = 0;
			$amount = $rr->amount;
	
			if ($rr->count > 0) $count = $rr->count;
			else $count = 0;
			
			if ($count == 0 || ($amount < $ammort))
			{
	
				//== post penalties here
	
				$temp = null;
				$temp = array();
				
				if ($count == 0)
				{
					$amt_due = $ammort;
					$amount = 0;
				} 
				else
				{
					$amt_due = $ammort - $amount;
				}
				
				$ald = explode('-',$loan_date);
				
				$term = $r->term;
				$month = $ald[1]+$term;
				
				if ($ald[2] < $withdraw_day)
				{
					//-- if loan date day is before atm withdrawal day
					$month--;
				}
				
				$_year = intval($month/12);
	
				$myear = $ald[0]+$_year;
				$mmonth= $month%12;
	
				if (strlen($mmonth) == 1) $mmonth = '0'.$mmonth;
				
				$duedate = $myear.'-'.$mmonth;
				$penalty=0;
				$amountdue = $r->ammort - $pay;
	
//	if ($ADMIN[admin_id]==1)
//		galert($count.', amountdue '.$amountdue.' < ammort '.$ammort.',  now '.$now.' <= duedate '.$duedate);		
				if ($now <= $duedate)
				{
					if ($amountdue <= 0) 
					{
						$pay = 0;
						continue;
					}	
					//withinloan rate is per month 
					$penalty = round($amountdue*($withinloan/100),2);
	//				$penalty = round($amt_due*($withinloan/100),2);
					$remark = 'Within Term (Payment Made '.$pay.')';
				}
				else
				{
					$q = "select balance from releasing where releasing_id = '$r->releasing_id'";
					$qqr = @pg_query($q);
					if (!$qqr)
					{
						galert('Error Collecting Loan Balance...');
						return done();
					}
					else
					{
						$rr = @pg_fetch_object($qqr);
					}
					//-- rate is per anum
					if ($rr->balance <= 0) 
					{
						$pay = 0;
						continue;
					}	
					$penalty = round($rr->balance*($beyondloan/1200),2);
					$amountdue = $rr->balance;
					$remark = 'Over Term';
				
				}			
				if ($r->tpenalty!=1)
				{
					$ctr++;
					$tr .= "<tr><td align='right'><font size='2'>$ctr. </font>
								<input type='checkbox' id=r'$r->releasing_id' name='delete[]' value='$releasing_id' ></td>
							<td><font size='2'>$r->account</font></td>
							<td><font size='2'>$r->releasing_id</font></td>
							<td><font size='2'>".ymd2mdy($r->loan_date)."</font></td>
							<td><font size='2'>".number_format($amountdue,2)."</font></td>
							<td align='right'><font size='2'>".number_format($penalty,2)."</font></td>
							<td><font size='2'>$remark</font></td>
							</tr>";
					$temp['releasing_id'] = $r->releasing_id;
					$temp['penalty'] = $penalty;
					$temp['account_id'] = $r->account_id;
					$pay = 0;
					$aPenalty[] = $temp;
				}					
			}
		}		
	}
	$table = "<table width='100%'>";
	$table .= "<tr bgColor='#DADADA'><td align='right'><font size='2'>#</font></td>
					<td><font size='2'>Account</font></td>
					<td><font size='2'>Reference</font></td>
					<td><font size='2'>LoanDate</font></td>
					<td><font size='2'>AmtDue</font></td>
					<td align='right'><font size='2'>Penalty</font></td>
					<td><font size='2'>Remark</font></td>
					</tr>";

	$table .= $tr."</table>";
	
	glayer('wait.layer','');
	glayer('grid',$table);
	galert("Finished Processing Penalties...\nClick Save to POST Penalties to ledger");
	return done();
}

$xajax->registerFunction('savePenalty');
function savePenalty($form) 
{
	global $aPenalty, $ADMIN;
	
	$date = mdy2ymd($form['date']);
	$withinloan = $form['withinloan'];
	$beyondloan = $form['beyondloan'];
	$month = $form['month'];
	$year = $form['year'];
	
	if (strlen($month) == 1) $month = '0'.$month;
	$remarks= $month.'/'.$year;
	//$reference = $month.'/'.$year;
	
	foreach ($aPenalty as $temp)
	{
		$q = "select * 
					from 
						ledger 
					where 
						releasing_id ='".$temp['releasing_id']."' and
						remarks = '$remarks' and
						type='P'";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage());
			return done();
		}
		if (@pg_num_rows($qr) == 0)
		{
			$q = "insert into ledger 
							(account_id, releasing_id, type, date, debit,remarks, admin_id)
						values
							('".$temp['account_id']."', '".$temp['releasing_id']."',
							'P','$date','".$temp['penalty']."', '$remarks','".$ADMIN['admin_id']."')";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert(pg_errormessage());
				return done();
			}
		}
		else
		{
			$rr = @pg_fetch_object($qr);
			$q = "update ledger set admin_id = '".$ADMIN['admin_id']."',
								debit = '".$temp['penalty']."',
								remarks = '$remarks'
							where
								ledger_id = '$rr->ledger_id'";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert(pg_errormessage());
				return done();
			}

		}				
		recalculate($temp['releasing_id'],'noneform');
	}
	
	$q = "select * from process where type='PENALTY' and date='$date'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage());
		return done();
	}
	if (@pg_num_rows($qr) == 0)
	{
		$q = "insert into process (date, admin_id, type, value1, value2)
					values ('$date','".$ADMIN['admin_id']."', 'PENALTY',
								'$withinloan','$beyondloan')";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage());
			return done();
		}

	}
	glayer('wait.layer','');
	galert("Finished Posting Penalties To Ledger...");
	return done();
}


$xajax->registerFunction('paymentapplication');
function paymentapplication($form) 
{
	global $iPayD;

	$temp = null;
	$temp = array();
	$releasing_id = $iPayD['releasing_id'];
	$account_id = $form['account_id'];
	$withdrawn = $form['withdrawn'];
	$balance = $form['balance'];
	$account_group_id =  $form['account_group_id'];
	$amount_due = 0;
	$balance = $iPayD['balance'];
	$mddate = mdy2ymd($form['ddate']);		 // applicable withdrawal date
	$mdate = mdy2ymd($form['date']);         // collection date
	
	if ($account_id == '' || $withdrawn=='') return done();
		
	
	$q = "select account_class_id , account_group, 
				account_group.account_group_id, withdraw_day
					from 
						account_group,
						account
					where
						account_group.account_group_id = account.account_group_id and
						account.account_id = '$account_id'";
	$qr = @pg_query($q);
	$r = @pg_fetch_object($qr);
	$withdraw_day = $r->withdraw_day;
	
	if ($account_group_id == '' or $account_group_id=='0')
	{
		gset('account_group_id',$r->account_group_id);
		gset('selectAccountGroup',$r->account_group);
	}
	
	
	if ($r->account_class_id == 99)
	{
		//-- none SSS
		gset('amount',$withdrawn);
		gset('excess',0);
		return done();
	}					
	else
	{
		//--- For SSS
		$q = " select 
					releasing.releasing_id,
					releasing.date as releasing_date,
					releasing.ammort as ammort,
					releasing.balance,
					releasing.term,
					releasing.mode,
					withdraw_day
				from 
					releasing
				where
					releasing.account_id='$account_id' and
					releasing.status!='C' and
					balance>0";
		$qr = @pg_query($q);
		if (!$qr)
		{
			 galert(pg_errormessage().$q);
		}
					
		if ($mddate != '' || $mddate !='//' || $mddate != '--') $d = $mddate;
		elseif ($mdate != '' || $mdate !='//' || $mdate != '--') $d = $mdate;
		else $d = '';	
		$amount_due = $amount = 0;
if ($account_id == '22641')
{
//	print_r($r);
//	galert($d);
}		
		while ($r = @pg_fetch_assoc($qr))
		{
			if ($r[withdraw_day]==0) $r['withdraw_day'] = $withdraw_day;
			else $withdraw_day = $r['withdraw_day'];
			if ($r['mode']=='S') $r['ammort'] = $r['ammort']/2;
			$aDue = amountDue($r, $d);
			$amount_due = $aDue['amount_due'];

			if ($r['balance'] >= $amount_due)
			{
				$amount += $amount_due;
			}
			elseif ($r['balance']>0)
			{
				$amount += $r['balance'];
			}
		}
		
		if ($withdrawn >= $amount)
		{
			$excess=$withdrawn - $amount;
		}
		else
		{
			$excess = 0;
			$amount = $withdrawn;
		}
		$amount = round($amount,2);
		$iPayD['amount'] = round($amount,2);	

		gset('amount',$amount); 
		gset('excess',$excess);
	}		
	return done();
}

$xajax->registerFunction('checkOverride');
function checkOverride($form) 
{
	global $aLoan, $ADMIN;

	$account_id = $aLoan['account_id'];
	if ($account_id*1 == '0') 
	{
		gset('term','');
		return done();
	}
	
	$Q = "select account_class_id
			 from 
			 	account,
			 	account_group
			 where 
			 	account_group.account_group_id = account.account_group_id and
			 	account_id = '$account_id'";
	$QR = @pg_query($Q);
	$R = @pg_fetch_object($QR);
	if($R->account_class_id != 1)
	{
		return done();
	}
	//-- check max term only if SSS
	
	if (chkRights2('loanoverride','madd',$ADMIN['admin_id']))
	{
		$aLoan['override_id'] = $ADMIN['admin_id'];
		galert('Authorized Override for term '.$form['term'].'  Max Specified '.$form['max_term']);
		return done();
	}
	if ($aLoan['releasing_id'] == '')
	{
		$mtable ='account';
		$mtable_id = $aLoan['account_id'];
	}
	else
	{
		$mtable ='releasing';
		$mtable_id = $aLoan['releasing_id'];
	}
	
	$term = $form['term'];
	$loan_type_id = $form['loan_type_id'];
	$account_id = $aLoan['account_id'];
			
	$mdate = mdy2ymd($form['date']);
	$now = date('Y-m-d');

	$q= "select * 
				from 
					override
				where
					admin_id_request ='".$ADMIN['admin_id']."' and
					mtable = '$mtable' and
					mtable_id ='$mtable_id' and
					date_request = '$now' and
					module = 'releasing' and
					field = 'term' and 
					status = 'G'";
					
	$qr = @pg_query($q);
	
	if (!$qr)
	{
		galert(pg_errormessage().$q);
		gset('term','');
		return done();
	}
	if (@pg_num_rows($qr) > 0)
	{
		$r = @pg_fetch_object($qr);

		if ($r->field_id != $loan_type_id)
		{
			gset('term','');
			$rlt = lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$loan_type_id);
			$glt = lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->field_id);
			
			galert(" Request Loan Type ($glt) is Different from \n Currently Specified Loan Type ($rlt)");
			return done();
		}
		elseif (intval($term) > $r->value_grant)
		{
			gset('term',$r->value_grant);
		}
		else
		{
			gset('term',intval($term));
		}
		$aLoan['admin_id_override'] = $r->admin_id_override;
		
		galert(" Loan Term Override Request Granted ".$r->value_grant." Requested Term ".$r->value);

	}
	else
	{
		$q= "select * from override
				where
					admin_id_request ='".$ADMIN['admin_id']."' and
					mtable = '$mtable' and
					mtable_id ='$mtable_id' and
					date_request = '$now' and
					field = 'loan_term' and 
					module = 'releasing'";
					
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
			gset('term','');
			return done();
		}
		if (@pg_num_rows($qr) == 0)
		{
			gset('term','');
			galert(" Term Exceed Maximum Term Specified. Click [ ~ ]To Request for Override");
			return done();
		}
		else
		{
			$r = @pg_fetch_object($qr);
			if ($r->status == 'D')
			{
				galert(" Request for Override Term (".$r->value.") is DENIED...");
				gset('term','');
				return done();
			}
			else
			{
				galert(" Request for Override Term (".$r->value.") request, Pending Approval...");
				gset('term','');
				return done();
			}			
		}
	}
	return done();					
}

$xajax->registerFunction('grantloanoverride');
function grantloanoverride($id, $value) 
{
	global $ADMIN;
	$q = "update override set
					status='G',
					value_grant = '$value',
					admin_id_override = '".$ADMIN['admin_id']."'
				where
					override_id='$id'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
	}
	else
	{
		galert('Request for override granted...');
	}
	return done();
}

$xajax->registerFunction('denyloanoverride');
function denyloanoverride($id, $value) 
{
	$q = "update override set
					status='D',
					value_grant = '0'
				where
					override_id='$id'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
	}
	else
	{
		galert('Request for override Denied!...');
	}
	return done();
}


$xajax->registerFunction('loanoverride');
function loanoverride($form, $value) 
{
	global $aLoan, $ADMIN;

	$date = mdy2ymd($form['date']);
	$now = date('Y-m-d');
	$remarks = '';
	$term = $form['term'];
//	$value = $form['term'];
	$field = 'term';
	$loan_type_id = $form['loan_type_id'];
	$rate = $form['rate'];
	$mclass = $form['mclass'];	
	$account = $aLoan['account'];
	$account_id = $aLoan['account_id'];
	
	
	$aC = vMClass($mclass);


	$remarks = 'Loan Type:'.lookUpTableReturnValue('loan_type','loan_type','loan_type_id','loan_type',$loan_type_id);
	$remarks .= ' Rate:' .$rate.'; ';
	$remarks .= $aC['cmclass'].'; ';
	if ($aLoan['mclass'] == '2')
	{
		$remarks .= 'Child is '.$aLoan['date_child21'] .' years before 21';
		if ($aLoan['date_child21'] < '3')
			$remarks .= 'Max Term:'.$aLoan['date_child21']*12;
		else
			$remarks .= 36;	//36 months max for beneficiary or survivor
			
	}
	elseif ($aLoan['mclass'] == '4')
	{
			$remarks .= 'Max Term:'.' 6';  //months permanent disability
	}
	elseif ($aLoan['mclass'] == '5')
	{
			$remarks .= 'Max Term:'.' 6';		//temporary diability
			if ($aLoan['npension'] < '6')
				$remarks .= 'Max Term:'. $aLoan['npension'];

	}
	elseif ($aLoan['mclass'] == '6')
	{
			$remarks .= 'Max Term:'.' 6';		
			if ($aLoan['nchangebank'] > '2')
			{
				$remarks .= 'Max Term:'.' Change Bank > 2x Needs Approval';
			}

	}
	else
	{
		$max_term = $form['max_term'];
		$remarks .= 'Max Term:'.$max_term;
	}
	
	if ($aLoan['releasing_id'] == '')
	{
		$mtable='account';
		$mtable_id = $aLoan['account_id'];
	}
	else
	{
		$mtable='releasing';
		$mtable_id = $aLoan['releasing_id'];
	}
	if ($mtable_id == '') 
	{
		galert("No Linked Record Generated...");
		return done();
	}
	$q = "select * 
			from 
				override 
			where 
				date_request='$now' and 
				module='releasing' and
				field = 'term' and 
				mtable='$mtable' and 
				mtable_id = '$mtable_id'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
		return done();
	}			  
	elseif (@pg_num_rows($qr) > 0)
	{
		$r = @pg_fetch_object($qr);
		$override_id = $r->override_id;
		
		if ($r->status == 'G') //granted
		{
			if ($r->value_grant != '')
			{	
				$value_grant = $r->value_grant;
				gset($field, $r->value_grant);
			}
			else
			{
				$value_grant = $value;
			}
			$aLoan['admin_id_override'] = $r->admin_id_override;
			galert('Override Granted. With the Values for '.$field.' = '.$value_grant);
			return done();
		}
		elseif ($r->status == 'D')
		{
			galert('Request for Loan Term Override Denied...');
			return done();
		}
		$q = "update override set
							date_request = '$now',
							account = '$account',
							remarks = '$remarks',
							mtable = '$mtable',
							mtable_id= '$mtable_id',
							field = '$field',
							value = '$value',
							admin_id_request = '".$ADMIN['admin_id']."'
						where
							override_id = '$override_id'";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
			return done();
		}
		galert(" Request for term Override accepted and updated. Approval Pending... ");

	}
	else
	{
		$q = "insert into override (
						admin_id_request, date_request, account,
						module, mtable, mtable_id,
						field, field_id, value,remarks)
					values (
						'".$ADMIN['admin_id']."', '$now','$account',
						'releasing','$mtable','$mtable_id',
						'$field','$loan_type_id','$value','$remarks')";
		$qr = @pg_query($q);
						
	}
	if (!$qr)
	{
		galert(pg_errormessage().$q);
	}
	elseif (@pg_affected_rows($qr)>0)
	{
		galert("Request for override. Done...\n $remarks");
	}
	else
	{
		galert("Request Failed...");
	}			  
	return done();
}

$xajax->registerFunction('loanfees');
function loanfees($form) 
{
	global $aLoan, $ADMIN, $SYSCONF;
	
	$aLoan['status'] = 'M';
	$fields = array('max_term','principal','interest','rate_basis',
					'rate','term','loan_type_id','referral_fee','photo',
					'printout','atm_charge','other_charges','service_charge',
					'collection_fee','gross','ammort','mode','vat','advance_payment',
					'insurance','previous_balance','advance_change',
					'ca_balance','redeem','salary','insureamt');
					
	for ($c=0;$c<count($fields);$c++)
	{
		$$fields[$c] = str_replace(',','',$form[$fields[$c]]);
	}
   if ($principal*1 == '0') $principal = 0;

	if ($aLoan['account_id'] == '')
	{
		galert(" No Account Specified...");
		$aLoan['account_id'] = 0;
	}

	$loan_interest = 'A'; //add-on
	$account_class_id = lookUpTableReturn('x','account_group','account_group_id','account_class_id',$aLoan['account_group_id']);
	if ($loan_type_id == '') 
	{
		galert("Specify Loan Type...");
		$script = "document.getElementById('loan_type_id').focus()";
		gscript($script);
		gset('loan_type_id',1);		
		return done();
	}

	$q = "select * from loan_type where loan_type_id = '$loan_type_id'";
	$qr = @pg_query($q);
	if (!$qr)
	{
			galert(pg_errormessage());
	}

	$r = @pg_fetch_object($qr);
		
	if ($r->loan_rate != '0.00')
	{
			$rate = $r->loan_rate;
			gset('rate',$r->loan_rate);
	}
	$loan_interest = $r->loan_interest;
	$rate_basis = $r->basis;
	gset('rate_basis',$r->basis);

	if ($principal*1 == '0') return done();
	

	//--- computations
	if ($principal >0 && $rate>0 && $term>0)
	{
	
		if ($rate_basis=='A')
		{
			$interest = ($principal*($rate/100)/12)*$term;
		}
		else if ($rate_basis=='M')
		{
			$interest = $principal*($rate/100)*$term;
		}
		else if ($rate_basis=='I')
		{
			$interest = $principal*($rate/100)*$term;
		}
		else
		{
			galert("Loan Type Specified has NO basis of computation.");
		}
	}
	
	if ($loan_interest == '' or $loan_interest == 'A') //add-on
	{
		$loan_interest = 'A';
		$gross = round($principal +  $interest);
	}
	else
	{
		$gross = $principal;
	}

	if ($mode=='S')
	{
		$ammort = round($gross/($term*2),2);
	}
	else if ($rate_basis=='I')
	{
		$ammort = round($principal*($rate/100),2);
	}	
	else
	{
		$ammort = round($gross/$term,2);
	}
	
	// -- solving for other loan ammorts
	$arid = $aLoan['arid'];
	
	$q = "select releasing_id from releasing where account_id ='".$aLoan['account_id']."'";
	$qxr = @pg_query($q);
	if (!$qxr)
	{
		galert(pg_errormessage().$q);
	}
	else
	{
		while ($rx = @pg_fetch_object($qxr))
		{
			recalculate($rx->releasing_id, 'noneform');
		}
	}

	$q = "select sum(ammort) as previous_ammort 
				from 
					releasing 
				where 
					status!='C' and 
					account_id = '".$aLoan['account_id']."' and
					balance>0 and date < '".$aLoan['date']."'";
	if ($aLoan['arid'] != '')
	{
		$q .= " and	not (releasing_id in ($arid)) ";
	}
	
	if ($aLoan['releasing_id'] != '')
	{
		$q .= " and	releasing_id != '".$aLoan['releasing_id']."'";
	}
	$qr =  @pg_query($q);
	if (!$qr)
	{
		galert(pg_errormessage().$q);
		return done();
	}
	else
	{
		$r = @pg_fetch_object($qr);
		$previous_ammort = $r->previous_ammort;
	}	
	
	
	$total_ammort = $previous_ammort + $ammort + $SYSCONF['MIN_EXCESS'];

	if ($total_ammort > $salary)
	{
		galert("Total Loan Ammortization (P ".number_format($total_ammort,2).") is More than Monthly Salary/Pension of (P ".number_format($salary,2).") ".
		"\n   Loan NOT allowed, or adjust Principal Loan Amount\n
		Existing Previous Ammortization: P ".number_format($previous_ammort,2)."\n
		Current Ammortization : P ".number_format($ammort,2)."\n
		Min Change Required : P ".number_format($SYSCONF['MIN_EXCESS'],2)."\n
		Total Ammortization : P ".number_format($total_ammort,2)."\n");
		gset('principal','');
		$script = "document.getElementById('principal').focus()";
		gscript($script);
		return done();
	}
	//--- fees
	$collection_fee = $service_charge = 0;	
	$principal = str_replace(',','',$principal);

        $q = "select * from feetable
			where type='C' and 
					enable='Y' and 
					afrom<='$principal' and
					ato>= '$principal'";
	$qr = @pg_query($q);
	if (!$qr)
	{
                galert("Error Querying Collection Fee ".pg_errormessage().$q);
		return done();
	} 

	if (@pg_num_rows($qr) > 0)
	{ 
		$r = @pg_fetch_object($qr);
		$collection_fee = $r->fee;
	}
	
	$q = "select * from feetable 
			where type='S' and 
					enable='Y' and 
					afrom<='$principal' and
					ato>= '$principal'";
	$qr = @pg_query($q);
	if (!$qr)
	{
		galert("Error Querying Collection Fee ".pg_errormessage());
		return done();
	}
	if (@pg_num_rows($qr) > 0)
	{ 
		$r = @pg_fetch_object($qr);
		$service_charge = $r->fee;	
	}

	if ($account_class_id == 1)
	{
		if ($SYSCONF['ATM_CHARGE_RATE']>0)
		{
			$atm_charge = $SYSCONF['ATM_CHARGE_RATE']*$term;
		}
		if ($SYSCONF['PRINTOUTFEE']>0)
		{
			$printout = $SYSCONF['PRINTOUTFEE'];
		}
	}
	$insurance = $principal*$SYSCONF['INSURANCEFEE']/100;
	$insureamt = $form[insureamt];
//	galert('prin '.$principal.' ins '.$SYSCONF['INSURANCEFEE'].' ins '.$insurance);

	$charges = $service_charge+$previous_balance+$ca_balance+
				$advance_payment+ $vat + $referral_fee + 
				$collection_fee + $photo + $printout + $atm_charge + 
				$advance_change + $other_charges + $redeem + insureamt;

	if ($loan_interest != 'A')
	{
		$charges += $interest;
	}

	gset('printout',number_format($printout,2));
	gset('atm_charge',number_format($atm_charge,2));
	gset('insurance',number_format($insurance,2));
	gset('collection_fee',number_format($collection_fee,2));
	gset('service_charge',number_format($service_charge,2));
	gset('principal',number_format($principal,2));
	gset('ammort',number_format($ammort,2));

	gset('interest',number_format($interest,2));
	gset('charges',number_format($charges,2));
	gset('gross',number_format($gross,2));
	gset('released',number_format(($principal - $charges),2));

	return done();
	
	
}


$xajax->registerFunction('age');
function age($form) 
{
	$date_birth = mdy2ymd($form['date_birth']);
	
	$adb = explode('-',$date_birth);
	if (count($adb) < 3) return done();
	$mo=date('m');
	$yr = date('Y');
	$day = date('d');
	$age = 0;
	if ($mo > $adb[1]*1)
	{
		$age = $yr - $adb[0]*1; 
	}
	elseif ($mo == $adb[1]*1)
	{

		if ($day >= $adb[2]*1)
		{
			$age = $yr - $adb[0]*1; 
		}
		else
		{
			$age = $yr - $adb[0]-1; 
		}
	}
	elseif ($mo < $adb[1]*1)
	{
		$age = $yr - $adb[0]-1; 
	}
	else
	{
		$age = 0;
	}
	gset ('age',$age);
	return done();
}

function age2($date_birth)
{
	$adb = explode('-',$date_birth);
	if (count($adb) < 3) return;
	$mo=date('m');
	$yr = date('Y');
	$day = date('d');
	$age = 0;
	if ($mo > $adb[1]*1)
	{
		$age = $yr - $adb[0]*1; 
	}
	elseif ($mo == $adb[1]*1)
	{

		if ($day >= $adb[2]*1)
		{
			$age = $yr - $adb[0]*1; 
		}
		else
		{
			$age = $yr - $adb[0]-1; 
		}
	}
	elseif ($mo < $adb[1]*1)
	{
		$age = $yr - $adb[0]-1; 
	}
	else
	{
		$age = 0;
	}
	return $age;
}

$xajax->registerFunction('computeMaxTerm');
function computeMaxTerm($form) 
{
	global $aLoan;
	$account_id = $aLoan['account_id'];

	if ($account_id*1 == '0') 
	{
		galert(" No account specified...");
		return done();
	}
	
	$Q = "select account_class_id
			 from 
			 	account,
			 	account_group
			 where 
			 	account_group.account_group_id = account.account_group_id and
			 	account_id = '$account_id'";
	$QR = @pg_query($Q);

	
	if (!$QR)
	{
		galert("Error Querying Account at Max Term...".pg_errormessage().$q);
		return done();
	}
	$R = @pg_fetch_object($QR);

	if (intval($R->account_class_id) == '0')
	{
		galert(" Warning! No Account Classification for this Account...");
		return done();
	}
	elseif($R->account_class_id != 1)
	{
		//--- if not SSS return. No Max term
		return done();
	}

	//-- check max term only if SSS
	
	$date_birth = mdy2ymd($form['date_birth']);
	$mclass = $form['mclass'];
	$date_child21 = mdy2ymd($form['date_child21']);
	$npension = $form['npension'];
	$nchangebank = $form['nchangebank'];	
	$age = age2($date_birth);
	$child21age = age2($date_child21);

	$max_term=0;
	if ($age<70)
	{
		$max_term = 36;
	}
	elseif ($age<74)
	{
		$max_term = 24;
	}
	elseif ($age<78)
	{
		$max_term = 12;
	}
	else
	{
		$max_term = 6;
	}
	
	if ($mclass == 2)
	{
		if ($date_child21=='')
		{
			$max_term = 36;
		}
		else
		{
			$max_term = (21-$child21age)*12;
		}
		if ($max_term >36) $max_term = 36;
		if ($max_term <= 0) $max_term = 36;
	
	}
	elseif ($mclass == 3)
	{
		$max_term = 6;
	}
	elseif ($mclass == 4)
	{
		//-- temporary disability
		if ($npension != '' && $npension<6)
		{
			$max_term = $npension;
		}	
		else
		{
			$max_term = 6;
		}
	}
	elseif ($mclass == 5)
	{
		$max_term=6;
	}
	//-- record of change bank
	if ($nchangebank != '') 
	{
		if ($nchangebank>2)
		{
			$max_term = ' 0';
		}
	}	
	
	gset('max_term',$max_term);
	gset('age',$age);
	return done();
}

$xajax->registerFunction('recalculate');
function recalculate($rid, $f) 
{
	$Q= "select sum(debit) as debit, sum(credit) as credit
				from
					ledger
				where
					releasing_id ='$rid' and
					status!='C'";

	$QR = @pg_query($Q); // or message(pg_errormessage().$q);

	$R = @pg_fetch_object($QR);
	$balance = $R->debit - $R->credit;
	
	$Q = "update releasing set balance = '$balance' where releasing_id = '$rid'";
	$QR = @pg_query($Q); // or message(pg_errormessage().$q);


	if ($f == 'form')
	{
		glayer('wait.layer','');
		galert("Finished Recalculation...");
	}
	return done();
}

function recalculatexx($rid, $f) 
{
	
	$q = "select 
				ledger.date,
				ledger.type,
				ledger.debit,
				ledger.credit,
				ledger.account_id,
				ledger.releasing_id,
				ledger.reference,
				ledger.remarks,
				ledger.ledger_id,
				releasing.principal,
				releasing.ammort,
				releasing.gross

			 from 
			 	ledger, 
				releasing
			 where 
			 	ledger.releasing_id=releasing.releasing_id and
				ledger.status!='C'";

	if ($rid != '')
	{
		$q .= " and releasing.releasing_id = '$rid'";
	}
	$q .= "	order by ledger.releasing_id, ledger.date";
	$qr = @pg_query($q);
	
	if (!$qr)
	{
		galert("Error querying ledger...".pg_errormessage().$q);
	}

	$ctr=0;
	$total_credit = $total_debit = 0;
	$sub_credit = $sub_debit = 0;
	$total_obligation = $total_ammort = 0;
	$mreleasing_id='';
	$multiple = $cc = 0;
		
	while ($temp = @pg_fetch_assoc($qr))
	{
			$withdrawn = $excess = 0;
			$reference = '';
			if ($mreleasing_id != $temp['releasing_id'] && $mreleasing_id != '')
			{
				//-- UPDATE RELEASING TABLE BALANCE FIELD
				$sub_balance = round($sub_balance,2);
				$qur = "update releasing set balance = '$sub_balance' where releasing_id = '$mreleasing_id'";
				@pg_query($qur);
				$multiple =1;
				$sub_credit = $sub_debit = $sub_balance = $sub_withdrawn = $sub_excess = 0;
			}
			$mreleasing_id = $temp['releasing_id'];
			$reference = '';
			if ($temp['type'] == 'D')
			{
				$total_ammort += $temp['ammort'];
				$total_obligation += $temp['gross'];
				$cc = 0;
			}
			elseif ($temp['remarks'] == 'RENEW')
			{
			}
			elseif ($temp['type'] == 'C')
			{
				$q = "select 	payment_header.admin_id,
									payment_detail.withdrawn,
									payment_detail.excess,
									payment_detail.amount,
									payment_detail.payment_detail_id,
									payment_detail.payment_header_id
								from 
									payment_header, 
									payment_detail
								where 
									payment_detail.payment_header_id = payment_header.payment_header_id and 
									payment_header.payment_header_id='".$temp['reference']."' and
									payment_detail.releasing_id = '".$temp['releasing_id']."' and
									payment_header.status!='C'";
				$qrp = @pg_query($q) ;
				if (!$qrp)
				{
					galert("Error querying ledger...".pg_errormessage().$q);
				}
				
	
				if (@pg_num_rows($qrp) > 0)
				{
					$rp = @pg_fetch_object($qrp);
					$withdrawn = $rp->withdrawn;
					$excess = $rp->excess;
					$temp['credit'] = $rp->amount;
				}
			}
			$accountbalance += $temp['debit'] - $temp['credit'];
			$total_credit += $temp['credit'];
			$total_debit += $temp['debit'];

			$sub_credit += $temp['credit'];
			$sub_debit += $temp['debit'];
			$sub_withdrawn += $withdrawn;
			$sub_excess += $excess;
			if ($withdrawn < $temp['credit'])
			{
				$sub_balance += $temp['debit'] - $temp['credit'] - $excess;
			}
			else
			{
				$sub_balance += $temp['debit'] - $temp['credit'] ;
			}
			if ($temp['type'] == 'D' && $r->advance_applied != '')
			{
				$cc++;
	
				$total_withdrawn += $withdrawn;
				$total_excess += $excess;
			}
			else
			{
				if ($temp['credit'] > 0)
				{
					$cc++;
				}
				
				$total_withdrawn += $withdrawn;
				$total_excess += $excess;
				
			}				
	}
	//-- UPDATE RELEASING TABLE BALANCE FIELD

	$sub_balance = round($sub_balance,2);
	$qur = "update releasing set balance = '$sub_balance' where releasing_id = '$mreleasing_id'";
	@pg_query($qur);
	
	
	if ($f == 'form')
	{
		glayer('wait.layer','');
		galert("Finished Recalculation...");
	}
	return done();
}
$xajax->registerFunction('manuinsure');
function manuinsure($form)
{
	$fields = array('max_term','principal','interest','rate_basis',
					'rate','term','loan_type_id','referral_fee','photo',
					'printout','atm_charge','other_charges','service_charge',
					'collection_fee','gross','ammort','mode','vat','advance_payment',
					'insurance','previous_balance','advance_change','age',
					'ca_balance','redeem','salary','insureamt','gross');
					
	for ($c=0;$c<count($fields);$c++)
	{
		$$fields[$c] = str_replace(',','',$form[$fields[$c]]);
	}

	if ($form[insure]==1)
	{
		if ($age==64 and $term>12) $term=12;
		if ($age==63 and $term>24) $term=24;
		$insureamt = round(($principal/1000) * $term * .56,2) + round(($principal/1000)*$term*.1,2);
	} 
	else $insureamt = 0; 	
	
	$charges =  $previous_balance + $ca_balance + $advance_payment + $vat + $referral_fee + $service_charge +
				$collection_fee + $photo + $printout + $atm_charge + $advance_change + $other_charges +
				$redeem + $insureamt;	

	gset('charges',number_format($charges,2));
	gset('released',number_format(($principal - $charges),2));
	gset('insureamt',number_format($insureamt,2));
	return done();
}

$xajax->registerFunction('loansave');
function loansave($form)
{
	global $SYSCONF, $aLoan, $aComaker, $ADMIN, $transdate;

	$fields = array('mode','rate','term','loan_type_id','date','principal',
		'advance_payment','advance_applied','ca_balance','previous_balance','redeem',
		'service_charge','collection_fee','insurance','interest', 
		'printout', 'photo', 'atm_charge', 'referral_fee',
		'advance_change', 'other_charges', 'other_remarks',
		'vat','gross','released','ammort','mclass','date_child21',
		'comaker1','comaker2', 'comaker1_address','comaker2_address',
		'comaker1_relation','comaker2_relation',
		'npension','nchangebank','max_term','age','deposit','withdraw_day',
		'comake1_id','comake2_id','tpenalty','account_group_id','insure');

	$fieldc = array('comake1','comake2','comake3','comake4','comake5',
		'comake1_address','comake2_address','comake3_address','comake4_address','comake5_address',
		'comake1_relation','comake2_relation','comake3_relation','comake4_relation','comake5_relation',
		'comake1_id','comake2_id','comake3_id','comake4_id','comake5_id');

	for ($c=0;$c<count($fields);$c++)
	{
		// $aLoan[$fields[$c]] = $form[$fields[$c]];
		if (substr($fields[$c],0,4) == 'date' or $fields[$c] == 'advance_applied')
		{
			if ($form[$fields[$c]] == ''or $form[$fields[$c]]=='--')
			{
				$aLoan[$fields[$c]] = '';
			}
			else
			{
				$aLoan[$fields[$c]] = mdy2ymd($form[$fields[$c]]);
			}
		}
		else
		{
			$aLoan[$fields[$c]] = $form[$fields[$c]];
			if (!in_array($fields[$c],array('other_remarks','comaker1','comaker2','comaker1_address','comaker2_address')))
			{
				if ($aLoan[$fields[$c]] == '')
				{
					$aLoan[$fields[$c]] = 0;
				}
				
				$aLoan[$fields[$c]] = str_replace(',','',$aLoan[$fields[$c]]);
			}
		}	

	}
	
	for ($c=0;$c<count($fieldc);$c++)
	{
		$aComaker[$fieldc[$c]] = $form[$fieldc[$c]];	
		if (in_array($fieldc[$c],array('comake1_id','comake2_id','comake3_id','comake4_id','comake5_id')))
		{
			if ($aComaker[$fieldc[$c]] == '')
			{
				$aComaker[$fieldc[$c]] = 0;
			}
		}	
	}

	$transdate = $form['transdate'];
	$insureamt = str_replace(',','',$form['insureamt']);
	$aLoan['admin_id_override'] = intval($aLoan['admin_id_override']*1);
	
	if (($transdate!=date('Y-m-d') or $aLoan['date']!=date('Y-m-d')) && $ADMIN['usergroup'] != 'A')
	{
		galert("  You have no permission to Update/Modify Loan Releasing...");
		return done();
	}
	
	if ($aLoan['released'] < '0')
	{
		galert("Cannot Save. Release Amount is Negative!");
		return done();
	}
	
	if ($SYSCONF['MAX_TERM'] > '0' and $aLoan['term']>$SYSCONF['MAX_TERM'])
	{
		galert("Transaction CANNOT be Saved. Term Specified Exceeds System Maximum Term (".$SYSCONF['MAX_TERM'].")!\n Please Check");
		return done();		
	}
	if ($aLoan['renew_releasing_id'] == '')
	{
		$aLoan['renew_releasing_id']=0;
	}
	
	if (!chkRights2('releasing','medit',$ADMIN['admin_id']) && $aLoan['releasing_id']!='')
	{
		galert("You have no permission to edit/update Loan Releasing...");
		return done();
	}
	elseif ((strlen($aLoan['comaker1'])<1 || strlen($aLoan['comaker2'])<1)) //&& !chkRights3('override','madd',$ADMIN['admin_id'])
	{
		galert("Lacking Co-Maker. Please complete information...");
		return done();
	}
	elseif ((strlen($aLoan['comaker1_address'])<1 || strlen($aLoan['comaker2_address'])<1)) //&& !chkRights3('override','madd',$ADMIN['admin_id'])
	{
		galert("Lacking Co-Maker Address. Please complete information...");
		return done();
	}
	elseif ((strlen($aLoan['comaker1_relation'])<1 || strlen($aLoan['comaker2_relation'])<1)) //&& !chkRights3('override','madd',$ADMIN['admin_id'])
	{
		galert("Lacking Co-Maker Relationship. Please complete information...");
		return done();
	}
	
	elseif (($aLoan['ammort']>$aLoan['salary'] )) 
	{
		galert("Ammortization Amount (P ".number_format($aLoan['ammort'],2).") is greater than Salary of (P ".number_format($aLoan['salary'],2).")");
		return done();
	}
	elseif (($aLoan['principal']==0 ||
	 		 $aLoan['ammort']==0 ||  $aLoan['loan_type_id']==0))
	{
		galert("Cannot Save. Lacking Important Data, Please Check...(Loan Type, Rate, Principal, Ammortization..)");
		return done();
	}
//galert("transdate ".$transdate."  Loan date : ".$aLoan['date']);
//return done();

	$account_group_id = lookUpTableReturnValue('x','account','account_id','account_group_id',$aLoan['account_id']);
	$aLoan[$account_group_id] = $account_group_id;
	
	if ($aLoan['releasing_id']=='')
	{
		if ($aLoan['edate'] == '') $aLoan['edate'] = date('Y-m-d');
		begin();
		$aLoan['audit'] ='Created by:'.$ADMIN['name'].' on '.date('m/d/Y g:ia');
		if ($aLoan['advance_applied']=='')
		{
			$q = "insert into releasing (account_id, loan_type_id, mode, term, max_term, rate, edate, date,
					principal,  advance_payment , ca_balance, previous_balance, interest, service_charge, collection_fee,
					printout, photo, atm_charge, advance_change, other_charges, other_remarks, referral_fee,
					insurance, vat, gross, released, ammort, admin_id, status, renew_releasing_id, 
					mclass, date_child21,npension, nchangebank, age, comaker1, comaker2,  
					comaker1_address, comaker2_address,comaker1_relation, comaker2_relation, redeem,
					admin_id_override, deposit, withdraw_day, tpenalty, insure, audit, account_group_id, enable)
				values
					('".$aLoan['account_id']."','".$aLoan['loan_type_id']."','".$aLoan['mode']."',
					'".$aLoan['term']."','".$aLoan['max_term']."','".$aLoan['rate']."','".$aLoan['edate']."','".$aLoan['date']."',
					'".$aLoan['principal']."','".$aLoan['advance_payment']."',
					'".$aLoan['ca_balance']."','".$aLoan['previous_balance']."','".$aLoan['interest']."',
					'".$aLoan['service_charge']."','".$aLoan['collection_fee']."',
					'".$aLoan['printout']."','".$aLoan['photo']."','".$aLoan['atm_charge']."','".$aLoan['advance_change']."',
					'".$aLoan['other_charges']."','".$aLoan['other_remarks']."','".$aLoan['referral_fee']."',
					'".$aLoan['insurance']."','".$aLoan['vat']."','".$aLoan['gross']."',
					'".$aLoan['released']."','".$aLoan['ammort']."',
					'".$ADMIN['admin_id']."','S', '".$aLoan['renew_releasing_id']."', 
					'".$aLoan['mclass']."','".$aLoan['date_child21']."', '".$aLoan['npension']."',
					'".$aLoan['nchangebank']."', '".$aLoan['age']."', 
					'".$aLoan['comaker1']."', '".$aLoan['comaker2']."', 
					'".$aLoan['comaker1_address']."', '".$aLoan['comaker2_address']."', 
					'".$aLoan['comaker1_relation']."', '".$aLoan['comaker2_relation']."', 
					'".$aLoan['redeem']."','".$aLoan['admin_id_override']."','".$aLoan['deposit']."',
					'".$aLoan['withdraw_day']."','".$aLoan['tpenalty']."','".$aLoan['insure']."','".$aLoan['audit']."','$account_group_id','t')";
		}
		else
		{
			$q = "insert into releasing (account_id, loan_type_id, mode, term, max_term, rate, edate, date,
					principal,  advance_payment , advance_applied, ca_balance, previous_balance, interest, service_charge,
					collection_fee,	printout, photo, atm_charge, advance_change, other_charges, other_remarks,referral_fee,
					insurance, vat, gross, released, ammort, admin_id, status, renew_releasing_id, 
					mclass, date_child21,npension, nchangebank, age, comaker1, comaker2, 
					comaker1_address, comaker2_address,  comaker1_relation, comaker2_relation,
					redeem,admin_id_override, withdraw_day, tpenalty, insure, audit, account_group_id, enable)
				values
					('".$aLoan['account_id']."','".$aLoan['loan_type_id']."','".$aLoan['mode']."',
					'".$aLoan['term']."','".$aLoan['max_term']."','".$aLoan['rate']."','".$aLoan['edate']."','".$aLoan['date']."',
					'".$aLoan['principal']."','".$aLoan['advance_payment']."','".$aLoan['advance_applied']."',
					'".$aLoan['ca_balance']."','".$aLoan['previous_balance']."','".$aLoan['interest']."',
					'".$aLoan['service_charge']."','".$aLoan['collection_fee']."',
					'".$aLoan['printout']."','".$aLoan['photo']."','".$aLoan['atm_charge']."','".$aLoan['advance_change']."',
					'".$aLoan['other_charges']."','".$aLoan['other_remarks']."','".$aLoan['referral_fee']."',
					'".$aLoan['insurance']."','".$aLoan['vat']."','".$aLoan['gross']."',
					'".$aLoan['released']."','".$aLoan['ammort']."',
					'".$ADMIN['admin_id']."','S', '".$aLoan['renew_releasing_id']."', 
					'".$aLoan['mclass']."','".$aLoan['date_child21']."', '".$aLoan['npension']."',
					'".$aLoan['nchangebank']."', '".$aLoan['age']."', 
					'".$aLoan['comaker1']."', '".$aLoan['comaker2']."', 
					'".$aLoan['comaker1_address']."', '".$aLoan['comaker2_address']."', 
					'".$aLoan['comaker1_relation']."', '".$aLoan['comaker2_relation']."', 
					'".$aLoan['redeem']."','".$aLoan['admin_id_override']."',
					'".$aLoan['withdraw_day']."','".$aLoan['tpenalty']."','".$aLoan['insure']."','".$aLoan['audit']."','$account_group_id','t')";
		}			
		$qr = @pg_query($q);
		if (!$qr)
		{
		 	galert(pg_errormessage().$q);
		 	return done();
		}
		$credit = 0;
		$credit = $aLoan['advance_payment'];
		if ($qr && @pg_affected_rows($qr)>0)
		{
			$q = "select currval('releasing_releasing_id_seq')" ;
			$r = fetch_object($q);
			$aLoan['releasing_id'] = $r->currval;


			$q = "update account set date_birth = '".$aLoan['date_birth']."', age='".$aLoan['age']."', 
							mclass='".$aLoan['mclass']."', date_child21='".$aLoan['date_child21']."',
							npension='".$aLoan['npension']."',  nchangebank ='".$aLoan['nchangebank']."'
						where
							account_id ='".$aLoan['account_id']."'";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert('Error Updating Account Info....'.pg_errormessage().$q);
				return done();
			}
			
			$q ="insert into ledger (account_id, releasing_id, reference, date,type,debit,credit,remarks)
					values ('".$aLoan['account_id']."','".$aLoan['releasing_id']."','".$aLoan['releasing_id']."',
							'".$aLoan['date']."','D','".$aLoan['gross']."','$credit',
							'".$aLoan['remarks']."')";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert('Error Updating Account Ledger....'.pg_errormessage().$q);
				return done();
			}
			
			if ($qr && pg_affected_rows($qr)>0)
			{
				recalculate($aLoan['releasing_id'],'noneform');
				commit();
				$maction = 'saved';
				$message = 'Loan Application Saved';
				$q = "select currval('ledger_ledger_id_seq')" ;
				$r = fetch_object($q);
				$aLoan['ledger_id'] = $r->currval;
				$aLoan['status']='S';
			}
			else
			{
				rollback();
				$aLoan['releasing_id'] = '';
				$maction = 'errorsave';
				$message = 'Cannot Save, Error Occured in Ledger Table '.pg_errormessage();
				galert($message);
				return done();
			}
		}	
		else
		{
			rollback();
			$maction = 'errorsave';
			$message = 'Cannot Save, Error Occured in Releasing Table '.pg_errormessage();
			galert($message);
			return done();
		}
	}
	else
	{
		begin();
		if ($ADMIN[admin_id]!=1)
			$aLoan['audit'] .=';Updated by:'.$ADMIN['name'].' on '.date('m/d/Y g:ia');
		$q = "update releasing set audit = '".$aLoan['audit']."'";
		for ($c=0;$c<count($fields);$c++)
		{
			if ($fields[$c] == 'advance_applied' && $aLoan[$fields[$c]]=='')
			{
				continue;
			}
			else
			{
				$q .= ",".$fields[$c]."='".$aLoan[$fields[$c]]."'";
			}	
		}
		$q .= " where releasing_id='".$aLoan['releasing_id']."'";

		$qr = @pg_query($q);
//if ($ADMIN[admin_id]==1) galert($q);

		if (!$qr)
		{
			galert(pg_errormessage().$q);
			return done();
		}

		if ($qr && pg_affected_rows($qr))
		{
			$credit = $aLoan['advance_payment'];	
		
			$q = "update ledger set
						date='".$aLoan['date']."',
						account_id='".$aLoan['account_id']."',
						reference='".$aLoan['releasing_id']."',
						debit='".$aLoan['gross']."',
						credit='$credit',
						status='S'";
			$q .= " where reference='".$aLoan['releasing_id']."' and
						type='D'";
						
			$qr = @pg_query($q);
			if (!$qr)
			{
				alert(pg_errormessage().$q);
				return done();
			}

			if ($qr && pg_affected_rows($qr)>0)
			{
				commit();
				$aLoan['status']='S';
			}
			elseif (@pg_affected_rows($qr) == 0)
			{
				$q ="insert into ledger (account_id, reference, date,type,debit,credit,remarks)
						values ('".$aLoan['account_id']."','".$aLoan['releasing_id']."',
								'".$aLoan['date']."','D','".$aLoan['gross']."','0',
								'".$aLoan['remarks']."')";
				$qr = @pg_query($q);
			}
			if (!$qr)
			{
				galert(pg_errormessage().$q);
				return done();
			}
			
			if ($qr && pg_affected_rows($qr)>0)
			{
				commit();
				recalculate($aLoan['releasing_id'],'noneform');

				$maction = 'updated';
				$message = "Loan Application updated...";
				$aLoan['status']='S';
 			}
			elseif (!$qr)
			{
				rollback();
				$maction='errorsave';
				$message = "NOT able to update Ledger. Error occured <br>".$q;
				galert($message);
				return done();

			}	
		}
		else
		{
			rollback();
			$maction='errorsave';
			$message = "NOT able to update transaction. Error occured <br>".$q;

			galert($message);
			return done();

		}
		
	}
	if ($aLoan['releasing_id'] != '' && $aLoan['arid'] != '')
	{
			$arestructure = explode (',',$aLoan['arid']);
			$previous_balance = $aLoan['previous_balance'];	

			for ($c=0;$c<count($arestructure);$c++)
			{
				//update renewal
				
				$rid = str_replace("'","",$arestructure[$c]);
		
				if ($rid == '') continue;
				if ($previous_balance <= 0) break;
				
				$q = "select sum(debit) as debit,
								sum(credit) as credit 
							from 
									ledger
							where 
									status!='C' and
									type!='R' and 
									reference != '".$aLoan['releasing_id']."' and 
									releasing_id = '$rid'";
				$qr = @pg_query($q);

				if (!$qr)
				{
					galert(pg_errormessage().$q);
					return done();
				}
				
				$r = @pg_fetch_object($qr);

		//	galert(($r->debit - $r->credit). ' '.$rid);
				if ($previous_balance>= ($r->debit - $r->credit))
				{
					$previous_balance -= ($r->debit - $r->credit);
					$credit_applied = ($r->debit - $r->credit);
					$balance=0;
				}	
				else
				{
					$balance = ($r->debit - $r->credit) - $previous_balance;
					$credit_applied = $previous_balance;
					$previous_balance = 0;
				}
				$q = "select * 
							from 
								ledger 
							where 
								releasing_id='$rid' and
								reference='".$aLoan['releasing_id']."' and 
								type in ('R')
							order by type desc
							offset 0 limit 1";
				$qr = @pg_query($q);
				
		
				if (!$qr)
				{
					galert(pg_errormessage().$q);
					return done();
				}

				if (@pg_num_rows($qr) > 0)
				{
					$rr = @pg_fetch_object($qr);
					$q = "update ledger set credit='$credit_applied'
							 where 
							 		ledger_id ='$rr->ledger_id'";
									
					$qr = @pg_query($q);

					if (!$qr)
					{
						galert(pg_errormessage().$q);
						return done();
					}
									

					recalculate($rid,'noneform');
				}
				else
				{
					$q ="insert into ledger (account_id, releasing_id, reference, date,type,debit,credit)
							values ('".$aLoan['account_id']."','$rid',
									'".$aLoan['releasing_id']."',
									'".$aLoan['date']."','R','0','$credit_applied')";
					$qr = @pg_query($q);
					if (!$qr)
					{
						galert(pg_errormessage().$q);
						return done();
					}
					
					recalculate($rid,'noneform');
				}	
			//	galert($q);return done();
			}

	}
	
	
	if ($aLoan['releasing_id']!='' && $aLoan['advance_change'] > 0)
	{
		$q = "select * 
					from 
						wexcess 
					where 
						account_id = '".$aLoan['account_id']."' and
						type='D' and  
						remarks = 'LOANCREDIT' and
						ps_remark = '".$aLoan['releasing_id']."'";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
		}
		else
		{

			if (@pg_num_rows($qr) == 0)
			{
				$q = "insert into wexcess (type,date,account_id, gross_amount, remarks, ps_remark, admin_id, audit)
						values ('D','".$aLoan['date']."','".$aLoan['account_id']."', '".$aLoan['advance_change']."',
						'LOANCREDIT','".$aLoan['releasing_id']."','".$ADMIN['admin_id']."',
						'Credit from Loan Advance Change Payment')";

				$qr = @pg_query($q);
				if (!$qr)
				{
					galert(pg_errormessage().$q);
				}
						 
			}
			else
			{
				$r = @pg_fetch_object($qr);
				$q = "update wexcess set date='".$aLoan['date']."', gross_amount='".$aLoan['advance_change']."'
								where
										wexcess_id = '$r->wexcess_id'";
				$qr = @pg_query($q);

				if (!$qr)
				{
					galert(pg_errormessage().$q);
				}
										
			}
		
		}
						
	}
	$q = "select * from loandeposit where releasing_id='".$aLoan['releasing_id']."' and 
					account_id='".$aLoan['account_id']."' and status='L'";
	$qr = @pg_query($q);
	
	if (@pg_num_rows($qr) == 0)
	{
		if ($aLoan[deposit] > 0)
		{
			$q = "insert into loandeposit (releasing_id,date,account_id, credit, admin_id, status,audit)
					values ('".$aLoan['releasing_id']."','".$aLoan['date']."','".$aLoan['account_id']."', 
					'".$aLoan['deposit']."','".$ADMIN['admin_id']."','L','".$aLoan['audit']."')";
			$qr = @pg_query($q);
			if (!$qr)
			{
				galert(pg_errormessage().$q);
			}
		}
	} else
	{
		$r1 = @pg_fetch_object($qr);
		$loandeposit_id = $r1->loandeposit_id;

		$q = "update loandeposit set date='".$aLoan['date']."', credit='".$aLoan['deposit']."'
						where 	loandeposit_id = '$loandeposit_id'";
		$qr = @pg_query($q);

		if (!$qr)
		{
			galert(pg_errormessage().$q);
		}
	}
	if ($aLoan[insure])
	{
		$apmonth = substr($aLoan['date'],0,7);
		$tb = $aLoan['term']-12;
		$termbal = ($tb >= 0?$tb:'0');
		$apterm  = ($tb >= 0?'12':$tb);
		$q = "select * from insurance where releasing_id='".$aLoan[releasing_id]."'";
		$qr= @pg_query($q);
		$r = @pg_fetch_object($qr);
		if ($r->releasing_id==$aLoan[releasing_id])
		{
			$q = "update insurance set status='A', credit='$insureamt', account_id='".$aLoan['account_id']."', date = '".$aLoan['date']."', 
									apmonth = '$apmonth', apterm = '$apterm', termbal = '$termbal'
							where 	releasing_id = '".$aLoan[releasing_id]."'";
			$qr = @pg_query($q);
	
			if (!$qr)
			{
				galert(pg_errormessage().$q);
			}
		}
		else
		{
			$q = "insert into insurance (releasing_id, status, credit, account_id, date, apmonth, apterm, termbal, admin_id) 
			         values ('".$aLoan['releasing_id']."', 'A', '$insureamt', '".$aLoan['account_id']."','".$aLoan['date']."',
							 '$apmonth', '$apterm', '$termbal', '".$ADMIN['admin_id']."')";
			$qr = @pg_query($q);
	
			if (!$qr)
			{
				galert(pg_errormessage().$q);
			}
		}
	} else
	{
		$q = "select count(*) as rnum from insurance where releasing_id='".$aLoan[releasing_id]."'";
		$qr= @pg_query($q);
		$r = @pg_fetch_object($qr);
		if ($r->rnum==1)
		{
			$q = "update insurance set status='C'f where	releasing_id = '".$aLoan[releasing_id]."'";
			$qr = @pg_query($q);
		} else gset ('insure','1');
	}

	$q = "select * from comaker where releasing_id='".$aLoan[releasing_id]."'";
	$qr= @pg_query($q);
	$rc = @pg_fetch_object($qr);
	
//galert($rc->comaker_id);
	
	if ($rc->comaker_id != 0)
	{
		$q = "update comaker set releasing_id='".$aLoan[releasing_id]."'";
		for ($c=0;$c<count($fieldc);$c++)
		{
			$q .= ",".$fieldc[$c]."='".$aComaker[$fieldc[$c]]."'";
		}
		$q .= " where releasing_id='".$aLoan['releasing_id']."'";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
		}
	}
	else
	{	
		$q = "insert into comaker (comake1, comake2, comake3, comake4, comake5,
				comake1_address, comake2_address, comake3_address, comake4_address, comake5_address,
				comake1_relation, comake2_relation, comake3_relation, comake4_relation, comake5_relation, 
				comake1_id, comake2_id, comake3_id, comake4_id,comake5_id, releasing_id)
			values
				('".$aComaker['comake1']."', '".$aComaker['comake2']."','".$aComaker['comake3']."','".$aComaker['comake4']."','".$aComaker['comake5']."', 
				 '".$aComaker['comake1_address']."','".$aComaker['comake2_address']."','".$aComaker['comake3_address']."',
				 '".$aComaker['comake4_address']."','".$aComaker['comake5_address']."',
				 '".$aComaker['comake1_relation']."','".$aComaker['comake2_relation']."','".$aComaker['comake3_relation']."',
				 '".$aComaker['comake4_relation']."','".$aComaker['comake5_relation']."',
				 '".$aComaker['comake1_id']."','".$aComaker['comake2_id']."','".$aComaker['comake3_id']."','".$aComaker['comake4_id']."','".$aComaker['comake5_id']."',
				 '".$aLoan['releasing_id']."')";
		$qr = @pg_query($q);
		if (!$qr)
		{
			galert(pg_errormessage().$q);
		}
	}
	gset('reference_id',str_pad($aLoan['releasing_id'],8,'0',STR_PAD_LEFT));
	galert("Loan Release Transaction Saved....\n Click Print Button for Voucher");
	
	return done();
}
?>
