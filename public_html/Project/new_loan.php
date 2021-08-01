<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
    $accType = se($_POST, "type", null, false);
    $deposit = trim(se($_POST, "deposit", null, false));
    $accTo = se($_POST, "toAccNum", null, false);

    $isValid = true;
    if (!isset($accType) || !isset($deposit)) {
        flash("Must provide account type and deposit", "warning");
        $isValid = false;
    }
    if ($deposit < 500) {
        flash("Deposit must be at least $500", "warning");
        $isValid = false;
    }
    if ($isValid) {
        new_loan($deposit, $accTo);
    }
}
?>
<div>
    <h1>Minimum loan of $500</h1>
    <h4>5% apy interest</h4>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <label for="type">Account type: </label>
            <select id="type" name="type" required>
                <option value="Checking">Loan</option>
            </select>
        </div>
        <div>
            <label for="toAccNum">To account: </label>
            <select id="toAccNum" name="toAccNum" required>
                <?php foreach (get_accounts() as $acc) : ?>
                    <?php $v = $acc["account_number"]; ?>
                    <option value ="<?php se($v); ?>"><?php se($v); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="deposit">loan: </label>
            <input type="number" id="deposit" name="deposit" required />
        </div>
        <div>
            <input type="submit" name="submit" value="create account" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let deposit = form.deposit.value;
        let isValid = true;
        if (deposit) {
            deposit = deposit.trim();
        }
        if (deposit < 500) {
            isValid = false;
            alert("Deposit must be at least $500");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>