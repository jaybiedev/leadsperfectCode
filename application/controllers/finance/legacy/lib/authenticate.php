<?
	if ($KURUKOKUK != lango(3))
	{
		echo "Security Check. Please Login Again";
		$p=login;
		exit;
	}	

	if ($p == "authenticate")
	{
		$p = "login";
		if ($mpassword == null or $username == null)
		{
			message("Pls. provide a valid username and password...");
		}
		else
		{
		
			$mpassword = md5($mpassword);
			$q = "select * from admin where username='$username' and mpassword='$mpassword'";	
			$qr = @pg_query($q) or message(pg_errormessage()); 
			if (!$qr)
			{
				message("An error occurred...Pls. try again...");
			}
			elseif (@pg_numrows($qr) == 0)
			{
				message("Invalid username or mpassword...");
			}
			else
			{
				if (!session_is_registered("ADMIN"))
				{
					session_register("ADMIN");
					$ADMIN=array();
				}

				$AR = @pg_fetch_assoc($qr);
				
				$start = strtotime($AR[date_update]);
				$end = strtotime(date("Y-m-d"));
				$xt = ceil(abs($end - $start) / 86400);

				if ($AR['enable']=="N") 
				{
					message("User priveledge is disabled. Access to the system denied.");
					exit;
				}
				elseif ($AR['date_expire'] < date('Y-m-d') && !in_array($AR['date_expire'],array('','--','//'))) 
				{
					message("User account expired. Access to the system denied.");
					exit;
				}
				elseif ($AR['date_update'] != '' and $xt > 30 and $AR[admin_id]!=1 )
				{
					message("You have not changed you password for more than 30 days");
//					sleep(30);
				}

				$ADMIN = null;
				$ADMIN = array();
				$ADMIN = $AR;
				

				$attempts = 0;
				while ($attempts < 10)
				{
					$attempts++;
					mt_srand ((double) microtime() * 1000000);
					$randval = md5(mt_rand().$ADMIN['admin_id']);
					$q = "update admin set sessionId='$randval' 
						where username='$username' and mpassword='$mpassword'";
						
					if (pg_exec($q)) 
					{
						$attempts = 0;
						break;
					}	
				}		
			
				if ($attempts == 0)
				{
					$sessionId = $randval;
					$ADMIN['sessionId']=$sessionId;
					$p = "";
					include_once('../var/system.conf.php');
				}
				else
				{
					message("An error occurred...Pls. try again...");
				}
			}
		}	
	}


	if ($ADMIN['sessionId'] == null)
	{
		include_once("../lib/login.php");
		exit;
	}	
	elseif ($ADMIN['sessionId'] != null)
	{

		$q = "select * from admin where sessionId = '".$ADMIN['sessionId']."'";
		$qr = pg_exec($q);
		if (!$qr)
		{
			include_once("login.php");
			exit;
		}
		elseif (pg_numrows($qr) == 0)
		{
			include_once("login.php");
			exit;
		}

	}
	$KURUKOKUK.='xyz';