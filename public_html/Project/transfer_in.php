<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
    $isValid = true;
    $toAccNum = se($_POST, "toAccNum", null, false);
    $fromAccNum = se($_POST, "fromAccNum", null, false);
    $amount = trim(se($_POST, "amount", null, false));
    $memo = se($_POST, "memo", null, false);

    $isValid = true;
    if (preg_match("/[\/><\\\"]/", $memo)) {
        flash("None of the following special characters in the memo /><\\\"", "warning");
        $isValid = false;
    }
    if ($toAccNum == $fromAccNum) {
        flash("The to account and from account must be different accounts!", "warning");
        $isValid = false;
    }
    if (!isset($amount)) {
        flash("Must provide amount", "warning");
        $isValid = false;
    }
    if ($amount <= 0) {
        flash("Amount must be greater than 0", "warning");
        $isValid = false;
    }
    if (strlen($memo)>99) {
        flash("Memo must be less than 100 characters", "warning");
        $isValid = false;
    }
    if ($isValid) {
        transaction($toAccNum, $fromAccNum, $amount, "Internal transfer", $memo);
    }
}
?>

<div>
    <h1>Make an internal transfer here:</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <label for="fromAccNum">From account: </label>
            <select id="fromAccNum" name="fromAccNum" required>
                <?php foreach (get_accounts() as $acc) : ?>
                    <?php $v = $acc["account_number"]; ?>
                    <option value ="<?php se($v); ?>"><?php se($v); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="toAccNum">To account (must be a different account): </label>
            <select id="toAccNum" name="toAccNum" required>
                <?php foreach (get_accounts() as $acc) : ?>
                    <?php $v = $acc["account_number"]; ?>
                    <option value ="<?php se($v); ?>"><?php se($v); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="amount">Amount (greater than 0): </label>
            <input type="number" id="amount" name="amount" required />
        </div>
        <div>
            <label for="memo">Memo: </label>
            <input type="text" id="memo" name="memo" maxlength=99 required />
        </div>
        <div>
            <input type="submit" name="submit" value="Make transfer" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let toAccNum = form.toAccNum.value;
        let fromAccNum = form.fromAccNum.value;
        let memo = form.memo.value;
        let amount = form.amount.value;
        let isValid = true;
        if (amount) {
            amount = amount.trim();
        }
        if (toAccNum==fromAccNum){
            isValid = false;
            alert("The to account and from account must be different accounts!");
        }
        if (/[\\/\"<>]/g.test(memo)){
            isValid = false;
            alert("None of the following special characters in the memo /><\\\"");
        }
        if (amount <= 0) {
            isValid = false;
            alert("Amount must be a positive, non-zero number");
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