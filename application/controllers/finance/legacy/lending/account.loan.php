<table class="table table-stripped">
    <thead>
    <tr>
        <td colspan="1">#</td>
        <td colspan="1">Date</td>
        <td>Reference</td>
        <td>Loan Type</td>
        <td>Term</td>
        <td>Principal</td>
        <td colspan="1">Gross</td>
        <td colspan="1">Ammort</td>
        <td colspan="1">Balance</td>
    </tr>
    </thead>
    <?

    if ($aaccount['account_id'] != '')
    {
        $l_balance = $l_ammort = 0;
        $q = "select * from releasing where account_id = '".$aaccount['account_id']."'  order by date desc ";
        $qr = @pg_query($q);
        $ll=0;
        while ($r = @pg_fetch_object($qr))
        {
            $ll++;
            echo "<tr><td align='right'>$ll.</td>";
            echo "<td>".ymd2mdy($r->date)."</td>";
            echo "<td align='center'>$r->releasing_id</td>";
            echo "<td align='default'>".lookUpTableReturnValue('x','loan_type','loan_type_id','loan_type',$r->loan_type_id)."</td>";
            echo "<td align='right'>$r->term</td>";
            echo "<td align='right'>".number_format($r->principal,2)."</td>";
            echo "<td align='right'>".number_format($r->gross,2)."</td>";
            echo "<td align='right'>".number_format($r->ammort,2)."</td>";
            echo "<td align='right'>".number_format($r->balance,2)."</td>";
            $l_balance += $r->balance;
            if ($r->balance>0) $l_ammort += $r->ammort;
        }
        echo "<tr><td></td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td ><b>Total</b></td>";
        echo "<td></td>";
        echo "<td></td>";
        echo "<td align='right'><b>".number_format($l_ammort,2)."</b></td>";
        echo "<td align='right'><b>".number_format($l_balance,2)."</b></td>";

    }

    ?>
</table>