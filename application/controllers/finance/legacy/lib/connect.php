<?php

	if ($p == "logout")
	{
        redirect('/logout/');
	}	
	
	$KURUKOKUK .='123';
	$cake = "select * from sysconfig where sysconfig='REG_SERIAL_NO'";
    $DBCONN = @pg_Connect($DBCONNECT); // or die("Can't connect to server...<a href='?p=logout'>[ LOGOUT ]</a>");

  	if (!$DBCONN)
  	{
  		echo "<div class='alert alert-danger'>!! ALERT WARNING !! <br>
  				Can't connect to database server... [$DBDOMAIN]<br><br>
  		 1. Is the database engine running?<br>
  	    2. Are you connected to the database server?<br>
  	    3. Does your database engine accept connection?
  		</div>";
  	    exit;
  	}

	$R = fetch_object($cake);
	$REG_SERIAL_NO = $R->value;
	if (!in_array(substr($REG_SERIAL_NO,0,13),we(1)))
	{
		$REG_SERIAL_NO = we(2);
		@pg_exec("update sysconfig set value='$REG_SERIAL_NO' where sysconfig='REG_SERIAL_NO'") or die (mysql_error());
		
	}	
	
	require_once('../lib/authenticate.php');
