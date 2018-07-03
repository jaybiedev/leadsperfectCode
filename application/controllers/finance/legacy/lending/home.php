<div style="margin-top:20px;margin-bottom:20px;margin-left:25%;float:left;">
    <form methpd="post" action="" class="form-horizontal">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <div class="input-group">
                <!--
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Ledger</a></li>
                        <li><a href="#">Loan</a></li>
                    </ul>
                </div>--><!-- /btn-group -->
                <input type="text" name="xSearch" value="<?= $xSearch;?>" placeholder="Search..." class="form-control">
                <span class="input-group-btn">
                <input type="submit" value="Go" class="btn btn-primary" />
                </span>
            </div>
            <input type="hidden" name="p1" value="Go" />
            <input type="hidden" name="p" value="home" />
        </div>
    </form>
</div>

 <table class="table">
  <tr valign="top"> 
    <td width="47%" align="center">
	<form name="f1" method="post" action="">
        <?
        if ($p1 == 'Go' && $xSearch != '')
        {
            $q = "select * from account where account ilike '%$xSearch%' order by account ";
            $qr = @pg_query($q) or message(pg_errormessage());
        ?>
            <table class="table">
              <tr>
                <td colspan="2"><h2><i class="fa "></i>Search Result</h2>
                  <hr color="#993300"></td>
              </tr>
              <?
              while ($r = pg_fetch_object($qr))
              {
                    $q = "select * from releasing where account_id='$r->account_id' order by releasing_id desc";
                    $rr = fetch_object($q);
              ?>
              <tr>
                <td colspan="2">
                <img src="../graphics/blue_bullet.gif" width="11" height="10"><?=$r->account;?></td>
              </tr>
              <tr>
                <td width="8%">&nbsp;</td>
                <td width="92%">
                  <li><a href="?p=account&p1=Load&id=<?=$r->account_id;?>">Account
                    Info</a></li>
                  <li><a href="?p=report.accountledger&p1=Selectreleasing&c_id=<?=$r->account_id;?>&xSearch=<?=$r->account;?>">Account
                    Ledger</a>
                  </li>
                  <?
                  if ($rr->releasing_id != '')
                  {
                    echo "<li><a href='?p=loan.releasing&p1=Load&id=$rr->releasing_id'>Loan Released</a></li>";
                    }
                    ?>
                </td>
          </tr>
		  <?
		  }
		  ?>
        </table>
        <?
	}
	else
	{
	?>
        <table class="table">
          <tr> 
            <td colspan="2"><img src="../graphics/post_discussion.gif" width="20" height="20"> 
              <strong>F</strong>inancing  
              <hr color="#993300"></td>
          </tr>
		  
          <tr> 
            <td width="8%" align="center"><img src="../graphics/bluelist.gif" width="16" height="17"></td>
            <td width="92%"><strong>Loan Releases</strong></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td> <img src="../graphics/blue_bullet.gif" width="11" height="10"> 
              <a href="?p=home&p1=releases2d">Today</a></td>
          </tr>
		  <?
		  if ($p1 == 'releases2d')
		  {
		  		$from_date = date('m/d/Y');
		  		$to_date = date('m/d/Y');
		  		$q = "select 
						sum(released) as amount, 
						account_class.account_class_id,
						account_class.account_class
					from
						releasing,
						account,
						account_class,
						account_group
					where
						account_class.account_class_id=account_group.account_class_id and
						account_group.account_group_id=account.account_group_id and
						account.account_id=releasing.account_id and
						status='S' and
						releasing.date = '".mdy2ymd($from_date)."'
					group by
						account_class.account_class_id,
						account_class.account_class";
				$qr = @pg_query($q) or message(pg_errormessage().$q);
				if (pg_num_rows($qr) == 0)
				{
					echo "<tr><td></td><td>&nbsp;<em>No Data Found...</em></td></tr>";
				}
				while ($r = pg_fetch_object($qr))
				{
					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td valign='center' >&nbsp;&nbsp;
						<img src='../graphics/redarrow_right.gif' width='4' height='5'> &nbsp;";
					echo $r->account_class."<b> P ";
					echo number_format($r->amount,2)."</b> | ";	
					echo "<a href='?p=report.releasing&from_date=$from_date&to_date=$to_date&show=S&account_class_id=$r->account_class_id&p1=Go'>
							 <em>view details</em></a></td>";
					echo "</tr>";
				}	
		  }
		  ?>
          <tr> 
            <td>&nbsp;</td>
            <td> 
			<img src="../graphics/blue_bullet.gif" width="11" height="10"><a href="?p=home&p1=releasesYd">Yesterday</a></td>
          </tr>
		  <?
		  if ($p1 == 'releasesYd')
		  {
		  		$from_date = addDate(date('m/d/Y'),-1);
		  		$to_date = addDate(date('m/d/Y'),-1);
		  		$q = "select 
						sum(released) as amount, 
						account_class.account_class_id,
						account_class.account_class
					from
						releasing,
						account,
						account_class,
						account_group
					where
						account_class.account_class_id=account_group.account_class_id and
						account_group.account_group_id=account.account_group_id and
						account.account_id=releasing.account_id and
						status='S' and
						releasing.date = '".mdy2ymd($from_date)."'
					group by
						account_class.account_class_id,
						account_class.account_class";
				$qr = @pg_query($q) or message(pg_errormessage().$q);
				if (pg_num_rows($qr) == 0)
				{
					echo "<tr><td></td><td>&nbsp;<em>No Data Found...</em></td></tr>";
				}
				while ($r = pg_fetch_object($qr))
				{
					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td valign='center' >&nbsp;&nbsp;
						<img src='../graphics/redarrow_right.gif' width='4' height='5'> &nbsp;";
					echo $r->account_class."<b> P ";
					echo number_format($r->amount,2)."</b> | ";	
					echo "<a href='?p=report.releasing&from_date=$from_date&to_date=$to_date&show=S&account_class_id=$r->account_class_id&p1=Go'>
							 <em>view details</em></a></td>";
					echo "</tr>";
				}	
		  }
		  ?>
		  
          <tr> 
            <td align="center"><img src="../graphics/waiting.gif" width="16" height="16"></td>
            <td><strong>Collection</strong></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>
			 <img src="../graphics/blue_bullet.gif" width="11" height="10"><a href="?p=home&p1=collection2d">Today</a></td>
          </tr>
		  <?
		  if ($p1 == 'collection2d')
		  {
		  		$from_date = date('m/d/Y');
		  		$to_date = date('m/d/Y');
//		  		$from_date = addDate(date('m/d/Y'),-1);
//		  		$to_date = addDate(date('m/d/Y'),-1);
		  		$q = "select 
						sum(total_amount) as amount, 
						account_class.account_class_id,
						account_class.account_class
					from
						payment_header,
						account_class,
						account_group
					where
						account_class.account_class_id=account_group.account_class_id and
						account_group.account_group_id=payment_header.account_group_id and
						payment_header.date = '".mdy2ymd($from_date)."'
					group by
						account_class.account_class_id,
						account_class.account_class";
				$qr = @pg_query($q) or message(pg_errormessage().$q);

				if (pg_num_rows($qr) == 0)
				{
					echo "<tr><td></td><td>&nbsp;<em>No Data Found...</em></td></tr>";
				}
				while ($r = pg_fetch_object($qr))
				{
					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td valign='center' >&nbsp;&nbsp;
						<img src='../graphics/redarrow_right.gif' width='4' height='5'> &nbsp;";
					echo $r->account_class."<b> P ";
					echo number_format($r->amount,2)."</b> | ";	
					echo "<a href='?p=report.releasing&from_date=$from_date&to_date=$to_date&show=S&account_class_id=$r->account_class_id&p1=Go'>
							 <em>view details</em></a></td>";
					echo "</tr>";
				}	
		  }
		  ?>
	      <tr> 
            <td>&nbsp;</td>
            <td>
			<img src="../graphics/blue_bullet.gif" width="11" height="10"><a href="?p=home&p1=collectionYd">Yesterday</a></td>
          </tr>
		  <?
		  if ($p1 == 'collectionYd')
		  {
		  		$from_date = addDate(date('m/d/Y'),-1);
		  		$to_date = addDate(date('m/d/Y'),-1);
		  		$q = "select 
						sum(total_amount) as amount, 
						account_class.account_class_id,
						account_class.account_class
					from
						payment_header,
						account_class,
						account_group
					where
						account_class.account_class_id=account_group.account_class_id and
						account_group.account_group_id=payment_header.account_group_id and
						payment_header.date = '".mdy2ymd($from_date)."'
					group by
						account_class.account_class_id,
						account_class.account_class";
				$qr = @pg_query($q) or message(pg_errormessage().$q);

				if (pg_num_rows($qr) == 0)
				{
					echo "<tr><td></td><td>&nbsp;<em>No Data Found...</em></td></tr>";
				}
				while ($r = pg_fetch_object($qr))
				{
					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td valign='center' >&nbsp;&nbsp;
						<img src='../graphics/redarrow_right.gif' width='4' height='5'> &nbsp;";
					echo $r->account_class."<b> P ";
					echo number_format($r->amount,2)."</b> | ";	
					echo "<a href='?p=report.releasing&from_date=$from_date&to_date=$to_date&show=S&account_class_id=$r->account_class_id&p1=Go'>
							 <em>view details</em></a></td>";
					echo "</tr>";
				}	
		  }
		  ?>
        </table>
		<?
		}
		?>
      </form></td>
    <td width="26%">&nbsp;</td>
    <td width="27%"><br>
	<table class="table">
        <tr> 
          <td bgcolor="#CCCCCC"><strong>SHORTCUTS</strong></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/greenlist.gif" width="16" height="16"> <a href="?p=loan.releasing.browse">Loan 
            Releasing</a></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/list3.gif" width="16" height="16"> <a href="?p=payment.browse">Payment 
            and Collection</a></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/info2.gif" width="16" height="16"> <a href="?p=account">Account 
            Information</a></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/team_wksp.gif" width="16" height="17"> <a href="?p=account_group">Account 
            Group</a></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/templates.gif" width="16" height="17"> <a href="?p=report.accountledger_oldledger">Account 
            Ledger</a></td>
        </tr>
        <tr> 
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/team_wksp.gif" width="16" height="17"> <a href="?p=../system/admin.cp">Change 
            Password </a></td>
        </tr>
        <tr>
          <td bgcolor="#EFEFEF">&nbsp; 
            <img src="../graphics/greenlist.gif" width="16" height="16"> <a href="?p=override">Overrides</a></td>
        </tr>
      </table></td>
  </tr>
</table>