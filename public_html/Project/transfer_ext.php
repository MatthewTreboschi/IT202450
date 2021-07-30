<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
    $isValid = true;
    $toAccNum = se($_POST, "toAccNum", null, false);
    $last_name = se($_POST, "last_name", null, false);
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
    if (preg_match("/[~`!#$%\^&*+=\-\[\]\\';,/\{\}|\":<>\?]/", $last_name)) {
        flash("No special characters are allowed in the last name", "warning");
        $isValid = false;
    }
    if (strlen($toAccNum) != 4) {
        flash("to account number must be 4 digits", "warning");
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
    if (strlen($last_name)>30)
    if ($isValid) {
        $toAccNum = get_account_num($last_name, $toAccNum);
        if (strlen($toAccNum) == 12) {
            transaction($toAccNum, $fromAccNum, $amount, "ext-transfer", $memo);
        }
        else {
            flash("error finding account", "warning");
        }
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
            <label for="last name">To last name: </label>
            <input type="text" name="last name" id="last name" maxlength=30 required />
        </div>
        <div>
            <label for="toAccNum">To account (last 4 digits): </label>
            <input type="text" id="toAccNum" name="toAccNum" maxlength=4 required />
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
        let last_name = form.last_name.value;
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
        if (toAccNum.length != 4) {
            isValid = false;
            alert("The to account must be 4 digits!");
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
        if (last_name.length>30){
            isValid = false;
            alert("last name must be 30 or fewer characters");
        }
        if (/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g.test(last_name)) {
            isValid = false;
            alert("No special characters allowed in the last name");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>