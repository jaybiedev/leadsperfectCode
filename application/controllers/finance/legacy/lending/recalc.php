<?php
$this->View->setPageTitle("Recalculate Loan Account Ledger");
?>
<form action="" method="post" name="f1" id="f1">
  <table class="table">
    <tr>
      <td>Loan Release
        No.<br>
        (Leave Blank For All Accounts)
        <input name="rid" type="text" id="rid" value="<?= $rid;?>" class="form-control" placeholder="Load release number or blank for all accounts">
          <br />
        <input name="p1" type="button" id="p1" value="Go" class="btn btn-primary" onCLick="wait('Please wait. Processing data...');xajax_recalculate(rid.value,'form');">
        <input type="button" name="Button" value="Close" class="btn btn-secondary" onClick="lending/";>
        </td>
    </tr>
    <tr> 
      <td><hr color="red" height="1"></td>
    </tr>
  </table>

</form>
