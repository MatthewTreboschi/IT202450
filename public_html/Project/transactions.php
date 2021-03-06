<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
$accNum = $_SESSION["accNum"];
if (isset($_POST["freeze"])) {
    toggle_freeze($accNum);
}
$info = get_account_info($accNum);
$start = date("Y-m-d", strtotime("-1 month"));
$end = "";
$type = "";
$page = 1;
if (isset($_POST["close"]) && get_balance($accNum) == 0) {
    //verifies the most recent balance from the db
    close($accNum);
    $_SESSION["accNum"] = -99;
    die(header("Location: accounts.php?removed=".$accNum));
}
else if (isset($_POST["close"]) && get_balance($accNum) != 0) {
    flash("Balance must be $0 to close an account", "Warning");
}
if (isset($_GET["page"])){
    $page = se($_GET, "page", null, false);
}
if (isset($_GET["submit"])) {
    $start = se($_GET, "start", null, false);
    $end = se($_GET, "end", null, false);
    if($end){
        $end = date("Y-m-d 23:59:59", strtotime($end));
    }
    $type = se($_GET, "type", null, false);

    
}
if ($info["user_id"] == get_user_id()) {
    $transactions = get_transactions($accNum, $start, $end, $type, $page);
    $total_pages = ceil(count_transactions()/10);
?>
<h1>This is the transactions page for account <?php echo($accNum)?></h1>
<h3>Account Number: <?php se($info["account_number"]); ?></h3>
<h3>Account Type: <?php $accType = $info["account_type"]; se($accType); ?></h3>
<h3>Balance: <?php $v = $info["balance"]; if ($accType == "loan") $v*=-1; se($v); ?></h3>
<h3>Opened: <?php se($info["created"]); ?></h3>
<?php $v = $info["apy"]; ?>
<h3>APY: <?php if ($v==0.0) {echo"-";} else {se($v);} ?></h3>
<?php if ($info["frozen"]=="1") { ?>
    <h3><b>This account is frozen!</b></h3>
<?php } ?>
<div>
    <h4>Filter: </h4>
    <form method="GET">
        <div>
            <label for="start">Start date: </label>
            <input type="date" name="start" id="start" />
        </div>
        <div>
            <label for="end">End date: </label>
            <input type="date" name="end" id="end" />
        </div>
        <div>
            <label for="type">Transaction Type: </label>
            <select name="type" id="type" >
                <option value="">All</option>
                <option value="deposit">deposit</option>
                <option value="withdrawal">withdrawal</option>
                <option value="Internal transfer">Internal transfer</option>
                <option value="ext-transfer">External transfer</option>
            </select>
        </div>
        <div>
            <input type="submit" name="submit" value="Filter" />
        </div>
    </form>
    <h4>Filtered transactions: </h4>
    <table>
        <tr>
            <th>Other Transactant</th>
            <th>Transaction Type</th>
            <th>Balance Change</th>
            <th>Memo</th>
            <th>Final Balance</th>
            <th>Time and Date</th>
        </tr>
        <?php foreach ($transactions as $transaction) : ?>
        <tr>
            <?php $v = $transaction["dest"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["transaction_type"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["bal_change"]; if ($accType == "loan") $v*=-1; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["memo"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["expected_total"]; if ($accType == "loan") $v*=-1; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
            <?php $v = $transaction["created"]; ?>
            <td value ="<?php se($v); ?>"><?php se($v); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div>
        <?php /** required $total_pages and $page to be set */ ?>
        <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
    </div>
    <form method = "POST" onsubmit="return validate(<?php echo get_balance($accNum); ?>);">
        <button type="submit" name="close" value="close">Close Account</button>
    </form>
    <?php if ($_SESSION["admin"]) { ?>
        <form method = "POST">
            <button type="submit" name="freeze" value="freeze">Toggle Freeze</button>
        </form>
    <?php } ?>
</div>
<?php } else { echo ("This isn't your account!");}?>
<script>
    function validate(balance) {
        isValid=true;
        if (balance != '0') {
            alert("Balance must be 0 to close an account!")
            isValid = false;
        }
        return isValid;
    }
</script>