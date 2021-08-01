<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
if (isset($_POST["submit"])) {
    $accType = se($_POST, "type", null, false);
    $deposit = trim(se($_POST, "deposit", null, false));

    $isValid = true;
    if (!isset($accType) || !isset($deposit)) {
        flash("Must provide account type and deposit", "warning");
        $isValid = false;
    }
    if ($deposit < 5) {
        flash("Deposit must be at least $5", "warning");
        $isValid = false;
    }
    if ($isValid) {
        //new_acc($deposit, $accType);
        flash($accType);
    }
}
?>
<div>
    <h1>Minimum deposit of $5 / Minimum loan of $500</h1>
    <form method="POST" onsubmit="return validate(this);">
        <div>
            <label for="type">Account type: </label>
            <select id="type" name="type" required>
                <option value="Checking">Checking</option>
                <option value="Savings">Savings</option>
            </select>
        </div>
        <div>
            <label for="deposit">Initial deposit/loan: </label>
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
        if (deposit < 5) {
            isValid = false;
            alert("Deposit must be at least $5");
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>