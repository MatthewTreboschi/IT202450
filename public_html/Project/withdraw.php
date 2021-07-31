<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
    $accNum = se($_POST, "Account_Number", null, false);
    $withdrawal = trim(se($_POST, "withdrawal", null, false));
    $memo = se($_POST, "memo", null, false);

    $isValid = true;
    if (preg_match("/[\/><\\\"]/", $memo)) {
        flash("None of the following special characters in the memo /><\\\"", "warning");
        $isValid = false;
    }
    if (!isset($accNum) || !isset($withdrawal)) {
        flash("Must provide account number and withdrawal amount", "warning");
        $isValid = false;
    }
    if ($withdrawal <= 0) {
        flash("Withdrawal must be greater than 0", "warning");
        $isValid = false;
    }
    if (strlen($memo)>99) {
        flash("Memo must be less than 100 characters", "warning");
        $isValid = false;
    }
    if ($isValid) {
        transaction("000000000000", $accNum, $withdrawal, "withdrawal", $memo);
    }
}
?>
<div>
    <h1>Make a withdrawal here:</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <label for="Account Number">Account number: </label>
            <select id="Account Number" name="Account Number" required>
                <?php foreach (get_accounts($loans = false) as $acc) : ?>
                    <?php $v = $acc["account_number"]; ?>
                    <option value ="<?php se($v); ?>"><?php se($v); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="withdrawal">Withdraw (greater than 0): </label>
            <input type="number" id="withdrawal" name="withdrawal" required />
        </div>
        <div>
            <label for="memo">Memo: </label>
            <input type="text" id="memo" name="memo" maxlength=99 required />
        </div>
        <div>
            <input type="submit" name="submit" value="Make withdrawal" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let memo = form.memo.value;
        let withdrawal = form.withdrawal.value;
        let isValid = true;
        if (withdrawal) {
            withdrawal = withdrawal.trim();
        }
        if (/[\\/\"<>]/g.test(memo)){
            isValid = false;
            alert("None of the following special characters in the memo /><\\\"")
        }
        if (withdrawal <= 0) {
            isValid = false;
            alert("Withdrawal must be a positive, non-zero number");
        }
        if (memo.length > 99) {
            isValid = false;
            alert("Memo must be less than 100 characters");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>